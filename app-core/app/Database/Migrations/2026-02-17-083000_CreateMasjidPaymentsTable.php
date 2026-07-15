<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasjidPaymentsTable extends Migration
{
    public function up()
    {
        // The `masjid_payments` table is already defined in `database.sql`.
        // Commenting out to prevent "Table `masjid_payments` already exists" error.
        /*
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'masjid_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'payment_mode' => [
                'type'       => 'ENUM',
                'constraint' => ['manual', 'multipay'],
                'default'    => 'manual',
            ],
            // Manual Transfer
            'bank_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'bank_account_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'bank_account_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'qris_image' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            // Multipay
            'multipay_api_key' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'multipay_secret_key' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
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
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('masjid_id', 'masjid', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('masjid_payments', true);
        */
    }

    public function down()
    {
        $this->forge->dropTable('masjid_payments');
    }
}
