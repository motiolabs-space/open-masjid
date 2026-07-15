<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToMasjid extends Migration
{
    public function up()
    {
        // Idempoten: kolom mungkin sudah ditambahkan manual di luar migrasi.
        if ($this->db->fieldExists('status', 'masjid')) {
            return;
        }

        $this->forge->addColumn('masjid', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'suspended'],
                'default'    => 'active',
                'after'      => 'username'
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('status', 'masjid')) {
            $this->forge->dropColumn('masjid', 'status');
        }
    }
}
