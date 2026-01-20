<?php

namespace App\Models;

use CodeIgniter\Model;

class CreditorModel extends Model
{
    protected $table = 'creditors';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'creditor_name',
        'phone',
        'email',
        'notes',
        'total_debt',
        'status',
        'company_id'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'creditor_name' => 'required|min_length[2]|max_length[255]'
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
            $this->where('creditors.company_id', $companyId);
        }
        return $data;
    }

    /**
     * Aktif alacaklıları getir
     */
    public function getActive()
    {
        $companyId = $this->getCompanyId();
        return $this->where('status', 'aktif')
            ->where('company_id', $companyId)
            ->orderBy('creditor_name', 'ASC')
            ->findAll();
    }

    /**
     * Alacaklının toplam borcunu güncelle
     */
    public function updateTotalDebt($creditorId)
    {
        $db = \Config\Database::connect();
        $companyId = $this->getCompanyId();

        // Bu alacaklıya olan ödenmemiş borçları topla
        $result = $db->table('my_debts')
            ->selectSum('amount')
            ->where('creditor_id', $creditorId)
            ->where('status', 'odenmedi')
            ->where('company_id', $companyId)
            ->get()
            ->getRowArray();

        $totalDebt = $result['amount'] ?? 0;

        return $db->table('creditors')
            ->where('id', $creditorId)
            ->where('company_id', $companyId)
            ->update(['total_debt' => $totalDebt]);
    }

    /**
     * Borcu olan alacaklıları getir
     */
    public function getWithDebt()
    {
        $companyId = $this->getCompanyId();
        return $this->where('total_debt >', 0)
            ->where('status', 'aktif')
            ->where('company_id', $companyId)
            ->orderBy('total_debt', 'DESC')
            ->findAll();
    }
}
