<?php

namespace App\Libraries;

/**
 * Pengirim email transaksional (lupa password, verifikasi, sambutan, laporan).
 *
 * Memakai layanan transaksional lewat HTTP API — bukan SMTP bawaan — supaya
 * email lebih andal masuk inbox (bukan spam) dan tidak bergantung pada mail
 * server hosting. Default: Resend (payload paling sederhana). Provider lain
 * (Mailgun/SendGrid/Postmark) cukup mengganti isi kirimResend() atau menambah
 * cabang di kirim(); struktur pemanggilnya tidak berubah.
 *
 * Config di .env:
 *   mail.provider  = 'resend'
 *   mail.apiKey    = '...'
 *   mail.fromEmail = 'noreply@masj.id'
 *   mail.fromName  = 'Masj.id'
 *
 * Seperti kanal WhatsApp/Telegram: kirim() mengembalikan FALSE bila gagal (tidak
 * pernah berpura-pura berhasil), dan pemanggil WAJIB memeriksanya untuk aksi
 * yang bergantung pada terkirimnya email (mis. jangan bilang "cek email Anda"
 * kalau pengiriman gagal).
 */
class Mailer
{
    private string $provider;
    private ?string $apiKey;
    private string $fromEmail;
    private string $fromName;
    private ?string $galat = null;

    public function __construct()
    {
        $this->provider  = getenv('mail.provider') ?: 'resend';
        $this->apiKey    = getenv('mail.apiKey') ?: null;
        $this->fromEmail = getenv('mail.fromEmail') ?: 'noreply@masj.id';
        $this->fromName  = getenv('mail.fromName') ?: 'Masj.id';
    }

    public function siap(): bool
    {
        return $this->apiKey !== null;
    }

    public function pesanGalat(): ?string
    {
        return $this->galat;
    }

    /**
     * @return bool false bila gagal — periksa selalu untuk alur yang bergantung
     *              pada terkirimnya email.
     */
    public function kirim(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        if (!$this->siap()) {
            $this->galat = 'Layanan email belum disetel (mail.apiKey kosong di .env).';
            log_message('error', 'Mailer: apiKey kosong, email tidak terkirim ke ' . $toEmail);
            return false;
        }

        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            $this->galat = 'Alamat email tujuan tidak sah.';
            return false;
        }

        return match ($this->provider) {
            'resend' => $this->kirimResend($toEmail, $toName, $subject, $htmlBody),
            default  => $this->gagal("Provider email tidak dikenal: {$this->provider}"),
        };
    }

    private function kirimResend(string $toEmail, string $toName, string $subject, string $html): bool
    {
        $payload = [
            'from'    => sprintf('%s <%s>', $this->fromName, $this->fromEmail),
            'to'      => [$toName !== '' ? sprintf('%s <%s>', $toName, $toEmail) : $toEmail],
            'subject' => $subject,
            'html'    => $html,
        ];

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($err !== '') {
            return $this->gagal('Tidak dapat terhubung ke layanan email: ' . $err);
        }

        // Resend membalas 200/201 dengan {"id":"..."} bila diterima.
        if ($code >= 200 && $code < 300) {
            return true;
        }

        $body = json_decode((string) $resp, true);
        $sebab = is_array($body) ? ($body['message'] ?? ($body['name'] ?? 'ditolak')) : 'ditolak';

        return $this->gagal("Layanan email menolak (HTTP {$code}): {$sebab}");
    }

    private function gagal(string $pesan): bool
    {
        $this->galat = $pesan;
        log_message('error', 'Mailer: ' . $pesan);
        return false;
    }
}
