<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Baca dari session, fallback ke 'id' jika belum pernah dipilih
        $locale = $request->session()->get('locale', 'id');

        // Hanya izinkan locale yang valid
        if (!in_array($locale, ['id', 'en'])) {
            $locale = 'id';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
