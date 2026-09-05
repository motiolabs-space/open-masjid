<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidProgramImpactPhotoModel extends Model
{
    protected $table         = 'masjid_program_impact_photos';
    protected $primaryKey    = 'id';
    protected $allowedFields  = ['masjid_id', 'program_id', 'photo', 'caption'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
