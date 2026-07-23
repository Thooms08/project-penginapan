<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        // Generate captcha baru setiap kali halaman login dibuka
        $captcha = $this->generateCaptcha();
        session(['login_captcha' => $captcha]);

        return view('auth.login');
    }

    /**
     * Proses login manual (email + password).
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
            'captcha'  => ['required', 'string'],
        ], [
            'captcha.required' => 'Kode captcha wajib diisi.',
        ]);

        // Validasi captcha (case-insensitive, tapi simpan uppercase)
        $expected = session('login_captcha');
        if (!$expected || strtoupper($request->captcha) !== strtoupper($expected)) {
            // Regenerate captcha setelah salah
            session(['login_captcha' => $this->generateCaptcha()]);
            return back()
                ->withErrors(['captcha' => 'Kode captcha salah. Silakan coba lagi.'])
                ->onlyInput('email');
        }

        // Hapus captcha dari session setelah dipakai
        session()->forget('login_captcha');

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectByRole(Auth::user());
        }

        // Regenerate captcha baru setelah login gagal
        session(['login_captcha' => $this->generateCaptcha()]);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Redirect ke Google OAuth.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Login Google gagal. Silakan coba lagi.']);
        }

        // Cari user berdasarkan google_id atau email
        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        if ($user) {
            // Update google_id & avatar jika belum ada
            $user->update([
                'google_id'          => $googleUser->getId(),
                'avatar'             => $googleUser->getAvatar(),
                'email_verified_at'  => $user->email_verified_at ?? now(),
            ]);
        } else {
            // Buat user baru dengan role visitor
            $user = User::create([
                'name'               => $googleUser->getName(),
                'email'              => $googleUser->getEmail(),
                'google_id'          => $googleUser->getId(),
                'avatar'             => $googleUser->getAvatar(),
                'password'           => null,
                'role'               => 'visitor',
                'email_verified_at'  => now(),
            ]);
        }

        Auth::login($user, true);

        return $this->redirectByRole($user);
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirect berdasarkan role user.
     */
    private function redirectByRole(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('visitor.dashboard');
    }

    /**
     * Generate 4 karakter random: huruf kapital + angka.
     * Format seperti: AB45, 3K9M, X2Z8
     */
    private function generateCaptcha(): string
    {
        $chars  = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // tanpa I,O,0,1 supaya mudah dibaca
        $result = '';
        for ($i = 0; $i < 4; $i++) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $result;
    }

    /**
     * Refresh captcha via AJAX (return JSON).
     */
    public function refreshCaptcha(Request $request)
    {
        $captcha = $this->generateCaptcha();
        session(['login_captcha' => $captcha]);
        return response()->json(['captcha' => $captcha]);
    }
}
