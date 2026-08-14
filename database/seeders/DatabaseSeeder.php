<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(WebsiteContentSeeder::class);

        $adminRole = Role::where('slug', 'admin')->first();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role_id' => $adminRole->id,
        ]);

        User::create([
            'name' => 'Muhammad Ismail',
            'email' => 'ismail@dblogistics.com',
            'password' => '$2y$12$DX7tcpAzv2jdGRxBuTfr7eCxJVFQNNuXV6By.rEFOx.KrkkH7ORlq',
            'role_id' => $adminRole->id,
        ]);
    }
}
