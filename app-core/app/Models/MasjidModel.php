<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidModel extends Model
{
    protected $table            = 'masjid';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name', 'tagline', 'nama_resmi', 'username', 'tahun_berdiri', 'jenis_masjid', 
        'no_sk', 'address', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 'provinsi_id', 'regency_id', 'district_id', 'village_id', 
        'visi', 'misi', 'foto_utama', 'logo', 'about_us', 'phone', 'whatsapp', 'email', 'telegram_bot_token',
        'action_button_active', 'action_button_text', 'action_button_url',
        'latitude', 'longitude', 'is_external_service',
        'username_updated_at', 'menu_berita', 'menu_program', 'menu_laporan', 'menu_kontak'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name'     => 'required|min_length[3]',
        'username' => 'required|alpha_dash|is_unique[masjid.username]|min_length[3]'
    ];
    protected $validationMessages   = [
        'username' => [
            'is_unique' => 'Username masjid ini sudah digunakan. Silakan pilih username lain.',
            'alpha_dash' => 'Username hanya boleh berisi huruf, angka, dash (-), dan underscore (_).'
        ]
    ];
    protected $cleanValidationRules = true;
}
