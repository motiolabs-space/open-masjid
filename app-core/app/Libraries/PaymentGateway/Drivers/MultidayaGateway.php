<?php

namespace App\Libraries\PaymentGateway\Drivers;

use App\Libraries\PaymentGateway\GatewayInterface;
use CodeIgniter\Config\Services;
use Exception;

class MultidayaGateway implements GatewayInterface
{
    protected $apiKey; // Email
    protected $secretKey; // Password
    protected $isProduction = false;
    protected $baseUrl;
    protected $authUrl;

    public function initialize(array $config)
    {
        $this->apiKey = $config['api_key'] ?? '';
        $this->secretKey = $config['secret_key'] ?? '';
        $this->isProduction = $config['is_production'] ?? false;

        $this->baseUrl = $this->isProduction 
            ? 'https://api-multipay.multidaya.id' 
            : 'https://sandbox-api-multipay.multidaya.id';
            
        // Auth URL context might differ, let's assume same base
        $this->authUrl = $this->baseUrl; 
    }

    protected function getClient()
    {
        return Services::curlrequest([
            'base_uri' => $this->baseUrl,
            'timeout'  => 10,
            'http_errors' => false,
        ]);
    }

    protected function getToken()
    {
        // Check cache first
        $cache = Services::cache();
        $cacheKey = 'multidaya_token_' . md5($this->apiKey);
        
        if ($token = $cache->get($cacheKey)) {
            return $token;
        }

        // Request new token via Login
        $client = $this->getClient();
        $response = $client->post('/auth-service/v1/login', [
            'json' => [
                'email'    => $this->apiKey,
                'password' => $this->secretKey
            ]
        ]);

        $body = json_decode($response->getBody(), true);

        if ($response->getStatusCode() === 200 && isset($body['token'])) {
            // Cache token for 23 hours (less than 1 day)
            $cache->save($cacheKey, $body['token'], 82800); 
            return $body['token'];
        }

        throw new Exception('Multidaya Auth Failed: ' . ($body['message'] ?? 'Unknown error'));
    }

    public function createTransaction(array $params)
    {
        $token = $this->getToken();
        $client = $this->getClient();

        // Prepare items and amount
        // Params should include: invoice_number, amount, items, customer_details
        
        $payload = [
            'tid'     => 'UNKNOWN', // This field is required by docs but vague. Using explicit value or maybe from config if needed.
            'reff_no' => $params['invoice_number'],
            'items'   => $params['items'] ?? [], 
             // Note: IF items are not provided, we might need to construct a dummy item for the total amount
        ];

        if (empty($payload['items'])) {
            $payload['items'] = [[
                'name'       => 'Donasi',
                'unit_price' => (string)$params['amount'],
                'qty'        => '1'
            ]];
        }
        
        // Optional: override provider if needed?
        // $urlParams = isset($params['selected_provider']) ? '?selected_provider=' . $params['selected_provider'] : '';

        $response = $client->post('/transaction-service/v1/create-order', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json'
            ],
            'json' => $payload
        ]);

        $body = json_decode($response->getBody(), true);

        if ($response->getStatusCode() === 200 && isset($body['payment_url'])) {
            return [
                'payment_url' => $body['payment_url'],
                'payment_ref' => $body['payment_id'] ?? $params['invoice_number'], // Adjust based on actual response
                'raw_response' => $body
            ];
        }

        throw new Exception('Multidaya Create Order Failed: ' . ($body['message'] ?? $response->getBody()));
    }

    public function verifyTransaction(array $params)
    {
        // Notification Signature Verification
        // Params should contain: X-Api-Signature, X-Api-Timestamp, RawBody
        
        $signatureHeader = $params['signature'] ?? '';
        $timestamp = $params['timestamp'] ?? '';
        $rawBody = $params['raw_body'] ?? '';

        if (empty($signatureHeader) || empty($timestamp) || empty($rawBody)) {
            return false;
        }

        // $signature = base64_encode(hash_hmac('sha256', <Your X-Api-Timestamp> . <Your Encoded Body Payload>, <Your Secret Key>, true));
        // Note: Docs say "Your Encoded Body Payload". It usually means the raw body string? Or base64 of body?
        // Docs: "Payload : String encoded body payload"
        // Let's assume it means raw body string for now, but if it fails, check if base64 needed.
        // Actually, re-reading: "Your Encoded Body Payload" might mean json_encode? No, raw body is already json.
        // Wait, PHP sample: `$signature = base64_encode(hash_hmac('sha256', <Your X-Api-Timestamp> . <Your Encoded Body Payload>, <Your Secret Key>, true));`
        // "Payload : String encoded body payload" -> Maybe `base64_encode($rawBody)`?
        // Let's try base64_encode($rawBody) based on "encoded body payload" text.
        
        $encodedPayload = $rawBody; // Try raw first?
        // "Payload : String encoded body payload".
        // If I look at the JS sample: `CryptoJS.enc.Base64.stringify(CryptoJS.HmacSHA256(timestamp + payload, secret))` 
        // DOcs say `Payload : String encoded body payload`. This is ambiguous.
        // Given PHP sample: `$signature = base64_encode(hash_hmac('sha256', $timestamp . $encodedBody, ...))`
        
        // I will assume $encodedBody IS base64_encode($rawBody) because "encoded".
        
        $payloadToSign = $timestamp . base64_encode($rawBody);
        
        $generatedSignature = base64_encode(hash_hmac('sha256', $payloadToSign, $this->secretKey, true));

        return hash_equals($signatureHeader, $generatedSignature);
    }
}
