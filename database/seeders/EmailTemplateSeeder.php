<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $from = 'no-reply@asyxgroup.co.tz';

        $templates = [
            // ─── Auth & Accounts ───
            ['name' => 'Welcome Email', 'module_name' => 'auth', 'subject' => 'Welcome to {app_name} — your account is ready', 'content' => "Dear {user_name},\n\nYour account has been created successfully.\n\nLogin email: {user_email}\nPortal: {login_url}\n\nIf you did not create this account, please ignore this email.\n\nRegards,\n{app_name} Team"],
            ['name' => 'Password Reset Code', 'module_name' => 'auth', 'subject' => 'Your password reset code', 'content' => "Dear {user_name},\n\nYour password reset code is: {reset_code}\n\nIt expires in 15 minutes. If you didn't request a reset, please ignore this email.\n\nRegards,\n{app_name} Security"],
            ['name' => 'Team Member Invitation', 'module_name' => 'auth', 'subject' => 'You\'ve been added to {company_name}', 'content' => "Dear {user_name},\n\n{invited_by} has added you to {company_name} on {app_name}.\n\nLog in with your email to access your workspace.\n\nRegards,\n{app_name} Team"],

            // ─── Onboarding & Billing ───
            ['name' => 'Company Approved', 'module_name' => 'onboarding', 'subject' => 'Congratulations — {company_name} is now active', 'content' => "Dear {user_name},\n\nYour company {company_name} has been approved and activated on the {plan_name} plan ({billing_cycle} billing).\n\nYou can now access your full dashboard.\n\nRegards,\n{app_name} Team"],
            ['name' => 'Onboarding Rejected', 'module_name' => 'onboarding', 'subject' => 'Your onboarding request — {order_id}', 'content' => "Dear {user_name},\n\nYour onboarding request for {company_name} was not approved at this time.\n\nReference: {order_id}\nPlease contact support if you believe this is an error.\n\nRegards,\n{app_name} Team"],
            ['name' => 'Subscription Expiring', 'module_name' => 'billing', 'subject' => '{company_name} — subscription expires in {days_left} days', 'content' => "Dear {user_name},\n\nThe {plan_name} subscription for {company_name} expires on {expiry_date}.\n\nRenew now to avoid interruption of service.\n\nRegards,\n{app_name} Billing"],
            ['name' => 'Payment Receipt', 'module_name' => 'billing', 'subject' => 'Payment received — {order_id}', 'content' => "Dear {user_name},\n\nWe received your payment.\n\nOrder: {order_id}\nPlan: {plan_name}\nAmount: {currency} {amount}\n\nThank you for your business.\n\nRegards,\n{app_name} Billing"],

            // ─── Sales ───
            ['name' => 'Quotation Sent', 'module_name' => 'sales', 'subject' => 'Quotation {quotation_number} from {company_name}', 'content' => "Dear {customer_name},\n\nPlease find quotation {quotation_number} for your review.\n\nTotal: {currency} {total}\nValid until: {valid_until}\n\nReply to this email to accept or discuss.\n\nRegards,\n{company_name}"],
            ['name' => 'Invoice Issued', 'module_name' => 'sales', 'subject' => 'Invoice {invoice_number} from {company_name}', 'content' => "Dear {customer_name},\n\nInvoice {invoice_number} has been issued.\n\nTotal: {currency} {total}\nDue date: {due_date}\n\nPlease arrange payment before the due date.\n\nRegards,\n{company_name}"],
            ['name' => 'Payment Received — Invoice', 'module_name' => 'sales', 'subject' => 'Payment received for invoice {invoice_number}', 'content' => "Dear {customer_name},\n\nWe confirm receipt of payment for invoice {invoice_number}.\n\nAmount: {currency} {amount}\nBalance: {currency} {balance}\n\nThank you.\n\nRegards,\n{company_name}"],
            ['name' => 'Invoice Overdue Reminder', 'module_name' => 'sales', 'subject' => 'Reminder — invoice {invoice_number} is overdue', 'content' => "Dear {customer_name},\n\nInvoice {invoice_number} was due on {due_date} and remains unpaid.\n\nOutstanding: {currency} {balance}\n\nPlease settle at your earliest convenience.\n\nRegards,\n{company_name}"],

            // ─── Approvals ───
            ['name' => 'Approval Requested', 'module_name' => 'approvals', 'subject' => 'Your approval is needed — {request_title}', 'content' => "Dear {approver_name},\n\n{requester_name} has submitted a request that requires your approval.\n\nModule: {module}\nAmount: {currency} {amount}\nLevel: {level} of {total_levels}\n\nPlease log in to review.\n\nRegards,\n{app_name}"],
            ['name' => 'Request Approved', 'module_name' => 'approvals', 'subject' => 'Your request was approved — {request_title}', 'content' => "Dear {requester_name},\n\nYour request \"{request_title}\" has been approved by {approver_name}.\n\nRegards,\n{app_name}"],
            ['name' => 'Request Rejected', 'module_name' => 'approvals', 'subject' => 'Your request was declined — {request_title}', 'content' => "Dear {requester_name},\n\nYour request \"{request_title}\" was declined by {approver_name}.\n\nReason: {reason}\n\nRegards,\n{app_name}"],

            // ─── Procurement & Inventory ───
            ['name' => 'Purchase Order Issued', 'module_name' => 'procurement', 'subject' => 'Purchase order {po_number} from {company_name}', 'content' => "Dear {supplier_name},\n\nPlease find purchase order {po_number} attached.\n\nTotal: {currency} {total}\n\nPlease confirm receipt and delivery schedule.\n\nRegards,\n{company_name}"],
            ['name' => 'Low Stock Alert', 'module_name' => 'inventory', 'subject' => 'Low stock alert — {product_name}', 'content' => "Dear {user_name},\n\nStock for {product_name} at {warehouse} is below reorder level.\n\nCurrent: {quantity} · Reorder level: {reorder_level}\n\nPlease create a purchase order.\n\nRegards,\n{app_name} Inventory"],

            // ─── HR & Payroll ───
            ['name' => 'Payslip Ready', 'module_name' => 'payroll', 'subject' => 'Your payslip for {period}', 'content' => "Dear {employee_name},\n\nYour payslip for {period} is ready.\n\nNet pay: {currency} {net_pay}\n\nLog in to view the full breakdown.\n\nRegards,\n{company_name} HR"],
            ['name' => 'Leave Approved', 'module_name' => 'hr', 'subject' => 'Your leave request was approved', 'content' => "Dear {employee_name},\n\nYour {leave_type} leave from {start_date} to {end_date} has been approved.\n\nRegards,\n{company_name} HR"],
            ['name' => 'Leave Rejected', 'module_name' => 'hr', 'subject' => 'Your leave request was declined', 'content' => "Dear {employee_name},\n\nYour {leave_type} leave request was declined.\n\nReason: {reason}\n\nRegards,\n{company_name} HR"],

            // ─── Support ───
            ['name' => 'Ticket Confirmation', 'module_name' => 'helpdesk', 'subject' => 'We received your ticket — {ticket_id}', 'content' => "Dear {requester_name},\n\nYour support ticket {ticket_id} has been opened.\n\nSubject: {ticket_title}\nPriority: {priority}\n\nOur team will respond shortly.\n\nRegards,\n{company_name} Support"],
            ['name' => 'Ticket Reply', 'module_name' => 'helpdesk', 'subject' => 'New reply on ticket {ticket_id}', 'content' => "Dear {requester_name},\n\nThere is a new reply on your ticket {ticket_id}.\n\n\"{reply_excerpt}\"\n\nLog in to view the full conversation.\n\nRegards,\n{company_name} Support"],
            ['name' => 'Ticket Resolved', 'module_name' => 'helpdesk', 'subject' => 'Ticket {ticket_id} resolved', 'content' => "Dear {requester_name},\n\nYour ticket {ticket_id} has been marked resolved.\n\nIf the issue persists, reply to reopen it.\n\nRegards,\n{company_name} Support"],

            // ─── Projects ───
            ['name' => 'Task Assigned', 'module_name' => 'projects', 'subject' => 'New task assigned — {task_title}', 'content' => "Dear {assignee_name},\n\n{assigned_by} assigned you a task on project {project_name}.\n\nTask: {task_title}\nDue: {due_date}\n\nRegards,\n{app_name} Projects"],
        ];

        foreach ($templates as $t) {
            EmailTemplate::updateOrCreate(
                ['name' => $t['name']],
                $t + ['from' => $from, 'is_active' => true]
            );
        }
    }
}
