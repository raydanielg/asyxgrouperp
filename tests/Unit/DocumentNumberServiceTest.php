<?php

namespace Tests\Unit;

use App\Services\DocumentNumberService;
use App\Models\User;
use App\Models\Company;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DocumentNumberServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_includes_company_prefix(): void
    {
        $company = Company::create(['name' => 'Test Co', 'legal_name' => 'Test Co Ltd', 'short_code' => 'TST', 'is_active' => true]);
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'company_id' => $company->id,
        ]);

        $this->actingAs($user);
        session(['switched_company_id' => $company->id]);

        $service = app(DocumentNumberService::class);
        $number = $service->generate('INV', 'sales_invoices');

        $this->assertStringStartsWith("INV-{$company->id}-", $number);
    }

    public function test_generate_without_company(): void
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'company_id' => null,
        ]);

        $this->actingAs($user);

        $service = app(DocumentNumberService::class);
        $number = $service->generate('DOC', 'documents');

        $this->assertStringStartsWith('DOC-', $number);
    }
}
