<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'company_id',
        'username',
        'password',
        'full_name',
        'email',
        'phone',
        'role',
        'role_id',
        'is_active',
        'last_login'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Callbacks
    protected $beforeInsert = ['hashPassword', 'addCompanyId'];
    protected $beforeUpdate = ['hashPassword'];
    protected $beforeFind = ['addCompanyFilter'];

    // Validation kuralları
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]',
        'full_name' => 'required|min_length[2]|max_length[100]',
        'email' => 'permit_empty|valid_email'
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'Kullanıcı adı zorunludur.'
        ]
    ];

    /**
     * Şirket ID'sini session'dan al
     */
    protected function getCompanyId()
    {
        $session = session();
        return $session->get('company_id');
    }

    /**
     * Insert öncesi company_id ekle
     */
    protected function addCompanyId(array $data)
    {
        // Eğer dışarıdan manuel olarak company_id verilmişse (örn: kayıt sırasında), onu kullan ve ezme.
        if (isset($data['data']['company_id']) && !empty($data['data']['company_id'])) {
            return $data;
        }

        $data['data']['company_id'] = $this->getCompanyId();
        return $data;
    }

    /**
     * Find öncesi company_id filtresi ekle
     */
    protected function addCompanyFilter(array $data)
    {
        $companyId = $this->getCompanyId();
        if ($companyId) {
            $this->where('users.company_id', $companyId);
        }
        return $data;
    }

    /**
     * Şifreyi hash'le
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            // Zaten hash'lenmiş mi kontrol et
            if (strlen($data['data']['password']) < 60) {
                $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            }
        }
        return $data;
    }

    /**
     * Kullanıcı girişi doğrula
     */
    public function login($username, $password)
    {
        // Login metodunda global filtreyi kullanmıyoruz (henüz session olmayabilir)
        // Bu yüzden manuel filtrelememiz lazım ama login henüz session oluşturmadığı için
        // burada tüm kullanıcılar aranır.
        // Ancak güvenlik için kullanıcı bulunduktan sonra company status kontrolü Auth.php'de yapılıyor.

        $user = $this->where('username', $username)
            ->where('is_active', 1)
            ->first();

        if ($user && password_verify($password, $user['password'])) {
            // Son giriş zamanını güncelle
            $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
            return $user;
        }

        return false;
    }

    /**
     * Aktif kullanıcıları getir
     */
    public function getActiveUsers()
    {
        // beforeFind otomatik olarak company_id ekleyecek
        return $this->where('is_active', 1)
            ->orderBy('full_name', 'ASC')
            ->findAll();
    }

    /**
     * Yöneticileri getir
     */
    public function getAdmins()
    {
        return $this->groupStart()
            ->where('role', 'yonetici')
            ->orWhere('role_id', 1)
            ->groupEnd()
            ->where('is_active', 1)
            ->findAll();
    }

    /**
     * Personelleri getir
     */
    public function getStaff()
    {
        return $this->groupStart()
            ->where('role', 'personel')
            ->orWhere('role_id', 2)
            ->groupEnd()
            ->where('is_active', 1)
            ->findAll();
    }

    /**
     * Şifre değiştir
     */
    public function changePassword($userId, $newPassword)
    {
        $companyId = $this->getCompanyId();
        // company_id kontrolü ile güncelleme yap
        return $this->where('id', $userId)
            ->where('company_id', $companyId)
            ->set(['password' => $newPassword]) // beforeUpdate çalışması için model update kullanılmalı
            ->update();
    }

    /**
     * Kullanıcı sayısını getir
     */
    public function getCount()
    {
        return $this->where('is_active', 1)->countAllResults();
    }

    /**
     * Kullanıcının rolünü getir (role veya role_id'den)
     */
    public function getUserRole($user)
    {
        if (!empty($user['role'])) {
            return $user['role'];
        }

        // role_id'den rol belirle
        if (isset($user['role_id'])) {
            return ($user['role_id'] == 1) ? 'yonetici' : 'personel';
        }

        return 'personel';
    }
}
