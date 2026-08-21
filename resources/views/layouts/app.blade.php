<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-dbl-gray">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DB Logistics') }} - Operational Analytics</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts & Styles (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js untuk Interaktivitas Sidebar & Dropdown -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js CDN (untuk Visualisasi) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="h-full font-sans antialiased text-gray-900 bg-dbl-gray" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex flex-col md:flex-row">
        
        <!-- Sidebar Navigation -->
        @include('layouts.navigation')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Header -->
            <header class="bg-white/95 backdrop-blur border-b border-gray-200 sticky top-0 z-10 relative">
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-dbl-green via-dbl-green/40 to-transparent"></div>
                <div class="px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
                    
                    <!-- Left: Mobile Menu Button & Breadcrumb/Title -->
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="p-2 -ml-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors md:hidden focus:outline-none">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div class="flex items-center gap-3">
                            <div class="hidden sm:flex flex-col items-start">
                                <nav class="flex items-center text-[11px] text-gray-400 mb-0.5">
                                    <a href="{{ route('dashboard') }}" class="hover:text-dbl-green-dark transition-colors font-medium">Beranda</a>
                                    <span class="mx-1.5 text-gray-300">/</span>
                                    <span class="text-gray-600 font-medium">{{ $header ?? 'Dashboard Monitoring' }}</span>
                                </nav>
                                <h1 class="text-lg font-bold text-gray-900 leading-tight">
                                    {{ $header ?? 'Dashboard Monitoring' }}
                                </h1>
                            </div>
                            <div class="sm:hidden">
                                <h1 class="text-base font-bold text-gray-900">{{ $header ?? 'Dashboard Monitoring' }}</h1>
                            </div>
                        </div>
                    </div>

                    <!-- Right: User Profile & Quick Status -->
                    <div class="flex items-center space-x-4">
                        <!-- Badge Network Status -->
                        <div class="hidden sm:flex items-center space-x-2.5 px-3.5 py-1.5 rounded-full bg-dbl-green-light/50 border border-dbl-green/20">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-dbl-green opacity-60"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-dbl-green"></span>
                            </span>
                            <span class="text-xs font-semibold text-dbl-green-dark">System Active</span>
                        </div>

                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 rounded-full p-1 pr-2 hover:bg-gray-50 transition-colors focus:outline-none">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-dbl-green to-dbl-green-dark text-white flex items-center justify-center font-bold text-sm border border-white shadow-sm">
                                    {{ substr(Auth::user()->name ?? 'Admin', 0, 1) }}
                                </div>
                                <div class="hidden md:flex flex-col items-start leading-tight">
                                    <span class="text-sm font-semibold text-gray-800">
                                        {{ Auth::user()->name ?? 'Ismail' }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-medium">{{ Auth::user()?->role?->name ?? 'Admin' }}</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 hidden md:block transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lift py-1.5 border border-gray-100 z-50">
                                <div class="px-4 py-2.5 border-b border-gray-100 mb-1">
                                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Signed in as</p>
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email ?? 'admin@dblogistics.com' }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Profile Settings
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            <!-- Main Page Content -->
            <!-- Flash Messages -->
            @if(session('success') || session('error') || session('status'))
                <div class="px-4 sm:px-6 lg:px-8 pt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    @if(session('success'))
                        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 mb-3 shadow-sm">
                            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium flex-1">{{ session('success') }}</span>
                            <button @click="show = false" class="text-emerald-400 hover:text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl px-4 py-3 mb-3 shadow-sm">
                            <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span class="text-sm font-medium flex-1">{{ session('error') }}</span>
                            <button @click="show = false" class="text-rose-400 hover:text-rose-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </div>
                    @endif
                    @if(session('status'))
                        <div class="flex items-center gap-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-4 py-3 mb-3 shadow-sm">
                            <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium flex-1">{{ session('status') }}</span>
                            <button @click="show = false" class="text-blue-400 hover:text-blue-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </div>
                    @endif
                </div>
            @endif

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="page-enter max-w-[1600px] mx-auto w-full">
                    {{ $slot }}
                </div>
            </main>

        </div>
    </div>

</body>
</html>
