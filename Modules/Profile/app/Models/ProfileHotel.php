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
        'description',
        'wa',
        'email',
        'address',
        'maps',
    ];

    /**
     * Relasi ke foto-foto hotel.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(HotelPhoto::class, 'profile_hotel_id');
    }
}
