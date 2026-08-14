<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Login khusus area pengelolaan website (CMS Berita).
 * Menggunakan akun & guard yang sama dengan dashboard, hanya jalur
 * login-nya yang terpisah. CMS hanya untuk role admin.
 */
class WebsiteAuthController extends Controller
{
    public function create(): View
    {
        return view('website.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        if (! $request->user()->isAdmin()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->with('error', 'Akun tidak memiliki akses pengelolaan website.');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('website.posts.index', absolute: false));
    }
}
