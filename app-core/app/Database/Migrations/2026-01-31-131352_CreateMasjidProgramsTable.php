<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasjidProgramsTable extends Migration
{
    public function up()
    {
        // $this->forge->addField([
        //     'id' => [
        //         'type'           => 'INT',
        //         'constraint'     => 11,
        //         'unsigned'       => true,
        //         'auto_increment' => true,
        //     ],
        //     'masjid_id' => [
        //         'type'       => 'INT',
        //         'constraint' => 11,
        //         'unsigned'   => true,
        //     ],
        //     'title' => [
        //         'type'       => 'VARCHAR',
        //         'constraint' => '255',
        //     ],
        //     'slug' => [
        //         'type'       => 'VARCHAR',
        //         'constraint' => '255',
        //     ],
        //     'description' => [
        //         'type' => 'TEXT',
        //     ],
        //     'thumbnail' => [
        //         'type'       => 'VARCHAR',
        //         'constraint' => '255',
        //         'null'       => true,
        //     ],
        //     'date_start' => [
        //         'type' => 'DATETIME',
        //     ],
        //     'date_end' => [
        //         'type' => 'DATETIME',
        //         'null' => true,
        //     ],
        //     'location' => [
        //         'type'       => 'VARCHAR',
        //         'constraint' => '255',
        //     ],
        //     'registration_link' => [
        //         'type'       => 'VARCHAR',
        //         'constraint' => '255',
        //         'null'       => true,
        //     ],
        //     'quota' => [
        //         'type'       => 'INT',
        //         'constraint' => 11,
        //         'null'       => true,
        //     ],
        //     'status' => [
        //         'type'       => 'ENUM',
        //         'constraint' => ['published', 'draft'],
        //         'default'    => 'published',
        //     ],
        //     'created_at' => [
        //         'type' => 'DATETIME',
        //         'null' => true,
        //     ],
        //     'updated_at' => [
        //         'type' => 'DATETIME',
        //         'null' => true,
        //     ],
        // ]);
        // $this->forge->addKey('id', true);
        // $this->forge->addForeignKey('masjid_id', 'masjid', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->createTable('masjid_programs');
    }

    public function down()
    {
        $this->forge->dropTable('masjid_programs');
    }
}
