<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidPushMessageModel extends Model
{
    protected $table         = 'masjid_push_messages';
    protected $primaryKey    = 'id';
    protected $allowedFields  = ['masjid_id', 'title', 'body', 'url'];
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
