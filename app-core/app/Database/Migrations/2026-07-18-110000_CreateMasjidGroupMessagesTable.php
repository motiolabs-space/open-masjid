<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menyimpan pesan grup jamaah untuk fitur "Ringkas obrolan" (dibantu AI).
 *
 * DATA SENSITIF — PERTIMBANGAN PRIVASI
 * Ini menyimpan percakapan jamaah. Karena itu:
 *  - HANYA pesan dari grup yang TERDAFTAR & AKTIF (masjid_groups) yang disimpan;
 *    grup asing atau menunggu-persetujuan tidak pernah tersimpan.
 *  - Retensi PENDEK: pesan lama dibuang otomatis oleh cron
 *    (broadcast:reminders memanggil prune), bawaan 3 hari. Tujuannya meringkas
 *    aktivitas terbaru, bukan mengarsipkan obrolan jamaah selamanya.
 *  - Yang disimpan seperlunya untuk ringkasan: nama pengirim (bukan nomor/HP),
 *    teks, dan waktu. Tidak menyimpan nomor telепon.
 *  - Bot Telegram baru menerima seluruh pesan grup bila "privacy mode" DImatikan
 *    lewat BotFather — itu keputusan sadar admin masjid. Bila menyala (bawaan),
 *    hanya pesan yang menyebut bot yang masuk, dan ringkasannya terbatas.
 */
class CreateMasjidGroupMessagesTable extends Migration
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
            'masjid_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'group_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            // ID pesan dari Telegram, untuk mencegah tersimpan ganda bila
            // Telegram mengirim ulang update yang sama.
            'tg_message_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'null'       => true,
            ],
            'sender_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'text' => [
                'type' => 'TEXT',
            ],
            // Waktu pesan menurut Telegram (epoch -> datetime).
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('group_id');
        $this->forge->addKey('created_at');
        // Satu pesan Telegram hanya tersimpan sekali per grup.
        $this->forge->addUniqueKey(['group_id', 'tg_message_id']);

        $this->forge->createTable('masjid_group_messages', true);
    }

    public function down()
    {
        $this->forge->dropTable('masjid_group_messages', true);
    }
}
