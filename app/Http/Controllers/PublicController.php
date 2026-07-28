<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Modules\Room\Models\Room;
use Modules\Profile\Models\ProfileHotel;
use Modules\Booking\Models\CheckInOutSetting;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Halaman utama publik — daftar kamar + info hotel.
     */
    public function index(Request $request)
    {
        // ── Kalkulasi jumlah malam dari query string ──────────────────
        $checkIn  = $request->input('check_in');
        $checkOut = $request->input('check_out');
        $nights   = 1;

        if ($checkIn && $checkOut) {
            try {
                $diff   = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
                $nights = max(1, (int) $diff);
            } catch (\Throwable) {
                $nights = 1;
            }
        }

        // ── Ambil semua kamar dengan relasi yang dibutuhkan ───────────
        $rooms = Room::with(['coverPhoto', 'photos', 'facilities'])
            ->orderByRaw("CASE WHEN status = 'available' THEN 0 ELSE 1 END")
            ->orderBy('price')
            ->get()
            ->map(function (Room $room) use ($nights) {
                $room->_isAvailable  = $room->is_available;
                $room->_hasDiscount  = $room->has_discount;
                $room->_nights       = $nights;
                $room->_priceDisplay = $room->_hasDiscount
                    ? 'Rp ' . number_format($room->getPriceAfterDiscount($nights), 0, ',', '.')
                    : $room->formatted_price;

                return $room;
            });

        // ── Info hotel ────────────────────────────────────────────────
        $hotel       = ProfileHotel::with('photos')->first();
        $hotelPhotos = $hotel?->photos ?? collect();

        return view('index', compact(
            'rooms', 'hotel', 'hotelPhotos', 'nights', 'checkIn', 'checkOut',
        ));
    }

    /**
     * Halaman detail kamar publik.
     */
    public function show(Request $request, string $uuid)
    {
        // ── Kamar + semua relasi ──────────────────────────────────────
        $room = Room::with(['photos', 'coverPhoto', 'facilities'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // ── Tanggal dari query string (diteruskan dari halaman list) ──
        $checkIn  = $request->input('check_in');
        $checkOut = $request->input('check_out');
        $nights   = 1;

        if ($checkIn && $checkOut) {
            try {
                $diff   = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
                $nights = max(1, (int) $diff);
            } catch (\Throwable) {
                $nights = 1;
            }
        }

        // ── Harga yang ditampilkan (dengan/tanpa diskon) ──────────────
        $isAvailable  = $room->is_available;
        $hasDiscount  = $room->has_discount;
        $pricePerNight = (float) $room->price;

        // Harga setelah diskon (sesuai jumlah malam)
        $discountedPrice = $hasDiscount
            ? $room->getPriceAfterDiscount($nights)
            : $pricePerNight;

        $priceDisplay = 'Rp ' . number_format($discountedPrice, 0, ',', '.');
        $originalPriceDisplay = $room->formatted_price;

        // Penghematan per malam
        $savingPerNight = max(0, $pricePerNight - $discountedPrice);
        $totalSaving    = $savingPerNight * $nights;

        // ── Jadwal check-in & check-out dari setting admin ────────────
        // Ambil setting terdekat dari tanggal hari ini ke depan
        $today = Carbon::today();

        $checkInSettings = CheckInOutSetting::checkIn()
            ->where('date', '>=', $today->toDateString())
            ->orderBy('date')
            ->orderBy('time')
            ->limit(5)
            ->get();

        $checkOutSettings = CheckInOutSetting::checkOut()
            ->where('date', '>=', $today->toDateString())
            ->orderBy('date')
            ->orderBy('time')
            ->limit(5)
            ->get();

        // Waktu default jika belum ada setting
        $defaultCheckInTime  = '14:00';
        $defaultCheckOutTime = '12:00';

        // Kamar lain yang tersedia (rekomenasi, max 3, exclude kamar ini)
        $otherRooms = Room::with(['coverPhoto', 'facilities'])
            ->where('uuid', '!=', $uuid)
            ->where('status', 'available')
            ->orderBy('price')
            ->limit(3)
            ->get()
            ->map(function (Room $r) use ($nights) {
                $r->_isAvailable  = $r->is_available;
                $r->_hasDiscount  = $r->has_discount;
                $r->_nights       = $nights;
                $r->_priceDisplay = $r->_hasDiscount
                    ? 'Rp ' . number_format($r->getPriceAfterDiscount($nights), 0, ',', '.')
                    : $r->formatted_price;
                return $r;
            });

        // ── Info hotel (untuk layout) ─────────────────────────────────
        $hotel = ProfileHotel::first();

        return view('Visitors.room-show', compact(
            'room',
            'isAvailable',
            'hasDiscount',
            'priceDisplay',
            'originalPriceDisplay',
            'savingPerNight',
            'totalSaving',
            'discountedPrice',
            'nights',
            'checkIn',
            'checkOut',
            'checkInSettings',
            'checkOutSettings',
            'defaultCheckInTime',
            'defaultCheckOutTime',
            'otherRooms',
            'hotel',
        ));
    }
}

