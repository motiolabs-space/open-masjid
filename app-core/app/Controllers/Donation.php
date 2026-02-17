<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MasjidDonationModel;
use App\Models\MasjidProgramModel;
use App\Models\MasjidFinanceTransactionModel;
use App\Models\MasjidFinanceCategoryModel;
use App\Libraries\PaymentGateway\PaymentManager;

class Donation extends BaseController
{
    protected $donationModel;
    protected $programModel;
    protected $financeModel;

    public function __construct()
    {
        $this->donationModel = new MasjidDonationModel();
        $this->programModel = new MasjidProgramModel();
        $this->financeModel = new MasjidFinanceTransactionModel();
    }

    /**
     * Show donation form for a specific program or general donation
     */
    public function create($username, $programSlug = null)
    {
        // 1. Resolve Masjid from username (Logic similar to Home::masjid)
        $masjidModel = new \App\Models\MasjidModel();
        $masjid = $masjidModel->where('username', $username)->first();

        if (!$masjid) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $program = null;
        if ($programSlug) {
            $program = $this->programModel->where([
                'masjid_id' => $masjid['id'],
                'slug'      => $programSlug
            ])->first();
        }

        return view('public/donation/form', [
            'masjid'  => $masjid,
            'program' => $program,
            'title'   => 'Donasi - ' . $masjid['name']
        ]);
    }

    /**
     * Process the donation submission
     */
    public function store()
    {
        $masjidId = $this->request->getPost('masjid_id');
        $programId = $this->request->getPost('program_id');
        
        // Generate Invoice Number: INV/YYYYMMDD/RANDOM
        $invoice = 'INV/' . date('Ymd') . '/' . strtoupper(substr(md5(uniqid()), 0, 6));
        
        // 1. Get Payment Settings
        $payModel = new \App\Models\MasjidPaymentModel();
        $settings = $payModel->where('masjid_id', $masjidId)->first();
        $paymentMode = $settings['payment_mode'] ?? 'manual';

        $data = [
            'masjid_id'      => $masjidId,
            'program_id'     => $programId ?: null,
            'invoice_number' => $invoice,
            'amount'         => str_replace(['.', ','], ['', '.'], $this->request->getPost('amount')),
            'donor_name'     => $this->request->getPost('name'),
            'donor_email'    => $this->request->getPost('email'),
            'donor_phone'    => $this->request->getPost('phone'),
            'message'        => $this->request->getPost('message'),
            'status'         => 'pending',
            'payment_method' => $paymentMode
        ];

        // 2. Save pending donation
        if (!$this->donationModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses donasi.');
        }

        $donationId = $this->donationModel->getInsertID();

        // 3. Process based on Mode
        if ($paymentMode === 'manual') {
            return redirect()->to(base_url("donation/manual/$invoice"));
        }

        // MULTIPAY Logic
        $config = [
            'api_key'       => $settings['multipay_api_key'] ?? '',
            'secret_key'    => $settings['multipay_secret_key'] ?? '',
            'is_production' => false // Default sandbox
        ];

        $paymentManager = new PaymentManager('multidaya', $config);
        $gateway = $paymentManager->getGateway();

        // Fetch program title for item name
        $programTitle = 'Donasi Umum';
        if ($programId) {
            $prog = $this->programModel->find($programId);
            if ($prog) $programTitle = $prog['title'];
        }

        try {
            $transaction = $gateway->createTransaction([
                'invoice_number' => $invoice,
                'amount'         => $data['amount'],
                'customer_name'  => $data['donor_name'],
                'customer_email' => $data['donor_email'],
                'items' => [[
                    'name' => "Donasi: " . substr($programTitle, 0, 40),
                    'unit_price' => $data['amount'],
                    'qty' => 1
                ]]
            ]);

            // Update donation with payment info
            $this->donationModel->update($donationId, [
                'payment_ref' => $transaction['payment_ref'] ?? null,
                'payment_url' => $transaction['payment_url']
            ]);

            return redirect()->to($transaction['payment_url']);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gateway Error: ' . $e->getMessage());
        }
    }

    public function manual($invoice)
    {
        $donation = $this->donationModel->where('invoice_number', $invoice)->first();
        if (!$donation) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $payModel = new \App\Models\MasjidPaymentModel();
        $settings = $payModel->where('masjid_id', $donation['masjid_id'])->first();

        return view('payment/manual', [
            'title' => 'Instruksi Pembayaran',
            'donation' => $donation,
            'paymentSettings' => $settings ?: [],
            'storage' => new \App\Libraries\Storage()
        ]);
    }
}
