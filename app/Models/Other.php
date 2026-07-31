<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Other extends Model
{
    protected $table = 'others';

    protected $fillable = [
        'about',
        'privacy_policy',
        'terms_conditions',
        'updated_by',
    ];

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
