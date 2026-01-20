<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpenseCategoryModel extends Model
{
    protected $table = 'expense_categories';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'category_name',
        'icon',
        'color',
        'sort_order',
        'status',
        'company_id'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    protected $validationRules = [
        'category_name' => 'required|min_length[2]|max_length[100]'
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
            $this->where('expense_categories.company_id', $companyId);
        }
        return $data;
    }

    /**
     * Aktif kategorileri sıralı getir
     */
    public function getActive()
    {
        $companyId = $this->getCompanyId();
        return $this->where('status', 'aktif')
            ->where('company_id', $companyId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    /**
     * Kategori bazlı harcama toplamlarını getir
     */
    public function getCategoryTotals($startDate = null, $endDate = null)
    {
        $db = \Config\Database::connect();
        $companyId = $this->getCompanyId();

        $builder = $db->table('expense_categories ec')
            ->select('ec.id, ec.category_name, ec.icon, ec.color, COALESCE(SUM(md.amount), 0) as total')
            ->join('my_debts md', 'md.category_id = ec.id AND md.status = "odenmedi" AND md.company_id = ' . (int) $companyId, 'left')
            ->where('ec.status', 'aktif')
            ->where('ec.company_id', $companyId)
            ->groupBy('ec.id')
            ->orderBy('total', 'DESC');

        if ($startDate && $endDate) {
            $builder->where('md.created_at >=', $startDate)
                ->where('md.created_at <=', $endDate);
        }

        return $builder->get()->getResultArray();
    }
}
