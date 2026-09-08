<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Riwayat login per pengguna — satu baris tiap kali seseorang berhasil masuk.
 *
 * KENAPA TABEL BARU, BUKAN KOLOM
 * `users.last_login` hanya menyimpan login TERAKHIR. Satu nilai yang selalu
 * ditimpa tidak bisa menjawab pertanyaan apa pun tentang waktu: "masjid yang
 * mendaftar bulan Maret, berapa yang masih aktif di bulan Juni" mustahil
 * dihitung darinya, sebab kunjungan bulan April dan Mei sudah hilang tertimpa.
 *
 * Padahal bagi produk yang adopsinya bertahap seperti ini, RETENSI biasanya
 * lebih menentukan daripada akuisisi: masjid yang mendaftar lalu berhenti
 * memakai jauh lebih mahal daripada masjid yang belum mendaftar.
 *
 * Satu tabel ini sekaligus membuka tiga hal yang sebelumnya tak terukur:
 * retensi kohort, tren DAU/MAU yang sungguhan (bukan sekadar posisi terkini),
 * dan riwayat keaktifan tiap masjid.
 *
 * Barisnya tumbuh terus, jadi dipangkas berkala oleh cron `broadcast:reminders`
 * — lihat UserLoginEventModel::pangkasLama().
 */
class CreateUserLoginEvents extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'user_id'   => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            // Peran saat login: memisahkan keaktifan pengurus dari jamaah tanpa
            // perlu menggabungkan tabel lain setiap kali menghitung.
            'role'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'logged_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        // Kedua indeks ini yang membuat kueri kohort tetap ringan: satu untuk
        // menyaring rentang waktu, satu untuk menelusuri per pengguna.
        $this->forge->addKey('logged_at');
        $this->forge->addKey(['user_id', 'logged_at']);
        $this->forge->createTable('user_login_events');
    }

    public function down()
    {
        $this->forge->dropTable('user_login_events', true);
    }
}
