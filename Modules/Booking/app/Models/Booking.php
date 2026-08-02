<?php

namespace Modules\Booking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Modules\Room\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'booking_code',
        'user_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'nights',
        'price_per_night',
        'subtotal',
        'tax_amount',
        'total_amount',
        'payment_type',
        'amount_paid',
        'amount_remaining',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'midtrans_payment_type',
        'midtrans_va_number',
        'midtrans_raw',
        'payment_status',
        'booking_status',
        'checked_in_at',
        'checked_out_at',
        'guest_note',
    ];

    protected $casts = [
        'check_in_date'   => 'date',
        'check_out_date'  => 'date',
        'nights'          => 'integer',
        'price_per_night' => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'total_amount'    => 'decimal:2',
        'amount_paid'     => 'decimal:2',
        'amount_remaining'=> 'decimal:2',
        'midtrans_raw'    => 'array',
        'checked_in_at'   => 'datetime',
        'checked_out_at'  => 'datetime',
    ];

    // ── Constants ─────────────────────────────────────────

    const TAX_RATE = 0.10; // 10%
    const DP_RATE  = 0.50; // 50%

    // Payment type
    const PAYMENT_DP   = 'dp';
    const PAYMENT_FULL = 'full';

    // Payment status
    const PAY_PENDING   = 'pending';
    const PAY_PAID      = 'paid';
    const PAY_FAILED    = 'failed';
    const PAY_EXPIRED   = 'expired';
    const PAY_CANCELLED = 'cancelled';

    // Booking status
    const STATUS_WAITING  = 'waiting_payment';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CHECKED_IN  = 'checked_in';
    const STATUS_CHECKED_OUT = 'checked_out';
    const STATUS_CANCELLED   = 'cancelled';
    const STATUS_FAILED      = 'failed';

    // ── Auto-generate booking code ─────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = static::generateCode();
            }
            if (empty($booking->midtrans_order_id)) {
                $booking->midtrans_order_id = $booking->booking_code;
            }
        });
    }

    /**
     * Generate a unique booking code: BK-YYYYMMDD-XXXX
     */
    public static function generateCode(): string
    {
        do {
            $code = 'BK-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        } while (static::where('booking_code', $code)->exists());

        return $code;
    }

    // ── Relationships ─────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    // ── Static: Price Calculator ──────────────────────────

    /**
     * Calculate pricing breakdown for given room price, nights, and payment type.
     *
     * @return array{
     *   price_per_night: float,
     *   nights: int,
     *   subtotal: float,
     *   tax_amount: float,
     *   total_amount: float,
     *   payment_type: string,
     *   amount_paid: float,
     *   amount_remaining: float
     * }
     */
    public static function calculatePrice(float $pricePerNight, int $nights, string $paymentType): array
    {
        $subtotal    = $pricePerNight * $nights;
        $tax         = round($subtotal * self::TAX_RATE, 2);
        $total       = $subtotal + $tax;

        if ($paymentType === self::PAYMENT_DP) {
            $amountPaid      = round($total * self::DP_RATE, 2);
            $amountRemaining = $total - $amountPaid;
        } else {
            $amountPaid      = $total;
            $amountRemaining = 0;
        }

        return [
            'price_per_night'  => $pricePerNight,
            'nights'           => $nights,
            'subtotal'         => $subtotal,
            'tax_amount'       => $tax,
            'total_amount'     => $total,
            'payment_type'     => $paymentType,
            'amount_paid'      => $amountPaid,
            'amount_remaining' => $amountRemaining,
        ];
    }

    // ── Static: Availability Check ────────────────────────

    /**
     * Check if a room is available for the given date range.
     *
     * Logic:
     *  - A room is blocked if there is an existing CONFIRMED or WAITING booking
     *    whose [check_in_date, check_out_date) overlaps with [requestedCheckIn, requestedCheckOut).
     *  - Overlap condition: existing.check_in < requested.check_out
     *                   AND existing.check_out > requested.check_in
     *
     * @param int    $roomId
     * @param string $checkIn   Y-m-d
     * @param string $checkOut  Y-m-d
     * @param int|null $excludeBookingId  Skip this booking (for edit scenarios)
     */
    public static function isRoomAvailable(int $roomId, string $checkIn, string $checkOut, ?int $excludeBookingId = null): bool
    {
        $query = static::where('room_id', $roomId)
            ->whereIn('booking_status', [self::STATUS_WAITING, self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN])
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn);

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->doesntExist();
    }

    /**
     * Get the latest checkout date that blocks a new booking starting from $checkIn.
     * Used to limit the maximum check-out date a visitor can pick.
     *
     * Returns null if no upcoming booking exists.
     */
    public static function getNextBlockedCheckIn(int $roomId, string $checkIn): ?string
    {
        $next = static::where('room_id', $roomId)
            ->whereIn('booking_status', [self::STATUS_WAITING, self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN])
            ->where('check_in_date', '>=', $checkIn)
            ->orderBy('check_in_date')
            ->value('check_in_date');

        return $next ? Carbon::parse($next)->format('Y-m-d') : null;
    }

    // ── Accessors ─────────────────────────────────────────

    public function getFormattedCheckedInAtAttribute(): string
    {
        return $this->checked_in_at
            ? $this->checked_in_at->locale('id')->isoFormat('D MMM YYYY, HH:mm')
            : '-';
    }

    public function getFormattedCheckedOutAtAttribute(): string
    {
        return $this->checked_out_at
            ? $this->checked_out_at->locale('id')->isoFormat('D MMM YYYY, HH:mm')
            : '-';
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedAmountPaidAttribute(): string
    {
        return 'Rp ' . number_format($this->amount_paid, 0, ',', '.');
    }

    public function getFormattedAmountRemainingAttribute(): string
    {
        return 'Rp ' . number_format($this->amount_remaining, 0, ',', '.');
    }

    public function getFormattedCheckInAttribute(): string
    {
        return $this->check_in_date
            ? Carbon::parse($this->check_in_date)->locale('id')->isoFormat('D MMMM YYYY')
            : '-';
    }

    public function getFormattedCheckOutAttribute(): string
    {
        return $this->check_out_date
            ? Carbon::parse($this->check_out_date)->locale('id')->isoFormat('D MMMM YYYY')
            : '-';
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return match ($this->payment_type) {
            self::PAYMENT_DP   => 'Down Payment (50%)',
            self::PAYMENT_FULL => 'Bayar Lunas',
            default            => $this->payment_type,
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAY_PENDING   => 'Menunggu Pembayaran',
            self::PAY_PAID      => 'Lunas',
            self::PAY_FAILED    => 'Gagal',
            self::PAY_EXPIRED   => 'Kedaluwarsa',
            self::PAY_CANCELLED => 'Dibatalkan',
            default             => $this->payment_status,
        };
    }

    public function getBookingStatusLabelAttribute(): string
    {
        return match ($this->booking_status) {
            self::STATUS_WAITING    => 'Menunggu Pembayaran',
            self::STATUS_CONFIRMED  => 'Terkonfirmasi',
            self::STATUS_CHECKED_IN => 'Sedang Menginap',
            self::STATUS_CHECKED_OUT=> 'Selesai',
            self::STATUS_CANCELLED  => 'Dibatalkan',
            self::STATUS_FAILED     => 'Gagal',
            default                 => $this->booking_status,
        };
    }

    public function getIsSuccessAttribute(): bool
    {
        return in_array($this->booking_status, [
            self::STATUS_CONFIRMED,
            self::STATUS_CHECKED_IN,
            self::STATUS_CHECKED_OUT,
        ]) && $this->payment_status === self::PAY_PAID;
    }

    public function getIsFailedAttribute(): bool
    {
        return in_array($this->payment_status, [
            self::PAY_FAILED,
            self::PAY_EXPIRED,
            self::PAY_CANCELLED,
        ]) || in_array($this->booking_status, [
            self::STATUS_CANCELLED,
            self::STATUS_FAILED,
        ]);
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeSuccess($query)
    {
        return $query->whereIn('booking_status', [
            self::STATUS_CONFIRMED,
            self::STATUS_CHECKED_IN,
            self::STATUS_CHECKED_OUT,
        ])->where('payment_status', self::PAY_PAID)
          ->where('payment_type', self::PAYMENT_FULL); // full-pay only, DP confirmed is "pending"
    }

    /**
     * Pending = DP sudah dibayar (payment_status=paid, payment_type=dp),
     * kamar terkonfirmasi tapi masih ada sisa yang harus dilunasi saat check-in.
     */
    public function scopePending($query)
    {
        return $query->where('payment_type', self::PAYMENT_DP)
                     ->where('payment_status', self::PAY_PAID)
                     ->whereIn('booking_status', [
                         self::STATUS_CONFIRMED,
                         self::STATUS_CHECKED_IN,
                     ]);
    }

    public function scopeFailed($query)
    {
        return $query->where(function ($q) {
            $q->whereIn('payment_status', [self::PAY_FAILED, self::PAY_EXPIRED, self::PAY_CANCELLED])
              ->orWhereIn('booking_status', [self::STATUS_CANCELLED, self::STATUS_FAILED]);
        });
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
