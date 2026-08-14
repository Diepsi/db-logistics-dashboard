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
            <header class="bg-white border-b border-gray-200 sticky top-0 z-10 shadow-sm">
                <div class="px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
                    
                    <!-- Left: Mobile Menu Button & Breadcrumb/Title -->
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 md:hidden focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-lg font-bold text-gray-800">
                                {{ $header ?? 'Dashboard Monitoring' }}
                            </h1>
                            <p class="text-xs text-gray-500">Amanah Nusantara Logistik - Operational Performance Layer</p>
                        </div>
                    </div>

                    <!-- Right: User Profile & Quick Status -->
                    <div class="flex items-center space-x-4">
                        <!-- Badge Network Status -->
                        <div class="hidden sm:flex items-center space-x-2 px-3 py-1 rounded-full bg-dbl-green-light/60 border border-dbl-green/20">
                            <span class="h-2 w-2 rounded-full bg-dbl-green animate-pulse"></span>
                            <span class="text-xs font-medium text-dbl-green-dark">System Active</span>
                        </div>

                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none">
                                <div class="w-8 h-8 rounded-full bg-dbl-dark text-dbl-green flex items-center justify-center font-bold text-sm border border-dbl-green/30">
                                    {{ substr(Auth::user()->name ?? 'Admin', 0, 1) }}
                                </div>
                                <span class="hidden md:block text-sm font-semibold text-gray-700">
                                    {{ Auth::user()->name ?? 'Ismail' }}
                                </span>
                                <svg class="w-4 h-4 text-gray-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 border border-gray-100 z-50">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-xs text-gray-400">Signed in as</p>
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->email ?? 'admin@dblogistics.com' }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile Settings</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>

        </div>
    </div>

</body>
</html>