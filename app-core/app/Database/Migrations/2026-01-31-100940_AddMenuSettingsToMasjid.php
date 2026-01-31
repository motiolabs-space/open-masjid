<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMenuSettingsToMasjid extends Migration
{
    public function up()
    {
        $this->forge->addColumn('masjid', [
            'menu_berita' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'username_updated_at'
            ],
            'menu_program' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'menu_berita'
            ],
            'menu_laporan' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'menu_program'
            ],
            'menu_kontak' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'menu_laporan'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('masjid', ['menu_berita', 'menu_program', 'menu_laporan', 'menu_kontak']);
    }
}
