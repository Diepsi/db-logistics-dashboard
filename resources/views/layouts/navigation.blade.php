<!-- Overlay Mobile Sidebar -->
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false" 
     class="fixed inset-0 z-20 bg-black/50 backdrop-blur-sm transition-opacity md:hidden"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"></div>

<!-- Sidebar Main Container -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed md:static inset-y-0 left-0 z-30 w-64 bg-gradient-to-b from-dbl-dark via-dbl-dark to-dbl-darker text-gray-300 transition-transform duration-300 ease-in-out md:translate-x-0 flex flex-col justify-between shrink-0 border-r border-gray-800">

    <div>
        <!-- Brand Header / Logo DB Logistics -->
        <div class="h-16 flex items-center px-6 bg-dbl-darker/80 border-b border-gray-800/80 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-dbl-green/10 via-transparent to-transparent"></div>
            <div class="flex items-center space-x-3 relative">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-dbl-green to-dbl-green-dark flex items-center justify-center text-dbl-dark font-extrabold text-xl shadow-lg shadow-dbl-green/20">
                    ANL
                </div>
                <div>
                    <span class="text-white font-bold tracking-wider text-base block leading-tight">LOGISTICS</span>
                    <span class="text-[10px] text-dbl-green font-semibold tracking-widest uppercase block">Amanah Nusantara Logistik</span>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="mt-5 px-3 space-y-1">

            <p class="px-3.5 pb-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Menu Utama</p>

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="group relative flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-dbl-green/15 to-dbl-green/5 text-dbl-green-light font-bold shadow-md shadow-dbl-green/10' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-dbl-green shadow-glow transition-all {{ request()->routeIs('dashboard') ? 'opacity-100' : 'opacity-0 group-hover:opacity-40' }}"></span>
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 00-1 1m-6 0h6" />
                </svg>
                <span>Dashboard KPI</span>
            </a>

            <!-- Data Pengiriman (semua role) -->
            <a href="{{ route('shipments.index') }}" 
               class="group relative flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('shipments.*') ? 'bg-gradient-to-r from-dbl-green/15 to-dbl-green/5 text-dbl-green-light font-bold shadow-md shadow-dbl-green/10' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-dbl-green shadow-glow transition-all {{ request()->routeIs('shipments.*') ? 'opacity-100' : 'opacity-0 group-hover:opacity-40' }}"></span>
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Data Pengiriman</span>
            </a>

            <!-- Issue Management -->
            <a href="{{ route('issues.index') }}"
               class="group relative flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('issues.*') ? 'bg-gradient-to-r from-dbl-green/15 to-dbl-green/5 text-dbl-green-light font-bold shadow-md shadow-dbl-green/10' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-dbl-green shadow-glow transition-all {{ request()->routeIs('issues.*') ? 'opacity-100' : 'opacity-0 group-hover:opacity-40' }}"></span>
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Issue Management</span>
            </a>

            @if(auth()->user()?->hasRole('admin') || auth()->user()?->hasRole('project-manager'))
                @if(auth()->user()?->hasRole('admin'))
                    <!-- Admin Section -->
                    <p class="pt-5 pb-2 px-3.5 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Kelola Data</p>

                    <!-- Import Data Excel -->
                    <a href="{{ route('imports.index') }}" 
                       class="group relative flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('imports.*') ? 'bg-gradient-to-r from-dbl-green/15 to-dbl-green/5 text-dbl-green-light font-bold shadow-md shadow-dbl-green/10' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-dbl-green shadow-glow transition-all {{ request()->routeIs('imports.*') ? 'opacity-100' : 'opacity-0 group-hover:opacity-40' }}"></span>
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <span>Import Data Excel</span>
                    </a>

                    <!-- Kelola User -->
                    <a href="{{ route('users.index') }}" 
                       class="group relative flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-dbl-green/15 to-dbl-green/5 text-dbl-green-light font-bold shadow-md shadow-dbl-green/10' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-dbl-green shadow-glow transition-all {{ request()->routeIs('users.*') ? 'opacity-100' : 'opacity-0 group-hover:opacity-40' }}"></span>
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Kelola User</span>
                    </a>
                @endif

                <!-- Laporan & Export (Admin & Project Manager) -->
                <a href="{{ route('reports.index') }}" 
                   class="group relative flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-dbl-green/15 to-dbl-green/5 text-dbl-green-light font-bold shadow-md shadow-dbl-green/10' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-dbl-green shadow-glow transition-all {{ request()->routeIs('reports.*') ? 'opacity-100' : 'opacity-0 group-hover:opacity-40' }}"></span>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Laporan & Export</span>
                </a>
            @endif

        </nav>
    </div>

    <!-- Footer Sidebar / Project Info -->
    <div class="p-4 border-t border-gray-800/80 bg-dbl-darker/60">
        <div class="flex items-center space-x-3 rounded-lg bg-white/[0.03] px-3 py-2.5 border border-white/5">
            <div class="relative flex h-2 w-2">
                <span class="absolute inline-flex h-full w-full rounded-full bg-dbl-green opacity-75 animate-ping"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-dbl-green"></span>
            </div>
            <div>
                <p class="text-xs text-gray-300 font-semibold">Praktik Lapang</p>
                <p class="text-[11px] text-gray-500">M. Ismail - 065123022</p>
            </div>
        </div>
    </div>

</aside>
