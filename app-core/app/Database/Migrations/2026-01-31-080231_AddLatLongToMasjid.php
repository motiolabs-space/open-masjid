<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLatLongToMasjid extends Migration
{
    public function up()
    {
        $this->forge->addColumn('masjid', [
            // 'latitude' => [  // Commented out: Latitude column is currently not added due to potential schema conflicts with existing database.sql structure
            //     'type'       => 'DECIMAL',
            //     'constraint' => '10,8',
            //     'null'       => true
            //     // 'after'      => 'kelurahan'  // Commented out: 'kelurahan' column doesn't exist in current schema
            // ]
            // 'longitude' => [ // Commented out: Duplicate column name 'longitude'
            //     'type'       => 'DECIMAL',
            //     'constraint' => '11,8',
            //     'null'       => true,
            //     'after'      => 'latitude'
            // ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('masjid', ['latitude', 'longitude']);
    }
}


