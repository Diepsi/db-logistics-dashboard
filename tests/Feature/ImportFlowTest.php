<?php

namespace Tests\Feature;

use App\Models\ImportBatch;
use App\Models\Location;
use App\Models\Role;
use App\Models\Shipment;
use App\Models\ShipmentIssue;
use App\Models\User;
use App\Models\Vendor;
use App\Services\ImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ImportFlowTest extends TestCase
{
    use RefreshDatabase;

    private const HEADERS = [
        'No Resi', 'NPSN', 'No Manifest', 'Vendor LM', 'Provinsi', 'Kabupaten/Kota',
        'Tgl HO dari SarTrans', 'ETA Pickup', 'SLA Pickup', 'Result Pickup for PANTHERA',
        'ETA Delivery', 'SLA', 'Result Delivery for PANTHERA', 'SLA LM', 'Result LM',
        'SLA FOR VENDOR', 'Result For Vendor', 'Status Update', 'Status Akhir',
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

    protected function validRows(int $count): array
    {
        $rows = [self::HEADERS];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'SHP-'.str_pad((string) $i, 5, '0', STR_PAD_LEFT), "NPSN-$i", "MNF-$i", 'Vendor Synth',
                'Provinsi X', 'Kota Y', '15/01/2026', '16/01/2026', 'On Time', 'Berhasil Pickup',
                '17/01/2026', 'On Time', 'Berhasil Dikirim', 'On Time', 'OK', 'On Time', 'OK',
                'Selesai', 'Delivered',
            ];
        }

        return $rows;
    }

    public function test_upload_rejects_file_without_raw_data_sheet(): void
    {
        $user = $this->createAdmin();
        $path = tempnam(sys_get_temp_dir(), 'imp').'.xlsx';
        $this->makeWorkbook($path, [
            'DASHBOARD' => $this->validRows(2),
            'SUMMARY' => $this->validRows(1),
        ]);

        $file = new UploadedFile($path, 'Panthera.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $this->actingAs($user)
            ->post(route('imports.store'), ['excel_file' => $file])
            ->assertRedirect()
            ->assertSessionHas('error', "Sheet 'RAW DATA' tidak ditemukan pada file Excel yang diunggah.");
    }

    public function test_upload_rejects_when_required_header_is_missing(): void
    {
        $user = $this->createAdmin();
        $path = tempnam(sys_get_temp_dir(), 'imp').'.xlsx';

        $headers = self::HEADERS;
        unset($headers[0]); // hilangkan 'No Resi'

        $this->makeWorkbook($path, [
            'RAW DATA' => [array_values($headers), ['SHP-00001', 'NPSN-1']],
        ]);

        $file = new UploadedFile($path, 'Panthera.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $this->actingAs($user)
            ->post(route('imports.store'), ['excel_file' => $file])
            ->assertRedirect()
            ->assertSessionHas('error', 'Header kolom wajib tidak lengkap. Kolom berikut tidak ditemukan: no_resi');
    }

    public function test_upload_rejects_unsupported_extension(): void
    {
        $user = $this->createAdmin();
        $file = UploadedFile::fake()->create('data.txt', 10, 'text/plain');

        $this->actingAs($user)
            ->post(route('imports.store'), ['excel_file' => $file])
            ->assertSessionHasErrors('excel_file');
    }

    public function test_preview_process_and_upsert_uses_raw_data_sheet(): void
    {
        $user = $this->createAdmin();
        $path = tempnam(sys_get_temp_dir(), 'imp').'.xlsx';

        $this->makeWorkbook($path, [
            'DASHBOARD' => [['KPI', 'Nilai'], ['Total', '999']],
            'RAW DATA' => $this->validRows(3),
            'SUMMARY' => [['Ringkasan', 'x']],
        ]);

        $file = new UploadedFile($path, 'Panthera.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
        $service = app(ImportService::class);

        $preview = $service->preview($file);
        $this->assertSame(3, $preview['total']);
        $this->assertSame(3, $preview['valid']);
        $this->assertSame(0, $preview['invalid']);
        $this->assertSame(0, $preview['duplicate']);

        $batch = ImportBatch::create([
            'file_name' => 'Panthera.xlsx',
            'uploaded_by' => $user->id,
            'status' => 'processing',
        ]);

        $result = $service->process($preview['token'], $batch->id);
        $this->assertSame(3, $result['new_rows']);
        $this->assertSame(0, $result['updated_rows']);
        $this->assertSame(3, Shipment::count());

        $row = Shipment::where('waybill_no', 'SHP-00001')->first();
        $this->assertSame('2026-01-15 00:00:00', DB::table('shipments')->where('id', $row->id)->value('ho_date'));
        $this->assertSame('Vendor Synth', $row->vendor_lm);
        $this->assertSame('Completed', $row->final_status);

        // Re-import: upsert by waybill_no -> updated, bukan baru
        $preview2 = $service->preview($file);
        $batch2 = ImportBatch::create([
            'file_name' => 'Panthera.xlsx',
            'uploaded_by' => $user->id,
            'status' => 'processing',
        ]);

        $result2 = $service->process($preview2['token'], $batch2->id);
        $this->assertSame(0, $result2['new_rows']);
        $this->assertSame(3, $result2['updated_rows']);
        $this->assertSame(3, Shipment::count());
    }

    public function test_admin_can_clear_all_imported_data(): void
    {
        $user = $this->createAdmin();

        $batch = ImportBatch::create([
            'file_name' => 'Panthera.xlsx',
            'uploaded_by' => $user->id,
            'status' => 'completed',
            'total_rows' => 1,
            'valid_rows' => 1,
        ]);

        $vendor = Vendor::create(['name' => 'Vendor Synth']);
        $location = Location::create(['province' => 'Provinsi X', 'city_regency' => 'Kota Y']);

        $shipment = Shipment::create([
            'import_batch_id' => $batch->id,
            'vendor_id' => $vendor->id,
            'location_id' => $location->id,
            'waybill_no' => 'SHP-00001',
            'final_status' => 'Undelivered',
            'is_within_sla' => false,
        ]);

        ShipmentIssue::create([
            'shipment_id' => $shipment->id,
            'issue_type' => 'undelivered',
            'status' => 'open',
        ]);

        $this->assertSame(1, Shipment::count());
        $this->assertSame(1, ImportBatch::count());
        $this->assertSame(1, Vendor::count());
        $this->assertSame(1, Location::count());
        $this->assertSame(1, ShipmentIssue::count());

        $this->actingAs($user)
            ->delete(route('imports.clear'))
            ->assertRedirect(route('imports.index'))
            ->assertSessionHas('success');

        $this->assertSame(0, Shipment::count());
        $this->assertSame(0, ImportBatch::count());
        $this->assertSame(0, Vendor::count());
        $this->assertSame(0, Location::count());
        $this->assertSame(0, ShipmentIssue::count());

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('roles', 1);
    }

    public function test_non_admin_cannot_clear_data(): void
    {
        $role = Role::create(['name' => 'User', 'slug' => 'user']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user)
            ->delete(route('imports.clear'))
            ->assertForbidden();
    }
}
