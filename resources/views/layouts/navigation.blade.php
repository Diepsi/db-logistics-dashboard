<!-- Overlay Mobile Sidebar -->
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false" 
     class="fixed inset-0 z-20 bg-black/50 transition-opacity md:hidden"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"></div>

<!-- Sidebar Main Container -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed md:static inset-y-0 left-0 z-30 w-64 bg-dbl-dark text-gray-300 transition-transform duration-300 ease-in-out md:translate-x-0 flex flex-col justify-between shrink-0 border-r border-gray-800">

    <div>
        <!-- Brand Header / Logo DB Logistics -->
        <div class="h-16 flex items-center px-6 bg-dbl-darker border-b border-gray-800">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-lg bg-dbl-green flex items-center justify-center text-dbl-dark font-extrabold text-xl shadow-lg shadow-dbl-green/20">
                    DB
                </div>
                <div>
                    <span class="text-white font-bold tracking-wider text-base block leading-tight">LOGISTICS</span>
                    <span class="text-[10px] text-dbl-green font-semibold tracking-widest uppercase block">DB Analytics Layer</span>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="mt-6 px-4 space-y-1">
            
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 {{ request()->routeIs('dashboard') ? 'bg-dbl-green text-dbl-dark font-bold shadow-md shadow-dbl-green/20' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 00-1 1m-6 0h6" />
                </svg>
                <span>Dashboard KPI</span>
            </a>

            <!-- Import Data Excel -->
            <a href="{{ route('imports.index') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 {{ request()->routeIs('imports.*') ? 'bg-dbl-green text-dbl-dark font-bold shadow-md shadow-dbl-green/20' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <span>Import Data Excel</span>
            </a>

            <!-- Data Pengiriman -->
            <a href="{{ route('shipments.index') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 {{ request()->routeIs('shipments.*') ? 'bg-dbl-green text-dbl-dark font-bold shadow-md shadow-dbl-green/20' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Data Pengiriman</span>
            </a>

            <!-- Analisis SLA -->
            <a href="{{ route('sla.index') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 {{ request()->routeIs('sla.*') ? 'bg-dbl-green text-dbl-dark font-bold shadow-md shadow-dbl-green/20' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Analisis SLA</span>
            </a>

            <!-- Analisis Vendor -->
            <a href="{{ route('vendors.index') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 {{ request()->routeIs('vendors.*') ? 'bg-dbl-green text-dbl-dark font-bold shadow-md shadow-dbl-green/20' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Analisis Vendor</span>
            </a>

            <!-- Laporan & Export -->
            <a href="{{ route('reports.index') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 {{ request()->routeIs('reports.*') ? 'bg-dbl-green text-dbl-dark font-bold shadow-md shadow-dbl-green/20' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Laporan & Export</span>
            </a>

        </nav>
    </div>

    <!-- Footer Sidebar / Project Info -->
    <div class="p-4 border-t border-gray-800 bg-dbl-darker">
        <div class="flex items-center space-x-3">
            <div class="w-2 h-2 rounded-full bg-dbl-green"></div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Praktik Lapang IPB/UNPAK</p>
                <p class="text-[11px] text-gray-500">M. Ismail - 065123022</p>
            </div>
        </div>
    </div>

</aside>