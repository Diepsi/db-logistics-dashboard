<?php

namespace Tests\Feature;

use App\Models\ImportBatch;
use App\Models\Role;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuickSearchTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $this->admin = User::factory()->create(['role_id' => $role->id]);

        $batch = ImportBatch::create([
            'file_name' => 'test.xlsx',
            'uploaded_by' => $this->admin->id,
            'status' => 'completed',
        ]);

        Shipment::create([
            'import_batch_id' => $batch->id,
            'waybill_no' => 'DBX123456789',
            'school_name' => 'SMA Negeri 1 Bandung',
            'city_regency' => 'Kota Bandung',
            'final_status' => 'On Delivery',
        ]);

        Shipment::create([
            'import_batch_id' => $batch->id,
            'waybill_no' => 'DBX987654321',
            'school_name' => 'SMK Negeri 2 Bogor',
            'city_regency' => 'Kab. Bogor',
            'final_status' => 'Completed',
        ]);
    }

    public function test_guest_cannot_use_search_endpoint(): void
    {
        $this->getJson(route('shipments.search', ['q' => 'DBX']))
            ->assertRedirect(route('login'));
    }

    public function test_search_requires_minimum_two_characters(): void
    {
        $this->actingAs($this->admin)
            ->getJson(route('shipments.search', ['q' => 'D']))
            ->assertOk()
            ->assertJson(['results' => []]);
    }

    public function test_search_finds_shipment_by_waybill_fragment(): void
    {
        $this->actingAs($this->admin)
            ->getJson(route('shipments.search', ['q' => '123456']))
            ->assertOk()
            ->assertJsonCount(1, 'results')
            ->assertJsonPath('results.0.waybill_no', 'DBX123456789')
            ->assertJsonPath('results.0.school_name', 'SMA Negeri 1 Bandung');
    }

    public function test_search_finds_shipment_by_school_name(): void
    {
        $this->actingAs($this->admin)
            ->getJson(route('shipments.search', ['q' => 'negeri 2 bogor']))
            ->assertOk()
            ->assertJsonCount(1, 'results')
            ->assertJsonPath('results.0.waybill_no', 'DBX987654321');
    }

    public function test_search_limits_results_to_eight(): void
    {
        $batch = ImportBatch::first();

        for ($i = 1; $i <= 10; $i++) {
            Shipment::create([
                'import_batch_id' => $batch->id,
                'waybill_no' => 'ZZZ'.str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'final_status' => 'On Delivery',
            ]);
        }

        $this->actingAs($this->admin)
            ->getJson(route('shipments.search', ['q' => 'ZZZ']))
            ->assertOk()
            ->assertJsonCount(8, 'results');
    }
}
