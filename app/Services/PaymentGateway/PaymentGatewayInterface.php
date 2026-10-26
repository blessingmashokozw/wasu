<?php

namespace App\Services\PaymentGateway;

interface PaymentGatewayInterface
{
    /**
     * Initiate a payment for an order.
     *
     * @param array $data Order data including amount, order_id, etc.
     * @return array Payment initiation response with redirect URL, payment reference, etc.
     */
    public function initiate(array $data): array;

    /**
     * Verify the payment callback signature.
     *
     * @param array $data Callback data
     * @param string $signature Signature from the payment provider
     * @return bool True if signature is valid
     */
    public function verifySignature(array $data, string $signature): bool;

    /**
     * Process the payment callback.
     *
     * @param array $data Callback data
     * @return array Payment status and details
     */
    public function processCallback(array $data): array;

    /**
     * Get the payment status for a transaction.
     *
     * @param string $reference Transaction reference
     * @return array Payment status
     */
    public function getStatus(string $reference): array;

    /**
     * Simulate a successful payment (for testing only).
     *
     * @param string $reference Transaction reference
     * @return array Simulated success response
     */
    public function simulateSuccess(string $reference): array;

    /**
     * Simulate a failed payment (for testing only).
     *
     * @param string $reference Transaction reference
     * @return array Simulated failure response
     */
    public function simulateFailure(string $reference): array;
}
