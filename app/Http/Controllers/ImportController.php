<?php

namespace App\Http\Controllers;

use App\Exceptions\ImportException;
use App\Models\ImportBatch;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentIssue;
use App\Models\Vendor;
use App\Services\IssueImportService;
use App\Services\SlaAllImportService;
use App\Services\SlaLastMileImportService;
use App\Services\SlaMiddleMileImportService;
use App\Services\InboundFirstMileImportService;
use App\Services\ShipmentImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    /**
     * Daftar module → service class.
     */
    private const MODULE_SERVICES = [
        'shipment' => ShipmentImportService::class,
        'issue' => IssueImportService::class,
        'sla-mm' => SlaMiddleMileImportService::class,
        'sla-lm' => SlaLastMileImportService::class,
        'sla-all' => SlaAllImportService::class,
        'inbound-fm' => InboundFirstMileImportService::class,
    ];

    /**
     * Daftar module → label untuk UI.
     */
    private const MODULE_LABELS = [
        'shipment' => 'Data Pengiriman',
        'issue' => 'Issue Pengiriman',
        'sla-mm' => 'SLA Middle Mile',
        'sla-lm' => 'SLA Last Mile',
        'sla-all' => 'SLA All',
        'inbound-fm' => 'Inbound / First Mile',
    ];

    /**
     * Resolve service class berdasarkan nama module.
     */
    private function resolveService(string $module): object
    {
        $serviceClass = self::MODULE_SERVICES[$module] ?? null;

        if ($serviceClass === null) {
            abort(404, 'Modul import tidak dikenali: '.$module);
        }

        return app($serviceClass);
    }

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
     * Langkah 2: Konfirmasi -> Eksekusi batch + upsert ke MySQL
     */
    public function process(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $fileName = $request->session()->pull('import_file_name') ?? 'raw_data.xlsx';

        DB::beginTransaction();

        try {
            $batch = ImportBatch::create([
                'file_name' => $fileName,
                'uploaded_by' => auth()->id(),
                'status' => 'processing',
                'notes' => 'Memproses impor file Excel.',
            ]);

            $result = app(ShipmentImportService::class)->process($request->token, $batch->id);

            $batch->update([
                'total_rows' => $result['total'],
                'valid_rows' => $result['valid'],
                'invalid_rows' => $result['invalid'],
                'duplicate_rows' => $result['duplicate'],
                'new_rows' => $result['new_rows'],
                'updated_rows' => $result['updated_rows'],
                'status' => 'completed',
                'notes' => sprintf(
                    'Import selesai: %d baru, %d diperbarui, %d tidak valid, %d duplikat.',
                    $result['new_rows'],
                    $result['updated_rows'],
                    $result['invalid'],
                    $result['duplicate']
                ),
            ]);

            DB::commit();

            return redirect()->route('imports.index')->with(
                'success',
                sprintf(
                    'File %s berhasil diimpor! (%d baru, %d diperbarui)',
                    $fileName,
                    $result['new_rows'],
                    $result['updated_rows']
                )
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            if (isset($batch)) {
                $batch->update([
                    'status' => 'failed',
                    'notes' => 'Gagal diproses: '.$e->getMessage(),
                ]);
            }

            return redirect()->route('imports.index')->with('error', 'Terjadi kesalahan saat mengimpor file: '.$e->getMessage());
        }
    }

    /**
     * Hapus semua data hasil upload (shipments, issues, batch, vendor, lokasi).
     * Hanya boleh dilakukan oleh Admin.
     */
    public function clear()
    {
        DB::beginTransaction();

        try {
            ShipmentIssue::query()->delete();
            Shipment::query()->delete();
            ImportBatch::query()->delete();
            Vendor::query()->delete();
            Location::query()->delete();

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
