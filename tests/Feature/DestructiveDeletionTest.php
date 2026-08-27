<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\SalesInvoice;
use App\Policies\SalesInvoicePolicy;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DestructiveDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_posted_sales_invoice_cannot_be_deleted(): void
    {
        $company = Company::create(['name' => 'Test Co', 'legal_name' => 'Test Co Ltd', 'short_code' => 'TST', 'is_active' => true]);
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'company_id' => null,
        ]);

        $invoice = SalesInvoice::create([
            'company_id' => $company->id,
            'invoice_number' => 'INV-001',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'customer_id' => $admin->id,
            'subtotal' => 100,
            'total_amount' => 100,
            'status' => 'posted',
        ]);

        $policy = new SalesInvoicePolicy();
        $this->assertFalse($policy->delete($admin, $invoice));
    }

    public function test_draft_sales_invoice_can_be_deleted(): void
    {
        $company = Company::create(['name' => 'Test Co', 'legal_name' => 'Test Co Ltd', 'short_code' => 'TST', 'is_active' => true]);
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'company_id' => null,
        ]);

        $invoice = SalesInvoice::create([
            'company_id' => $company->id,
            'invoice_number' => 'INV-002',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'customer_id' => $admin->id,
            'subtotal' => 100,
            'total_amount' => 100,
            'status' => 'draft',
        ]);

        $policy = new SalesInvoicePolicy();
        $this->assertTrue($policy->delete($admin, $invoice));
    }
}
