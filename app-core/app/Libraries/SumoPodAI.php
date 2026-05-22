<?php

namespace App\Libraries;

use Config\Services;

class SumoPodAI
{
    protected $apiUrl;
    protected $apiKey;
    protected $model;
    protected $fallbackModels;
    protected $client;

    public function __construct()
    {
        // Load configurations from .env
        $this->apiUrl = getenv('sumopod.apiUrl') ?: 'https://ai.sumopod.com/v1';
        $this->apiKey = getenv('sumopod.apiKey');
        $this->model  = getenv('sumopod.model') ?: 'gpt-4o-mini';
        
        $fallback = getenv('sumopod.fallbackModels');
        if ($fallback) {
            $this->fallbackModels = array_map('trim', explode(',', $fallback));
        } else {
            $this->fallbackModels = ['gpt-4o-mini', 'claude-3-5-haiku-20241022', 'gemini-1.5-flash'];
        }
        
        $this->client = Services::curlrequest();
    }

    /**
     * Send a chat completion request to SumoPod AI
     *
     * @param string|array $messages The message string or array of messages
     * @param array $options Additional options like max_tokens, temperature
     * @return string|null The response content
     */
    public function chatCompletion($messages, array $options = [])
    {
        // If it's a simple string, convert to messages array format
        if (is_string($messages)) {
            $messages = [
                ['role' => 'user', 'content' => $messages]
            ];
        }

        $primaryModel = $options['model'] ?? $this->model;
        $modelsToTry = [$primaryModel];
        
        foreach ($this->fallbackModels as $fb) {
            if (!in_array($fb, $modelsToTry)) {
                $modelsToTry[] = $fb;
            }
        }

        $url = rtrim($this->apiUrl, '/') . '/chat/completions';
        
        foreach ($modelsToTry as $currentModel) {
            $payload = array_merge([
                'model'    => $currentModel,
                'messages' => $messages,
                'max_tokens'  => 150,
                'temperature' => 0.7,
            ], $options);
            
            // Ensure we use the current model in the loop
            $payload['model'] = $currentModel;

            try {
                $response = $this->client->post($url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type'  => 'application/json',
                    ],
                    'json' => $payload,
                    'http_errors' => false
                ]);

                if ($response->getStatusCode() === 200) {
                    $body = json_decode($response->getBody(), true);
                    return $body['choices'][0]['message']['content'] ?? null;
                }

                // If failed (e.g. 429 Quota Exceeded), log warning and continue to next model
                log_message('warning', "SumoPod AI Error on model {$currentModel}: [" . $response->getStatusCode() . '] ' . $response->getBody());
                continue;

            } catch (\Exception $e) {
                // If network error, log and continue to next model
                log_message('error', "SumoPod AI Exception on model {$currentModel}: " . $e->getMessage());
                continue;
            }
        }

        // If all models fail
        log_message('error', 'SumoPod AI: All fallback models failed.');
        return null;
    }
}
