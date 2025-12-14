<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@saas.com'],
            [
                'name' => 'Super Admin',
                'password' => 'LaravelSaaS2026',
                'email_verified_at' => now(),
            ]
        );

        $superAdmin->assignRole(RoleEnum::SuperAdmin);
    }
}
