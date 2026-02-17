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
    protected $validationRules      = [
        'masjid_id' => 'required',
        'name'      => 'required|min_length[3]|max_length[100]',
        'nik'       => 'permit_empty|numeric|min_length[16]|max_length[16]',
        'kk'        => 'permit_empty|numeric|min_length[16]|max_length[16]',
        'phone'     => 'permit_empty|numeric|min_length[10]|max_length[15]',
        'status'    => 'required|in_list[active,inactive,moved,deceased]',
        'economic_status' => 'required|in_list[mampu,cukup,kurang_mampu,fakir,miskin,yatim]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
