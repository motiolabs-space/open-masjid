<?php

namespace App\Libraries;

/**
 * Web Push self-hosted dengan VAPID (RFC 8292), TANPA pustaka pihak ketiga.
 *
 * Pengiriman bersifat PAYLOADLESS: badan permintaan kosong, hanya header VAPID
 * (Authorization + TTL). Push service meneruskan "ketukan" ke browser; service
 * worker lalu mengambil isi notifikasi dari server. Dengan begitu enkripsi
 * payload (RFC 8291, aes128gcm — bagian tersulit & paling rawan) tidak
 * diperlukan sama sekali.
 *
 * Kunci VAPID dibuat sekali dengan `php spark push:vapid` lalu disimpan di .env:
 *   vapid.publicKey  = <base64url titik publik EC, 65 byte>
 *   vapid.privateKey = <PEM kunci privat, dikodekan base64 agar muat satu baris>
 *   vapid.subject    = mailto:admin@masj.id  (atau URL)
 */
class WebPush
{
    private ?string $publicKey;
    private $privateKey;      // OpenSSL key resource/objek, atau null
    private string $subject;

    public function __construct()
    {
        $this->publicKey = env('vapid.publicKey') ?: null;
        $this->subject   = env('vapid.subject') ?: 'mailto:admin@masj.id';

        $pemB64 = env('vapid.privateKey');
        $this->privateKey = $pemB64 ? openssl_pkey_get_private(base64_decode($pemB64)) : null;
    }

    /** Siap mengirim bila sepasang kunci VAPID ada dan valid. */
    public function siap(): bool
    {
        return $this->publicKey !== null && $this->privateKey !== false && $this->privateKey !== null;
    }

    public function publicKey(): ?string
    {
        return $this->publicKey;
    }

    /**
     * Membuat sepasang kunci VAPID (EC prime256v1). Dipanggil command generator.
     *
     * @return array{publicKey:string, privateKey:string} privateKey = PEM base64.
     */
    public static function buatKunci(): array
    {
        $res = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name'       => 'prime256v1',
        ]);
        openssl_pkey_export($res, $pem);
        $d = openssl_pkey_get_details($res);

        // Titik publik tak-terkompresi: 0x04 || X(32) || Y(32).
        $point = "\x04" . str_pad($d['ec']['x'], 32, "\x00", STR_PAD_LEFT)
                        . str_pad($d['ec']['y'], 32, "\x00", STR_PAD_LEFT);

        return [
            'publicKey'  => self::b64url($point),
            'privateKey' => base64_encode($pem),
        ];
    }

    /**
     * Host push service yang sah. Endpoint langganan datang dari browser dan
     * disimpan lewat endpoint publik, jadi tak boleh dipercaya begitu saja.
     */
    private const HOST_SAH = [
        'fcm.googleapis.com',                 // Chrome / Chromium
        'updates.push.services.mozilla.com',  // Firefox
        'web.push.apple.com',                 // Safari
        '.notify.windows.com',                // Edge (wns2-*.notify.windows.com)
        '.push.services.mozilla.com',
    ];

    /**
     * Apakah endpoint benar-benar milik layanan push browser?
     *
     * Tanpa pemeriksaan ini, siapa pun bisa mendaftarkan endpoint sembarang
     * (mis. http://127.0.0.1:8080/… atau alamat internal) lalu memancing server
     * mengirim permintaan ke sana saat pengurus menekan broadcast — server
     * dipakai sebagai perantara ke jaringan dalam (SSRF).
     */
    public static function endpointSah(string $endpoint): bool
    {
        $parts = parse_url($endpoint);
        if (! is_array($parts) || ($parts['scheme'] ?? '') !== 'https' || empty($parts['host'])) {
            return false;
        }
        $host = strtolower($parts['host']);

        foreach (self::HOST_SAH as $sah) {
            $cocok = $sah[0] === '.'
                ? str_ends_with($host, $sah)   // subdomain
                : $host === $sah;              // host persis
            if ($cocok) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kirim "ketukan" push payloadless ke satu langganan.
     *
     * @return int Kode HTTP push service (201 = diterima). 0/4xx/5xx = gagal.
     *             404/410 berarti langganan mati dan sebaiknya dihapus pemanggil.
     */
    public function kirim(string $endpoint, int $ttl = 2419200): int
    {
        if (! $this->siap() || ! self::endpointSah($endpoint)) {
            return 0;
        }

        $parts = parse_url($endpoint);
        $audience = $parts['scheme'] . '://' . $parts['host'];
        $jwt = $this->buatJwt($audience);
        if ($jwt === null) {
            return 0;
        }

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => '',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => [
                'Authorization: vapid t=' . $jwt . ',k=' . $this->publicKey,
                'TTL: ' . $ttl,
                'Content-Length: 0',
                'Urgency: normal',
            ],
        ]);
        curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $code;
    }

    /**
     * JWT VAPID (ES256). Tanda tangan DER dari OpenSSL diubah ke bentuk mentah
     * R||S (64 byte) sesuai JOSE.
     */
    public function buatJwt(string $audience): ?string
    {
        $header  = self::b64url(json_encode(['typ' => 'JWT', 'alg' => 'ES256']));
        $payload = self::b64url(json_encode([
            'aud' => $audience,
            'exp' => time() + 43200, // 12 jam
            'sub' => $this->subject,
        ]));
        $data = $header . '.' . $payload;

        $der = '';
        if (! openssl_sign($data, $der, $this->privateKey, OPENSSL_ALGO_SHA256)) {
            return null;
        }

        return $data . '.' . self::b64url(self::derKeMentah($der));
    }

    /** Ubah tanda tangan ECDSA DER menjadi R||S mentah 64 byte. */
    public static function derKeMentah(string $der): string
    {
        $pos = 0;
        $baca = function (string $s, int &$p): string {
            $p++;                       // lewati tag 0x02 (INTEGER)
            $len = ord($s[$p]);
            $p++;
            $val = substr($s, $p, $len);
            $p += $len;
            // Buang byte 0x00 di depan (padding tanda), lalu pad ke 32 byte.
            $val = ltrim($val, "\x00");
            return str_pad($val, 32, "\x00", STR_PAD_LEFT);
        };

        $pos += 2;                      // lewati SEQUENCE tag + panjang
        $r = $baca($der, $pos);
        $s = $baca($der, $pos);
        return $r . $s;
    }

    private static function b64url(string $bin): string
    {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    }
}
