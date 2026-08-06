<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Simpan locale ke session lalu redirect kembali.
     */
    public function set(Request $request, string $locale)
    {
        // Hanya izinkan locale yang valid
        if (!in_array($locale, ['id', 'en'])) {
            $locale = 'id';
        }

        $request->session()->put('locale', $locale);

        return redirect()->back()->withFragment($request->query('fragment'));
    }
}
