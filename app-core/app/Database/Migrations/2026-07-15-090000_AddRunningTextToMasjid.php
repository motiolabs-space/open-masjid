<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRunningTextToMasjid extends Migration
{
    public function up()
    {
        $this->forge->addColumn('masjid', [
            'running_text' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'tagline',
                'comment'    => 'Teks berjalan pada Display TV. Kosong = otomatis dari agenda & berita.',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('masjid', 'running_text');
    }
}
