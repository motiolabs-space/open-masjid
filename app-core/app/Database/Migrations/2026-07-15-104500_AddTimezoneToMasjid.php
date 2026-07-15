<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTimezoneToMasjid extends Migration
{
    public function up()
    {
        $this->forge->addColumn('masjid', [
            // Zona waktu masjid, mis. Asia/Jakarta (WIB), Asia/Makassar (WITA),
            // Asia/Jayapura (WIT). NULL = ditentukan otomatis dari koordinat.
            'timezone' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'longitude',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('masjid', 'timezone');
    }
}
