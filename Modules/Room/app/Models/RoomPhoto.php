<?php

namespace Modules\Room\Models;

use Illuminate\Database\Eloquent\Model;

class RoomPhoto extends Model
{
    protected $fillable = ['room_id', 'path', 'original_name', 'is_cover', 'sort_order'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getUrlAttribute(): string
    {
        return asset($this->path);
    }
}
