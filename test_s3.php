<?php
// Script Sederhana untuk Test Upload ke S3 / CDN yang Kompatibel (MinIO, DO Spaces, dll)

// Tampilkan semua error untuk proses debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load autoloader Composer
$autoloadPath = __DIR__ . '/app-core/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Autoloader tidak ditemukan. Pastikan Anda telah menjalankan 'composer install' di dalam folder app-core.");
}
require $autoloadPath;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// -----------------------------------------------------------------------------
// KONFIGURASI S3 ANDA (Bisa disesuaikan langsung di sini untuk test cepat)
// -----------------------------------------------------------------------------
$s3Key      = ''; // Ganti dengan Access Key Anda
$s3Secret   = ''; // Ganti dengan Secret Key Anda
$s3Region   = ''; // Contoh: ap-southeast-1
$s3Bucket   = ''; // Nama Bucket S3 Anda
$s3Endpoint = ''; // Jika pakai DO Spaces / MinIO, isi (Contoh: https://sgp1.digitaloceanspaces.com). Kosongkan jika pakai AWS asli.

// Jika Anda sudah mengisi .env di CodeIgniter dan ingin mengambil otomatis, 
// pastikan Anda menyesuaikan script ini (namun script standalone lebih baik diisi manual untuk kepastian).
// -----------------------------------------------------------------------------

$message = '';
$uploadUrl = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_test'])) {
    
    if (empty($s3Key) || empty($s3Secret) || empty($s3Bucket)) {
        $message = "<div style='color:red;'>Error: Konfigurasi S3 di dalam script ini belum diisi! Buka file test_s3.php dan isi kredensial Anda.</div>";
    } elseif ($_FILES['file_test']['error'] !== UPLOAD_ERR_OK) {
        $message = "<div style='color:red;'>Error upload: " . $_FILES['file_test']['error'] . "</div>";
    } else {
        $fileTmpName = $_FILES['file_test']['tmp_name'];
        $fileName    = 'test_folder/' . time() . '_' . basename($_FILES['file_test']['name']);
        
        try {
            // Inisialisasi S3 Client
            $s3Config = [
                'version'     => 'latest',
                'region'      => $s3Region,
                'credentials' => [
                    'key'    => $s3Key,
                    'secret' => $s3Secret,
                ]
            ];
            
            // Tambahkan endpoint jika tidak menggunakan default AWS
            if (!empty($s3Endpoint)) {
                $s3Config['endpoint'] = $s3Endpoint;
                $s3Config['use_path_style_endpoint'] = true; // Seringkali diperlukan untuk custom endpoint
            }
            
            $s3Client = new S3Client($s3Config);
            
            // Lakukan upload
            $result = $s3Client->putObject([
                'Bucket'      => $s3Bucket,
                'Key'         => $fileName,
                'SourceFile'  => $fileTmpName,
                'ACL'         => 'public-read', // Agar bisa diakses publik (pastikan bucket memperbolehkan public ACL)
            ]);
            
            $uploadUrl = $result['ObjectURL'];
            $message = "<div style='color:green; font-weight:bold;'>Upload Berhasil!</div>";
            
        } catch (AwsException $e) {
            $message = "<div style='color:red;'>AWS Error: " . $e->getMessage() . "</div>";
        } catch (Exception $e) {
            $message = "<div style='color:red;'>Error Umum: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Test Upload S3 CDN</title>
    <style>
        body { font-family: sans-serif; padding: 40px; background: #f4f4f9; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #333; }
        .result { margin: 20px 0; padding: 10px; border-radius: 4px; background: #f9f9f9; border: 1px solid #ddd; }
        input[type="file"] { margin-bottom: 20px; display: block; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .link { margin-top: 10px; word-break: break-all; }
    </style>
</head>
<body>

<div class="container">
    <h2>Test Upload ke S3 / CDN</h2>
    <p>Script ini dibuat secara independen untuk menguji koneksi S3. Pastikan Anda telah membuka file <code>test_s3.php</code> dan mengisi kredensial S3 Anda di bagian atas.</p>
    
    <?= $message ?>
    
    <?php if ($uploadUrl): ?>
        <div class="result">
            <strong>URL File Anda:</strong><br>
            <a class="link" href="<?= htmlspecialchars($uploadUrl) ?>" target="_blank"><?= htmlspecialchars($uploadUrl) ?></a>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Pilih File (Gambar/PDF dll):</label>
        <input type="file" name="file_test" required>
        <button type="submit">Upload Sekarang</button>
    </form>
</div>

</body>
</html>
