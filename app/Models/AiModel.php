<?php

namespace App\Models;

use CodeIgniter\Model;

class AiModel extends Model
{
    protected $table = 'ai_chat_history';
    protected $primaryKey = 'id';
    protected $allowedFields = ['role', 'content', 'created_at', 'company_id'];

    protected $beforeInsert = ['addCompanyId'];
    protected $beforeFind = ['filterByCompany'];

    protected function getCompanyId()
    {
        $session = \Config\Services::session();
        return $session->get('company_id');
    }

    protected function addCompanyId(array $data)
    {
        $data['data']['company_id'] = $this->getCompanyId();
        return $data;
    }

    protected function filterByCompany(array $data)
    {
        $companyId = $this->getCompanyId();
        if ($companyId) {
            $this->where('ai_chat_history.company_id', $companyId);
        }
        return $data;
    }

    /**
     * Mesaj kaydet
     */
    public function saveMessage($role, $content)
    {
        return $this->insert([
            'role' => $role,
            'content' => $content
        ]);
    }

    /**
     * Geçmişi getir (son N kayıt, eskiden yeniye)
     */
    public function getHistory($limit = 10)
    {
        $results = $this->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();

        // Sonuçları tarih sırasına (eskiden yeniye) döndür
        return array_reverse($results);
    }

    /**
     * Geçmişi temizle
     */
    public function clearHistory()
    {
        // CodeIgniter truncate() genellikle tüm tabloyu temizler!
        // Şirket bazlı silme için where+delete kullanmalıyız.
        $companyId = $this->getCompanyId();
        if ($companyId) {
            return $this->where('company_id', $companyId)->delete();
        }
        return false;
    }
}
