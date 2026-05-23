<?php

namespace App\Models;

use CodeIgniter\Model;

class LmsMaterialModel extends Model
{
    protected $table            = 'lms_materials';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'module_id',
        'title',
        'type',
        'content',
        'order_number',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
