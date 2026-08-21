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

    public function test_staff_can_access_operational_pages_but_not_admin_modules(): void
    {
        $staff = $this->createUserWithRole('staff');

        $this->actingAs($staff)->get('/dashboard')->assertOk();
        $this->actingAs($staff)->get('/shipments')->assertOk();
        $this->actingAs($staff)->get('/analytics')->assertOk();
        $this->actingAs($staff)->get('/reports')->assertOk();
        $this->actingAs($staff)->get('/imports')->assertForbidden();
    }

    public function test_admin_can_download_reports(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)->get('/reports/export-excel')->assertOk();
        $this->actingAs($admin)->get('/reports/export-pdf')->assertOk();
    }
}
