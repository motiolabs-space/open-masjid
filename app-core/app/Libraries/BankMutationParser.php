<?php

namespace App\Libraries;

class BankMutationParser
{
    /**
     * Parse CSV bank mutation based on bank type
     * 
     * @param string $filePath
     * @param string $bankType (bca, mandiri, bsi, bni, bri)
     * @return array
     */
    public function parse($filePath, $bankType = 'generic')
    {
        $rows = [];
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $header = fgetcsv($handle, 1000, ","); // Skip/read header
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $parsed = $this->mapRow($data, $bankType);
                if ($parsed) {
                    $rows[] = $parsed;
                }
            }
            fclose($handle);
        }
        return $rows;
    }

    private function mapRow($data, $bankType)
    {
        // Default / Generic Mapping (Date, Description, Amount, Type)
        $mapped = [
            'date'        => $data[0] ?? '',
            'description' => $data[1] ?? '',
            'amount'      => 0,
            'type'        => 'CR', // CR = Credit/Masuk, DB = Debit/Keluar
            'raw_amount'  => $data[2] ?? 0
        ];

        switch (strtolower($bankType)) {
            case 'bca':
                // BCA Example: Tgl, Keterangan, Cabang, Nominal, Tipe (CR/DB)
                $mapped['date'] = $data[0] ?? '';
                $mapped['description'] = $data[1] ?? '';
                $mapped['amount'] = $this->cleanAmount($data[3] ?? 0);
                $mapped['type'] = ($data[4] ?? 'CR') == 'CR' ? 'CR' : 'DB';
                break;

            case 'mandiri':
            case 'bsi':
                // Mandiri/BSI Example: Date, Description, Ref, Debit, Credit
                $mapped['date'] = $data[0] ?? '';
                $mapped['description'] = $data[1] ?? '';
                $debit = $this->cleanAmount($data[3] ?? 0);
                $credit = $this->cleanAmount($data[4] ?? 0);
                
                if ($credit > 0) {
                    $mapped['amount'] = $credit;
                    $mapped['type'] = 'CR';
                } else {
                    $mapped['amount'] = $debit;
                    $mapped['type'] = 'DB';
                }
                break;

            default:
                // Tanpa kolom penanda CR/DB, satu-satunya petunjuk arah uang
                // adalah tanda minus pada nominalnya.
                helper('custom');
                $nominal          = parse_rupiah($mapped['raw_amount']);
                $mapped['amount'] = abs($nominal);
                $mapped['type']   = $nominal < 0 ? 'DB' : 'CR';
                break;
        }

        // Tanggal dibakukan ke 'Y-m-d' di sini, sekali saja, supaya tampilan dan
        // penyimpanan tidak perlu lagi menebak urutan hari/bulan. Baris yang
        // tanggalnya tak terbaca DIBUANG: menyimpannya berarti transaksi
        // bertanggal ngawur menyelinap ke buku kas masjid.
        helper('custom');
        $mapped['date'] = parse_tanggal($mapped['date']);

        if ($mapped['date'] === null || $mapped['amount'] <= 0) {
            return null;
        }

        return $mapped;
    }

    /**
     * Nominal selalu dikembalikan positif; arah uang (CR/DB) ditentukan kolom
     * lain, kecuali pada mode generic yang memakai tanda minus (lihat mapRow).
     */
    private function cleanAmount($val)
    {
        helper('custom');

        return abs(parse_rupiah($val));
    }
}
