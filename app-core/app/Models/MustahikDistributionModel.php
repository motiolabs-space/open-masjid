<?php

namespace App\Models;

use CodeIgniter\Model;

class MustahikDistributionModel extends Model
{
    protected $table            = 'masjid_mustahik_distributions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'masjid_id', 'mustahik_id', 'date', 'amount', 'description'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation — lihat catatan pada MustahikModel soal bentuk label/rules.
    protected $validationRules      = [
        'masjid_id'   => 'required|is_natural_no_zero',
        'mustahik_id' => [
            'label' => 'Mustahik Penerima',
            'rules' => 'required|is_natural_no_zero',
        ],
        'date' => [
            'label' => 'Tanggal Penyaluran',
            'rules' => 'required|valid_date',
        ],
        'amount' => [
            'label' => 'Nominal',
            'rules' => 'required|numeric',
        ],
    ];
}
