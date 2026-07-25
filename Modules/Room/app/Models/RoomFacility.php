<?php

namespace Modules\Room\Models;

use Illuminate\Database\Eloquent\Model;

class RoomFacility extends Model
{
    protected $fillable = ['room_id', 'name', 'qty', 'description'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
