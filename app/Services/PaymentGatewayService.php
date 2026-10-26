<?php

namespace App\Services;

use App\Services\PaymentGateway\FakeGateway;
use App\Services\PaymentGateway\PaynowGateway;
use App\Services\PaymentGateway\PaymentGatewayInterface;
use Exception;

class PaymentGatewayService
{
    protected PaymentGatewayInterface $gateway;

    public function __construct()
    {
        $gatewayType = config('services.payment.driver', 'paynow');

        $this->gateway = match ($gatewayType) {
            'paynow' => new PaynowGateway(),
            'fake' => new FakeGateway(),
            default => throw new Exception("Unsupported payment gateway: {$gatewayType}"),
        };

        
    }

    /**
     * Get the current gateway instance.
     */
    public function getGateway(): PaymentGatewayInterface
    {
        return $this->gateway;
    }

    /**
     * Initiate a payment.
     */
    public function initiate(array $data): array
    {
        return $this->gateway->initiate($data);
    }

    /**
     * Verify a callback signature.
     */
    public function verifySignature(array $data, string $signature): bool
    {
        return $this->gateway->verifySignature($data, $signature);
    }

    /**
     * Process a callback.
     */
    public function processCallback(array $data): array
    {
        return $this->gateway->processCallback($data);
    }

    /**
     * Get payment status.
     */
    public function getStatus(string $reference): array
    {
        return $this->gateway->getStatus($reference);
    }

    /**
     * Simulate success (for testing).
     */
    public function simulateSuccess(string $reference): array
    {
        return $this->gateway->simulateSuccess($reference);
    }

    /**
     * Simulate failure (for testing).
     */
    public function simulateFailure(string $reference): array
    {
        return $this->gateway->simulateFailure($reference);
    }
}
