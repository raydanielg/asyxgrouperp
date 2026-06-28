<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::with('category', 'warehouse')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);
        $product = Product::create($data);
        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json($product->load('category', 'warehouse', 'movements'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'string|max:255',
            'sku' => 'string|max:100|unique:products,sku,' . $product->id,
            'description' => 'nullable|string',
            'unit_price' => 'numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
        ]);
        $product->update($data);
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }

    public function lowStock()
    {
        $products = Product::whereColumn('stock_quantity', '<=', 'reorder_level')
            ->where('reorder_level', '>', 0)
            ->with('category')
            ->get();
        return response()->json($products);
    }

    public function stockMovements()
    {
        return response()->json(StockMovement::with('product', 'warehouse')->latest()->paginate(30));
    }
}
