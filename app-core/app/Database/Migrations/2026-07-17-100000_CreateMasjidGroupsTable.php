<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Daftar grup jamaah tujuan siaran (Telegram / WhatsApp).
 *
 * MENGAPA GRUPNYA DIDAFTARKAN, BUKAN DIAMBIL DARI PESAN MASUK
 * Bot Telegram sebelumnya memakai chat_id apa pun yang mengirim pesan
 * kepadanya. Artinya siapa pun bisa menambahkan bot sebuah masjid ke grup
 * miliknya sendiri, lalu bot itu melayaninya — termasuk membacakan ringkasan
 * keuangan masjid tersebut. Dengan tabel ini, hanya grup yang sengaja
 * didaftarkan pengurus yang dilayani dan boleh menerima siaran.
 */
class CreateMasjidGroupsTable extends Migration
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
            'channel' => [
                'type'       => 'ENUM',
                'constraint' => ['telegram', 'whatsapp'],
            ],
            // Telegram memakai chat id bertanda negatif untuk grup
            // (mis. -1001234567890); WhatsApp memakai id bergaya
            // 62812xxxx-1234567890@g.us. Keduanya disimpan apa adanya sebagai
            // teks — jangan pernah dijadikan angka.
            'group_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('masjid_id');
        // Satu grup hanya boleh terdaftar sekali per kanal. Tanpa ini siaran
        // yang sama bisa terkirim dua kali ke grup yang sama.
        $this->forge->addUniqueKey(['channel', 'group_id']);

        $this->forge->createTable('masjid_groups', true);
    }

    public function down()
    {
        $this->forge->dropTable('masjid_groups', true);
    }
}
