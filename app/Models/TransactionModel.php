<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'customer_id',
        'transaction_type',
        'amount',
        'description',
        'transaction_date',
        'company_id'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = false; // Bu tabloda updated_at yok

    // Validation kuralları
    protected $validationRules = [
        'customer_id' => 'required|integer',
        'transaction_type' => 'required|in_list[borc,tahsilat]',
        'amount' => 'required|numeric|greater_than[0]',
        'transaction_date' => 'required|valid_date'
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
            $this->where('transactions.company_id', $companyId);
        }
        return $data;
    }

    /**
     * Müşteri adı ile birlikte tüm işlemleri getir
     */
    public function getWithCustomer($limit = null)
    {
        $companyId = $this->getCompanyId();
        $builder = $this->select('transactions.*, customers.customer_name')
            ->join('customers', 'customers.id = transactions.customer_id')
            ->where('transactions.company_id', $companyId)
            ->orderBy('transactions.id', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Belirli müşterinin işlemlerini getir
     */
    public function getByCustomer($customerId)
    {
        $companyId = $this->getCompanyId();
        return $this->where('customer_id', $customerId)
            ->where('company_id', $companyId)
            ->orderBy('transaction_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    /**
     * Son N işlemi getir
     */
    public function getRecent($limit = 5)
    {
        return $this->getWithCustomer($limit);
    }

    /**
     * Belirli tarih aralığındaki işlemleri getir
     */
    public function getByDateRange($startDate, $endDate)
    {
        $companyId = $this->getCompanyId();
        return $this->select('transactions.*, customers.customer_name')
            ->join('customers', 'customers.id = transactions.customer_id')
            ->where('transaction_date >=', $startDate)
            ->where('transaction_date <=', $endDate)
            ->where('transactions.company_id', $companyId)
            ->orderBy('transaction_date', 'DESC')
            ->findAll();
    }

    /**
     * Aylık işlem toplamlarını getir (Grafik için)
     */
    public function getMonthlyTotals($months = 6)
    {
        $result = [];
        $db = \Config\Database::connect();
        $companyId = $this->getCompanyId();

        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = date('Y-m-01', strtotime("-$i months"));
            $monthEnd = date('Y-m-t', strtotime("-$i months"));
            $monthName = date('F', strtotime("-$i months"));

            // Borç toplamı
            $borcQuery = $db->table('transactions')
                ->selectSum('amount')
                ->where('transaction_type', 'borc')
                ->where('transaction_date >=', $monthStart)
                ->where('transaction_date <=', $monthEnd)
                ->where('company_id', $companyId)
                ->get()
                ->getRowArray();
            $borcTotal = $borcQuery['amount'] ?? 0;

            // Tahsilat toplamı
            $tahsilatQuery = $db->table('transactions')
                ->selectSum('amount')
                ->where('transaction_type', 'tahsilat')
                ->where('transaction_date >=', $monthStart)
                ->where('transaction_date <=', $monthEnd)
                ->where('company_id', $companyId)
                ->get()
                ->getRowArray();
            $tahsilatTotal = $tahsilatQuery['amount'] ?? 0;

            $result[] = [
                'month' => $monthName,
                'borc' => (float) $borcTotal,
                'tahsilat' => (float) $tahsilatTotal
            ];
        }

        return $result;
    }
}
