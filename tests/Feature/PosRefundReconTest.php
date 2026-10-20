<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\PosReturn;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\Product;
use App\Models\ProductWarehouseStock;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PosRefundReconTest extends TestCase
{
    use RefreshDatabase;

    private function company(): Company
    {
        return Company::firstOrCreate(
            ['name' => 'Test Co'],
            ['legal_name' => 'Test Co Ltd', 'short_code' => 'TST', 'is_active' => true]
        );
    }

    private function makeUser(): User
    {
        return User::create([
            'name' => 'U' . uniqid(), 'email' => 'u' . uniqid() . '@test.com',
            'password' => Hash::make('password'), 'role' => 'admin',
            'company_id' => $this->company()->id, 'is_enable_login' => true,
        ]);
    }

    private function makeWarehouse(): Warehouse
    {
        return Warehouse::withoutGlobalScopes()->create([
            'name' => 'WH', 'address' => 'a', 'city' => 'a', 'zip_code' => '1',
            'company_id' => $this->company()->id, 'is_active' => true,
        ]);
    }

    private function makeProduct(?int $whId = null): Product
    {
        return Product::withoutGlobalScopes()->create([
            'name' => 'P-X', 'product_code' => 'P-' . uniqid(), 'warehouse_id' => $whId,
            'company_id' => $this->company()->id, 'is_active' => true, 'stock_quantity' => 50,
        ]);
    }

    private function makeSale(User $cashier, Product $product, float $qty = 10, float $price = 10): array
    {
        $this->actingAs($cashier);
        $sale = PosSale::create([
            'sale_number' => 'POS-' . uniqid(), 'subtotal' => $qty * $price,
            'total_amount' => $qty * $price, 'paid_amount' => $qty * $price,
            'payment_method' => 'cash', 'status' => 'completed', 'cashier_id' => $cashier->id,
            'company_id' => $this->company()->id,
        ]);
        $item = PosSaleItem::create([
            'pos_sale_id' => $sale->id, 'product_id' => $product->id,
            'product_name' => $product->name, 'quantity' => $qty,
            'unit_price' => $price, 'total' => $qty * $price,
        ]);
        return [$sale, $item];
    }

    private function makeProcessedRefund(User $cashier, float $refund = 30): array
    {
        $wh = $this->makeWarehouse();
        $product = $this->makeProduct($wh->id);
        ProductWarehouseStock::withoutGlobalScopes()->create([
            'product_id' => $product->id, 'warehouse_id' => $wh->id, 'quantity' => 40, 'reserved_quantity' => 0,
        ]);
        [$sale, $item] = $this->makeSale($cashier, $product, 10);

        $this->post("/admin/pos-sales/{$sale->id}/returns", [
            'reason' => 'x', 'refund_amount' => $refund,
            'items' => [['pos_sale_item_id' => $item->id, 'quantity' => 3, 'disposition' => 'restock']],
        ]);
        $return = PosReturn::latest('id')->first();
        $this->actingAs($this->makeUser())->post("/admin/pos-returns/{$return->id}/process");
        return [$return, $sale];
    }

    public function test_processed_refund_is_awaiting_not_confirmed(): void
    {
        $cashier = $this->makeUser();
        [$return] = $this->makeProcessedRefund($cashier);
        $this->assertEquals('awaiting_reconciliation', $return->fresh()->refund_status);
        $this->assertNull($return->fresh()->refund_confirmed_by);
    }

    public function test_manual_confirmation_records_actor_timestamp_reference(): void
    {
        $cashier = $this->makeUser();
        $confirmer = $this->makeUser();
        [$return] = $this->makeProcessedRefund($cashier);

        $this->actingAs($confirmer)->post("/admin/pos-returns/{$return->id}/confirm-refund", [
            'refund_reference' => 'BANK-TXN-001',
        ])->assertRedirect();

        $r = $return->fresh();
        $this->assertEquals('confirmed', $r->refund_status);
        $this->assertEquals($confirmer->id, $r->refund_confirmed_by);
        $this->assertNotNull($r->refund_confirmed_at);
        $this->assertEquals('BANK-TXN-001', $r->refund_reference);
        $this->assertDatabaseHas('audit_logs', ['module' => 'pos', 'module_action' => 'pos_refund_confirmed']);
    }

    public function test_double_confirmation_rejected(): void
    {
        $cashier = $this->makeUser();
        $confirmer = $this->makeUser();
        [$return] = $this->makeProcessedRefund($cashier);
        $this->actingAs($confirmer)->post("/admin/pos-returns/{$return->id}/confirm-refund")->assertRedirect();
        $this->actingAs($confirmer)->post("/admin/pos-returns/{$return->id}/confirm-refund")->assertStatus(403);
    }

    public function test_cumulative_refund_cap_blocks_second_sale_refund(): void
    {
        $cashier = $this->makeUser();
        [$return, $sale] = $this->makeProcessedRefund($cashier, 100); // full paid refund

        // Second return on same sale requesting refund beyond paid → blocked at store
        $item = $sale->items->first();
        $this->actingAs($cashier)->post("/admin/pos-sales/{$sale->id}/returns", [
            'reason' => 'more', 'refund_amount' => 50,
            'items' => [['pos_sale_item_id' => $item->id, 'quantity' => 2, 'disposition' => 'damaged']],
        ]);
        $ret2 = PosReturn::latest('id')->first();
        $this->actingAs($this->makeUser())->post("/admin/pos-returns/{$ret2->id}/process");
        $this->assertNotEquals('processed', $ret2->fresh()->status);
    }

    public function test_transfer_reconciliation_and_ambiguity(): void
    {
        $admin = $this->makeUser();
        $wh = $this->makeWarehouse();
        $wh2 = \App\Models\Warehouse::withoutGlobalScopes()->create([
            'name' => 'W2', 'address' => 'a', 'city' => 'a', 'zip_code' => '1',
            'company_id' => $this->company()->id, 'is_active' => true,
        ]);

        // Clear case — both warehouses same company, transfer missing company
        // (DB insert bypasses the BelongsToCompany auto-assign hook)
        $transferId = \Illuminate\Support\Facades\DB::table('transfers')->insertGetId([
            'from_warehouse' => $wh->id, 'to_warehouse' => $wh2->id, 'product_name' => 'X',
            'quantity' => 5, 'status' => 'pending', 'created_by' => $admin->id, 'company_id' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->actingAs($admin)->post('/admin/inventory/reconcile-transfer', [
            'transfer_id' => $transferId, 'reason' => 'Consistent warehouses',
        ])->assertRedirect();

        $this->assertEquals($this->company()->id, \App\Models\Transfer::withoutGlobalScopes()->find($transferId)->company_id);
        $this->assertDatabaseHas('audit_logs', ['module' => 'inventory', 'module_action' => 'transfer_company_reconciled']);
    }

    public function test_ambiguous_transfer_needs_manual_review(): void
    {
        $admin = $this->makeUser();
        $wh = $this->makeWarehouse();
        $otherCo = \App\Models\Company::create(['name' => 'Other', 'legal_name' => 'O', 'short_code' => 'OTH', 'is_active' => true]);
        $foreignWh = \App\Models\Warehouse::withoutGlobalScopes()->create([
            'name' => 'FW', 'address' => 'a', 'city' => 'a', 'zip_code' => '1',
            'company_id' => $otherCo->id, 'is_active' => true,
        ]);

        $transferId = \Illuminate\Support\Facades\DB::table('transfers')->insertGetId([
            'from_warehouse' => $wh->id, 'to_warehouse' => $foreignWh->id, 'product_name' => 'X',
            'quantity' => 5, 'status' => 'pending', 'created_by' => $admin->id, 'company_id' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->actingAs($admin)->post('/admin/inventory/reconcile-transfer', [
            'transfer_id' => $transferId, 'reason' => 'guess',
        ]);

        $this->assertNull(\App\Models\Transfer::withoutGlobalScopes()->find($transferId)->company_id); // not assigned — ambiguous
    }
}