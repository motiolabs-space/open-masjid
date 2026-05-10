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
                $mapped['amount'] = $this->cleanAmount($mapped['raw_amount']);
                break;
        }

        // Basic validation: must have amount and date
        if (empty($mapped['date']) || $mapped['amount'] <= 0) {
            return null;
        }

        return $mapped;
    }

    private function cleanAmount($val)
    {
        $clean = preg_replace('/[^0-9.]/', '', str_replace(',', '.', $val));
        return (float) $clean;
    }
}
