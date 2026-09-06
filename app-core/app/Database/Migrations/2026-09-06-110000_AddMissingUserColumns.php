<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menambah kolom `users` yang dipakai kode tetapi tidak pernah dibuat migrasi:
 * `telegram_chat_id`, `register_ip`, `register_country`.
 *
 * Ketiganya ada di `database.sql` (dump awal) dan di `UserModel::allowedFields`,
 * tetapi tak pernah punya migrasi — sehingga basis data yang dibangun dari dump
 * lain (termasuk dump produksi `dbtq71g8ngq2pa.sql`) tidak memilikinya.
 *
 * Akibatnya PENDAFTARAN GAGAL TOTAL: `Auth::registerMasjid` dan
 * `registerJamaah` menulis `register_ip` & `register_country`, sehingga insert
 * ditolak dengan "Unknown column 'register_ip' in 'field list'" dan pengguna
 * mendapat galat 500. Tidak ada masjid maupun jamaah baru yang bisa mendaftar.
 * Halaman `superadmin/users/analytics/{id}` juga mati karena view membaca
 * `telegram_chat_id`.
 *
 * Ditulis idempoten (`fieldExists`) supaya aman dijalankan pada basis data yang
 * kolomnya sudah ada.
 */
class AddMissingUserColumns extends Migration
{
    /** @var array<string, array<string, mixed>> */
    private array $kolom = [
        'telegram_chat_id' => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true],
        'register_ip'      => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true],
        'register_country' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
    ];

    public function up()
    {
        $tambah = [];
        foreach ($this->kolom as $nama => $definisi) {
            if (! $this->db->fieldExists($nama, 'users')) {
                $tambah[$nama] = $definisi;
            }
        }

        if ($tambah !== []) {
            $this->forge->addColumn('users', $tambah);
        }
    }

    public function down()
    {
        $buang = [];
        foreach (array_keys($this->kolom) as $nama) {
            if ($this->db->fieldExists($nama, 'users')) {
                $buang[] = $nama;
            }
        }

        if ($buang !== []) {
            $this->forge->dropColumn('users', $buang);
        }
    }
}
