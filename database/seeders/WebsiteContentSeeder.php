<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class WebsiteContentSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(['key' => 'site.logo'], ['value' => 'images/logo-anl.jpg']);

        $services = [
            [
                'slug' => 'ltl',
                'section' => 'layanan',
                'name' => 'Less Than Truckload (LTL)',
                'badge' => 'Retail',
                'description' => 'Pengiriman Retail / Parsial — menggabungkan muatan beberapa pengirim dalam satu truk sehingga biaya lebih efisien untuk kiriman skala kecil hingga menengah.',
                'icon_svg' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'sort_order' => 1,
            ],
            [
                'slug' => 'ftl',
                'section' => 'layanan',
                'name' => 'Full Truckload (FTL)',
                'badge' => 'Charter',
                'description' => 'Sewa Truk / Charter Penuh — penyewaan armada penuh untuk kebutuhan distribusi dengan volume besar, rute khusus, dan prioritas pengiriman.',
                'icon_svg' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1',
                'sort_order' => 2,
            ],
            [
                'slug' => 'project-logistics',
                'section' => 'layanan',
                'name' => 'Project Logistics',
                'badge' => 'Custom Cargo',
                'description' => 'Pengiriman kargo khusus & project logistics — penanganan muatan berukuran/berat ekstra, alat berat, dan kebutuhan proyek dengan perencanaan menyeluruh.',
                'icon_svg' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8M9 12h6',
                'sort_order' => 3,
            ],
            [
                'slug' => 'darat',
                'section' => 'moda',
                'name' => 'Darat',
                'description' => 'Armada trucking untuk rute antar kota & antar pulau di Pulau Sumatera dan sekitarnya.',
                'icon_svg' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                'sort_order' => 1,
            ],
            [
                'slug' => 'laut',
                'section' => 'moda',
                'name' => 'Laut',
                'description' => 'Pengangkutan muatan antar pulau dengan perencanaan logistik matang dan penanganan aman.',
                'icon_svg' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'sort_order' => 2,
            ],
            [
                'slug' => 'udara',
                'section' => 'moda',
                'name' => 'Udara',
                'description' => 'Pengiriman cepat untuk kebutuhan mendesak dengan prioritas penanganan dan ketepatan waktu.',
                'icon_svg' => 'M12 19V9m0 0l-4 4m4-4l4 4M12 3a9 9 0 110 16 9 9 0 010-16z',
                'sort_order' => 3,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['slug' => $service['slug']], $service);
        }
    }
}
