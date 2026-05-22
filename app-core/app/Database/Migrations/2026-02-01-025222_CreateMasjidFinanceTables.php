<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasjidFinanceTables extends Migration
{
    public function up()
    {
        // 1. Finance Categories Table
        // The `masjid_finance_categories` table is already defined in `database.sql`.
        // Commenting out to prevent "Table `masjid_finance_categories` already exists" error.
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['pemasukan', 'pengeluaran'],
                'default'    => 'pemasukan',
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        $this->forge->createTable('masjid_finance_categories');
        */

        // 2. Finance Transactions Table
        // The `masjid_finance_transactions` table is already defined in `database.sql`.
        // Commenting out to prevent "Table `masjid_finance_transactions` already exists" error.
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
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'program_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['pemasukan', 'pengeluaran'],
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'donor_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'donor_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'attachment' => [
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
        $this->forge->addForeignKey('category_id', 'masjid_finance_categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('program_id', 'masjid_programs', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('masjid_finance_transactions');
        */
    }

    public function down()
    {
        $this->forge->dropTable('masjid_finance_transactions');
        $this->forge->dropTable('masjid_finance_categories');
    }
}
