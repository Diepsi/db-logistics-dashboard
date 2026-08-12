<x-app-layout>
    <x-slot name="header">
        Import Data Excel Operasional
    </x-slot>

    <div class="space-y-6">

        <!-- Notifikasi Sukses / Error -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-xl">✅</span>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-xl">⚠️</span>
                    <p class="text-sm font-semibold">{{ session('error') }}</p>
                </div>
            </div>
        @endif


        <!-- ==================== FORM UPLOAD EXCEL ==================== -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <h3 class="text-base font-bold text-gray-800 mb-1">Unggah File Raw Data (.xlsx)</h3>
            <p class="text-xs text-gray-500 mb-4">Pilih file hasil ekspor sistem/vendor DB Logistics (misal: <code>Panthera Tracing Project TV.xlsx</code>)</p>

            <form action="{{ route('imports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="flex items-center justify-center w-full">
                    <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-dbl-green-light/20 hover:border-dbl-green transition-all">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-dbl-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="mb-1 text-sm font-semibold text-gray-700">Klik untuk upload atau drag and drop</p>
                            <p class="text-xs text-gray-500">Format yang didukung: XLSX, XLS (Maks. 20MB)</p>
                        </div>
                        <input id="dropzone-file" name="excel_file" type="file" class="hidden" accept=".xlsx, .xls" required />
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-dbl-green hover:bg-dbl-green-dark text-dbl-dark font-bold text-sm rounded-lg shadow-md transition-all flex items-center space-x-2">
                        <span>Mulai Impor Data</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>


        <!-- ==================== TABEL RIWAYAT BATCH IMPORT ==================== -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-800">Riwayat Batch Impor</h3>
                <span class="text-xs text-gray-400">Total Batch: {{ $importHistory->total() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3">Nama File</th>
                            <th class="px-6 py-3">Diupload Oleh</th>
                            <th class="px-6 py-3">Total Baris</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Waktu Impor</th>
                            <th class="px-6 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($importHistory as $batch)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-900 flex items-center space-x-2">
                                    <span>📄</span>
                                    <span>{{ $batch->file_name }}</span>
                                </td>
                                <td class="px-6 py-4">{{ $batch->user->name ?? 'Admin' }}</td>
                                <td class="px-6 py-4 font-bold text-gray-800">{{ number_format($batch->total_rows) }}</td>
                                <td class="px-6 py-4">
                                    @if($batch->status === 'completed')
                                        <span class="px-2.5 py-1 text-xs font-bold text-emerald-800 bg-emerald-100 rounded-full">Completed</span>
                                    @elseif($batch->status === 'processing')
                                        <span class="px-2.5 py-1 text-xs font-bold text-blue-800 bg-blue-100 rounded-full animate-pulse">Processing</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-bold text-rose-800 bg-rose-100 rounded-full">Failed</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">{{ $batch->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">{{ $batch->notes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                    Belum ada riwayat impor file Excel.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100">
                {{ $importHistory->links() }}
            </div>
        </div>

    </div>
</x-app-layout>