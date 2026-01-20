<?php

namespace App\Models;

use CodeIgniter\Model;

class MyDebtModel extends Model
{
    protected $table = 'my_debts';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'creditor_id',
        'category_id',
        'expense_type',
        'creditor_name',
        'amount',
        'description',
        'due_date',
        'status',
        'paid_date',
        'company_id'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'amount' => 'required|numeric|greater_than[0]'
    ];

    protected $validationMessages = [
        'amount' => [
            'required' => 'Tutar zorunludur.',
            'greater_than' => 'Tutar 0\'dan büyük olmalıdır.'
        ]
    ];

    // Callbacks
    protected $beforeInsert = ['addCompanyId'];
    protected $beforeFind = ['addCompanyFilter'];

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
            $this->where('my_debts.company_id', $companyId);
        }
        return $data;
    }

    /**
     * Tüm borçları kategori ve alacaklı bilgisiyle getir
     */
    public function getAllWithDetails($status = null)
    {
        $db = \Config\Database::connect();
        $companyId = $this->getCompanyId();

        $builder = $db->table('my_debts md')
            ->select('md.*, 
                                ec.category_name, ec.icon as category_icon, ec.color as category_color,
                                cr.creditor_name as creditor_display_name')
            ->join('expense_categories ec', 'ec.id = md.category_id', 'left')
            ->join('creditors cr', 'cr.id = md.creditor_id', 'left')
            ->where('md.company_id', $companyId)
            ->orderBy('md.due_date', 'ASC')
            ->orderBy('md.id', 'DESC');

        if ($status) {
            $builder->where('md.status', $status);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Ödenmemiş borçları getir
     */
    public function getUnpaid()
    {
        return $this->getAllWithDetails('odenmedi');
    }

    /**
     * Ödenmiş borçları getir
     */
    public function getPaid()
    {
        $db = \Config\Database::connect();
        $companyId = $this->getCompanyId();

        return $db->table('my_debts md')
            ->select('md.*, 
                            ec.category_name, ec.icon as category_icon, ec.color as category_color,
                            cr.creditor_name as creditor_display_name')
            ->join('expense_categories ec', 'ec.id = md.category_id', 'left')
            ->join('creditors cr', 'cr.id = md.creditor_id', 'left')
            ->where('md.status', 'odendi')
            ->where('md.company_id', $companyId)
            ->orderBy('md.paid_date', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Toplam ödenmemiş borç tutarını hesapla
     */
    public function getTotalUnpaidDebt()
    {
        $db = \Config\Database::connect();
        $companyId = $this->getCompanyId();

        $result = $db->table('my_debts')
            ->selectSum('amount')
            ->where('status', 'odenmedi')
            ->where('company_id', $companyId)
            ->get()
            ->getRowArray();
        return $result['amount'] ?? 0;
    }

    /**
     * Gecikmiş borçları getir
     */
    public function getOverdue()
    {
        $today = date('Y-m-d');
        $db = \Config\Database::connect();
        $companyId = $this->getCompanyId();

        return $db->table('my_debts md')
            ->select('md.*, 
                            ec.category_name, ec.icon as category_icon, ec.color as category_color,
                            cr.creditor_name as creditor_display_name')
            ->join('expense_categories ec', 'ec.id = md.category_id', 'left')
            ->join('creditors cr', 'cr.id = md.creditor_id', 'left')
            ->where('md.status', 'odenmedi')
            ->where('md.due_date <', $today)
            ->where('md.due_date IS NOT NULL')
            ->where('md.company_id', $companyId)
            ->orderBy('md.due_date', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Gecikmiş borç sayısını getir
     */
    public function getOverdueCount()
    {
        $today = date('Y-m-d');
        $companyId = $this->getCompanyId();

        return $this->where('status', 'odenmedi')
            ->where('due_date <', $today)
            ->where('due_date IS NOT NULL')
            ->where('company_id', $companyId)
            ->countAllResults();
    }

    /**
     * Ödenmemiş borçları getir (AI için)
     */
    public function getUnpaidDebts()
    {
        return $this->getUnpaid();
    }

    /**
     * Borcu ödenmiş olarak işaretle
     */
    public function markAsPaid($debtId)
    {
        $db = \Config\Database::connect();
        $companyId = $this->getCompanyId();

        // Önce borç bilgisini al
        $debt = $this->find($debtId);

        // Borcu ödenmiş yap (company_id kontrolü ile)
        $result = $db->table('my_debts')
            ->where('id', $debtId)
            ->where('company_id', $companyId)
            ->update([
                'status' => 'odendi',
                'paid_date' => date('Y-m-d')
            ]);

        // Alacaklının toplam borcunu güncelle
        if ($result && $debt && $debt['creditor_id']) {
            $creditorModel = new CreditorModel();
            $creditorModel->updateTotalDebt($debt['creditor_id']);
        }

        return $result;
    }

    /**
     * Yaklaşan ödemeler (7 gün içinde)
     */
    public function getUpcoming($days = 7)
    {
        $today = date('Y-m-d');
        $futureDate = date('Y-m-d', strtotime("+$days days"));
        $db = \Config\Database::connect();
        $companyId = $this->getCompanyId();

        return $db->table('my_debts md')
            ->select('md.*, 
                            ec.category_name, ec.icon as category_icon, ec.color as category_color,
                            cr.creditor_name as creditor_display_name')
            ->join('expense_categories ec', 'ec.id = md.category_id', 'left')
            ->join('creditors cr', 'cr.id = md.creditor_id', 'left')
            ->where('md.status', 'odenmedi')
            ->where('md.due_date >=', $today)
            ->where('md.due_date <=', $futureDate)
            ->where('md.company_id', $companyId)
            ->orderBy('md.due_date', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Belirli alacaklının borçlarını getir
     */
    public function getByCreditor($creditorId)
    {
        $db = \Config\Database::connect();
        $companyId = $this->getCompanyId();

        return $db->table('my_debts md')
            ->select('md.*, ec.category_name, ec.icon as category_icon, ec.color as category_color')
            ->join('expense_categories ec', 'ec.id = md.category_id', 'left')
            ->where('md.creditor_id', $creditorId)
            ->where('md.company_id', $companyId)
            ->orderBy('md.status', 'ASC')
            ->orderBy('md.due_date', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Belirli kategorinin borçlarını getir
     */
    public function getByCategory($categoryId)
    {
        $db = \Config\Database::connect();
        $companyId = $this->getCompanyId();

        return $db->table('my_debts md')
            ->select('md.*, cr.creditor_name as creditor_display_name')
            ->join('creditors cr', 'cr.id = md.creditor_id', 'left')
            ->where('md.category_id', $categoryId)
            ->where('md.company_id', $companyId)
            ->orderBy('md.status', 'ASC')
            ->orderBy('md.due_date', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Kategori bazlı özet
     */
    public function getCategorySummary()
    {
        $db = \Config\Database::connect();
        $companyId = $this->getCompanyId();

        return $db->table('my_debts md')
            ->select('ec.id, ec.category_name, ec.icon, ec.color, 
                            COUNT(md.id) as count, 
                            SUM(md.amount) as total')
            ->join('expense_categories ec', 'ec.id = md.category_id', 'left')
            ->where('md.status', 'odenmedi')
            ->where('md.company_id', $companyId)
            ->groupBy('ec.id')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Alacaklı bazlı özet
     */
    public function getCreditorSummary()
    {
        $db = \Config\Database::connect();
        $companyId = $this->getCompanyId();

        return $db->table('my_debts md')
            ->select('cr.id, cr.creditor_name, 
                            COUNT(md.id) as count, 
                            SUM(md.amount) as total')
            ->join('creditors cr', 'cr.id = md.creditor_id', 'left')
            ->where('md.status', 'odenmedi')
            ->where('md.company_id', $companyId)
            ->groupBy('cr.id')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();
    }
}
