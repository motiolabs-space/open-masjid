<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidDonationModel extends Model
{
    protected $table            = 'masjid_donations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'masjid_id', 'program_id', 'invoice_number', 'amount',
        'donor_name', 'donor_email', 'donor_phone', 'message',
        'payment_method', 'payment_channel', 'payment_ref', 'payment_url',
        'status', 'paid_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'masjid_id'      => 'required',
        'invoice_number' => 'required|is_unique[masjid_donations.invoice_number]',
        'amount'         => 'required|numeric|greater_than[0]',
        'donor_name'     => 'required|min_length[3]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
