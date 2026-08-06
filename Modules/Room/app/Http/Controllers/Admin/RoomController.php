<?php

namespace Modules\Room\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Room\Models\Room;
use Modules\Room\Services\ImageService;

class RoomController extends Controller
{
    // ── INDEX ──────────────────────────────────────────────────
    public function index()
    {
        $rooms = Room::with(['coverPhoto', 'facilities', 'photos'])
            ->latest()
            ->paginate(12);

        return view('room::Admin.index', compact('rooms'));
    }

    // ── CREATE ────────────────────────────────────────────────
    public function create()
    {
        return view('room::Admin.create');
    }

    // ── STORE ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'                            => 'required|string|max:255',
            'name_en'                         => 'nullable|string|max:255',
            'capacity'                        => 'required|integer|min:1|max:100',
            'price'                           => 'required|numeric|min:0',
            'status'                          => 'required|in:available,unavailable',
            'description'                     => 'nullable|string|max:5000',
            'description_en'                  => 'nullable|string|max:5000',
            'discount_type'                   => 'nullable|in:none,percentage,fixed',
            'discount_value'                  => 'nullable|numeric|min:0',
            'discount_min_nights'             => 'nullable|integer|min:0',
            'photos.*'                        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'facilities'                      => 'nullable|array',
            'facilities.*.name'               => 'required_with:facilities|string|max:100',
            'facilities.*.name_en'            => 'nullable|string|max:100',
            'facilities.*.qty'                => 'required_with:facilities|integer|min:1',
            'facilities.*.description'        => 'nullable|string|max:150',
            'facilities.*.description_en'     => 'nullable|string|max:150',
        ], [
            'name.required'       => 'Nama kamar wajib diisi.',
            'capacity.required'   => 'Kapasitas wajib diisi.',
            'price.required'      => 'Harga wajib diisi.',
            'photos.*.image'      => 'File harus berupa gambar.',
            'photos.*.max'        => 'Ukuran foto maksimal 8MB.',
            'discount_type.in'    => 'Tipe diskon tidak valid.',
            'discount_value.min'  => 'Nilai diskon tidak boleh negatif.',
        ]);

        DB::transaction(function () use ($request) {
            $room = Room::create([
                'name'                 => $request->name,
                'name_en'              => $request->name_en,
                'capacity'             => $request->capacity,
                'price'                => $request->price,
                'status'               => $request->status,
                'description'          => $request->description,
                'description_en'       => $request->description_en,
                'discount_type'        => $request->discount_type ?? 'none',
                'discount_value'       => $request->discount_value ?? 0,
                'discount_min_nights'  => $request->discount_min_nights ?? 0,
            ]);

            // Simpan fasilitas
            if ($request->filled('facilities')) {
                foreach ($request->facilities as $f) {
                    if (!empty($f['name'])) {
                        $room->facilities()->create([
                            'name'           => $f['name'],
                            'name_en'        => $f['name_en'] ?? null,
                            'qty'            => $f['qty'] ?? 1,
                            'description'    => $f['description'] ?? null,
                            'description_en' => $f['description_en'] ?? null,
                        ]);
                    }
                }
            }

            // Simpan & kompres foto
            $this->savePhotos($request, $room);
        });

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Kamar berhasil ditambahkan.');
    }

    // ── EDIT ──────────────────────────────────────────────────
    public function edit(string $uuid)
    {
        $room = Room::with(['facilities', 'photos'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return view('room::Admin.edit', compact('room'));
    }

    // ── UPDATE ────────────────────────────────────────────────
    public function update(Request $request, string $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'name'                            => 'required|string|max:255',
            'name_en'                         => 'nullable|string|max:255',
            'capacity'                        => 'required|integer|min:1|max:100',
            'price'                           => 'required|numeric|min:0',
            'status'                          => 'required|in:available,unavailable',
            'description'                     => 'nullable|string|max:5000',
            'description_en'                  => 'nullable|string|max:5000',
            'discount_type'                   => 'nullable|in:none,percentage,fixed',
            'discount_value'                  => 'nullable|numeric|min:0',
            'discount_min_nights'             => 'nullable|integer|min:0',
            'photos.*'                        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'facilities'                      => 'nullable|array',
            'facilities.*.name'               => 'required_with:facilities|string|max:100',
            'facilities.*.name_en'            => 'nullable|string|max:100',
            'facilities.*.qty'                => 'required_with:facilities|integer|min:1',
            'facilities.*.description'        => 'nullable|string|max:150',
            'facilities.*.description_en'     => 'nullable|string|max:150',
            'delete_photos'                   => 'nullable|array',
            'delete_photos.*'                 => 'integer|exists:room_photos,id',
        ]);

        DB::transaction(function () use ($request, $room) {
            $room->update([
                'name'                 => $request->name,
                'name_en'              => $request->name_en,
                'capacity'             => $request->capacity,
                'price'                => $request->price,
                'status'               => $request->status,
                'description'          => $request->description,
                'description_en'       => $request->description_en,
                'discount_type'        => $request->discount_type ?? 'none',
                'discount_value'       => $request->discount_value ?? 0,
                'discount_min_nights'  => $request->discount_min_nights ?? 0,
            ]);

            // Hapus foto yang ditandai
            if ($request->filled('delete_photos')) {
                $toDelete = $room->photos()->whereIn('id', $request->delete_photos)->get();
                foreach ($toDelete as $photo) {
                    $path = public_path($photo->path);
                    if (file_exists($path)) unlink($path);
                    $photo->delete();
                }
                // Pastikan selalu ada cover setelah penghapusan
                $this->ensureCover($room);
            }

            // Tambah foto baru
            $this->savePhotos($request, $room);

            // Sync fasilitas
            $room->facilities()->delete();
            if ($request->filled('facilities')) {
                foreach ($request->facilities as $f) {
                    if (!empty($f['name'])) {
                        $room->facilities()->create([
                            'name'           => $f['name'],
                            'name_en'        => $f['name_en'] ?? null,
                            'qty'            => $f['qty'] ?? 1,
                            'description'    => $f['description'] ?? null,
                            'description_en' => $f['description_en'] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Kamar berhasil diperbarui.');
    }

    // ── DESTROY ───────────────────────────────────────────────
    public function destroy(string $uuid)
    {
        $room = Room::with('photos')->where('uuid', $uuid)->firstOrFail();

        DB::transaction(function () use ($room) {
            foreach ($room->photos as $photo) {
                $path = public_path($photo->path);
                if (file_exists($path)) unlink($path);
            }
            $room->delete();
        });

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Kamar berhasil dihapus.');
    }

    // ── PRIVATE HELPERS ───────────────────────────────────────

    /**
     * Simpan foto yang diupload, kompres ke 200KB via ImageService.
     * Fix: set is_cover = true untuk foto pertama jika belum ada cover.
     */
    private function savePhotos(Request $request, Room $room): void
    {
        if (!$request->hasFile('photos')) return;

        $files = $request->file('photos');
        // Filter: hanya file yang valid
        $files = array_filter($files, fn($f) => $f && $f->isValid());

        if (empty($files)) return;

        $dir      = public_path('assets/rooms');
        $hasCover = $room->photos()->where('is_cover', true)->exists();
        $lastSort = $room->photos()->max('sort_order') ?? -1;

        foreach (array_values($files) as $index => $photo) {
            $safeName = preg_replace('/[^a-zA-Z0-9]/', '_', pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME));
            $filename = time() . '_' . uniqid() . '_' . $safeName;

            // Kompres dengan GD → simpan ke public/assets/rooms/{filename}.jpg
            $relativePath = ImageService::compressAndSave($photo, $dir, $filename, 200);

            $isCover = !$hasCover && $index === 0;

            $room->photos()->create([
                'path'          => $relativePath,
                'original_name' => $photo->getClientOriginalName(),
                'is_cover'      => $isCover,
                'sort_order'    => $lastSort + $index + 1,
            ]);

            if ($isCover) $hasCover = true;
        }
    }

    /**
     * Pastikan selalu ada satu foto cover setelah penghapusan.
     */
    private function ensureCover(Room $room): void
    {
        $hasCover = $room->photos()->where('is_cover', true)->exists();
        if (!$hasCover) {
            $first = $room->photos()->orderBy('sort_order')->first();
            if ($first) {
                $first->update(['is_cover' => true]);
            }
        }
    }
}
