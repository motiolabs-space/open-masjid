<?php

namespace App\Libraries;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class Storage
{
    protected $driver;
    protected $uploadPath;

    public function __construct()
    {
        $this->driver = env('STORAGE_DRIVER', 'local');
        
        // Determine physical upload path
        // Priority: Env STORAGE_PATH -> FCPATH
        $this->uploadPath = rtrim(env('STORAGE_PATH', FCPATH), '/\\') . '/';

        if ($this->driver === 's3') {
            if (!class_exists('\Aws\S3\S3Client')) {
                log_message('error', 'AWS SDK not found. Please run composer require aws/aws-sdk-php. Falling back to local storage.');
                $this->driver = 'local';
            } else {
                $endpoint = (string) env('S3_ENDPOINT');
                $this->bucket = env('S3_BUCKET');

                // S3_ENDPOINT harus endpoint REGION, bukan endpoint bucket.
                // AWS SDK menambahkan nama bucket di depan host endpoint, sehingga
                // endpoint bucket membuat namanya tertulis dua kali:
                //   cdn-masjid + cdn-masjid.sgp1.digitaloceanspaces.com
                //   = cdn-masjid.cdn-masjid.sgp1.digitaloceanspaces.com
                // Host bertingkat dua itu tidak tercakup sertifikat wildcard DO,
                // sehingga TLS gagal: gambar diblokir browser dan upload gagal —
                // tanpa pesan yang jelas. Peringatan ini memunculkannya di log.
                if ($this->bucket && $endpoint && !env('S3_USE_PATH_STYLE', false)) {
                    $host = parse_url($endpoint, PHP_URL_HOST) ?: '';
                    if (str_starts_with($host, $this->bucket . '.')) {
                        log_message('error', sprintf(
                            'Konfigurasi S3 keliru: S3_ENDPOINT (%s) sudah memuat nama bucket "%s", '
                            . 'sehingga URL yang terbentuk menjadi %s.%s dan TLS akan gagal. '
                            . 'Pakai endpoint region, mis. https://sgp1.digitaloceanspaces.com',
                            $endpoint, $this->bucket, $this->bucket, $host
                        ));
                    }
                }

                $this->s3Client = new S3Client([
                    'version' => 'latest',
                    'region'  => env('S3_REGION'),
                    'credentials' => [
                        'key'    => env('S3_KEY'),
                        'secret' => env('S3_SECRET'),
                    ],
                    'endpoint' => $endpoint, // For Minio or other S3 compatibles
                    'use_path_style_endpoint' => env('S3_USE_PATH_STYLE', false),
                ]);
            }
        }
    }

    /**
     * Upload a file
     * 
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file
     * @param string $path Target directory/prefix
     * @param array $allowedTypes Optional allowed extensions
     * @param int $maxSize Max size in bytes (default 5MB)
     * @return string|null Filename/Key on success, null on failure
     */
    public function upload($file, $path = 'uploads', $allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif'], $maxSize = 5242880)
    {
        if (!$file->isValid() || $file->hasMoved()) {
            return null;
        }

        // 0. Check File Size
        if ($file->getSize() > $maxSize) {
            log_message('error', 'Upload Blocked: File size exceeds limit ' . ($maxSize / 1024 / 1024) . 'MB');
            return null;
        }

        // --- SAAS FOLDER ISOLATION ---
        $masjidUsername = session()->get('masjid_username');
        $datePath = date('Y/m');
        
        // Remove trailing or leading slashes from original path
        $path = trim($path, '/');
        
        if (!empty($masjidUsername)) {
            $path = "{$masjidUsername}/{$datePath}/{$path}";
        } else {
            // For global/superadmin uploads
            $path = "global/{$datePath}/{$path}";
        }

        // --- SECURITY VALIDATION ---
        // 1. Check Extension
        $ext = strtolower($file->getExtension());
        if (!in_array($ext, $allowedTypes)) {
            log_message('error', 'Upload Blocked: Invalid extension ' . $ext);
            return null;
        }

        // 2. Check MIME Type (More secure)
        $mime = $file->getMimeType();
        $baseMimes = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'webp' => 'image/webp',
            'gif'  => 'image/gif',
            'pdf'  => 'application/pdf',
        ];

        // Ensure MIME matches expected extension
        if (isset($baseMimes[$ext])) {
            if ($mime !== $baseMimes[$ext]) {
                log_message('error', "Upload Blocked: MIME type mismatch for extension {$ext}. Got {$mime}");
                return null;
            }
        }

        // Double check against global allowed list
        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'
        ];
        
        if (!in_array($mime, $allowedMimes)) {
            log_message('error', 'Upload Blocked: Malicious or unsupported MIME type ' . $mime);
            return null;
        }

        // 3. Rename to random string (prevents directory traversal & original file name leaks)
        $newName = $file->getRandomName();

        if ($this->driver === 's3') {
            try {
                $this->s3Client->putObject([
                    'Bucket' => $this->bucket,
                    'Key'    => $path . '/' . $newName,
                    'Body'   => fopen($file->getTempName(), 'r'),
                    'ACL'    => 'public-read',
                    'ContentType' => $mime
                ]);
                return $path . '/' . $newName;
            } catch (\Throwable $e) {
                // Jangan buang berkas pengurus hanya karena S3 sedang bermasalah.
                // Simpan ke disk sebagai cadangan; url() akan mengutamakan berkas
                // lokal bila ada, sehingga gambarnya tetap tampil. Setelah S3 pulih,
                // berkas ini dapat disalin dengan migrasi MigrateFilesToS3.
                log_message('error', 'S3 Upload Error: ' . $e->getMessage() . ' — beralih menyimpan ke disk.');

                if ($this->simpanLokal($file, $path, $newName)) {
                    log_message('warning', "Storage: {$path}/{$newName} tersimpan di disk sebagai cadangan, BELUM ada di S3.");
                    return $path . '/' . $newName;
                }

                log_message('error', 'Storage: cadangan ke disk juga gagal untuk ' . $path);
                return null;
            }
        }

        return $this->simpanLokal($file, $path, $newName) ? $path . '/' . $newName : null;
    }

    /**
     * Menyimpan berkas ke disk pada uploadPath (STORAGE_PATH).
     */
    private function simpanLokal($file, string $path, string $newName): bool
    {
        $targetDir = rtrim($this->uploadPath . $path, '/\\');

        if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
            log_message('error', "Storage: gagal membuat folder {$targetDir}");
            return false;
        }

        return (bool) $file->move($targetDir, $newName);
    }

    /**
     * URL untuk berkas yang tersimpan di disk.
     *
     * asset_url() TIDAK dipakai di sini: helper itu menambahkan awalan 'public/'
     * karena ditujukan untuk aset statis di dalam folder public/, sedangkan
     * berkas unggahan berada di STORAGE_PATH. Pada pemasangan ini STORAGE_PATH
     * menunjuk langsung ke root web, sehingga awalan itu justru membuat URL
     * meleset (404).
     */
    private function urlLokal(string $path): string
    {
        $storageUrl = env('STORAGE_URL');
        if (!empty($storageUrl)) {
            return rtrim($storageUrl, '/') . '/' . ltrim($path, '/');
        }

        return base_url(ltrim($path, '/'));
    }

    /**
     * Delete a file
     * 
     * @param string $path Full path/key of the file
     * @return bool
     */
    public function delete($path)
    {
        if (empty($path)) return false;

        if ($this->driver === 's3') {
            try {
                $this->s3Client->deleteObject([
                    'Bucket' => $this->bucket,
                    'Key'    => $path,
                ]);
                return true;
            } catch (AwsException $e) {
                log_message('error', 'S3 Delete Error: ' . $e->getMessage());
                return false;
            }
        } else {
            $fullPath = $this->uploadPath . $path;
            if (file_exists($fullPath)) {
                return unlink($fullPath);
            }
        }

        return false;
    }

    /**
     * Get public URL of a file
     * 
     * @param string $path Full path/key of the file
     * @return string
     */
    public function url($path)
    {
        if (empty($path)) return '';

        if ($this->driver === 's3') {
            // Berkas yang tersimpan di disk diutamakan. Dua keadaan membutuhkan ini:
            //  1. Cadangan saat S3 gagal (lihat upload()).
            //  2. Berkas lama dari era penyimpanan lokal yang belum tersalin ke S3.
            // Tanpa pengecekan ini, url() menunjuk ke S3 untuk berkas yang tidak
            // ada di sana, dan gambarnya rusak tanpa penjelasan.
            if (is_file($this->uploadPath . ltrim($path, '/'))) {
                return $this->urlLokal($path);
            }

            $publicUrl = env('S3_PUBLIC_URL');
            if (!empty($publicUrl)) {
                return rtrim($publicUrl, '/') . '/' . ltrim($path, '/');
            }
            return $this->s3Client->getObjectUrl($this->bucket, $path);
        }

        return $this->urlLokal($path);
    }
}
