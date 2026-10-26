<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\Payment;
use App\Services\AuditLogger;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    protected PaymentGatewayService $paymentService;

    public function __construct(PaymentGatewayService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle payment callback from payment gateway.
     * This endpoint must be idempotent.
     */
    public function callback(Request $request): Response
    {
        $data = $request->all();
        $signature = $request->header('X-Payment-Signature') ?? $request->input('signature');

        // Verify signature
        if (!$this->paymentService->verifySignature($data, $signature)) {
            Log::warning('Invalid payment signature', ['data' => $data]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $reference = $data['reference'] ?? null;
        if (!$reference) {
            return response()->json(['error' => 'Missing reference'], 400);
        }

        // Find the payment record
        $payment = Payment::where('transaction_ref', $reference)->first();
        if (!$payment) {
            Log::warning('Payment not found for reference', ['reference' => $reference]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // If already processed, return success (idempotent)
        if ($payment->payment_status === 'Successful') {
            return response()->json(['message' => 'Payment already processed']);
        }

        // Process callback
        $result = $this->paymentService->processCallback($data);

        if (!$result['success']) {
            // Payment failed
            $payment->update([
                'payment_status' => 'Failed',
            ]);

            AuditLogger::logPayment($payment->order->buyer_id, 'failed');

            return response()->json(['message' => 'Payment failed']);
        }

        // Payment successful - process in transaction
        DB::transaction(function () use ($payment, $result) {
            $order = $payment->order;

            // Verify order is still pending payment
            if ($order->order_status !== 'Pending payment') {
                Log::warning('Order not in pending payment status', [
                    'order_id' => $order->order_id,
                    'status' => $order->order_status,
                ]);
                return;
            }

            // Verify amount matches
            if (abs($result['amount'] - $order->total_amount) > 0.01) {
                Log::warning('Payment amount mismatch', [
                    'expected' => $order->total_amount,
                    'received' => $result['amount'],
                ]);
                return;
            }

            // Update payment
            $payment->update([
                'payment_status' => 'Successful',
                'paid_at' => now(),
            ]);

            // Update order
            $order->update([
                'order_status' => 'Paid',
            ]);

            // Deduct stock and update listing status
            foreach ($order->orderItems as $orderItem) {
                $listing = $orderItem->listing;
                $listing->decrement('quantity_available', $orderItem->quantity);

                if ($listing->quantity_available === 0) {
                    $listing->update(['listing_status' => 'Out of stock']);
                }
            }

            // Create delivery record
            Delivery::create([
                'order_id' => $order->order_id,
                'delivery_method' => 'Standard Delivery',
                'estimated_date' => now()->addDays(7),
                'current_status' => 'Processed',
            ]);

            AuditLogger::logPayment($order->buyer_id, 'successful');
        });

        return response()->json(['message' => 'Payment processed successfully']);
    }

    /**
     * Fake payment redirect for testing.
     */
    public function fakeRedirect(Request $request)
    {
        $reference = $request->query('reference');

        return view('payment.fake-redirect', [
            'reference' => $reference,
            'success_url' => route('payment.fake.success', ['reference' => $reference]),
            'fail_url' => route('payment.fake.fail', ['reference' => $reference]),
        ]);
    }

    /**
     * Simulate successful payment (for testing).
     */
    public function fakeSuccess(Request $request)
    {
        $reference = $request->query('reference');
        $result = $this->paymentService->simulateSuccess($reference);

        return redirect($result['callback_url']);
    }

    /**
     * Simulate failed payment (for testing).
     */
    public function fakeFail(Request $request)
    {
        $reference = $request->query('reference');
        $result = $this->paymentService->simulateFailure($reference);

        return redirect($result['callback_url']);
    }
}
