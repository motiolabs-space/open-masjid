<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIqomahSettingsToMasjid extends Migration
{
    public function up()
    {
        $this->forge->addColumn('masjid', [
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
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('masjid', ['iqomah_settings', 'sholat_duration']);
    }
}
