<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class JobCardController extends Controller
{
    public function index()
    {
        $query = JobCard::with(['project', 'assignedTo', 'creator']);
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $query->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
        }
        $jobCards = $query->latest()->paginate(15);
        $projects = Project::where('status', 'in_progress')->get();
        $technicians = User::whereHas('roles', fn($q) => $q->whereIn('name', ['technician', 'field_technician', 'support_engineer']))->get();
        $stats = [
            'total' => JobCard::count(),
            'open' => JobCard::where('status', 'open')->count(),
            'in_progress' => JobCard::where('status', 'in_progress')->count(),
            'resolved' => JobCard::where('status', 'resolved')->count(),
        ];
        return view('admin.job-cards.index', compact('jobCards', 'projects', 'technicians', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|string|in:low,medium,high,critical',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        $data['job_number'] = 'JC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $data['company_id'] = auth()->user()?->company_id;
        $data['created_by'] = auth()->id();
        $data['status'] = 'open';
        JobCard::create($data);
        return back()->with('success', 'Job card created.');
    }

    public function update(Request $request, JobCard $jobCard)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|string|in:low,medium,high,critical',
            'status' => 'required|string|in:open,in_progress,resolved,closed',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'resolution_notes' => 'nullable|string',
        ]);
        if ($data['status'] === 'resolved' && $jobCard->status !== 'resolved') {
            $data['resolved_at'] = now();
        }
        $jobCard->update($data);
        return back()->with('success', 'Job card updated.');
    }

    public function show(JobCard $jobCard)
    {
        $jobCard->load(['project', 'assignedTo', 'creator']);
        return view('admin.job-cards.show', compact('jobCard'));
    }

    public function print(JobCard $jobCard)
    {
        $jobCard->load(['project', 'assignedTo', 'creator', 'company']);
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
}
