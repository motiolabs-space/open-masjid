<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidVolunteerPointModel extends Model
{
    protected $table         = 'masjid_volunteer_points';
    protected $primaryKey    = 'id';
    protected $allowedFields  = ['masjid_id', 'volunteer_id', 'program_id', 'points', 'reason'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
