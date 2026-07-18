<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Catatan pemakaian token AI, satu baris per panggilan yang berhasil.
 *
 * MENGAPA PERLU
 * Kunci SumoPod satu untuk seluruh masjid, jadi setiap token dibayar platform.
 * Tanpa catatan ini, biaya AI adalah kotak hitam: tidak ada cara tahu model
 * mana yang paling mahal, fitur mana yang paling boros, atau masjid mana yang
 * memicu pemakaian terbanyak.
 *
 * model_requested vs model_used SENGAJA DIPISAH
 * SumoPodAI mencoba model utama lalu jatuh ke cadangan bila gagal. Yang dicatat
 * adalah model yang BENAR-BENAR melayani (dari balasan API), bukan yang diminta.
 * Selisih keduanya justru sinyal penting: bila tugas "ringan" ternyata sering
 * dilayani model mahal karena yang murah tumbang, biayanya membengkak diam-diam.
 */
class CreateAiUsageLogsTable extends Migration
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
            // Nullable: sebagian panggilan tidak terikat masjid tertentu.
            'masjid_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
            'tier' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            // Label fitur pemicu (telegram, mustahik_score, audit, ...), supaya
            // superadmin bisa melihat apa yang menghabiskan token, bukan sekadar
            // model apa.
            'feature' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'model_requested' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
            ],
            'model_used' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
            ],
            'prompt_tokens' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'completion_tokens' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'total_tokens' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('masjid_id');
        $this->forge->addKey('model_used');
        $this->forge->addKey('created_at');

        $this->forge->createTable('ai_usage_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('ai_usage_logs', true);
    }
}
