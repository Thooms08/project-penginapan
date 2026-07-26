<?php

namespace Modules\Profile\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelPhoto extends Model
{
    use HasFactory;

    protected $table = 'hotel_photos';

    protected $fillable = [
        'profile_hotel_id',
        'photo',
    ];

    /**
     * Relasi balik ke ProfileHotel.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(ProfileHotel::class, 'profile_hotel_id');
    }
}
