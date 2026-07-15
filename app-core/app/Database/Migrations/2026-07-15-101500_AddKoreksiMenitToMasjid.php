<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKoreksiMenitToMasjid extends Migration
{
    public function up()
    {
        $this->forge->addColumn('masjid', [
            // Koreksi waktu sholat dalam menit, per waktu sholat. Boleh negatif
            // (lebih awal) atau positif (lebih lambat), mengikuti kebiasaan
            // setempat. Contoh: {"Subuh":0,"Dzuhur":2,"Ashar":0,"Maghrib":-1,"Isya":0}
            // NULL = tanpa koreksi.
            'koreksi_menit' => [
                'type'  => 'JSON',
                'null'  => true,
                'after' => 'sholat_duration',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('masjid', 'koreksi_menit');
    }
}
