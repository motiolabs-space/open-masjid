<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Web Push self-hosted (VAPID), tanpa pihak ketiga.
 *
 *  - masjid_push_subscriptions : langganan browser (endpoint + kunci p256dh/auth)
 *    per masjid. endpoint unik agar tak dobel.
 *  - masjid_push_messages      : notifikasi terakhir per masjid. Pengiriman
 *    dilakukan TANPA payload; service worker menampilkan isi dengan mengambil
 *    baris terbaru dari sini (pola payloadless → tak perlu enkripsi payload).
 */
class CreatePushTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'masjid_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'endpoint'   => ['type' => 'TEXT'],
            'p256dh'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'auth'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'endpoint_hash' => ['type' => 'CHAR', 'constraint' => 64], // sha256(endpoint), untuk indeks unik
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('endpoint_hash');
        $this->forge->addKey('masjid_id');
        $this->forge->createTable('masjid_push_subscriptions');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'masjid_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'body'       => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'url'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['masjid_id', 'created_at']);
        $this->forge->createTable('masjid_push_messages');
    }

    public function down()
    {
        $this->forge->dropTable('masjid_push_messages');
        $this->forge->dropTable('masjid_push_subscriptions');
    }
}
