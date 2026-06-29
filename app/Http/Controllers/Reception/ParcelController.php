<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParcelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Parcel::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                    ->orWhere('sender_name', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('courier', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'parcels' => $items,
            'todayCount' => Parcel::whereDate('received_date', today())->orWhereDate('delivered_date', today())->count(),
            'weekCount' => Parcel::whereBetween('received_date', [now()->startOfWeek(), now()->endOfWeek()])->orWhereBetween('delivered_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'pendingCount' => Parcel::where('status', 'received')->count(),
            'totalCount' => Parcel::count(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tracking_number' => 'nullable|string|max:100',
            'sender_name' => 'nullable|string|max:255',
            'recipient_name' => 'nullable|string|max:255',
            'courier' => 'nullable|string|max:255',
            'status' => 'required|in:received,out_for_delivery,delivered,returned',
            'received_date' => 'nullable|date',
            'delivered_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();
        $data['created_by'] = auth()->id();
        if (empty($data['tracking_number'])) {
            $data['tracking_number'] = 'PCL-' . strtoupper(uniqid());
        }
        if (empty($data['received_date'])) {
            $data['received_date'] = now();
        }

        $parcel = Parcel::create($data);

        return response()->json(['success' => true, 'message' => 'Parcel recorded.', 'parcel' => $parcel]);
    }

    public function update(Request $request, Parcel $parcel)
    {
        $validator = Validator::make($request->all(), [
            'tracking_number' => 'nullable|string|max:100',
            'sender_name' => 'nullable|string|max:255',
            'recipient_name' => 'nullable|string|max:255',
            'courier' => 'nullable|string|max:255',
            'status' => 'required|in:received,out_for_delivery,delivered,returned',
            'received_date' => 'nullable|date',
            'delivered_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $parcel->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Parcel updated.', 'parcel' => $parcel]);
    }

    public function destroy(Parcel $parcel)
    {
        $parcel->delete();
        return response()->json(['success' => true, 'message' => 'Parcel deleted.']);
    }

    public function markDelivered(Parcel $parcel)
    {
        $parcel->update(['status' => 'delivered', 'delivered_date' => now()]);
        return response()->json(['success' => true, 'message' => 'Parcel marked as delivered.', 'parcel' => $parcel]);
    }
}
