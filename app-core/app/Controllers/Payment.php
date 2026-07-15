<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MasjidDonationModel;
use App\Models\MasjidFinanceTransactionModel;
use App\Models\MasjidFinanceCategoryModel;

class Payment extends BaseController
{
    protected $donationModel;
    protected $financeModel;

    public function __construct()
    {
        $this->donationModel = new MasjidDonationModel();
        $this->financeModel = new MasjidFinanceTransactionModel();
    }

    /**
     * Dummy Payment Simulation Page
     */
    public function simulation($invoice)
    {
        // Decode invoice if needed, or lookup in DB
        $donation = $this->donationModel->where('invoice_number', $invoice)->first();

        if (!$donation) {
            return "Invoice not found";
        }

        if ($donation['status'] == 'success') {
            return "This invoice is already paid.";
        }

        return view('payment/simulation', [
            'donation' => $donation,
            'title'    => 'Pembayaran Donasi'
        ]);
    }

    /**
     * Process Callback from Dummy Gateway (Simulated)
     */
    /**
     * Process Callback (Support Dummy POST & Multipay Webhook)
     */
    public function callback()
    {
        // 1. Check for Multipay Signature (Server-to-Server)
        $signature = $this->request->getHeaderLine('X-Api-Signature');
        if ($signature) {
            return $this->handleMultipayCallback($signature);
        }

        // 2. Fallback to Dummy Simulation (Form POST)
        $invoice = $this->request->getPost('invoice_number');
        $status  = $this->request->getPost('status'); // success or failed

        if (!$invoice) {
            return redirect()->to('/')->with('error', 'Invalid Request');
        }

        $donation = $this->donationModel->where('invoice_number', $invoice)->first();
        if (!$donation) {
            return redirect()->back()->with('error', 'Invoice tidak valid.');
        }

        if ($status === 'success') {
            $this->processSuccess($donation, 'dummy', 'simulation');
            return redirect()->to(base_url('payment/success/' . $invoice));
        } else {
            $this->donationModel->update($donation['id'], ['status' => 'failed']);
            return redirect()->back()->with('error', 'Pembayaran gagal.');
        }
    }

    private function handleMultipayCallback($signature)
    {
        $rawBody = $this->request->getBody();
        $timestamp = $this->request->getHeaderLine('X-Api-Timestamp');
        $data = json_decode($rawBody, true);
        
        $invoice = $data['reff_no'] ?? null;
        if (!$invoice) return $this->response->setStatusCode(400)->setBody('Invoice not found');
        
        $donation = $this->donationModel->where('invoice_number', $invoice)->first();
        if (!$donation) return $this->response->setStatusCode(404)->setBody('Donation not found');

        // Fetch Secret Key
        $paySettings = (new \App\Models\MasjidPaymentModel())->where('masjid_id', $donation['masjid_id'])->first();
        $secretKey = $paySettings['multipay_secret_key'] ?? '';

        // Verify
        $gateway = new \App\Libraries\PaymentGateway\Drivers\MultidayaGateway();
        $gateway->initialize(['secret_key' => $secretKey, 'is_production' => false]);
        
        $isValid = $gateway->verifyTransaction([
            'signature' => $signature,
            'timestamp' => $timestamp,
            'raw_body'  => $rawBody
        ]);

        if (!$isValid) {
             return $this->response->setStatusCode(401)->setBody('Invalid Signature');
        }

        // Process Status
        if (($data['status'] ?? '') === 'SUCCESS') {
            $this->processSuccess($donation, 'multipay', $data['provider'] ?? 'unknown');
        } elseif (in_array(($data['status'] ?? ''), ['FAILED', 'EXPIRED', 'CANCEL'])) {
            $this->donationModel->update($donation['id'], ['status' => 'failed']);
        }

        return $this->response->setJSON(['status' => 'ok']);
    }

    private function processSuccess($donation, $method, $channel)
    {
        // Prevent duplicate processing
        if ($donation['status'] === 'success') return;

        // 1. Update Donation Status
        $this->donationModel->update($donation['id'], [
            'status'  => 'success',
            'paid_at' => date('Y-m-d H:i:s'),
            'payment_method' => $method,
            'payment_channel' => $channel
        ]);

        // 2. Auto-record to Masjid Finance
        // Find or creat generic 'Donasi Online' category
        $catModel = new MasjidFinanceCategoryModel();
        $category = $catModel->where(['masjid_id' => $donation['masjid_id'], 'slug' => 'penerimaan-donasi-online'])->first();
        
        if (!$category) {
            $catId = $catModel->insert([
                'masjid_id' => $donation['masjid_id'], 
                'name' => 'Penerimaan Donasi Online',
                'type' => 'pemasukan',
                'slug' => 'penerimaan-donasi-online'
            ]);
        } else {
            $catId = $category['id'];
        }

        $this->financeModel->insert([
            'masjid_id'   => $donation['masjid_id'],
            'category_id' => $catId,
            'program_id'  => $donation['program_id'],
            'date'        => date('Y-m-d'),
            'amount'      => $donation['amount'],
            'type'        => 'pemasukan',
            'description' => 'Donasi Online #' . $donation['invoice_number'] . ' dari ' . $donation['donor_name'],
            'donor_name'  => $donation['donor_name'],
            'donor_phone' => $donation['donor_phone'],
        ]);

        // 3. Send WhatsApp Receipt if phone exists
        if (!empty($donation['donor_phone'])) {
            $masjidModel = new \App\Models\MasjidModel();
            $masjid = $masjidModel->find($donation['masjid_id']);
            
            $programName = 'Umum';
            if ($donation['program_id']) {
                $prog = (new \App\Models\MasjidProgramModel())->find($donation['program_id']);
                if ($prog) $programName = $prog['title'];
            }

            $wa = new \App\Libraries\WhatsAppService();
            $wa->sendDonationReceipt($donation['donor_phone'], [
                'masjid_name'     => $masjid['name'],
                'masjid_username' => $masjid['username'],
                'donor_name'      => $donation['donor_name'],
                'amount'          => $donation['amount'],
                'program_name'    => $programName
            ]);
        }
    }

    public function success($invoice)
    {
        return view('payment/success', [
            'invoice' => $invoice,
            'title'   => 'Donasi Berhasil',
        ]);
    }
}
