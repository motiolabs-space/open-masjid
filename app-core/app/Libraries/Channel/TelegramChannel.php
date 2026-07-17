<?php

namespace App\Libraries\Channel;

use App\Libraries\TelegramLibrary;

/**
 * Mengirim ke grup Telegram memakai bot milik masjid sendiri.
 *
 * Token diambil dari masjid.telegram_bot_token, bukan dari .env: tiap masjid
 * memakai botnya sendiri, sehingga jamaah melihat nama masjidnya dan token satu
 * masjid tidak pernah bisa dipakai mengirim atas nama masjid lain.
 *
 * CARA PENGURUS MENDAPAT ID GRUP
 * Masukkan bot ke grup, lalu kirim satu pesan apa pun di grup itu. Webhook akan
 * mencatatnya sebagai grup yang menunggu persetujuan (lihat Api\Telegram).
 */
class TelegramChannel implements ChannelInterface
{
    private ?string $token;
    private ?string $galat = null;

    public function __construct(?string $botToken)
    {
        $this->token = $botToken ?: null;
    }

    public function siap(): bool
    {
        return $this->token !== null;
    }

    public function kirim(string $groupId, string $pesan): bool
    {
        if (!$this->siap()) {
            $this->galat = 'Bot Telegram masjid belum disetel. Isi Token Bot pada Pengaturan Masjid.';

            return false;
        }

        $telegram = new TelegramLibrary($this->token, $groupId);

        // TelegramLibrary mengembalikan array balasan bila berhasil, dan false
        // bila gagal — alasannya sudah dicatat ke log olehnya.
        $hasil = $telegram->sendMessage($pesan, 'HTML');

        if ($hasil === false) {
            $this->galat = 'Telegram menolak pesannya. Pastikan bot masih menjadi anggota grup tersebut.';

            return false;
        }

        return true;
    }

    public function pesanGalat(): ?string
    {
        return $this->galat;
    }
}
