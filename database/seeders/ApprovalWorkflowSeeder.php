<?php

namespace Database\Seeders;

use App\Models\ApprovalWorkflow;
use App\Models\ApprovalStep;
use Illuminate\Database\Seeder;

class ApprovalWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $companyId = 1;

        // ═══ LPO Approval Workflow ═══
        $lpoWf = ApprovalWorkflow::create([
            'name' => 'LPO Approval Process',
            'module' => 'lpo',
            'description' => 'Multi-level approval for Local Purchase Orders',
            'is_active' => true,
            'company_id' => $companyId,
            'created_by' => 1,
        ]);

        ApprovalStep::create(['workflow_id' => $lpoWf->id, 'level' => 1, 'name' => 'Procurement Officer Review', 'approver_type' => 'role', 'approver_role' => 'procurement', 'order' => 0]);
        ApprovalStep::create(['workflow_id' => $lpoWf->id, 'level' => 2, 'name' => 'Technical Manager Approval', 'approver_type' => 'role', 'approver_role' => 'manager', 'order' => 1]);
        ApprovalStep::create(['workflow_id' => $lpoWf->id, 'level' => 3, 'name' => 'Finance Manager Approval', 'approver_type' => 'role', 'approver_role' => 'finance', 'order' => 2]);
        ApprovalStep::create(['workflow_id' => $lpoWf->id, 'level' => 4, 'name' => 'Managing Director Final Approval', 'approver_type' => 'role', 'approver_role' => 'admin', 'is_final' => true, 'order' => 3]);

        // ═══ Office Expense Approval Workflow ═══
        $expenseWf = ApprovalWorkflow::create([
            'name' => 'Office Expense Approval',
            'module' => 'office_expense',
            'description' => 'Approval for office-related expenses',
            'is_active' => true,
            'company_id' => $companyId,
            'created_by' => 1,
        ]);

        ApprovalStep::create(['workflow_id' => $expenseWf->id, 'level' => 1, 'name' => 'Department Head Approval', 'approver_type' => 'role', 'approver_role' => 'manager', 'order' => 0]);
        ApprovalStep::create(['workflow_id' => $expenseWf->id, 'level' => 2, 'name' => 'Finance Manager Approval', 'approver_type' => 'role', 'approver_role' => 'finance', 'is_final' => true, 'order' => 1]);

        // ═══ Project Budget Approval Workflow ═══
        $budgetWf = ApprovalWorkflow::create([
            'name' => 'Project Budget Approval',
            'module' => 'budget',
            'description' => 'Approval for project budget allocations',
            'is_active' => true,
            'company_id' => $companyId,
            'created_by' => 1,
        ]);

        ApprovalStep::create(['workflow_id' => $budgetWf->id, 'level' => 1, 'name' => 'Project Manager Review', 'approver_type' => 'role', 'approver_role' => 'manager', 'order' => 0]);
        ApprovalStep::create(['workflow_id' => $budgetWf->id, 'level' => 2, 'name' => 'Finance Manager Review', 'approver_type' => 'role', 'approver_role' => 'finance', 'order' => 1]);
        ApprovalStep::create(['workflow_id' => $budgetWf->id, 'level' => 3, 'name' => 'Managing Director Final Approval', 'approver_type' => 'role', 'approver_role' => 'admin', 'is_final' => true, 'order' => 2]);

        // ═══ Vendor Invoice Approval Workflow ═══
        $invoiceWf = ApprovalWorkflow::create([
            'name' => 'Vendor Invoice Approval',
            'module' => 'vendor_invoice',
            'description' => 'Approval for vendor/supplier invoices',
            'is_active' => true,
            'company_id' => $companyId,
            'created_by' => 1,
        ]);

        ApprovalStep::create(['workflow_id' => $invoiceWf->id, 'level' => 1, 'name' => 'Procurement Verification', 'approver_type' => 'role', 'approver_role' => 'procurement', 'order' => 0]);
        ApprovalStep::create(['workflow_id' => $invoiceWf->id, 'level' => 2, 'name' => 'Finance Manager Approval', 'approver_type' => 'role', 'approver_role' => 'finance', 'is_final' => true, 'order' => 1]);

        // ═══ Tender Submission Approval Workflow ═══
        $tenderWf = ApprovalWorkflow::create([
            'name' => 'Tender Submission Approval',
            'module' => 'tender',
            'description' => 'Approval before submitting tenders',
            'is_active' => true,
            'company_id' => $companyId,
            'created_by' => 1,
        ]);

        ApprovalStep::create(['workflow_id' => $tenderWf->id, 'level' => 1, 'name' => 'Technical Review', 'approver_type' => 'role', 'approver_role' => 'manager', 'order' => 0]);
        ApprovalStep::create(['workflow_id' => $tenderWf->id, 'level' => 2, 'name' => 'Managing Director Approval', 'approver_type' => 'role', 'approver_role' => 'admin', 'is_final' => true, 'order' => 1]);
    }
}
