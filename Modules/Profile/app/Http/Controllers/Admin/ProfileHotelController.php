<?php

namespace Modules\Profile\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Profile\Models\HotelPhoto;
use Modules\Profile\Models\ProfileHotel;

class ProfileHotelController extends Controller
{
    // ── INDEX / EDIT FORM ──────────────────────────────────
    public function index()
    {
        // Ambil satu baris profil hotel (singleton), buat jika belum ada
        $hotel = ProfileHotel::with('photos')->firstOrCreate([]);

        return view('profile::Admin.hotel', compact('hotel'));
    }

    // ── UPDATE INFO HOTEL ──────────────────────────────────
    public function update(Request $request)
    {
        $hotel = ProfileHotel::with('photos')->firstOrCreate([]);

        $request->validate([
            'name'        => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'address'     => 'nullable|string|max:500',
            'wa'          => 'nullable|string|max:20|regex:/^[0-9+\-\s]+$/',
            'email'       => 'nullable|email|max:255',
            'maps'        => 'nullable|string|max:1000',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'wa.regex'       => 'Format nomor WhatsApp tidak valid.',
            'email.email'    => 'Format email tidak valid.',
            'logo.image'     => 'Logo harus berupa gambar.',
            'logo.max'       => 'Ukuran logo maksimal 2MB.',
            'logo.mimes'     => 'Format logo harus JPG, PNG, atau WEBP.',
        ]);

        // ── Upload logo ───────────────────────────────────
        $logoPath = $hotel->logo;
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            // Hapus logo lama
            if ($logoPath && file_exists(public_path($logoPath))) {
                @unlink(public_path($logoPath));
            }

            $dir = public_path('assets/hotel');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $filename = 'logo_' . time() . '.jpg';
            $logoPath = 'assets/hotel/' . $filename;

            $this->saveImage(
                $request->file('logo')->getRealPath(),
                public_path($logoPath),
                $request->file('logo')->getMimeType(),
                400, 400, true   // crop persegi
            );
        }

        $hotel->update([
            'name'        => $request->name,
            'description' => $request->description,
            'address'     => $request->address,
            'wa'          => $request->wa,
            'email'       => $request->email,
            'maps'        => $request->maps,
            'logo'        => $logoPath,
        ]);

        // ── Upload foto-foto hotel (multiple) ─────────────
        if ($request->hasFile('photos')) {
            $dir = public_path('assets/hotel/photos');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            foreach ($request->file('photos') as $file) {
                if (!$file->isValid()) {
                    continue;
                }

                // Validasi per file
                if ($file->getSize() > 2 * 1024 * 1024) {
                    continue; // sudah divalidasi di JS, skip saja
                }

                $filename  = 'photo_' . time() . '_' . uniqid() . '.jpg';
                $photoPath = 'assets/hotel/photos/' . $filename;

                $this->saveImage(
                    $file->getRealPath(),
                    public_path($photoPath),
                    $file->getMimeType(),
                    1200, 800, false  // tidak crop, resize proporsional
                );

                HotelPhoto::create([
                    'profile_hotel_id' => $hotel->id,
                    'photo'            => $photoPath,
                ]);
            }
        }

        return redirect()
            ->route('admin.hotel.index')
            ->with('success', 'Profil hotel berhasil disimpan.');
    }

    // ── DELETE FOTO HOTEL ──────────────────────────────────
    public function deletePhoto(HotelPhoto $photo)
    {
        // Hapus file fisik
        if ($photo->photo && file_exists(public_path($photo->photo))) {
            @unlink(public_path($photo->photo));
        }

        $photo->delete();

        return redirect()
            ->route('admin.hotel.index')
            ->with('success', 'Foto berhasil dihapus.');
    }

    // ── PRIVATE: Simpan/resize gambar dengan GD ────────────
    private function saveImage(
        string $src,
        string $dest,
        string $mime,
        int $maxW,
        int $maxH,
        bool $crop = false
    ): void {
        $img = match (true) {
            str_contains($mime, 'jpeg'), str_contains($mime, 'jpg') => @imagecreatefromjpeg($src),
            str_contains($mime, 'png')  => @imagecreatefrompng($src),
            str_contains($mime, 'webp') => @imagecreatefromwebp($src),
            default                     => null,
        };

        if (!$img) {
            copy($src, $dest);
            return;
        }

        $srcW = imagesx($img);
        $srcH = imagesy($img);

        if ($crop) {
            // Crop center square
            $size  = min($srcW, $srcH);
            $srcX  = (int)(($srcW - $size) / 2);
            $srcY  = (int)(($srcH - $size) / 2);
            $thumb = imagecreatetruecolor($maxW, $maxH);
            imagecopyresampled($thumb, $img, 0, 0, $srcX, $srcY, $maxW, $maxH, $size, $size);
        } else {
            // Resize proporsional
            $ratio = min($maxW / $srcW, $maxH / $srcH, 1.0);
            $newW  = (int)($srcW * $ratio);
            $newH  = (int)($srcH * $ratio);
            $thumb = imagecreatetruecolor($newW, $newH);

            // Isi background putih untuk PNG transparan
            $white = imagecolorallocate($thumb, 255, 255, 255);
            imagefill($thumb, 0, 0, $white);

            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);
        }

        imagejpeg($thumb, $dest, 85);
        imagedestroy($img);
        imagedestroy($thumb);
    }
}
