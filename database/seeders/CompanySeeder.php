<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::updateOrCreate(
            ['short_code' => 'ASYX'],
            [
                'name' => 'ASYX',
                'legal_name' => 'ASYX Limited',
                'short_code' => 'ASYX',
                'registration_number' => 'REG-2009-002',
                'tax_id' => 'TIN-100-000-001',
                'vrn' => 'VRN-100-000-001',
                'address' => 'Tropical Center, 3rd Floor, New Bagamoyo Road',
                'city' => 'Dar es Salaam',
                'country' => 'Tanzania',
                'phone' => '+255 22 000 0000',
                'email' => 'info@asyxgroup.co.tz',
                'website' => 'https://www.asyxgroup.co.tz',
                'currency' => 'TZS',
                'is_group' => false,
                'is_active' => true,
            ]
        );

        $admin = User::where('email', 'admin@djanproject.com')->first();
        if ($admin && !$admin->company_id) {
            $admin->update(['company_id' => $company->id]);
        }

        $superadmin = User::where('email', 'superadmin@asyxgroup.co.tz')->first();
        if ($superadmin && !$superadmin->company_id) {
            $superadmin->update(['company_id' => $company->id]);
        }
    }
}
