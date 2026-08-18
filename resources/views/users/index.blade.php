<x-app-layout>
    <x-slot name="header">
        Kelola User
    </x-slot>

    <div class="space-y-6">

        <!-- Notifikasi Sukses / Error -->
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl animate-fade-up" x-data="{ show: true }" x-show="show">
                <span class="icon-chip !w-9 !h-9 bg-emerald-100 text-emerald-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <p class="text-sm font-bold flex-1">{{ session('success') }}</p>
                <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center gap-3 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl animate-fade-up" x-data="{ show: true }" x-show="show">
                <span class="icon-chip !w-9 !h-9 bg-rose-100 text-rose-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </span>
                <p class="text-sm font-bold flex-1">{{ session('error') }}</p>
                <button @click="show = false" class="text-rose-600 hover:text-rose-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <!-- Header: Judul & Tombol Tambah -->
        <div class="card p-5" x-reveal>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="icon-chip bg-dbl-green-light/60 text-dbl-green-dark">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                    <h3 class="text-base font-bold text-gray-800">Daftar User</h3>
                    <span class="text-xs text-gray-400 bg-white border border-gray-200 rounded-full px-2.5 py-0.5">Total: {{ $users->total() }}</span>
                </div>
                <a href="{{ route('users.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah User
                </a>
            </div>
        </div>

        <!-- Filter & Pencarian -->
        <div class="card p-5" x-reveal>
            <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="field-label">Cari Nama / Email</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama atau email..."
                            class="field-input !pl-9">
                    </div>
                </div>

                <div>
                    <label class="field-label">Filter Role</label>
                    <select name="role" class="field-input">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->slug }}" {{ request('role') == $role->slug ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-3 flex items-end justify-end gap-2">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Terapkan
                    </button>
                    @if(request()->hasAny(['search', 'role']))
                        <a href="{{ route('users.index') }}" class="btn-ghost">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Daftar User -->
        <div class="card overflow-hidden" x-reveal>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200 sticky top-0">
                        <tr>
                            <th class="px-6 py-3 font-bold">Nama</th>
                            <th class="px-6 py-3 font-bold">Email</th>
                            <th class="px-6 py-3 font-bold">Role</th>
                            <th class="px-6 py-3 font-bold">Dibuat</th>
                            <th class="px-6 py-3 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-dbl-green-light/20 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    <div class="flex items-center gap-2.5">
                                        <span class="icon-chip !w-8 !h-8 bg-dbl-green-light/60 text-dbl-green-dark text-xs font-bold">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                        <span>{{ $user->name }}</span>
                                        @if($user->id === Auth::id())
                                            <span class="text-[10px] font-bold text-dbl-green bg-dbl-green-light/60 px-1.5 py-0.5 rounded">Anda</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    @if($user->role?->slug === 'admin')
                                        <span class="badge bg-purple-50 text-purple-700"><span class="dot bg-purple-500"></span>{{ $user->role->name }}</span>
                                    @elseif($user->role?->slug === 'project-manager')
                                        <span class="badge bg-blue-50 text-blue-700"><span class="dot bg-blue-500"></span>{{ $user->role->name }}</span>
                                    @elseif($user->role?->slug === 'staff')
                                        <span class="badge bg-gray-100 text-gray-600"><span class="dot bg-gray-400"></span>{{ $user->role->name }}</span>
                                    @else
                                        <span class="badge bg-rose-50 text-rose-700"><span class="dot bg-rose-500"></span>Tanpa Role</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">{{ $user->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('users.edit', $user) }}" class="btn-ghost !px-3 !py-1.5 !text-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                        @if($user->id !== Auth::id())
                                            <button x-data x-on:click="$dispatch('open-modal', 'delete-user-{{ $user->id }}')" class="btn-danger !px-3 !py-1.5 !text-xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="icon-chip bg-gray-100 text-gray-400 mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-500">Tidak ada user ditemukan</p>
                                        <p class="text-xs text-gray-400 mt-1">Klik "Tambah User" untuk membuat akun baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $users->links() }}
            </div>
        </div>

    </div>

    <!-- Modal Konfirmasi Hapus (per user) -->
    @foreach($users as $user)
        @if($user->id !== Auth::id())
            <x-modal name="delete-user-{{ $user->id }}" maxWidth="md">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-11 h-11 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Hapus User?</h3>
                            <p class="text-xs text-gray-500">Akun <strong>{{ $user->name }}</strong> akan dihapus permanen.</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" x-on:click="$dispatch('close')" class="btn-ghost">Batal</button>
                        <form action="{{ route('users.destroy', $user) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger">Ya, Hapus</button>
                        </form>
                    </div>
                </div>
            </x-modal>
        @endif
    @endforeach
</x-app-layout>
