<?php

namespace Tests\Feature;

use App\Models\ImportBatch;
use App\Models\Location;
use App\Models\Role;
use App\Models\Shipment;
use App\Models\ShipmentIssue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MapDataTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private ImportBatch $batch;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $this->admin = User::factory()->create(['role_id' => $role->id]);

        $this->batch = ImportBatch::create([
            'file_name' => 'test.xlsx',
            'uploaded_by' => $this->admin->id,
            'status' => 'completed',
        ]);
    }

    private function createShipment(array $attributes = []): Shipment
    {
        return Shipment::create(array_merge([
            'import_batch_id' => $this->batch->id,
            'waybill_no' => 'AWB'.str_pad((string) Shipment::count(), 8, '0', STR_PAD_LEFT),
            'final_status' => 'On Delivery',
            'is_within_sla' => true,
        ], $attributes));
    }

    public function test_guest_cannot_access_map_data(): void
    {
        $this->get(route('analytics.map-data'))->assertRedirect(route('login'));
    }

    public function test_project_manager_can_access_map_data(): void
    {
        $role = Role::create(['name' => 'Project Manager', 'slug' => 'project-manager']);
        $pm = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($pm)
            ->getJson(route('analytics.map-data'))
            ->assertOk();
    }

    public function test_returns_marker_metrics_per_location(): void
    {
        $location = Location::create([
            'province' => 'Jawa Barat',
            'city_regency' => 'Kota Bandung',
            'latitude' => -6.9175,
            'longitude' => 107.6191,
        ]);

        $withinSla = $this->createShipment(['location_id' => $location->id]);
        $breached = $this->createShipment(['location_id' => $location->id, 'is_within_sla' => false]);

        ShipmentIssue::create([
            'shipment_id' => $breached->id,
            'issue_type' => 'auto_stuck_transit',
            'reported_at' => Carbon::now(),
            'status' => 'open',
        ]);

        ShipmentIssue::create([
            'shipment_id' => $withinSla->id,
            'issue_type' => 'damaged',
            'reported_at' => Carbon::now(),
            'status' => 'resolved',
        ]);

        $this->actingAs($this->admin)
            ->getJson(route('analytics.map-data'))
            ->assertOk()
            ->assertJsonCount(1, 'markers')
            ->assertJsonPath('markers.0.city_regency', 'Kota Bandung')
            ->assertJsonPath('markers.0.total_shipments', 2)
            ->assertJsonPath('markers.0.sla_breach', 1)
            ->assertJsonPath('markers.0.open_issues', 1)
            ->assertJsonPath('unmapped_shipments', 0);
    }

    public function test_shipments_without_coordinates_are_counted_as_unmapped(): void
    {
        $unmappedLocation = Location::create([
            'province' => 'Papua',
            'city_regency' => 'Kab. Merauke',
        ]);

        $this->createShipment(['location_id' => $unmappedLocation->id]);

        $this->actingAs($this->admin)
            ->getJson(route('analytics.map-data'))
            ->assertOk()
            ->assertJsonCount(0, 'markers')
            ->assertJsonPath('unmapped_shipments', 1);
    }
}
