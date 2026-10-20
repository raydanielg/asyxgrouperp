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

                // Reservation-aware stock removal — respects
                // available = on-hand − reserved at the product's home warehouse
                $product = Product::where('id', $item['product_id'])->first();
                if ($product) {
                    // Explicit policy: missing warehouse_id never bypasses
                    // stock validation — assign a warehouse first.
                    if (!$product->warehouse_id) {
                        abort(422, "Product '{$product->name}' has no warehouse assignment — stock cannot be verified.");
                    }
                    try {
                        app(\App\Services\InventoryService::class)->removeStock(
                            $product->id, $product->warehouse_id, $item['quantity'],
                            "POS {$sale->sale_number}"
                        );
                    } catch (\RuntimeException $e) {
                        abort(422, "Insufficient available stock for {$product->name}: {$e->getMessage()}");
                    }
                }
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



    /**
     * List returns for the user's company.
     */
    public function returns(Request $request)
    {
        $returns = \App\Models\PosReturn::with(['sale:id,sale_number', 'items', 'creator:id,name'])
            ->latest()->paginate(20);
        return response()->json($returns);
    }

    /**
     * Create a pending return — same service + validation rules as web.
     */
    public function returnStore(Request $request, PosSale $posSale)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.pos_sale_item_id' => 'required|exists:pos_sale_items,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.disposition' => 'required|in:restock,damaged',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        $return = app(\App\Services\PosReturnService::class)->create(
            $posSale, $data['items'], $data['reason'], $data['refund_amount'] ?? 0, $request->user()
        );

        return response()->json($return->load('items'), 201);
    }

    /**
     * Process a return — restock + refund intent. Creator cannot process.
     */
    public function returnProcess(\App\Models\PosReturn $posReturn)
    {
        app(\App\Services\PosReturnService::class)->process($posReturn, auth()->user());
        return response()->json($posReturn->fresh()->load('items'));
    }

    /**
     * Confirm a refund — manual reconciliation. Does NOT claim external
     * payment settlement; records who/when/reference.
     */
    public function returnConfirmRefund(Request $request, \App\Models\PosReturn $posReturn)
    {
        $data = $request->validate([
            'refund_reference' => 'nullable|string|max:255',
            'refund_notes' => 'nullable|string|max:500',
        ]);

        app(\App\Services\PosReturnService::class)->confirmRefund(
            $posReturn, auth()->user(), $data['refund_reference'] ?? null, $data['refund_notes'] ?? null
        );

        return response()->json($posReturn->fresh()->load('items'));
    }
}
