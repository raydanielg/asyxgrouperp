<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Leave;
use App\Models\PerformanceReview;
use App\Models\Training;
use App\Models\JobPosting;
use App\Models\EmployeeAsset;
use App\Models\HrEvent;
use App\Models\Policy;
use App\Models\CrmLead;
use App\Models\CrmDeal;
use App\Models\CrmContract;
use App\Models\CrmContact;
use App\Models\BankAccount;
use App\Models\BankTransferAcc;
use App\Models\Expense;
use App\Models\Revenue;
use App\Models\Bill;
use App\Models\Estimate;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Timesheet;
use App\Models\ProjectBug;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\SalesProposal;
use App\Models\SalesInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Warehouse;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\JobApplication;
use App\Models\VendorInvoice;
use App\Models\OfficeExpense;
use App\Models\ClientReceipt;

class ErpExtendedController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $publicMethods = ['careersJobsIndex', 'careersApplyForm', 'careersApplySubmit'];
            if (in_array($request->route()->getActionMethod(), $publicMethods)) {
                return $next($request);
            }
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized access. Admin only.');
            }
            return $next($request);
        });
    }

    // ═══════════════════════════════════════════════════════
    //  HRM — EMPLOYEES
    // ═══════════════════════════════════════════════════════
    public function employeeIndex(Request $request)
    {
        $query = Employee::with(['manager']);

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('first_name', 'like', "%{$q}%")
                     ->orWhere('last_name', 'like', "%{$q}%")
                     ->orWhere('email', 'like', "%{$q}%")
                     ->orWhere('employee_id', 'like', "%{$q}%");
            });
        }

        $employees = $query->latest()->paginate(15)->appends($request->except('page'));
        $departments = Employee::distinct()->pluck('department')->filter()->values()->toArray();
        return view('admin.hrm.employees.index', compact('employees', 'departments'));
    }

    public function employeeCreate()
    {
        $departments = ['Administration', 'Finance', 'Human Resources', 'Sales', 'Marketing', 'IT', 'Operations', 'Customer Support', 'Logistics', 'Production'];
        $designations = ['Manager', 'Senior Officer', 'Officer', 'Team Lead', 'Supervisor', 'Executive', 'Assistant', 'Clerk', 'Technician', 'Director'];
        $employmentTypes = ['Full-time', 'Part-time', 'Contract', 'Intern', 'Probation'];
        $managers = Employee::where('status', 'active')->get();
        $projects = Project::whereIn('status', ['in_progress', 'planning', 'active'])->orderBy('title')->get();
        return view('admin.hrm.employees.create', compact('departments', 'designations', 'employmentTypes', 'managers', 'projects'));
    }

    public function employeeStore(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|string|unique:employees,employee_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'department' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'joining_date' => 'nullable|date',
            'employment_type' => 'nullable|string',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'manager_id' => 'nullable|exists:employees,id',
            'marital_status' => 'nullable|string',
            'shift' => 'nullable|string',
            'work_location' => 'nullable|string',
        ]);
        $data['created_by'] = auth()->id();
        $projectIds = $request->input('project_ids', []);
        $projectRoles = $request->input('project_roles', []);
        $employee = Employee::create($data);
        if (!empty($projectIds)) {
            $syncData = [];
            foreach ($projectIds as $pid) {
                $syncData[$pid] = [
                    'role' => $projectRoles[$pid] ?? null,
                    'assigned_from' => now(),
                    'is_active' => true,
                ];
            }
            $employee->projects()->sync($syncData);
        }
        return redirect()->route('admin.employees.index')->with('success', 'Employee added successfully.');
    }

    public function employeeEdit(Employee $employee)
    {
        $departments = ['Administration', 'Finance', 'Human Resources', 'Sales', 'Marketing', 'IT', 'Operations', 'Customer Support', 'Logistics', 'Production'];
        $designations = ['Manager', 'Senior Officer', 'Officer', 'Team Lead', 'Supervisor', 'Executive', 'Assistant', 'Clerk', 'Technician', 'Director'];
        $employmentTypes = ['Full-time', 'Part-time', 'Contract', 'Intern', 'Probation'];
        $managers = Employee::where('status', 'active')->where('id', '!=', $employee->id)->get();
        $projects = Project::whereIn('status', ['in_progress', 'planning', 'active', 'completed'])->orderBy('title')->get();
        $employee->load('projects');
        return view('admin.hrm.employees.edit', compact('employee', 'departments', 'designations', 'employmentTypes', 'managers', 'projects'));
    }

    public function employeeUpdate(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'employee_id' => 'required|string|unique:employees,employee_id,' . $employee->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'department' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'joining_date' => 'nullable|date',
            'leaving_date' => 'nullable|date',
            'employment_type' => 'nullable|string',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'manager_id' => 'nullable|exists:employees,id',
            'marital_status' => 'nullable|string',
            'shift' => 'nullable|string',
            'work_location' => 'nullable|string',
        ]);
        $employee->update($data);
        $projectIds = $request->input('project_ids', []);
        $projectRoles = $request->input('project_roles', []);
        if (!empty($projectIds)) {
            $syncData = [];
            foreach ($projectIds as $pid) {
                $syncData[$pid] = [
                    'role' => $projectRoles[$pid] ?? null,
                    'is_active' => true,
                ];
            }
            $employee->projects()->sync($syncData);
        } else {
            $employee->projects()->detach();
        }
        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully.');
    }

    public function employeeShow(Employee $employee)
    {
        $employee->load([
            'attendances' => fn($q) => $q->latest()->take(10),
            'payrolls' => fn($q) => $q->latest()->take(5),
            'leaves' => fn($q) => $q->latest()->take(5),
            'assets' => fn($q) => $q->latest()->take(5),
            'projects' => fn($q) => $q->withPivot(['role', 'assigned_from', 'assigned_until', 'is_active']),
            'bonuses' => fn($q) => $q->latest()->take(10),
        ]);
        $assignedTasks = ProjectTask::where('assigned_to', $employee->user_id)->with('project')->latest()->take(10)->get();
        $performanceReviews = PerformanceReview::where('employee_id', $employee->id)->latest()->take(3)->get();
        return view('admin.hrm.employees.show', compact('employee', 'assignedTasks', 'performanceReviews'));
    }

    public function employeeDestroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  HRM — EMPLOYEE BONUSES
    // ═══════════════════════════════════════════════════════
    public function bonusIndex()
    {
        $bonuses = \App\Models\EmployeeBonus::with(['employee', 'project', 'approvedBy'])
            ->latest()->paginate(20);
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        $projects = Project::whereIn('status', ['in_progress', 'planning', 'active', 'completed'])->orderBy('title')->get();
        return view('admin.hrm.bonuses.index', compact('bonuses', 'employees', 'projects'));
    }

    public function bonusStore(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'project_id' => 'nullable|exists:projects,id',
            'type' => 'required|in:performance,project_completion,milestone,special',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'bonus_date' => 'required|date',
        ]);
        $data['bonus_number'] = 'BNS-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $data['status'] = 'pending';
        $data['created_by'] = auth()->id();
        \App\Models\EmployeeBonus::create($data);
        return redirect()->route('admin.bonuses.index')->with('success', 'Bonus created successfully.');
    }

    public function bonusApprove(\App\Models\EmployeeBonus $bonus)
    {
        $bonus->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return redirect()->back()->with('success', 'Bonus approved.');
    }

    public function bonusReject(\App\Models\EmployeeBonus $bonus)
    {
        $bonus->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Bonus rejected.');
    }

    public function bonusMarkPaid(\App\Models\EmployeeBonus $bonus)
    {
        $bonus->update(['status' => 'paid']);
        return redirect()->back()->with('success', 'Bonus marked as paid.');
    }

    public function bonusDestroy(\App\Models\EmployeeBonus $bonus)
    {
        $bonus->delete();
        return redirect()->route('admin.bonuses.index')->with('success', 'Bonus deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  HRM — ATTENDANCE
    // ═══════════════════════════════════════════════════════
    public function attendanceIndex()
    {
        $attendances = Attendance::with('employee')->latest()->paginate(15);
        $employees = Employee::where('status', 'active')->get();
        return view('admin.hrm.attendance.index', compact('attendances', 'employees'));
    }

    public function attendanceStore(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'status' => 'required|string',
            'note' => 'nullable|string',
        ]);
        $data['created_by'] = auth()->id();
        Attendance::create($data);
        return redirect()->route('admin.attendance.index')->with('success', 'Attendance recorded.');
    }

    public function attendanceDestroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('admin.attendance.index')->with('success', 'Attendance record deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  HRM — PAYROLL
    // ═══════════════════════════════════════════════════════
    public function payrollIndex()
    {
        $payrolls = Payroll::with('employee')->latest()->paginate(15);
        $employees = Employee::where('status', 'active')->get();
        return view('admin.hrm.payroll.index', compact('payrolls', 'employees'));
    }

    public function payrollStore(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_number' => 'required|string|unique:payrolls,payroll_number',
            'month' => 'required|string',
            'year' => 'required|integer',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'net_salary' => 'required|numeric|min:0',
            'status' => 'nullable|string',
        ]);
        $data['created_by'] = auth()->id();
        Payroll::create($data);
        return redirect()->route('admin.payroll.index')->with('success', 'Payroll created.');
    }

    public function payrollDestroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('admin.payroll.index')->with('success', 'Payroll deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  HRM — LEAVE
    // ═══════════════════════════════════════════════════════
    public function leaveIndex()
    {
        $leaves = Leave::with('employee')->latest()->paginate(15);
        $employees = Employee::where('status', 'active')->get();
        return view('admin.hrm.leaves.index', compact('leaves', 'employees'));
    }

    public function leaveStore(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days' => 'required|integer|min:1',
            'reason' => 'nullable|string',
            'status' => 'nullable|string',
        ]);
        Leave::create($data);
        return redirect()->route('admin.leaves.index')->with('success', 'Leave request created.');
    }

    public function leaveApprove(Leave $leave)
    {
        $leave->update(['status' => 'approved', 'approved_by' => auth()->id()]);
        return redirect()->back()->with('success', 'Leave approved.');
    }

    public function leaveReject(Leave $leave)
    {
        $leave->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Leave rejected.');
    }

    public function leaveDestroy(Leave $leave)
    {
        $leave->delete();
        return redirect()->route('admin.leaves.index')->with('success', 'Leave deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  HRM — PERFORMANCE
    // ═══════════════════════════════════════════════════════
    public function performanceIndex()
    {
        $reviews = PerformanceReview::with('employee')->latest()->paginate(15);
        $employees = Employee::where('status', 'active')->get();
        return view('admin.hrm.performance.index', compact('reviews', 'employees'));
    }

    public function performanceStore(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'review_period' => 'nullable|string',
            'goals' => 'nullable|string',
            'achievements' => 'nullable|string',
            'feedback' => 'nullable|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);
        $data['reviewer_id'] = auth()->id();
        PerformanceReview::create($data);
        return redirect()->route('admin.performance.index')->with('success', 'Performance review added.');
    }

    public function performanceDestroy(PerformanceReview $review)
    {
        $review->delete();
        return redirect()->route('admin.performance.index')->with('success', 'Review deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  HRM — TRAINING
    // ═══════════════════════════════════════════════════════
    public function trainingIndex()
    {
        $trainings = Training::latest()->paginate(15);
        return view('admin.hrm.training.index', compact('trainings'));
    }

    public function trainingStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trainer' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string',
        ]);
        Training::create($data);
        return redirect()->route('admin.training.index')->with('success', 'Training created.');
    }

    public function trainingDestroy(Training $training)
    {
        $training->delete();
        return redirect()->route('admin.training.index')->with('success', 'Training deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  HRM — RECRUITMENT / JOB POSTINGS
    // ═══════════════════════════════════════════════════════
    public function jobPostingIndex()
    {
        $jobs = JobPosting::latest()->paginate(15);
        return view('admin.hrm.recruitment.index', compact('jobs'));
    }

    public function jobPostingStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'location' => 'nullable|string|max:100',
            'job_type' => 'nullable|string',
            'vacancies' => 'nullable|integer|min:1',
            'deadline' => 'nullable|date',
            'status' => 'nullable|string',
        ]);
        JobPosting::create($data);
        return redirect()->route('admin.job-postings.index')->with('success', 'Job posting created.');
    }

    public function jobPostingDestroy(JobPosting $jobPosting)
    {
        $jobPosting->delete();
        return redirect()->route('admin.job-postings.index')->with('success', 'Job posting deleted.');
    }

    // ───────────────────────────────────────────────────────
    //  Applications
    // ───────────────────────────────────────────────────────
    public function applicationsIndex(Request $request)
    {
        $companyId = session('switched_company_id', auth()->user()->company_id);
        $q = \App\Models\JobApplication::query()->with('jobPosting')
            ->when($companyId, fn($qq) => $qq->where('company_id', $companyId))
            ->when($request->filled('job'), fn($qq) => $qq->where('job_posting_id', $request->integer('job')))
            ->when($request->filled('status'), fn($qq) => $qq->where('status', $request->string('status')))
            ->when($request->filled('search'), function($qq) use ($request) {
                $s = $request->string('search');
                $qq->where(function($w) use ($s){
                    $w->where('full_name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%")
                      ->orWhere('phone', 'like', "%{$s}%");
                });
            })
            ->latest();
        $applications = $q->paginate(15)->withQueryString();
        $jobs = JobPosting::query()->latest()->get(['id','title']);
        return view('admin.hrm.recruitment.applications.index', compact('applications','jobs'));
    }

    public function applicationsForJob(JobPosting $jobPosting, Request $request)
    {
        $companyId = session('switched_company_id', auth()->user()->company_id);
        $applications = \App\Models\JobApplication::query()
            ->where('job_posting_id', $jobPosting->id)
            ->when($companyId, fn($qq) => $qq->where('company_id', $companyId))
            ->latest()->paginate(15)->withQueryString();
        return view('admin.hrm.recruitment.applications.index', [
            'applications' => $applications,
            'jobs' => collect([$jobPosting->only(['id','title'])]),
        ]);
    }

    public function applicationShow(\App\Models\JobApplication $application)
    {
        $application->load(['jobPosting','approvals.approver']);
        return view('admin.hrm.recruitment.applications.show', compact('application'));
    }

    public function applicationApprove(Request $request, \App\Models\JobApplication $application)
    {
        $data = $request->validate([
            'decision' => 'required|in:shortlist,reject,hire',
            'comment' => 'nullable|string',
        ]);
        \App\Models\JobApplicationApproval::create([
            'job_application_id' => $application->id,
            'approved_by' => auth()->id(),
            'decision' => $data['decision'],
            'comment' => $data['comment'] ?? null,
        ]);
        $statusMap = [
            'shortlist' => 'shortlisted',
            'reject' => 'rejected',
            'hire' => 'hired',
        ];
        $application->update(['status' => $statusMap[$data['decision']] ?? $application->status]);
        return back()->with('success', 'Decision recorded.');
    }

    public function applicationCreate()
    {
        $jobs = JobPosting::query()->latest()->get(['id','title']);
        return view('admin.hrm.recruitment.applications.create', compact('jobs'));
    }

    public function applicationStore(Request $request)
    {
        $data = $request->validate([
            'job_posting_id' => 'required|exists:job_postings,id',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'attachments.*' => 'nullable|file|max:5120',
            'notes' => 'nullable|string',
        ]);
        $companyId = session('switched_company_id', auth()->user()->company_id);
        $resumePath = $request->file('resume')?->store('applications/resumes', 'public');
        $coverPath = $request->file('cover_letter')?->store('applications/covers', 'public');
        $extra = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $f) {
                $extra[] = $f->store('applications/attachments', 'public');
            }
        }
        $app = JobApplication::create([
            'job_posting_id' => $data['job_posting_id'],
            'company_id' => $companyId,
            'full_name' => $data['full_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'resume_path' => $resumePath,
            'cover_letter_path' => $coverPath,
            'extra_docs' => $extra ?: null,
            'status' => 'submitted',
            'notes' => $data['notes'] ?? null,
        ]);
        $this->notifyHrNewApplication($app);
        return redirect()->route('admin.applications.index')->with('success', 'Application created.');
    }

    private function notifyHrNewApplication(JobApplication $application): void
    {
        try {
            $hrUsers = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['hr_officer', 'administrator', 'admin_manager']);
            })->pluck('email')->filter()->unique();

            $name = $application->full_name;
            $email = $application->email ?: 'N/A';
            $phone = $application->phone ?: 'N/A';
            $jobTitle = $application->jobPosting ? $application->jobPosting->title : 'N/A';
            $status = ucfirst($application->status);
            $reviewUrl = url('/admin/applications/' . $application->id);

            foreach ($hrUsers as $toEmail) {
                Mail::raw(
                    "New job application received.\n\n"
                    . "Applicant: {$name}\n"
                    . "Email: {$email}\n"
                    . "Phone: {$phone}\n"
                    . "Job: {$jobTitle}\n"
                    . "Status: {$status}\n\n"
                    . "Review at: {$reviewUrl}",
                    function ($msg) use ($toEmail, $name) {
                        $msg->to($toEmail)
                            ->subject('New Job Application: ' . $name);
                    }
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Failed to send HR application notification: ' . $e->getMessage());
        }
    }

    // ───────────────────────────────────────────────────────
    //  Public Careers
    // ───────────────────────────────────────────────────────
    public function careersJobsIndex()
    {
        $jobs = JobPosting::where('status', 'open')->latest()->paginate(12);
        return view('careers.jobs.index', compact('jobs'));
    }

    public function careersApplyForm(JobPosting $jobPosting)
    {
        abort_if($jobPosting->status !== 'open', 404);
        return view('careers.jobs.apply', compact('jobPosting'));
    }

    public function careersApplySubmit(Request $request, JobPosting $jobPosting)
    {
        abort_if($jobPosting->status !== 'open', 404);
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'attachments.*' => 'nullable|file|max:5120',
            'notes' => 'nullable|string',
        ]);
        $resumePath = $request->file('resume')?->store('applications/resumes', 'public');
        $coverPath = $request->file('cover_letter')?->store('applications/covers', 'public');
        $extra = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $f) {
                $extra[] = $f->store('applications/attachments', 'public');
            }
        }
        $app = JobApplication::create([
            'job_posting_id' => $jobPosting->id,
            'company_id' => $jobPosting->company_id ?? null,
            'full_name' => $data['full_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'resume_path' => $resumePath,
            'cover_letter_path' => $coverPath,
            'extra_docs' => $extra ?: null,
            'status' => 'submitted',
            'notes' => $data['notes'] ?? null,
        ]);
        $this->notifyHrNewApplication($app);
        return redirect()->route('careers')->with('success', 'Application submitted successfully.');
    }

    // ═══════════════════════════════════════════════════════
    //  HRM — ASSETS
    // ═══════════════════════════════════════════════════════
    public function assetIndex()
    {
        $assets = EmployeeAsset::with('employee')->latest()->paginate(15);
        $employees = Employee::where('status', 'active')->get();
        return view('admin.hrm.assets.index', compact('assets', 'employees'));
    }

    public function assetStore(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'asset_name' => 'required|string|max:255',
            'asset_type' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'assigned_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'status' => 'nullable|string',
        ]);
        EmployeeAsset::create($data);
        return redirect()->route('admin.assets.index')->with('success', 'Asset assigned.');
    }

    public function assetDestroy(EmployeeAsset $asset)
    {
        $asset->delete();
        return redirect()->route('admin.assets.index')->with('success', 'Asset record deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  HRM — EVENTS
    // ═══════════════════════════════════════════════════════
    public function hrEventIndex()
    {
        $events = HrEvent::latest()->paginate(15);
        return view('admin.hrm.events.index', compact('events'));
    }

    public function hrEventStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'event_time' => 'nullable',
            'location' => 'nullable|string|max:255',
            'type' => 'nullable|string',
        ]);
        HrEvent::create($data);
        return redirect()->route('admin.hr-events.index')->with('success', 'Event created.');
    }

    public function hrEventDestroy(HrEvent $hrEvent)
    {
        $hrEvent->delete();
        return redirect()->route('admin.hr-events.index')->with('success', 'Event deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  HRM — POLICIES
    // ═══════════════════════════════════════════════════════
    public function policyIndex()
    {
        $policies = Policy::latest()->paginate(15);
        return view('admin.hrm.policies.index', compact('policies'));
    }

    public function policyStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        Policy::create($data);
        return redirect()->route('admin.policies.index')->with('success', 'Policy created.');
    }

    public function policyDestroy(Policy $policy)
    {
        $policy->delete();
        return redirect()->route('admin.policies.index')->with('success', 'Policy deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  CRM — LEADS
    // ═══════════════════════════════════════════════════════
    public function crmLeadIndex()
    {
        $leads = CrmLead::with(['deals', 'assignedTo'])->latest()->paginate(15);
        $users = User::where('role', 'admin')->get();
        return view('admin.crm.leads.index', compact('leads', 'users'));
    }

    public function crmLeadStore(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:100',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        $data['lead_number'] = 'LEAD-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $data['created_by'] = auth()->id();
        CrmLead::create($data);
        return redirect()->route('admin.crm-leads.index')->with('success', 'Lead created.');
    }

    public function crmLeadDestroy(CrmLead $lead)
    {
        $lead->delete();
        return redirect()->route('admin.crm-leads.index')->with('success', 'Lead deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  CRM — DEALS
    // ═══════════════════════════════════════════════════════
    public function crmDealIndex()
    {
        $deals = CrmDeal::with('lead')->latest()->paginate(15);
        $leads = CrmLead::all();
        $users = User::where('role', 'admin')->get();
        return view('admin.crm.deals.index', compact('deals', 'leads', 'users'));
    }

    public function crmDealStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'lead_id' => 'nullable|exists:crm_leads,id',
            'value' => 'required|numeric|min:0',
            'stage' => 'nullable|string',
            'expected_close_date' => 'nullable|date',
            'status' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);
        $data['deal_number'] = 'DEAL-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        CrmDeal::create($data);
        return redirect()->route('admin.crm-deals.index')->with('success', 'Deal created.');
    }

    public function crmDealDestroy(CrmDeal $deal)
    {
        $deal->delete();
        return redirect()->route('admin.crm-deals.index')->with('success', 'Deal deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  CRM — CONTRACTS
    // ═══════════════════════════════════════════════════════
    public function crmContractIndex()
    {
        $contracts = CrmContract::latest()->paginate(15);
        $deals = CrmDeal::all();
        return view('admin.crm.contracts.index', compact('contracts', 'deals'));
    }

    public function crmContractStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'deal_id' => 'nullable|exists:crm_deals,id',
            'client_name' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'terms' => 'nullable|string',
            'status' => 'nullable|string',
        ]);
        $data['contract_number'] = 'CON-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        CrmContract::create($data);
        return redirect()->route('admin.crm-contracts.index')->with('success', 'Contract created.');
    }

    public function crmContractDestroy(CrmContract $contract)
    {
        $contract->delete();
        return redirect()->route('admin.crm-contracts.index')->with('success', 'Contract deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  CRM — CONTACTS
    // ═══════════════════════════════════════════════════════
    public function crmContactIndex()
    {
        $contacts = CrmContact::latest()->paginate(15);
        return view('admin.crm.contacts.index', compact('contacts'));
    }

    public function crmContactStore(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        CrmContact::create($data);
        return redirect()->route('admin.crm-contacts.index')->with('success', 'Contact added.');
    }

    public function crmContactDestroy(CrmContact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.crm-contacts.index')->with('success', 'Contact deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  ACCOUNTING — BANK ACCOUNTS
    // ═══════════════════════════════════════════════════════
    public function bankAccountIndex()
    {
        $accounts = BankAccount::latest()->paginate(15);
        return view('admin.accounting.bank-accounts.index', compact('accounts'));
    }

    public function bankAccountStore(Request $request)
    {
        $data = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:10',
            'opening_balance' => 'nullable|numeric|min:0',
            'current_balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        if (!isset($data['current_balance'])) $data['current_balance'] = $data['opening_balance'] ?? 0;
        BankAccount::create($data);
        return redirect()->route('admin.bank-accounts.index')->with('success', 'Bank account created.');
    }

    public function bankAccountUpdate(Request $request, BankAccount $bankAccount)
    {
        $data = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:10',
            'current_balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $bankAccount->update($data);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Bank account updated.', 'item' => $bankAccount]);
        }
        return redirect()->route('admin.bank-accounts.index')->with('success', 'Bank account updated.');
    }

    public function bankAccountDestroy(BankAccount $bankAccount, Request $request)
    {
        $bankAccount->delete();
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Bank account deleted.']);
        }
        return redirect()->route('admin.bank-accounts.index')->with('success', 'Bank account deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  ACCOUNTING — TRANSFERS
    // ═══════════════════════════════════════════════════════
    public function accTransferIndex()
    {
        $transfers = BankTransferAcc::with(['fromAccount', 'toAccount'])->latest()->paginate(15);
        $accounts = BankAccount::where('is_active', true)->get();
        return view('admin.accounting.transfers.index', compact('transfers', 'accounts'));
    }

    public function accTransferStore(Request $request)
    {
        $data = $request->validate([
            'from_account_id' => 'required|exists:bank_accounts,id',
            'to_account_id' => 'required|exists:bank_accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:0.01',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'nullable|string',
        ]);
        $data['transfer_number'] = 'TRF-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        BankTransferAcc::create($data);
        return redirect()->route('admin.acc-transfers.index')->with('success', 'Transfer recorded.');
    }

    public function accTransferDestroy(BankTransferAcc $transfer)
    {
        $transfer->delete();
        return redirect()->route('admin.acc-transfers.index')->with('success', 'Transfer deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  ACCOUNTING — EXPENSES
    // ═══════════════════════════════════════════════════════
    public function expenseIndex()
    {
        $expenses = Expense::latest()->paginate(15);
        $accounts = BankAccount::where('is_active', true)->get();
        return view('admin.accounting.expenses.index', compact('expenses', 'accounts'));
    }

    public function expenseStore(Request $request)
    {
        $data = $request->validate([
            'category' => 'nullable|string|max:100',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'payment_method' => 'nullable|string',
            'payee' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        $data['expense_number'] = 'EXP-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $data['created_by'] = auth()->id();
        Expense::create($data);
        return redirect()->route('admin.expenses.index')->with('success', 'Expense recorded.');
    }

    public function expenseDestroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  ACCOUNTING — REVENUE
    // ═══════════════════════════════════════════════════════
    public function revenueIndex()
    {
        $revenues = Revenue::latest()->paginate(15);
        $accounts = BankAccount::where('is_active', true)->get();
        return view('admin.accounting.revenues.index', compact('revenues', 'accounts'));
    }

    public function revenueStore(Request $request)
    {
        $data = $request->validate([
            'category' => 'nullable|string|max:100',
            'amount' => 'required|numeric|min:0',
            'revenue_date' => 'required|date',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'payment_method' => 'nullable|string',
            'payer' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        $data['revenue_number'] = 'REV-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $data['created_by'] = auth()->id();
        Revenue::create($data);
        return redirect()->route('admin.revenues.index')->with('success', 'Revenue recorded.');
    }

    public function revenueDestroy(Revenue $revenue)
    {
        $revenue->delete();
        return redirect()->route('admin.revenues.index')->with('success', 'Revenue deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  ACCOUNTING — BILLS
    // ═══════════════════════════════════════════════════════
    public function billIndex()
    {
        $bills = Bill::latest()->paginate(15);
        return view('admin.accounting.bills.index', compact('bills'));
    }

    public function billStore(Request $request)
    {
        $data = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'bill_date' => 'required|date',
            'due_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $data['bill_number'] = 'BILL-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        Bill::create($data);
        return redirect()->route('admin.bills.index')->with('success', 'Bill created.');
    }

    public function billDestroy(Bill $bill)
    {
        $bill->delete();
        return redirect()->route('admin.bills.index')->with('success', 'Bill deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  ACCOUNTING — ESTIMATES
    // ═══════════════════════════════════════════════════════
    public function estimateIndex()
    {
        $estimates = Estimate::latest()->paginate(15);
        return view('admin.accounting.estimates.index', compact('estimates'));
    }

    public function estimateStore(Request $request)
    {
        $data = $request->validate([
            'client_name' => 'required|string|max:255',
            'estimate_date' => 'required|date',
            'expiry_date' => 'nullable|date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $data['estimate_number'] = 'EST-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        Estimate::create($data);
        return redirect()->route('admin.estimates.index')->with('success', 'Estimate created.');
    }

    public function estimateDestroy(Estimate $estimate)
    {
        $estimate->delete();
        return redirect()->route('admin.estimates.index')->with('success', 'Estimate deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  PROJECTS
    // ═══════════════════════════════════════════════════════
    public function projectIndex()
    {
        $projects = Project::withCount('employees')->latest()->paginate(15);
        $managers = User::where('role', 'admin')->get();
        $employees = Employee::where('status', 'active')->with('projects')->orderBy('first_name')->get();
        return view('admin.projects.index', compact('projects', 'managers', 'employees'));
    }

    public function convertProposalToProject(SalesProposal $proposal)
    {
        if ($proposal->status !== 'accepted') {
            return redirect()->back()->with('error', 'Only accepted quotations can be converted to a project.');
        }

        $project = Project::create([
            'project_number' => 'PRJ-' . date('Ym') . '-' . strtoupper(Str::random(4)),
            'title' => 'Project for ' . ($proposal->customer?->name ?? 'Customer') . ' - ' . $proposal->proposal_number,
            'description' => $proposal->notes,
            'start_date' => now(),
            'due_date' => $proposal->due_date,
            'status' => 'in_progress',
            'priority' => 'medium',
            'manager_id' => auth()->id(),
            'progress' => 0,
            'budget' => $proposal->total_amount,
            'customer_id' => $proposal->customer_id,
            'proposal_id' => $proposal->id,
        ]);

        return redirect()->route('admin.projects.show', $project)->with('success', 'Project created from accepted quotation.');
    }

    public function projectStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'progress' => 'nullable|integer|min:0|max:100',
            'budget' => 'nullable|numeric|min:0',
            'recurring_invoicing' => 'nullable|boolean',
            'billing_amount' => 'nullable|numeric|min:0',
            'billing_day' => 'nullable|integer|min:1|max:28',
            'invoicing_end_date' => 'nullable|date',
            'invoicing_type' => 'nullable|in:recurring,one_time,none',
            'one_time_amount' => 'nullable|numeric|min:0',
            'one_time_when' => 'nullable|in:immediately,manual,completion',
        ]);
        $invoicingType = $data['invoicing_type'] ?? 'none';
        $data['recurring_invoicing'] = ($invoicingType === 'recurring');
        if ($invoicingType !== 'recurring') {
            $data['billing_amount'] = null;
            $data['billing_day'] = null;
        }
        $oneTimeAmount = $data['one_time_amount'] ?? 0;
        $oneTimeWhen = $data['one_time_when'] ?? 'manual';
        unset($data['invoicing_type'], $data['one_time_amount'], $data['one_time_when']);

        $data['project_number'] = 'PRJ-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $data['company_id'] = session('current_company_id');
        $projectIds = $request->input('project_employee_ids', []);
        $projectRoles = $request->input('project_employee_roles', []);
        $project = Project::create($data);
        if (!empty($projectIds)) {
            $syncData = [];
            foreach ($projectIds as $eid) {
                $syncData[$eid] = [
                    'role' => $projectRoles[$eid] ?? null,
                    'assigned_from' => now(),
                    'is_active' => true,
                ];
            }
            $project->employees()->sync($syncData);
        }

        if ($invoicingType === 'one_time' && $oneTimeWhen === 'immediately' && $oneTimeAmount > 0) {
            $project->update(['budget' => $oneTimeAmount]);
            $customerId = $project->customer_id ?? ($project->deal?->customer_id ?? null);
            if ($customerId) {
                $invoiceNumber = 'INV-P-' . date('Ymd') . '-' . strtoupper(Str::random(4));
                $invoice = SalesInvoice::create([
                    'invoice_number' => $invoiceNumber,
                    'invoice_date' => now(),
                    'due_date' => now()->copy()->addDays(14),
                    'customer_id' => $customerId,
                    'subtotal' => $oneTimeAmount,
                    'tax_amount' => round($oneTimeAmount * 0.18, 2),
                    'discount_amount' => 0,
                    'total_amount' => round($oneTimeAmount * 1.18, 2),
                    'paid_amount' => 0,
                    'balance_amount' => round($oneTimeAmount * 1.18, 2),
                    'status' => 'draft',
                    'type' => 'service',
                    'payment_terms' => 'Due in 14 days',
                    'notes' => 'One-time invoice for ' . $project->title,
                    'creator_id' => auth()->id(),
                    'created_by' => auth()->id(),
                    'project_id' => $project->id,
                ]);
                $invoice->items()->create([
                    'product_name' => 'Project: ' . $project->title,
                    'quantity' => 1,
                    'unit_price' => $oneTimeAmount,
                    'discount_amount' => 0,
                    'discount_percentage' => 0,
                    'tax_percentage' => 18,
                    'tax_amount' => round($oneTimeAmount * 0.18, 2),
                    'total_amount' => round($oneTimeAmount * 1.18, 2),
                ]);
            }
        }

        $msg = 'Project created with ' . count($projectIds) . ' staff assigned.';
        if ($invoicingType === 'recurring') {
            $msg .= ' Recurring invoicing enabled.';
        } elseif ($invoicingType === 'one_time' && $oneTimeWhen === 'immediately') {
            $msg .= ' One-time invoice generated.';
        } elseif ($invoicingType === 'one_time') {
            $msg .= ' One-time invoice will be generated ' . ($oneTimeWhen === 'completion' ? 'on completion.' : 'manually.');
        }
        return redirect()->route('admin.projects.index')->with('success', $msg);
    }

    public function projectShow(Project $project)
    {
        $project->load(['tasks', 'bugs', 'timesheets', 'meetings', 'documents', 'employees', 'bonuses.employee', 'invoices']);
        return view('admin.projects.show', compact('project'));
    }

    public function projectSettlements(Project $project)
    {
        $project->load(['invoices', 'vendorInvoices', 'officeExpenses', 'clientReceipts', 'timesheets.employee']);

        $startDate = $project->start_date ?? $project->created_at->copy()->startOfMonth();
        $endDate = $project->due_date ?? now()->copy()->endOfMonth();
        if ($endDate->gt(now())) $endDate = now()->copy()->endOfMonth();

        $months = [];
        $current = $startDate->copy()->startOfMonth();
        while ($current->lte($endDate)) {
            $monthKey = $current->format('Y-m');
            $monthLabel = $current->format('M Y');

            $invoiced = SalesInvoice::where('project_id', $project->id)
                ->whereYear('invoice_date', $current->year)
                ->whereMonth('invoice_date', $current->month)
                ->get();
            $invoicedTotal = $invoiced->sum('total_amount');
            $invoicedPaid = $invoiced->sum('paid_amount');

            $receipts = ClientReceipt::where('project_id', $project->id)
                ->whereYear('receipt_date', $current->year)
                ->whereMonth('receipt_date', $current->month)
                ->get();
            $receivedTotal = $receipts->sum('amount');

            $vendorInvoices = VendorInvoice::where('project_id', $project->id)
                ->whereYear('invoice_date', $current->year)
                ->whereMonth('invoice_date', $current->month)
                ->get();
            $vendorCost = $vendorInvoices->sum('total');
            $vendorPaid = $vendorInvoices->sum('amount_paid');

            $officeExp = OfficeExpense::where('project_id', $project->id)
                ->where('status', 'approved')
                ->whereYear('expense_date', $current->year)
                ->whereMonth('expense_date', $current->month)
                ->sum('amount');

            $timesheets = Timesheet::where('project_id', $project->id)
                ->whereYear('date', $current->year)
                ->whereMonth('date', $current->month)
                ->with('employee')
                ->get();
            $staffHours = $timesheets->sum('hours');
            $staffCost = 0;
            foreach ($timesheets as $ts) {
                if ($ts->employee && $ts->employee->salary > 0) {
                    $hourlyRate = $ts->employee->salary / 160;
                    $staffCost += $ts->hours * $hourlyRate;
                }
            }

            $bonusCost = \App\Models\EmployeeBonus::where('project_id', $project->id)
                ->whereIn('status', ['approved', 'paid'])
                ->whereYear('bonus_date', $current->year)
                ->whereMonth('bonus_date', $current->month)
                ->sum('amount');

            $totalCost = $vendorCost + $officeExp + $staffCost + $bonusCost;
            $net = $receivedTotal - $totalCost;
            $outstanding = $invoicedTotal - $invoicedPaid;

            $months[] = [
                'key' => $monthKey,
                'label' => $monthLabel,
                'invoiced' => $invoicedTotal,
                'received' => $receivedTotal,
                'outstanding' => $outstanding,
                'vendor_cost' => $vendorCost,
                'vendor_paid' => $vendorPaid,
                'office_cost' => $officeExp,
                'staff_hours' => $staffHours,
                'staff_cost' => $staffCost,
                'bonus_cost' => $bonusCost,
                'total_cost' => $totalCost,
                'net' => $net,
                'invoices' => $invoiced,
                'receipts' => $receipts,
                'vendor_invoices' => $vendorInvoices,
            ];

            $current->addMonth();
        }

        $totals = [
            'invoiced' => array_sum(array_column($months, 'invoiced')),
            'received' => array_sum(array_column($months, 'received')),
            'outstanding' => array_sum(array_column($months, 'outstanding')),
            'vendor_cost' => array_sum(array_column($months, 'vendor_cost')),
            'office_cost' => array_sum(array_column($months, 'office_cost')),
            'staff_cost' => array_sum(array_column($months, 'staff_cost')),
            'staff_hours' => array_sum(array_column($months, 'staff_hours')),
            'bonus_cost' => array_sum(array_column($months, 'bonus_cost')),
            'total_cost' => array_sum(array_column($months, 'total_cost')),
            'net' => array_sum(array_column($months, 'net')),
        ];
        $totals['margin'] = $totals['received'] > 0 ? ($totals['net'] / $totals['received']) * 100 : 0;

        return view('admin.projects.settlements', compact('project', 'months', 'totals'));
    }

    public function projectSettlementsIndex()
    {
        $projects = Project::whereIn('status', ['in_progress', 'planning', 'active', 'completed'])
            ->with(['invoices', 'clientReceipts', 'vendorInvoices', 'officeExpenses'])
            ->orderBy('title')
            ->get();

        $projectSummaries = [];
        foreach ($projects as $project) {
            $invoiced = $project->invoices->sum('total_amount');
            $received = $project->clientReceipts->sum('amount');
            $vendorCost = $project->vendorInvoices->sum('total');
            $officeCost = $project->officeExpenses->where('status', 'approved')->sum('amount');
            $totalCost = $vendorCost + $officeCost;
            $net = $received - $totalCost;

            $projectSummaries[] = [
                'id' => $project->id,
                'title' => $project->title,
                'project_number' => $project->project_number,
                'status' => $project->status,
                'recurring' => $project->recurring_invoicing,
                'billing_amount' => $project->billing_amount ?? 0,
                'invoiced' => $invoiced,
                'received' => $received,
                'outstanding' => $invoiced - $project->invoices->sum('paid_amount'),
                'vendor_cost' => $vendorCost,
                'office_cost' => $officeCost,
                'total_cost' => $totalCost,
                'net' => $net,
                'margin' => $received > 0 ? ($net / $received) * 100 : 0,
            ];
        }

        return view('admin.projects.settlements-index', compact('projectSummaries'));
    }

    public function generateProjectInvoice(Project $project)
    {
        if ($project->recurring_invoicing) {
            return redirect()->back()->with('error', 'This project uses recurring invoicing. Use the recurring invoice generator instead.');
        }

        if (!in_array($project->status, ['completed', 'in_progress', 'planning'])) {
            return redirect()->back()->with('error', 'Invalid project status for invoicing.');
        }

        // Prevent duplicate invoices tied to this project
        $existing = SalesInvoice::where('project_id', $project->id)->first();
        if ($existing) {
            return redirect()->route('admin.sales-invoices.show', $existing)->with('info', 'Invoice for this project already exists.');
        }

        $customerId = $project->customer_id ?? ($project->deal?->customer_id ?? null);
        if (!$customerId) {
            return redirect()->back()->with('error', 'This project has no linked customer. Please assign a customer to the project or its deal before generating an invoice.');
        }

        $invoice = SalesInvoice::create([
            'invoice_number' => 'INV-P-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'invoice_date' => now(),
            'due_date' => now()->copy()->addDays(14),
            'customer_id' => $customerId,
            'subtotal' => $project->budget,
            'tax_amount' => round($project->budget * 0.18, 2),
            'discount_amount' => 0,
            'total_amount' => round($project->budget * 1.18, 2),
            'paid_amount' => 0,
            'balance_amount' => round($project->budget * 1.18, 2),
            'status' => 'draft',
            'type' => 'service',
            'payment_terms' => 'Due in 14 days',
            'notes' => 'Invoice for project ' . ($project->title ?? $project->project_number),
            'creator_id' => auth()->id(),
            'created_by' => auth()->id(),
            'project_id' => $project->id,
            'proposal_id' => $project->proposal_id,
        ]);

        // Single summary line
        $invoice->items()->create([
            'product_name' => 'Project: ' . ($project->title ?? $project->project_number),
            'quantity' => 1,
            'unit_price' => $project->budget,
            'discount_amount' => 0,
            'discount_percentage' => 0,
            'tax_percentage' => 18,
            'tax_amount' => round($project->budget * 0.18, 2),
            'total_amount' => round($project->budget * 1.18, 2),
        ]);

        // Optionally update project status to invoiced if already completed
        if ($project->status === 'completed') {
            $project->update(['status' => 'invoiced']);
        }

        return redirect()->route('admin.sales-invoices.show', $invoice)->with('success', 'Tax invoice generated from project.');
    }

    public function projectDestroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Project deleted.');
    }

    public function projectTaskStore(Request $request, Project $project)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);
        $data['project_id'] = $project->id;
        ProjectTask::create($data);
        return redirect()->back()->with('success', 'Task added.');
    }

    public function projectTaskDestroy(ProjectTask $task)
    {
        $task->delete();
        return redirect()->back()->with('success', 'Task deleted.');
    }

    public function timesheetIndex()
    {
        $timesheets = Timesheet::with(['project', 'task'])->latest()->paginate(15);
        $projects = Project::all();
        return view('admin.projects.timesheets', compact('timesheets', 'projects'));
    }

    public function timesheetStore(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'nullable|exists:project_tasks,id',
            'employee_id' => 'nullable|exists:employees,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);
        Timesheet::create($data);
        return redirect()->route('admin.timesheets.index')->with('success', 'Timesheet entry added.');
    }

    public function timesheetDestroy(Timesheet $timesheet)
    {
        $timesheet->delete();
        return redirect()->route('admin.timesheets.index')->with('success', 'Timesheet deleted.');
    }

    public function bugIndex()
    {
        $bugs = ProjectBug::with('project')->latest()->paginate(15);
        $projects = Project::all();
        return view('admin.projects.bugs', compact('bugs', 'projects'));
    }

    public function bugStore(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'severity' => 'nullable|string',
            'status' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        $data['reported_by'] = auth()->id();
        ProjectBug::create($data);
        return redirect()->route('admin.bugs.index')->with('success', 'Bug reported.');
    }

    public function bugDestroy(ProjectBug $bug)
    {
        $bug->delete();
        return redirect()->route('admin.bugs.index')->with('success', 'Bug deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  PRODUCTS & INVENTORY
    // ═══════════════════════════════════════════════════════
    public function productCategoryIndex()
    {
        $categories = ProductCategory::latest()->paginate(15);
        return view('admin.products.categories', compact('categories'));
    }

    public function productCategoryStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);
        $data['is_active'] = $request->boolean('is_active', true);
        ProductCategory::create($data);
        return redirect()->route('admin.product-categories.index')->with('success', 'Category created.');
    }

    public function productCategoryDestroy(ProductCategory $category)
    {
        $category->delete();
        return redirect()->route('admin.product-categories.index')->with('success', 'Category deleted.');
    }

    public function productIndex()
    {
        $products = Product::with(['category', 'warehouse', 'company'])->latest()->paginate(15);
        $categories = ProductCategory::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $companies = Company::orderBy('is_group', 'desc')->orderBy('name')->get();
        $userCompany = auth()->user()->company;
        $isGroupUser = $userCompany && $userCompany->is_group;
        $currentCompanyId = session('switched_company_id', auth()->user()->company_id);
        return view('admin.products.index', compact('products', 'categories', 'warehouses', 'companies', 'userCompany', 'isGroupUser', 'currentCompanyId'));
    }

    public function productStore(Request $request)
    {
        $isAjax = $request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category_id' => 'nullable|exists:product_categories,id',
                'unit' => 'nullable|string|max:20',
                'purchase_price' => 'nullable|numeric|min:0',
                'sale_price' => 'nullable|numeric|min:0',
                'stock_quantity' => 'nullable|integer|min:0',
                'reorder_level' => 'nullable|integer|min:0',
                'type' => 'nullable|string',
                'is_active' => 'boolean',
                'warehouse_id' => 'nullable|exists:warehouses,id',
                'company_id' => 'nullable|exists:companies,id',
            ]);
            $data['product_code'] = 'PRD-' . strtoupper(Str::random(8));
            $data['is_active'] = $request->boolean('is_active', true);
            if (!isset($data['company_id']) || !$data['company_id']) {
                $switchedId = session('switched_company_id');
                $data['company_id'] = $switchedId ?? auth()->user()->company_id;
            }

            $product = Product::create($data);
            $product->load(['category', 'warehouse', 'company']);

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product "' . $data['name'] . '" created successfully!',
                    'product' => $product
                ]);
            }

            return redirect()->route('admin.products.index')->with('success', 'Product "' . $data['name'] . '" created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error creating product: ' . $e->getMessage())->withInput();
        }
    }

    public function productDestroy(Product $product)
    {
        $name = $product->name;
        $product->delete();

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Product "' . $name . '" deleted successfully!']);
        }
        return redirect()->route('admin.products.index')->with('success', 'Product "' . $name . '" deleted successfully!');
    }

    public function supplierIndex()
    {
        $suppliers = Supplier::latest()->paginate(15);
        return view('admin.products.suppliers', compact('suppliers'));
    }

    public function supplierStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'payment_terms' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        Supplier::create($data);
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier created.');
    }

    public function supplierDestroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier deleted.');
    }

    public function stockMovementIndex()
    {
        $movements = StockMovement::with(['product', 'warehouse'])->latest()->paginate(15);
        return view('admin.products.stock-movements', compact('movements'));
    }

    // ═══════════════════════════════════════════════════════
    //  POS
    // ═══════════════════════════════════════════════════════
    public function posIndex()
    {
        $products = Product::where('is_active', true)->where('stock_quantity', '>', 0)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('admin.pos.index', compact('products', 'warehouses'));
    }

    public function posStore(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);
        $data['sale_number'] = 'POS-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $data['cashier_id'] = auth()->id();
        $data['status'] = 'completed';
        $sale = PosSale::create($data);
        foreach ($data['items'] as $item) {
            $sale->items()->create($item);
            if (!empty($item['product_id'])) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->decrement('stock_quantity', $item['quantity']);
                }
            }
        }
        return response()->json(['success' => true, 'sale_number' => $sale->sale_number]);
    }

    public function posReports()
    {
        $sales = PosSale::with('items')->latest()->paginate(20);
        $todayTotal = PosSale::whereDate('created_at', today())->sum('total_amount');
        $monthTotal = PosSale::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('total_amount');
        return view('admin.pos.reports', compact('sales', 'todayTotal', 'monthTotal'));
    }

    public function posSaleShow(PosSale $posSale)
    {
        $posSale->load('items');
        return view('admin.pos.show', compact('posSale'));
    }

    public function posSaleDestroy(PosSale $posSale)
    {
        $posSale->delete();
        return redirect()->route('admin.pos.reports')->with('success', 'POS sale deleted.');
    }

    public function crmLeadPdf(CrmLead $lead)
    {
        $lead->load(['deals', 'assignedTo']);
        $company = auth()->user()->company ?? \App\Models\Company::where('is_group', true)->first();

        $pdf = Pdf::loadView('pdf.lead', compact('lead', 'company'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf->download('lead-' . $lead->lead_number . '.pdf');
    }

    public function crmDealPdf(CrmDeal $deal)
    {
        $deal->load(['lead', 'contracts', 'project']);
        $company = auth()->user()->company ?? \App\Models\Company::where('is_group', true)->first();

        $pdf = Pdf::loadView('pdf.deal', compact('deal', 'company'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf->download('deal-' . $deal->deal_number . '.pdf');
    }

    public function projectPdf(Project $project)
    {
        $project->load(['tasks', 'bugs', 'timesheets.employee', 'manager', 'deal', 'employees', 'bonuses', 'invoices']);
        $company = auth()->user()->company ?? \App\Models\Company::where('is_group', true)->first();

        $pdf = Pdf::loadView('pdf.project', compact('project', 'company'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf->download('project-' . $project->project_number . '.pdf');
    }
}
