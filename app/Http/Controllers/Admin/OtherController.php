<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Other;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtherController extends Controller
{
    /**
     * Display the About page editor.
     */
    public function about()
    {
        $other = Other::getInstance();
        return view('Admin.other.about', compact('other'));
    }

    /**
     * Update About content.
     */
    public function updateAbout(Request $request)
    {
        $request->validate([
            'about' => 'nullable|string',
        ]);

        $other = Other::getInstance();
        $other->update([
            'about'      => $request->about,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('admin.other.about')
            ->with('success', 'Konten "Tentang" berhasil diperbarui.');
    }

    /**
     * Display the Privacy Policy page editor.
     */
    public function privacyPolicy()
    {
        $other = Other::getInstance();
        return view('Admin.other.privacy-policy', compact('other'));
    }

    /**
     * Update Privacy Policy content.
     */
    public function updatePrivacyPolicy(Request $request)
    {
        $request->validate([
            'privacy_policy' => 'nullable|string',
        ]);

        $other = Other::getInstance();
        $other->update([
            'privacy_policy' => $request->privacy_policy,
            'updated_by'     => Auth::id(),
        ]);

        return redirect()
            ->route('admin.other.privacy-policy')
            ->with('success', 'Konten "Kebijakan & Privasi" berhasil diperbarui.');
    }

    /**
     * Display the Terms & Conditions page editor.
     */
    public function termsConditions()
    {
        $other = Other::getInstance();
        return view('Admin.other.terms-conditions', compact('other'));
    }

    /**
     * Update Terms & Conditions content.
     */
    public function updateTermsConditions(Request $request)
    {
        $request->validate([
            'terms_conditions' => 'nullable|string',
        ]);

        $other = Other::getInstance();
        $other->update([
            'terms_conditions' => $request->terms_conditions,
            'updated_by'       => Auth::id(),
        ]);

        return redirect()
            ->route('admin.other.terms-conditions')
            ->with('success', 'Konten "Syarat & Ketentuan" berhasil diperbarui.');
    }
}
