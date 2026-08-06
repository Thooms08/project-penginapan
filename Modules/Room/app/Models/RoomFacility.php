<?php

namespace Modules\Room\Models;

use Illuminate\Database\Eloquent\Model;

class RoomFacility extends Model
{
    protected $fillable = ['room_id', 'name', 'name_en', 'qty', 'description', 'description_en'];

    /**
     * Kembalikan nama fasilitas sesuai locale aktif.
     * Jika versi EN kosong, fallback ke versi ID.
     */
    public function getTransName(): string
    {
        if (app()->getLocale() === 'en' && !empty($this->name_en)) {
            return $this->name_en;
        }
        return $this->name;
    }

    /**
     * Kembalikan deskripsi fasilitas sesuai locale aktif.
     * Jika versi EN kosong, fallback ke versi ID.
     */
    public function getTransDescription(): ?string
    {
        if (app()->getLocale() === 'en' && !empty($this->description_en)) {
            return $this->description_en;
        }
        return $this->description;
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
