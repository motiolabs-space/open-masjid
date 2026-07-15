<?php

/**
 * Menghapus salinan lokal berkas yang SUDAH ADA di S3.
 *
 * LATAR BELAKANG
 * Storage::url() mengutamakan berkas yang ada di disk sebelum memakai URL S3 —
 * demi menutup dua keadaan: berkas cadangan saat S3 gagal, dan berkas lama yang
 * belum tersalin. Akibatnya, selama salinan lama masih ada di STORAGE_PATH,
 * gambar dilayani server sendiri, bukan S3.
 *
 * Skrip ini menghapus salinan lokal itu supaya gambar kembali dilayani S3.
 *
 * SENGAJA TIDAK DIJALANKAN OTOMATIS SAAT DEPLOY
 * Menyalin berkas boleh otomatis (MigrateFilesToS3), tetapi MENGHAPUS tidak:
 * kesalahan menyalin masih bisa diperbaiki, kesalahan menghapus tidak.
 *
 * CARA MENJALANKAN (di server produksi, dari folder app-core):
 *     php cleanup_local_files.php            # mode uji: hanya melaporkan
 *     php cleanup_local_files.php --jalan    # benar-benar menghapus
 *
 * PENGAMAN
 * - Sebuah berkas hanya dihapus bila objeknya TERBUKTI ada di S3, diperiksa
 *   satu per satu — bukan diasumsikan.
 * - Berkas di luar STORAGE_PATH tidak pernah disentuh.
 * - Basis data tidak diubah: path yang tersimpan tetap sahih karena berkasnya
 *   ada di S3.
 * - Bila S3 padam kemudian, Storage akan membuat cadangan lokal lagi dengan
 *   sendirinya.
 */

$jalan = in_array('--jalan', $argv, true);

require __DIR__ . '/vendor/autoload.php';

// ── Muat .env ────────────────────────────────────────────────────────
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

$driver   = $env['STORAGE_DRIVER'] ?? 'local';
$basePath = rtrim($env['STORAGE_PATH'] ?? '', '/\\');
$bucket   = $env['S3_BUCKET'] ?? '';
$endpoint = $env['S3_ENDPOINT'] ?? '';

if ($driver !== 's3') {
    exit("GAGAL: STORAGE_DRIVER bukan 's3' (sekarang: {$driver}).\n"
       . "       Menghapus salinan lokal akan menghilangkan satu-satunya berkas.\n");
}
if ($basePath === '' || !is_dir($basePath)) {
    exit("GAGAL: STORAGE_PATH tidak valid: '{$basePath}'\n");
}
$host = parse_url($endpoint, PHP_URL_HOST) ?: '';
if ($bucket !== '' && str_starts_with($host, $bucket . '.')) {
    exit("GAGAL: S3_ENDPOINT ({$endpoint}) memuat nama bucket '{$bucket}'.\n"
       . "       Pakai endpoint region, mis. https://sgp1.digitaloceanspaces.com\n");
}

$s3 = new Aws\S3\S3Client([
    'version'     => 'latest',
    'region'      => $env['S3_REGION'] ?? 'sgp1',
    'credentials' => ['key' => $env['S3_KEY'] ?? '', 'secret' => $env['S3_SECRET'] ?? ''],
    'endpoint'    => $endpoint,
    'use_path_style_endpoint' => filter_var($env['S3_USE_PATH_STYLE'] ?? false, FILTER_VALIDATE_BOOL),
]);

// ── Kumpulkan path dari basis data ───────────────────────────────────
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
    if ($res = @$db->query($q)) {
        while ($row = $res->fetch_row()) {
            $p = ltrim(trim((string) $row[0]), '/');
            if ($p !== '') {
                $paths[$p] = true;
            }
        }
    }
}
$paths = array_keys($paths);

// ── Bersihkan ────────────────────────────────────────────────────────
$stat = ['hapus' => 0, 'belum_di_s3' => 0, 'tak_ada' => 0, 'gagal' => 0];
$folderTersentuh = [];

echo $jalan ? "MODE: menghapus\n" : "MODE UJI (tambahkan --jalan untuk benar-benar menghapus)\n";
echo "STORAGE_PATH: {$basePath}\n";
echo str_repeat('-', 68) . "\n";

foreach ($paths as $p) {
    $lokal = $basePath . '/' . $p;

    // Pengaman: jangan pernah keluar dari STORAGE_PATH.
    $nyata = realpath($lokal);
    if ($nyata !== false && !str_starts_with(str_replace('\\', '/', $nyata), str_replace('\\', '/', realpath($basePath)))) {
        printf("  DILEWATI (di luar STORAGE_PATH)  %s\n", $p);
        continue;
    }

    if (!is_file($lokal)) {
        $stat['tak_ada']++;
        continue;
    }

    try {
        if (!$s3->doesObjectExist($bucket, $p)) {
            printf("  DIPERTAHANKAN (belum ada di S3)  %s\n", $p);
            $stat['belum_di_s3']++;
            continue;
        }
    } catch (Throwable $e) {
        printf("  GAGAL memeriksa S3               %s — %s\n", $p, $e->getMessage());
        $stat['gagal']++;
        continue;
    }

    if (!$jalan) {
        printf("  akan dihapus (sudah di S3)       %s\n", $p);
        $stat['hapus']++;
        continue;
    }

    if (@unlink($lokal)) {
        printf("  dihapus                          %s\n", $p);
        $stat['hapus']++;
        $folderTersentuh[dirname($lokal)] = true;
    } else {
        printf("  GAGAL menghapus                  %s\n", $p);
        $stat['gagal']++;
    }
}

// Rapikan folder yang menjadi kosong — termasuk folder bernama username masjid
// di root yang dulu menutupi rute profil publik.
$folderTerhapus = 0;
if ($jalan) {
    foreach (array_keys($folderTersentuh) as $dir) {
        $d = $dir;
        while (
            is_dir($d)
            && str_starts_with(str_replace('\\', '/', realpath($d) ?: ''), str_replace('\\', '/', realpath($basePath)))
            && realpath($d) !== realpath($basePath)
            && count(scandir($d)) === 2 // hanya . dan ..
        ) {
            if (!@rmdir($d)) {
                break;
            }
            $folderTerhapus++;
            $d = dirname($d);
        }
    }
}

echo str_repeat('-', 68) . "\n";
printf("Total %d berkas — %s: %d, dipertahankan (belum di S3): %d, tidak ada di disk: %d, gagal: %d\n",
    count($paths), $jalan ? 'dihapus' : 'akan dihapus',
    $stat['hapus'], $stat['belum_di_s3'], $stat['tak_ada'], $stat['gagal']);

if ($jalan && $folderTerhapus > 0) {
    printf("Folder kosong dirapikan: %d\n", $folderTerhapus);
}
if (!$jalan && $stat['hapus'] > 0) {
    echo "\nJalankan ulang dengan --jalan untuk menghapus.\n";
}
if ($stat['belum_di_s3'] > 0) {
    echo "\nBerkas 'DIPERTAHANKAN' belum ada di S3 — jangan dihapus. Jalankan\n"
       . "MigrateFilesToS3 (otomatis saat deploy) atau migrate_files_to_s3.php dulu.\n";
}
