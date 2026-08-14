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

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl">
                <p class="text-sm font-semibold mb-1">Validasi gagal:</p>
                <ul class="text-xs list-disc pl-5 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <!-- ==================== FORM UPLOAD EXCEL ==================== -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <h3 class="text-base font-bold text-gray-800 mb-1">Unggah File Raw Data (.xlsx / .xls)</h3>
            <p class="text-xs text-gray-500 mb-4">
                Pilih file hasil ekspor sistem/vendor DB Logistics (misal: <code>Panthera Tracing Project TV.xlsx</code>).
                File akan divalidasi dan ditampilkan preview sebelum disimpan.
            </p>

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
                        <input id="dropzone-file" name="excel_file" type="file" class="hidden" accept=".xlsx, .xls" required
                               onchange="var f = this.files[0]; document.getElementById('selected-file-name').textContent = f ? f.name : ''; document.getElementById('selected-file-wrap').classList.toggle('hidden', !f); document.getElementById('no-file-hint').classList.toggle('hidden', !!f);" />
                    </label>
                </div>

                <div id="selected-file-wrap" class="hidden text-xs font-semibold text-dbl-green-dark bg-dbl-green-light/40 border border-dbl-green/20 rounded-lg px-3 py-2">
                    File terpilih: <span id="selected-file-name"></span>
                </div>
                <p id="no-file-hint" class="text-xs text-amber-600">Belum ada file yang dipilih. Silakan pilih file .xlsx / .xls terlebih dahulu.</p>

                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-dbl-green hover:bg-dbl-green-dark text-dbl-dark font-bold text-sm rounded-lg shadow-md transition-all flex items-center space-x-2">
                        <span>Unggah & Validasi File</span>
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
                <div class="flex items-center space-x-3">
                    <h3 class="text-base font-bold text-gray-800">Riwayat Batch Impor</h3>
                    <span class="text-xs text-gray-400">Total Batch: {{ $importHistory->total() }}</span>
                </div>

                @if($importHistory->isNotEmpty())
                    <button type="button" x-data x-on:click="$dispatch('open-modal', 'clear-data-modal')"
                            class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Bersihkan Semua Data</span>
                    </button>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3">Nama File</th>
                            <th class="px-6 py-3">Diupload Oleh</th>
                            <th class="px-6 py-3">Baris Valid</th>
                            <th class="px-6 py-3">Baru / Update</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Waktu Impor</th>
                            <th class="px-6 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($importHistory as $batch)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    <div class="flex items-center space-x-2">
                                        <span>📄</span>
                                        <span>{{ $batch->file_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ $batch->user->name ?? 'Admin' }}</td>
                                <td class="px-6 py-4 font-bold text-gray-800">
                                    {{ number_format($batch->valid_rows) }}
                                    <span class="text-[10px] font-normal text-gray-400">/ {{ number_format($batch->total_rows) }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    <span class="font-semibold text-emerald-700">{{ number_format($batch->new_rows) }} baru</span>
                                    <span class="text-gray-400">·</span>
                                    <span class="font-semibold text-blue-700">{{ number_format($batch->updated_rows) }} update</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($batch->status === 'completed')
                                        <span class="px-2.5 py-1 text-xs font-bold text-emerald-800 bg-emerald-100 rounded-full">Completed</span>
                                    @elseif(in_array($batch->status, ['processing', 'preview'], true))
                                        <span class="px-2.5 py-1 text-xs font-bold text-blue-800 bg-blue-100 rounded-full animate-pulse">{{ ucfirst($batch->status) }}</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-bold text-rose-800 bg-rose-100 rounded-full">Failed</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">{{ $batch->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">{{ $batch->notes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-400">
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

    <!-- Modal Konfirmasi Bersihkan Semua Data -->
    <x-modal name="clear-data-modal" maxWidth="md">
        <div class="p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Bersihkan Semua Data?</h3>
            </div>

            <p class="text-sm text-gray-600 mb-2">
                Tindakan ini akan menghapus <strong>permanen</strong> semua data hasil import:
            </p>
            <ul class="text-xs text-gray-500 list-disc pl-5 mb-5 space-y-1">
                <li>Semua data pengiriman (shipments)</li>
                <li>Riwayat batch import</li>
                <li>Issue pengiriman (Undelivered)</li>
                <li>Data vendor &amp; lokasi otomatis</li>
            </ul>
            <p class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-5">
                ⚠️ Tindakan ini tidak dapat dibatalkan. Dashboard akan kembali kosong.
            </p>

            <div class="flex justify-end space-x-3">
                <button type="button" x-on:click="$dispatch('close')"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-lg transition-all">
                    Batal
                </button>
                <form action="{{ route('imports.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-lg shadow-sm transition-all">
                        Ya, Bersihkan Semua
                    </button>
                </form>
            </div>
        </div>
    </x-modal>
</x-app-layout>
