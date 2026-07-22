<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Token reset password (fitur "Lupa Password").
 *
 * KEAMANAN
 * - Token disimpan sebagai HASH (sha256), bukan mentah. Bila basis data bocor,
 *   token di dalamnya tidak bisa dipakai — hanya nilai mentah yang dikirim ke
 *   email pengguna yang berlaku.
 * - Kedaluwarsa (expires_at) dan sekali pakai (used_at): mempersempit jendela
 *   penyalahgunaan.
 * - Terikat email, bukan hanya user_id, agar cocok dengan alur "masukkan email".
 */
class CreatePasswordResetsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            // sha256 dari token mentah — 64 karakter heksadesimal.
            'token_hash' => [
                'type'       => 'CHAR',
                'constraint' => 64,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
            ],
            'used_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('token_hash');
        $this->forge->addKey('email');

        $this->forge->createTable('password_resets', true);
    }

    public function down()
    {
        $this->forge->dropTable('password_resets', true);
    }
}
