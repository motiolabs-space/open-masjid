<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * masjid_broadcasts.type semula enum('email','whatsapp') — 'telegram' belum ada,
 * padahal justru kanal itu yang paling siap dipakai. Ditambahkan sekalian
 * 'group_id' agar tercatat siaran itu benar-benar dikirim ke grup yang mana:
 * tanpa itu, riwayat siaran tidak bisa dipertanggungjawabkan bila pengurus
 * bertanya "pengumuman kemarin masuk ke grup mana?".
 */
class AddTelegramToBroadcastType extends Migration
{
    public function up()
    {
        // MODIFY, bukan tambah kolom: nilai lama 'email' dan 'whatsapp' tetap
        // sah sehingga baris yang sudah ada tidak terganggu.
        $this->db->query(
            "ALTER TABLE `masjid_broadcasts`
             MODIFY `type` ENUM('email','whatsapp','telegram') NULL DEFAULT 'email'"
        );

        if (!$this->db->fieldExists('group_id', 'masjid_broadcasts')) {
            $this->forge->addColumn('masjid_broadcasts', [
                'group_id' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'type',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('group_id', 'masjid_broadcasts')) {
            $this->forge->dropColumn('masjid_broadcasts', 'group_id');
        }

        $this->db->query(
            "ALTER TABLE `masjid_broadcasts`
             MODIFY `type` ENUM('email','whatsapp') NULL DEFAULT 'email'"
        );
    }
}
