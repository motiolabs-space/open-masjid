<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidPaymentModel extends Model
{
    protected $table            = 'masjid_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'masjid_id', 'payment_mode', 'bank_name', 'bank_account_name', 
        'bank_account_number', 'qris_image', 
        'multipay_api_key', 'multipay_secret_key',
        'api_key', 'api_secret', 'merchant_id', 'callback_token'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'masjid_id'    => 'required|numeric',
        'payment_mode' => 'required|in_list[manual,multipay]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
