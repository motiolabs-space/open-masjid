<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Jejak audit untuk SETIAP perubahan data lewat API / MCP.
 *
 * MENGAPA WAJIB ADA
 * Token API & MCP kini boleh membuat, mengubah, dan menghapus data — termasuk
 * transaksi keuangan. Token bisa bocor atau dipakai agen AI yang keliru, dan
 * perubahan lewat API tidak meninggalkan jejak seperti aksi lewat dashboard.
 * Tabel ini adalah pengaman utamanya: apa pun yang ditulis lewat API/MCP tercatat
 * — siapa (masjid & sumber), apa (aksi, entitas, id), kapan, dari IP mana, dan
 * berhasil atau gagal.
 *
 * Kegagalan ikut dicatat: percobaan yang ditolak justru sinyal paling penting
 * saat menyelidiki penyalahgunaan token.
 *
 * Catatan: baris di sini TIDAK boleh dihapus/diubah lewat API mana pun — hanya
 * dibaca dari dashboard.
 */
class CreateApiAuditLogsTable extends Migration
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
            // 'api' (REST) atau 'mcp' (agen AI) — supaya jelas jalur mana yang
            // dipakai bila terjadi perubahan tak dikenali.
            'source' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 10, // create | update | delete
            ],
            'entity' => [
                'type'       => 'VARCHAR',
                'constraint' => 30, // transaksi | berita | program
            ],
            'entity_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true, // null bila create gagal
            ],
            // Ringkasan data yang dikirim (JSON). Jangan pernah memuat token.
            'payload' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 10, // success | failed
            ],
            'message' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'ip' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('masjid_id');
        $this->forge->addKey('created_at');
        $this->forge->addKey(['entity', 'entity_id']);

        $this->forge->createTable('api_audit_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('api_audit_logs', true);
    }
}
