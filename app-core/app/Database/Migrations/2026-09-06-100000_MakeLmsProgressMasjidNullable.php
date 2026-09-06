<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * `lms_progress.masjid_id` dibuat nullable.
 *
 * LMS adalah materi milik platform, bukan milik satu masjid, dan progres
 * belajar melekat pada PENGGUNA. Kolom masjid_id di sini hanya keterangan
 * "sedang bertindak atas nama masjid mana" — bukan pemilik barisnya.
 *
 * Selama kolom ini NOT NULL, setiap pengguna yang membuka LMS tanpa konteks
 * masjid (jamaah biasa — rute LMS hanya digerbang dashboardGuard, bukan
 * kepengurusan) membuat tombol "Tandai Selesai" gagal dengan galat 500:
 * "Column 'masjid_id' cannot be null".
 */
class MakeLmsProgressMasjidNullable extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('lms_progress', [
            'masjid_id' => [
                'name'       => 'masjid_id',
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        // Baris tanpa masjid dibuang lebih dulu, kalau tidak pengembalian
        // kolom ke NOT NULL akan ditolak basis data.
        $this->db->query('DELETE FROM lms_progress WHERE masjid_id IS NULL');

        $this->forge->modifyColumn('lms_progress', [
            'masjid_id' => [
                'name'       => 'masjid_id',
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);
    }
}
