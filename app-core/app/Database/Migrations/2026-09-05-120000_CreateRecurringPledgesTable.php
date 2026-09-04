<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Donasi rutin (infaq terjadwal) sebagai JANJI + PENGINGAT, bukan auto-charge.
 *
 * Pembayaran di sini bertumpu transfer manual/QRIS, jadi tidak ada langganan
 * gateway yang bisa menarik dana otomatis. Model yang jujur: donatur berjanji
 * nominal rutin, sistem mengirim pengingat berkala berisi tautan donasi terisi.
 * next_reminder_date menandai kapan pengingat berikutnya jatuh tempo; cron yang
 * sama dengan broadcast:reminders yang memprosesnya.
 */
class CreateRecurringPledgesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'masjid_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'program_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'donor_name'         => ['type' => 'VARCHAR', 'constraint' => 150],
            'donor_phone'        => ['type' => 'VARCHAR', 'constraint' => 30],
            'donor_email'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'amount'             => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'frequency'          => ['type' => 'VARCHAR', 'constraint' => 20], // mingguan | bulanan
            'next_reminder_date' => ['type' => 'DATE'],
            'active'             => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'last_reminded_at'   => ['type' => 'DATETIME', 'null' => true],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['masjid_id', 'active']);
        $this->forge->addKey('next_reminder_date');
        $this->forge->createTable('masjid_recurring_pledges');
    }

    public function down()
    {
        $this->forge->dropTable('masjid_recurring_pledges');
    }
}
