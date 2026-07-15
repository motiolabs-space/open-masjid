<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasjidWilayahTable extends Migration
{
    public function up()
    {
        // Table masjid_wilayah
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'masjid_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('masjid_id', 'masjid', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('masjid_wilayah', true);

        // Add column to masjid table
        $this->forge->addColumn('masjid', [
            'is_external_service' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'longitude'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('masjid_wilayah');
        $this->forge->dropColumn('masjid', 'is_external_service');
    }
}
