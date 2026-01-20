<?php

namespace App\Libraries;

class GroqService
{
    private $apiKey;
    private $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
    private $model = 'llama-3.3-70b-versatile'; // Using a powerful model supported by Groq

    public function __construct()
    {
        // Initialize with API Key from .env or use the provided fallback
        $this->apiKey = getenv('GROQ_API_KEY');
        if (!$this->apiKey) {
            log_message('error', 'GROQ_API_KEY not found in .env and no fallback provided');
        }
    }

    /**
     * Send a socket message to Groq AI
     *
     * @param array $messages Full array of messages incl. system prompt
     * @return array|null Response data or null on failure
     */
    public function chat($messages)
    {
        $data = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1024,
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
        ]);

        // SSL verification disable for localhost dev/xampp issues commonly found
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            log_message('error', 'Groq API Curl Error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}
