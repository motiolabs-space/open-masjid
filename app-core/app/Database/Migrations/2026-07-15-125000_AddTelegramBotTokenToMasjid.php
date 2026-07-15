<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Kolom masjid.telegram_bot_token sudah lama terdaftar di
 * MasjidModel::allowedFields dan memiliki input pada form Profil Masjid,
 * tetapi tidak pernah ada di basis data produksi maupun di migrasi mana pun —
 * hanya tercantum pada database.sql yang sudah usang.
 *
 * Karena Admin::updateProfile meneruskan seluruh field yang lolos
 * allowedFields ke SATU perintah UPDATE, satu kolom yang tidak ada membuat
 * SELURUH penyimpanan Profil Masjid gagal dengan
 * "Unknown column 'telegram_bot_token' in 'field list'".
 *
 * Idempoten: pada pemasangan yang kolomnya sudah ada (mis. yang dibuat dari
 * database.sql, atau yang sudah dijalankan lewat fix_schema_2026_07_15.sql),
 * migrasi ini tidak melakukan apa pun.
 */
class AddTelegramBotTokenToMasjid extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('telegram_bot_token', 'masjid')) {
            return;
        }

        $this->forge->addColumn('masjid', [
            'telegram_bot_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'email',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('telegram_bot_token', 'masjid')) {
            $this->forge->dropColumn('masjid', 'telegram_bot_token');
        }
    }
}
