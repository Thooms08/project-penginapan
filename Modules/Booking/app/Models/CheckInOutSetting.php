<?php

namespace Modules\Booking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Carbon\Carbon;

class CheckInOutSetting extends Model
{
    use HasFactory;

    protected $table = 'check_in_out_settings';

    protected $fillable = [
        'date',
        'type',
        'time',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // ── Constants ────────────────────────────────────────

    const TYPE_CHECK_IN  = 'check_in';
    const TYPE_CHECK_OUT = 'check_out';

    // ── Relationships ────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Scopes ───────────────────────────────────────────

    public function scopeCheckIn($query)
    {
        return $query->where('type', self::TYPE_CHECK_IN);
    }

    public function scopeCheckOut($query)
    {
        return $query->where('type', self::TYPE_CHECK_OUT);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForToday($query)
    {
        return $query->whereDate('date', Carbon::today());
    }

    // ── Static Helpers ───────────────────────────────────

    /**
     * Get all check-in times for today.
     */
    public static function todayCheckIns()
    {
        return static::checkIn()->forToday()->orderBy('time')->get();
    }

    /**
     * Get all check-out times for today.
     */
    public static function todayCheckOuts()
    {
        return static::checkOut()->forToday()->orderBy('time')->get();
    }

    // ── Accessors ────────────────────────────────────────

    /**
     * Format time as H:i (e.g. 14:00).
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->time
            ? Carbon::createFromFormat('H:i:s', $this->time)->format('H:i')
            : '--:--';
    }

    /**
     * Human-readable date (e.g. Senin, 27 Juli 2026).
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date
            ? Carbon::parse($this->date)->locale('id')->isoFormat('dddd, D MMMM YYYY')
            : '-';
    }

    /**
     * Short date label (e.g. 27 Jul 2026).
     */
    public function getShortDateAttribute(): string
    {
        return $this->date
            ? Carbon::parse($this->date)->locale('id')->isoFormat('D MMM YYYY')
            : '-';
    }
}
