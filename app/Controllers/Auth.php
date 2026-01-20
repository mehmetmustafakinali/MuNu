<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CompanyModel;

class Auth extends BaseController
{
    protected $userModel;
    protected $companyModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->companyModel = new CompanyModel();
        $this->session = \Config\Services::session();
    }

    public function login()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/Munu');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            $user = $this->userModel->login($username, $password);

            if ($user) {
                // Şirket bilgisini al
                $company = $this->companyModel->find($user['company_id']);

                // Şirket durumu kontrolü (status yoksa varsayılan aktif sayılabilir veya hata verilebilir, burada güvenli kontrol yapıyoruz)
                $companyStatus = $company['status'] ?? 'aktif';

                if (!$company || $companyStatus !== 'aktif') {
                    $this->session->setFlashdata('error', 'Şirket hesabı aktif değil veya bulunamadı.');
                    return view('login_view');
                }

                $userData = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role'],
                    'company_id' => $user['company_id'],
                    'company_name' => $company['company_name'] ?? 'Bilinmeyen Şirket',
                    'company_logo' => $company['logo_url'] ?? null,
                    'isLoggedIn' => true
                ];

                $this->session->set($userData);
                return redirect()->to('/Munu');
            } else {
                $this->session->setFlashdata('error', 'Kullanıcı adı veya şifre hatalı.');
            }
        }

        return view('login_view');
    }

    public function register_company()
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            try {
                $companyName = trim((string) $this->request->getPost('company_name'));
                $adminUsername = trim((string) $this->request->getPost('username'));
                $adminPassword = (string) $this->request->getPost('password');
                $adminFullName = trim((string) $this->request->getPost('full_name'));
                $adminPhone = trim((string) $this->request->getPost('phone'));
                $adminEmail = trim((string) $this->request->getPost('email'));

                // Basic Validation
                if (empty($companyName) || empty($adminUsername) || empty($adminPassword) || empty($adminPhone)) {
                    $this->session->setFlashdata('reg_error', 'Lütfen tüm alanları doldurun.');
                    return redirect()->to('/Auth/login#register');
                }

                $db = \Config\Database::connect();
                $db->transStart();

                // 1. Şirketi Oluştur - company_code benzersiz olmalı
                $companyCode = 'C' . strtoupper(substr(md5($companyName . time()), 0, 8));

                $companyData = [
                    'company_name' => $companyName,
                    'company_code' => $companyCode,
                    'status' => 'aktif'
                ];
                $this->companyModel->save($companyData);
                $companyId = $this->companyModel->getInsertID();

                // 2. Yönetici Kullanıcısını Oluştur
                $userInsertData = [
                    'company_id' => $companyId,
                    'username' => $adminUsername,
                    'password' => $adminPassword,
                    'full_name' => $adminFullName,
                    'phone' => $adminPhone,
                    'email' => $adminEmail,
                    'role' => 'yonetici',
                    'is_active' => 1
                ];

                $this->userModel->save($userInsertData);

                $db->transComplete();

                if ($db->transStatus() === false) {
                    $errors = [];
                    // Hataları topla
                    if ($this->companyModel->errors())
                        $errors[] = implode(', ', $this->companyModel->errors());
                    if ($this->userModel->errors())
                        $errors[] = implode(', ', $this->userModel->errors());

                    $errorMsg = !empty($errors) ? implode(' | ', $errors) : 'Veritabanı işlem hatası.';
                    $this->session->setFlashdata('reg_error', 'Kayıt başarısız: ' . $errorMsg);
                    return redirect()->to('/Auth/login#register');
                }

                $this->session->setFlashdata('success', 'Şirket başarıyla oluşturuldu. Giriş yapabilirsiniz.');
                $this->session->setFlashdata('success', 'Şirket başarıyla oluşturuldu. Hoş geldiniz!');

                // OTOMATİK GİRİŞ YAP
                $userData = [
                    'id' => $this->userModel->getInsertID(), // Yeni eklenen kullanıcı ID
                    'username' => $adminUsername,
                    'full_name' => $adminFullName,
                    'role' => 'yonetici',
                    'company_id' => $companyId,
                    'company_name' => $companyName,
                    'company_logo' => null, // Yeni şirket olduğu için logo yok
                    'isLoggedIn' => true
                ];

                $this->session->set($userData);
                return redirect()->to('/Munu');

            } catch (\Exception $e) {
                log_message('error', 'Register company error: ' . $e->getMessage());
                $this->session->setFlashdata('reg_error', 'Kayıt sırasında bir hata oluştu: ' . $e->getMessage());
                return redirect()->to('/Auth/login#register');
            }
        }

        return redirect()->to('/Auth/login');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/Auth/login');
    }
}
