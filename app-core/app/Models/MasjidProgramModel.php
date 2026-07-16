<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidProgramModel extends Model
{
    protected $table            = 'masjid_programs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'masjid_id', 'category_id', 'title', 'slug', 'description', 'thumbnail', 
        'date_start', 'date_end', 'location', 'registration_link', 
        'quota', 'target_donation', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    // Lihat catatan pada MasjidNewsModel soal label.
    protected $validationRules      = [
        'masjid_id' => 'required|numeric',
        'title'     => [
            'label' => 'Nama Program',
            'rules' => 'required|min_length[3]|max_length[255]',
        ],
        'slug'        => 'required|max_length[255]',
        'description' => [
            'label' => 'Deskripsi Program',
            'rules' => 'required',
        ],
        'date_start' => [
            'label' => 'Tanggal Mulai',
            'rules' => 'required|valid_date',
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
