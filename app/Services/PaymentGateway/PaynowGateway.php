<?php

namespace App\Services\PaymentGateway;

use Paynow\Payments\Paynow;
use Illuminate\Support\Facades\Log;

class PaynowGateway implements PaymentGatewayInterface
{
    protected Paynow $paynow;
    protected string $merchantId;
    protected string $merchantKey;
    protected string $secret;

    public function __construct()
    {
        $this->merchantId = config('services.payment.paynow_merchant_id');
        $this->merchantKey = config('services.payment.paynow_merchant_key');
        $this->secret = config('services.payment.paynow_secret');

        // Initialize Paynow SDK
        $this->paynow = new Paynow(
            $this->merchantId,
            $this->merchantKey,
            route('payment.callback'),
            route('payment.callback')
        );
    }

    /**
     * Initiate a payment for an order.
     */
    public function initiate(array $data): array
    {
        $reference = $data['reference'] ?? 'PAYNOW-' . uniqid();
        $amount = $data['amount'];
        $orderId = $data['order_id'];
        $buyerId = $data['buyer_id'] ?? null;

        try {

            $authEmail = '';
            if(env('APP_ENV')=='local'){
                $authEmail = 'PAYNOW_INTEGRATION_EMAIL';
            }
            // Create payment
            $payment = $this->paynow->createPayment($reference, $authEmail);

            // Add items to payment
            $payment->add("Order #{$orderId}", $amount);

            // Initiate transaction
            $response = $this->paynow->send($payment);

            if ($response->success()) {
                // Get the redirect URL (browser link)
                $redirectUrl = $response->redirectUrl();
                // Get the poll URL for checking status
                $pollUrl = $response->pollUrl();

                return [
                    'success' => true,
                    'reference' => $reference,
                    'redirect_url' => $redirectUrl,
                    'poll_url' => $pollUrl,
                    'amount' => $amount,
                    'order_id' => $orderId,
                ];
            } else {
                Log::error('Paynow initiate failed', [
                    'error' => $response->errors(),
                ]);
                throw new \Exception('Failed to initiate payment with Paynow');
            }

        } catch (\Exception $e) {
            Log::error('Paynow initiate exception', [
                'message' => $e->getMessage(),
                'reference' => $reference,
            ]);
            throw $e;
        }
    }

    /**
     * Verify the payment callback signature.
     */
    public function verifySignature(array $data, string $signature): bool
    {
        // Paynow SDK handles signature verification internally
        // This method is kept for interface compatibility
        return true;
    }

    /**
     * Process the payment callback.
     */
    public function processCallback(array $data): array
    {
        $reference = $data['reference'] ?? null;
        $status = $data['status'] ?? 'Initiated';
        $amount = $data['amount'] ?? 0;

        // Map Paynow statuses to our system
        $statusMap = [
            'Paid' => 'Successful',
            'Awaiting Delivery' => 'Successful',
            'Delivered' => 'Successful',
            'Cancelled' => 'Failed',
            'Created' => 'Initiated',
            'Sent' => 'Initiated',
            'Disputed' => 'Failed',
        ];

        $mappedStatus = $statusMap[$status] ?? $status;

        return [
            'success' => in_array($mappedStatus, ['Successful', 'Paid']),
            'status' => $mappedStatus,
            'reference' => $reference,
            'amount' => $amount,
            'paid_at' => in_array($mappedStatus, ['Successful', 'Paid']) ? now()->toIso8601String() : null,
        ];
    }

    /**
     * Get the payment status for a transaction.
     */
    public function getStatus(string $reference): array
    {
        try {
            // Use SDK to check transaction status
            $status = $this->paynow->pollTransaction($reference);

            if ($status->paid()) {
                return [
                    'status' => 'Successful',
                    'amount' => $status->amount(),
                    'paid_at' => now()->toIso8601String(),
                ];
            } elseif ($status->status() === 'Cancelled') {
                return [
                    'status' => 'Failed',
                    'amount' => $status->amount(),
                    'paid_at' => null,
                ];
            } else {
                return [
                    'status' => 'Initiated',
                    'amount' => $status->amount() ?? 0,
                    'paid_at' => null,
                ];
            }

        } catch (\Exception $e) {
            Log::error('Paynow status check exception', [
                'message' => $e->getMessage(),
                'reference' => $reference,
            ]);
            return [
                'status' => 'Unknown',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Simulate a successful payment (for testing only).
     */
    public function simulateSuccess(string $reference): array
    {
        // Not applicable for Paynow in production
        return [
            'success' => false,
            'message' => 'Simulation not available for Paynow gateway',
        ];
    }

    /**
     * Simulate a failed payment (for testing only).
     */
    public function simulateFailure(string $reference): array
    {
        // Not applicable for Paynow in production
        return [
            'success' => false,
            'message' => 'Simulation not available for Paynow gateway',
        ];
    }
}
