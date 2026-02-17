<?php

namespace App\Libraries\PaymentGateway\Drivers;

use App\Libraries\PaymentGateway\GatewayInterface;

class DummyGateway implements GatewayInterface
{
    protected $config;

    public function initialize(array $config)
    {
        $this->config = $config;
    }

    public function createTransaction(array $params)
    {
        // Simulate creating a payment
        // In a real gateway, we would call an API here.
        
        $invoice = $params['invoice_number'];
        $amount  = $params['amount'];
        
        // Generate a dummy payment URL that points to our local simulation page
        $paymentUrl = base_url('payment/simulation/' . $invoice);
        
        return [
            'payment_url' => $paymentUrl,
            'payment_ref' => 'DUMMY-' . uniqid(),
            'raw_response' => [
                'status' => 'created',
                'message' => 'Dummy transaction created successfully'
            ]
        ];
    }

    public function verifyTransaction(string $id)
    {
        // In dummy mode, we can't really verify against an external server unless we store state.
        // For now, we assume this is called when we want to check status manually or via callbacks.
        // We will implement a callback mechanism in the Payment Controller.
        
        return [
            'status' => 'pending', // Default unknown state
            'raw_response' => []
        ];
    }
}
