<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'product_code',
        'product_name',
        'category',
        'unit',
        'stock_quantity',
        'min_stock_level',
        'unit_price',
        'description',
        'status',
        'company_id'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation kuralları
    protected $validationRules = [
        'product_name' => 'required|min_length[2]|max_length[200]',
        'unit_price' => 'permit_empty|numeric',
        'stock_quantity' => 'permit_empty|integer'
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
            $this->where('products.company_id', $companyId);
        }
        return $data;
    }

    /**
     * Aktif ürünleri getir
     */
    public function getActiveProducts()
    {
        $companyId = $this->getCompanyId();
        return $this->where('company_id', $companyId)
            ->groupStart()
            ->where('status', 'aktif')
            ->orWhere('status IS NULL')
            ->groupEnd()
            ->orderBy('product_name', 'ASC')
            ->findAll();
    }

    /**
     * Kritik stok seviyesindeki ürünleri getir
     */
    public function getCriticalStock()
    {
        $companyId = $this->getCompanyId();
        return $this->where('stock_quantity < min_stock_level', null, false)
            ->where('company_id', $companyId)
            ->groupStart()
            ->where('status', 'aktif')
            ->orWhere('status IS NULL')
            ->groupEnd()
            ->orderBy('stock_quantity', 'ASC')
            ->findAll();
    }

    /**
     * Kritik stok sayısını getir
     */
    public function getCriticalStockCount()
    {
        $companyId = $this->getCompanyId();
        return $this->where('stock_quantity < min_stock_level', null, false)
            ->where('company_id', $companyId)
            ->groupStart()
            ->where('status', 'aktif')
            ->orWhere('status IS NULL')
            ->groupEnd()
            ->countAllResults();
    }

    /**
     * Stok miktarını güncelle
     */
    public function updateStock($productId, $quantity, $operation = 'add')
    {
        $companyId = $this->getCompanyId();
        $product = $this->find($productId);
        if ($product && $product['company_id'] == $companyId) {
            $newQty = ($operation == 'add')
                ? $product['stock_quantity'] + $quantity
                : $product['stock_quantity'] - $quantity;

            // Stok negatife düşmesin
            $newQty = max(0, $newQty);

            $db = \Config\Database::connect();
            return $db->table('products')
                ->where('id', $productId)
                ->where('company_id', $companyId)
                ->update(['stock_quantity' => $newQty]);
        }
        return false;
    }

    /**
     * Ürün kodu üret
     */
    public function generateProductCode()
    {
        $companyId = $this->getCompanyId();
        $lastProduct = $this->where('company_id', $companyId)
            ->orderBy('id', 'DESC')
            ->first();
        $nextId = $lastProduct ? $lastProduct['id'] + 1 : 1;
        return 'URN' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Kategoriye göre ürünleri getir
     */
    public function getByCategory($category)
    {
        $companyId = $this->getCompanyId();
        return $this->where('category', $category)
            ->where('company_id', $companyId)
            ->groupStart()
            ->where('status', 'aktif')
            ->orWhere('status IS NULL')
            ->groupEnd()
            ->findAll();
    }

    /**
     * Tüm kategorileri getir
     */
    public function getCategories()
    {
        $companyId = $this->getCompanyId();
        return $this->select('category')
            ->where('category IS NOT NULL')
            ->where('category !=', '')
            ->where('company_id', $companyId)
            ->groupBy('category')
            ->findAll();
    }
}
