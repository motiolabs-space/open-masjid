<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTitleToMasjidPengurus extends Migration
{
    public function up()
    {
        $this->forge->addColumn('masjid_pengurus', [
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'role'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('masjid_pengurus', 'title');
    }
}
