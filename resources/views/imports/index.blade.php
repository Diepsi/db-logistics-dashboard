<x-app-layout>
    <x-slot name="header">
        Import Data Excel Operasional
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


        <!-- ==================== FORM UPLOAD EXCEL ==================== -->
        <div class="card p-6" x-reveal>
            <div class="flex items-center gap-2.5 mb-1">
                <span class="icon-chip bg-dbl-green-light/60 text-dbl-green-dark">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </span>
                <h3 class="text-base font-bold text-gray-800">Unggah File Raw Data (.xlsx / .xls)</h3>
            </div>
            <p class="text-xs text-gray-500 mb-5 ml-12">
                Pilih file hasil ekspor sistem/vendor DB Logistics (misal: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-[11px] font-semibold">Panthera Tracing Project TV.xlsx</code>).
                File akan divalidasi dan ditampilkan preview sebelum disimpan.
            </p>

            <form action="{{ route('imports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" x-data="{ dragging: false }">
                @csrf
                <div class="flex items-center justify-center w-full">
                    <label for="dropzone-file" 
                           :class="dragging ? 'border-dbl-green bg-dbl-green-light/20 scale-[1.005] shadow-glow' : 'border-gray-300 bg-gray-50 hover:border-dbl-green hover:bg-dbl-green-light/20'"
                           @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="dragging = false"
                           class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed rounded-2xl cursor-pointer transition-all duration-300">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 px-6 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-white border border-gray-200 shadow-sm flex items-center justify-center mb-3 transition-transform duration-300" :class="dragging ? 'scale-110 -rotate-6' : ''">
                                <svg class="w-7 h-7 text-dbl-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <p class="mb-1 text-sm font-bold text-gray-700" x-text="dragging ? 'Lepaskan file di sini' : 'Klik untuk upload atau drag and drop'"></p>
                            <p class="text-xs text-gray-500">Format yang didukung: XLSX, XLS (Maks. 20MB)</p>
                        </div>
                        <input id="dropzone-file" name="excel_file" type="file" class="hidden" accept=".xlsx, .xls" required
                               onchange="var f = this.files[0]; document.getElementById('selected-file-name').textContent = f ? f.name : ''; document.getElementById('selected-file-wrap').classList.toggle('hidden', !f); document.getElementById('no-file-hint').classList.toggle('hidden', !!f);" />
                    </label>
                </div>

                <div id="selected-file-wrap" class="hidden items-center gap-2.5 text-xs font-semibold text-dbl-green-dark bg-dbl-green-light/40 border border-dbl-green/20 rounded-lg px-3 py-2.5">
                    <span class="icon-chip !w-7 !h-7 bg-white text-dbl-green">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    File terpilih: <span id="selected-file-name" class="font-bold"></span>
                </div>
                <p id="no-file-hint" class="text-xs font-semibold text-amber-600 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Belum ada file yang dipilih. Silakan pilih file .xlsx / .xls terlebih dahulu.
                </p>

                <div class="flex justify-end pt-1">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                        Unggah &amp; Validasi File
                    </button>
                </div>
            </form>
        </div>


        <!-- ==================== TABEL RIWAYAT BATCH IMPORT ==================== -->
        <div class="card overflow-hidden" x-reveal>
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
                <div class="flex items-center gap-2.5">
                    <span class="icon-chip !w-8 !h-8 bg-blue-100 text-blue-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <h3 class="text-base font-bold text-gray-800">Riwayat Batch Impor</h3>
                    <span class="text-xs text-gray-400 bg-white border border-gray-200 rounded-full px-2.5 py-0.5">Total Batch: {{ $importHistory->total() }}</span>
                </div>

                @if($importHistory->isNotEmpty())
                    <button type="button" x-data x-on:click="$dispatch('open-modal', 'clear-data-modal')"
                            class="btn-danger !px-3.5 !py-2 !text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Bersihkan Semua Data
                    </button>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200 sticky top-0">
                        <tr>
                            <th class="px-6 py-3 font-bold">Nama File</th>
                            <th class="px-6 py-3 font-bold">Diupload Oleh</th>
                            <th class="px-6 py-3 font-bold">Baris Valid</th>
                            <th class="px-6 py-3 font-bold">Baru / Update</th>
                            <th class="px-6 py-3 font-bold">Status</th>
                            <th class="px-6 py-3 font-bold">Waktu Impor</th>
                            <th class="px-6 py-3 font-bold">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($importHistory as $batch)
                            <tr class="hover:bg-dbl-green-light/20 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    <div class="flex items-center gap-2.5">
                                        <span class="icon-chip !w-8 !h-8 bg-gray-100 text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                        </span>
                                        <span>{{ $batch->file_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ $batch->user->name ?? 'Admin' }}</td>
                                <td class="px-6 py-4 font-bold text-gray-800 tabular-nums">
                                    {{ number_format($batch->valid_rows) }}
                                    <span class="text-[10px] font-normal text-gray-400">/ {{ number_format($batch->total_rows) }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    <span class="badge bg-emerald-50 text-emerald-700"><span class="dot bg-emerald-500"></span>{{ number_format($batch->new_rows) }} baru</span>
                                    <span class="badge bg-blue-50 text-blue-700"><span class="dot bg-blue-500"></span>{{ number_format($batch->updated_rows) }} update</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($batch->status === 'completed')
                                        <span class="badge bg-emerald-50 text-emerald-700"><span class="dot bg-emerald-500"></span>Completed</span>
                                    @elseif(in_array($batch->status, ['processing', 'preview'], true))
                                        <span class="badge bg-blue-50 text-blue-700"><span class="dot bg-blue-500 animate-pulse-soft"></span>{{ ucfirst($batch->status) }}</span>
                                    @else
                                        <span class="badge bg-rose-50 text-rose-700"><span class="dot bg-rose-500"></span>Failed</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">{{ $batch->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">{{ $batch->notes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="icon-chip bg-gray-100 text-gray-400 mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-500">Belum ada riwayat impor file Excel</p>
                                        <p class="text-xs text-gray-400 mt-1">Unggah file raw data di atas untuk memulai.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $importHistory->links() }}
            </div>
        </div>

    </div>

    <!-- Modal Konfirmasi Bersihkan Semua Data -->
    <x-modal name="clear-data-modal" maxWidth="md">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-11 h-11 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Bersihkan Semua Data?</h3>
                    <p class="text-xs text-gray-500">Tindakan ini akan menghapus permanen semua data hasil import.</p>
                </div>
            </div>

            <ul class="text-xs text-gray-500 list-disc pl-5 mb-5 space-y-1.5">
                <li>Semua data pengiriman (shipments)</li>
                <li>Riwayat batch import</li>
                <li>Issue pengiriman (Undelivered)</li>
                <li>Data vendor &amp; lokasi otomatis</li>
            </ul>
            <p class="text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-3.5 py-2.5 mb-5 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Tindakan ini tidak dapat dibatalkan. Dashboard akan kembali kosong.
            </p>

            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="btn-ghost">
                    Batal
                </button>
                <form action="{{ route('imports.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">
                        Ya, Bersihkan Semua
                    </button>
                </form>
            </div>
        </div>
    </x-modal>
</x-app-layout>
