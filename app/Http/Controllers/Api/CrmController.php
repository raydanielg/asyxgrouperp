<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CrmLead;
use App\Models\CrmDeal;
use App\Models\CrmContact;
use App\Models\CrmContract;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    // ═══ Leads ═══
    public function leads(Request $request)
    {
        $query = CrmLead::latest();
        if ($request->status) $query->where('status', $request->status);
        if ($request->assigned_to) $query->where('assigned_to', $request->assigned_to);
        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function storeLead(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
            'source' => 'nullable|string',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $data['status'] = $data['status'] ?? 'new';
        $data['company_id'] = $request->user()->company_id;
        $lead = CrmLead::create($data);

        return response()->json($lead, 201);
    }

    public function updateLead(Request $request, CrmLead $lead)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
            'source' => 'nullable|string',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $lead->update($data);
        return response()->json($lead);
    }

    public function convertLeadToDeal(Request $request, CrmLead $lead)
    {
        $deal = CrmDeal::create([
            'name' => $lead->name,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'company' => $lead->company,
            'value' => $request->value ?? 0,
            'status' => 'open',
            'source' => 'lead_conversion',
            'company_id' => $lead->company_id,
        ]);

        $lead->update(['status' => 'converted']);

        return response()->json(['deal' => $deal, 'lead' => $lead], 201);
    }

    public function destroyLead(CrmLead $lead)
    {
        $lead->delete();
        return response()->json(['message' => 'Lead deleted']);
    }

    // ═══ Deals ═══
    public function deals(Request $request)
    {
        $query = CrmDeal::latest();
        if ($request->status) $query->where('status', $request->status);
        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function storeDeal(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
            'value' => 'required|numeric|min:0',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $data['status'] = $data['status'] ?? 'open';
        $data['company_id'] = $request->user()->company_id;
        $deal = CrmDeal::create($data);

        return response()->json($deal, 201);
    }

    public function updateDeal(Request $request, CrmDeal $deal)
    {
        $deal->update($request->only(['name', 'email', 'phone', 'company', 'value', 'status', 'notes']));
        return response()->json($deal);
    }

    public function destroyDeal(CrmDeal $deal)
    {
        $deal->delete();
        return response()->json(['message' => 'Deal deleted']);
    }

    // ═══ Contacts ═══
    public function contacts(Request $request)
    {
        $query = CrmContact::latest();
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }
        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function storeContact(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
            'position' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $data['company_id'] = $request->user()->company_id;
        $contact = CrmContact::create($data);

        return response()->json($contact, 201);
    }

    public function destroyContact(CrmContact $contact)
    {
        $contact->delete();
        return response()->json(['message' => 'Contact deleted']);
    }

    // ═══ Contracts ═══
    public function contracts(Request $request)
    {
        $query = CrmContract::latest();
        if ($request->status) $query->where('status', $request->status);
        return response()->json($query->paginate($request->per_page ?? 20));
    }

    public function storeContract(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'client' => 'required|string',
            'value' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $data['status'] = $data['status'] ?? 'draft';
        $data['company_id'] = $request->user()->company_id;
        $contract = CrmContract::create($data);

        return response()->json($contract, 201);
    }

    public function destroyContract(CrmContract $contract)
    {
        $contract->delete();
        return response()->json(['message' => 'Contract deleted']);
    }
}
