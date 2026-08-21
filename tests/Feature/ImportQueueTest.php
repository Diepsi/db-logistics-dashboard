<?php

namespace Tests\Feature;

use App\Jobs\ProcessImportJob;
use App\Models\ImportBatch;
use App\Models\Role;
use App\Models\User;
use App\Services\ShipmentImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ImportQueueTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $this->admin = User::factory()->create(['role_id' => $role->id]);
    }

    private function createBatch(array $attributes = []): ImportBatch
    {
        return ImportBatch::create(array_merge([
            'file_name' => 'raw_data.xlsx',
            'uploaded_by' => $this->admin->id,
            'status' => 'pending',
        ], $attributes));
    }

    public function test_guest_cannot_access_progress_endpoint(): void
    {
        $batch = $this->createBatch();

        $this->get(route('imports.progress', $batch))
            ->assertRedirect(route('login'));
    }

    public function test_progress_endpoint_returns_json_metrics(): void
    {
        $batch = $this->createBatch([
            'status' => 'processing',
            'total_rows' => 200,
            'processed_rows' => 50,
            'failed_rows' => 3,
        ]);

        $this->actingAs($this->admin)
            ->getJson(route('imports.progress', $batch))
            ->assertOk()
            ->assertJsonPath('status', 'processing')
            ->assertJsonPath('total_rows', 200)
            ->assertJsonPath('processed_rows', 50)
            ->assertJsonPath('failed_rows', 3)
            ->assertJsonPath('percentage', 25);
    }

    public function test_progress_endpoint_reports_full_percentage_when_completed(): void
    {
        $batch = $this->createBatch([
            'status' => 'completed',
            'total_rows' => 100,
            'processed_rows' => 100,
        ]);

        $this->actingAs($this->admin)
            ->getJson(route('imports.progress', $batch))
            ->assertOk()
            ->assertJsonPath('percentage', 100);
    }

    public function test_process_confirmation_dispatches_background_job(): void
    {
        Bus::fake();

        $this->actingAs($this->admin)
            ->withSession(['import_file_name' => 'panthera_tracing.xlsx'])
            ->post(route('imports.process'), ['token' => 'tok-abc-123'])
            ->assertRedirect(route('imports.index'))
            ->assertSessionHas('success');

        Bus::assertDispatched(ProcessImportJob::class, function (ProcessImportJob $job) {
            return $job->token === 'tok-abc-123'
                && $job->fileName === 'panthera_tracing.xlsx';
        });

        $this->assertDatabaseHas('import_batches', [
            'file_name' => 'panthera_tracing.xlsx',
            'status' => 'pending',
            'uploaded_by' => $this->admin->id,
        ]);
    }

    public function test_process_confirmation_requires_token(): void
    {
        Bus::fake();

        $this->actingAs($this->admin)
            ->post(route('imports.process'), [])
            ->assertSessionHasErrors('token');

        Bus::assertNothingDispatched();
    }

    public function test_job_marks_batch_failed_when_temp_file_missing(): void
    {
        $batch = $this->createBatch(['status' => 'pending']);

        (new ProcessImportJob('missing-token', $batch->id, 'raw_data.xlsx'))
            ->handle(app(ShipmentImportService::class));

        $batch->refresh();

        $this->assertSame('failed', $batch->status);
        $this->assertStringContainsString('tidak ditemukan', (string) $batch->notes);
    }

    public function test_job_ignores_batch_that_is_already_completed(): void
    {
        $batch = $this->createBatch([
            'status' => 'completed',
            'notes' => 'Import selesai sebelumnya.',
        ]);

        (new ProcessImportJob('missing-token', $batch->id, 'raw_data.xlsx'))
            ->handle(app(ShipmentImportService::class));

        $batch->refresh();

        $this->assertSame('completed', $batch->status);
        $this->assertSame('Import selesai sebelumnya.', $batch->notes);
    }
}
