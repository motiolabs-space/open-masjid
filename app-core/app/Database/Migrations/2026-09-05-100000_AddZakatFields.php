<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menandai zakat sebagai kelas tersendiri di dua sisi arus dana:
 *  - masjid_donations.zakat_type : jenis zakat yang MASUK (maal/penghasilan/
 *    fitrah); NULL berarti infaq/donasi biasa, bukan zakat.
 *  - masjid_mustahik.asnaf       : kategori penerima zakat yang KELUAR — 8 asnaf.
 *
 * Keduanya nullable: data lama tetap sah (donasi lama = non-zakat, mustahik
 * lama = belum diklasifikasi), dan pengurus mengisinya bertahap.
 */
class AddZakatFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('masjid_donations', [
            'zakat_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'program_id',
            ],
        ]);

        $this->forge->addColumn('masjid_mustahik', [
            'asnaf' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'house_ownership',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('masjid_donations', 'zakat_type');
        $this->forge->dropColumn('masjid_mustahik', 'asnaf');
    }
}
