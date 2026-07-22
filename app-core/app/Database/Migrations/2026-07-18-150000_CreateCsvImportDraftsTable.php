<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Penyimpanan sementara hasil parse impor CSV keuangan (halaman review).
 *
 * MENGAPA PINDAH DARI SESSION
 * Sebelumnya data transaksi hasil parse disimpan di SESSION
 * (session('temp_csv_transactions')). Untuk CSV besar, file session membengkak
 * (ratusan KB) dan gagal ditulis di sebagian hosting -> "error session" saat
 * unggah. Selain itu, memegang data besar di session memperberat kunci session
 * selama panggilan AI yang lambat.
 *
 * Kini datanya di tabel ini (payload JSON), dan id draf dibawa lewat URL review
 * -> alur impor CSV tidak lagi menyentuh session sama sekali. Draf dibatasi
 * masjid_id + user_id (hanya pengunggahnya yang bisa membukanya), dibuang saat
 * disimpan, dan dipangkas otomatis bila menganggur.
 */
class CreateCsvImportDraftsTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            // Transaksi hasil parse + kategori usulan AI, sebagai JSON.
            'payload' => [
                'type' => 'LONGTEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['masjid_id', 'user_id']);
        $this->forge->addKey('created_at');

        $this->forge->createTable('csv_import_drafts', true);
    }

    public function down()
    {
        $this->forge->dropTable('csv_import_drafts', true);
    }
}
