<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasjidInventoryTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'brand' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'quantity' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 1,
            ],
            'unit' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'pcs',
            ],
            'condition' => [
                'type'       => 'ENUM',
                'constraint' => ['good', 'damaged_light', 'damaged_heavy', 'lost'],
                'default'    => 'good',
            ],
            'purchase_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'purchase_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'photo' => [
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
        $this->forge->createTable('masjid_inventory');
    }

    public function down()
    {
        $this->forge->dropTable('masjid_inventory');
    }
}
