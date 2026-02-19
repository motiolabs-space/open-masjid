<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidSocialModel extends Model
{
    protected $table            = 'masjid_socials';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['masjid_id', 'platform', 'url'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'masjid_id' => 'required|integer',
        'platform'  => 'required|max_length[50]',
        'url'       => 'required|valid_url|max_length[255]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
