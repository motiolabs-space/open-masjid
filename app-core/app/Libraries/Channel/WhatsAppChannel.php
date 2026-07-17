<?php

namespace App\Libraries\Channel;

/**
 * Mengirim ke grup WhatsApp lewat gateway Fonnte.
 *
 * MENGAPA GATEWAY, BUKAN API RESMI
 * WhatsApp Cloud API resmi TIDAK mendukung pengiriman ke grup sama sekali —
 * hanya ke nomor perorangan. Satu-satunya cara menjangkau grup adalah gateway
 * yang mengemudikan WhatsApp Web, seperti Fonnte. Konsekuensinya harus
 * disadari: nomor pengirim berisiko diblokir WhatsApp, dan bila Fonnte berubah,
 * kelas ini ikut berubah. Telegram tidak punya masalah ini.
 *
 * Pendahulunya, WhatsAppService::sendMessage(), hanya menulis ke log lalu selalu
 * mengembalikan true — seluruh kode Fonnte-nya dikomentari. Akibatnya tidak satu
 * pun pesan WhatsApp pernah terkirim sejak awal, termasuk kuitansi donasi, dan
 * tidak ada yang tahu karena kegagalannya selalu dilaporkan sebagai berhasil.
 *
 * TOKEN PER MASJID, TIDAK ADA CADANGAN KE AKUN PLATFORM
 * Token diambil dari masjid.whatsapp_api_key — lihat alasannya pada migrasi
 * AddWhatsappTokenToMasjid. Sengaja TIDAK ada cadangan ke env('WHATSAPP_API_KEY'):
 * masjid yang diam-diam memakai nomor bersama akan ikut mati begitu nomor itu
 * diblokir, dan tidak akan tahu sebabnya. Lebih baik ia diberi tahu bahwa
 * kanalnya belum disetel, lalu memakai Telegram yang gratis dan resmi.
 */
class WhatsAppChannel implements ChannelInterface
{
    private const ENDPOINT = 'https://api.fonnte.com/send';

    private ?string $token;
    private ?string $galat = null;

    public function __construct(?string $token)
    {
        $this->token = $token ?: null;
    }

    public function siap(): bool
    {
        return $this->token !== null;
    }

    public function kirim(string $groupId, string $pesan): bool
    {
        if (!$this->siap()) {
            $this->galat = 'WhatsApp masjid belum disetel. Isi Kunci Gateway WhatsApp pada Pengaturan Masjid.';

            return false;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => self::ENDPOINT,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                // Untuk grup, 'target' diisi id grup (…@g.us) dan Fonnte
                // memerlukan penanda ini; tanpa itu ia memperlakukannya sebagai
                // nomor perorangan dan pesannya tidak sampai ke mana-mana.
                'target'  => $groupId,
                'message' => $pesan,
                'delay'   => '2',
            ]),
            CURLOPT_HTTPHEADER     => ['Authorization: ' . $this->token],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $hasil = curl_exec($ch);
        $galatCurl = curl_error($ch);
        curl_close($ch);

        if ($galatCurl !== '') {
            log_message('error', 'WhatsApp (Fonnte) gagal terhubung: ' . $galatCurl);
            $this->galat = 'Tidak dapat terhubung ke gateway WhatsApp.';

            return false;
        }

        $balasan = json_decode((string) $hasil, true);

        // Fonnte menjawab {"status":true,...} bila diterima. Balasan yang tidak
        // terbaca diperlakukan sebagai gagal — lebih baik pengurus mengulang
        // daripada mengira pengumumannya sudah sampai.
        if (!is_array($balasan) || ($balasan['status'] ?? false) !== true) {
            $sebab = is_array($balasan) ? ($balasan['reason'] ?? 'balasan tidak dikenali') : 'balasan tidak dikenali';
            log_message('error', 'WhatsApp (Fonnte) menolak: ' . $sebab);
            $this->galat = 'Gateway WhatsApp menolak pesannya: ' . $sebab;

            return false;
        }

        return true;
    }

    public function pesanGalat(): ?string
    {
        return $this->galat;
    }
}
