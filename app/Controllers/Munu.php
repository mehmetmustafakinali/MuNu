<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\TransactionModel;
use App\Models\ProductModel;
use App\Models\MyDebtModel;
use App\Models\UserModel;
use App\Models\CreditorModel;
use App\Models\ExpenseCategoryModel;
use App\Models\NoteModel;

class Munu extends BaseController
{
    protected $customerModel;
    protected $transactionModel;
    protected $productModel;
    protected $myDebtModel;
    protected $userModel;
    protected $creditorModel;
    protected $expenseCategoryModel;
    protected $noteModel;
    protected $session;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->transactionModel = new TransactionModel();
        $this->productModel = new ProductModel();
        $this->myDebtModel = new MyDebtModel();
        $this->userModel = new UserModel();
        $this->creditorModel = new CreditorModel();
        $this->expenseCategoryModel = new ExpenseCategoryModel();
        $this->noteModel = new NoteModel();
        $this->session = \Config\Services::session();

        // ---------------------------------------------------------
        // OTOMATİK VERİTABANI ONARIMI (Kullanıcı isteği üzerine)
        // ---------------------------------------------------------
        $this->autoFixDatabase();
    }

    /**
     * Veritabanı tablolarını ve sütunlarını otomatik kontrol et ve oluştur
     */
    private function autoFixDatabase()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        // 1. Companies tablosu yoksa oluştur
        if (!$db->tableExists('companies')) {
            $db->query("CREATE TABLE `companies` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `company_name` varchar(150) NOT NULL,
                `logo_url` varchar(255) DEFAULT NULL,
                `status` enum('aktif','pasif') DEFAULT 'aktif',
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // Varsayılan şirket ekle
            $db->query("INSERT INTO `companies` (`id`, `company_name`, `created_at`) VALUES (1, 'Varsayılan Şirket', NOW())");
        }

        // 1.5 Companies tablosu sütun kontrolü (status ve logo_url)
        if ($db->tableExists('companies')) {
            if (!$db->fieldExists('status', 'companies')) {
                $db->query("ALTER TABLE `companies` ADD COLUMN `status` enum('aktif','pasif') DEFAULT 'aktif' AFTER `company_name`");
            }
            if (!$db->fieldExists('logo_url', 'companies')) {
                $db->query("ALTER TABLE `companies` ADD COLUMN `logo_url` varchar(255) DEFAULT NULL AFTER `company_name`");
            }
        }

        // 2. Tablolara company_id sütunu ekle
        $tables = [
            'users',
            'customers',
            'transactions',
            'notes',
            'my_debts',
            'creditors',
            'expense_categories',
            'products',
            'ai_chat_history'
        ];

        foreach ($tables as $table) {
            if ($db->tableExists($table) && !$db->fieldExists('company_id', $table)) {
                $db->query("ALTER TABLE `$table` ADD COLUMN `company_id` int(11) UNSIGNED DEFAULT 1 AFTER `id`");
            }
        }
    }

    /**
     * Ana Sayfa - Dashboard
     */
    public function index()
    {
        // Oturum kontrolü - giriş yapmamış kullanıcıları login'e yönlendir
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/Auth/login');
        }

        // Dashboard verileri
        // Not istatistikleri
        $noteStats = $this->noteModel->getStats();

        $data = [
            'customers' => $this->customerModel->getActiveCustomers(),
            'transactions' => $this->transactionModel->getWithCustomer(10),
            'my_debts' => $this->myDebtModel->getUnpaid(),
            'users' => $this->userModel->getActiveUsers(),

            // Karalama Defteri verileri
            'notes' => $this->noteModel->getActive(),
            'today_notes' => $this->noteModel->getTodayReminders(),
            'upcoming_notes' => $this->noteModel->getUpcoming(7),
            'overdue_notes' => $this->noteModel->getOverdue(),
            'note_stats' => $noteStats,

            // Borçlarım için ek veriler
            'creditors' => $this->creditorModel->getActive(),
            'expense_categories' => $this->expenseCategoryModel->getActive(),
            'creditor_summary' => $this->myDebtModel->getCreditorSummary(),
            'category_summary' => $this->myDebtModel->getCategorySummary(),

            // Dashboard özet verileri
            'total_receivables' => $this->customerModel->getTotalReceivables(),
            'total_my_debts' => $this->myDebtModel->getTotalUnpaidDebt(),
            'pending_notes' => $noteStats['overdue'] + $noteStats['today'],
            'customer_count' => $this->customerModel->getActiveCount(),
            'top_debtors' => $this->customerModel->getTopDebtors(5),
            'recent_transactions' => $this->transactionModel->getRecent(5),
            'overdue_debts' => $this->myDebtModel->getOverdueCount(),
            'monthly_data' => $this->transactionModel->getMonthlyTotals(6),
        ];

        return view('munu_view', $data);
    }

    // =========================================
    // MÜŞTERİ İŞLEMLERİ
    // =========================================

    /**
     * Müşteri Ekle
     */
    public function add_customer()
    {
        // POST kontrolü (büyük/küçük harf duyarsız)
        if (strtolower($this->request->getMethod()) === 'post') {
            $data = [
                'customer_name' => $this->request->getPost('customer_name') ?? '',
                'phone' => $this->request->getPost('phone') ?? '',
                'email' => $this->request->getPost('email') ?? '',
                'address' => $this->request->getPost('address') ?? '',
                'city' => $this->request->getPost('city') ?? '',
                'tax_number' => $this->request->getPost('tax_number') ?? '',
                'tax_office' => $this->request->getPost('tax_office') ?? '',
                'customer_type' => $this->request->getPost('customer_type') ?: 'bireysel',
                'notes' => $this->request->getPost('notes') ?? '',
                'balance' => 0,
                'status' => 'aktif'
            ];

            // Müşteri adı boş mu kontrol et
            if (empty($data['customer_name'])) {
                $this->session->setFlashdata('error', 'Müşteri adı zorunludur!');
                return redirect()->to('/Munu');
            }

            if ($this->customerModel->insert($data)) {
                $this->session->setFlashdata('success', 'Müşteri başarıyla eklendi.');
            } else {
                // Hata detayını göster
                $errors = $this->customerModel->errors();
                $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Bilinmeyen hata';
                $this->session->setFlashdata('error', 'Hata: ' . $errorMsg);
            }
        }

        return redirect()->to('/Munu#customers');
    }

    /**
     * Müşteri Güncelle
     */
    public function update_customer($id)
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            $data = [
                'customer_name' => $this->request->getPost('customer_name') ?? '',
                'phone' => $this->request->getPost('phone') ?? '',
                'email' => $this->request->getPost('email') ?? '',
                'address' => $this->request->getPost('address') ?? '',
                'city' => $this->request->getPost('city') ?? '',
                'customer_type' => $this->request->getPost('customer_type') ?: 'bireysel',
                'notes' => $this->request->getPost('notes') ?? '',
            ];

            if ($this->customerModel->update($id, $data)) {
                $this->session->setFlashdata('success', 'Müşteri başarıyla güncellendi.');
            } else {
                $errors = $this->customerModel->errors();
                $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Bilinmeyen hata';
                $this->session->setFlashdata('error', 'Hata: ' . $errorMsg);
            }
        }

        return redirect()->to('/Munu#customers');
    }

    /**
     * Müşteri Sil
     */
    public function delete_customer($id)
    {
        if ($this->customerModel->delete($id)) {
            $this->session->setFlashdata('success', 'Müşteri silindi.');
        } else {
            $this->session->setFlashdata('error', 'Müşteri silinirken hata oluştu.');
        }

        return redirect()->to('/Munu#customers');
    }

    /**
     * Müşteri Detayları (AJAX)
     */
    public function get_customer($id)
    {
        $customer = $this->customerModel->find($id);
        $transactions = $this->transactionModel->getByCustomer($id);

        return $this->response->setJSON([
            'customer' => $customer,
            'transactions' => $transactions
        ]);
    }

    /**
     * Müşteri Detay Raporu Sayfası
     */
    public function customer_report($id)
    {
        $id = (int) $id;
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            $this->session->setFlashdata('error', 'Müşteri bulunamadı!');
            return redirect()->to('/Munu#customers');
        }

        // Müşterinin tüm işlemlerini al
        $transactions = $this->transactionModel->getByCustomer($id);

        // İstatistikleri hesapla
        $totalBorc = 0;
        $totalTahsilat = 0;
        $transactionCount = count($transactions);

        foreach ($transactions as $t) {
            if ($t['transaction_type'] === 'borc') {
                $totalBorc += (float) $t['amount'];
            } else {
                $totalTahsilat += (float) $t['amount'];
            }
        }

        // Aylık özet (son 12 ay)
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = date('Y-m-01', strtotime("-$i months"));
            $monthEnd = date('Y-m-t', strtotime("-$i months"));
            $monthName = date('M Y', strtotime("-$i months"));

            $monthBorc = 0;
            $monthTahsilat = 0;

            foreach ($transactions as $t) {
                $tDate = $t['transaction_date'];
                if ($tDate >= $monthStart && $tDate <= $monthEnd) {
                    if ($t['transaction_type'] === 'borc') {
                        $monthBorc += (float) $t['amount'];
                    } else {
                        $monthTahsilat += (float) $t['amount'];
                    }
                }
            }

            if ($monthBorc > 0 || $monthTahsilat > 0) {
                $monthlyStats[] = [
                    'month' => $monthName,
                    'borc' => $monthBorc,
                    'tahsilat' => $monthTahsilat
                ];
            }
        }

        $data = [
            'customer' => $customer,
            'transactions' => $transactions,
            'totalBorc' => $totalBorc,
            'totalTahsilat' => $totalTahsilat,
            'transactionCount' => $transactionCount,
            'monthlyStats' => $monthlyStats
        ];

        return view('customer_report_view', $data);
    }

    // =========================================
    // İŞLEM (BORÇ/TAHSİLAT) İŞLEMLERİ
    // =========================================

    /**
     * İşlem Ekle (Borç veya Tahsilat)
     */
    public function add_transaction()
    {
        // POST kontrolü (büyük/küçük harf duyarsız)
        if (strtolower($this->request->getMethod()) === 'post') {
            $customerId = (int) $this->request->getPost('customer_id');
            $type = (string) $this->request->getPost('transaction_type');
            $amount = (float) $this->request->getPost('amount');
            $description = (string) ($this->request->getPost('description') ?? '');
            $date = (string) ($this->request->getPost('transaction_date') ?: date('Y-m-d'));
            $redirectTo = $this->request->getPost('redirect_to');

            // Müşteri ID kontrolü
            if ($customerId <= 0) {
                $this->session->setFlashdata('error', 'Müşteri seçilmedi!');
                return redirect()->to($redirectTo ?: '/Munu#customers');
            }

            // Tahsilat kontrolü - borçtan fazla tahsilat yapılamasın
            if ($type === 'tahsilat') {
                $customer = $this->customerModel->find($customerId);
                if ($customer && $amount > (float) $customer['balance']) {
                    $this->session->setFlashdata('error', 'Tahsilat tutarı müşteri borcundan fazla olamaz!');
                    return redirect()->to($redirectTo ?: '/Munu#customers');
                }
            }

            // Veritabanına kaydet (doğrudan query builder ile)
            $db = \Config\Database::connect();
            $builder = $db->table('transactions');

            $insertData = [
                'customer_id' => $customerId,
                'transaction_type' => $type,
                'amount' => $amount,
                'description' => $description,
                'transaction_date' => $date
            ];

            if ($builder->insert($insertData)) {
                // Müşteri bakiyesini güncelle
                $this->customerModel->updateBalance($customerId, $amount, $type);

                $msg = ($type === 'borc') ? 'Borç kaydedildi.' : 'Tahsilat kaydedildi.';
                $this->session->setFlashdata('success', $msg);
            } else {
                $this->session->setFlashdata('error', 'İşlem kaydedilirken hata oluştu.');
            }
        }

        // Eğer redirect_to parametresi varsa oraya yönlendir
        $redirectTo = $this->request->getPost('redirect_to');
        if ($redirectTo) {
            return redirect()->to($redirectTo);
        }

        return redirect()->to('/Munu#customers');
    }

    /**
     * İşlem Sil
     */
    public function delete_transaction($id)
    {
        $id = (int) $id;
        $transaction = $this->transactionModel->find($id);

        if ($transaction) {
            // Bakiyeyi geri al
            $reverseType = ($transaction['transaction_type'] === 'borc') ? 'tahsilat' : 'borc';
            $this->customerModel->updateBalance(
                (int) $transaction['customer_id'],
                (float) $transaction['amount'],
                $reverseType
            );

            $this->transactionModel->delete($id);
            $this->session->setFlashdata('success', 'İşlem silindi ve bakiye güncellendi.');
        }

        return redirect()->to('/Munu#transactions');
    }

    // =========================================
    // KARALAMA DEFTERİ / NOT İŞLEMLERİ
    // =========================================

    /**
     * Not Ekle
     */
    public function add_note()
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            $dueDate = $this->request->getPost('due_date');
            $dueTime = $this->request->getPost('due_time');

            // Tarih ve saat birleştir
            $dueDatetime = null;
            if ($dueDate) {
                $dueDatetime = $dueDate . ($dueTime ? ' ' . $dueTime . ':00' : ' 09:00:00');
            }

            $data = [
                'title' => trim((string) $this->request->getPost('title')),
                'content' => trim((string) $this->request->getPost('content')),
                'note_type' => $this->request->getPost('note_type') ?: 'not',
                'priority' => $this->request->getPost('priority') ?: 'normal',
                'due_date' => $dueDatetime,
                'color' => $this->request->getPost('color') ?: '#fef3c7',
                'status' => 'aktif'
            ];

            $db = \Config\Database::connect();
            if ($db->table('notes')->insert($data)) {
                $this->session->setFlashdata('success', 'Not başarıyla eklendi.');
            } else {
                $this->session->setFlashdata('error', 'Not eklenirken hata oluştu.');
            }
        }

        return redirect()->to('/Munu#notes');
    }

    /**
     * Not Güncelle
     */
    public function update_note($id)
    {
        $id = (int) $id;

        if (strtolower($this->request->getMethod()) === 'post') {
            $dueDate = $this->request->getPost('due_date');
            $dueTime = $this->request->getPost('due_time');

            $dueDatetime = null;
            if ($dueDate) {
                $dueDatetime = $dueDate . ($dueTime ? ' ' . $dueTime . ':00' : ' 09:00:00');
            }

            $data = [
                'title' => trim((string) $this->request->getPost('title')),
                'content' => trim((string) $this->request->getPost('content')),
                'note_type' => $this->request->getPost('note_type') ?: 'not',
                'priority' => $this->request->getPost('priority') ?: 'normal',
                'due_date' => $dueDatetime,
                'color' => $this->request->getPost('color') ?: '#fef3c7',
            ];

            $db = \Config\Database::connect();
            if ($db->table('notes')->where('id', $id)->update($data)) {
                $this->session->setFlashdata('success', 'Not güncellendi.');
            } else {
                $this->session->setFlashdata('error', 'Not güncellenirken hata oluştu.');
            }
        }

        return redirect()->to('/Munu#notes');
    }

    /**
     * Notu Tamamla
     */
    public function complete_note($id)
    {
        $id = (int) $id;

        if ($this->noteModel->markAsCompleted($id)) {
            $this->session->setFlashdata('success', 'Not tamamlandı olarak işaretlendi.');
        } else {
            $this->session->setFlashdata('error', 'İşlem sırasında hata oluştu.');
        }

        return redirect()->to('/Munu#notes');
    }

    /**
     * Notu Sabitle/Kaldır
     */
    public function toggle_pin_note($id)
    {
        $id = (int) $id;

        if ($this->noteModel->togglePin($id)) {
            $this->session->setFlashdata('success', 'Not sabitleme durumu değiştirildi.');
        }

        return redirect()->to('/Munu#notes');
    }

    /**
     * Not Sil
     */
    public function delete_note($id)
    {
        $id = (int) $id;

        if ($this->noteModel->delete($id)) {
            $this->session->setFlashdata('success', 'Not silindi.');
        }

        return redirect()->to('/Munu#notes');
    }

    /**
     * Not Bilgilerini Getir (AJAX)
     */
    public function get_note($id)
    {
        $id = (int) $id;
        $note = $this->noteModel->find($id);

        return $this->response->setJSON([
            'note' => $note
        ]);
    }

    /**
     * Takvim Verilerini Getir (AJAX)
     */
    public function get_calendar_data()
    {
        $year = (int) ($this->request->getGet('year') ?: date('Y'));
        $month = (int) ($this->request->getGet('month') ?: date('m'));

        $notes = $this->noteModel->getByMonth($year, $month);

        // Günlere göre grupla
        $calendarData = [];
        foreach ($notes as $note) {
            $day = date('j', strtotime($note['due_date']));
            if (!isset($calendarData[$day])) {
                $calendarData[$day] = [];
            }
            $calendarData[$day][] = $note;
        }

        return $this->response->setJSON([
            'year' => $year,
            'month' => $month,
            'notes' => $calendarData
        ]);
    }

    // =========================================
    // BENİM BORÇLARIM İŞLEMLERİ
    // =========================================

    /**
     * Borç/Gider Ekle
     */
    public function add_my_debt()
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            $creditorId = (int) $this->request->getPost('creditor_id');
            $categoryId = (int) $this->request->getPost('category_id');
            $newCreditor = trim((string) $this->request->getPost('new_creditor'));
            $expenseType = $this->request->getPost('expense_type') ?: 'borc';

            // Yeni alacaklı eklenmişse
            if (!empty($newCreditor) && $creditorId === 0) {
                $db = \Config\Database::connect();
                $db->table('creditors')->insert([
                    'creditor_name' => $newCreditor,
                    'status' => 'aktif'
                ]);
                $creditorId = $db->insertID();
            }

            // Alacaklı adını al
            $creditorName = '';
            if ($creditorId > 0) {
                $creditor = $this->creditorModel->find($creditorId);
                $creditorName = $creditor ? $creditor['creditor_name'] : '';
            }

            $data = [
                'creditor_id' => $creditorId ?: null,
                'category_id' => $categoryId ?: null,
                'expense_type' => $expenseType,
                'creditor_name' => $creditorName ?: trim((string) $this->request->getPost('creditor_name')),
                'amount' => (float) $this->request->getPost('amount'),
                'description' => trim((string) $this->request->getPost('description')),
                'due_date' => $this->request->getPost('due_date') ?: null,
                'status' => 'odenmedi'
            ];

            $db = \Config\Database::connect();
            if ($db->table('my_debts')->insert($data)) {
                // Alacaklı toplam borcunu güncelle
                if ($creditorId > 0) {
                    $this->creditorModel->updateTotalDebt($creditorId);
                }

                $msg = ($expenseType === 'gider') ? 'Gider kaydedildi.' : 'Borç kaydedildi.';
                $this->session->setFlashdata('success', $msg);
            } else {
                $this->session->setFlashdata('error', 'Kayıt eklenirken hata oluştu.');
            }
        }

        // Redirect URL varsa oraya git
        $redirectTo = $this->request->getPost('redirect_to');
        if ($redirectTo) {
            return redirect()->to($redirectTo);
        }
        return redirect()->to('/Munu#mydebts');
    }

    /**
     * Alacaklı Ekle
     */
    public function add_creditor()
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            $data = [
                'creditor_name' => trim((string) $this->request->getPost('creditor_name')),
                'phone' => trim((string) $this->request->getPost('phone')),
                'email' => trim((string) $this->request->getPost('email')),
                'notes' => trim((string) $this->request->getPost('notes')),
                'status' => 'aktif'
            ];

            $db = \Config\Database::connect();
            if ($db->table('creditors')->insert($data)) {
                $this->session->setFlashdata('success', 'Alacaklı kaydedildi.');
            } else {
                $this->session->setFlashdata('error', 'Alacaklı eklenirken hata oluştu.');
            }
        }

        return redirect()->to('/Munu#mydebts');
    }

    /**
     * Alacaklı Sil
     */
    public function delete_creditor($id)
    {
        $id = (int) $id;

        // Bu alacaklıya ait borç var mı kontrol et
        $db = \Config\Database::connect();
        $debtCount = $db->table('my_debts')
            ->where('creditor_id', $id)
            ->where('status', 'odenmedi')
            ->countAllResults();

        if ($debtCount > 0) {
            $this->session->setFlashdata('error', 'Bu alacaklıya ait ödenmemiş borçlar var. Önce borçları silin.');
            return redirect()->to('/Munu#creditors');
        }

        if ($this->creditorModel->delete($id)) {
            $this->session->setFlashdata('success', 'Alacaklı silindi.');
        } else {
            $this->session->setFlashdata('error', 'Silme işlemi başarısız.');
        }

        return redirect()->to('/Munu#creditors');
    }

    /**
     * Alacaklı Güncelle
     */
    public function update_creditor($id)
    {
        $id = (int) $id;

        if (strtolower($this->request->getMethod()) === 'post') {
            $data = [
                'creditor_name' => trim((string) $this->request->getPost('creditor_name')),
                'phone' => trim((string) $this->request->getPost('phone')),
                'email' => trim((string) $this->request->getPost('email')),
                'notes' => trim((string) $this->request->getPost('notes')),
                'status' => $this->request->getPost('status') ?: 'aktif'
            ];

            $db = \Config\Database::connect();
            if ($db->table('creditors')->where('id', $id)->update($data)) {
                $this->session->setFlashdata('success', 'Alacaklı güncellendi.');
            } else {
                $this->session->setFlashdata('error', 'Güncelleme başarısız.');
            }
        }

        return redirect()->to('/Munu#creditors');
    }

    /**
     * Alacaklı Bilgilerini Getir (AJAX)
     */
    public function get_creditor($id)
    {
        $id = (int) $id;
        $creditor = $this->creditorModel->find($id);

        return $this->response->setJSON([
            'creditor' => $creditor
        ]);
    }

    /**
     * Alacaklı Detay Raporu
     */
    public function creditor_report($id)
    {
        $id = (int) $id;
        $creditor = $this->creditorModel->find($id);

        if (!$creditor) {
            $this->session->setFlashdata('error', 'Alacaklı bulunamadı!');
            return redirect()->to('/Munu#creditors');
        }

        // Bu alacaklıya olan borçları al
        $debts = $this->myDebtModel->getByCreditor($id);

        // İstatistikler
        $totalDebt = 0;
        $totalPaid = 0;
        $unpaidCount = 0;
        $paidCount = 0;

        foreach ($debts as $debt) {
            if ($debt['status'] === 'odenmedi') {
                $totalDebt += (float) $debt['amount'];
                $unpaidCount++;
            } else {
                $totalPaid += (float) $debt['amount'];
                $paidCount++;
            }
        }

        // Kategorilere göre dağılım
        $categoryStats = [];
        foreach ($debts as $debt) {
            $catName = $debt['category_name'] ?? 'Diğer';
            if (!isset($categoryStats[$catName])) {
                $categoryStats[$catName] = [
                    'name' => $catName,
                    'icon' => $debt['category_icon'] ?? 'fa-tag',
                    'color' => $debt['category_color'] ?? '#6b7280',
                    'total' => 0,
                    'count' => 0
                ];
            }
            $categoryStats[$catName]['total'] += (float) $debt['amount'];
            $categoryStats[$catName]['count']++;
        }

        $data = [
            'creditor' => $creditor,
            'debts' => $debts,
            'totalDebt' => $totalDebt,
            'totalPaid' => $totalPaid,
            'unpaidCount' => $unpaidCount,
            'paidCount' => $paidCount,
            'categoryStats' => array_values($categoryStats),
            'expense_categories' => $this->expenseCategoryModel->getActive()
        ];

        return view('creditor_report_view', $data);
    }

    /**
     * Borcu Ödenmiş Olarak İşaretle
     */
    public function pay_my_debt($id)
    {
        if ($this->myDebtModel->markAsPaid($id)) {
            $this->session->setFlashdata('success', 'Borç ödenmiş olarak işaretlendi.');
        } else {
            $this->session->setFlashdata('error', 'İşlem sırasında hata oluştu.');
        }

        // Redirect URL varsa oraya git
        $redirect = $this->request->getGet('redirect');
        if ($redirect) {
            return redirect()->to($redirect);
        }
        return redirect()->to('/Munu#mydebts');
    }

    /**
     * Borç Sil
     */
    public function delete_my_debt($id)
    {
        if ($this->myDebtModel->delete($id)) {
            $this->session->setFlashdata('success', 'Borç silindi.');
        }

        // Redirect URL varsa oraya git
        $redirect = $this->request->getGet('redirect');
        if ($redirect) {
            return redirect()->to($redirect);
        }
        return redirect()->to('/Munu#mydebts');
    }

    // =========================================
    // KULLANICI İŞLEMLERİ
    // =========================================

    /**
     * Kullanıcı Ekle
     */
    public function add_user()
    {
        if ($this->request->getMethod() === 'post') {
            $data = [
                'username' => trim($this->request->getPost('username')),
                'password' => $this->request->getPost('password') ?: '1234',
                'full_name' => trim($this->request->getPost('full_name')),
                'email' => trim($this->request->getPost('email')),
                'phone' => trim($this->request->getPost('phone')),
                'role' => $this->request->getPost('role') ?: 'personel',
                'is_active' => 1
            ];

            if ($this->userModel->save($data)) {
                $this->session->setFlashdata('success', 'Kullanıcı oluşturuldu.');
            } else {
                $this->session->setFlashdata('error', 'Kullanıcı oluşturulurken hata oluştu.');
            }
        }

        return redirect()->to('/Munu#users');
    }

    /**
     * Kullanıcı Sil
     */
    public function delete_user($id)
    {
        // Admin silinemesin
        $user = $this->userModel->find($id);
        if ($user && $user['username'] === 'admin') {
            $this->session->setFlashdata('error', 'Yönetici hesabı silinemez!');
            return redirect()->to('/Munu#users');
        }

        if ($this->userModel->delete($id)) {
            $this->session->setFlashdata('success', 'Kullanıcı silindi.');
        }

        return redirect()->to('/Munu#users');
    }

    /**
     * Şifre Değiştir
     */
    public function change_password()
    {
        if ($this->request->getMethod() === 'post') {
            $userId = session()->get('user_id');
            $currentPassword = $this->request->getPost('current_password');
            $newPassword = $this->request->getPost('new_password');
            $confirmPassword = $this->request->getPost('confirm_password');

            if ($newPassword !== $confirmPassword) {
                $this->session->setFlashdata('error', 'Yeni şifreler eşleşmiyor!');
                return redirect()->to('/Munu');
            }

            if (strlen($newPassword) < 6) {
                $this->session->setFlashdata('error', 'Yeni şifre en az 6 karakter olmalıdır!');
                return redirect()->to('/Munu');
            }

            $user = $this->userModel->find($userId);

            if ($user && password_verify($currentPassword, $user['password'])) {
                // UserModel::changePassword metodu şirketi de kontrol ederek güvenli güncelleme yapar
                $this->userModel->changePassword($userId, $newPassword);
                $this->session->setFlashdata('success', 'Şifreniz başarıyla güncellendi.');
            } else {
                $this->session->setFlashdata('error', 'Mevcut şifreniz hatalı, lütfen tekrar deneyin.');
            }
        }

        return redirect()->to('/Munu');
    }

    // =========================================
    // AJAX İŞLEMLERİ
    // =========================================

    /**
     * Müşteri Arama (AJAX)
     */
    public function search_customer()
    {
        $keyword = $this->request->getGet('q');
        $results = $this->customerModel->search($keyword);
        return $this->response->setJSON($results);
    }

    /**
     * Dashboard Verileri (AJAX)
     */
    public function get_dashboard_data()
    {
        $data = [
            'total_receivables' => $this->customerModel->getTotalReceivables(),
            'total_my_debts' => $this->myDebtModel->getTotalUnpaidDebt(),
            'critical_stock' => $this->productModel->getCriticalStockCount(),
            'customer_count' => $this->customerModel->where('status', 'aktif')->countAllResults(),
            'monthly_data' => $this->transactionModel->getMonthlyTotals(6)
        ];

        return $this->response->setJSON($data);
    }

    /**
     * Aylık Rapor Verileri (AJAX)
     */
    public function get_monthly_report()
    {
        $month = $this->request->getGet('month') ?: date('m');
        $year = $this->request->getGet('year') ?: date('Y');

        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $transactions = $this->transactionModel->getByDateRange($startDate, $endDate);

        return $this->response->setJSON($transactions);
    }
}
