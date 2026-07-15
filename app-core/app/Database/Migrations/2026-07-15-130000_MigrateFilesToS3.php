<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menyalin berkas lama dari disk server ke S3 / DigitalOcean Spaces.
 *
 * LATAR BELAKANG
 * STORAGE_DRIVER sudah 's3', tetapi seluruh berkas yang diunggah SEBELUM
 * peralihan masih berada di disk (STORAGE_PATH). Storage::url() kini selalu
 * mengembalikan URL S3, sehingga berkas-berkas itu tidak lagi tertampilkan:
 * logo masjid, foto utama, galeri, thumbnail berita, dan QRIS menjadi rusak.
 *
 * Ini migrasi DATA, bukan skema — diletakkan di sini agar ikut berjalan
 * otomatis lewat 'php spark migrate' saat deploy, dan tercatat sehingga hanya
 * dieksekusi sekali.
 *
 * SIFAT AMAN
 * - Dilewati sepenuhnya bila STORAGE_DRIVER bukan 's3'.
 * - Tidak pernah menggagalkan deploy: kegagalan per berkas hanya dicatat ke
 *   log. Gambar yang belum tersalin lebih baik daripada deploy yang macet.
 * - Idempoten: objek yang sudah ada di S3 dilewati.
 * - Tidak menghapus berkas di disk dan tidak mengubah basis data — path pada
 *   basis data sudah benar, yang kurang hanya objeknya di S3.
 */
class MigrateFilesToS3 extends Migration
{
    public function up()
    {
        if (env('STORAGE_DRIVER', 'local') !== 's3') {
            log_message('info', 'MigrateFilesToS3: dilewati, STORAGE_DRIVER bukan s3.');
            return;
        }

        if (!class_exists('\Aws\S3\S3Client')) {
            log_message('error', 'MigrateFilesToS3: AWS SDK tidak tersedia, migrasi berkas dilewati.');
            return;
        }

        $bucket   = (string) env('S3_BUCKET');
        $endpoint = (string) env('S3_ENDPOINT');
        $basePath = rtrim((string) env('STORAGE_PATH', FCPATH), '/\\');

        // Endpoint bucket membuat nama bucket tertulis dua kali sehingga TLS gagal.
        $host = parse_url($endpoint, PHP_URL_HOST) ?: '';
        if ($bucket !== '' && str_starts_with($host, $bucket . '.')) {
            log_message('error', sprintf(
                'MigrateFilesToS3: S3_ENDPOINT (%s) memuat nama bucket "%s" — pakai endpoint region '
                . '(mis. https://sgp1.digitaloceanspaces.com). Migrasi berkas dilewati.',
                $endpoint, $bucket
            ));
            return;
        }

        if ($bucket === '' || $basePath === '' || !is_dir($basePath)) {
            log_message('error', 'MigrateFilesToS3: S3_BUCKET atau STORAGE_PATH tidak valid, dilewati.');
            return;
        }

        try {
            $s3 = new \Aws\S3\S3Client([
                'version'     => 'latest',
                'region'      => env('S3_REGION', 'sgp1'),
                'credentials' => ['key' => env('S3_KEY'), 'secret' => env('S3_SECRET')],
                'endpoint'    => $endpoint,
                'use_path_style_endpoint' => env('S3_USE_PATH_STYLE', false),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'MigrateFilesToS3: gagal membuat klien S3 — ' . $e->getMessage());
            return;
        }

        $mime = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
            'webp' => 'image/webp', 'gif' => 'image/gif', 'pdf' => 'application/pdf',
        ];

        $unggah = 0;
        $lewati = 0;
        $gagal  = 0;

        foreach ($this->kumpulkanPath() as $path) {
            $lokal = $basePath . '/' . $path;
            if (!is_file($lokal)) {
                log_message('warning', "MigrateFilesToS3: berkas tidak ada di disk — {$path}");
                continue;
            }

            try {
                if ($s3->doesObjectExist($bucket, $path)) {
                    $lewati++;
                    continue;
                }

                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $s3->putObject([
                    'Bucket'      => $bucket,
                    'Key'         => $path,
                    'SourceFile'  => $lokal,
                    'ACL'         => 'public-read',
                    'ContentType' => $mime[$ext] ?? 'application/octet-stream',
                ]);
                $unggah++;
            } catch (\Throwable $e) {
                // Sengaja tidak dilempar: kegagalan menyalin berkas tidak boleh
                // menggagalkan deploy.
                $gagal++;
                log_message('error', "MigrateFilesToS3: gagal mengunggah {$path} — " . $e->getMessage());
            }
        }

        log_message('info', sprintf(
            'MigrateFilesToS3: selesai — diunggah %d, sudah ada %d, gagal %d.',
            $unggah, $lewati, $gagal
        ));
    }

    public function down()
    {
        // Sengaja tidak menghapus apa pun dari S3: berkas itu satu-satunya
        // salinan yang dipakai halaman publik.
    }

    /**
     * Seluruh path berkas yang tercatat di basis data, tanpa duplikat.
     */
    private function kumpulkanPath(): array
    {
        $sumber = [
            ['masjid', 'logo'],
            ['masjid', 'foto_utama'],
            ['masjid_gallery', 'image_path'],
            ['masjid_payments', 'qris_image'],
            ['masjid_finance_transactions', 'attachment'],
            ['masjid_news', 'thumbnail'],
            ['lms_modules', 'thumbnail'],
        ];

        $paths = [];
        foreach ($sumber as [$tabel, $kolom]) {
            // Lewati tabel/kolom yang belum ada agar aman di pemasangan baru.
            if (!$this->db->tableExists($tabel) || !$this->db->fieldExists($kolom, $tabel)) {
                continue;
            }

            $rows = $this->db->table($tabel)
                ->select($kolom)
                ->where("{$kolom} IS NOT NULL")
                ->where("{$kolom} !=", '')
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                $p = ltrim(trim((string) $row[$kolom]), '/');
                if ($p !== '') {
                    $paths[$p] = true;
                }
            }
        }

        return array_keys($paths);
    }
}
