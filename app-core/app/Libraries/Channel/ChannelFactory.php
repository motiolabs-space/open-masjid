<?php

namespace App\Libraries\Channel;

/**
 * Menyiapkan kanal yang tepat untuk sebuah masjid.
 *
 * Dipisahkan supaya pemanggilnya — siaran, pengingat terjadwal, agen AI — tidak
 * perlu tahu bahwa Telegram memakai token per masjid sementara WhatsApp memakai
 * kunci gateway satu untuk semua.
 */
class ChannelFactory
{
    /**
     * @param array $masjid baris tabel masjid (butuh telegram_bot_token)
     *
     * @throws \InvalidArgumentException bila kanalnya tidak dikenal — sengaja
     *         dilempar, bukan mengembalikan null, agar salah ketik nama kanal
     *         ketahuan saat itu juga, bukan berakhir sebagai pesan yang diam-diam
     *         tidak terkirim.
     */
    public static function untuk(string $channel, array $masjid): ChannelInterface
    {
        return match ($channel) {
            'telegram'  => new TelegramChannel($masjid['telegram_bot_token'] ?? null),
            'whatsapp'  => new WhatsAppChannel(),
            default     => throw new \InvalidArgumentException("Kanal tidak dikenal: {$channel}"),
        };
    }
}
