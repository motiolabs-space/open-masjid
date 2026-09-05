<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Laporan Dampak program: mengubah "program" (rencana) menjadi "dampak"
 * (hasil terukur bagi masyarakat). Diisi pengurus SETELAH program berjalan.
 *
 *  - masjid_programs.beneficiaries_count : jumlah penerima manfaat
 *  - masjid_programs.impact_narrative    : cerita dampak (naratif)
 *  - masjid_programs.impact_published    : 1 = tampil publik di halaman program
 *  - masjid_program_impact_photos        : foto bukti (boleh banyak, ber-caption)
 *
 * Semua nullable / default 0: program lama tetap sah tanpa laporan dampak.
 */
class AddProgramImpactFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('masjid_programs', [
            'beneficiaries_count' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'target_donation'],
            'impact_narrative'    => ['type' => 'TEXT', 'null' => true, 'after' => 'beneficiaries_count'],
            'impact_published'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'impact_narrative'],
        ]);

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'masjid_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'program_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'photo'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'caption'    => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['masjid_id', 'program_id']);
        $this->forge->createTable('masjid_program_impact_photos');
    }

    public function down()
    {
        $this->forge->dropColumn('masjid_programs', ['beneficiaries_count', 'impact_narrative', 'impact_published']);
        $this->forge->dropTable('masjid_program_impact_photos');
    }
}
