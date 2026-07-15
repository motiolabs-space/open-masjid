<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasjidSchedulesTable extends Migration
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
            'date' => [
                'type' => 'DATE',
            ],
            'prayer_type' => [
                'type'       => 'ENUM',
                'constraint' => ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya', 'jumat', 'tarawih', 'eid_fitr', 'eid_adha'],
            ],
            'imam_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'khatib_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'muadzin_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'bilal_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        $this->forge->createTable('masjid_schedules', true);

        // Add index for faster lookup.
        // Idempoten: pada basis data yang tabelnya sudah dibuat manual di luar
        // migrasi, indeks ini pun sudah ada. Tanpa penjagaan, migrasi gagal
        // dengan "Duplicate key name" dan menghentikan seluruh rantai migrasi.
        $indeksSudahAda = $this->db->query(
            "SHOW INDEX FROM masjid_schedules WHERE Key_name = 'idx_schedule_lookup'"
        )->getNumRows() > 0;

        if (!$indeksSudahAda) {
            $this->db->query('CREATE INDEX idx_schedule_lookup ON masjid_schedules(masjid_id, date, prayer_type)');
        }
    }

    public function down()
    {
        $this->forge->dropTable('masjid_schedules');
    }
}
