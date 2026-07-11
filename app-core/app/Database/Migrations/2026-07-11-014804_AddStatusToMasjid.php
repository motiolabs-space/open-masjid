<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToMasjid extends Migration
{
    public function up()
    {
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
        $this->forge->dropColumn('masjid', 'status');
    }
}
