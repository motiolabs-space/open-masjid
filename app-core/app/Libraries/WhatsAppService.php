<?php

namespace App\Libraries;

/**
 * WhatsApp Service for Masj.id
 * Handles community engagement and automated receipts.
 */
class WhatsAppService
{
    protected $apiKey;
    protected $sender;

    public function __construct($config = [])
    {
        // Future: Fetch from DB masjid_settings
        $this->apiKey = $config['api_key'] ?? env('WHATSAPP_API_KEY');
        $this->sender = $config['sender'] ?? env('WHATSAPP_SENDER');
    }

    /**
     * Send a Jazakallah Receipt to Donors
     */
    public function sendDonationReceipt($phone, $data)
    {
        $masjidName = $data['masjid_name'] ?? 'Masjid Kami';
        $amount     = number_format($data['amount'], 0, ',', '.');
        $donorName  = $data['donor_name'] ?? 'Hamba Allah';
        $program    = $data['program_name'] ?? 'Umum';
        
        $message = "Jazakallah Khairaan Katsiira, Bapak/Ibu *$donorName*.\n\n";
        $message .= "Kami telah menerima donasi Anda melalui *Masj.id* sebesar *Rp $amount* untuk program *$program* di *$masjidName*.\n\n";
        $message .= "Semoga menjadi pemberat amal timbangan di akhirat kelak. Aamiin.\n\n";
        $message .= "---\n";
        $message .= "Cek laporan amanah di: " . base_url($data['masjid_username'] . '/laporan');

        return $this->sendMessage($phone, $message);
    }

    /**
     * Internal Send Method
     */
    protected function sendMessage($phone, $message)
    {
        // Sanitize phone
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strpos($phone, '0') === 0) $phone = '62' . substr($phone, 1);

        // Placeholder for real API call (e.g. Fonnte / Wablas)
        log_message('info', "WA to $phone: $message");

        // If no API key, just log it
        if (empty($this->apiKey)) {
            return true; 
        }

        // Example Fonnte Implementation:
        /*
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: $this->apiKey"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        */

        return true;
    }
}
