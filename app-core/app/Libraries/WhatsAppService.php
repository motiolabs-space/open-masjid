<?php

namespace App\Libraries;

use App\Libraries\Channel\WhatsAppChannel;

/**
 * Pesan WhatsApp perorangan (bukan grup) — saat ini: kuitansi donasi.
 *
 * SEBELUMNYA KELAS INI TIDAK PERNAH MENGIRIM APA PUN
 * sendMessage() hanya menulis baris log lalu selalu mengembalikan true; seluruh
 * kode gateway-nya berupa komentar. Akibatnya tidak satu pun kuitansi donasi
 * pernah sampai ke donatur sejak awal, dan tidak ada yang tahu — karena
 * kegagalan selalu dilaporkan sebagai keberhasilan. Pengirimannya kini
 * diserahkan ke WhatsAppChannel, yang benar-benar menghubungi gateway dan
 * mengembalikan false bila gagal.
 *
 * Kuncinya milik masjid yang bersangkutan, bukan platform — lihat alasannya
 * pada migrasi AddWhatsappTokenToMasjid.
 */
class WhatsAppService
{
    private WhatsAppChannel $kanal;

    /**
     * @param string|null $apiKey masjid.whatsapp_api_key milik masjid pengirim.
     *        Wajib diisi pemanggil: tanpa itu tidak ada yang terkirim, dan
     *        kelas ini tidak lagi berpura-pura sebaliknya.
     */
    public function __construct(?string $apiKey = null)
    {
        $this->kanal = new WhatsAppChannel($apiKey);
    }

    public function siap(): bool
    {
        return $this->kanal->siap();
    }

    public function pesanGalat(): ?string
    {
        return $this->kanal->pesanGalat();
    }

    /**
     * Kuitansi "Jazakallah" untuk donatur.
     *
     * @return bool false bila tidak terkirim — pemanggil WAJIB memeriksanya.
     */
    public function sendDonationReceipt(string $phone, array $data): bool
    {
        $masjidName = $data['masjid_name'] ?? 'Masjid Kami';
        $amount     = number_format((float) ($data['amount'] ?? 0), 0, ',', '.');
        $donorName  = $data['donor_name'] ?? 'Hamba Allah';
        $program    = $data['program_name'] ?? 'Umum';

        $message = "Jazakallah Khairaan Katsiira, Bapak/Ibu *{$donorName}*.\n\n";
        $message .= "Kami telah menerima donasi Anda melalui *Masj.id* sebesar *Rp {$amount}* untuk program *{$program}* di *{$masjidName}*.\n\n";
        $message .= "Semoga menjadi pemberat amal timbangan di akhirat kelak. Aamiin.\n\n";
        $message .= "---\n";
        $message .= 'Cek laporan amanah di: ' . base_url(($data['masjid_username'] ?? '') . '/laporan');

        return $this->kanal->kirim($this->normalkanNomor($phone), $message);
    }

    /**
     * 08xx… menjadi 62xx…; Fonnte memerlukan bentuk internasional.
     */
    private function normalkanNomor(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        return $phone;
    }
}
