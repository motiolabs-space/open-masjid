<?php

namespace App\Models;

use CodeIgniter\Model;

class LmsProgressModel extends Model
{
    protected $table            = 'lms_progress';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'user_id',
        'masjid_id',
        'material_id',
        'completed_at'
    ];

    protected $useTimestamps = false;
}
