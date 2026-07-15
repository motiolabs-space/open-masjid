<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIqomahSettingsToMasjid extends Migration
{
    private array $kolom = [
        // Jeda adzan -> iqomah dalam menit, per waktu sholat.
        // Contoh: {"Subuh":20,"Dzuhur":10,"Ashar":10,"Maghrib":7,"Isya":10}
        // NULL = pakai bawaan pada Display TV.
        'iqomah_settings' => [
            'type'  => 'JSON',
            'null'  => true,
            'after' => 'running_text',
        ],
        // Lama layar digelapkan saat sholat berlangsung (menit), dihitung
        // sejak iqomah.
        'sholat_duration' => [
            'type'       => 'INT',
            'constraint' => 3,
            'unsigned'   => true,
            'default'    => 10,
            'after'      => 'iqomah_settings',
        ],
    ];

    public function up()
    {
        // Idempoten: sebagian kolom mungkin sudah ditambahkan manual di luar
        // migrasi, jadi hanya yang benar-benar belum ada yang ditambahkan.
        $tambah = [];
        foreach ($this->kolom as $nama => $definisi) {
            if (!$this->db->fieldExists($nama, 'masjid')) {
                $tambah[$nama] = $definisi;
            }
        }

        if ($tambah !== []) {
            $this->forge->addColumn('masjid', $tambah);
        }
    }

    public function down()
    {
        foreach (array_keys($this->kolom) as $nama) {
            if ($this->db->fieldExists($nama, 'masjid')) {
                $this->forge->dropColumn('masjid', $nama);
            }
        }
    }
}
