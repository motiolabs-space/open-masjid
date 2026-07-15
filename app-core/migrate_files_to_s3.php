<?php

/**
 * Migrasi berkas lama dari disk server ke S3 / DigitalOcean Spaces.
 *
 * LATAR BELAKANG
 * STORAGE_DRIVER sudah 's3', tetapi seluruh berkas yang diunggah SEBELUM
 * peralihan masih berada di disk (STORAGE_PATH). Storage::url() kini selalu
 * mengembalikan URL S3, sehingga berkas-berkas itu tidak lagi tertampilkan —
 * logo masjid, foto utama, galeri, thumbnail berita, dan QRIS menjadi rusak.
 * Skrip ini menyalinnya ke S3 supaya URL yang dipakai halaman menjadi sahih.
 *
 * CARA MENJALANKAN (di server produksi, dari folder app-core):
 *     php migrate_files_to_s3.php            # mode uji: hanya melaporkan
 *     php migrate_files_to_s3.php --jalan    # benar-benar mengunggah
 *
 * AMAN DIULANG: berkas yang sudah ada di S3 dilewati, dan tidak ada satu pun
 * berkas di disk yang dihapus. Basis data tidak diubah — path pada DB sudah
 * benar, yang kurang hanyalah objeknya di S3.
 */

$jalan = in_array('--jalan', $argv, true);

require __DIR__ . '/vendor/autoload.php';

// ── Muat .env sederhana ──────────────────────────────────────────────
$envFile = __DIR__ . '/.env';
if (!is_file($envFile)) {
    exit("GAGAL: .env tidak ditemukan di {$envFile}\n");
}
$env = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $baris) {
    $baris = trim($baris);
    if ($baris === '' || str_starts_with($baris, '#') || !str_contains($baris, '=')) {
        continue;
    }
    [$k, $v] = explode('=', $baris, 2);
    $env[trim($k)] = trim(trim($v), " \t\"'");
}

$driver  = $env['STORAGE_DRIVER'] ?? 'local';
$basePath = rtrim($env['STORAGE_PATH'] ?? '', '/\\');
$bucket  = $env['S3_BUCKET'] ?? '';
$endpoint = $env['S3_ENDPOINT'] ?? '';

if ($driver !== 's3') {
    exit("GAGAL: STORAGE_DRIVER bukan 's3' (sekarang: {$driver}). Tidak ada yang perlu dimigrasi.\n");
}
if ($basePath === '' || !is_dir($basePath)) {
    exit("GAGAL: STORAGE_PATH tidak valid: '{$basePath}'\n");
}

// Pengaman: endpoint bucket membuat nama bucket tertulis dua kali sehingga TLS gagal.
$host = parse_url($endpoint, PHP_URL_HOST) ?: '';
if ($bucket && str_starts_with($host, $bucket . '.')) {
    exit("GAGAL: S3_ENDPOINT ({$endpoint}) sudah memuat nama bucket '{$bucket}'.\n"
       . "       Pakai endpoint region, mis. https://sgp1.digitaloceanspaces.com\n");
}

$s3 = new Aws\S3\S3Client([
    'version'     => 'latest',
    'region'      => $env['S3_REGION'] ?? 'sgp1',
    'credentials' => ['key' => $env['S3_KEY'] ?? '', 'secret' => $env['S3_SECRET'] ?? ''],
    'endpoint'    => $endpoint,
    'use_path_style_endpoint' => filter_var($env['S3_USE_PATH_STYLE'] ?? false, FILTER_VALIDATE_BOOL),
]);

// ── Kumpulkan path berkas dari basis data ────────────────────────────
$db = new mysqli(
    $env['database.default.hostname'] ?? 'localhost',
    $env['database.default.username'] ?? '',
    $env['database.default.password'] ?? '',
    $env['database.default.database'] ?? ''
);
if ($db->connect_errno) {
    exit("GAGAL: koneksi basis data — {$db->connect_error}\n");
}

$kueri = [
    "SELECT logo AS p FROM masjid WHERE logo IS NOT NULL AND logo <> ''",
    "SELECT foto_utama FROM masjid WHERE foto_utama IS NOT NULL AND foto_utama <> ''",
    "SELECT image_path FROM masjid_gallery WHERE image_path IS NOT NULL AND image_path <> ''",
    "SELECT qris_image FROM masjid_payments WHERE qris_image IS NOT NULL AND qris_image <> ''",
    "SELECT attachment FROM masjid_finance_transactions WHERE attachment IS NOT NULL AND attachment <> ''",
    "SELECT thumbnail FROM masjid_news WHERE thumbnail IS NOT NULL AND thumbnail <> ''",
    "SELECT thumbnail FROM lms_modules WHERE thumbnail IS NOT NULL AND thumbnail <> ''",
];

$paths = [];
foreach ($kueri as $q) {
    if ($res = $db->query($q)) {
        while ($row = $res->fetch_row()) {
            $p = ltrim(trim((string) $row[0]), '/');
            if ($p !== '') {
                $paths[$p] = true;
            }
        }
    }
}
$paths = array_keys($paths);

// ── Migrasi ──────────────────────────────────────────────────────────
$mime = [
    'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
    'webp' => 'image/webp', 'gif' => 'image/gif', 'pdf' => 'application/pdf',
];

$stat = ['unggah' => 0, 'lewati' => 0, 'hilang' => 0, 'gagal' => 0];

echo $jalan ? "MODE: mengunggah\n" : "MODE UJI (tambahkan --jalan untuk benar-benar mengunggah)\n";
echo str_repeat('-', 64) . "\n";

foreach ($paths as $p) {
    $lokal = $basePath . '/' . $p;

    if (!is_file($lokal)) {
        printf("  HILANG di disk  %s\n", $p);
        $stat['hilang']++;
        continue;
    }

    try {
        if ($s3->doesObjectExist($bucket, $p)) {
            printf("  sudah di S3     %s\n", $p);
            $stat['lewati']++;
            continue;
        }
    } catch (Throwable $e) {
        printf("  GAGAL cek       %s — %s\n", $p, $e->getMessage());
        $stat['gagal']++;
        continue;
    }

    if (!$jalan) {
        printf("  akan diunggah   %s (%s KB)\n", $p, number_format(filesize($lokal) / 1024, 1));
        $stat['unggah']++;
        continue;
    }

    try {
        $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
        $s3->putObject([
            'Bucket'      => $bucket,
            'Key'         => $p,
            'SourceFile'  => $lokal,
            'ACL'         => 'public-read',
            'ContentType' => $mime[$ext] ?? 'application/octet-stream',
        ]);
        printf("  diunggah        %s\n", $p);
        $stat['unggah']++;
    } catch (Throwable $e) {
        printf("  GAGAL unggah    %s — %s\n", $p, $e->getMessage());
        $stat['gagal']++;
    }
}

echo str_repeat('-', 64) . "\n";
printf("Total %d berkas — diunggah/akan: %d, sudah ada: %d, hilang di disk: %d, gagal: %d\n",
    count($paths), $stat['unggah'], $stat['lewati'], $stat['hilang'], $stat['gagal']);

if (!$jalan && $stat['unggah'] > 0) {
    echo "\nJalankan ulang dengan --jalan untuk mengunggah.\n";
}
if ($stat['hilang'] > 0) {
    echo "\nCatatan: berkas 'HILANG di disk' berarti path pada basis data menunjuk\n"
       . "berkas yang tidak ada. Perlu diperiksa terpisah.\n";
}
