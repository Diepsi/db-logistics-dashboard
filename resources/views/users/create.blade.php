<x-app-layout>
    <x-slot name="header">
        Tambah User Baru
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">

        <!-- Notifikasi Error Validasi -->
        @if($errors->any())
            <div class="flex items-start gap-3 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl animate-fade-up">
                <span class="icon-chip !w-9 !h-9 bg-rose-100 text-rose-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-bold mb-1">Validasi gagal:</p>
                    <ul class="text-xs list-disc pl-5 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="card p-6 sm:p-8" x-reveal>
            <div class="flex items-center gap-2.5 mb-6">
                <span class="icon-chip bg-dbl-green-light/60 text-dbl-green-dark">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </span>
                <h3 class="text-base font-bold text-gray-800">Informasi User</h3>
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="name" value="Nama Lengkap" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="role_id" value="Role" />
                    <select id="role_id" name="role_id" class="field-input mt-1" required>
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}@if($role->description) — {{ $role->description }}@endif
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('role_id')" />
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <x-primary-button>Simpan User</x-primary-button>
                    <a href="{{ route('users.index') }}" class="btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
