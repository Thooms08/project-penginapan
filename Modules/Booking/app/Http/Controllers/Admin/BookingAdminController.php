<?php

namespace Modules\Booking\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Booking\Models\Booking;
use Carbon\Carbon;

class BookingAdminController extends Controller
{
    /**
     * GET /admin/bookings
     * Halaman utama daftar booking visitor (pending + confirmed).
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'pending');

        // Pending = DP sudah bayar, sisa belum lunas saat check-in
        $pendingBookings = Booking::with(['user', 'room.coverPhoto'])
            ->pending()
            ->orderBy('check_in_date')
            ->paginate(15, ['*'], 'pending_page');

        // Confirmed = bayar lunas via Midtrans, sudah terkonfirmasi
        $confirmedBookings = Booking::with(['user', 'room.coverPhoto'])
            ->success()
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'confirmed_page');

        return view('booking::Admin.booking', compact(
            'pendingBookings',
            'confirmedBookings',
            'tab'
        ));
    }

    /**
     * POST /admin/bookings/{id}/confirm
     * Konfirmasi booking:
     *  - full-pay  → langsung set checked_in
     *  - DP        → terima cash_received, validasi cukup, set checked_in
     */
    public function confirm(Request $request, int $id)
    {
        $booking = Booking::with(['user', 'room'])->findOrFail($id);

        // Hanya booking confirmed (full-pay) atau pending (DP) yang boleh dikonfirmasi
        $allowedStatuses = [Booking::STATUS_CONFIRMED, Booking::STATUS_WAITING];
        if (!in_array($booking->booking_status, $allowedStatuses)
            && !($booking->payment_type === Booking::PAYMENT_DP
                 && $booking->payment_status === Booking::PAY_PAID)) {
            return response()->json([
                'success' => false,
                'message' => 'Status booking tidak memungkinkan konfirmasi.',
            ], 422);
        }

        if ($booking->payment_type === Booking::PAYMENT_FULL) {
            // Bayar lunas via Midtrans — langsung set checked_in
            $booking->update([
                'booking_status' => Booking::STATUS_CHECKED_IN,
                'checked_in_at'  => now(),
            ]);

            return response()->json([
                'success'      => true,
                'message'      => 'Tamu berhasil di-check-in!',
                'booking_code' => $booking->booking_code,
                'guest_name'   => $booking->user->name,
            ]);
        }

        // DP — validasi cash_received
        $request->validate([
            'cash_received' => ['required', 'numeric', 'min:0'],
        ], [
            'cash_received.required' => 'Nominal cash wajib diisi.',
            'cash_received.numeric'  => 'Nominal harus berupa angka.',
            'cash_received.min'      => 'Nominal tidak boleh negatif.',
        ]);

        $cashReceived    = (float) $request->cash_received;
        $amountRemaining = (float) $booking->amount_remaining;

        if ($cashReceived < $amountRemaining) {
            return response()->json([
                'success'   => false,
                'message'   => 'Pembayaran kurang. Sisa yang harus dibayar: Rp ' .
                               number_format($amountRemaining, 0, ',', '.'),
                'shortage'  => $amountRemaining - $cashReceived,
            ], 422);
        }

        $change = $cashReceived - $amountRemaining;

        $booking->update([
            'booking_status'   => Booking::STATUS_CHECKED_IN,
            'checked_in_at'    => now(),
            'payment_status'   => Booking::PAY_PAID,
            'amount_remaining' => 0,
        ]);

        return response()->json([
            'success'      => true,
            'message'      => 'Tamu berhasil di-check-in!',
            'booking_code' => $booking->booking_code,
            'guest_name'   => $booking->user->name,
            'change'       => $change,
            'change_fmt'   => 'Rp ' . number_format($change, 0, ',', '.'),
        ]);
    }

    /**
     * GET /admin/bookings/{id}/detail  (JSON)
     * Kembalikan detail booking untuk modal.
     */
    public function detail(int $id)
    {
        $booking = Booking::with(['user', 'room'])->findOrFail($id);

        return response()->json([
            'booking_code'       => $booking->booking_code,
            'guest_name'         => $booking->user->name,
            'guest_email'        => $booking->user->email,
            'room_name'          => $booking->room->name,
            'check_in'           => $booking->formatted_check_in,
            'check_out'          => $booking->formatted_check_out,
            'nights'             => $booking->nights,
            'payment_type'       => $booking->payment_type,
            'payment_type_label' => $booking->payment_type_label,
            'payment_status'     => $booking->payment_status,
            'payment_status_label' => $booking->payment_status_label,
            'booking_status'     => $booking->booking_status,
            'booking_status_label' => $booking->booking_status_label,
            'total_amount'       => $booking->formatted_total,
            'amount_paid'        => $booking->formatted_amount_paid,
            'amount_remaining'   => $booking->formatted_amount_remaining,
            'midtrans_payment_type' => $booking->midtrans_payment_type ?? '-',
            'guest_note'         => $booking->guest_note ?? '-',
            'created_at'         => $booking->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm'),
        ]);
    }
}
