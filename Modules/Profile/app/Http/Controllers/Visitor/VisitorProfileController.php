<?php

namespace Modules\Profile\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Modules\Profile\Models\ProfileUser;

class VisitorProfileController extends Controller
{
    // ── SHOW ──────────────────────────────────────────────
    public function index()
    {
        $user    = Auth::user();
        $profile = ProfileUser::firstOrCreate(
            ['user_id' => $user->id],
            ['uuid' => (string) \Illuminate\Support\Str::uuid()]
        );

        return view('profile::Visitor.index', compact('user', 'profile'));
    }

    // ── UPDATE INFO (nama, email, wa, city, province, country, foto) ──
    public function updateInfo(Request $request)
    {
        $user    = Auth::user();
        $profile = ProfileUser::firstOrCreate(
            ['user_id' => $user->id],
            ['uuid' => (string) \Illuminate\Support\Str::uuid()]
        );

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'wa'       => 'nullable|string|max:20|regex:/^[0-9+\-\s]+$/',
            'city'     => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'country'  => 'nullable|string|max:100',
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah digunakan akun lain.',
            'wa.regex'       => 'Format nomor WhatsApp tidak valid (contoh: 08123456789).',
            'foto.image'     => 'File harus berupa gambar.',
            'foto.mimes'     => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto.max'       => 'Ukuran foto maksimal 2MB.',
        ]);

        // Update nama & email di tabel users
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        // Handle upload foto
        $fotoPath = $profile->foto;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            // Hapus foto lama
            if ($fotoPath && file_exists(public_path($fotoPath))) {
                @unlink(public_path($fotoPath));
            }

            $file = $request->file('foto');
            $dir  = public_path('assets/profiles');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $filename = time() . '_' . \Illuminate\Support\Str::random(8) . '.jpg';
            $fotoPath = 'assets/profiles/' . $filename;
            $this->compressImage($file->getRealPath(), public_path($fotoPath), $file->getMimeType());
        }

        $profile->update([
            'foto'     => $fotoPath,
            'wa'       => $request->wa,
            'city'     => $request->city,
            'province' => $request->province,
            'country'  => $request->country,
        ]);

        return redirect()
            ->route('visitor.profile.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    // ── UPDATE PASSWORD ────────────────────────────────────
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // Akun Google (tidak punya password lokal) tidak bisa ganti password
        if (!$user->password) {
            return back()->withErrors([
                'current_password' => 'Akun ini menggunakan login Google, tidak bisa mengubah password.',
            ]);
        }

        $request->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'string', 'min:8', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('visitor.profile.index')
            ->with('success_password', 'Password berhasil diperbarui.');
    }

    // ── HAPUS FOTO ─────────────────────────────────────────
    public function deleteFoto()
    {
        $profile = ProfileUser::where('user_id', Auth::id())->firstOrFail();

        if ($profile->foto && file_exists(public_path($profile->foto))) {
            @unlink(public_path($profile->foto));
        }

        $profile->update(['foto' => null]);

        return redirect()
            ->route('visitor.profile.index')
            ->with('success', 'Foto profil berhasil dihapus.');
    }

    // ── PRIVATE: Kompres & crop gambar ke 400×400 ──────────
    private function compressImage(string $src, string $dest, string $mime): void
    {
        $img = match (true) {
            in_array($mime, ['image/jpeg', 'image/jpg']) => imagecreatefromjpeg($src),
            $mime === 'image/png'  => imagecreatefrompng($src),
            $mime === 'image/webp' => imagecreatefromwebp($src),
            default                => null,
        };

        if (!$img) {
            copy($src, $dest);
            return;
        }

        $srcW  = imagesx($img);
        $srcH  = imagesy($img);
        $size  = 400;
        $ratio = min($srcW, $srcH);
        $srcX  = (int)(($srcW - $ratio) / 2);
        $srcY  = (int)(($srcH - $ratio) / 2);

        $thumb = imagecreatetruecolor($size, $size);
        imagecopyresampled($thumb, $img, 0, 0, $srcX, $srcY, $size, $size, $ratio, $ratio);
        imagejpeg($thumb, $dest, 85);

        imagedestroy($img);
        imagedestroy($thumb);
    }
}
