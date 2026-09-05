<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidVolunteerModel extends Model
{
    protected $table         = 'masjid_volunteers';
    protected $primaryKey    = 'id';
    protected $allowedFields  = ['masjid_id', 'warga_id', 'name', 'phone', 'role', 'points', 'status', 'joined_at'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
