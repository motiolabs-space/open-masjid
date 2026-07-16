<?php

namespace App\Models;

use CodeIgniter\Model;

class MustahikModel extends Model
{
    protected $table            = 'masjid_mustahik';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'masjid_id', 'name', 'nik', 'phone', 'address', 
        'income_per_month', 'dependents_count', 'house_ownership', 
        'status', 'ai_score', 'ai_reasoning'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    //
    // Bentuk ['label' => ..., 'rules' => ...] dipakai agar pesan kesalahan
    // menyebut nama yang dikenal pengurus, bukan nama kolom basis data —
    // tanpa label, tertulis "Kolom name harus diisi minimal 3 huruf."
    // (CI4 tidak mengenal properti $validationLabels; label hanya bisa
    // disisipkan di dalam aturan seperti ini.)
    // Kalimat pesannya sendiri ada di app/Language/en/Validation.php.
    protected $validationRules      = [
        'masjid_id' => 'required|is_natural_no_zero',
        'name'      => [
            'label' => 'Nama Lengkap',
            'rules' => 'required|min_length[3]',
        ],
        'income_per_month' => [
            'label' => 'Pendapatan per Bulan',
            'rules' => 'numeric',
        ],
        'dependents_count' => [
            'label' => 'Jumlah Tanggungan',
            'rules' => 'integer',
        ],
        'status' => [
            'label' => 'Status',
            'rules' => 'in_list[active,inactive]',
        ],
    ];
}
