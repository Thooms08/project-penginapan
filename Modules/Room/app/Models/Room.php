<?php

namespace Modules\Room\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Room extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'name', 'name_en', 'capacity', 'price', 'description', 'description_en', 'status',
        'discount_type', 'discount_value', 'discount_min_nights',
    ];

    /**
     * Kembalikan nilai field sesuai locale aktif.
     * Jika versi EN kosong, fallback ke versi ID.
     */
    public function trans(string $field): ?string
    {
        if (app()->getLocale() === 'en') {
            $enValue = $this->getAttribute($field . '_en');
            if (!empty($enValue)) {
                return $enValue;
            }
        }
        return $this->getAttribute($field);
    }

    protected $casts = [
        'price'                => 'decimal:2',
        'capacity'             => 'integer',
        'discount_value'       => 'decimal:2',
        'discount_min_nights'  => 'integer',
    ];

    // ── Auto-generate UUID ────────────────────────
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

    // ── Accessor harga ────────────────────────────
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'available';
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_type !== 'none' && $this->discount_value > 0;
    }

    /**
     * Hitung harga setelah diskon untuk N malam.
     * Jika malam < discount_min_nights, diskon tidak berlaku.
     */
    public function getPriceAfterDiscount(int $nights = 1): float
    {
        if (!$this->has_discount) {
            return (float) $this->price;
        }

        if ($this->discount_min_nights > 0 && $nights < $this->discount_min_nights) {
            return (float) $this->price;
        }

        if ($this->discount_type === 'percentage') {
            $discount = $this->price * ($this->discount_value / 100);
            return max(0, (float) ($this->price - $discount));
        }

        // fixed
        return max(0, (float) ($this->price - $this->discount_value));
    }

    public function getFormattedDiscountAttribute(): string
    {
        if (!$this->has_discount) return '';

        if ($this->discount_type === 'percentage') {
            return number_format($this->discount_value, 0) . '%';
        }

        return 'Rp ' . number_format($this->discount_value, 0, ',', '.');
    }
}
