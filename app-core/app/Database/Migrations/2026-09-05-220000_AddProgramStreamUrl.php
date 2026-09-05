<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Live streaming kajian: tautan siaran (YouTube/embed) per program. Nullable —
 * program tanpa siaran tetap sah. Ditampilkan tertanam di halaman publik program.
 */
class AddProgramStreamUrl extends Migration
{
    public function up()
    {
        $this->forge->addColumn('masjid_programs', [
            'stream_url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'registration_link'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('masjid_programs', 'stream_url');
    }
}
