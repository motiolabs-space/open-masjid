<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\WebPush;

/**
 * Membuat sepasang kunci VAPID untuk Web Push. Jalankan sekali, lalu salin
 * keluarannya ke .env. Di XAMPP Windows mungkin perlu OPENSSL_CONF di-set.
 *
 *   php spark push:vapid
 */
class GenerateVapid extends BaseCommand
{
    protected $group       = 'Masjid';
    protected $name        = 'push:vapid';
    protected $description  = 'Buat sepasang kunci VAPID untuk Web Push (self-hosted).';

    public function run(array $params)
    {
        $k = WebPush::buatKunci();
        if (empty($k['publicKey'])) {
            CLI::error('Gagal membuat kunci EC. Pastikan ekstensi openssl aktif dan (Windows) OPENSSL_CONF ter-set.');
            return;
        }

        CLI::write('Kunci VAPID dibuat. Tambahkan ke .env (jangan bagikan privateKey):', 'yellow');
        CLI::newLine();
        CLI::write("vapid.publicKey = '{$k['publicKey']}'", 'green');
        CLI::write("vapid.privateKey = '{$k['privateKey']}'", 'green');
        CLI::write("vapid.subject = 'mailto:admin@masj.id'", 'green');
        CLI::newLine();
        CLI::write('publicKey juga otomatis dipakai tombol "Aktifkan Notifikasi" di halaman masjid.', 'dark_gray');
    }
}
