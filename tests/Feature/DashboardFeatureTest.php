<?php

namespace Tests\Feature;

use App\Models\ImportBatch;
use App\Models\Role;
use App\Models\Shipment;
use App\Models\ShipmentIssue;
use App\Models\User;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdmin(): User
    {
        $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);

        return User::factory()->create(['role_id' => $role->id]);
    }

    protected function createShipments(): void
    {
        $user = $this->createAdmin();
        $batch = ImportBatch::create([
            'file_name' => 'Panthera.xlsx',
            'uploaded_by' => $user->id,
            'total_rows' => 4,
            'status' => 'completed',
        ]);

        Shipment::create([
            'import_batch_id' => $batch->id,
            'waybill_no' => 'SHP-00001',
            'province' => 'Jawa Barat',
            'city_regency' => 'Bandung',
            'vendor_lm' => 'Vendor A',
            'ho_date' => Carbon::parse('2026-01-10 08:00:00'),
            'pickup_eta' => Carbon::parse('2026-01-11 08:00:00'),
            'delivery_eta' => Carbon::parse('2026-01-13 08:00:00'),
            'pickup_sla_status' => 'On Time',
            'delivery_sla_status' => 'On Time',
            'lm_sla_status' => 'OK',
            'vendor_sla_status' => 'On Time',
            'final_status' => 'Completed',
            'is_within_sla' => true,
        ]);

        Shipment::create([
            'import_batch_id' => $batch->id,
            'waybill_no' => 'SHP-00002',
            'province' => 'Jawa Barat',
            'city_regency' => 'Bogor',
            'vendor_lm' => 'Vendor A',
            'ho_date' => Carbon::parse('2026-01-10 09:00:00'),
            'pickup_eta' => Carbon::parse('2026-01-11 09:00:00'),
            'delivery_eta' => Carbon::parse('2026-01-13 09:00:00'),
            'pickup_sla_status' => 'On Time',
            'delivery_sla_status' => 'Late',
            'lm_sla_status' => 'OK',
            'vendor_sla_status' => 'Late',
            'final_status' => 'Completed',
            'is_within_sla' => false,
        ]);

        Shipment::create([
            'import_batch_id' => $batch->id,
            'waybill_no' => 'SHP-00003',
            'province' => 'DKI Jakarta',
            'city_regency' => 'Jakarta Selatan',
            'vendor_lm' => 'Vendor B',
            'ho_date' => Carbon::parse('2026-01-15 10:00:00'),
            'delivery_eta' => Carbon::parse('2026-01-20 10:00:00'),
            'pickup_sla_status' => 'Late',
            'delivery_sla_status' => 'On Time',
            'lm_sla_status' => 'OK',
            'vendor_sla_status' => 'On Time',
            'final_status' => 'Undelivered',
            'is_within_sla' => true,
        ]);

        Shipment::create([
            'import_batch_id' => $batch->id,
            'waybill_no' => 'SHP-00004',
            'province' => 'DKI Jakarta',
            'city_regency' => 'Jakarta Timur',
            'vendor_lm' => 'Vendor B',
            'ho_date' => Carbon::parse('2026-01-16 11:00:00'),
            'pickup_sla_status' => 'On Time',
            'delivery_sla_status' => 'On Time',
            'lm_sla_status' => 'OK',
            'vendor_sla_status' => 'On Time',
            'final_status' => 'On Delivery',
            'is_within_sla' => true,
        ]);

        ShipmentIssue::create([
            'shipment_id' => Shipment::where('waybill_no', 'SHP-00003')->value('id'),
            'issue_type' => 'Alamat Tidak Lengkap',
            'description' => 'Alamat penerima tidak ditemukan',
            'reported_at' => Carbon::parse('2026-01-16 12:00:00'),
            'status' => 'open',
        ]);

        ShipmentIssue::create([
            'shipment_id' => Shipment::where('waybill_no', 'SHP-00001')->value('id'),
            'issue_type' => 'Selesai',
            'description' => 'Sudah diselesaikan',
            'reported_at' => Carbon::parse('2026-01-12 12:00:00'),
            'status' => 'resolved',
        ]);
    }

    public function test_dashboard_renders_all_analytics_panels(): void
    {
        $this->createShipments();
        $user = User::first();

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Kesegaran Data')
            ->assertSee('Kepatuhan SLA per Tahap')
            ->assertSee('Rata-rata Lead Time')
            ->assertSee('Vendor dengan Over-SLA Tertinggi')
            ->assertSee('Undelivered per Wilayah')
            ->assertSee('Issue Terbuka')
            ->assertSee('Dispatch Terbaru')
            ->assertSee('Tren Pengiriman per Bulan');
    }

    public function test_sla_filter_restricts_dashboard_data(): void
    {
        $this->createShipments();
        $user = User::first();

        $response = $this->actingAs($user)->get(route('dashboard', ['sla' => 'over']));

        $response->assertOk()
            ->assertSee('SLA Rate')
            ->assertSee('0%', false);

        $service = app(DashboardService::class);
        $kpis = $service->kpis($service->shipmentQuery(request()->merge(['sla' => 'over'])));
        $this->assertSame(1, $kpis['totalShipments']);
        $this->assertSame(0, $kpis['withinSla']);
        $this->assertSame(1, $kpis['overSla']);
    }

    public function test_sla_stage_funnel_uses_sla_status_columns(): void
    {
        $this->createShipments();
        $service = app(DashboardService::class);

        $breakdown = $service->slaStageBreakdown($service->shipmentQuery(request()));

        $byKey = collect($breakdown)->keyBy('key');

        // pickup: 3 On Time dari 4
        $this->assertSame(4, $byKey['pickup']['total']);
        $this->assertSame(3, $byKey['pickup']['within']);
        $this->assertSame(75.0, $byKey['pickup']['rate']);

        // delivery: 3 On Time dari 4
        $this->assertSame(4, $byKey['delivery']['total']);
        $this->assertSame(3, $byKey['delivery']['within']);

        // lm: semua OK
        $this->assertSame(4, $byKey['lm']['total']);
        $this->assertSame(4, $byKey['lm']['within']);
        $this->assertSame(100.0, $byKey['lm']['rate']);

        // vendor: 3 On Time dari 4
        $this->assertSame(4, $byKey['vendor']['total']);
        $this->assertSame(3, $byKey['vendor']['within']);
    }

    public function test_lead_times_calculated_in_days(): void
    {
        $this->createShipments();
        $service = app(DashboardService::class);

        $leadTimes = $service->leadTimes($service->shipmentQuery(request()));

        // SHP-00001: HO->Pickup = 1 hari, HO->Delivery = 3 hari
        // SHP-00002: HO->Pickup = 1 hari, HO->Delivery = 3 hari
        // SHP-00003: HO->Delivery = 5 hari (delivery_eta 20 Jan)
        $this->assertSame(1.0, $leadTimes['ho_to_pickup']);
        $this->assertSame(3.7, $leadTimes['ho_to_delivery']);
    }

    public function test_previous_period_kpis_and_delta(): void
    {
        $this->createShipments();

        // Tambah 2 resi pada periode sebelumnya (5 hari sebelum start)
        $user = User::first();
        $batch = ImportBatch::first();
        Shipment::create([
            'import_batch_id' => $batch->id,
            'waybill_no' => 'SHP-00005',
            'province' => 'Jawa Barat',
            'vendor_lm' => 'Vendor A',
            'ho_date' => Carbon::parse('2026-01-06 08:00:00'),
            'pickup_sla_status' => 'On Time',
            'final_status' => 'Completed',
            'is_within_sla' => true,
        ]);
        Shipment::create([
            'import_batch_id' => $batch->id,
            'waybill_no' => 'SHP-00006',
            'province' => 'Jawa Barat',
            'vendor_lm' => 'Vendor B',
            'ho_date' => Carbon::parse('2026-01-07 08:00:00'),
            'pickup_sla_status' => 'Late',
            'final_status' => 'Undelivered',
            'is_within_sla' => false,
        ]);

        $service = app(DashboardService::class);
        $request = request()->merge(['start_date' => '2026-01-10', 'end_date' => '2026-01-16']);

        $prev = $service->previousPeriodKpis($request);
        $this->assertNotNull($prev);
        $this->assertSame(2, $prev['total']);
        $this->assertSame(1, $prev['within_sla']);

        $this->assertSame(100.0, DashboardService::delta(4, 2));
        $this->assertNull(DashboardService::delta(4, 0));
    }

    public function test_worst_vendors_ranked_by_over_sla_rate(): void
    {
        $this->createShipments();

        // Tambah volume agar melewati ambang minimal 10 resi per vendor
        $user = User::first();
        $batch = ImportBatch::first();

        foreach (range(1, 10) as $i) {
            $day = Carbon::create(2026, 1, $i)->format('Y-m-d');
            Shipment::create([
                'import_batch_id' => $batch->id,
                'waybill_no' => "SHP-0V{$i}A",
                'province' => 'Jawa Barat',
                'vendor_lm' => 'Vendor A',
                'ho_date' => Carbon::parse("{$day} 08:00:00"),
                'pickup_sla_status' => 'On Time',
                'final_status' => 'Completed',
                'is_within_sla' => false,
            ]);
            Shipment::create([
                'import_batch_id' => $batch->id,
                'waybill_no' => "SHP-0V{$i}B",
                'province' => 'Jawa Barat',
                'vendor_lm' => 'Vendor B',
                'ho_date' => Carbon::parse("{$day} 08:00:00"),
                'pickup_sla_status' => 'On Time',
                'final_status' => 'Completed',
                'is_within_sla' => true,
            ]);
        }

        $service = app(DashboardService::class);

        $worst = $service->worstVendors($service->shipmentQuery(request()));

        // Vendor A: 11/12 over SLA = 91.7%, Vendor B: 0/12 over SLA = 0%
        $this->assertCount(2, $worst);
        $this->assertSame('Vendor A', $worst->first()->vendor_lm);
        $this->assertSame(91.7, $worst->first()->rate);
        $this->assertSame('Vendor B', $worst->last()->vendor_lm);
        $this->assertSame(0.0, $worst->last()->rate);
    }

    public function test_worst_regions_counts_undelivered(): void
    {
        $this->createShipments();
        $service = app(DashboardService::class);

        $regions = $service->worstRegions($service->shipmentQuery(request()));

        $this->assertSame(1, $regions['provinces']->first()->undelivered);
        $this->assertSame('DKI Jakarta', $regions['provinces']->first()->province);
        $this->assertSame(1, $regions['cities']->first()->undelivered);
        $this->assertSame('Jakarta Selatan', $regions['cities']->first()->city_regency);
    }

    public function test_open_issues_only_counts_matching_scope(): void
    {
        $this->createShipments();
        $service = app(DashboardService::class);

        $issues = $service->openIssues(request());
        $this->assertSame(1, $issues['total']);
        $this->assertSame('Alamat Tidak Lengkap', $issues['items']->first()->issue_type);
    }

    public function test_shipments_index_accepts_sla_filter(): void
    {
        $this->createShipments();
        $user = User::first();

        $this->actingAs($user)->get(route('shipments.index', ['sla' => 'over']))
            ->assertOk()
            ->assertSee('SHP-00002')
            ->assertDontSee('SHP-00001');
    }
}
