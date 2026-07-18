<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\MasjidModel;
use App\Models\MasjidGroupModel;
use App\Models\MasjidFinanceTransactionModel;
use App\Models\MasjidProgramModel;
use App\Libraries\TelegramLibrary;
use App\Libraries\SumoPodAI;

/**
 * Webhook bot Telegram per masjid.
 *
 * Bot menjawab pertanyaan jamaah seputar masjid (keuangan, program) memakai AI.
 * Data yang dipakainya — ringkasan keuangan bulan ini — memang sudah tampil
 * publik di halaman /laporan, jadi menjawab di japri siapa pun tidak
 * membocorkan apa-apa.
 *
 * DI GRUP, DUA HAL WAJIB DIJAGA
 *  1. Hanya grup yang TERDAFTAR & AKTIF di masjid_groups yang dilayani.
 *     Sebelumnya bot memakai chat_id apa pun yang mengirim pesan, sehingga siapa
 *     pun bisa memasukkan bot sebuah masjid ke grupnya sendiri lalu memancing
 *     keluar ringkasan keuangan masjid itu. Grup tak dikenal kini hanya dicatat
 *     sebagai menunggu-persetujuan, tidak dilayani.
 *  2. Bot hanya menjawab saat DI-MENTION atau pesannya DI-REPLY. Tanpa ini bot
 *     menyahut setiap pesan di grup dan membanjiri obrolan jamaah.
 */
class Telegram extends BaseController
{
    public function webhook($username)
    {
        $request = json_decode(file_get_contents('php://input'), true);
        if (!$request) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'No payload']);
        }

        $message = $request['message'] ?? $request['edited_message'] ?? null;
        if (!$message || !isset($message['text'])) {
            // Balas 200: sinyal ke Telegram bahwa update sudah diterima, supaya
            // ia tidak mengirim ulang. Bukan pesan teks — tidak ada yang perlu
            // dikerjakan.
            return $this->response->setStatusCode(200)->setJSON(['status' => 'success', 'message' => 'Not a text message']);
        }

        $masjid = (new MasjidModel())->where('username', $username)->first();
        if (!$masjid || empty($masjid['telegram_bot_token'])) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Masjid not found or bot not configured']);
        }

        $botToken = $masjid['telegram_bot_token'];
        $chatId   = $message['chat']['id'];
        $chatType = $message['chat']['type'] ?? 'private';
        $userText = $message['text'];

        $telegram = new TelegramLibrary($botToken, $chatId);

        // Grup: lolos hanya bila terdaftar, aktif, dan bot memang disapa.
        if (in_array($chatType, ['group', 'supergroup'], true)) {
            if (!$this->grupBolehDilayani($masjid, $message, $telegram)) {
                // 200 selalu: apa pun keputusannya, update sudah kita tangani.
                // 4xx membuat Telegram mengirim ulang update yang sama berkali-kali.
                return $this->response->setStatusCode(200)->setJSON(['status' => 'ignored']);
            }
        }

        $this->jawabPertanyaan($masjid, $userText, $telegram, $botToken, $chatId, $message['message_id'] ?? null);

        return $this->response->setStatusCode(200)->setJSON(['status' => 'success']);
    }

    /**
     * Menentukan apakah sebuah pesan grup boleh dijawab, sekaligus mencatat grup
     * baru sebagai menunggu-persetujuan.
     */
    private function grupBolehDilayani(array $masjid, array $message, TelegramLibrary $telegram): bool
    {
        $groupId    = (string) $message['chat']['id'];
        $groupModel = new MasjidGroupModel();

        $grup = $groupModel->cari('telegram', $groupId);

        if ($grup === null) {
            // Grup belum dikenal: catat agar group_id-nya muncul di halaman
            // kelola grup, lalu diam. Nama grup diambil dari judulnya bila ada.
            $groupModel->catatPending(
                (int) $masjid['id'],
                'telegram',
                $groupId,
                (string) ($message['chat']['title'] ?? '')
            );

            return false;
        }

        // Terdaftar tetapi milik masjid lain, atau masih menunggu persetujuan:
        // jangan layani. (Grup unik lintas masjid, jadi ini juga mencegah satu
        // grup dipakai dua masjid.)
        if ((int) $grup['masjid_id'] !== (int) $masjid['id'] || (int) $grup['is_active'] !== 1) {
            return false;
        }

        // Aktif: hanya jawab bila bot benar-benar disapa, bukan tiap pesan.
        return $this->botDisapa($message, $telegram);
    }

    /**
     * Apakah pesan ini ditujukan ke bot — lewat mention @botusername atau
     * membalas salah satu pesan bot.
     */
    private function botDisapa(array $message, TelegramLibrary $telegram): bool
    {
        $me = $telegram->getMe();
        if ($me === null) {
            // Identitas bot tak terbaca: lebih baik diam daripada menyahut semua
            // pesan grup. Pengurus akan menyadarinya karena bot tampak bisu.
            return false;
        }

        // Balasan terhadap pesan bot.
        $pembalas = $message['reply_to_message']['from']['id'] ?? null;
        if ($pembalas !== null && (int) $pembalas === $me['id']) {
            return true;
        }

        // Mention @botusername di dalam teks.
        if ($me['username'] !== '' && stripos($message['text'], '@' . $me['username']) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Mengumpulkan data masjid, bertanya ke AI, lalu membalas.
     * (Isi method ini sama dengan perilaku lama; hanya dipindah ke sini agar
     * penjagaan grup berada di depan.)
     */
    private function jawabPertanyaan(array $masjid, string $userText, TelegramLibrary $telegram, string $botToken, $chatId, $replyToMessageId): void
    {
        $financeModel = new MasjidFinanceTransactionModel();
        $programModel = new MasjidProgramModel();

        $finances = $financeModel->where('masjid_id', $masjid['id'])
            ->where('MONTH(date)', date('m'))
            ->where('YEAR(date)', date('Y'))
            ->findAll();

        $totalPemasukan = 0;
        $totalPengeluaran = 0;
        foreach ($finances as $f) {
            if ($f['type'] === 'pemasukan') $totalPemasukan += $f['amount'];
            if ($f['type'] === 'pengeluaran') $totalPengeluaran += $f['amount'];
        }

        $programs = $programModel->where('masjid_id', $masjid['id'])
            ->where('status', 'published')
            ->findAll(5);
        $programList = [];
        foreach ($programs as $p) {
            $programList[] = "- {$p['title']} (Target: Rp " . number_format($p['target_donation'], 0, ',', '.') . ")";
        }
        $programStr = implode("\n", $programList);

        $systemPrompt = "Anda adalah asisten AI resmi untuk Masjid '{$masjid['name']}'.
Jawab pertanyaan dari jamaah dengan ramah, sopan, dan Islami.
Berikut adalah data terkini masjid bulan ini:
- Total Pemasukan: Rp " . number_format($totalPemasukan, 0, ',', '.') . "
- Total Pengeluaran: Rp " . number_format($totalPengeluaran, 0, ',', '.') . "
- Saldo Bulan Ini: Rp " . number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') . "

Program/Kegiatan Aktif:
" . ($programStr ?: "Belum ada program aktif.") . "

Jika jamaah bertanya tentang kondisi, keuangan, atau program, rangkumkan berdasarkan data di atas dengan gaya bahasa yang rapi. Jika pertanyaannya di luar konteks masjid, jawab dengan sopan bahwa Anda hanya bisa membantu informasi seputar masjid.";

        $this->sendChatAction($botToken, $chatId, 'typing');

        // Ringan: tanya-jawab jamaah adalah aktivitas harian bervolume tinggi.
        $ai = new SumoPodAI((int) $masjid['id']);
        $aiResponse = $ai->chatCompletion([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userText],
        ], ['tier' => 'ringan', 'feature' => 'telegram']);

        // Di grup, balasan ditautkan ke pesan penanya agar jelas menjawab siapa.
        if ($aiResponse) {
            $telegram->sendMessage($aiResponse, 'Markdown', $replyToMessageId);
        } else {
            $telegram->sendMessage('Mohon maaf, sistem sedang mengalami gangguan. Silakan coba beberapa saat lagi.', 'HTML', $replyToMessageId);
        }
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
        // Verifikasi TLS menyala — lihat catatan pada TelegramLibrary.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // pendek agar tidak menghambat
        curl_exec($ch);
        curl_close($ch);
    }
}
