<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\TransactionModel;
use App\Models\MyDebtModel;
use App\Models\CreditorModel;
use App\Models\NoteModel;

class AiController extends BaseController
{
    protected $customerModel;
    protected $transactionModel;
    protected $myDebtModel;
    protected $creditorModel;
    protected $noteModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->transactionModel = new TransactionModel();
        $this->myDebtModel = new MyDebtModel();
        $this->creditorModel = new CreditorModel();
        $this->noteModel = new NoteModel();
    }

    /**
     * AI Chat Endpoint
     * İşletme verilerine erişerek soruları cevaplar
     */
    public function chat()
    {
        // AJAX isteği kontrolü
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Geçersiz istek'
            ]);
        }

        // Kullanıcı mesajını al
        $userMessage = $this->request->getPost('message');

        if (empty($userMessage)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Mesaj boş olamaz'
            ]);
        }

        // İşletme verilerini topla
        $businessData = $this->getBusinessData();

        // System prompt oluştur
        $systemPrompt = $this->buildSystemPrompt($businessData);

        // Groq API'ye istek gönder
        $aiResponse = $this->callGroqAPI($systemPrompt, $userMessage);

        if ($aiResponse['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $aiResponse['message']
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $aiResponse['message']
            ]);
        }
    }

    /**
     * İşletme verilerini topla
     */
    private function getBusinessData()
    {
        // Müşteri verileri
        $customers = $this->customerModel->findAll();
        $customerCount = count($customers);
        $totalReceivables = array_sum(array_column($customers, 'balance'));

        // En borçlu müşteriler
        $topDebtors = $this->customerModel->orderBy('balance', 'DESC')->limit(5)->findAll();

        // İşlem verileri
        $transactions = $this->transactionModel->getRecent(10);

        // Borç verileri
        $myDebts = $this->myDebtModel->getUnpaid();
        $totalMyDebts = array_sum(array_column($myDebts, 'amount'));
        $overdueDebts = count(array_filter($myDebts, function ($d) {
            return $d['due_date'] && strtotime($d['due_date']) < strtotime('today');
        }));

        // Alacaklılar
        $creditors = $this->creditorModel->findAll();

        // Notlar
        $allNotes = $this->noteModel->findAll();
        $pendingNotes = count(array_filter($allNotes, fn($n) => $n['status'] !== 'tamamlandi'));

        return [
            'customer_count' => $customerCount,
            'total_receivables' => $totalReceivables,
            'top_debtors' => $topDebtors,
            'recent_transactions' => $transactions,
            'my_debts' => $myDebts,
            'total_my_debts' => $totalMyDebts,
            'overdue_debts' => $overdueDebts,
            'creditor_count' => count($creditors),
            'creditors' => $creditors,
            'pending_notes' => $pendingNotes,
            'current_date' => date('d.m.Y H:i')
        ];
    }

    /**
     * System prompt oluştur
     */
    private function buildSystemPrompt($data)
    {
        $topDebtorsList = '';
        foreach ($data['top_debtors'] as $i => $debtor) {
            $topDebtorsList .= ($i + 1) . ". {$debtor['customer_name']}: " . number_format($debtor['balance'], 2, ',', '.') . " TL\n";
        }

        $recentTransList = '';
        foreach ($data['recent_transactions'] as $t) {
            $type = $t['transaction_type'] == 'borc' ? 'Borç' : 'Tahsilat';
            $recentTransList .= "- {$t['customer_name']}: {$type} " . number_format($t['amount'], 2, ',', '.') . " TL (" . date('d.m.Y', strtotime($t['transaction_date'])) . ")\n";
        }

        $unpaidDebtsList = '';
        foreach (array_slice($data['my_debts'], 0, 5) as $debt) {
            $dueDate = $debt['due_date'] ? date('d.m.Y', strtotime($debt['due_date'])) : 'Belirsiz';
            $unpaidDebtsList .= "- {$debt['description']}: " . number_format($debt['amount'], 2, ',', '.') . " TL (Son ödeme: {$dueDate})\n";
        }

        return "Sen MUNU Ön Muhasebe sisteminin yapay zeka asistanısın. İşletme sahibine finansal veriler hakkında yardımcı oluyorsun.

GÜNCEL TARİH: {$data['current_date']}

İŞLETME VERİLERİ:
================

📊 ÖZET BİLGİLER:
- Toplam Müşteri Sayısı: {$data['customer_count']}
- Toplam Alacak: " . number_format($data['total_receivables'], 2, ',', '.') . " TL
- Toplam Borcum: " . number_format($data['total_my_debts'], 2, ',', '.') . " TL
- Gecikmiş Borç Sayısı: {$data['overdue_debts']}
- Alacaklı Sayısı: {$data['creditor_count']}
- Bekleyen Not Sayısı: {$data['pending_notes']}

🔥 EN BORÇLU 5 MÜŞTERİ:
{$topDebtorsList}

📝 SON 10 İŞLEM:
{$recentTransList}

💰 ÖDENMEMİŞ BORÇLARIM (İlk 5):
{$unpaidDebtsList}

KURALLAR:
- Türkçe cevap ver
- Kısa ve öz ol
- Sayıları TL formatında göster (1.234,56 TL)
- Finansal tavsiyeler verirken dikkatli ol
- Bilmediğin konularda 'Bu bilgiye erişimim yok' de
- Emoji kullanarak cevapları daha okunabilir yap";
    }

    /**
     * Groq API'ye istek gönder
     */
    private function callGroqAPI($systemPrompt, $userMessage)
    {
        $apiKey = getenv('GROQ_API_KEY') ?: env('GROQ_API_KEY');

        if (empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'API anahtarı bulunamadı. Lütfen .env dosyasını kontrol edin.'
            ];
        }

        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $data = [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => $userMessage
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1024
        ];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            log_message('error', 'Groq API cURL Error: ' . $error);
            return [
                'success' => false,
                'message' => 'Bağlantı hatası: ' . $error
            ];
        }

        $result = json_decode($response, true);

        if ($httpCode !== 200) {
            $errorMsg = $result['error']['message'] ?? 'Bilinmeyen hata';
            log_message('error', 'Groq API Error: ' . $errorMsg);
            return [
                'success' => false,
                'message' => 'API hatası: ' . $errorMsg
            ];
        }

        if (isset($result['choices'][0]['message']['content'])) {
            return [
                'success' => true,
                'message' => $result['choices'][0]['message']['content']
            ];
        }

        return [
            'success' => false,
            'message' => 'Yapay zekadan cevap alınamadı'
        ];
    }
}
