<?php

namespace App\Models;

use CodeIgniter\Model;

class NoteModel extends Model
{
    protected $table = 'notes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'title',
        'content',
        'note_type',
        'priority',
        'due_date',
        'reminder_date',
        'is_pinned',
        'color',
        'status',
        'completed_at',
        'company_id'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'title' => 'required|min_length[1]|max_length[255]'
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
            $this->where('notes.company_id', $companyId);
        }
        return $data;
    }

    /**
     * Aktif notları getir (pinned önce)
     */
    public function getActive()
    {
        $db = \Config\Database::connect();
        $companyId = $this->getCompanyId();

        try {
            $result = $db->table('notes')
                ->where('company_id', $companyId)
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();
            return $result ?: [];
        } catch (\Exception $e) {
            log_message('error', 'Notes query error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Bugünkü hatırlatıcıları getir
     */
    public function getTodayReminders()
    {
        $today = date('Y-m-d');
        $companyId = $this->getCompanyId();
        return $this->where('status', 'aktif')
            ->where('DATE(due_date)', $today)
            ->where('company_id', $companyId)
            ->orderBy('due_date', 'ASC')
            ->findAll();
    }

    /**
     * Yaklaşan hatırlatıcıları getir (7 gün içinde)
     */
    public function getUpcoming($days = 7)
    {
        $today = date('Y-m-d');
        $futureDate = date('Y-m-d', strtotime("+$days days"));
        $companyId = $this->getCompanyId();

        return $this->where('status', 'aktif')
            ->where('due_date IS NOT NULL')
            ->where('DATE(due_date) >=', $today)
            ->where('DATE(due_date) <=', $futureDate)
            ->where('company_id', $companyId)
            ->orderBy('due_date', 'ASC')
            ->findAll();
    }

    /**
     * Gecikmiş notları getir
     */
    public function getOverdue()
    {
        $now = date('Y-m-d H:i:s');
        $companyId = $this->getCompanyId();
        return $this->where('status', 'aktif')
            ->where('due_date IS NOT NULL')
            ->where('due_date <', $now)
            ->where('company_id', $companyId)
            ->orderBy('due_date', 'ASC')
            ->findAll();
    }

    /**
     * Tamamlanan notları getir
     */
    public function getCompleted()
    {
        $companyId = $this->getCompanyId();
        return $this->where('status', 'tamamlandi')
            ->where('company_id', $companyId)
            ->orderBy('completed_at', 'DESC')
            ->findAll();
    }

    /**
     * Notu tamamla
     */
    public function markAsCompleted($id)
    {
        $db = \Config\Database::connect();
        $companyId = $this->getCompanyId();
        return $db->table('notes')
            ->where('id', (int) $id)
            ->where('company_id', $companyId)
            ->update([
                'status' => 'tamamlandi',
                'completed_at' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * Notu sabitle/kaldır
     */
    public function togglePin($id)
    {
        $note = $this->find($id);
        $companyId = $this->getCompanyId();
        if ($note && $note['company_id'] == $companyId) {
            $db = \Config\Database::connect();
            return $db->table('notes')
                ->where('id', (int) $id)
                ->where('company_id', $companyId)
                ->update(['is_pinned' => $note['is_pinned'] ? 0 : 1]);
        }
        return false;
    }

    /**
     * Takvim için notları getir (ay bazlı)
     */
    public function getByMonth($year, $month)
    {
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        $companyId = $this->getCompanyId();

        return $this->where('status', 'aktif')
            ->where('due_date IS NOT NULL')
            ->where('DATE(due_date) >=', $startDate)
            ->where('DATE(due_date) <=', $endDate)
            ->where('company_id', $companyId)
            ->orderBy('due_date', 'ASC')
            ->findAll();
    }

    /**
     * Belirli günün notlarını getir
     */
    public function getByDate($date)
    {
        $companyId = $this->getCompanyId();
        return $this->where('status', 'aktif')
            ->where('DATE(due_date)', $date)
            ->where('company_id', $companyId)
            ->orderBy('priority', 'DESC')
            ->orderBy('due_date', 'ASC')
            ->findAll();
    }

    /**
     * Türe göre notları getir
     */
    public function getByType($type)
    {
        $companyId = $this->getCompanyId();
        return $this->where('status', 'aktif')
            ->where('note_type', $type)
            ->where('company_id', $companyId)
            ->orderBy('is_pinned', 'DESC')
            ->orderBy('due_date', 'ASC')
            ->findAll();
    }

    /**
     * İstatistikler
     */
    public function getStats()
    {
        try {
            $db = \Config\Database::connect();
            $companyId = $this->getCompanyId();

            $totalResult = $db->table('notes')
                ->where('status', 'aktif')
                ->where('company_id', $companyId)
                ->countAllResults();
            $total = $totalResult ?? 0;

            $overdueResult = $db->table('notes')
                ->where('status', 'aktif')
                ->where('due_date IS NOT NULL')
                ->where('due_date <', date('Y-m-d H:i:s'))
                ->where('company_id', $companyId)
                ->countAllResults();
            $overdue = $overdueResult ?? 0;

            $today = date('Y-m-d');
            $todayResult = $db->table('notes')
                ->where('status', 'aktif')
                ->where('DATE(due_date)', $today)
                ->where('company_id', $companyId)
                ->countAllResults();
            $todayCount = $todayResult ?? 0;

            $completedResult = $db->table('notes')
                ->where('status', 'tamamlandi')
                ->where('company_id', $companyId)
                ->countAllResults();
            $completed = $completedResult ?? 0;

            return [
                'total' => (int) $total,
                'overdue' => (int) $overdue,
                'today' => (int) $todayCount,
                'completed' => (int) $completed
            ];
        } catch (\Exception $e) {
            log_message('error', 'Notes stats error: ' . $e->getMessage());
            return [
                'total' => 0,
                'overdue' => 0,
                'today' => 0,
                'completed' => 0
            ];
        }
    }
}
