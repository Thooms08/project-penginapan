<?php

namespace Modules\Room\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Room extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'name', 'capacity', 'price', 'description', 'status',
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'capacity' => 'integer',
    ];

    // Auto-generate UUID saat create
    protected static function booted(): void
    {
        static::creating(function (Room $room) {
            if (empty($room->uuid)) {
                $room->uuid = (string) Str::uuid();
            }
        });
    }

    // ── Relasi ────────────────────────────────────
    public function facilities()
    {
        return $this->hasMany(RoomFacility::class);
    }

    public function photos()
    {
        return $this->hasMany(RoomPhoto::class)->orderBy('sort_order');
    }

    public function coverPhoto()
    {
        return $this->hasOne(RoomPhoto::class)->where('is_cover', true);
    }

    // ── Accessor ──────────────────────────────────
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'available';
    }
}
