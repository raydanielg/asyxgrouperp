<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\JobCardPart;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class JobCardController extends Controller
{
    public function index()
    {
        $query = JobCard::with(['project', 'assignedTo', 'creator']);
        $user = auth()->user();
        $managerRoles = ['admin', 'superadmin', 'technical_manager', 'project_manager'];
        if (!$user->isAdmin() && !in_array($user->role, $managerRoles)) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
            });
        }
        $jobCards = $query->latest()->paginate(15);
        $projects = Project::where('status', 'in_progress')->get();
        $technicians = User::whereHas('roles', fn($q) => $q->whereIn('name', ['technician', 'field_technician', 'support_engineer', 'systems_engineer', 'network_engineer', 'senior_systems_engineer']))->orWhere('role', 'technician')->get();
        $stats = [
            'total' => JobCard::count(),
            'open' => JobCard::where('status', 'open')->count(),
            'in_progress' => JobCard::where('status', 'in_progress')->count(),
            'resolved' => JobCard::where('status', 'resolved')->count(),
            'pending_payment' => JobCard::where('payment_status', 'pending')->whereNotNull('end_user_signed_at')->count(),
        ];
        return view('admin.job-cards.index', compact('jobCards', 'projects', 'technicians', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $this->validateJobCard($request);
        $data['job_number'] = 'JC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $data['company_id'] = auth()->user()?->company_id;
        $data['created_by'] = auth()->id();
        $data['status'] = 'open';
        $data['payment_status'] = 'pending';

        $jobCard = JobCard::create($data);
        $this->syncParts($jobCard, $request->input('parts', []));

        return back()->with('success', 'Job card / service call report created.');
    }

    public function update(Request $request, JobCard $jobCard)
    {
        $data = $this->validateJobCard($request, true);
        if (($data['status'] ?? $jobCard->status) === 'resolved' && $jobCard->status !== 'resolved') {
            $data['resolved_at'] = now();
        }
        $jobCard->update($data);
        $this->syncParts($jobCard, $request->input('parts', []));

        return back()->with('success', 'Job card / service call report updated.');
    }

    public function show(JobCard $jobCard)
    {
        $jobCard->load(['project', 'assignedTo', 'creator', 'approver', 'parts']);
        $projects = Project::where('status', 'in_progress')->orWhere('id', $jobCard->project_id)->get();
        $technicians = User::whereHas('roles', fn($q) => $q->whereIn('name', ['technician', 'field_technician', 'support_engineer', 'systems_engineer', 'network_engineer', 'senior_systems_engineer']))->orWhere('role', 'technician')->get();
        return view('admin.job-cards.show', compact('jobCard', 'technicians', 'projects'));
    }

    public function print(JobCard $jobCard)
    {
        $jobCard->load(['project', 'assignedTo', 'creator', 'company', 'parts']);
        return view('admin.job-cards.print', compact('jobCard'));
    }

    public function destroy(JobCard $jobCard)
    {
        $jobCard->delete();
        return back()->with('success', 'Job card deleted.');
    }

    public function updateStatus(Request $request, JobCard $jobCard)
    {
        $request->validate(['status' => 'required|string|in:open,in_progress,resolved,closed']);
        $data = ['status' => $request->status];
        if ($request->status === 'resolved') {
            $data['resolved_at'] = now();
        }
        $jobCard->update($data);
        return response()->json(['success' => true]);
    }

    public function signOff(Request $request, JobCard $jobCard)
    {
        $request->validate([
            'type' => 'required|string|in:end_user,technician',
            'name' => 'required|string|max:255',
            'signature' => 'required|string',
        ]);

        $user = auth()->user();
        if ($request->type === 'end_user') {
            $jobCard->update([
                'end_user_name' => $request->name,
                'end_user_signature' => $request->signature,
                'end_user_signed_at' => now(),
            ]);
        } else {
            $jobCard->update([
                'technician_name' => $request->name,
                'technician_signature' => $request->signature,
                'technician_signed_at' => now(),
            ]);
        }

        return back()->with('success', ucfirst(str_replace('_', ' ', $request->type)) . ' signed off successfully.');
    }

    public function approvePayment(Request $request, JobCard $jobCard)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasAnyRole(['admin', 'finance_manager', 'finance_director', 'general_manager', 'managing_director'])) {
            abort(403, 'Unauthorized.');
        }

        $jobCard->update([
            'payment_status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Payment approved. Job card can now proceed to invoicing.');
    }

    private function validateJobCard(Request $request, bool $isUpdate = false): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|string|in:low,medium,high,critical',
            'status' => ($isUpdate ? 'required' : 'nullable') . '|string|in:open,in_progress,resolved,closed',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'resolution_notes' => 'nullable|string',
            // Service call report fields
            'csr_no' => 'nullable|string|max:255',
            'report_date' => 'nullable|date',
            'customer_name' => 'nullable|string|max:255',
            'customer_address' => 'nullable|string',
            'branch_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'equipment_type' => 'nullable|string',
            'make_brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string',
            'call_type' => 'nullable|string|in:corrective,corrective_preventive,preventive,installation',
            'problem_reported' => 'nullable|string',
            'defects_found' => 'nullable|string',
            'action_taken' => 'nullable|string',
        ];

        return $request->validate($rules);
    }

    private function syncParts(JobCard $jobCard, array $parts): void
    {
        $existingIds = [];
        foreach ($parts as $index => $part) {
            if (empty($part['part_name'])) {
                continue;
            }
            $id = $part['id'] ?? null;
            $payload = [
                'part_name' => $part['part_name'],
                'quantity' => $part['quantity'] ?? 1,
                'model' => $part['model'] ?? null,
                'part_number' => $part['part_number'] ?? null,
                'sort_order' => $index,
            ];
            if ($id) {
                $jobCard->parts()->where('id', $id)->update($payload);
                $existingIds[] = $id;
            } else {
                $created = $jobCard->parts()->create($payload);
                $existingIds[] = $created->id;
            }
        }
        $jobCard->parts()->whereNotIn('id', $existingIds)->delete();
    }
}
