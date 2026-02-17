<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidInventoryModel extends Model
{
    protected $table            = 'masjid_inventory';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'masjid_id', 'name', 'brand', 'quantity', 'unit', 
        'condition', 'purchase_date', 'purchase_price', 
        'description', 'photo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'masjid_id' => 'required',
        'name'      => 'required|min_length[3]|max_length[255]',
        'quantity'  => 'required|integer',
        'condition' => 'required|in_list[good,damaged_light,damaged_heavy,lost]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
