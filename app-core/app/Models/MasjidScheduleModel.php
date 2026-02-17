<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidScheduleModel extends Model
{
    protected $table            = 'masjid_schedules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'masjid_id', 'date', 'prayer_type', 
        'imam_name', 'khatib_name', 'muadzin_name', 'bilal_name'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'masjid_id'   => 'required|numeric',
        'date'        => 'required|valid_date',
        'prayer_type' => 'required|in_list[subuh,dzuhur,ashar,maghrib,isya,jumat,tarawih,eid_fitr,eid_adha]',
        'imam_name'   => 'permit_empty|min_length[3]|max_length[100]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
