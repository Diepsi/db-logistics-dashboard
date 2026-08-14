<x-site-layout>
    <x-slot name="title">Kontak</x-slot>

    <!-- HEADER -->
    <section class="bg-anl-navy text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <p class="text-anl-gold font-semibold tracking-widest uppercase text-sm mb-3">Hubungi Kami</p>
            <h1 class="text-4xl font-extrabold">Kontak</h1>
        </div>
    </section>

    <!-- INFO KONTAK -->
    <section class="py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <!-- Alamat & Kontak -->
                <div>
                    <h2 class="text-2xl font-extrabold text-anl-navy mb-6">Info Kontak</h2>
                    <ul class="space-y-5">
                        @foreach ([
                            ['Alamat', 'Jl. Raya Logistik No. 12, Jakarta Selatan, DKI Jakarta', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1m-6 0h6'],
                            ['Email', 'info@amanahnusantaralogistik.id', 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                            ['Telepon / WhatsApp', '+62 812-3456-7890', 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
                            ['Jam Operasional', 'Senin - Sabtu, 08.00 - 17.00 WIB', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ] as [$label, $value, $icon])
                            <li class="flex space-x-4">
                                <div class="w-11 h-11 rounded-xl bg-anl-navy flex items-center justify-center text-anl-gold shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-anl-navy text-sm uppercase tracking-wide">{{ $label }}</h3>
                                    <p class="text-gray-600 mt-1">{{ $value }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Form Kontak -->
                <div class="bg-gray-50 rounded-2xl border border-gray-200 p-8">
                    <h2 class="text-2xl font-extrabold text-anl-navy mb-2">Kirim Pesan</h2>
                    <p class="text-sm text-gray-600 mb-6">Isi formulir berikut dan tim kami akan segera menghubungi Anda.</p>

                    <form method="POST" action="{{ route('contact') }}" class="space-y-5">
                        @csrf
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" placeholder="Nama Anda" />
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" placeholder="email@contoh.com" />
                        </div>
                        <div>
                            <x-input-label for="phone" :value="__('No. Telepon')" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" placeholder="+62 812-3456-7890" />
                        </div>
                        <div>
                            <x-input-label for="message" :value="__('Pesan')" />
                            <textarea id="message" name="message" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-anl-navy focus:ring-anl-navy" placeholder="Tuliskan kebutuhan logistik Anda..."></textarea>
                        </div>
                        <div>
                            <x-primary-button class="!bg-anl-gold hover:!bg-anl-gold-dark">
                                {{ __('Kirim Pesan') }}
                            </x-primary-button>
                        </div>
                    </form>

                    @if(session('message'))
                        <div class="mt-4 p-4 rounded-lg bg-anl-gold/10 border border-anl-gold/30 text-sm text-anl-navy">
                            {{ session('message') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-site-layout>
