<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidBroadcastModel extends Model
{
    protected $table            = 'masjid_broadcasts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    // 'group_id' WAJIB ada di sini. Kolom yang tidak terdaftar dibuang diam-diam
    // oleh model — tanpa pesan apa pun — sehingga riwayat siaran tersimpan tanpa
    // keterangan grup tujuannya dan pertanyaan "pengumuman kemarin masuk ke grup
    // mana?" tidak bisa dijawab.
    protected $allowedFields    = ['masjid_id', 'subject', 'content', 'type', 'group_id', 'status', 'recipient_count', 'sent_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'subject' => 'required',
        'content' => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
