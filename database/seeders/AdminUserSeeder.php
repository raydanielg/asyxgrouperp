<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $company = \App\Models\Company::where('short_code', 'ASYX')->first();
        $companyId = $company?->id;

        User::updateOrCreate(
            ['email' => 'admin@djanproject.com'],
            [
                'name' => 'System Admin',
                'first_name' => 'System',
                'last_name' => 'Admin',
                'email' => 'admin@djanproject.com',
                'phone' => '255700000000',
                'role' => 'admin',
                'company_id' => $companyId,
                'password' => Hash::make('admin12345'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'superadmin@asyxgroup.co.tz'],
            [
                'name' => 'ERP Super Administrator',
                'first_name' => 'ERP Super',
                'last_name' => 'Administrator',
                'email' => 'superadmin@asyxgroup.co.tz',
                'phone' => '255700000001',
                'role' => 'superadmin',
                'company_id' => $companyId,
                'password' => Hash::make('superadmin12345'),
                'email_verified_at' => now(),
            ]
        );
    }
}
