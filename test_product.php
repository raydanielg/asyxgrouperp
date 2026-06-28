<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Product;

$u = User::first();
echo "User: " . $u->email . "\n";
echo "Company ID: " . ($u->company_id ?? 'NULL') . "\n";
echo "Company: " . ($u->company ? $u->company->name : 'NONE') . "\n";
echo "Is group: " . ($u->company && $u->company->is_group ? 'yes' : 'no') . "\n";

$switchedId = session('switched_company_id');
echo "Switched ID: " . ($switchedId ?? 'NULL') . "\n";

$companyId = $switchedId ?? $u->company_id;
echo "Resolved company_id: " . ($companyId ?? 'NULL') . "\n";

echo "\n--- Products with scope ---\n";
$products = Product::latest()->take(3)->get(['id','name','company_id']);
echo "Count: " . $products->count() . "\n";
foreach ($products as $p) {
    echo "  - " . $p->name . " (company_id: " . ($p->company_id ?? 'NULL') . ")\n";
}

echo "\n--- Products without scope ---\n";
$allProducts = Product::withoutGlobalScope('company')->latest()->take(5)->get(['id','name','company_id']);
echo "Count: " . $allProducts->count() . "\n";
foreach ($allProducts as $p) {
    echo "  - " . $p->name . " (company_id: " . ($p->company_id ?? 'NULL') . ")\n";
}

echo "\n--- Test create ---\n";
try {
    $p = Product::create([
        'name' => 'Test AJAX Product',
        'product_code' => 'PRD-AJAXTEST',
        'purchase_price' => 100,
        'sale_price' => 200,
        'stock_quantity' => 10,
        'unit' => 'piece',
        'type' => 'product',
        'is_active' => true,
        'company_id' => $companyId,
    ]);
    echo "Created! ID: " . $p->id . " company_id: " . ($p->company_id ?? 'NULL') . "\n";
    $p->delete();
    echo "Cleaned up\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
