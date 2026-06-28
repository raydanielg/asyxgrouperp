<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function products(Request $request)
    {
        $query = Product::where('stock_quantity', '>', 0);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        return response()->json($query->get());
    }

    public function sell(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'customer_name' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['price'];
            }

            $discount = $request->discount ?? 0;
            $totalAmount -= $discount;

            $sale = PosSale::create([
                'user_id' => $request->user()->id,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'customer_name' => $request->customer_name,
                'discount' => $discount,
                'status' => 'completed',
                'company_id' => $request->user()->company_id,
            ]);

            foreach ($request->items as $item) {
                PosSaleItem::create([
                    'pos_sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price'],
                ]);

                // Decrement stock
                Product::where('id', $item['product_id'])
                    ->decrement('stock_quantity', $item['quantity']);
            }

            return response()->json($sale->load('items.product'), 201);
        });
    }

    public function sales(Request $request)
    {
        $query = PosSale::with('items.product')->latest();

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->from && $request->to) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function todaySummary(Request $request)
    {
        $today = PosSale::whereDate('created_at', today());

        return response()->json([
            'total_sales' => $today->sum('total_amount'),
            'total_count' => $today->count(),
            'cash_sales' => PosSale::whereDate('created_at', today())->where('payment_method', 'cash')->sum('total_amount'),
            'mobile_sales' => PosSale::whereDate('created_at', today())->where('payment_method', 'mobile_money')->sum('total_amount'),
            'card_sales' => PosSale::whereDate('created_at', today())->where('payment_method', 'card')->sum('total_amount'),
        ]);
    }

    public function show(PosSale $posSale)
    {
        return response()->json($posSale->load('items.product'));
    }
}
