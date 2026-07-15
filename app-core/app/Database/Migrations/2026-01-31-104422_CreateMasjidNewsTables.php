<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasjidNewsTables extends Migration
{
    public function up()
    {
        // // 1. Categories Table
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
        //     'name' => [
        //         'type'       => 'VARCHAR',
        //         'constraint' => '100',
        //     ],
        //     'slug' => [
        //         'type'       => 'VARCHAR',
        //         'constraint' => '100',
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
        // $this->forge->createTable('masjid_news_categories', true);

        // // 2. News Table
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
        //     'category_id' => [
        //         'type'       => 'INT',
        //         'constraint' => 11,
        //         'unsigned'   => true,
        //         'null'       => true,
        //     ],
        //     'title' => [
        //         'type'       => 'VARCHAR',
        //         'constraint' => '255',
        //     ],
        //     'slug' => [
        //         'type'       => 'VARCHAR',
        //         'constraint' => '255',
        //     ],
        //     'content' => [
        //         'type' => 'TEXT',
        //     ],
        //     'thumbnail' => [
        //         'type'       => 'VARCHAR',
        //         'constraint' => '255',
        //         'null'       => true,
        //     ],
        //     'status' => [
        //         'type'       => 'ENUM',
        //         'constraint' => ['published', 'draft'],
        //         'default'    => 'published',
        //     ],
        //     'views' => [
        //         'type'       => 'INT',
        //         'constraint' => 11,
        //         'default'    => 0,
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
        // $this->forge->addForeignKey('category_id', 'masjid_news_categories', 'id', 'SET NULL', 'CASCADE');
        // $this->forge->createTable('masjid_news', true);
    }

    public function down()
    {
        $this->forge->dropTable('masjid_news');
        $this->forge->dropTable('masjid_news_categories');
    }
}
