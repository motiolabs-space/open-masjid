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
     * @param string   $message
     * @param string   $parseMode        (HTML or Markdown)
     * @param int|null $replyToMessageId Balas pesan tertentu — di grup, ini
     *                                   memperjelas bot menjawab pertanyaan siapa.
     * @return bool|array
     */
    public function sendMessage($message, $parseMode = 'HTML', $replyToMessageId = null)
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
        if ($replyToMessageId !== null) {
            $data['reply_to_message_id'] = $replyToMessageId;
        }

        return $this->sendRequest($url, $data);
    }

    /**
     * Identitas bot (id & username), untuk mengenali mention dan balasan di
     * grup. Di-cache karena tidak pernah berubah — tanpa itu setiap pesan grup
     * memicu satu panggilan tambahan ke Telegram.
     *
     * @return array|null ['id' => int, 'username' => string]
     */
    public function getMe(): ?array
    {
        if (empty($this->botToken)) {
            return null;
        }

        $cache = \Config\Services::cache();
        $kunci = 'tg_bot_me_' . md5($this->botToken);

        $tersimpan = $cache->get($kunci);
        if (is_array($tersimpan)) {
            return $tersimpan;
        }

        $hasil = $this->sendRequest($this->apiUrl . $this->botToken . '/getMe', []);
        if (!is_array($hasil) || empty($hasil['result']['id'])) {
            return null;
        }

        $me = [
            'id'       => (int) $hasil['result']['id'],
            'username' => (string) ($hasil['result']['username'] ?? ''),
        ];
        $cache->save($kunci, $me, DAY);

        return $me;
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
        // Verifikasi TLS WAJIB menyala. Sebelumnya dimatikan, sehingga token bot
        // dan seluruh isi pesan bisa disadap dan diubah di tengah jalan oleh
        // siapa pun yang menguasai jaringan — dan token bot itu memberi kendali
        // penuh atas bot masjid.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

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
