<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Other extends Model
{
    protected $table = 'others';

    protected $fillable = [
        'about',
        'about_en',
        'privacy_policy',
        'privacy_policy_en',
        'terms_conditions',
        'terms_conditions_en',
        'updated_by',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Helper Methods ───────────────────────────────────

    /**
     * Get the singleton instance (always ID 1).
     * Creates the record if it doesn't exist yet.
     */
    public static function getInstance(): static
    {
        return static::firstOrCreate(['id' => 1], [
            'about'            => null,
            'privacy_policy'   => null,
            'terms_conditions' => null,
        ]);
    }
}
