<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLatLongToMasjid extends Migration
{
    public function up()
    {
        $this->forge->addColumn('masjid', [
            'latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
                'null'       => true,
                'after'      => 'kelurahan'
            ],
            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
                'after'      => 'latitude'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('masjid', ['latitude', 'longitude']);
    }
}
