<?php

namespace Modules\Booking\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use Modules\Room\Models\Room;
use Carbon\Carbon;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class BookingController extends Controller
{
    // ─────────────────────────────────────────────────────────
    //  Constructor: configure Midtrans SDK
    // ─────────────────────────────────────────────────────────

    public function __construct()
    {
        MidtransConfig::$serverKey    = config('services.midtrans.server_key');
        MidtransConfig::$clientKey    = config('services.midtrans.client_key');
        MidtransConfig::$isProduction = config('services.midtrans.is_production', false);
        MidtransConfig::$isSanitized  = config('services.midtrans.is_sanitized', true);
        MidtransConfig::$is3ds        = config('services.midtrans.is_3ds', true);
    }

    // ─────────────────────────────────────────────────────────
    //  VISITOR: Show booking form for a specific room
    //  GET /booking/room/{uuid}
    // ─────────────────────────────────────────────────────────

    public function create(Request $request, string $uuid)
    {
        $room = Room::with(['coverPhoto', 'facilities', 'photos'])
            ->where('uuid', $uuid)
            ->where('status', 'available')
            ->firstOrFail();

        // Pre-fill dates from query string (passed from room-show or card)
        $preCheckIn  = $request->query('check_in', '');
        $preCheckOut = $request->query('check_out', '');

        return view('booking::Visitor.booking', compact('room', 'preCheckIn', 'preCheckOut'));
    }

    // ─────────────────────────────────────────────────────────
    //  VISITOR: Store booking + create Midtrans Snap token
    //  POST /booking/room/{uuid}
    // ─────────────────────────────────────────────────────────

    public function store(Request $request, string $uuid)
    {
        $room = Room::where('uuid', $uuid)
            ->where('status', 'available')
            ->firstOrFail();

        // ── Validate input ──
        $validated = $request->validate([
            'check_in'       => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'check_out'      => ['required', 'date_format:Y-m-d', 'after:check_in'],
            'payment_type'   => ['required', 'in:dp,full'],
            'guest_note'     => ['nullable', 'string', 'max:500'],
        ], [
            'check_in.required'          => 'Tanggal check-in wajib diisi.',
            'check_in.after_or_equal'    => 'Tanggal check-in tidak boleh di masa lampau.',
            'check_out.required'         => 'Tanggal check-out wajib diisi.',
            'check_out.after'            => 'Tanggal check-out harus setelah check-in.',
            'payment_type.required'      => 'Metode pembayaran wajib dipilih.',
            'payment_type.in'            => 'Metode pembayaran tidak valid.',
        ]);

        $checkIn  = $validated['check_in'];
        $checkOut = $validated['check_out'];
        $nights   = (int) Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));

        if ($nights < 1) {
            return back()->withErrors(['check_out' => 'Minimum menginap 1 malam.'])->withInput();
        }

        // ── Check room availability ──
        if (!Booking::isRoomAvailable($room->id, $checkIn, $checkOut)) {
            return back()
                ->withErrors(['check_in' => 'Kamar tidak tersedia pada rentang tanggal tersebut. Silakan pilih tanggal lain.'])
                ->withInput();
        }

        // ── Calculate pricing ──
        $pricePerNight = (float) $room->getPriceAfterDiscount($nights);
        $pricing       = Booking::calculatePrice($pricePerNight, $nights, $validated['payment_type']);

        // ── Create booking record ──
        $booking = DB::transaction(function () use ($room, $pricing, $validated, $nights, $checkIn, $checkOut) {
            return Booking::create([
                'user_id'          => Auth::id(),
                'room_id'          => $room->id,
                'check_in_date'    => $checkIn,
                'check_out_date'   => $checkOut,
                'nights'           => $nights,
                'price_per_night'  => $pricing['price_per_night'],
                'subtotal'         => $pricing['subtotal'],
                'tax_amount'       => $pricing['tax_amount'],
                'total_amount'     => $pricing['total_amount'],
                'payment_type'     => $pricing['payment_type'],
                'amount_paid'      => $pricing['amount_paid'],
                'amount_remaining' => $pricing['amount_remaining'],
                'payment_status'   => Booking::PAY_PENDING,
                'booking_status'   => Booking::STATUS_WAITING,
                'guest_note'       => $validated['guest_note'] ?? null,
            ]);
        });

        // ── Create Midtrans Snap transaction ──
        try {
            $snapToken = $this->createSnapToken($booking);
            $booking->update(['midtrans_order_id' => $booking->booking_code]);

            return response()->json([
                'snap_token'   => $snapToken,
                'booking_code' => $booking->booking_code,
                'amount_paid'  => $booking->amount_paid,
                'payment_type' => $booking->payment_type,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap token error', [
                'booking_id' => $booking->id,
                'message'    => $e->getMessage(),
            ]);

            // Mark booking as failed if Snap token creation fails
            $booking->update([
                'payment_status' => Booking::PAY_FAILED,
                'booking_status' => Booking::STATUS_FAILED,
            ]);

            return response()->json([
                'error' => 'Gagal membuat sesi pembayaran. Silakan coba lagi.',
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────
    //  VISITOR: History page
    //  GET /booking/history
    // ─────────────────────────────────────────────────────────

    public function history(Request $request)
    {
        $userId = Auth::id();
        $tab    = $request->query('tab', 'pending'); // 'success' | 'pending' | 'failed'

        $successBookings = Booking::with(['room.coverPhoto'])
            ->forUser($userId)
            ->success()
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'success_page');

        $pendingBookings = Booking::with(['room.coverPhoto'])
            ->forUser($userId)
            ->pending()
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'pending_page');

        $failedBookings = Booking::with(['room.coverPhoto'])
            ->forUser($userId)
            ->failed()
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'failed_page');

        return view('booking::Visitor.history.index', compact(
            'successBookings',
            'pendingBookings',
            'failedBookings',
            'tab'
        ));
    }

    // ─────────────────────────────────────────────────────────
    //  VISITOR: Verify payment status from Midtrans (fallback)
    //  POST /booking/verify-payment
    // ─────────────────────────────────────────────────────────

    public function verifyPayment(Request $request)
    {
        $orderId = $request->input('order_id');
        if (!$orderId) return response()->json(['ok' => false]);

        $booking = Booking::where('midtrans_order_id', $orderId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$booking) return response()->json(['ok' => false]);

        // Already confirmed — nothing to do
        if ($booking->payment_status === Booking::PAY_PAID) {
            return response()->json(['ok' => true, 'status' => $booking->payment_status]);
        }

        try {
            $status = \Midtrans\Transaction::status($orderId);
            $txStatus   = $status->transaction_status ?? null;
            $fraudStatus = $status->fraud_status ?? null;

            if ($txStatus === 'settlement' || ($txStatus === 'capture' && $fraudStatus === 'accept')) {
                $booking->update([
                    'payment_status'           => Booking::PAY_PAID,
                    'booking_status'           => Booking::STATUS_CONFIRMED,
                    'midtrans_transaction_id'  => $status->transaction_id ?? null,
                    'midtrans_payment_type'    => $status->payment_type ?? null,
                ]);
            } elseif (in_array($txStatus, ['deny', 'cancel', 'failure'])) {
                $booking->update([
                    'payment_status' => Booking::PAY_FAILED,
                    'booking_status' => Booking::STATUS_FAILED,
                ]);
            } elseif ($txStatus === 'expire') {
                $booking->update([
                    'payment_status' => Booking::PAY_EXPIRED,
                    'booking_status' => Booking::STATUS_CANCELLED,
                ]);
            }

            return response()->json(['ok' => true, 'status' => $booking->fresh()->payment_status]);
        } catch (\Exception $e) {
            Log::warning('verifyPayment error', ['order_id' => $orderId, 'msg' => $e->getMessage()]);
            return response()->json(['ok' => false]);
        }
    }

    // ─────────────────────────────────────────────────────────
    //  VISITOR: Booking success redirect
    //  GET /booking/success
    // ─────────────────────────────────────────────────────────

    public function success(Request $request)
    {
        $orderId = $request->query('order_id');
        $tab     = 'success'; // default redirect tab

        if ($orderId) {
            $booking = Booking::where('midtrans_order_id', $orderId)
                ->where('user_id', Auth::id())
                ->first();

            if ($booking) {
                // DP yang berhasil → redirect ke tab pending (sisa bayar saat check-in)
                // Full pay yang berhasil → redirect ke tab success
                if ($booking->payment_type === Booking::PAYMENT_DP) {
                    $tab = 'pending';
                } else {
                    $tab = 'success';
                }
            }
        }

        return redirect()->route('booking.history', ['tab' => $tab]);
    }

    // ─────────────────────────────────────────────────────────
    //  AJAX: Check room availability for date range
    //  GET /booking/check-availability
    // ─────────────────────────────────────────────────────────

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_id'    => 'required|integer|exists:rooms,id',
            'check_in'   => 'required|date_format:Y-m-d',
            'check_out'  => 'required|date_format:Y-m-d|after:check_in',
        ]);

        $available   = Booking::isRoomAvailable(
            $request->room_id,
            $request->check_in,
            $request->check_out
        );
        $maxCheckOut = Booking::getNextBlockedCheckIn($request->room_id, $request->check_in);

        return response()->json([
            'available'    => $available,
            'max_checkout' => $maxCheckOut,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    //  AJAX: Get blocked dates for a room (for calendar disable)
    //  GET /booking/blocked-dates/{room_id}
    // ─────────────────────────────────────────────────────────

    public function blockedDates(int $roomId)
    {
        $bookings = Booking::where('room_id', $roomId)
            ->whereIn('booking_status', [
                Booking::STATUS_WAITING,
                Booking::STATUS_CONFIRMED,
                Booking::STATUS_CHECKED_IN,
            ])
            ->where('check_out_date', '>=', now()->format('Y-m-d'))
            ->get(['check_in_date', 'check_out_date']);

        // Build array of blocked ranges
        $ranges = $bookings->map(fn($b) => [
            'from' => $b->check_in_date->format('Y-m-d'),
            'to'   => $b->check_out_date->subDay()->format('Y-m-d'), // last occupied night
        ]);

        return response()->json(['blocked' => $ranges]);
    }

    // ─────────────────────────────────────────────────────────
    //  Private: Build Midtrans Snap token
    // ─────────────────────────────────────────────────────────

    private function createSnapToken(Booking $booking): string
    {
        $user = $booking->user;
        $room = $booking->room;

        $itemLabel = $booking->payment_type === Booking::PAYMENT_DP
            ? 'Down Payment (50%) - ' . $room->name
            : 'Pembayaran Lunas - ' . $room->name;

        $params = [
            'transaction_details' => [
                'order_id'     => $booking->booking_code,
                'gross_amount' => (int) round($booking->amount_paid),
            ],
            'item_details' => [
                [
                    'id'       => 'room-' . $room->id,
                    'price'    => (int) round($booking->amount_paid),
                    'quantity' => 1,
                    'name'     => mb_substr($itemLabel, 0, 50),
                ],
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone ?? '',
            ],
            'callbacks' => [
                'finish' => config('app.url') . '/booking/success',
            ],
        ];

        return Snap::getSnapToken($params);
    }
}
