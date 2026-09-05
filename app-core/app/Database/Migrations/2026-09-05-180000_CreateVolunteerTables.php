<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Manajemen relawan: rekrut → peran → poin partisipasi → sertifikat.
 *
 *  - masjid_volunteers        : registry relawan (nama, peran, poin total, status)
 *  - masjid_volunteer_points  : log setiap pemberian poin (jejak & alasan),
 *                               sekaligus sumber kebenaran; kolom points pada
 *                               relawan adalah cache yang dijaga controller.
 *
 * warga_id opsional — relawan boleh berupa warga terdaftar atau orang luar.
 */
class CreateVolunteerTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'masjid_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'warga_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'role'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'points'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'status'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'joined_at'  => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['masjid_id', 'status']);
        $this->forge->createTable('masjid_volunteers');

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'masjid_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'volunteer_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'program_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'points'       => ['type' => 'INT', 'constraint' => 11],
            'reason'       => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['masjid_id', 'volunteer_id']);
        $this->forge->createTable('masjid_volunteer_points');
    }

    public function down()
    {
        $this->forge->dropTable('masjid_volunteer_points');
        $this->forge->dropTable('masjid_volunteers');
    }
}
