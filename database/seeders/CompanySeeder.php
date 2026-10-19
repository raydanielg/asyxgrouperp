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
                'name' => 'ASYX Group',
                'legal_name' => 'ASYX Group Company Limited',
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
                'is_group' => true,
                'is_active' => true,
            ]
        );

        // ASYX Group subsidiaries per the requirements specification.
        $subsidiaries = [
            ['name' => 'Parktech Tanzania', 'legal_name' => 'Parktech Tanzania Limited', 'short_code' => 'PARKTECH', 'email' => 'info@parktech.co.tz', 'domain' => 'parktech.co.tz'],
            ['name' => 'Motisha', 'legal_name' => 'Motisha Company Limited', 'short_code' => 'MOTISHA', 'email' => 'info@motisha.co.tz', 'domain' => 'motisha.co.tz'],
            ['name' => 'Terkmark', 'legal_name' => 'Terkmark Limited', 'short_code' => 'TERKMARK', 'email' => 'info@terkmark.co.tz', 'domain' => 'terkmark.co.tz'],
            ['name' => 'Glovin', 'legal_name' => 'Glovin Limited', 'short_code' => 'GLOVIN', 'email' => 'info@glovin.co.tz', 'domain' => 'glovin.co.tz'],
        ];
        $i = 1;
        foreach ($subsidiaries as $sub) {
            Company::updateOrCreate(
                ['short_code' => $sub['short_code']],
                [
                    'name' => $sub['name'],
                    'legal_name' => $sub['legal_name'],
                    'short_code' => $sub['short_code'],
                    'registration_number' => 'REG-2024-00' . $i,
                    'tax_id' => 'TIN-100-00' . $i . '-001',
                    'address' => 'Tropical Center, New Bagamoyo Road',
                    'city' => 'Dar es Salaam',
                    'country' => 'Tanzania',
                    'phone' => '+255 22 000 000' . $i,
                    'email' => $sub['email'],
                    'website' => 'https://www.' . $sub['domain'],
                    'currency' => 'TZS',
                    'is_group' => false,
                    'is_active' => true,
                ]
            );
            $i++;
        }

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
