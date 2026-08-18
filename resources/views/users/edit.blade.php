<x-app-layout>
    <x-slot name="header">
        Edit User: {{ $user->name }}
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </span>
                <h3 class="text-base font-bold text-gray-800">Informasi User</h3>
            </div>

            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="name" value="Nama Lengkap" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <div>
                    <x-input-label for="password" value="Password Baru" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" placeholder="Kosongkan jika tidak diubah" />
                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                </div>

                <div>
                    <x-input-label for="role_id" value="Role" />
                    <select id="role_id" name="role_id" class="field-input mt-1" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}@if($role->description) — {{ $role->description }}@endif
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('role_id')" />
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <x-primary-button>Simpan Perubahan</x-primary-button>
                    <a href="{{ route('users.index') }}" class="btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
