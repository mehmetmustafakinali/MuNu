<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'customer_name',
        'phone',
        'email',
        'address',
        'city',
        'tax_number',
        'tax_office',
        'customer_type',
        'balance',
        'notes',
        'status',
        'company_id'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation kuralları
    protected $validationRules = [
        'customer_name' => 'required|min_length[2]|max_length[100]',
        'phone' => 'permit_empty|max_length[20]',
        'email' => 'permit_empty|valid_email',
    ];

    protected $validationMessages = [
        'customer_name' => [
            'required' => 'Müşteri adı zorunludur.',
            'min_length' => 'Müşteri adı en az 2 karakter olmalıdır.'
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
            $this->where('customers.company_id', $companyId);
        }
        return $data;
    }

    /**
     * Aktif müşterileri getir
     */
    public function getActiveCustomers()
    {
        $companyId = $this->getCompanyId();
        return $this->where('company_id', $companyId)
            ->groupStart()
            ->where('status', 'aktif')
            ->orWhere('status IS NULL')
            ->groupEnd()
            ->orderBy('customer_name', 'ASC')
            ->findAll();
    }

    /**
     * Borçlu müşterileri getir (balance > 0)
     */
    public function getDebtors()
    {
        $companyId = $this->getCompanyId();
        return $this->where('balance >', 0)
            ->where('company_id', $companyId)
            ->groupStart()
            ->where('status', 'aktif')
            ->orWhere('status IS NULL')
            ->groupEnd()
            ->orderBy('balance', 'DESC')
            ->findAll();
    }

    /**
     * En borçlu N müşteriyi getir
     */
    public function getTopDebtors($limit = 5)
    {
        $companyId = $this->getCompanyId();
        return $this->where('balance >', 0)
            ->where('company_id', $companyId)
            ->groupStart()
            ->where('status', 'aktif')
            ->orWhere('status IS NULL')
            ->groupEnd()
            ->orderBy('balance', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Toplam alacak tutarını hesapla
     */
    public function getTotalReceivables()
    {
        $companyId = $this->getCompanyId();
        $result = $this->selectSum('balance')
            ->where('balance >', 0)
            ->where('company_id', $companyId)
            ->first();
        return $result['balance'] ?? 0;
    }

    /**
     * Müşteri bakiyesini güncelle
     */
    public function updateBalance($customerId, $amount, $type)
    {
        $customerId = (int) $customerId;
        $amount = (float) $amount;
        $type = (string) $type;
        $companyId = $this->getCompanyId();

        $customer = $this->find($customerId);
        if ($customer && $customer['company_id'] == $companyId) {
            $currentBalance = (float) $customer['balance'];
            $newBalance = ($type == 'borc')
                ? $currentBalance + $amount
                : $currentBalance - $amount;

            // Doğrudan query builder kullan
            $db = \Config\Database::connect();
            return $db->table('customers')
                ->where('id', $customerId)
                ->where('company_id', $companyId)
                ->update(['balance' => $newBalance]);
        }
        return false;
    }

    /**
     * Müşteri ara (isim veya telefon ile)
     */
    public function search($keyword)
    {
        $companyId = $this->getCompanyId();
        return $this->where('company_id', $companyId)
            ->groupStart()
            ->like('customer_name', $keyword)
            ->orLike('phone', $keyword)
            ->groupEnd()
            ->findAll();
    }

    /**
     * Aktif müşteri sayısını getir
     */
    public function getActiveCount()
    {
        $companyId = $this->getCompanyId();
        return $this->where('company_id', $companyId)
            ->groupStart()
            ->where('status', 'aktif')
            ->orWhere('status IS NULL')
            ->groupEnd()
            ->countAllResults();
    }
}
