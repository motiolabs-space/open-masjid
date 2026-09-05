<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * RSVP + absensi kegiatan. Jamaah/publik mengonfirmasi kehadiran ke sebuah
 * program; pengurus menandai kehadiran saat acara berlangsung.
 *
 *  - guests : jumlah orang yang dibawa (termasuk pendaftar), default 1, dipakai
 *             menghitung perkiraan hadir vs kuota program.
 *  - status : registered (mendaftar) | attended (hadir) | no_show (tak hadir).
 */
class CreateProgramRsvpsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'masjid_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'program_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 30],
            'guests'     => ['type' => 'INT', 'constraint' => 5, 'default' => 1],
            'note'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'registered'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['program_id', 'masjid_id']);
        $this->forge->createTable('masjid_program_rsvps');
    }

    public function down()
    {
        $this->forge->dropTable('masjid_program_rsvps');
    }
}
