<?php

namespace App\Libraries\Channel;

/**
 * Satu cara mengirim pesan ke grup jamaah, apa pun kanalnya.
 *
 * Dibuat agar pemanggilnya (siaran, pengingat terjadwal, agen AI) tidak perlu
 * tahu bedanya Telegram dan WhatsApp — dan agar kanal ketiga kelak cukup
 * menambah satu kelas, bukan menyebar percabangan ke seluruh kode.
 */
interface ChannelInterface
{
    /**
     * Mengirim pesan ke satu grup.
     *
     * WAJIB mengembalikan false bila gagal, bukan true. Pendahulunya —
     * WhatsAppService::sendMessage() — selalu mengembalikan true meski tidak
     * mengirim apa pun, sehingga kegagalan tidak pernah terlihat siapa pun.
     */
    public function kirim(string $groupId, string $pesan): bool;

    /**
     * Alasan kegagalan terakhir, untuk ditampilkan kepada pengurus.
     */
    public function pesanGalat(): ?string;

    /**
     * Apakah kanal ini siap dipakai (token/kunci sudah diisi).
     * Dipakai untuk memberi tahu pengurus sebelum ia menulis panjang-panjang
     * lalu menekan kirim dan gagal.
     */
    public function siap(): bool;
}
