<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // ─── Auth & Accounts ───
            ['name' => 'Welcome - New Registration', 'type' => 'registration', 'subject' => 'Welcome to {app_name}', 'content' => "Hi {user_name},\n\nYour account has been created successfully. Log in to set up your company and choose a plan.\n\nEmail: {user_email}"],
            ['name' => 'Password Reset', 'type' => 'auth', 'subject' => 'Reset your password', 'content' => "Hi {user_name},\n\nUse this code to reset your password: {reset_code}\n\nIt expires in 15 minutes. If you didn't request this, ignore this message."],
            ['name' => 'New Member Added', 'type' => 'member', 'subject' => 'You\'ve been added to {company_name}', 'content' => "Hi {user_name},\n\n{invited_by} has added you to {company_name} on {app_name}.\n\nLog in with your email to get started."],
            ['name' => 'Company Approved', 'type' => 'onboarding', 'subject' => 'Your workspace is now active', 'content' => "Congratulations {user_name}!\n\n{company_name} has been approved and activated on the {plan_name} plan. You can now access your dashboard."],
            ['name' => 'Company Rejected', 'type' => 'onboarding', 'subject' => 'Your onboarding request was declined', 'content' => "Hi {user_name},\n\nUnfortunately your request for {company_name} was not approved. Contact support for details.\n\nReference: {order_id}"],

            // ─── Sales ───
            ['name' => 'Sale Completed', 'type' => 'sale', 'subject' => 'Sale {sale_number} completed', 'content' => "Sale {sale_number} was completed by {cashier}.\n\nTotal: {currency} {total}\nPayment: {payment_method}\nChange: {change}"],
            ['name' => 'Invoice Created', 'type' => 'invoice', 'subject' => 'Invoice {invoice_number} issued', 'content' => "An invoice has been issued to {customer_name}.\n\nInvoice: {invoice_number}\nTotal: {currency} {total}\nDue date: {due_date}"],
            ['name' => 'Invoice Paid', 'type' => 'invoice', 'subject' => 'Invoice {invoice_number} paid', 'content' => "Payment received for invoice {invoice_number}.\n\nCustomer: {customer_name}\nAmount: {currency} {amount}\nBalance: {balance}"],
            ['name' => 'Invoice Overdue', 'type' => 'invoice', 'subject' => 'Invoice {invoice_number} is overdue', 'content' => "Invoice {invoice_number} for {customer_name} is now overdue.\n\nDue date was: {due_date}\nOutstanding: {currency} {balance}"],
            ['name' => 'Quotation Sent', 'type' => 'quotation', 'subject' => 'Quotation {quotation_number} sent', 'content' => "Quotation {quotation_number} was sent to {customer_name}.\n\nTotal: {currency} {total}\nValid until: {valid_until}"],
            ['name' => 'Quote Accepted', 'type' => 'quotation', 'subject' => 'Quotation {quotation_number} accepted', 'content' => "Good news — {customer_name} accepted quotation {quotation_number}.\n\nTotal: {currency} {total}"],
            ['name' => 'Quote Rejected', 'type' => 'quotation', 'subject' => 'Quotation {quotation_number} rejected', 'content' => "{customer_name} rejected quotation {quotation_number}.\n\nFollow up to understand the reason."],

            // ─── Approvals ───
            ['name' => 'Approval Requested', 'type' => 'approval', 'subject' => 'Approval needed: {request_title}', 'content' => "Hi {approver_name},\n\n{requester_name} submitted a request for your approval.\n\nType: {module}\nAmount: {currency} {amount}\nLevel: {level}/{total_levels}"],
            ['name' => 'Request Approved', 'type' => 'approval', 'subject' => 'Your request was approved', 'content' => "Hi {requester_name},\n\nYour request \"{request_title}\" was approved by {approver_name}.\n\nProgress: {level}/{total_levels}"],
            ['name' => 'Request Rejected', 'type' => 'approval', 'subject' => 'Your request was rejected', 'content' => "Hi {requester_name},\n\nYour request \"{request_title}\" was rejected by {approver_name}.\n\nReason: {reason}"],

            // ─── Purchases & Inventory ───
            ['name' => 'Purchase Order Created', 'type' => 'purchase', 'subject' => 'Purchase order {po_number} created', 'content' => "Purchase order {po_number} was created for {supplier_name}.\n\nTotal: {currency} {total}\nStatus: {status}"],
            ['name' => 'Low Stock Alert', 'type' => 'inventory', 'subject' => 'Low stock: {product_name}', 'content' => "Stock for {product_name} is running low.\n\nWarehouse: {warehouse}\nCurrent: {quantity} · Reorder level: {reorder_level}"],
            ['name' => 'Stock Transfer Pending', 'type' => 'inventory', 'subject' => 'Transfer {transfer_id} needs approval', 'content' => "A stock transfer needs your approval.\n\nProduct: {product_name}\nFrom: {from_warehouse} → To: {to_warehouse}\nQty: {quantity}"],

            // ─── HR & Payroll ───
            ['name' => 'Payslip Ready', 'type' => 'payroll', 'subject' => 'Your payslip for {period} is ready', 'content' => "Hi {employee_name},\n\nYour payslip for {period} has been processed.\n\nNet pay: {currency} {net_pay}"],
            ['name' => 'Leave Requested', 'type' => 'hr', 'subject' => 'Leave request from {employee_name}', 'content' => "{employee_name} requested leave.\n\nType: {leave_type}\nFrom: {start_date} to {end_date}\nReason: {reason}"],
            ['name' => 'Leave Approved', 'type' => 'hr', 'subject' => 'Your leave was approved', 'content' => "Hi {employee_name},\n\nYour {leave_type} leave from {start_date} to {end_date} was approved by {approver_name}."],
            ['name' => 'Leave Rejected', 'type' => 'hr', 'subject' => 'Your leave was declined', 'content' => "Hi {employee_name},\n\nYour {leave_type} leave request was declined by {approver_name}.\n\nReason: {reason}"],

            // ─── Support ───
            ['name' => 'Ticket Created', 'type' => 'helpdesk', 'subject' => 'Ticket {ticket_id} opened', 'content' => "A new support ticket was opened.\n\nTitle: {ticket_title}\nPriority: {priority}\nBy: {requester_name}"],
            ['name' => 'Ticket Reply', 'type' => 'helpdesk', 'subject' => 'New reply on ticket {ticket_id}', 'content' => "{replier_name} replied to ticket {ticket_id}.\n\n\"{reply_excerpt}\""],
            ['name' => 'Ticket Resolved', 'type' => 'helpdesk', 'subject' => 'Ticket {ticket_id} resolved', 'content' => "Ticket {ticket_id} — \"{ticket_title}\" — was resolved by {resolver_name}."],

            // ─── Projects & Tasks ───
            ['name' => 'Task Assigned', 'type' => 'project', 'subject' => 'You were assigned: {task_title}', 'content' => "Hi {assignee_name},\n\n{assigned_by} assigned you a task.\n\nTask: {task_title}\nProject: {project_name}\nDue: {due_date}"],
            ['name' => 'Milestone Reached', 'type' => 'project', 'subject' => 'Milestone completed: {milestone_name}', 'content' => "Milestone \"{milestone_name}\" on project {project_name} was completed."],

            // ─── SaaS Billing ───
            ['name' => 'Subscription Renewed', 'type' => 'billing', 'subject' => 'Subscription renewed for {company_name}', 'content' => "Subscription for {company_name} was renewed.\n\nPlan: {plan_name}\nAmount: {currency} {amount}\nNext renewal: {next_date}"],
            ['name' => 'Subscription Expiring', 'type' => 'billing', 'subject' => 'Your subscription expires in {days_left} days', 'content' => "Hi {user_name},\n\nThe {plan_name} subscription for {company_name} expires on {expiry_date}. Renew to keep access."],
            ['name' => 'Payment Received', 'type' => 'billing', 'subject' => 'Payment received — {order_id}', 'content' => "Payment of {currency} {amount} was received for order {order_id}.\n\nCompany: {company_name}\nPlan: {plan_name}"],
        ];

        foreach ($templates as $t) {
            NotificationTemplate::updateOrCreate(
                ['name' => $t['name']],
                $t + ['is_active' => true]
            );
        }
    }
}
