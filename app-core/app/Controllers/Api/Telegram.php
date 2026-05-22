<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\MasjidModel;
use App\Models\MasjidFinanceTransactionModel;
use App\Models\MasjidProgramModel;
use App\Libraries\TelegramLibrary;
use App\Libraries\SumoPodAI;

class Telegram extends BaseController
{
    public function webhook($username)
    {
        // 1. Get incoming request from Telegram
        $request = json_decode(file_get_contents('php://input'), true);
        if (!$request) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'No payload']);
        }

        // Handle message or edited_message
        $message = $request['message'] ?? $request['edited_message'] ?? null;
        if (!$message || !isset($message['text'])) {
            return $this->response->setStatusCode(200)->setJSON(['status' => 'success', 'message' => 'Not a text message']);
        }

        $chatId = $message['chat']['id'];
        $userText = $message['text'];

        // 2. Find Masjid by username
        $masjidModel = new MasjidModel();
        $masjid = $masjidModel->where('username', $username)->first();

        if (!$masjid || empty($masjid['telegram_bot_token'])) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Masjid not found or bot not configured']);
        }

        $botToken = $masjid['telegram_bot_token'];
        $telegram = new TelegramLibrary($botToken, $chatId);

        // 3. Process the command / question
        // We'll gather data to send to AI
        $financeModel = new MasjidFinanceTransactionModel();
        $programModel = new MasjidProgramModel();

        // Get recent finance data (this month)
        $month = date('m');
        $year = date('Y');
        
        $finances = $financeModel->where('masjid_id', $masjid['id'])
                                 ->where('MONTH(date)', $month)
                                 ->where('YEAR(date)', $year)
                                 ->findAll();

        $totalPemasukan = 0;
        $totalPengeluaran = 0;
        foreach ($finances as $f) {
            if ($f['type'] === 'pemasukan') $totalPemasukan += $f['amount'];
            if ($f['type'] === 'pengeluaran') $totalPengeluaran += $f['amount'];
        }

        // Get active programs
        $programs = $programModel->where('masjid_id', $masjid['id'])
                                 ->where('status', 'published')
                                 ->findAll(5);
        $programList = [];
        foreach ($programs as $p) {
            $programList[] = "- {$p['title']} (Target: Rp " . number_format($p['target_donation'], 0, ',', '.') . ")";
        }
        $programStr = implode("\n", $programList);

        // 4. Construct Prompt for SumoPod AI
        $systemPrompt = "Anda adalah asisten AI resmi untuk Masjid '{$masjid['name']}'. 
Jawab pertanyaan dari jamaah dengan ramah, sopan, dan Islami. 
Berikut adalah data terkini masjid bulan ini:
- Total Pemasukan: Rp " . number_format($totalPemasukan, 0, ',', '.') . "
- Total Pengeluaran: Rp " . number_format($totalPengeluaran, 0, ',', '.') . "
- Saldo Bulan Ini: Rp " . number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') . "

Program/Kegiatan Aktif:
" . ($programStr ?: "Belum ada program aktif.") . "

Pertanyaan jamaah: \"{$userText}\"

Jika jamaah bertanya tentang kondisi, keuangan, atau program, rangkumkan berdasarkan data di atas dengan gaya bahasa yang rapi (Markdown Telegram didukung). Jika pertanyaannya di luar konteks masjid, jawab dengan sopan bahwa Anda hanya bisa membantu informasi seputar masjid.";

        // Send 'typing' action
        $this->sendChatAction($botToken, $chatId, 'typing');

        $ai = new SumoPodAI();
        $aiResponse = $ai->chatCompletion([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userText]
        ]);

        if ($aiResponse) {
            // 5. Reply to Telegram
            $telegram->sendMessage($aiResponse, 'Markdown');
        } else {
            $telegram->sendMessage("Mohon maaf, sistem sedang mengalami gangguan. Silakan coba beberapa saat lagi.", 'HTML');
        }

        return $this->response->setStatusCode(200)->setJSON(['status' => 'success']);
    }

    private function sendChatAction($botToken, $chatId, $action)
    {
        $url = "https://api.telegram.org/bot{$botToken}/sendChatAction";
        $data = ['chat_id' => $chatId, 'action' => $action];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // short timeout so it doesn't block
        curl_exec($ch);
        curl_close($ch);
    }
}
