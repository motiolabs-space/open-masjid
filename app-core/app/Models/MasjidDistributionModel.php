<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidDistributionModel extends Model
{
    protected $table            = 'masjid_distributions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['masjid_id', 'warga_id', 'program_id', 'date', 'type', 'amount', 'items', 'description', 'evidence_photo'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    // Label disisipkan ke dalam aturan agar pesan kesalahan menyebut nama yang
    // dikenal pengurus, bukan nama kolom basis data ("Kolom date wajib diisi").
    // Kalimat pesannya ada di app/Language/en/Validation.php.
    protected $validationRules      = [
        'date' => [
            'label' => 'Tanggal Penyaluran',
            'rules' => 'required',
        ],
        'type' => [
            'label' => 'Jenis Bantuan',
            'rules' => 'required',
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
