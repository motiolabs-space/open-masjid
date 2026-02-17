<?php

namespace App\Libraries\PaymentGateway;

use App\Libraries\PaymentGateway\Drivers\DummyGateway;
use Exception;

class PaymentManager
{
    protected $driverMode;
    protected $config;

    /**
     * @param string $driverMode 'dummy' or 'multidaya'
     * @param array $config Configuration for the driver
     */
    public function __construct(string $driverMode = 'dummy', array $config = [])
    {
        $this->driverMode = $driverMode;
        $this->config = $config;
    }

    /**
     * Get the payment gateway instance
     */
    public function getGateway(): GatewayInterface
    {
        switch ($this->driverMode) {
            case 'dummy':
                $gateway = new DummyGateway();
                break;
            case 'multidaya':
                $gateway = new Drivers\MultidayaGateway();
                break;
            default:
                throw new Exception("Payment driver '{$this->driverMode}' not supported.");
        }

        $gateway->initialize($this->config);
        return $gateway;
    }
}
