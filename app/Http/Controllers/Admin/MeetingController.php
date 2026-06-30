<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingActionPoint;
use App\Models\MeetingAttendee;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check()) return redirect()->route('login');
            if (auth()->user()->isAdmin()) return $next($request);
            if (auth()->user()->hasPermission('view-meetings')) return $next($request);
            abort(403, 'You do not have permission to access meetings.');
        });
    }

    public function index(Request $request)
    {
        $query = Meeting::with(['project', 'creator', 'attendees']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('from')) {
            $query->where('meeting_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('meeting_date', '<=', $request->to);
        }

        $meetings = $query->latest('meeting_date')->paginate(15)->withQueryString();
        $projects = Project::orderBy('title')->get(['id', 'title']);

        $stats = [
            'total' => Meeting::count(),
            'scheduled' => Meeting::where('status', 'scheduled')->count(),
            'completed' => Meeting::where('status', 'completed')->count(),
            'projectMeetings' => Meeting::where('type', 'project')->count(),
            'officeMeetings' => Meeting::where('type', 'office')->count(),
            'pendingActions' => MeetingActionPoint::where('status', 'pending')->count(),
        ];

        return view('admin.meetings.index', compact('meetings', 'projects', 'stats'));
    }

    public function create()
    {
        $projects = Project::orderBy('title')->get(['id', 'title']);
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        return view('admin.meetings.create', compact('projects', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:project,office',
            'mode' => 'required|in:physical,online',
            'project_id' => 'nullable|exists:projects,id',
            'meeting_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|string|max:500',
            'agenda' => 'nullable|string',
            'attendees' => 'nullable|array',
            'attendees.*' => 'exists:users,id',
            'action_points' => 'nullable|array',
            'action_points.*.description' => 'required|string',
            'action_points.*.assigned_to' => 'nullable|exists:users,id',
            'action_points.*.due_date' => 'nullable|date',
        ]);

        $meeting = Meeting::create([
            'company_id' => session('current_company_id'),
            'project_id' => $validated['type'] === 'project' ? ($validated['project_id'] ?? null) : null,
            'title' => $validated['title'],
            'type' => $validated['type'],
            'mode' => $validated['mode'],
            'meeting_date' => $validated['meeting_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'location' => $validated['location'] ?? null,
            'meeting_link' => $validated['meeting_link'] ?? null,
            'agenda' => $validated['agenda'] ?? null,
            'status' => 'scheduled',
            'created_by' => auth()->id(),
        ]);

        if (!empty($validated['attendees'])) {
            foreach ($validated['attendees'] as $userId) {
                MeetingAttendee::create([
                    'meeting_id' => $meeting->id,
                    'user_id' => $userId,
                    'attended' => false,
                ]);
            }
        }

        if (!empty($validated['action_points'])) {
            foreach ($validated['action_points'] as $ap) {
                if (!empty($ap['description'])) {
                    MeetingActionPoint::create([
                        'meeting_id' => $meeting->id,
                        'description' => $ap['description'],
                        'assigned_to' => $ap['assigned_to'] ?? null,
                        'due_date' => $ap['due_date'] ?? null,
                        'status' => 'pending',
                    ]);
                }
            }
        }

        return redirect()->route('admin.meetings.show', $meeting)
            ->with('success', 'Meeting created successfully.');
    }

    public function show(Meeting $meeting)
    {
        $meeting->load(['project', 'attendees.user', 'actionPoints.assignee', 'creator']);
        return view('admin.meetings.show', compact('meeting'));
    }

    public function edit(Meeting $meeting)
    {
        $projects = Project::orderBy('title')->get(['id', 'title']);
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $meeting->load(['attendees', 'actionPoints']);
        return view('admin.meetings.edit', compact('meeting', 'projects', 'users'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:project,office',
            'mode' => 'required|in:physical,online',
            'project_id' => 'nullable|exists:projects,id',
            'meeting_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|string|max:500',
            'agenda' => 'nullable|string',
            'minutes' => 'nullable|string',
            'report' => 'nullable|string',
            'status' => 'required|in:scheduled,completed,cancelled',
        ]);

        $validated['project_id'] = $validated['type'] === 'project' ? ($validated['project_id'] ?? null) : null;

        $meeting->update($validated);

        return redirect()->route('admin.meetings.show', $meeting)
            ->with('success', 'Meeting updated successfully.');
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();
        return redirect()->route('admin.meetings.index')
            ->with('success', 'Meeting deleted.');
    }

    public function recordAttendance(Request $request, Meeting $meeting)
    {
        $request->validate([
            'attendees' => 'array',
            'attendees.*.id' => 'exists:meeting_attendees,id',
            'attendees.*.attended' => 'boolean',
            'attendees.*.notes' => 'nullable|string',
        ]);

        foreach ($request->attendees ?? [] as $data) {
            $attendee = MeetingAttendee::find($data['id']);
            if ($attendee && $attendee->meeting_id === $meeting->id) {
                $attendee->update([
                    'attended' => $data['attended'] ?? false,
                    'notes' => $data['notes'] ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Attendance recorded.');
    }

    public function updateActionPoint(Request $request, MeetingActionPoint $actionPoint)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,overdue',
        ]);

        $actionPoint->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        return response()->json(['success' => true, 'status' => $actionPoint->status]);
    }

    public function generateInvoice(Project $project)
    {
        if (!$project->recurring_invoicing) {
            return redirect()->back()->with('error', 'Recurring invoicing is not enabled for this project.');
        }

        $nextDate = $project->nextInvoiceDate();
        if (!$nextDate) {
            return redirect()->back()->with('error', 'No pending invoice date for this project.');
        }

        $existing = SalesInvoice::where('project_id', $project->id)
            ->whereYear('invoice_date', $nextDate->year)
            ->whereMonth('invoice_date', $nextDate->month)
            ->exists();

        if ($existing) {
            return redirect()->back()->with('error', 'Invoice already exists for ' . $nextDate->format('F Y'));
        }

        $invoiceNumber = 'INV-' . now()->format('YmdHis') . '-' . \Str::random(4);

        $invoice = SalesInvoice::create([
            'company_id' => $project->company_id,
            'project_id' => $project->id,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $nextDate,
            'due_date' => $nextDate->copy()->addDays(30),
            'customer_id' => $project->customer_id,
            'subtotal' => $project->billing_amount,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $project->billing_amount,
            'paid_amount' => 0,
            'balance_amount' => $project->billing_amount,
            'status' => 'draft',
            'type' => 'project',
            'payment_terms' => 'Monthly recurring invoice for ' . $project->title,
            'notes' => 'Manual recurring invoice for ' . $nextDate->format('F Y'),
            'creator_id' => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        $project->update(['last_invoiced_at' => now()]);

        return redirect()->route('admin.sales-invoices.show', $invoice)
            ->with('success', 'Invoice generated for ' . $nextDate->format('F Y'));
    }
}
