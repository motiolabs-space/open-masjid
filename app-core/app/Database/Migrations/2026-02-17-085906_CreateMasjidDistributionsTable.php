<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasjidDistributionsTable extends Migration
{
    public function up()
    {
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
            'warga_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'program_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['money', 'goods', 'service'],
                'default'    => 'money',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
            ],
            'items' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'evidence_photo' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('masjid_id', 'masjid', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('warga_id', 'masjid_warga', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('program_id', 'masjid_programs', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('masjid_distributions', true);
    }

    public function down()
    {
        $this->forge->dropTable('masjid_distributions');
    }
}
