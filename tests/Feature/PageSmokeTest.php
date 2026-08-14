<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function createUserWithRole(string $slug): User
    {
        $role = Role::create(['name' => ucfirst($slug), 'slug' => $slug]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/imports')->assertRedirect(route('login'));
        $this->get('/reports')->assertRedirect(route('login'));
    }

    public function test_public_website_pages_are_accessible_to_guest(): void
    {
        $this->get('/')->assertOk()->assertSee('Amanah Nusantara Logistik');
        $this->get('/tentang')->assertOk();
        $this->get('/layanan')->assertOk();
        $this->get('/kontak')->assertOk();
    }

    public function test_public_website_does_not_expose_dashboard_access(): void
    {
        $this->get('/')->assertDontSee('Masuk Dashboard')->assertDontSee('Buka Dashboard');
        $this->get('/tentang')->assertDontSee('Masuk Dashboard')->assertDontSee('Buka Dashboard');
        $this->get('/layanan')->assertDontSee('Masuk Dashboard')->assertDontSee('Buka Dashboard');
        $this->get('/kontak')->assertDontSee('Masuk Dashboard')->assertDontSee('Buka Dashboard');
    }

    public function test_contact_form_can_be_submitted(): void
    {
        $this->post('/kontak', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Ini pesan uji coba',
        ])->assertRedirect()->assertSessionHas('message');
    }

    public function test_contact_form_can_be_submitted_via_json(): void
    {
        $this->postJson('/kontak', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '081362323510',
            'message' => 'Ini pesan uji coba',
        ])->assertOk()->assertJsonPath('message', 'Terima kasih! Pesan Anda telah kami terima dan akan segera kami balas.');
    }

    public function test_contact_form_returns_json_validation_errors(): void
    {
        $this->postJson('/kontak', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    public function test_admin_can_access_all_pages(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->actingAs($admin)->get('/shipments')->assertOk();
        $this->actingAs($admin)->get('/imports')->assertOk();
        $this->actingAs($admin)->get('/reports')->assertOk();
    }

    public function test_project_manager_can_access_reports_but_not_imports(): void
    {
        $pm = $this->createUserWithRole('project-manager');

        $this->actingAs($pm)->get('/dashboard')->assertOk();
        $this->actingAs($pm)->get('/reports')->assertOk();
        $this->actingAs($pm)->get('/imports')->assertForbidden();
    }

    public function test_staff_can_only_access_dashboard_and_shipments(): void
    {
        $staff = $this->createUserWithRole('staff');

        $this->actingAs($staff)->get('/dashboard')->assertOk();
        $this->actingAs($staff)->get('/shipments')->assertOk();
        $this->actingAs($staff)->get('/imports')->assertForbidden();
        $this->actingAs($staff)->get('/reports')->assertForbidden();
    }

    public function test_admin_can_download_reports(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)->get('/reports/export-excel')->assertOk();
        $this->actingAs($admin)->get('/reports/export-pdf')->assertOk();
    }
}
