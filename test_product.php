<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Warehouse;

$u = User::first();
echo "User: " . ($u ? $u->email : 'none') . "\n";
echo "Is admin: " . ($u && $u->isAdmin() ? 'yes' : 'no') . "\n";
echo "Company ID: " . ($u ? $u->company_id : 'none') . "\n";
echo "Is group: " . ($u && $u->company ? ($u->company->is_group ? 'yes' : 'no') : 'none') . "\n";
echo "Switched company: " . session('switched_company_id', 'not set') . "\n";

// Test creating a product
echo "\n--- Test Product Creation ---\n";
try {
    $p = Product::create([
        'name' => 'Test Product AJAX',
        'product_code' => 'PRD-TEST0001',
        'purchase_price' => 100,
        'sale_price' => 200,
        'stock_quantity' => 10,
        'unit' => 'piece',
        'type' => 'product',
        'is_active' => true,
        'company_id' => $u ? $u->company_id : 1,
    ]);
    echo "Product created! ID: " . $p->id . "\n";
    $p->delete();
    echo "Product deleted (cleanup)\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
