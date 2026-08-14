<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Akses penuh ke seluruh modul sistem (import, pengaturan, laporan).'],
            ['name' => 'Project Manager', 'slug' => 'project-manager', 'description' => 'Monitoring dashboard, analisis SLA/vendor, dan laporan.'],
            ['name' => 'Staff', 'slug' => 'staff', 'description' => 'Read-only: dashboard dan tabel data pengiriman.'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
