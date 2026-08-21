<?php

namespace App\Http\Controllers;

use App\Exceptions\ImportException;
use App\Jobs\ProcessImportJob;
use App\Models\ImportBatch;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentIssue;
use App\Models\Vendor;
use App\Services\ShipmentImportService;
use Illuminate\Http\Request;
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
            ->paginate(10)
            ->withQueryString();

        $activeBatches = ImportBatch::query()
            ->whereIn('status', ['pending', 'processing'])
            ->orderByDesc('created_at')
            ->get();

        return view('imports.index', compact('importHistory', 'activeBatches'));
    }

    /**
     * Langkah 1: Upload file -> Validasi & Preview ringkasan baris valid/invalid
     */
    public function store(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'max:20480', 'extensions:xlsx,xls'],
        ]);

        $file = $request->file('excel_file');

        try {
            $preview = app(ShipmentImportService::class)->preview($file);
        } catch (ImportException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'File tidak dapat diproses: '.$e->getMessage());
        }

        $request->session()->put('import_file_name', $file->getClientOriginalName());

        return view('imports.preview', compact('preview'));
    }

    /**
     * Langkah 2: Konfirmasi -> Buat batch pending & dispatch background job.
     * Pemrosesan berat dieksekusi oleh queue worker (ProcessImportJob).
     */
    public function process(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $fileName = $request->session()->pull('import_file_name') ?? 'raw_data.xlsx';

        $batch = ImportBatch::create([
            'file_name' => $fileName,
            'uploaded_by' => auth()->id(),
            'status' => 'pending',
            'notes' => 'Menunggu diambil oleh queue worker.',
        ]);

        ProcessImportJob::dispatch($request->token, $batch->id, $fileName);

        return redirect()->route('imports.index')->with(
            'success',
            "File {$fileName} masuk antrean pemrosesan. Pantau progresnya pada panel status impor."
        );
    }

    /**
     * Endpoint polling JSON untuk progress bar real-time.
     */
    public function progress(ImportBatch $batch)
    {
        $percentage = match (true) {
            $batch->status === 'completed' => 100,
            $batch->total_rows > 0 => min(100, (int) floor(($batch->processed_rows / $batch->total_rows) * 100)),
            default => 0,
        };

        return response()->json([
            'id' => $batch->id,
            'file_name' => $batch->file_name,
            'status' => $batch->status,
            'total_rows' => $batch->total_rows,
            'processed_rows' => $batch->processed_rows,
            'failed_rows' => $batch->failed_rows,
            'valid_rows' => $batch->valid_rows,
            'new_rows' => $batch->new_rows,
            'updated_rows' => $batch->updated_rows,
            'percentage' => $percentage,
            'notes' => $batch->notes,
        ]);
    }

    /**
     * Hapus semua data hasil upload (shipments, issues, batch, vendor, lokasi).
     * Hanya boleh dilakukan oleh Admin.
     */
    public function clear()
    {
        DB::beginTransaction();

        try {
            ShipmentIssue::query()->forceDelete();
            Shipment::query()->forceDelete();
            ImportBatch::query()->forceDelete();
            Vendor::query()->forceDelete();
            Location::query()->forceDelete();

            DB::commit();

            return redirect()->route('imports.index')->with(
                'success',
                'Semua data hasil import berhasil dibersihkan. Dashboard kembali kosong.'
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->route('imports.index')->with('error', 'Gagal membersihkan data: '.$e->getMessage());
        }
    }
}
