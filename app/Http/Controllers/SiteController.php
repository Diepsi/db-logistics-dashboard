<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Halaman publik website Amanah Nusantara Logistik.
 * Modul tambahan atas permintaan perusahaan — terpisah dari modul dashboard.
 */
class SiteController extends Controller
{
    public function home(): View
    {
        $latestPosts = Post::published()
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        $services = Service::active()
            ->section('layanan')
            ->orderBy('sort_order')
            ->get();

        $clients = Client::active()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Client $client) => [
                'url' => asset('storage/'.$client->image_path),
                'name' => $client->name,
            ]);

        if ($clients->isEmpty()) {
            $clients = collect(glob(public_path('images/clients/*')))
                ->filter(fn ($path) => is_file($path))
                ->map(fn ($path) => [
                    'url' => asset('images/clients/'.basename($path)),
                    'name' => pathinfo($path, PATHINFO_FILENAME),
                ])
                ->values();
        }

        return view('site.home', compact('latestPosts', 'services', 'clients'));
    }

    public function about(): View
    {
        return view('site.about');
    }

    public function services(): View
    {
        $services = Service::active()
            ->section('layanan')
            ->orderBy('sort_order')
            ->get();

        $moda = Service::active()
            ->section('moda')
            ->orderBy('sort_order')
            ->get();

        return view('site.services', compact('services', 'moda'));
    }

    public function contact(): View
    {
        return view('site.contact');
    }

    public function berita(): View
    {
        $posts = Post::published()
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('site.berita', compact('posts'));
    }

    public function beritaShow(string $slug): View
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        return view('site.berita-detail', compact('post'));
    }

    /**
     * Simpan pesan dari form kontak (tanpa DB — hanya konfirmasi).
     */
    public function storeContact(Request $request): RedirectResponse|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Data yang diberikan tidak valid.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        logger()->info('Pesan baru dari website', $validator->validated());

        $message = 'Terima kasih! Pesan Anda telah kami terima dan akan segera kami balas.';

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('message', $message);
    }
}
