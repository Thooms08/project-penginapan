<?php

namespace Modules\Profile\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Illuminate\Support\Str;

class ProfileUser extends Model
{
    /**
     * Nama tabel eksplisit.
     */
    protected $table = 'profile_users';

    protected $fillable = [
        'user_id',
        'uuid',
        'foto',
        'wa',
        'city',
        'province',
        'country',
    ];

    /**
     * Boot: generate UUID otomatis saat create.
     */
    protected static function booted(): void
    {
        static::creating(function (ProfileUser $profile) {
            if (empty($profile->uuid)) {
                $profile->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relasi ke User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * URL foto profile — jika null kembalikan null.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto) return null;
        return asset($this->foto);
    }
}
