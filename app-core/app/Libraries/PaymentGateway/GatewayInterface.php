<?php

namespace App\Libraries\PaymentGateway;

interface GatewayInterface
{
    /**
     * Initialize gateway with configuration
     */
    public function initialize(array $config);

    /**
     * Create a new transaction
     * Must return array with:
     * - payment_url: string (Redirect URL)
     * - payment_ref: string (Reference ID from gateway)
     * - raw_response: array (Original response)
     */
    public function createTransaction(array $params);

    /**
     * Verify transaction status
     * Must return array with:
     * - status: string (pending, success, failed, expired)
     * - raw_response: array
     */
    public function verifyTransaction(string $id);
}
