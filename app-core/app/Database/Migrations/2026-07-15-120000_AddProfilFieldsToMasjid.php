<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Melengkapi kolom profil masjid yang dipakai form Profil Masjid dan terdaftar
 * di MasjidModel::allowedFields, tetapi tidak pernah ada di database.sql maupun
 * migrasi mana pun.
 *
 * Dampaknya berat: Admin::updateProfile() meneruskan seluruh field yang lolos
 * allowedFields ke UPDATE, sehingga satu kolom yang tidak ada membuat SELURUH
 * penyimpanan profil gagal dengan "Unknown column 'nama_resmi'". Pada pemasangan
 * yang mengikuti repositori ini, profil masjid sama sekali tidak dapat disimpan.
 *
 * Tipe kolom mengikuti kolom sekerabat yang sudah ada:
 *   - regency_id       VARCHAR(50)  -> provinsi_id, district_id, village_id
 *   - provinsi/kabupaten VARCHAR(100) -> kecamatan, kelurahan
 *   - name             VARCHAR(255) -> nama_resmi
 *
 * Dibuat IDEMPOTEN memakai fieldExists(): pada basis data yang kolomnya sudah
 * ditambahkan manual di luar migrasi, migrasi ini tidak melakukan apa pun
 * sehingga 'spark migrate' saat deploy tetap aman.
 */
class AddProfilFieldsToMasjid extends Migration
{
    private array $kolom = [
        'nama_resmi'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        'tahun_berdiri' => ['type' => 'VARCHAR', 'constraint' => 10,  'null' => true],
        'jenis_masjid'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'no_sk'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'kecamatan'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'kelurahan'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'provinsi_id'   => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true],
        'district_id'   => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true],
        'village_id'    => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true],
    ];

    public function up()
    {
        $tambah = [];
        foreach ($this->kolom as $nama => $definisi) {
            if (!$this->db->fieldExists($nama, 'masjid')) {
                $tambah[$nama] = $definisi;
            }
        }

        if ($tambah !== []) {
            $this->forge->addColumn('masjid', $tambah);
        }
    }

    public function down()
    {
        foreach (array_keys($this->kolom) as $nama) {
            if ($this->db->fieldExists($nama, 'masjid')) {
                $this->forge->dropColumn('masjid', $nama);
            }
        }
    }
}
