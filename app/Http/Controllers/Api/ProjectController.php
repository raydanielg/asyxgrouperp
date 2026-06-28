<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectBudget;
use App\Models\OfficeExpense;
use App\Models\SalesInvoice;
use App\Models\VendorInvoice;
use App\Models\Lpo;
use App\Models\Timesheet;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return response()->json(Project::with('company', 'manager', 'deal')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high',
            'manager_id' => 'nullable|exists:users,id',
        ]);
        $project = Project::create($data);
        return response()->json($project, 201);
    }

    public function show(Project $project)
    {
        $project->load('company', 'manager', 'deal', 'tasks', 'bugs', 'budgets', 'lpos', 'officeExpenses', 'vendorInvoices');
        return response()->json($project);
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'status' => 'in:planning,in_progress,on_hold,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high',
        ]);
        $project->update($data);
        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['message' => 'Project deleted']);
    }

    public function tasks(Project $project)
    {
        return response()->json(ProjectTask::where('project_id', $project->id)->with('assignedTo')->get());
    }

    public function budget(Project $project)
    {
        return response()->json([
            'budgets' => ProjectBudget::where('project_id', $project->id)->get(),
            'total_budget' => $project->budgets->sum('amount'),
            'total_spent' => $project->budgets->sum('spent'),
        ]);
    }

    public function profitability(Project $project)
    {
        $totalRevenue = SalesInvoice::whereHas('items', function ($q) use ($project) {
            $q->where('project_id', $project->id);
        })->sum('total_amount');

        $totalCost = OfficeExpense::where('project_id', $project->id)->sum('amount')
            + VendorInvoice::whereHas('lpo', function ($q) use ($project) {
                $q->where('project_id', $project->id);
            })->sum('total_amount')
            + Lpo::where('project_id', $project->id)->sum('total');

        $hours = Timesheet::where('project_id', $project->id)->sum('hours');

        return response()->json([
            'project' => $project->name,
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'gross_profit' => $totalRevenue - $totalCost,
            'margin_percent' => $totalRevenue > 0 ? round(($totalRevenue - $totalCost) / $totalRevenue * 100, 2) : 0,
            'total_hours' => $hours,
            'budget_vs_actual' => $project->budgets->map(function ($b) {
                return ['category' => $b->category, 'budgeted' => $b->amount, 'spent' => $b->spent, 'remaining' => $b->amount - $b->spent];
            }),
        ]);
    }
}
