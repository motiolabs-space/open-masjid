<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Mencatat ASAL pendaftar: utm_source, utm_medium, utm_campaign, dan referrer.
 *
 * Sebelum ini pendaftaran hanya menyimpan alamat IP dan negara, sehingga
 * pertanyaan paling pokok bagi pemasaran — "kanal mana yang benar-benar
 * mendatangkan masjid" — tidak bisa dijawab dengan data, hanya dikira-kira.
 * Akibatnya tenaga dan anggaran tak punya dasar untuk dialihkan.
 *
 * Ditaruh pada DUA tabel karena keduanya adalah peristiwa akuisisi yang
 * berbeda: `masjid` untuk masjid yang mendaftar (satuan yang paling menentukan
 * bagi GTM), `users` untuk jamaah yang mendaftar sendiri.
 *
 * Semua nullable — pendaftar yang datang langsung tanpa kampanye memang tidak
 * punya nilai apa pun di sini, dan itu sah.
 *
 * Idempoten (`fieldExists`) agar aman dijalankan pada basis data yang sebagian
 * kolomnya sudah ada.
 */
class AddAcquisitionColumns extends Migration
{
    /** @var array<string, array<string, mixed>> */
    private array $kolom = [
        'utm_source'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'utm_medium'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'utm_campaign' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'referrer'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
    ];

    private array $tabel = ['masjid', 'users'];

    public function up()
    {
        foreach ($this->tabel as $tabel) {
            if (! $this->db->tableExists($tabel)) {
                continue;
            }

            $tambah = [];
            foreach ($this->kolom as $nama => $definisi) {
                if (! $this->db->fieldExists($nama, $tabel)) {
                    $tambah[$nama] = $definisi;
                }
            }

            if ($tambah !== []) {
                $this->forge->addColumn($tabel, $tambah);
            }
        }
    }

    public function down()
    {
        foreach ($this->tabel as $tabel) {
            if (! $this->db->tableExists($tabel)) {
                continue;
            }

            $buang = [];
            foreach (array_keys($this->kolom) as $nama) {
                if ($this->db->fieldExists($nama, $tabel)) {
                    $buang[] = $nama;
                }
            }

            if ($buang !== []) {
                $this->forge->dropColumn($tabel, $buang);
            }
        }
    }
}
