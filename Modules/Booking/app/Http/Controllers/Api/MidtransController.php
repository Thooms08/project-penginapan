<?php

namespace Modules\Booking\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use Midtrans\Config as MidtransConfig;

class MidtransController extends Controller
{
    public function __construct()
    {
        MidtransConfig::$serverKey    = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = config('services.midtrans.is_production', false);
        MidtransConfig::$isSanitized  = config('services.midtrans.is_sanitized', true);
        MidtransConfig::$is3ds        = config('services.midtrans.is_3ds', true);
    }

    /**
     * Handle Midtrans webhook notification.
     * POST /api/midtrans/callback
     *
     * Called by Midtrans server — no CSRF, no auth.
     */
    public function callback(Request $request)
    {
        // Parse payload — Midtrans sends JSON body
        $payload = $request->all();

        // Fallback: parse raw JSON body if $request->all() is empty
        if (empty($payload)) {
            $raw     = $request->getContent();
            $payload = json_decode($raw, true) ?? [];
        }

        $orderId           = $payload['order_id']           ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus       = $payload['fraud_status']       ?? null;
        $paymentType       = $payload['payment_type']       ?? null;
        $transactionId     = $payload['transaction_id']     ?? null;
        $grossAmount       = $payload['gross_amount']       ?? null;
        $signatureKey      = $payload['signature_key']      ?? null;

        Log::info('Midtrans callback received', [
            'order_id'  => $orderId,
            'status'    => $transactionStatus,
            'payment'   => $paymentType,
            'payload'   => $payload,
        ]);

        if (!$orderId || !$transactionStatus) {
            Log::warning('Midtrans callback: missing required fields', $payload);
            return response()->json(['message' => 'Bad payload'], 400);
        }

        // ── Verify signature ──────────────────────────────────────
        $serverKey        = config('services.midtrans.server_key');
        $expectedSignature = hash('sha512',
            $orderId . $payload['status_code'] . $grossAmount . $serverKey
        );

        if ($signatureKey && $signatureKey !== $expectedSignature) {
            Log::warning('Midtrans callback: invalid signature', ['order_id' => $orderId]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // ── Find booking ──────────────────────────────────────────
        $booking = Booking::where('midtrans_order_id', $orderId)->first();

        if (!$booking) {
            Log::warning('Midtrans callback: booking not found', ['order_id' => $orderId]);
            // Return 200 to stop Midtrans from retrying with same invalid order_id
            return response()->json(['message' => 'Booking not found, acknowledged'], 200);
        }

        // ── Save raw payload ──────────────────────────────────────
        $booking->midtrans_transaction_id = $transactionId;
        $booking->midtrans_payment_type   = $paymentType;
        $booking->midtrans_raw            = $payload;

        // VA number
        $vaNumbers = $payload['va_numbers'] ?? [];
        if (!empty($vaNumbers) && is_array($vaNumbers)) {
            $va = $vaNumbers[0];
            $booking->midtrans_va_number = ($va['bank'] ?? '') . ' - ' . ($va['va_number'] ?? '');
        }

        // ── Map transaction status ────────────────────────────────
        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'challenge') {
                $booking->payment_status = Booking::PAY_PENDING;
                $booking->booking_status = Booking::STATUS_WAITING;
            } else {
                // 'accept' or no fraud check
                $booking->payment_status = Booking::PAY_PAID;
                $booking->booking_status = Booking::STATUS_CONFIRMED;
            }
        } elseif ($transactionStatus === 'settlement') {
            $booking->payment_status = Booking::PAY_PAID;
            $booking->booking_status = Booking::STATUS_CONFIRMED;
        } elseif ($transactionStatus === 'pending') {
            $booking->payment_status = Booking::PAY_PENDING;
            $booking->booking_status = Booking::STATUS_WAITING;
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'failure'])) {
            $booking->payment_status = Booking::PAY_FAILED;
            $booking->booking_status = Booking::STATUS_FAILED;
        } elseif ($transactionStatus === 'expire') {
            $booking->payment_status = Booking::PAY_EXPIRED;
            $booking->booking_status = Booking::STATUS_CANCELLED;
        }

        $booking->save();

        Log::info('Midtrans callback processed', [
            'order_id'   => $orderId,
            'status'     => $transactionStatus,
            'booking_id' => $booking->id,
            'pay_status' => $booking->payment_status,
        ]);

        return response()->json(['message' => 'OK']);
    }
}
