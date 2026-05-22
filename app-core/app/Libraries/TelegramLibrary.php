<?php

namespace App\Libraries;

/**
 * TelegramLibrary
 * 
 * A simple library to send messages to Telegram via Bot API.
 */
class TelegramLibrary
{
    protected $botToken;
    protected $chatId;
    protected $apiUrl = 'https://api.telegram.org/bot';

    public function __construct($botToken = null, $chatId = null)
    {
        $this->botToken = $botToken ?: env('telegram.botToken');
        $this->chatId = $chatId ?: env('telegram.chatId');
    }

    public function setChatId($chatId)
    {
        $this->chatId = $chatId;
        return $this;
    }

    /**
     * Send a text message to the configured chat ID.
     * 
     * @param string $message
     * @param string $parseMode (HTML or Markdown)
     * @return bool|array
     */
    public function sendMessage($message, $parseMode = 'HTML')
    {
        if (empty($this->botToken) || empty($this->chatId)) {
            log_message('error', 'Telegram Bot Token or Chat ID is not configured in .env');
            return false;
        }

        $url = $this->apiUrl . $this->botToken . '/sendMessage';
        
        $data = [
            'chat_id'    => $this->chatId,
            'text'       => $message,
            'parse_mode' => $parseMode,
        ];

        return $this->sendRequest($url, $data);
    }

    /**
     * Send a request to Telegram API.
     */
    protected function sendRequest($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            log_message('error', 'Telegram API Error: ' . $error);
            return false;
        }

        $response = json_decode($result, true);
        if (!$response || !isset($response['ok']) || !$response['ok']) {
            log_message('error', 'Telegram API Response Error: ' . ($response['description'] ?? 'Unknown error'));
            return false;
        }

        return $response;
    }
}
