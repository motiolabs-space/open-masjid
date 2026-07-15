<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasjidBroadcastsTable extends Migration
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
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'content' => [
                'type'       => 'TEXT',
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['email', 'whatsapp'],
                'default'    => 'email',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'sent', 'failed'],
                'default'    => 'draft',
            ],
            'recipient_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->createTable('masjid_broadcasts', true);
    }

    public function down()
    {
        $this->forge->dropTable('masjid_broadcasts');
    }
}
