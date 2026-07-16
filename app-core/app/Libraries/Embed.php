<?php

namespace App\Libraries;

/**
 * Membaca tautan yang ditempel pengurus menjadi tampilan sematan yang aman.
 *
 * Mendukung YouTube, TikTok, Instagram, dan Facebook. Tautan lain — misalnya
 * artikel berita — ditampilkan sebagai kartu tautan, bukan diabaikan.
 *
 * ATURAN KEAMANAN
 * Tautan ini diisi pengurus lalu tampil di halaman publik masjid, jadi ia data
 * dari luar dan tidak boleh dipercaya:
 *
 *  1. HTML dari pengguna TIDAK PERNAH disisipkan. Yang diambil hanya ID video
 *     atau kode kiriman lewat pola yang ketat, lalu alamat sematannya disusun
 *     sendiri di sini. Menempelkan potongan <script> dari TikTok atau Instagram
 *     — cara yang disarankan situsnya — berarti mempersilakan siapa pun yang
 *     bisa menulis berita menjalankan skrip di halaman masjid.
 *  2. Host tujuan iframe berasal dari daftar tetap di kelas ini, bukan dari
 *     tautannya. Tanpa itu 'javascript:...' atau situs mana pun bisa masuk ke
 *     src iframe.
 *  3. ID yang diambil dibatasi huruf, angka, dan garis — sehingga tidak bisa
 *     dipakai keluar dari alamat sematan.
 *  4. Tidak ada permintaan jaringan sama sekali. Mengambil <meta og:*> dari
 *     tautan artikel memang membuat kartunya lebih kaya, tetapi berarti server
 *     masjid menghubungi alamat yang ditentukan orang luar — pintu masuk SSRF
 *     yang harus disiapkan sendiri pengamanannya (tolak IP jaringan lokal,
 *     batasi pengalihan, batasi ukuran, batasi waktu). Sengaja belum dilakukan.
 *
 * Kembaliannya data, bukan HTML; penggambarannya di app/Views/partials/embed.php.
 */
class Embed
{
    /**
     * @return array|null null bila tautannya kosong atau bukan http/https.
     *
     * Bentuk kembalian:
     *   ['jenis' => 'iframe', 'penyedia' => 'youtube', 'src' => '...', 'rasio' => 'video'|'tegak']
     *   ['jenis' => 'tautan', 'penyedia' => 'lainnya', 'url' => '...', 'domain' => '...']
     */
    public static function baca(?string $url): ?array
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        // Hanya http/https. Menutup 'javascript:', 'data:', dan sejenisnya
        // sebelum apa pun dikerjakan.
        $skema = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (!in_array($skema, ['http', 'https'], true)) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if ($host === '') {
            return null;
        }

        if ($id = self::idYoutube($url)) {
            return [
                'jenis'    => 'iframe',
                'penyedia' => 'youtube',
                'src'      => 'https://www.youtube-nocookie.com/embed/' . $id,
                'rasio'    => 'video',
            ];
        }

        if ($id = self::idTiktok($url)) {
            return [
                'jenis'    => 'iframe',
                'penyedia' => 'tiktok',
                'src'      => 'https://www.tiktok.com/embed/v2/' . $id,
                'rasio'    => 'tegak',
            ];
        }

        if ($kode = self::kodeInstagram($url)) {
            return [
                'jenis'    => 'iframe',
                'penyedia' => 'instagram',
                'src'      => 'https://www.instagram.com/p/' . $kode . '/embed/',
                'rasio'    => 'tegak',
            ];
        }

        // Sisanya — artikel berita, tautan TikTok pendek (vm.tiktok.com) yang
        // tidak memuat ID, apa pun — disajikan sebagai kartu tautan.
        return [
            'jenis'    => 'tautan',
            'penyedia' => 'lainnya',
            'url'      => $url,
            'domain'   => preg_replace('/^www\./', '', $host),
        ];
    }

    /**
     * Menutup watch?v=, youtu.be/, embed/, shorts/, dan live/.
     * ID YouTube selalu 11 karakter dari [A-Za-z0-9_-].
     */
    private static function idYoutube(string $url): ?string
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $host = preg_replace('/^(www|m)\./', '', $host);

        if (!in_array($host, ['youtube.com', 'youtu.be', 'youtube-nocookie.com'], true)) {
            return null;
        }

        $pola = [
            '~[?&]v=([A-Za-z0-9_-]{11})~',                       // watch?v=
            '~youtu\.be/([A-Za-z0-9_-]{11})~',                   // youtu.be/
            '~/(?:embed|shorts|live|v)/([A-Za-z0-9_-]{11})~',    // embed/ shorts/ live/ v/
        ];

        foreach ($pola as $p) {
            if (preg_match($p, $url, $m)) {
                return $m[1];
            }
        }

        return null;
    }

    /**
     * Hanya bentuk panjang yang memuat ID: tiktok.com/@nama/video/123...
     * Tautan pendek (vm.tiktok.com/xxx) tidak memuat ID dan hanya bisa
     * dipecahkan dengan menghubungi TikTok — lihat aturan 4 di atas.
     */
    private static function idTiktok(string $url): ?string
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $host = preg_replace('/^(www|m)\./', '', $host);

        if ($host !== 'tiktok.com') {
            return null;
        }

        return preg_match('~/video/(\d{5,30})~', $url, $m) ? $m[1] : null;
    }

    /**
     * Menutup /p/, /reel/, /reels/, dan /tv/. Ketiganya disematkan lewat
     * alamat /p/{kode}/embed/ yang sama.
     */
    private static function kodeInstagram(string $url): ?string
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $host = preg_replace('/^(www|m)\./', '', $host);

        if ($host !== 'instagram.com') {
            return null;
        }

        return preg_match('~/(?:p|reels?|tv)/([A-Za-z0-9_-]{5,20})~', $url, $m) ? $m[1] : null;
    }
}
