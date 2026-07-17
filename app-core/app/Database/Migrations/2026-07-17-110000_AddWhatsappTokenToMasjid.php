<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Kunci gateway WhatsApp (Fonnte) milik masing-masing masjid.
 *
 * MENGAPA PER MASJID, BUKAN SATU AKUN UNTUK SEMUA
 * Fonnte mengemudikan WhatsApp Web, bukan API resmi — WhatsApp tidak
 * mengizinkan pengiriman ke grup lewat jalur resmi mana pun. Konsekuensinya:
 *
 *  1. Mengirim pengumuman massal dari satu nomor ke banyak grup adalah persis
 *     pola yang diblokir WhatsApp. Dengan satu akun bersama, satu pemblokiran
 *     mematikan siaran SELURUH masjid sekaligus; per masjid, itu masalah satu
 *     masjid saja.
 *  2. Nomor pengirim harus menjadi anggota grup. Akun bersama berarti satu nomor
 *     asing duduk di dalam grup jamaah setiap masjid — jamaah melihatnya seperti
 *     spam dan lebih mudah melaporkannya, yang justru mempercepat pemblokiran.
 *     Nomor DKM sendiri dikenali jamaahnya.
 *  3. Satu akun bersama bisa membaca dan mengirim ke semua grup, artinya
 *     pengelola platform secara teknis berada di dalam grup privat tiap masjid.
 *
 * Sejalan pula dengan pola yang sudah dipakai: masjid.telegram_bot_token dan
 * masjid_payments.multipay_api_key sama-sama per masjid.
 *
 * Masjid yang tidak mau berlangganan Fonnte tetap bisa menyiarkan lewat
 * Telegram, yang resmi, gratis, dan tanpa risiko pemblokiran.
 */
class AddWhatsappTokenToMasjid extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('whatsapp_api_key', 'masjid')) {
            return;
        }

        $this->forge->addColumn('masjid', [
            'whatsapp_api_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'telegram_bot_token',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('whatsapp_api_key', 'masjid')) {
            $this->forge->dropColumn('masjid', 'whatsapp_api_key');
        }
    }
}
