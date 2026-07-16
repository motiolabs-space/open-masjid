<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidNewsModel extends Model
{
    protected $table            = 'masjid_news';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'masjid_id', 'category_id', 'title', 'slug', 'content', 'thumbnail', 'video_url', 'status', 'views'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    // Label disisipkan ke dalam aturan agar pesan kesalahan menyebut nama yang
    // dikenal pengurus, bukan nama kolom basis data ("Kolom title ...").
    // Kalimat pesannya ada di app/Language/en/Validation.php.
    protected $validationRules      = [
        'title' => [
            'label' => 'Judul Berita',
            'rules' => 'required|min_length[5]|max_length[255]',
        ],
        'slug'    => 'required|max_length[255]',
        'content' => [
            'label' => 'Isi Berita',
            'rules' => 'required',
        ],
        'masjid_id' => 'required|numeric',
    ];
}
