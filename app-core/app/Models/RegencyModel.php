<?php

namespace App\Models;

use CodeIgniter\Model;

class RegencyModel extends Model
{
    protected $table            = 'regencies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'province_id', 'name'];
}
