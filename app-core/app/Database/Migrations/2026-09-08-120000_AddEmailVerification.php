<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Verifikasi alamat email pendaftar.
 *
 * VERIFIKASINYA LUNAK, BUKAN GERBANG. Pendaftar tetap langsung masuk dashboard
 * tanpa menunggu klik apa pun. Alasannya ada pada data aktivasi platform ini
 * sendiri: 8 dari 11 masjid mendaftar lalu tak pernah mengisi apa pun.
 * Memasang tembok tepat di detik ketika momentum paling dibutuhkan akan
 * memperburuk angka itu, bukan memperbaikinya.
 *
 * Lalu apa gunanya? Yang paling nyata: MENANGKAP SALAH KETIK ALAMAT EMAIL.
 * Pengurus yang mendaftar dengan email keliru tidak akan pernah bisa memulihkan
 * kata sandinya — masjidnya hilang begitu saja bersama akses satu-satunya.
 * Selain itu email sambutan, pengingat, dan laporan rutin semuanya bergantung
 * pada alamat yang benar-benar sampai.
 *
 * `email_verifications` sengaja meniru bentuk `password_resets` yang sudah
 * terbukti: hanya HASH token yang disimpan, ada masa berlaku, dan ada penanda
 * sudah terpakai sehingga satu tautan tak bisa dipakai dua kali.
 */
class AddEmailVerification extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('email_verified_at', 'users')) {
            $this->forge->addColumn('users', [
                'email_verified_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
        }

        if ($this->db->tableExists('email_verifications')) {
            return;
        }

        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 255],
            // Token mentah hanya ada di email penerima; di sini cukup hash-nya,
            // sehingga bocornya basis data tidak memberi siapa pun tautan aktif.
            'token_hash' => ['type' => 'CHAR', 'constraint' => 64],
            'expires_at' => ['type' => 'DATETIME'],
            'used_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('token_hash');
        $this->forge->addKey('user_id');
        $this->forge->createTable('email_verifications');
    }

    public function down()
    {
        $this->forge->dropTable('email_verifications', true);

        if ($this->db->fieldExists('email_verified_at', 'users')) {
            $this->forge->dropColumn('users', 'email_verified_at');
        }
    }
}
