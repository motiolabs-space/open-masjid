<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Mengosongkan kembali masjid contoh.
 *
 *   php spark db:seed DemoMasjidClearSeeder
 *
 * Profil masjidnya sendiri TIDAK dihapus — hanya kontennya. Barisnya ditapis
 * lewat masjid_id milik masjid contoh, jadi masjid lain tak pernah tersentuh.
 */
class DemoMasjidClearSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $masjid = $db->table('masjid')->where('username', DemoMasjidSeeder::USERNAME)->get()->getRowArray();
        if (! $masjid) {
            if (is_cli()) {
                \CodeIgniter\CLI\CLI::write('Masjid contoh tidak ada; tidak ada yang perlu dibersihkan.', 'yellow');
            }

            return;
        }

        $id = (int) $masjid['id'];
        $total = 0;
        foreach (DemoMasjidSeeder::TABEL as $t) {
            if (! $db->tableExists($t)) {
                continue;
            }
            $db->table($t)->where('masjid_id', $id)->delete();
            $total += $db->affectedRows();
        }

        if (is_cli()) {
            \CodeIgniter\CLI\CLI::write("Konten masjid contoh dihapus: {$total} baris. Profil masjid dibiarkan.", 'green');
        }
    }
}
