<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Pengaturan konten website (logo, kartu layanan, moda, logo klien)
 * agar dapat dikelola dari admin tanpa mengubah kode.
 */
class WebsiteSettingController extends Controller
{
    public function index(): View
    {
        $services = Service::orderBy('section')->orderBy('sort_order')->get();
        $clients = Client::orderBy('sort_order')->orderBy('id')->get();

        return view('website.settings.index', compact('services', 'clients'));
    }

    public function updateLogo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'logo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $path = $request->file('logo')->store('uploads/settings', 'public');

        $old = Setting::get('site.logo');
        if ($old !== null && str_starts_with($old, 'uploads/')) {
            Storage::disk('public')->delete($old);
        }

        Setting::updateOrCreate(['key' => 'site.logo'], ['value' => $path]);

        return back()->with('success', 'Logo website berhasil diperbarui.');
    }

    public function updateServices(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'services' => ['required', 'array'],
            'services.*.id' => ['required', 'integer', 'exists:services,id'],
            'services.*.name' => ['required', 'string', 'max:255'],
            'services.*.badge' => ['nullable', 'string', 'max:255'],
            'services.*.description' => ['nullable', 'string'],
            'services.*.icon_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'services.*.remove_icon' => ['nullable'],
        ]);

        foreach ($data['services'] as $input) {
            $service = Service::findOrFail($input['id']);

            $payload = [
                'name' => $input['name'],
                'badge' => $input['badge'] ?? null,
                'description' => $input['description'] ?? null,
            ];

            if (! empty($input['remove_icon'])) {
                $payload['icon_image'] = null;
                if ($service->icon_image) {
                    Storage::disk('public')->delete($service->icon_image);
                }
            }

            if (! empty($input['icon_image']) && $input['icon_image'] instanceof \Illuminate\Http\UploadedFile) {
                $payload['icon_image'] = $input['icon_image']->store('uploads/services', 'public');
                if ($service->icon_image) {
                    Storage::disk('public')->delete($service->icon_image);
                }
            }

            $service->update($payload);
        }

        return back()->with('success', 'Layanan & moda berhasil diperbarui.');
    }

    public function storeClient(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'logos' => ['required', 'array'],
            'logos.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $nextOrder = (Client::max('sort_order') ?? 0) + 1;

        foreach ($data['logos'] as $file) {
            Client::create([
                'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'image_path' => $file->store('uploads/clients', 'public'),
                'sort_order' => $nextOrder++,
            ]);
        }

        return back()->with('success', 'Logo klien berhasil ditambahkan.');
    }

    public function destroyClient(Client $client): RedirectResponse
    {
        Storage::disk('public')->delete($client->image_path);
        $client->delete();

        return back()->with('success', 'Logo klien berhasil dihapus.');
    }

    public function toggleClient(Client $client): RedirectResponse
    {
        $client->update(['is_active' => ! $client->is_active]);

        return back()->with('success', 'Status logo klien diperbarui.');
    }

    public function moveClient(Client $client, Request $request): RedirectResponse
    {
        $direction = $request->input('direction') === 'down' ? 'down' : 'up';

        $neighbor = Client::where(
            'sort_order',
            $direction === 'up' ? '<' : '>',
            $client->sort_order
        )
            ->orderBy('sort_order', $direction === 'up' ? 'desc' : 'asc')
            ->first();

        if ($neighbor !== null) {
            [$client->sort_order, $neighbor->sort_order] = [$neighbor->sort_order, $client->sort_order];
            $client->save();
            $neighbor->save();
        }

        return back()->with('success', 'Urutan logo klien diubah.');
    }
}
