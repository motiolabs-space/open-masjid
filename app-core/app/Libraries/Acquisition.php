<?php

namespace App\Libraries;

/**
 * Mengingat dari mana seorang pengunjung datang, lalu menyerahkannya saat ia
 * mendaftar.
 *
 * KENAPA DISIMPAN DI COOKIE, BUKAN LANGSUNG DIPAKAI
 * Orang jarang mendaftar pada kunjungan pertama. Ia mengklik tautan Instagram
 * hari ini, berkeliling, lalu baru mendaftar tiga hari kemudian lewat pencarian
 * Google. Bila asalnya hanya dibaca saat formulir dikirim, seluruh pendaftaran
 * akan tercatat "datang langsung" dan datanya justru menyesatkan.
 *
 * KENAPA SENTUHAN PERTAMA YANG MENANG
 * Nilai yang sudah tersimpan TIDAK ditimpa. Yang ingin dijawab adalah "kanal
 * mana yang memperkenalkan masjid ini kepada kami", bukan "halaman apa yang
 * terakhir ia buka". Untuk produk yang keputusannya lambat seperti ini,
 * sentuhan pertama jauh lebih berguna.
 *
 * SELURUH NILAINYA BERASAL DARI PENGUNJUNG, jadi diperlakukan sebagai masukan
 * tak tepercaya: dipangkas panjangnya dan dibersihkan dari karakter kendali
 * sebelum disimpan.
 */
class Acquisition
{
    /** Nama cookie penyimpan asal kunjungan. */
    public const COOKIE = 'masjid_acq';

    /** Umur cookie: cukup panjang untuk menampung jeda mempertimbangkan. */
    private const UMUR_HARI = 90;

    /** Kolom yang diisi ke basis data. */
    public const KOLOM = ['utm_source', 'utm_medium', 'utm_campaign', 'referrer'];

    /**
     * Dipanggil filter pada tiap permintaan GET. Menyimpan asal kunjungan bila
     * belum pernah tersimpan.
     */
    public static function rekam(\CodeIgniter\HTTP\RequestInterface $request): void
    {
        // Sentuhan pertama menang: sudah ada, tidak diapa-apakan.
        if (! empty($_COOKIE[self::COOKIE])) {
            return;
        }

        $data = array_filter([
            'utm_source'   => self::bersihkan($request->getGet('utm_source'), 100),
            'utm_medium'   => self::bersihkan($request->getGet('utm_medium'), 100),
            'utm_campaign' => self::bersihkan($request->getGet('utm_campaign'), 100),
        ]);

        // Tanpa penanda kampanye, rujukan dari situs LUAR masih bercerita.
        // Rujukan dari halaman sendiri tidak — itu sekadar berpindah halaman.
        if ($data === []) {
            $rujukan = self::rujukanLuar($request);
            if ($rujukan === null) {
                return;
            }
            $data['referrer'] = $rujukan;
        } else {
            $rujukan = self::rujukanLuar($request);
            if ($rujukan !== null) {
                $data['referrer'] = $rujukan;
            }
        }

        setcookie(self::COOKIE, json_encode($data), [
            'expires'  => time() + self::UMUR_HARI * 86400,
            'path'     => '/',
            'secure'   => ! empty($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Asal kunjungan yang tersimpan, siap disatukan ke data pendaftaran.
     *
     * @return array<string, string|null> selalu berisi seluruh kunci di KOLOM,
     *                                    bernilai null bila tak diketahui.
     */
    public static function untukPendaftaran(): array
    {
        $kosong = array_fill_keys(self::KOLOM, null);

        if (empty($_COOKIE[self::COOKIE])) {
            return $kosong;
        }

        $data = json_decode((string) $_COOKIE[self::COOKIE], true);
        if (! is_array($data)) {
            return $kosong;
        }

        $hasil = $kosong;
        foreach (self::KOLOM as $k) {
            if (! empty($data[$k]) && is_string($data[$k])) {
                $hasil[$k] = self::bersihkan($data[$k], $k === 'referrer' ? 255 : 100);
            }
        }

        return $hasil;
    }

    /** Host rujukan bila berasal dari luar situs ini; null bila dari dalam. */
    private static function rujukanLuar(\CodeIgniter\HTTP\RequestInterface $request): ?string
    {
        $rujukan = trim($request->getServer('HTTP_REFERER') ?? '');
        if ($rujukan === '') {
            return null;
        }

        $hostRujukan = strtolower((string) parse_url($rujukan, PHP_URL_HOST));
        $hostSendiri = strtolower((string) parse_url(base_url(), PHP_URL_HOST));
        if ($hostRujukan === '' || $hostRujukan === $hostSendiri) {
            return null;
        }

        // Host saja, tanpa path: jalur lengkap kerap memuat kueri pencarian atau
        // penanda pribadi yang tak perlu ikut tersimpan.
        return self::bersihkan($hostRujukan, 255);
    }

    /** Memangkas dan membuang karakter yang tak pantas masuk basis data. */
    private static function bersihkan($nilai, int $maks): ?string
    {
        if (! is_string($nilai)) {
            return null;
        }

        $nilai = preg_replace('/[\x00-\x1F\x7F]/u', '', trim($nilai));
        $nilai = mb_substr((string) $nilai, 0, $maks);

        return $nilai === '' ? null : $nilai;
    }
}
