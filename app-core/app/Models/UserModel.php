<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'email', 'phone', 'password_hash', 'role', 'last_login'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name'     => 'required|min_length[3]',
        'email'    => 'required|valid_email|is_unique[users.email]',
        'password_hash' => 'required',
        'role'     => 'required|in_list[superadmin,user]'
    ];
    protected $validationMessages   = [
        'email' => [
            'is_unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau masuk ke akun yang ada.'
        ]
    ];
    protected $cleanValidationRules = true;
}
