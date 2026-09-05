<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidPushSubscriptionModel extends Model
{
    protected $table         = 'masjid_push_subscriptions';
    protected $primaryKey    = 'id';
    protected $allowedFields  = ['masjid_id', 'endpoint', 'p256dh', 'auth', 'endpoint_hash'];
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
