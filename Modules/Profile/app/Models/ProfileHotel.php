<?php

namespace Modules\Profile\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfileHotel extends Model
{
    use HasFactory;

    protected $table = 'profile_hotels';

    protected $fillable = [
        'logo',
        'name',
        'name_en',
        'description',
        'description_en',
        'wa',
        'email',
        'address',
        'address_en',
        'maps',
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

    /**
     * Relasi ke foto-foto hotel.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(HotelPhoto::class, 'profile_hotel_id');
    }
}
