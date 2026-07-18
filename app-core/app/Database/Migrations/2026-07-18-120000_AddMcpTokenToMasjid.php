<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Token MCP per masjid.
 *
 * MCP (Model Context Protocol) memberi agen AI akses terkontrol ke data masjid.
 * Aplikasi ini multi-tenant, jadi PENYARINGAN TENANT adalah nyawa keamanannya:
 * token ini menentukan SATU masjid, dan seluruh permintaan MCP dari pemegang
 * token itu hanya boleh menyentuh data masjid tersebut. Tidak ada tool MCP yang
 * menerima masjid_id dari pemanggil — masjid selalu diturunkan dari token.
 *
 * Token kosong (null) = MCP nonaktif untuk masjid itu (bawaan). Admin masjid
 * membuat token bila memang ingin menghubungkan agen AI.
 */
class AddMcpTokenToMasjid extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('mcp_token', 'masjid')) {
            return;
        }

        $this->forge->addColumn('masjid', [
            'mcp_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
                'after'      => 'whatsapp_api_key',
            ],
        ]);

        // Unik agar satu token memetakan tepat satu masjid — dasar penyaringan
        // tenant. Indeks unik MySQL mengabaikan NULL, jadi banyak masjid boleh
        // sama-sama belum punya token.
        $this->db->query('ALTER TABLE `masjid` ADD UNIQUE `uq_mcp_token` (`mcp_token`)');
    }

    public function down()
    {
        if ($this->db->fieldExists('mcp_token', 'masjid')) {
            $this->db->query('ALTER TABLE `masjid` DROP INDEX `uq_mcp_token`');
            $this->forge->dropColumn('masjid', 'mcp_token');
        }
    }
}
