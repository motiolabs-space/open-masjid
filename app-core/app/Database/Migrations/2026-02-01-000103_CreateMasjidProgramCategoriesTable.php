<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasjidProgramCategoriesTable extends Migration
{
    public function up()
    {
        // 1. Program Categories Table
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
                'constraint' => '100',
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        $this->forge->createTable('masjid_program_categories', true);

        // 2. Add category_id to masjid_programs
        $this->forge->addColumn('masjid_programs', [
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'masjid_id'
            ]
        ]);
        $this->forge->addForeignKey('category_id', 'masjid_program_categories', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        // Drop foreign key first
        // Note: Forge standard doesn't have a direct dropForeignKey for all DBs easily, 
        // but CI4 handles it when dropping column or table usually.
        $this->forge->dropColumn('masjid_programs', 'category_id');
        $this->forge->dropTable('masjid_program_categories');
    }
}
