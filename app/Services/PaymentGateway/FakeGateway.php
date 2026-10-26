<?php

namespace App\Services\PaymentGateway;

class FakeGateway implements PaymentGatewayInterface
{
    protected string $secret;

    public function __construct()
    {
        $this->secret = config('services.payment.fake_secret', 'fake-secret-key');
    }

    /**
     * Initiate a payment for an order.
     */
    public function initiate(array $data): array
    {
        $reference = 'FAKE-' . uniqid();

        return [
            'success' => true,
            'reference' => $reference,
            'redirect_url' => route('payment.fake.redirect', ['reference' => $reference]),
            'amount' => $data['amount'],
            'order_id' => $data['order_id'],
        ];
    }

    /**
     * Verify the payment callback signature.
     */
    public function verifySignature(array $data, string $signature): bool
    {
        $expectedSignature = hash_hmac('sha256', json_encode($data), $this->secret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Process the payment callback.
     */
    public function processCallback(array $data): array
    {
        // For fake gateway, we simulate the status based on the reference
        $reference = $data['reference'] ?? null;

        if (!$reference) {
            return [
                'success' => false,
                'status' => 'Failed',
                'message' => 'Invalid reference',
            ];
        }

        // Check if this is a simulated success or failure
        if (str_contains($reference, '-SUCCESS')) {
            return [
                'success' => true,
                'status' => 'Successful',
                'reference' => $reference,
                'amount' => $data['amount'] ?? 0,
                'paid_at' => now()->toIso8601String(),
            ];
        }

        if (str_contains($reference, '-FAIL')) {
            return [
                'success' => false,
                'status' => 'Failed',
                'reference' => $reference,
                'message' => 'Payment failed',
            ];
        }

        // Default to pending for new references
        return [
            'success' => false,
            'status' => 'Initiated',
            'reference' => $reference,
        ];
    }

    /**
     * Get the payment status for a transaction.
     */
    public function getStatus(string $reference): array
    {
        // For fake gateway, return the status based on reference pattern
        if (str_contains($reference, '-SUCCESS')) {
            return [
                'status' => 'Successful',
                'paid_at' => now()->toIso8601String(),
            ];
        }

        if (str_contains($reference, '-FAIL')) {
            return [
                'status' => 'Failed',
            ];
        }

        return [
            'status' => 'Initiated',
        ];
    }

    /**
     * Simulate a successful payment (for testing only).
     */
    public function simulateSuccess(string $reference): array
    {
        $successReference = $reference . '-SUCCESS';

        return [
            'success' => true,
            'reference' => $successReference,
            'status' => 'Successful',
            'amount' => 0,
            'paid_at' => now()->toIso8601String(),
            'callback_url' => route('payment.callback', [
                'gateway' => 'fake',
                'reference' => $successReference,
                'status' => 'Successful',
                'amount' => 0,
                'signature' => $this->generateSignature([
                    'reference' => $successReference,
                    'status' => 'Successful',
                    'amount' => 0,
                ]),
            ]),
        ];
    }

    /**
     * Simulate a failed payment (for testing only).
     */
    public function simulateFailure(string $reference): array
    {
        $failReference = $reference . '-FAIL';

        return [
            'success' => false,
            'reference' => $failReference,
            'status' => 'Failed',
            'callback_url' => route('payment.callback', [
                'gateway' => 'fake',
                'reference' => $failReference,
                'status' => 'Failed',
                'signature' => $this->generateSignature([
                    'reference' => $failReference,
                    'status' => 'Failed',
                ]),
            ]),
        ];
    }

    /**
     * Generate a signature for the callback.
     */
    protected function generateSignature(array $data): string
    {
        return hash_hmac('sha256', json_encode($data), $this->secret);
    }
}
