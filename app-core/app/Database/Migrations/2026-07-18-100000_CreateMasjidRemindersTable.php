<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Pengingat terjadwal yang dikirim otomatis ke grup jamaah.
 *
 * Contoh: jadwal sholat harian tiap subuh, ringkasan kas tiap Jumat. Dijalankan
 * oleh `php spark broadcast:reminders` yang dipanggil cron di server.
 *
 * group_id menunjuk ke masjid_groups.id (BUKAN chat id mentah), sehingga kanal,
 * id grup, dan token bot masjid semuanya ikut dari baris grup itu — satu sumber,
 * dan grup nonaktif otomatis ikut berhenti menerima pengingat.
 */
class CreateMasjidRemindersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'masjid_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            // Grup tujuan (masjid_groups.id). Sengaja bukan chat id mentah.
            'group_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30, // jadwal_sholat | laporan_kas
            ],
            'frequency' => [
                'type'       => 'ENUM',
                'constraint' => ['harian', 'mingguan', 'bulanan'],
                'default'    => 'harian',
            ],
            // Diisi hanya sesuai frequency: day_of_week untuk mingguan
            // (0=Minggu..6=Sabtu), day_of_month untuk bulanan (1..28 — dibatasi
            // 28 supaya selalu ada di setiap bulan).
            'day_of_week' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => true,
            ],
            'day_of_month' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'null'       => true,
            ],
            'time' => [
                'type' => 'TIME', // jam kirim, dalam zona waktu masjid
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            // Penanda anti-kirim-ganda: bila tanggal (zona masjid) pada nilai ini
            // sama dengan hari ini, pengingat dianggap sudah terkirim hari ini.
            'last_sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('masjid_id');
        $this->forge->addKey(['is_active', 'frequency']);

        $this->forge->createTable('masjid_reminders', true);
    }

    public function down()
    {
        $this->forge->dropTable('masjid_reminders', true);
    }
}
