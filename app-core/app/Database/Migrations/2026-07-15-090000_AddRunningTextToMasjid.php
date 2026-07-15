<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRunningTextToMasjid extends Migration
{
    public function up()
    {
        // Idempoten: kolom mungkin sudah ditambahkan manual di luar migrasi.
        if ($this->db->fieldExists('running_text', 'masjid')) {
            return;
        }

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
        if ($this->db->fieldExists('running_text', 'masjid')) {
            $this->forge->dropColumn('masjid', 'running_text');
        }
    }
}
