<?php

namespace Modules\Room\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Room\Models\Room;

class RoomController extends Controller
{
    // ── INDEX ──────────────────────────────────────────────────
    public function index()
    {
        $rooms = Room::with(['coverPhoto', 'facilities'])
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
        $validated = $request->validate([
            'name'                         => 'required|string|max:255',
            'capacity'                     => 'required|integer|min:1|max:100',
            'price'                        => 'required|numeric|min:0',
            'status'                       => 'required|in:available,unavailable',
            'description'                  => 'nullable|string|max:5000',
            'photos.*'                     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'facilities'                   => 'nullable|array',
            'facilities.*.name'            => 'required_with:facilities|string|max:100',
            'facilities.*.qty'             => 'required_with:facilities|integer|min:1',
            'facilities.*.description'     => 'nullable|string|max:150',
        ], [
            'name.required'            => 'Nama kamar wajib diisi.',
            'capacity.required'        => 'Kapasitas wajib diisi.',
            'price.required'           => 'Harga wajib diisi.',
            'photos.*.image'           => 'File harus berupa gambar.',
            'photos.*.max'             => 'Ukuran foto maksimal 4MB.',
            'facilities.*.name.required_with' => 'Nama fasilitas wajib diisi.',
            'facilities.*.qty.required_with'  => 'Jumlah fasilitas wajib diisi.',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $room = Room::create([
                'name'        => $validated['name'],
                'capacity'    => $validated['capacity'],
                'price'       => $validated['price'],
                'status'      => $validated['status'],
                'description' => $validated['description'] ?? null,
            ]);

            // Simpan fasilitas
            if (!empty($validated['facilities'])) {
                foreach ($validated['facilities'] as $f) {
                    if (!empty($f['name'])) {
                        $room->facilities()->create([
                            'name'        => $f['name'],
                            'qty'         => $f['qty'],
                            'description' => $f['description'] ?? null,
                        ]);
                    }
                }
            }

            // Simpan foto
            if ($request->hasFile('photos')) {
                $dir = public_path('assets/rooms');
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                foreach ($request->file('photos') as $index => $photo) {
                    $filename = time() . '_' . $index . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $photo->getClientOriginalName());
                    $photo->move($dir, $filename);

                    $room->photos()->create([
                        'path'          => 'assets/rooms/' . $filename,
                        'original_name' => $photo->getClientOriginalName(),
                        'is_cover'      => $index === 0,
                        'sort_order'    => $index,
                    ]);
                }
            }
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

        $validated = $request->validate([
            'name'                         => 'required|string|max:255',
            'capacity'                     => 'required|integer|min:1|max:100',
            'price'                        => 'required|numeric|min:0',
            'status'                       => 'required|in:available,unavailable',
            'description'                  => 'nullable|string|max:5000',
            'photos.*'                     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'facilities'                   => 'nullable|array',
            'facilities.*.name'            => 'required_with:facilities|string|max:100',
            'facilities.*.qty'             => 'required_with:facilities|integer|min:1',
            'facilities.*.description'     => 'nullable|string|max:150',
            'delete_photos'                => 'nullable|array',
            'delete_photos.*'              => 'integer|exists:room_photos,id',
        ]);

        DB::transaction(function () use ($request, $validated, $room) {
            $room->update([
                'name'        => $validated['name'],
                'capacity'    => $validated['capacity'],
                'price'       => $validated['price'],
                'status'      => $validated['status'],
                'description' => $validated['description'] ?? null,
            ]);

            // Hapus foto yang dipilih admin
            if (!empty($validated['delete_photos'])) {
                foreach ($room->photos()->whereIn('id', $validated['delete_photos'])->get() as $photo) {
                    $filePath = public_path($photo->path);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    $photo->delete();
                }
                // Tandai foto pertama sebagai cover jika cover dihapus
                $firstPhoto = $room->photos()->first();
                if ($firstPhoto) {
                    $room->photos()->update(['is_cover' => false]);
                    $firstPhoto->update(['is_cover' => true]);
                }
            }

            // Tambah foto baru
            if ($request->hasFile('photos')) {
                $dir       = public_path('assets/rooms');
                $lastOrder = $room->photos()->max('sort_order') ?? -1;
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                foreach ($request->file('photos') as $index => $photo) {
                    $filename = time() . '_' . $index . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $photo->getClientOriginalName());
                    $photo->move($dir, $filename);
                    $room->photos()->create([
                        'path'          => 'assets/rooms/' . $filename,
                        'original_name' => $photo->getClientOriginalName(),
                        'is_cover'      => false,
                        'sort_order'    => $lastOrder + $index + 1,
                    ]);
                }
            }

            // Sync fasilitas — hapus semua lalu insert ulang
            $room->facilities()->delete();
            if (!empty($validated['facilities'])) {
                foreach ($validated['facilities'] as $f) {
                    if (!empty($f['name'])) {
                        $room->facilities()->create([
                            'name'        => $f['name'],
                            'qty'         => $f['qty'],
                            'description' => $f['description'] ?? null,
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
            // Hapus semua foto dari disk
            foreach ($room->photos as $photo) {
                $filePath = public_path($photo->path);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $room->delete(); // soft delete
        });

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Kamar berhasil dihapus.');
    }
}
