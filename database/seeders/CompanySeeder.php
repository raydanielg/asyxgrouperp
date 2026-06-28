<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $group = Company::updateOrCreate(
            ['short_code' => 'GRP'],
            [
                'name' => 'ASYX Group',
                'legal_name' => 'ASYX Group Limited',
                'short_code' => 'GRP',
                'registration_number' => 'REG-2009-001',
                'tax_id' => 'TIN-100-000-000',
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

        $companies = [
            [
                'name' => 'ASYX',
                'legal_name' => 'ASYX Limited',
                'short_code' => 'ASYX',
                'registration_number' => 'REG-2009-002',
                'tax_id' => 'TIN-100-000-001',
            ],
            [
                'name' => 'Parktech',
                'legal_name' => 'Parktech Limited',
                'short_code' => 'PTEC',
                'registration_number' => 'REG-2010-003',
                'tax_id' => 'TIN-100-000-002',
            ],
            [
                'name' => 'Motisha',
                'legal_name' => 'Motisha Limited',
                'short_code' => 'MTSH',
                'registration_number' => 'REG-2011-004',
                'tax_id' => 'TIN-100-000-003',
            ],
            [
                'name' => 'Terkmark',
                'legal_name' => 'Terkmark Limited',
                'short_code' => 'TRMK',
                'registration_number' => 'REG-2012-005',
                'tax_id' => 'TIN-100-000-004',
            ],
            [
                'name' => 'Glovin',
                'legal_name' => 'Glovin Limited',
                'short_code' => 'GLVN',
                'registration_number' => 'REG-2013-006',
                'tax_id' => 'TIN-100-000-005',
            ],
        ];

        foreach ($companies as $data) {
            Company::updateOrCreate(
                ['short_code' => $data['short_code']],
                array_merge($data, [
                    'address' => 'Tropical Center, 3rd Floor, New Bagamoyo Road',
                    'city' => 'Dar es Salaam',
                    'country' => 'Tanzania',
                    'phone' => '+255 22 000 0000',
                    'email' => strtolower($data['short_code']) . '@asyxgroup.co.tz',
                    'currency' => 'TZS',
                    'parent_id' => $group->id,
                    'is_group' => false,
                    'is_active' => true,
                ])
            );
        }

        $admin = User::where('email', 'admin@djanproject.com')->first();
        if ($admin && !$admin->company_id) {
            $admin->update(['company_id' => $group->id]);
        }
    }
}
