<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menaikkan pengurus tunggal menjadi Admin Masjid.
 *
 * LATAR BELAKANG
 * masjid_pengurus.role selama ini tersimpan tetapi tidak pernah diperiksa, jadi
 * 'admin' dan 'pengurus' sama saja: keduanya berakses penuh. Mulai sekarang
 * jabatannya ditegakkan (lihat App\Filters\MasjidAdmin).
 *
 * MENGAPA MIGRASI INI PERLU
 * Penegakan itu tidak boleh mencabut akses yang sudah dimiliki orang. Di
 * produksi ada satu masjid yang seluruh pengurusnya berjabatan 'pengurus' dan
 * tidak punya seorang admin pun. Tanpa migrasi ini, penegakan membuat masjid
 * tersebut tidak bisa lagi menghapus data maupun mengelola pengurus — dan tidak
 * ada siapa pun yang berwenang mengangkat admin barunya. Terkunci permanen.
 *
 * Menaikkannya bukan memberi hak baru: hari ini ia memang sudah berakses penuh.
 * Ini hanya mempertahankan keadaan yang sekarang.
 *
 * Yang dinaikkan hanya pengurus TUNGGAL di masjid yang belum punya admin —
 * bukan semua orang. Bila sebuah masjid punya beberapa pengurus tanpa admin,
 * memilih salah satunya adalah keputusan manusia, bukan keputusan migrasi.
 */
class PromoteSolePengurusToAdmin extends Migration
{
    public function up()
    {
        $tanpaAdmin = $this->db->query(
            'SELECT masjid_id
               FROM masjid_pengurus
              GROUP BY masjid_id
             HAVING SUM(role = ?) = 0 AND COUNT(*) = 1',
            ['admin']
        )->getResultArray();

        foreach ($tanpaAdmin as $baris) {
            $this->db->table('masjid_pengurus')
                ->where('masjid_id', $baris['masjid_id'])
                ->update(['role' => 'admin', 'title' => 'Admin Utama']);

            log_message('info', 'Pengurus tunggal masjid_id={id} dinaikkan menjadi admin.', [
                'id' => $baris['masjid_id'],
            ]);
        }
    }

    public function down()
    {
        // Tidak dibalik dengan sengaja: menurunkan kembali akan mengunci masjid
        // yang bersangkutan, dan baris mana yang semula 'pengurus' tidak dicatat.
    }
}
