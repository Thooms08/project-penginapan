<?php

namespace Modules\Booking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Carbon\Carbon;

class SurchargeSetting extends Model
{
    use HasFactory;

    protected $table = 'surcharge_settings';

    protected $fillable = [
        'type',
        'threshold_time',
        'fee_type',
        'fee_amount',
        'label',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'fee_amount' => 'integer',
    ];

    // ── Constants ─────────────────────────────────────────

    const TYPE_EARLY_CHECKIN  = 'early_checkin';
    const TYPE_LATE_CHECKOUT  = 'late_checkout';

    const FEE_FIXED   = 'fixed';
    const FEE_PERCENT = 'percent';

    // ── Relationships ─────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeEarlyCheckin($query)
    {
        return $query->where('type', self::TYPE_EARLY_CHECKIN);
    }

    public function scopeLateCheckout($query)
    {
        return $query->where('type', self::TYPE_LATE_CHECKOUT);
    }

    // ── Static Helpers ────────────────────────────────────

    /**
     * Semua aturan early check-in yang aktif, urut dari threshold paling awal.
     */
    public static function activeEarlyCheckins()
    {
        return static::active()->earlyCheckin()->orderBy('threshold_time')->get();
    }

    /**
     * Semua aturan late check-out yang aktif, urut dari threshold paling akhir.
     */
    public static function activeLateCheckouts()
    {
        return static::active()->lateCheckout()->orderBy('threshold_time')->get();
    }

    /**
     * Semua aturan (aktif maupun tidak), dipakai di halaman admin.
     */
    public static function allForAdmin()
    {
        return static::orderByRaw("FIELD(type, 'early_checkin', 'late_checkout')")
            ->orderBy('threshold_time')
            ->get();
    }

    // ── Accessors ─────────────────────────────────────────

    /**
     * Format threshold_time sebagai H:i (contoh: 10:00).
     */
    public function getFormattedThresholdAttribute(): string
    {
        return $this->threshold_time
            ? Carbon::createFromFormat('H:i:s', $this->threshold_time)->format('H:i')
            : '--:--';
    }

    /**
     * Label tipe biaya untuk tampilan (Nominal / Persen).
     */
    public function getFeeTypeLabelAttribute(): string
    {
        return $this->fee_type === self::FEE_PERCENT ? 'Persen (%)' : 'Nominal (Rp)';
    }

    /**
     * Format nilai biaya: "Rp 50.000" atau "25%".
     */
    public function getFormattedFeeAttribute(): string
    {
        if ($this->fee_type === self::FEE_PERCENT) {
            return $this->fee_amount . '%';
        }

        return 'Rp ' . number_format($this->fee_amount, 0, ',', '.');
    }

    /**
     * Label tipe surcharge dalam Bahasa Indonesia.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_EARLY_CHECKIN => 'Early Check-In',
            self::TYPE_LATE_CHECKOUT => 'Late Check-Out',
            default                  => $this->type,
        };
    }

    /**
     * Deskripsi singkat otomatis jika label kosong.
     */
    public function getAutoLabelAttribute(): string
    {
        if ($this->label) {
            return $this->label;
        }

        $time = $this->formatted_threshold;

        return match ($this->type) {
            self::TYPE_EARLY_CHECKIN => "Early Check-In (sebelum {$time})",
            self::TYPE_LATE_CHECKOUT => "Late Check-Out (setelah {$time})",
            default                  => $this->type,
        };
    }
}
