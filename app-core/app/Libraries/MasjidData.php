<?php

namespace App\Libraries;

use App\Models\MasjidFinanceTransactionModel;

/**
 * Pembaca data masjid yang HANYA-BACA, dipakai bersama oleh MCP server
 * (Api\Mcp) dan REST API (Api\RestApi).
 *
 * Sengaja satu sumber: dua antarmuka menyajikan angka yang SAMA, jadi
 * perhitungannya tidak boleh diduplikasi di dua tempat — kalau tidak, kas yang
 * dilaporkan MCP dan REST bisa berbeda.
 *
 * Setiap metode menerima baris $masjid (yang sudah dipastikan pemanggil dari
 * TOKEN, bukan dari input). Tidak ada metode di sini yang menerima masjid_id
 * mentah — penyaringan tenant terjadi di lapisan pemanggil, dan data di sini
 * selalu difilter dengan $masjid['id'].
 */
class MasjidData
{
    /**
     * Ringkasan kas bulan berjalan.
     *
     * @return array{bulan:string, pemasukan:float, pengeluaran:float, saldo:float}
     */
    public function kasBulanIni(array $masjid): array
    {
        $rows = (new MasjidFinanceTransactionModel())
            ->where('masjid_id', $masjid['id'])
            ->where('MONTH(date)', date('m'))
            ->where('YEAR(date)', date('Y'))
            ->findAll();

        $masuk = 0.0;
        $keluar = 0.0;
        foreach ($rows as $t) {
            if ($t['type'] === 'pemasukan') {
                $masuk += (float) $t['amount'];
            } elseif ($t['type'] === 'pengeluaran') {
                $keluar += (float) $t['amount'];
            }
        }

        return [
            'bulan'       => date('Y-m'),
            'pemasukan'   => $masuk,
            'pengeluaran' => $keluar,
            'saldo'       => $masuk - $keluar,
        ];
    }

    /**
     * Jadwal sholat hari ini (sudah terkoreksi), atau null bila belum tersedia.
     *
     * @return array<string,string>|null  ['Subuh'=>'04:35', ...]
     */
    public function jadwalSholatHariIni(array $masjid): ?array
    {
        return (new PrayerTimes())->hariIni($masjid);
    }

    /**
     * Donasi berhasil terbaru.
     *
     * @return array<int, array{donor_name:string, amount:float, paid_at:?string}>
     */
    public function donasiTerbaru(array $masjid, int $limit = 10): array
    {
        $rows = \Config\Database::connect()->table('masjid_donations')
            ->select('donor_name, amount, paid_at')
            ->where('masjid_id', $masjid['id'])
            ->where('status', 'success')
            ->orderBy('paid_at', 'DESC')
            ->limit(max(1, min($limit, 50)))
            ->get()->getResultArray();

        return array_map(fn($d) => [
            'donor_name' => $d['donor_name'] ?: 'Hamba Allah',
            'amount'     => (float) $d['amount'],
            'paid_at'    => $d['paid_at'] ?: null,
        ], $rows);
    }
}
