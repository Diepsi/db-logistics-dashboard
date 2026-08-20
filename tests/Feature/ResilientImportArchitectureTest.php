<?php

namespace Tests\Feature;

use App\Imports\ShipmentImport;
use App\Models\ImportBatch;
use App\Models\SlaMiddleMile;
use App\Models\SlaLastMile;
use App\Models\SlaAll;
use App\Models\InboundFirstMile;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Role;
use App\Services\ImportService;
use App\Services\ShipmentImportService;
use App\Services\SlaMiddleMileImportService;
use App\Support\Contracts\ImportNormalizer;
use App\Support\ShipmentRowNormalizer;
use App\Support\IssueRowNormalizer;
use App\Support\SlaMiddleMileRowNormalizer;
use App\Support\SlaLastMileRowNormalizer;
use App\Support\SlaAllRowNormalizer;
use App\Support\InboundFirstMileRowNormalizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ResilientImportArchitectureTest extends TestCase
{
    use RefreshDatabase;

    private const SHIPMENT_REQUIRED_HEADERS = [
        'No Resi', 'NPSN', 'No Manifest', 'Vendor LM', 'Provinsi', 'Kabupaten/Kota',
        'Tgl HO dari SarTrans', 'ETA Delivery', 'SLA', 'Result Delivery for DB',
        'SLA LM', 'Result LM', 'Status Update', 'Status Akhir',
    ];

    protected function createAdmin(): User
    {
        $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);

        return User::factory()->create(['role_id' => $role->id]);
    }

    protected function makeWorkbook(string $path, array $sheets): void
    {
        $spreadsheet = new Spreadsheet;
        $first = true;

        foreach ($sheets as $title => $rows) {
            $sheet = $first ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $sheet->setTitle($title);
            $sheet->fromArray($rows, null, 'A1');
            $first = false;
        }

        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();
    }

    protected function shipmentRow(array $overrides = []): array
    {
        $base = [
            'SHP-00001', 'NPSN-1', 'MNF-1', 'Vendor Synth', 'Provinsi X', 'Kota Y',
            '15/01/2026', '18/01/2026', 'On Time', 'Berhasil Dikirim',
            'On Time', 'OK', 'Selesai', 'Delivered',
        ];

        foreach ($overrides as $index => $value) {
            $base[$index] = $value;
        }

        return $base;
    }

    // ================================================================
    // INTERFACE & CONTRACT TESTS
    // ================================================================

    public function test_shipment_normalizer_implements_import_normalizer(): void
    {
        $this->assertInstanceOf(ImportNormalizer::class, new ShipmentRowNormalizer());

        $reflection = new \ReflectionClass(ShipmentRowNormalizer::class);
        $constants = $reflection->getConstants();

        $this->assertArrayHasKey('REQUIRED_HEADERS', $constants);
        $this->assertArrayHasKey('HEADER_ALIASES', $constants);
    }

    public function test_all_normalizers_implement_import_normalizer(): void
    {
        $normalizers = [
            ShipmentRowNormalizer::class,
            IssueRowNormalizer::class,
            SlaMiddleMileRowNormalizer::class,
            SlaLastMileRowNormalizer::class,
            SlaAllRowNormalizer::class,
            InboundFirstMileRowNormalizer::class,
        ];

        foreach ($normalizers as $class) {
            $this->assertTrue(is_a($class, ImportNormalizer::class, true), "{$class} must implement ImportNormalizer");

            $reflection = new \ReflectionClass($class);
            $constants = $reflection->getConstants();

            $this->assertArrayHasKey('REQUIRED_HEADERS', $constants, "{$class} must define REQUIRED_HEADERS");
            $this->assertArrayHasKey('OPTIONAL_HEADERS', $constants, "{$class} must define OPTIONAL_HEADERS");
            $this->assertArrayHasKey('HEADER_ALIASES', $constants, "{$class} must define HEADER_ALIASES");
            $this->assertArrayHasKey('OPTIONAL_HEADER_ALIASES', $constants, "{$class} must define OPTIONAL_HEADER_ALIASES");
        }
    }

    // ================================================================
    // NORMALIZER CANONICAL KEY TESTS
    // ================================================================

    public function test_issue_normalizer_canonical_key_maps_correctly(): void
    {
        $this->assertSame('no_resi', IssueRowNormalizer::canonicalKey('Nomor Resi'));
        $this->assertSame('no_resi', IssueRowNormalizer::canonicalKey('Nomor Surat'));
        $this->assertSame('issue_type', IssueRowNormalizer::canonicalKey('Tipe Issue'));
        $this->assertSame('description', IssueRowNormalizer::canonicalKey('Keterangan'));
        $this->assertSame('status', IssueRowNormalizer::canonicalKey('Status Tiket'));
        $this->assertNull(IssueRowNormalizer::canonicalKey('Kolom Asal'));
    }

    public function test_sla_mm_normalizer_canonical_key_maps_correctly(): void
    {
        $this->assertSame('no_resi', SlaMiddleMileRowNormalizer::canonicalKey('Nomor Redock'));
        $this->assertSame('vendor_mm', SlaMiddleMileRowNormalizer::canonicalKey('Vendor MM'));
        $this->assertSame('eta_mm', SlaMiddleMileRowNormalizer::canonicalKey('ETA MM'));
        $this->assertSame('sla_mm', SlaMiddleMileRowNormalizer::canonicalKey('SLA MM'));
        $this->assertSame('result_mm', SlaMiddleMileRowNormalizer::canonicalKey('Result MM'));
        $this->assertSame('tgl_sampai_kota_tujuan', SlaMiddleMileRowNormalizer::canonicalKey('Tgl Sampai'));
        $this->assertSame('tgl_sampai_kota_tujuan', SlaMiddleMileRowNormalizer::canonicalKey('Tgl Sampai Kota Tujuan'));
        $this->assertNull(SlaMiddleMileRowNormalizer::canonicalKey('Kolom Tidak Dikenal'));
    }

    public function test_sla_lm_normalizer_canonical_key_maps_correctly(): void
    {
        $this->assertSame('no_resi', SlaLastMileRowNormalizer::canonicalKey('Delivery Order'));
        $this->assertSame('vendor_lm', SlaLastMileRowNormalizer::canonicalKey('Vendor LM'));
        $this->assertSame('eta_lm', SlaLastMileRowNormalizer::canonicalKey('ETA LM'));
        $this->assertSame('sla_lm', SlaLastMileRowNormalizer::canonicalKey('SLA LM'));
        $this->assertSame('result_lm', SlaLastMileRowNormalizer::canonicalKey('Result LM'));
        $this->assertNull(SlaLastMileRowNormalizer::canonicalKey('Kolom Asal'));
    }

    public function test_sla_all_normalizer_canonical_key_maps_correctly(): void
    {
        $this->assertSame('no_resi', SlaAllRowNormalizer::canonicalKey('Nomor Resi'));
        $this->assertSame('sla_overall', SlaAllRowNormalizer::canonicalKey('SLA Keseluruhan'));
        $this->assertSame('sla_overall', SlaAllRowNormalizer::canonicalKey('SLA ALL'));
        $this->assertSame('result_overall', SlaAllRowNormalizer::canonicalKey('Result Keseluruhan'));
        $this->assertSame('result_overall', SlaAllRowNormalizer::canonicalKey('Result ALL'));
        $this->assertNull(SlaAllRowNormalizer::canonicalKey('Kolom Sampah'));
    }

    public function test_inbound_fm_normalizer_canonical_key_maps_correctly(): void
    {
        $this->assertSame('no_resi', InboundFirstMileRowNormalizer::canonicalKey('Delivery Order'));
        $this->assertSame('manifest_no', InboundFirstMileRowNormalizer::canonicalKey('Manifest First Mile'));
        $this->assertSame('eta_pickup', InboundFirstMileRowNormalizer::canonicalKey('ETA Pickup'));
        $this->assertSame('status_inbound', InboundFirstMileRowNormalizer::canonicalKey('Status Inbound'));
        $this->assertSame('status_inbound', InboundFirstMileRowNormalizer::canonicalKey('FM Status'));
        $this->assertNull(InboundFirstMileRowNormalizer::canonicalKey('Kolom Asing'));
    }

    // ================================================================
    // EMPTY ROW DETECTION TESTS
    // ================================================================

    public function test_all_normalizers_detect_empty_rows(): void
    {
        $normalizers = [
            ShipmentRowNormalizer::class,
            IssueRowNormalizer::class,
            SlaMiddleMileRowNormalizer::class,
            SlaLastMileRowNormalizer::class,
            SlaAllRowNormalizer::class,
            InboundFirstMileRowNormalizer::class,
        ];

        foreach ($normalizers as $class) {
            $this->assertTrue($class::isEmptyRow([]), "{$class} should detect empty array");
            $this->assertTrue($class::isEmptyRow(['no_resi' => '']), "{$class} should detect empty no_resi");
            $this->assertTrue($class::isEmptyRow(['no_resi' => '   ']), "{$class} should detect whitespace no_resi");
            $this->assertFalse($class::isEmptyRow(['no_resi' => 'SHP-00001']), "{$class} should accept valid no_resi");
        }
    }

    // ================================================================
    // EXCEL STREAM READER KEY RESOLVER TESTS
    // ================================================================

    public function test_excel_stream_reader_accepts_custom_key_resolver(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'imp').'.xlsx';

        $headers = ['No Resi', 'Vendor LM', 'Provinsi'];
        $rows = [
            $headers,
            ['SHP-00001', 'Vendor A', 'DKI Jakarta'],
        ];

        $this->makeWorkbook($path, ['RAW DATA' => $rows]);

        $customResolver = fn (string $key) => strtoupper(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($key)));

        $outHeaders = [];
        $data = [];
        foreach (\App\Support\ExcelStreamReader::rows($path, $outHeaders, null, null, 'RAW DATA', $customResolver) as $row) {
            $data[] = $row;
        }

        $this->assertCount(1, $data);
        $this->assertArrayHasKey('NO_RESI', $data[0]);
        $this->assertArrayHasKey('VENDOR_LM', $data[0]);
        $this->assertArrayHasKey('PROVINSI', $data[0]);
        $this->assertSame('SHP-00001', $data[0]['NO_RESI']);

        @unlink($path);
    }

    public function test_excel_stream_reader_falls_back_to_raw_key_without_resolver(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'imp').'.xlsx';

        $headers = ['NoResi', 'VendorLM', 'Provinsi'];
        $rows = [
            $headers,
            ['SHP-00001', 'Vendor A', 'DKI Jakarta'],
        ];

        $this->makeWorkbook($path, ['RAW DATA' => $rows]);

        $outHeaders = [];
        $data = [];
        foreach (\App\Support\ExcelStreamReader::rows($path, $outHeaders, null, null, 'RAW DATA', null) as $row) {
            $data[] = $row;
        }

        $this->assertCount(1, $data);
        $this->assertArrayHasKey('noresi', $data[0]);
        $this->assertArrayHasKey('vendorlm', $data[0]);
        $this->assertArrayHasKey('provinsi', $data[0]);
        $this->assertSame('SHP-00001', $data[0]['noresi']);

        @unlink($path);
    }

    // ================================================================
    // SHIPMENT SERVICE ARCHITECTURE TEST
    // ================================================================

    public function test_shipment_import_service_uses_correct_sheet_candidates(): void
    {
        $this->assertSame(
            ['RAW DATA MM & LM', 'RAW DATA', 'DATA PENGIRIMAN'],
            ShipmentImportService::SHEET_CANDIDATES
        );
    }

    public function test_shipment_import_service_preview_uses_generic_service(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'imp').'.xlsx';
        $this->makeWorkbook($path, [
            'RAW DATA MM & LM' => [
                self::SHIPMENT_REQUIRED_HEADERS,
                $this->shipmentRow(),
            ],
        ]);

        $file = new UploadedFile($path, 'Test.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
        $service = app(ShipmentImportService::class);

        $preview = $service->preview($file);
        $this->assertSame(1, $preview['valid']);
        $this->assertSame(0, $preview['invalid']);

        @unlink($path);
    }

    // ================================================================
    // SHEET CANDIDATE REGISTRATION TESTS
    // ================================================================

    public function test_all_import_services_define_sheet_candidates(): void
    {
        $services = [
            ShipmentImportService::class => ['RAW DATA MM & LM', 'RAW DATA', 'DATA PENGIRIMAN'],
            \App\Services\IssueImportService::class => ['Database Issue', 'ISSUE KIRIMAN', 'ISSUE'],
            \App\Services\SlaMiddleMileImportService::class => ['Database SLA MM', 'SLA Middlemile', 'SLA MM'],
            \App\Services\SlaLastMileImportService::class => ['Database SLA LM', 'SLA Lastmile', 'SLA LM'],
            \App\Services\SlaAllImportService::class => ['Database SLA ALL', 'SLA ALL'],
            \App\Services\InboundFirstMileImportService::class => ['FM Inbound', 'Inventory Gudang Pusat'],
        ];

        foreach ($services as $class => $expectedCandidates) {
            $this->assertSame($expectedCandidates, $class::SHEET_CANDIDATES);
        }
    }

    // ================================================================
    // CROSS-SHEET RESILIENCE TEST
    // ================================================================

    public function test_shipment_import_resilient_to_sheet_name_variation(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'imp').'.xlsx';
        $this->makeWorkbook($path, [
            'DATA PENGIRIMAN' => [
                self::SHIPMENT_REQUIRED_HEADERS,
                $this->shipmentRow(['SHP-VAR-001']),
            ],
        ]);

        $file = new UploadedFile($path, 'Variant.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
        $service = app(ShipmentImportService::class);

        $preview = $service->preview($file);
        $this->assertSame(1, $preview['valid']);

        @unlink($path);
    }

    // ================================================================
    // MULTIPLE SHEET RESILIENCE TEST
    // ================================================================

    public function test_shipment_import_prefers_first_matching_sheet(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'imp').'.xlsx';
        $this->makeWorkbook($path, [
            'RAW DATA MM & LM' => [
                self::SHIPMENT_REQUIRED_HEADERS,
                $this->shipmentRow(['SHP-FIRST-001']),
            ],
            'RAW DATA' => [
                self::SHIPMENT_REQUIRED_HEADERS,
                $this->shipmentRow(['SHP-SECOND-002']),
            ],
        ]);

        $file = new UploadedFile($path, 'MultiSheet.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
        $service = app(ShipmentImportService::class);

        $preview = $service->preview($file);
        $this->assertSame(1, $preview['valid']);

        $user = $this->createAdmin();
        $batch = ImportBatch::create([
            'file_name' => 'MultiSheet.xlsx',
            'uploaded_by' => $user->id,
            'status' => 'processing',
        ]);

        $result = $service->process($preview['token'], $batch->id);
        $this->assertSame(1, $result['new_rows']);
        $this->assertTrue(Shipment::where('waybill_no', 'SHP-FIRST-001')->exists());
        $this->assertFalse(Shipment::where('waybill_no', 'SHP-SECOND-002')->exists());

        @unlink($path);
    }

    // ================================================================
    // DYNAMIC HEADER RESOLUTION VIA KEY RESOLVER
    // ================================================================

    public function test_key_resolver_is_passed_through_to_excel_stream_reader(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'imp').'.xlsx';
        $this->makeWorkbook($path, [
            'RAW DATA' => [
                ['No Resi', 'NPSN / Resi DB', 'Manifest First Mile', 'Vendor LM', 'Provinsi', 'Kota/Kab Tujuan', 'Tgl HO dari SarTrans', 'ETA Delivery', 'SLA for DB', 'Result Delivery for DB', 'SLA LM', 'Result LM', 'Status Update', 'Status Akhir'],
                ['SHP-ALIAS-001', 'NPSN-ALIAS', 'MNF-ALIAS-1', 'Vendor X', 'DKI Jakarta', 'Jakarta', '15/01/2026', '18/01/2026', 'On Time', 'Berhasil Dikirim', 'On Time', 'OK', 'Selesai', 'Delivered'],
            ],
        ]);

        $file = new UploadedFile($path, 'AliasHeaders.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
        $service = app(ShipmentImportService::class);

        $preview = $service->preview($file);
        $this->assertSame(1, $preview['valid']);

        $user = $this->createAdmin();
        $batch = ImportBatch::create([
            'file_name' => 'AliasHeaders.xlsx',
            'uploaded_by' => $user->id,
            'status' => 'processing',
        ]);

        $result = $service->process($preview['token'], $batch->id);
        $this->assertSame(1, $result['new_rows']);

        $row = Shipment::where('waybill_no', 'SHP-ALIAS-001')->first();
        $this->assertNotNull($row);
        $this->assertSame('MNF-ALIAS-1', $row->manifest_no);
        $this->assertSame('Jakarta', $row->city_regency);

        @unlink($path);
    }
}
