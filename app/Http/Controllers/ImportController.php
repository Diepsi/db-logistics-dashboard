<?php

namespace App\Http\Controllers;

use App\Models\ImportBatch;
use App\Imports\ShipmentsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    /**
     * Tampilkan Halaman Import & Riwayat Upload
     */
    public function index()
    {
        $importHistory = ImportBatch::with('user')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('imports.index', compact('importHistory'));
    }

    /**
     * Proses Upload File Excel RAW DATA
     */
    public function store(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:20480', // Max 20MB
        ]);

        $file = $request->file('excel_file');
        $fileName = $file->getClientOriginalName();

        DB::beginTransaction();
        try {
            // 1. Buat Record Batch Import
            $batch = ImportBatch::create([
                'file_name'     => $fileName,
                'uploaded_by'   => auth()->id() ?? 1,
                'total_rows'    => 0,
                'valid_rows'    => 0,
                'invalid_rows'  => 0,
                'status'        => 'processing',
                'notes'         => 'Proses impor file Excel dimulai.',
            ]);

            // 2. Eksekusi Import menggunakan Class ShipmentsImport
            Excel::import(new ShipmentsImport($batch->id), $file);

            // 3. Update Status Batch Setelah Selesai
            $totalImported = $batch->shipments()->count();
            
            $batch->update([
                'total_rows' => $totalImported,
                'valid_rows' => $totalImported,
                'status'     => 'completed',
                'notes'      => 'Impor data berhasil diproses sepenuhnya.',
            ]);

            DB::commit();

            return redirect()->route('imports.index')
                ->with('success', "File {$fileName} berhasil diimpor! Total {$totalImported} resi berhasil disimpan.");

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($batch)) {
                $batch->update([
                    'status' => 'failed',
                    'notes'  => 'Gagal diproses: ' . $e->getMessage(),
                ]);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }
}