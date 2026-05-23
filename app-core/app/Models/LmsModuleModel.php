<?php

namespace App\Models;

use CodeIgniter\Model;

class LmsModuleModel extends Model
{
    protected $table            = 'lms_modules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'title',
        'slug',
        'description',
        'thumbnail',
        'lembaga_pemateri',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
