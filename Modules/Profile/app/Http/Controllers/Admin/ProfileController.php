<?php

namespace Modules\Profile\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Modules\Profile\Models\ProfileUser;

class ProfileController extends Controller
{
    // ── INDEX ──────────────────────────────────────────────
    public function index()
    {
        $user    = Auth::user();
        $profile = ProfileUser::firstOrCreate(
            ['user_id' => $user->id],
            ['uuid' => (string) \Illuminate\Support\Str::uuid()]
        );

        return view('profile::Admin.index', compact('user', 'profile'));
    }

    // ── UPDATE INFO (nama, email, wa, foto) ─────────────────
    public function updateInfo(Request $request)
    {
        $user    = Auth::user();
        $profile = ProfileUser::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'wa'    => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
            'foto'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah digunakan oleh akun lain.',
            'wa.required'    => 'Nomor WhatsApp wajib diisi.',
            'wa.regex'       => 'Format nomor WhatsApp tidak valid.',
            'foto.image'     => 'File harus berupa gambar.',
            'foto.max'       => 'Ukuran foto maksimal 2MB.',
        ]);

        // Update user
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        // Handle upload foto
        $fotoPath = $profile->foto;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            // Hapus foto lama jika ada
            if ($fotoPath && file_exists(public_path($fotoPath))) {
                @unlink(public_path($fotoPath));
            }

            $file     = $request->file('foto');
            $dir      = public_path('assets/profiles');
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $filename = time() . '_' . \Illuminate\Support\Str::random(8) . '.jpg';
            $fotoPath = 'assets/profiles/' . $filename;

            // Kompres dengan GD ke 80% kualitas
            $this->compressImage($file->getRealPath(), public_path($fotoPath), $file->getMimeType());
        }

        $profile->update([
            'wa'   => $request->wa,
            'foto' => $fotoPath,
        ]);

        return redirect()
            ->route('admin.profile.index')
            ->with('success', 'Informasi profil berhasil diperbarui.');
    }

    // ── UPDATE PASSWORD ────────────────────────────────────
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

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
            return back()
                ->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('admin.profile.index')
            ->with('success_password', 'Password berhasil diperbarui. Silakan login ulang jika diperlukan.');
    }

    // ── AJAX: CHECK PASSWORD MATCH ─────────────────────────
    public function checkPassword(Request $request)
    {
        $password        = $request->input('password', '');
        $confirmation    = $request->input('password_confirmation', '');

        if (empty($password) || empty($confirmation)) {
            return response()->json(['match' => null]);
        }

        return response()->json([
            'match' => $password === $confirmation,
        ]);
    }

    // ── PRIVATE: Kompres gambar dengan GD ─────────────────
    private function compressImage(string $src, string $dest, string $mime): void
    {
        $img = null;
        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            $img = imagecreatefromjpeg($src);
        } elseif ($mime === 'image/png') {
            $img = imagecreatefrompng($src);
        } elseif ($mime === 'image/webp') {
            $img = imagecreatefromwebp($src);
        }

        if (!$img) {
            // Fallback: copy saja
            copy($src, $dest);
            return;
        }

        // Resize ke max 400x400 crop center
        $srcW = imagesx($img);
        $srcH = imagesy($img);
        $size = 400;
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
