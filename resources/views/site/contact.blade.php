<x-site-layout>
    <x-slot name="title">Kontak</x-slot>

    <!-- HEADER -->
    <section class="relative overflow-hidden bg-gradient-to-br from-anl-navy to-anl-navy-dark text-white">
        <div class="absolute -top-24 right-0 w-96 h-96 rounded-full bg-anl-blue/20 blur-3xl pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <p class="text-anl-amber font-bold tracking-widest uppercase text-sm mb-3">Get In Touch</p>
            <h1 class="text-4xl font-extrabold tracking-tight">Kontak</h1>
            <p class="mt-4 text-slate-300 max-w-2xl leading-relaxed">
                Kami siap mendukung kebutuhan distribusi bisnis Anda dengan layanan yang andal, aman, dan tepat waktu.
            </p>
        </div>
    </section>

    <!-- INFO KONTAK + FORM -->
    <section class="py-20 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Form -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-[0_10px_30px_-5px_rgba(15,43,72,0.08)] p-8" x-data="contactForm()">
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-2">Kirim Pesan</h2>
                <p class="text-sm text-slate-600 mb-6">Isi formulir berikut untuk permintaan penawaran, dan tim kami akan segera menghubungi Anda.</p>

                <div x-show="success" x-cloak class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-800">
                    Terima kasih! Pesan Anda telah kami terima dan akan segera kami balas.
                </div>
                <div x-show="serverError" x-cloak class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-800" x-text="serverError"></div>

                <form method="POST" action="{{ route('contact') }}" class="space-y-5" @submit.prevent="submit($event)">
                    @csrf
                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap')" />
                        <x-text-input id="name" name="name" type="text" x-model="name"
                                      :class="errors.name ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                      class="mt-1 block w-full rounded-xl border-slate-300" placeholder="Nama Anda" />
                        <p x-show="errors.name" x-cloak x-text="errors.name.join(', ')" class="mt-1 text-xs text-red-600"></p>
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" x-model="email"
                                      :class="errors.email ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                      class="mt-1 block w-full rounded-xl border-slate-300" placeholder="email@contoh.com" />
                        <p x-show="errors.email" x-cloak x-text="errors.email.join(', ')" class="mt-1 text-xs text-red-600"></p>
                    </div>
                    <div>
                        <x-input-label for="phone" :value="__('No. Telepon')" />
                        <x-text-input id="phone" name="phone" type="text" x-model="phone"
                                      :class="errors.phone ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                      class="mt-1 block w-full rounded-xl border-slate-300" placeholder="+62 813-6232-3510" />
                        <p x-show="errors.phone" x-cloak x-text="errors.phone.join(', ')" class="mt-1 text-xs text-red-600"></p>
                    </div>
                    <div>
                        <x-input-label for="message" :value="__('Pesan')" />
                        <textarea id="message" name="message" rows="4" x-model="message"
                                  :class="errors.message ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'focus:border-anl-blue focus:ring-anl-blue'"
                                  class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm" placeholder="Tuliskan kebutuhan logistik Anda..."></textarea>
                        <p x-show="errors.message" x-cloak x-text="errors.message.join(', ')" class="mt-1 text-xs text-red-600"></p>
                    </div>
                    <div>
                        <button type="submit" :disabled="sending"
                                class="w-full sm:w-auto min-h-[44px] px-7 py-3 rounded-xl bg-anl-blue text-white font-bold shadow-md shadow-anl-blue/20 hover:bg-anl-blue-dark disabled:opacity-60 disabled:cursor-not-allowed transition-all duration-300">
                            <span x-show="!sending">Kirim Pesan</span>
                            <span x-show="sending" x-cloak>Mengirim...</span>
                        </button>
                    </div>
                </form>

                @if(session('message'))
                    <div class="mt-5 p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-800">
                        {{ session('message') }}
                    </div>
                @endif
            </div>

            <!-- Kartu Kontak -->
            <div>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-[0_10px_30px_-5px_rgba(15,43,72,0.08)] p-8">
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-6">Info Kontak</h2>
                    <ul class="space-y-6">
                        @foreach ([
                            ['Alamat', 'Jl. Letda Natsir No.10A, Bojong Kulur, Kec. Gn. Putri, Kabupaten Bogor, Jawa Barat 16968', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1m-6 0h6', null, 'blue'],
                            ['Telepon / WhatsApp', '+62 813-6232-3510', 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'https://wa.me/6281362323510', 'blue'],
                            ['Email Marketing', 'marketing@amanahlogistik.co.id', 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'mailto:marketing@amanahlogistik.co.id', 'amber'],
                            ['Email Office', 'office@dblogistics.co.id', 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'mailto:office@dblogistics.co.id', 'blue'],
                        ] as [$label, $value, $icon, $href, $tone])
                            <li class="flex space-x-4">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 {{ $tone === 'amber' ? 'bg-anl-amber-light text-anl-amber' : 'bg-anl-blue-light text-anl-blue' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wide">{{ $label }}</h3>
                                    @if ($href)
                                        <a href="{{ $href }}" target="_blank" rel="noopener" class="text-slate-600 mt-1 block hover:text-anl-blue transition-colors">{{ $value }}</a>
                                    @else
                                        <p class="text-slate-600 mt-1">{{ $value }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Google Maps -->
                <div class="mt-6 rounded-2xl overflow-hidden border border-slate-200 shadow-[0_10px_30px_-5px_rgba(15,43,72,0.08)]">
                    <iframe
                        src="https://www.google.com/maps?q=Jl.%20Letda%20Natsir%20No.10A%2C%20Bojong%20Kulur%2C%20Gunung%20Putri%2C%20Bogor%2C%20Jawa%20Barat&output=embed"
                        class="w-full h-64 grayscale-[20%]"
                        style="border:0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Peta lokasi kantor ANL"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>
    </section>
</x-site-layout>
