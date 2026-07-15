<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Kolom users.avatar di-query di tiga tempat (Admin::followers,
 * Admin::pengurus, Admin::searchUsers) dan ditampilkan di dua view, tetapi
 * tidak pernah terdefinisi di migrasi mana pun maupun database.sql. Akibatnya
 * halaman Daftar Pengikut dan pencarian pengurus gagal dengan
 * "Unknown column 'u.avatar'".
 *
 * Migrasi dibuat idempoten: pada pemasangan yang kolomnya sudah ditambahkan
 * manual, migrasi ini tidak melakukan apa pun sehingga deploy tetap aman.
 */
class AddAvatarToUsers extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('avatar', 'users')) {
            return;
        }

        $this->forge->addColumn('users', [
            'avatar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'phone',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('avatar', 'users')) {
            $this->forge->dropColumn('users', 'avatar');
        }
    }
}
