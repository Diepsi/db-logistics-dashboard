<?php

namespace Tests\Feature;

use App\Models\ImportBatch;
use App\Models\Role;
use App\Models\SlaMiddleMile;
use App\Models\Shipment;
use App\Models\ShipmentIssue;
use App\Models\User;
use App\Services\AnomalyDetectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AnomalyDetectionTest extends TestCase
{
    use RefreshDatabase;

    private ImportBatch $batch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->batch = ImportBatch::create([
            'file_name' => 'test.xlsx',
            'uploaded_by' => User::factory()->create()->id,
            'status' => 'completed',
        ]);
    }

    private function createShipment(array $attributes = []): Shipment
    {
        return Shipment::create(array_merge([
            'import_batch_id' => $this->batch->id,
            'waybill_no' => 'AWB'.str_pad((string) Shipment::count(), 8, '0', STR_PAD_LEFT),
            'final_status' => 'On Delivery',
        ], $attributes));
    }

    public function test_stuck_transit_shipment_creates_auto_issue(): void
    {
        $shipment = $this->createShipment([
            'ho_date' => Carbon::now()->subDays(10),
        ]);

        $result = app(AnomalyDetectionService::class)->detectAll();

        $this->assertSame(1, $result['stuck_transit']);
        $this->assertDatabaseHas('shipment_issues', [
            'shipment_id' => $shipment->id,
            'issue_type' => AnomalyDetectionService::TYPE_STUCK_TRANSIT,
            'status' => 'open',
        ]);
    }

    public function test_fresh_shipment_is_not_flagged(): void
    {
        $this->createShipment([
            'ho_date' => Carbon::now()->subDays(2),
        ]);

        $result = app(AnomalyDetectionService::class)->detectAll();

        $this->assertSame(0, $result['stuck_transit']);
        $this->assertDatabaseCount('shipment_issues', 0);
    }

    public function test_completed_shipment_is_not_flagged(): void
    {
        $this->createShipment([
            'ho_date' => Carbon::now()->subDays(30),
            'final_status' => 'Completed',
        ]);

        $result = app(AnomalyDetectionService::class)->detectAll();

        $this->assertSame(0, $result['stuck_transit']);
        $this->assertDatabaseCount('shipment_issues', 0);
    }

    public function test_rerun_does_not_duplicate_open_issues(): void
    {
        $this->createShipment([
            'ho_date' => Carbon::now()->subDays(15),
        ]);

        $service = app(AnomalyDetectionService::class);
        $service->detectAll();
        $service->detectAll();

        $this->assertDatabaseCount('shipment_issues', 1);
    }

    public function test_middle_mile_overdue_creates_issue(): void
    {
        $shipment = $this->createShipment();

        SlaMiddleMile::create([
            'import_batch_id' => $this->batch->id,
            'waybill_no' => $shipment->waybill_no,
            'eta_mm' => Carbon::now()->subDays(3),
            'tgl_sampai_kota_tujuan' => null,
        ]);

        $result = app(AnomalyDetectionService::class)->detectAll();

        $this->assertSame(1, $result['middle_mile_overdue']);
        $this->assertDatabaseHas('shipment_issues', [
            'shipment_id' => $shipment->id,
            'issue_type' => AnomalyDetectionService::TYPE_MIDDLE_MILE_OVERDUE,
            'status' => 'open',
        ]);
    }

    public function test_middle_mile_arrived_shipment_is_not_flagged(): void
    {
        $shipment = $this->createShipment();

        SlaMiddleMile::create([
            'import_batch_id' => $this->batch->id,
            'waybill_no' => $shipment->waybill_no,
            'eta_mm' => Carbon::now()->subDays(3),
            'tgl_sampai_kota_tujuan' => Carbon::now()->subDay(),
        ]);

        $result = app(AnomalyDetectionService::class)->detectAll();

        $this->assertSame(0, $result['middle_mile_overdue']);
        $this->assertDatabaseCount('shipment_issues', 0);
    }

    public function test_anomalies_command_runs_successfully(): void
    {
        $this->createShipment([
            'ho_date' => Carbon::now()->subDays(9),
        ]);

        $this->artisan('anomalies:detect')->assertSuccessful();

        $this->assertDatabaseHas('shipment_issues', [
            'issue_type' => AnomalyDetectionService::TYPE_STUCK_TRANSIT,
        ]);
    }

    public function test_shipment_show_page_renders_lifecycle_timeline(): void
    {
        $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        $shipment = $this->createShipment([
            'ho_date' => Carbon::now()->subDays(2),
            'school_name' => 'SMA Negeri 1 Contoh',
        ]);

        $this->actingAs($admin)
            ->get(route('shipments.show', $shipment->id))
            ->assertOk()
            ->assertSee('Timeline Pengiriman')
            ->assertSee('Order Created')
            ->assertSee('First Mile Inbound')
            ->assertSee('Middle Mile Linehaul')
            ->assertSee('Last Mile Hub')
            ->assertSee($shipment->waybill_no);
    }
}
