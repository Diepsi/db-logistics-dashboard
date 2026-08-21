<!-- Overlay Mobile Sidebar -->
<div x-show="sidebarOpen"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-sm transition-opacity md:hidden"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"></div>

<!-- Sidebar Main Container -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed md:sticky md:top-0 md:h-screen inset-y-0 left-0 z-40 w-64 flex flex-col shrink-0 bg-white dark:bg-slate-900 border-r border-slate-200/80 dark:border-slate-800 transition-transform duration-300 ease-in-out md:translate-x-0">

    <!-- Brand Header -->
    <div class="h-16 flex items-center gap-3 px-5 border-b border-slate-200/80 dark:border-slate-800 shrink-0">
        <div class="w-11 h-11 rounded-xl bg-white dark:bg-white flex items-center justify-center overflow-hidden shadow-md shadow-slate-900/10 ring-1 ring-slate-200/80 dark:ring-slate-700 shrink-0">
            <img src="{{ asset('images/logo-anl.png') }}" alt="Logo Amanah Nusantara Logistik" class="w-full h-full object-contain" loading="eager">
        </div>
        <div class="min-w-0">
            <p class="text-sm font-bold tracking-wide text-slate-900 dark:text-white leading-tight truncate">Logistics Hub</p>
            <p class="text-[10px] font-semibold uppercase tracking-widest text-indigo-500 dark:text-indigo-400 leading-tight truncate">Amanah Nusantara Logistik</p>
        </div>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">

        <!-- Ringkasan -->
        <div>
            <p class="px-2.5 pb-2 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Ringkasan</p>
            <div class="space-y-1">
                <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" @click="sidebarOpen = false">
                    <x-slot:icon>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 00-1 1m-6 0h6"/></svg>
                    </x-slot:icon>
                    Dashboard
                </x-nav-link>
            </div>
        </div>

        <!-- Operasional & Logistik -->
        <div>
            <p class="px-2.5 pb-2 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Operasional &amp; Logistik</p>
            <div class="space-y-1">
                <x-nav-link href="{{ route('shipments.index') }}" :active="request()->routeIs('shipments.*')" @click="sidebarOpen = false">
                    <x-slot:icon>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z"/></svg>
                    </x-slot:icon>
                    Shipments
                </x-nav-link>

                <x-nav-link href="{{ route('issues.index') }}" :active="request()->routeIs('issues.*')" @click="sidebarOpen = false">
                    <x-slot:icon>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </x-slot:icon>
                    Issues
                </x-nav-link>
            </div>
        </div>

        @if(auth()->user()?->hasRole('admin'))
            <!-- Manajemen Data -->
            <div>
                <p class="px-2.5 pb-2 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Manajemen Data</p>
                <div class="space-y-1">
                    <x-nav-link href="{{ route('imports.index') }}" :active="request()->routeIs('imports.*')" @click="sidebarOpen = false">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        </x-slot:icon>
                        Import Center
                    </x-nav-link>
                </div>
            </div>
        @endif

        <!-- Analisis & Laporan -->
        @if(auth()->user()?->hasRole('admin') || auth()->user()?->hasRole('project-manager') || auth()->user()?->hasRole('staff'))
            <div>
                <p class="px-2.5 pb-2 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Analisis &amp; Laporan</p>
                <div class="space-y-1">
                    <x-nav-link href="{{ route('analytics.index') }}" :active="request()->routeIs('analytics.*')" @click="sidebarOpen = false">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </x-slot:icon>
                        Analytics
                    </x-nav-link>

                    <x-nav-link href="{{ route('reports.index') }}" :active="request()->routeIs('reports.*')" @click="sidebarOpen = false">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </x-slot:icon>
                        Reports
                    </x-nav-link>
                </div>
            </div>
        @endif

        @if(auth()->user()?->hasRole('admin'))
            <!-- Administrasi -->
            <div>
                <p class="px-2.5 pb-2 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Administrasi</p>
                <div class="space-y-1">
                    <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.*')" @click="sidebarOpen = false">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </x-slot:icon>
                        User Management
                    </x-nav-link>
                </div>
            </div>
        @endif

    </nav>

    <!-- Footer Sidebar / User Card -->
    <div class="p-3 border-t border-slate-200/80 dark:border-slate-800 shrink-0">
        @php
            $nameWords = preg_split('/\s+/', trim(Auth::user()?->name ?? 'AN'));
            $userInitials = strtoupper(substr($nameWords[0], 0, 1).(isset($nameWords[1]) ? substr($nameWords[1], 0, 1) : ''));
        @endphp
        <div class="flex items-center gap-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 ring-1 ring-slate-200/70 dark:ring-slate-700/50 p-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-xs font-bold text-white shadow-md shadow-indigo-500/20 shrink-0">
                {{ $userInitials }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-slate-900 dark:text-white truncate">{{ Auth::user()?->name }}</p>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()?->email }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" title="Keluar"
                        class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 focus:outline-none focus:ring-2 focus:ring-rose-500/30 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>

</aside>
