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
    protected $validationRules      = [
        'masjid_id' => 'required|is_natural_no_zero',
        'name'      => 'required|min_length[3]',
        'income_per_month' => 'numeric',
        'dependents_count' => 'integer',
        'status'    => 'in_list[active,inactive]'
    ];
}
