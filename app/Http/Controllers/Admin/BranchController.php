<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::with('company')->withCount('users')->latest()->paginate(20);
        return view('admin.branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $user = auth()->user();
        // Tenant admin branches always belong to their own company;
        // only superadmin can pick another company.
        $companyId = $user->isSuperAdmin()
            ? ($validated['company_id'] ?? $user->company_id)
            : $user->company_id;

        abort_unless($companyId, 403, 'Your account is not assigned to a company.');

        $branch = Branch::create([
            'company_id' => $companyId,
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        AuditLog::log('create', 'branches', [
            'module_action' => 'branch_created',
            'new_values' => ['branch' => $branch->name, 'company_id' => $companyId],
        ]);

        return back()->with('success', "Branch '{$branch->name}' created.");
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);

        $branch->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? $branch->code,
            'address' => $validated['address'] ?? $branch->address,
            'phone' => $validated['phone'] ?? $branch->phone,
            'is_active' => $request->boolean('is_active', $branch->is_active),
        ]);

        AuditLog::log('update', 'branches', [
            'module_action' => 'branch_updated',
            'new_values' => ['branch' => $branch->name],
        ]);

        return back()->with('success', 'Branch updated.');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->users()->exists()) {
            return back()->with('error', 'This branch has assigned users — reassign them first.');
        }

        $branch->delete();
        return back()->with('success', 'Branch deleted.');
    }
}
