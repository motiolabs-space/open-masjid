<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Token REST API per masjid — TERPISAH dari mcp_token.
 *
 * MCP (agen AI) dan REST API (integrasi umum) dipisahkan tokennya agar bisa
 * dicabut sendiri-sendiri: mematikan akses agen AI tidak harus ikut mematikan
 * integrasi lain, dan sebaliknya. Sama seperti mcp_token, token ini menentukan
 * tepat SATU masjid — dasar penyaringan tenant.
 */
class AddApiTokenToMasjid extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('api_token', 'masjid')) {
            return;
        }

        $this->forge->addColumn('masjid', [
            'api_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
                'after'      => 'mcp_token',
            ],
        ]);

        $this->db->query('ALTER TABLE `masjid` ADD UNIQUE `uq_api_token` (`api_token`)');
    }

    public function down()
    {
        if ($this->db->fieldExists('api_token', 'masjid')) {
            $this->db->query('ALTER TABLE `masjid` DROP INDEX `uq_api_token`');
            $this->forge->dropColumn('masjid', 'api_token');
        }
    }
}
