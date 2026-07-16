<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidWargaModel extends Model
{
    protected $table            = 'masjid_warga';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'masjid_id', 'name', 'nik', 'kk', 'phone', 
        'address', 'economic_status', 'status', 'notes'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    // Label disisipkan ke dalam aturan agar pesan kesalahan menyebut nama yang
    // dikenal pengurus, bukan nama kolom basis data ("Kolom nik ...").
    // Kalimat pesannya ada di app/Language/en/Validation.php.
    protected $validationRules      = [
        'masjid_id' => 'required',
        'name'      => [
            'label' => 'Nama Lengkap',
            'rules' => 'required|min_length[3]|max_length[100]',
        ],
        'nik' => [
            'label' => 'NIK',
            'rules' => 'permit_empty|numeric|min_length[16]|max_length[16]',
        ],
        'kk' => [
            'label' => 'Nomor Kartu Keluarga',
            'rules' => 'permit_empty|numeric|min_length[16]|max_length[16]',
        ],
        'phone' => [
            'label' => 'Nomor HP',
            'rules' => 'permit_empty|numeric|min_length[10]|max_length[15]',
        ],
        'status' => [
            'label' => 'Status Warga',
            'rules' => 'required|in_list[active,inactive,moved,deceased]',
        ],
        'economic_status' => [
            'label' => 'Kondisi Ekonomi',
            'rules' => 'required|in_list[mampu,cukup,kurang_mampu,fakir,miskin,yatim]',
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
