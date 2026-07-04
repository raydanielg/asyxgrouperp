<?php

namespace Database\Seeders;

use App\Models\DocumentationPage;
use Illuminate\Database\Seeder;

class DocumentationSeeder extends Seeder
{
    public function run(): void
    {
        $docs = [
            // Getting Started
            [
                'slug' => 'introduction',
                'title' => 'Introduction',
                'category' => 'getting_started',
                'sort_order' => 1,
                'is_published' => true,
                'role_scope' => 'all',
                'content' => <<<'MD'
# Welcome to the ERP System

This is the central documentation for the ASYX Group ERP platform. It covers every module, role, and workflow so that every user — from super administrators to front-desk officers — can use the system confidently.

## What is covered

- **Admin guides** — company management, users, roles, permissions, reports, and system control.
- **Role guides** — task-specific instructions for accountants, HR, sales, procurement, reception, and more.
- **Module guides** — detailed walkthroughs for HR, CRM, accounting, inventory, projects, fleet, and all ERP modules.
- **API & Integrations** — machine-readable endpoints and LLM-friendly exports.
- **Security & Maintenance** — backups, maintenance mode, and audit logs.

## How to use this documentation

- Use the **search box** on the left to find any topic quickly.
- Browse by **category** for role-based guides.
- Click **Copy Markdown** or **Export .md** to share documentation offline.
- Use the **Power** button in the app to reopen AI insights at any time.

## Support

If you cannot find what you need, contact your system administrator or check the **Audit Logs** module for recent changes.
MD
            ],

            // Admin Guides
            [
                'slug' => 'admin-dashboard',
                'title' => 'Admin Dashboard',
                'category' => 'admin_guide',
                'sort_order' => 1,
                'is_published' => true,
                'role_scope' => 'admin,erp_super_administrator',
                'content' => <<<'MD'
# Admin Dashboard

The Admin Dashboard gives a high-level overview of the entire ERP system.

## Cards and KPIs

- **Total Revenue / Expenses** — consolidated financial position across companies.
- **Active Users** — currently logged-in users.
- **Pending Approvals** — items awaiting action.
- **Recent Activity** — latest transactions, logins, and system events.

## Quick Actions

- Switch company context using the top-left company selector.
- Open AI Power Insights using the **Power** button in the header.
- Access System Control to enable maintenance mode or download a database backup.

## Navigation

The sidebar is permission-driven. Only modules you have access to will appear.
MD
            ],

            [
                'slug' => 'companies-management',
                'title' => 'Companies Management',
                'category' => 'admin_guide',
                'sort_order' => 2,
                'is_published' => true,
                'role_scope' => 'admin,erp_super_administrator',
                'content' => <<<'MD'
# Companies Management

## Overview

Create and manage group companies, subsidiaries, and branches. Each company can have its own users, chart of accounts, and operational data.

## Creating a Company

1. Go to **Companies → Add Company**.
2. Fill in name, short code, tax details, and contact information.
3. Mark as group company if it is the holding entity.
4. Save and assign users.

## Consolidated View

Use **Consolidated Companies** to view combined reports across all entities.

## Switching Context

Use the company switcher in the top header to work within a specific company or group view.
MD
            ],

            [
                'slug' => 'users-and-roles',
                'title' => 'Users & Roles',
                'category' => 'admin_guide',
                'sort_order' => 3,
                'is_published' => true,
                'role_scope' => 'admin,erp_super_administrator',
                'content' => <<<'MD'
# Users & Roles

## Roles

Roles define what a user can see and do. The system includes built-in roles such as:

- `erp_super_administrator` — full system control.
- `director`, `manager`, `accountant`, `cashier`, `hr_manager`, etc.
- Self-service roles for employees.

## Permissions

Each role is linked to permissions like `view-users`, `view-companies`, `view-payroll`, etc. The sidebar is filtered automatically based on these permissions.

## Creating Users

1. Go to **Users → Add User**.
2. Assign name, email, phone, and role.
3. Set `is_enable_login` to allow access.
4. Save. The user will receive login instructions.

## Login History

Track active sessions and recent logins from the **Login History** page.
MD
            ],

            [
                'slug' => 'system-control',
                'title' => 'System Control & Backups',
                'category' => 'admin_guide',
                'sort_order' => 4,
                'is_published' => true,
                'role_scope' => 'admin,erp_super_administrator',
                'content' => <<<'MD'
# System Control & Backups

## Maintenance Mode

Super administrators can enable maintenance mode from the dashboard.

1. Click **Enable Maintenance Mode**.
2. All non-admin users will be logged out.
3. Only super administrators can access the system.
4. Click **Bring System Online** to restore access.

## Database Backup

1. Click **Download Database Backup**.
2. A SQL dump of the entire database is generated.
3. The file downloads automatically.

> Ensure the server has `mysqldump` available for backups to work.

## Audit Logs

All sensitive actions are logged. Use **Audit Logs** to review changes.
MD
            ],

            // Role Guides
            [
                'slug' => 'erp-super-administrator-guide',
                'title' => 'ERP Super Administrator Guide',
                'category' => 'role_guide',
                'sort_order' => 1,
                'is_published' => true,
                'role_scope' => 'erp_super_administrator',
                'content' => <<<'MD'
# ERP Super Administrator Guide

## Your Responsibilities

As an ERP Super Administrator, you have full visibility and control over:

- All companies and subsidiaries.
- All users, roles, and permissions.
- System-wide reports and settings.
- Maintenance mode and database backups.

## Daily Tasks

- Review the dashboard KPIs and AI insights.
- Check pending approvals and user login activity.
- Manage companies and role assignments.
- Download regular database backups.

## Important Notes

- Your sidebar is dynamic — it shows all modules because you have all permissions.
- Use the company switcher to drill into a specific company.
- Never disable your own account or remove your admin role.
MD
            ],

            [
                'slug' => 'accountant-guide',
                'title' => 'Accountant Guide',
                'category' => 'role_guide',
                'sort_order' => 2,
                'is_published' => true,
                'role_scope' => 'accountant',
                'content' => <<<'MD'
# Accountant Guide

## Modules You Use

- Journal Entries
- Invoices
- Expenses
- Cost Centres
- Bank Accounts
- Transfers

## Common Workflows

1. Record expenses and revenues daily.
2. Reconcile bank accounts weekly.
3. Generate and review financial reports.
4. Process vendor payments and client receipts.

## Reports

Use the **Reports** module to view profit & loss, balance sheet, and trial balance.
MD
            ],

            [
                'slug' => 'hr-manager-guide',
                'title' => 'HR Manager Guide',
                'category' => 'role_guide',
                'sort_order' => 3,
                'is_published' => true,
                'role_scope' => 'hr_manager',
                'content' => <<<'MD'
# HR Manager Guide

## Modules You Use

- Employee Records
- Recruitment
- Leave Approvals
- Payroll Approval
- Disciplinary
- Appraisals

## Common Workflows

1. Maintain employee records and contracts.
2. Post job openings and manage applications.
3. Approve or reject leave requests.
4. Review payroll before it is processed.
5. Conduct performance appraisals.

## Approvals

Use the **Approvals** module to track pending HR actions.
MD
            ],

            [
                'slug' => 'sales-manager-guide',
                'title' => 'Sales Manager Guide',
                'category' => 'role_guide',
                'sort_order' => 4,
                'is_published' => true,
                'role_scope' => 'sales_manager',
                'content' => <<<'MD'
# Sales Manager Guide

## Modules You Use

- Deal Pipeline
- Sales Forecast
- Quotations
- Team Performance

## Common Workflows

1. Review the deal pipeline and forecast.
2. Create and send quotations to prospects.
3. Monitor team performance and sales targets.
4. Convert won deals into sales invoices.

## CRM Integration

Use the CRM modules to track leads, deals, and customer communications.
MD
            ],

            [
                'slug' => 'receptionist-guide',
                'title' => 'Receptionist Guide',
                'category' => 'role_guide',
                'sort_order' => 5,
                'is_published' => true,
                'role_scope' => 'receptionist',
                'content' => <<<'MD'
# Receptionist Guide

## Modules You Use

- Visitors
- Appointments
- Calls
- Correspondence
- Parcels

## Common Workflows

1. Register all incoming and outgoing visitors.
2. Schedule and manage appointments.
3. Log phone calls and messages.
4. Track incoming and outgoing parcels.

## Front Desk

The front desk module is your central workspace. Use it to check today's schedule and visitor status.
MD
            ],

            // Module Guides
            [
                'slug' => 'hr-module',
                'title' => 'HR Module',
                'category' => 'module_guide',
                'sort_order' => 1,
                'is_published' => true,
                'role_scope' => 'all',
                'content' => <<<'MD'
# HR Module

## Sections

- **Employees** — records, contracts, documents.
- **Attendance** — daily attendance, shifts, overtime.
- **Payroll** — salaries, deductions, bonuses, payslips.
- **Leaves** — applications, approvals, balances.
- **Performance** — appraisals and goals.
- **Training** — courses, records, certifications.
- **Recruitment** — job postings and applications.

## Quick Tips

- HR managers approve requests; employees apply through self-service.
- Payroll should be locked after processing to prevent changes.
- Use reports to monitor attendance and leave trends.
MD
            ],

            [
                'slug' => 'accounting-module',
                'title' => 'Accounting Module',
                'category' => 'module_guide',
                'sort_order' => 2,
                'is_published' => true,
                'role_scope' => 'all',
                'content' => <<<'MD'
# Accounting Module

## Sections

- **Bank Accounts** — manage company accounts.
- **Transfers** — internal transfers.
- **Expenses** — record and categorize expenses.
- **Revenue** — record income.
- **Bills** — vendor bills and payments.
- **Estimates** — quotes and proposals.

## Reports

Generate profit & loss, trial balance, cash flow, and bank reconciliation reports.

## Approval Flow

Some expenses require approval before payment. Check your role permissions.
MD
            ],

            [
                'slug' => 'inventory-module',
                'title' => 'Inventory Module',
                'category' => 'module_guide',
                'sort_order' => 3,
                'is_published' => true,
                'role_scope' => 'all',
                'content' => <<<'MD'
# Inventory Module

## Sections

- **Products** — product catalogue and pricing.
- **Categories** — organize products.
- **Suppliers** — vendor records.
- **Stock Movements** — goods in/out.
- **Warehouses** — multiple locations.
- **Transfers** — stock transfers between warehouses.

## Stock Management

- Use stock movements to record receipts and issues.
- Set reorder levels to avoid stockouts.
- Review inventory reports for valuation and movement history.
MD
            ],

            [
                'slug' => 'projects-module',
                'title' => 'Projects Module',
                'category' => 'module_guide',
                'sort_order' => 4,
                'is_published' => true,
                'role_scope' => 'all',
                'content' => <<<'MD'
# Projects Module

## Sections

- **Projects** — create and manage projects.
- **Timesheets** — log time against projects.
- **Bugs** — issue tracking.
- **Meetings** — project meetings and action points.
- **Settlements** — project budgets and settlements.

## Project Lifecycle

1. Create a project with budget and timeline.
2. Assign team members and tasks.
3. Log timesheets and expenses against the project.
4. Track progress via meetings and reports.
5. Generate invoices from project milestones.
MD
            ],

            [
                'slug' => 'crm-module',
                'title' => 'CRM Module',
                'category' => 'module_guide',
                'sort_order' => 5,
                'is_published' => true,
                'role_scope' => 'all',
                'content' => <<<'MD'
# CRM Module

## Sections

- **Leads** — potential customers.
- **Deals** — sales opportunities.
- **Contracts** — customer agreements.
- **Customers** — client database.
- **Contacts** — people associated with customers.

## Sales Pipeline

Move leads through stages: New → Contacted → Qualified → Proposal → Negotiation → Won/Lost.

## Communication

Log all calls, emails, and meetings against leads or deals for full history.
MD
            ],

            // API & Security
            [
                'slug' => 'api-reference',
                'title' => 'API Reference',
                'category' => 'api_reference',
                'sort_order' => 1,
                'is_published' => true,
                'role_scope' => 'all',
                'content' => <<<'MD'
# API Reference

## Public Endpoints

- `GET /api/docs` — list all documentation pages as JSON.
- `GET /api/docs/{slug}` — single documentation page as JSON.
- `GET /docs/export.md` — full documentation as Markdown.
- `GET /llms.txt` — LLM discovery index.
- `GET /llms-full.txt` — full content for AI ingestion.

## Machine Readable

All documentation is available in JSON and Markdown. The API is CORS-enabled for integration with AI tools and external systems.

## Usage

```bash
curl https://your-domain.com/api/docs
```
MD
            ],

            [
                'slug' => 'security-best-practices',
                'title' => 'Security Best Practices',
                'category' => 'security',
                'sort_order' => 1,
                'is_published' => true,
                'role_scope' => 'all',
                'content' => <<<'MD'
# Security Best Practices

## User Accounts

- Use strong passwords and change them regularly.
- Do not share login credentials.
- Report suspicious activity immediately.

## Permissions

- Only assign permissions required for a user's role.
- Review role permissions quarterly.
- Disable login for inactive users.

## System Maintenance

- Download database backups regularly.
- Enable maintenance mode before major updates.
- Review audit logs for unusual actions.

## Data Protection

- Sensitive data should not be downloaded to personal devices.
- Follow company data retention policies.
MD
            ],

            [
                'slug' => 'faq',
                'title' => 'Frequently Asked Questions',
                'category' => 'faq',
                'sort_order' => 1,
                'is_published' => true,
                'role_scope' => 'all',
                'content' => <<<'MD'
# Frequently Asked Questions

## Why can't I see a module?

The sidebar is filtered by your role permissions. Contact your administrator if you need access.

## How do I change my password?

Go to **My Profile** and use the password update form.

## What happens when maintenance mode is enabled?

Only super administrators can log in. All other users are logged out and see a maintenance page.

## How do I get help?

- Check this documentation first.
- Contact your system administrator.
- Review the **Audit Logs** for recent changes.

## What is the Power button?

The Power button opens AI insights. It appears automatically on first login each day and can be reopened anytime.
MD
            ],
        ];

        foreach ($docs as $doc) {
            DocumentationPage::updateOrCreate(
                ['slug' => $doc['slug']],
                $doc
            );
        }
    }
}
