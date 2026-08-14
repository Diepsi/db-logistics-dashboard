<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman publik website Amanah Nusantara Logistik.
 * Modul tambahan atas permintaan perusahaan — terpisah dari modul dashboard.
 */
class SiteController extends Controller
{
    public function home(): View
    {
        return view('site.home');
    }

    public function about(): View
    {
        return view('site.about');
    }

    public function services(): View
    {
        return view('site.services');
    }

    public function contact(): View
    {
        return view('site.contact');
    }

    /**
     * Simpan pesan dari form kontak (tanpa DB — hanya konfirmasi).
     */
    public function storeContact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        logger()->info('Pesan baru dari website', $data);

        return back()->with('message', 'Terima kasih! Pesan Anda telah kami terima dan akan segera kami balas.');
    }
}
