# ASYX GROUP ERP SYSTEM — COMPREHENSIVE TECHNICAL DOCUMENTATION

> **Project Codename:** DjanProject  
> **Product Name:** ASYX Group ERP  
> **Last Updated:** June 30, 2026  
> **Framework:** Laravel 13.x | PHP 8.3 | SQLite (MySQL-ready)

---

## TABLE OF CONTENTS

1. [System Overview](#1-system-overview)
2. [Tech Stack & Dependencies](#2-tech-stack--dependencies)
3. [Directory Structure](#3-directory-structure)
4. [Environment Configuration](#4-environment-configuration)
5. [Installation & Setup](#5-installation--setup)
6. [Authentication Flow](#6-authentication-flow)
7. [Multi-Company Architecture](#7-multi-company-architecture)
8. [Role-Based Access Control (RBAC)](#8-role-based-access-control-rbac)
9. [Sidebar & Menu System](#9-sidebar--menu-system)
10. [Admin Module](#10-admin-module)
11. [Reception Module](#11-reception-module)
12. [Finance Officer Module](#12-finance-officer-module)
13. [HRM Module](#13-hrm-module)
14. [CRM Module](#14-crm-module)
15. [Business Flow Module](#15-business-flow-module)
16. [Sales Module](#16-sales-module)
17. [Purchase Module](#17-purchase-module)
18. [Accounting Module](#18-accounting-module)
19. [Inventory & Products Module](#19-inventory--products-module)
20. [Projects Module](#20-projects-module)
21. [POS Module](#21-pos-module)
22. [Helpdesk Module](#22-helpdesk-module)
23. [Fleet Management Module](#23-fleet-management-module)
24. [Fixed Assets Module](#24-fixed-assets-module)
25. [Document Management Module](#25-document-management-module)
26. [Call Center Module](#26-call-center-module)
27. [Approval Workflow Engine](#27-approval-workflow-engine)
28. [Audit Logs Module](#28-audit-logs-module)
29. [Salary Advance Module](#29-salary-advance-module)
30. [Profile Management](#30-profile-management)
31. [Payroll Module](#31-payroll-module)
32. [Database Tables Reference](#32-database-tables-reference)
33. [Controllers Reference](#33-controllers-reference)
34. [Routes Reference](#34-routes-reference)
35. [View Structure](#35-view-structure)
36. [Role Dashboards & Role Pages](#36-role-dashboards--role-pages)
37. [PDF Generation](#37-pdf-generation)
38. [Public Pages & Careers](#38-public-pages--careers)
39. [Common Artisan Commands](#39-common-artisan-commands)
40. [Extending the System](#40-extending-the-system)
---

## 1. System Overview

**Jina la Mfumo:** Djanproject — ASYX Group ERP System  
**Aina:** Enterprise Resource Planning (ERP) + Customer Relationship Management (CRM)  
**Lengo:** Kutoa platform moja ya kusimamia shughuli zote za kampuni ikiwemo HR, Finance, Sales, Inventory, Projects, CRM, Fleet, Assets, Documents na Reception.

Mfumo huu ni **full-stack web application** yenye **Laravel backend**, **Livewire/Tailwind frontend**, na **Flutter mobile app**. Una uwezo wa **multi-company**, **role-based access control**, **approval workflows**, **audit logging**, na **real-time notifications**.

### Key Features:
- Multi-Company architecture (Group + Child companies)
- 18+ Role-specific dashboards
- 52 database migrations covering all modules
- 105+ Eloquent models
- Full REST API (Sanctum authentication)
- Flutter mobile app (Android, iOS, Windows, macOS, Linux, Web)
- Nextra documentation site
- DomPDF for PDF generation (invoices, payslips, reports)
- Real-time messaging via Chatify

---

## 2. Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend Framework** | Laravel 11.x |
| **Frontend** | Laravel Blade + Tailwind CSS + Livewire |
| **Mobile App** | Flutter (Dart) |
| **Database** | MySQL (via Laravel Eloquent) |
| **Authentication** | Laravel Sanctum (API) + Laravel Fortify (Web) |
| **PDF Generation** | barryvdh/laravel-dompdf |
| **Messaging** | Chatify (real-time chat) |
| **Documentation** | Nextra (Next.js-based MDX docs) |
| **Assets** | Vite |
| **Server** | Apache/Nginx (via .htaccess) |
| **OS** | Cross-platform (Windows/Linux/macOS support) |

---

## 3. Architecture

```
┌─────────────────────────────────────────────────────┐
│                    PUBLIC LANDING                    │
│  /  /about  /services  /sectors  /why-asyx  /contact│
│  /hosting  /careers                                  │
└──────────────────┬──────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────┐
│              AUTHENTICATION (Fortify)                │
│  Login / Password Reset / 2FA / Email Verification  │
└──────────────────┬──────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────┐
│              ROLE-BASED DASHBOARD (/dashboard)       │
│  18+ roles each with custom KPIs, charts, actions   │
└──────────────────┬──────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────┐
│              ADMIN PANEL (/admin/*)                  │
│  ┌─────────┐ ┌──────────┐ ┌────────┐ ┌──────────┐  │
│  │   ERP   │ │Extended  │ │Business│ │ Advanced │  │
│  │ Modules │ │ ERP (HRM,│ │ Flow   │ │ Modules  │  │
│  │(Invoices│ │ CRM, POS,│ │(Tenders│ │(Fleet,   │  │
│  │,Plans,  │ │ Projects,│ │, LPOs, │ │ Assets,  │  │
│  │Orders,  │ │ Products,│ │ GRNs,  │ │ Docs,    │  │
│  │Coupons) │ │ Acc, etc)│ │ Budget)│ │Call Ctr) │  │
│  └─────────┘ └──────────┘ └────────┘ └──────────┘  │
└──────────────────┬──────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────┐
│              RECEPTION MODULE (/reception/*)         │
│  Visitors | Appointments | Calls | Correspondence    │
│  Parcels | Front Desk | Departments | Announcements  │
│  Messages | Reports | My Account                     │
└──────────────────┬──────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────┐
│              REST API (/api/*)                       │
│  Sanctum auth | All CRUD | Reports | Dashboard KPIs │
└──────────────────┬──────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────┐
│              MOBILE APP (Flutter)                    │
│  Android | iOS | Windows | macOS | Linux | Web      │
└─────────────────────────────────────────────────────┘
```

### Directory Structure Overview:

```
C:\Users\Administrator\Desktop\Djanproject\
├── app/
│   ├── Actions/          # Fortify auth actions
│   ├── Helpers/          # currency.php helpers
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/        # 18+ admin controllers
│   │   │   ├── Api/          # 16 API controllers
│   │   │   ├── Auth/         # 6 auth controllers
│   │   │   └── Reception/    # 10 reception controllers
│   │   └── Middleware/       # 4 middleware classes
│   ├── Models/           # 105+ Eloquent models
│   ├── Providers/        # App & Fortify service providers
│   └── Traits/           # BelongsToCompany trait
├── bootstrap/
├── config/               # 15 config files
├── database/
│   ├── migrations/       # 52 migration files
│   └── seeders/          # 7 seeders
├── docs/                 # Nextra documentation site
├── mobile/               # Flutter multi-platform app
├── resources/views/      # 200+ Blade view files
├── routes/
│   ├── web.php           # ~600 lines of web routes
│   └── api.php           # ~175 lines of API routes
├── public/               # Web root
├── scripts/              # auto_push.ps1
├── storage/
├── tests/
└── vendor/
```

---

## 4. Multi-Company Architecture

### Core Concept
Mfumo unaweza kusimamia **multiple companies** chini ya **group company** moja. Kila kampuni ina data yake pekee (data isolation) kupitia `BelongsToCompany` trait.

### Company Model (`App\Models\Company`)
- **parent_id**: Self-referential hierarchy (Group -> Child companies)
- **is_group**: Identifies the group/parent company
- **short_code**: Unique code for each company
- Data fields: name, legal_name, tax_id, address, currency, timezone, etc.

### BelongsToCompany Trait (`App\Traits\BelongsToCompany`)
Hii ni **core trait** inayotumiwa na models nyingi (Employee, Attendance, Product, Project, Quotation, LPO, CRM, Inventory, n.k.).

**Global Scope Behavior:**
```php
// 1. If user is in GROUP company and has switched:
//    -> Filter by session('switched_company_id')
// 2. If user is in GROUP company and NO switch:
//    -> NO filter (sees all companies)
// 3. If user is in CHILD company:
//    -> Filter by user's company_id
// 4. Auto-sets company_id on record creation
```

**Scope Methods:**
- `scopeForCompany($companyId)` — Bypass global scope, filter by specific ID
- `scopeAllCompanies()` — Bypass global scope entirely

### Company Switching
- `/admin/companies/switch` — Admin can switch between companies
- `SetCompanyContext` middleware sets `session('current_company_id')` on every request
- `Company::current()` — Get the currently active company

### Intercompany Transactions
- `IntercompanyTransaction` model with `from_company_id` and `to_company_id`
- Supports elimination entries for consolidated reporting
- `/admin/intercompany/*` routes for management

### Consolidated Reporting
- `/admin/companies-consolidated` — Shows combined data across all companies
- Used by group-level management

### Seed Data (CompanySeeder)
```
ASYX Group (parent, is_group=true)
├── ASYX (child)
├── Parktech (child)
├── Motisha (child)
├── DITTA (child)
└── AJIRA (child)
```

---

## 5. Authentication & Authorization

### Authentication — Laravel Fortify
- **Login**: Email + password with rate limiting (5 attempts/min)
- **Registration**: Disabled by default (admin creates users)
- **Password Reset**: Custom code-based flow (sends 6-digit code via email)
- **2FA**: Two-factor columns added to users table
- **Email Verification**: Optional via Fortify

### Authorization — Dual System

#### 1. Role-based (RoleMiddleware)
```
User -> role_user pivot -> Role -> permissions (many-to-many)
User also has a direct 'role' column (string fallback)
```

**RoleMiddleware** checks:
- If user `isAdmin()` → allow all
- If user has any of the required roles via `$user->role === $role || $user->hasRole($role)` → allow
- Otherwise → 403

#### 2. Permission-based (Sidebar filtering)
Each sidebar menu item has a `permMap` that maps route patterns to permission names.
```php
'admin.employees*' => 'view-employees',
'admin.payroll*' => 'view-payroll',
// ... 60+ permission mappings
```
Non-admin users only see sidebar items they have permissions for.

### User Levels
- **Admin/Administrator**: Full access to everything
- **Role-based users**: Access limited to their role's permissions
- **18 roles** supported: director, admin_manager, finance_officer, auditor, hr_officer, legal_officer, ict_officer, ict_engineer, receptionist, cashier, call_center_agent, logistics_officer, operations_manager, project_manager, technical_manager, supervisor, technician, admin

### User Impersonation
- Admin can impersonate any user via `/admin/users/{user}/impersonate`
- Stop impersonating via `/admin/users/stop-impersonating`

### API Authentication
- **Laravel Sanctum** token-based
- `POST /api/login` returns Sanctum token
- All API routes protected via `auth:sanctum` middleware

---

## 6. Database Structure

### 52 Migration Files (key tables per module)

```
USERS & AUTH (7 migrations)
├── 0001_01_01_000000_create_users_table
├── 0001_01_01_000001_create_cache_table
├── 0001_01_01_000002_create_jobs_table
├── 2026_06_27_170715_create_personal_access_tokens_table
├── 2026_06_27_170746_create_permission_tables (Spatie-like)
├── 2026_06_27_170820_add_two_factor_columns_to_users_table
├── 2026_06_27_200000_add_user_profile_columns
├── 2026_06_28_000001_add_code_to_password_reset_tokens

NOTIFICATIONS & TEMPLATES
├── 2024_12_01_000001_create_notification_templates_table

ROLES & PERMISSIONS
├── 2024_12_03_000001_create_roles_permissions_tables
├── 2024_12_03_000002_add_user_extra_columns
├── 2024_12_03_000003_add_employee_extra_columns

ERP EXTENDED MODULES
├── 2024_12_02_000001_create_erp_extended_modules_tables
├── 2024_12_03_000004_create_business_flow_tables

CORE ERP (July 2025)
├── Warehouses, Settings, Plans, Helpdesk, Media
├── Add-ons, Email Templates, Notifications
├── Orders, Coupons, Login History, Bank Transfers
├── Transfers, Messenger
├── Purchase Invoice, Sales Invoice, Sales Proposal

COMPANIES & INTERCOMPANY
├── 2026_06_28_100000_create_companies_table
├── 2026_06_28_100001_create_intercompany_transactions_table
├── 2026_06_28_100002_add_company_id_to_business_tables

ADVANCED MODULES (June 2026)
├── Approval Workflow, Fleet Management
├── Fixed Assets, Document Management
├── Call Center, Audit Logs
├── Attendance clock fields
├── Project links to projects/sales invoices
├── Employee deductions & salary advances
├── Job Applications
├── Add company_id to job postings

RECEPTION MODULE (June 2026)
├── Visitors, Appointments, Calls, Correspondence
├── Parcels, Front Desks, Departments
├── Announcements, Messages
├── Reception permissions
```

### Key Models (105+)

| Model | Table | Module |
|-------|-------|--------|
| User | users | Core |
| Company | companies | Multi-Company |
| Role | roles | Auth |
| Permission | permissions | Auth |
| Employee | employees | HRM |
| Attendance | attendances | HRM |
| Payroll | payrolls | HRM |
| Leave | leaves | HRM |
| Product | products | Inventory |
| Warehouse | warehouses | Inventory |
| Supplier | suppliers | Inventory |
| StockMovement | stock_movements | Inventory |
| Project | projects | Projects |
| ProjectTask | project_tasks | Projects |
| ProjectBug | project_bugs | Projects |
| Timesheet | timesheets | Projects |
| SalesInvoice | sales_invoices | Sales |
| PurchaseInvoice | purchase_invoices | Purchase |
| SalesProposal | sales_proposals | Sales |
| CrmLead | crm_leads | CRM |
| CrmDeal | crm_deals | CRM |
| CrmContact | crm_contacts | CRM |
| CrmContract | crm_contracts | CRM |
| Quotation | quotations | Business Flow |
| Lpo | lpos | Business Flow |
| Grn | grns | Business Flow |
| Tender | tenders | Business Flow |
| Visitor | visitors | Reception |
| Appointment | appointments | Reception |
| Call | calls | Reception |
| Correspondence | correspondences | Reception |
| Parcel | parcels | Reception |
| FrontDesk | front_desks | Reception |
| Department | departments | Reception |
| Announcement | announcements | Reception |
| Message | messages | Reception |
| FixedAsset | fixed_assets | Assets |
| Vehicle | vehicles | Fleet |
| Document | documents | Documents |
| HelpdeskTicket | helpdesk_tickets | Helpdesk |
| ApprovalWorkflow | approval_workflows | Approvals |
| IntercompanyTransaction | intercompany_transactions | Intercompany |
| AuditLog | audit_logs | System |
| Setting | settings | System |
| And 65+ more... | | |

---

## 7. Module-by-Module Breakdown

### Controller Map

```
Controllers/
├── Controller.php (Base)
├── HomeController.php (Post-login landing)
├── ProfileController.php (User profile)
├── RoleDashboardController.php (Role-specific dashboards)
├── RolePageController.php (Role-specific module pages)
│
├── Admin/
│   ├── DashboardController.php (Admin metrics, users, reports)
│   ├── ErpController.php (30+ methods: warehouses, transfers, plans, orders,
│   │   coupons, helpdesk, purchase/sales invoices, returns, proposals,
│   │   email templates, settings, login history, bank transfers, add-ons,
│   │   messenger, media, notification templates, user management)
│   ├── ErpExtendedController.php (40+ methods: HRM employees, leaves,
│   │   performance, training, job postings, applications, assets, events,
│   │   policies; CRM leads, deals, contracts, contacts; Accounting bank
│   │   accounts, transfers, expenses, revenues, bills, estimates; Projects,
│   │   timesheets, bugs; Products, categories, suppliers, stock movements;
│   │   POS terminal, reports; Careers public pages)
│   ├── BusinessFlowController.php (20 methods: dashboard, tenders,
│   │   quotations, lead→deal→project conversion chain, budgets, LPOs,
│   │   GRNs, delivery notes, vendor invoices, payments, office expenses,
│   │   client receipts, project profit)
│   ├── CompanyController.php (Multi-company CRUD, switch, consolidated)
│   ├── UserController.php (Enhanced user CRUD, password, login history,
│   │   impersonation)
│   ├── RoleController.php (Roles & permissions CRUD)
│   ├── AttendanceController.php (Clock in/out, attendance records)
│   ├── PayrollController.php (Payroll generation, PDF payslips)
│   ├── FleetController.php (Vehicle CRUD, maintenance, fuel logs)
│   ├── FixedAssetController.php (Asset CRUD, depreciation, disposal)
│   ├── DocumentController.php (Doc CRUD, download, sign, decline)
│   ├── CallCenterController.php (Campaigns, call logs)
│   ├── AuditLogController.php (Audit log viewer, filter)
│   ├── ApprovalWorkflowController.php (Workflow CRUD, approve/reject)
│   └── IntercompanyTransactionController.php (Transaction CRUD, elimination)
│
├── Api/
│   ├── AuthController.php (Login/register/logout)
│   ├── DashboardController.php (Role dashboard, KPIs)
│   ├── CompanyController.php (CRUD + consolidated)
│   ├── EmployeeController.php (CRUD + sub-resources)
│   ├── AttendanceController.php (Clock in/out, today)
│   ├── LeaveController.php (CRUD + approve/reject)
│   ├── PayrollController.php (Generate, approve)
│   ├── PosController.php (Products, sell, sales)
│   ├── FleetController.php (CRUD + maintenance/fuel)
│   ├── ExpenseRevenueController.php (Expenses, revenues, banks, summary)
│   ├── CrmController.php (Leads, deals, contacts, contracts + convert)
│   ├── BusinessFlowController.php (Tenders, Quotations, LPOs, GRNs, etc.)
│   ├── ProjectController.php (CRUD + tasks, budget, profitability)
│   ├── ProductController.php (CRUD + low stock, movements)
│   ├── InvoiceController.php (Sales/purchase invoices)
│   ├── CustomerController.php (Legacy customers/leads/deals)
│   ├── TicketController.php (Helpdesk CRUD + reply)
│   └── ReportController.php (Financial, sales, project, employee, inventory)
│
├── Auth/
│   ├── LoginController.php, RegisterController.php
│   ├── PasswordResetController.php (Custom code-based)
│   ├── ForgotPasswordController.php
│   ├── ResetPasswordController.php
│   ├── ConfirmPasswordController.php
│   └── VerificationController.php
│
└── Reception/
    ├── VisitorController.php (Check-in/out, CRUD)
    ├── AppointmentController.php (Schedule, complete, cancel)
    ├── CallController.php (Log calls, mark status)
    ├── CorrespondenceController.php (Track mail, mark status)
    ├── ParcelController.php (Track parcels, mark delivered)
    ├── FrontDeskController.php (Check-in/out for all person types)
    ├── DepartmentController.php (Department CRUD)
    ├── AnnouncementController.php (CRUD, toggle status)
    ├── MessageController.php (Internal messaging)
    ├── ReportController.php (Reception reports)
    └── MyAccountController.php (User settings)
```

---

## 8. Reception Module

### Overview
Reception module ni **standalone subsystem** yenye routes zake (/reception/*) na controllers zake (App\Http\Controllers\Reception). Inarun kama AJAX SPA — all responses ni JSON.

### Sub-Modules

#### 8.1 Visitors (`VisitorController`)
**Model:** `App\Models\Visitor`
- Fields: name, phone, email, company, purpose, host, department, badge_number, check_in_at, check_out_at, status, notes, created_by
- **Check-in:** POST /reception/visitors (auto-sets status=checked_in, check_in_at=now)
- **Check-out:** POST /reception/visitors/{visitor}/checkout (sets status=checked_out, check_out_at=now)
- **Dashboard stats:** todayCount, weekCount, pendingCount, totalCount
- Search by: name, phone, company, badge_number
- Filter by: status

#### 8.2 Appointments (`AppointmentController`)
**Model:** `App\Models\Appointment`
- Fields: visitor_name, phone, email, company, purpose, host, appointment_date, status, notes, created_by
- Statuses: scheduled, completed, cancelled
- Actions: store, update, complete, cancel

#### 8.3 Calls (`CallController`)
**Model:** `App\Models\Call`
- Fields: caller_name, phone, email, call_type (inbound/outbound), subject, status, duration, call_time, notes, created_by
- Mark status: POST /reception/calls/{call}/status

#### 8.4 Correspondence (`CorrespondenceController`)
**Model:** `App\Models\Correspondence`
- Fields: reference_number, type (incoming/outgoing), sender_name, recipient_name, subject, status, notes, created_by
- Tracking mail and documents in/out of the office

#### 8.5 Parcels (`ParcelController`)
**Model:** `App\Models\Parcel`
- Fields: tracking_number, sender_name, recipient_name, courier, status, notes, created_by
- Mark delivered: POST /reception/parcels/{parcel}/deliver

#### 8.6 Front Desk (`FrontDeskController`)
**Model:** `App\Models\FrontDesk`
- Fields: name, person_type, purpose, host, department, status, check_in_at, check_out_at, notes, created_by
- General front desk check-in/out for any person type

#### 8.7 Departments (`DepartmentController`)
**Model:** `App\Models\Department`
- Fields: name, code, head_name, phone, email, description, status
- CRUD for company departments

#### 8.8 Announcements (`AnnouncementController`)
**Model:** `App\Models\Announcement`
- Fields: title, body, audience, priority, start_date, end_date, status, created_by
- Toggle active/inactive: POST /reception/announcements/{announcement}/toggle

#### 8.9 Messages (`MessageController`)
**Model:** `App\Models\Message`
- Fields: subject, body, sender_id, recipient_id, status, priority, sent_at
- Internal messaging between users
- Mark status (read/unread): POST /reception/messages/{message}/status

#### 8.10 Reports (`ReportController`)
- Reception reports view

#### 8.11 My Account (`MyAccountController`)
- User profile update
- Password change

---

## 9. HRM Module

### Controllers
- `ErpExtendedController` (employees, leaves, performance, training, recruitment, assets, events, policies)
- `AttendanceController` (dedicated clock in/out)
- `PayrollController` (dedicated payroll management)

### Employees
**Model:** `Employee` (uses BelongsToCompany)
- Full CRUD with create/edit/show views
- Linked to User model
- Status: active/inactive

### Attendance
**Model:** `Attendance` (uses BelongsToCompany)
- **Clock In:** POST /admin/attendance/clock-in
- **Clock Out:** POST /admin/attendance/clock-out
- **Clock Out All:** POST /admin/attendance/clock-out-all
- Daily attendance tracking with status (present/absent/late/etc.)
- Scope: today, clockedIn

### Payroll
**Model:** `Payroll` (uses BelongsToCompany)
- **Generate:** POST /admin/payroll/generate — automated payroll calculation
- **PDF:** /admin/payroll/{payroll}/pdf (dompdf payslip generation)
- Status tracking (draft, approved, paid)

### Leaves
**Model:** `Leave`
- CRUD + approve/reject workflow
- Statuses: pending, approved, rejected

### Performance Reviews
**Model:** `PerformanceReview`
- Employee performance tracking

### Training
**Model:** `Training`
- Training programs and participation tracking

### Recruitment
**Models:** `JobPosting`, `JobApplication`, `JobApplicationApproval`
- Public career page: /careers
- Application form: /careers/{jobPosting}/apply
- Admin manages job postings and reviews applications
- Internal application creation

### Employee Assets
**Model:** `EmployeeAsset`
- Track assets assigned to employees (laptops, phones, etc.)

### HR Events
**Model:** `HrEvent`
- Company events calendar

### Policies
**Model:** `Policy`
- Company policy documents

---

## 10. CRM Module

### Models
- `CrmLead` (BelongsToCompany)
- `CrmDeal`
- `CrmContact`
- `CrmContract`

### Lead Management
- Full CRUD
- Statuses: new, contacted, qualified, converted, lost
- PDF export: /admin/crm-leads/{lead}/pdf
- **Convert to Deal:** POST /admin/crm-leads/{lead}/convert-to-deal

### Deal Management
- Full CRUD
- Linked to Lead, Project, Contracts
- Value tracking
- PDF export: /admin/crm-deals/{deal}/pdf
- **Convert to Project:** POST /admin/crm-deals/{deal}/convert-to-project

### Contact Management
- CRUD for CRM contacts

### Contract Management
- CRUD for contracts
- Status: active, completed, terminated

### Conversion Pipeline
```
Tender → Lead → Deal → Project → Invoice
                        ↓
                    Contract
```

---

## 11. Business Flow Module

### Controller: `BusinessFlowController`
Hii ni **procurement-to-payment pipeline** inayosimamia process nzima ya biashara.

### Flow:

```
TENDER → LEAD → QUOTATION → DEAL → PROJECT BUDGET → PROJECT
                                                         ↓
                          ┌──────────────────────────────┘
                          ↓
                    LPO (Local Purchase Order)
                          ↓
                    GRN (Goods Received Note)
                          ↓
                    DELIVERY NOTE
                          ↓
                    VENDOR INVOICE
                          ↓
                    VENDOR PAYMENT
                          ↓
                    OFFICE EXPENSES / CLIENT RECEIPTS
```

### Sub-Modules:

#### 11.1 Tenders
**Model:** `Tender` (BelongsToCompany)
- CRUD + show detail
- Convert to Lead: POST /admin/tenders/{tender}/convert-to-lead

#### 11.2 Quotations
**Model:** `Quotation` (BelongsToCompany) + `QuotationItem`
- CRUD + show
- PDF export: /admin/quotations/{quotation}/pdf
- Status update: /admin/quotations/{quotation}/status

#### 11.3 Budgets
**Model:** `ProjectBudget` (BelongsToCompany)
- CRUD + approve/reject workflow
- Linked to projects

#### 11.4 LPOs (Local Purchase Orders)
**Model:** `Lpo` (BelongsToCompany) + `LpoItem`
- CRUD + show detail
- Status tracking
- Linked to: Project, Supplier, GRNs, Delivery Notes, Vendor Invoices

#### 11.5 GRNs (Goods Received Notes)
**Model:** `Grn` (BelongsToCompany) + `GrnItem`
- CRUD + show
- Linked to LPO

#### 11.6 Delivery Notes
**Model:** `DeliveryNote`
- CRUD

#### 11.7 Vendor Invoices
**Model:** `VendorInvoice` (BelongsToCompany)
- CRUD + show
- Linked to LPO

#### 11.8 Vendor Payments
**Model:** `VendorPayment`
- Record payments to vendors

#### 11.9 Office Expenses
**Model:** `OfficeExpense` (BelongsToCompany)
- CRUD + approve/reject workflow

#### 11.10 Client Receipts
**Model:** `ClientReceipt`
- Record payments received from clients

#### 11.11 Project Profit
- `/admin/projects/{project}/profit` — Profitability analysis per project

### Business Flow Dashboard
- `/admin/business-flow` — Central dashboard showing all flow items

---

## 12. Sales Module

### Controller: `ErpController`

### Sales Invoices
**Model:** `SalesInvoice` + `SalesInvoiceItem` + `SalesInvoiceReturn` + `SalesInvoiceReturnItem`
- Full CRUD + post (draft → posted)
- PDF download: /admin/sales-invoices/{invoice}/pdf
- Receipt print: /admin/sales-invoices/{invoice}/receipt
- Receipt PDF: /admin/sales-invoices/{invoice}/receipt/pdf
- Status: draft, posted, overdue, paid

### Sales Proposals
**Model:** `SalesProposal` + `SalesProposalItem`
- Full CRUD + status update
- Convert to Invoice: POST /admin/sales-proposals/{proposal}/convert
- Convert to Project: POST /admin/sales-proposals/{proposal}/convert-to-project
- Statuses: draft, sent, accepted, rejected

### Sales Returns
**Model:** `SalesInvoiceReturn` + `SalesInvoiceReturnItem`
- Track returned goods from customers

### Sales Dashboard
- `/admin/sales-dashboard` — Sales KPIs and charts

---

## 13. Purchase Module

### Controller: `ErpController`

### Purchase Invoices
**Model:** `PurchaseInvoice` + `PurchaseInvoiceItem`
- Full CRUD + post
- Vendor management
- Status: draft, posted, paid

### Purchase Returns
**Model:** `PurchaseReturn` + `PurchaseReturnItem`
- Track returned goods to suppliers

---

## 14. Inventory & Products Module

### Controller: `ErpExtendedController`

### Products
**Model:** `Product` (BelongsToCompany)
- Full CRUD
- Stock quantity tracking
- Reorder level alerts
- Linked to categories, warehouses

### Product Categories
**Model:** `ProductCategory`
- Simple CRUD for categorizing products

### Suppliers
**Model:** `Supplier` (BelongsToCompany)
- Vendor/supplier management

### Stock Movements
**Model:** `StockMovement` (BelongsToCompany)
- Track all inventory movements (in/out/transfer)
- Auto-logged on invoice posting

### Warehouses
**Model:** `Warehouse` (BelongsToCompany)
- CRUD for storage locations
- Active/inactive status

### Transfers
**Model:** `Transfer`
- Inventory transfers between warehouses

---

## 15. Accounting Module

### Controller: `ErpExtendedController`

### Bank Accounts
**Model:** `BankAccount` (BelongsToCompany)
- Company bank accounts management

### Accounting Transfers
**Model:** `BankTransfer`
- Money transfers between bank accounts

### Expenses
**Model:** `Expense` (BelongsToCompany)
- Track all company expenses
- Approval workflow support

### Revenues
**Model:** `Revenue` (BelongsToCompany)
- Track all company revenues

### Bills
**Model:** `Bill` (BelongsToCompany)
- Accounts payable tracking

### Estimates
**Model:** `Estimate` (BelongsToCompany)
- Cost estimates for projects/services

### Bank Transfer Accounts (External)
**Model:** `BankTransferAcc`, `BankTransferPayment`
- External bank transfer tracking

---

## 16. Projects Module

### Controller: `ErpExtendedController`

### Projects
**Model:** `Project` (BelongsToCompany)
- Full CRUD + show
- Linked to: Deal, Manager, Tasks, Bugs, Timesheets, Budgets, LPOs
- **Generate Invoice:** `/admin/projects/{project}/generate-invoice`
- **PDF export:** `/admin/projects/{project}/pdf`
- Status: in_progress, completed, on_hold, cancelled

### Project Tasks
**Model:** `ProjectTask`
- Task management within projects
- Assigned to users

### Timesheets
**Model:** `Timesheet`
- Time tracking for projects
- Employee hour logging

### Bugs
**Model:** `ProjectBug`
- Bug/issue tracking for projects
- Status tracking

### Project Budgets
**Model:** `ProjectBudget`
- Budget allocation and approval

### Project Profitability
- `/admin/projects/{project}/profit` — Revenue vs cost analysis

---

## 17. POS Module

### Controller: `ErpExtendedController` (web), `PosController` (API)

### POS Terminal
- `/admin/pos` — Point of Sale interface
- Product selection, cart management, checkout
- Stock auto-deduction on sale

### POS Sales
**Model:** `PosSale` + `PosSaleItem`
- Record of all POS transactions
- Cashier tracking

### POS Reports
- `/admin/pos/reports` — Sales summaries, daily totals

---

## 18. Helpdesk Module

### Controller: `ErpController`

### Tickets
**Model:** `HelpdeskTicket` (BelongsToCompany), `HelpdeskReply`, `HelpdeskCategory`
- Full CRUD + replies
- Statuses: open, in_progress, resolved, closed
- Priority: low, medium, high, urgent
- Ticket categories
- Department/agent assignment

### Categories
- Manage helpdesk ticket categories

---

## 19. Fleet Management Module

### Controller: `FleetController`

### Vehicles
**Model:** `Vehicle` (BelongsToCompany)
- vehicle_number, registration, make, model, fuel_type
- Assigned to employee
- Telematics data support
- Status: active, maintenance, retired

### Maintenance Records
**Model:** `VehicleMaintenance`
- Service history per vehicle
- POST /admin/fleet/{vehicle}/maintenance

### Fuel Logs
**Model:** `FuelLog`
- Fuel consumption tracking
- POST /admin/fleet/{vehicle}/fuel

---

## 20. Fixed Assets Module

### Controller: `FixedAssetController`

### Fixed Assets
**Model:** `FixedAsset` (BelongsToCompany)
- Fields: asset_number, name, category, acquisition_date, acquisition_cost
- salvage_value, useful_life_years, depreciation_method
- Assigned to employee

### Depreciation
- **Run Depreciation:** POST /admin/fixed-assets/{asset}/depreciate
- Methods: straight-line, declining balance
- **DepreciationRecord** model tracks each period

### Disposal
- **Dispose Asset:** POST /admin/fixed-assets/{asset}/dispose
- Records disposal date, proceeds, loss/gain

---

## 21. Document Management Module

### Controller: `DocumentController`

### Documents
**Model:** `Document` (BelongsToCompany)
- Fields: document_number, title, file_path, file_type, version, status
- Uploaded by user
- Version control

### Digital Signatures
**Model:** `DocumentSignature`
- Sign document: POST /admin/documents/{document}/sign
- Decline signature: POST /admin/documents/{document}/decline

### Access Logs
**Model:** `DocumentAccessLog`
- Track who viewed/downloaded documents

### Download
- GET /admin/documents/{document}/download

---

## 22. Call Center Module

### Controller: `CallCenterController`

### Campaigns
- Outbound call campaign management
- POST /admin/call-center/campaigns

### Call Logs
**Model:** `CallLog`
- Record inbound/outbound calls
- Call duration, notes, disposition

### Dashboard
- `/admin/call-center` — Campaign performance metrics

---

## 23. Approval Workflow Module

### Controller: `ApprovalWorkflowController`

### Workflows
**Model:** `ApprovalWorkflow` (BelongsToCompany)
- Define approval processes per module
- Multi-step workflows

### Steps
**Model:** `ApprovalStep`
- Sequential approval steps
- Each step has approver role/user

### Requests
**Model:** `ApprovalRequest`, `ApprovalTrack`
- Submit items for approval
- Approve: POST /admin/approval-requests/{request}/approve
- Reject: POST /admin/approval-requests/{request}/reject
- Full audit trail via ApprovalTrack

---

## 24. Routes & Flows

### Web Routes Summary (598 lines)

```
PUBLIC (no auth required)
├── GET / → welcome page
├── GET /about, /services, /sectors-clients, /why-asyx, /contact, /hosting
├── GET /careers, GET+POST /careers/{jobPosting}/apply
├── Auth::routes (login, register disabled)
├── Custom password reset: /password/reset, /password/code, etc.
└── GET /register/success

AUTHENTICATED
├── /dashboard → RoleDashboardController
├── /dashboard/report-pdf → PDF report download
├── /role/{module} → RolePageController

RECEPTION (/reception)
├── Visitors: GET, POST, PUT, DELETE + checkout
├── Appointments: GET, POST, PUT, DELETE + complete + cancel
├── Calls: GET, POST, PUT, DELETE + status
├── Correspondence: GET, POST, PUT, DELETE + status
├── Parcels: GET, POST, PUT, DELETE + deliver
├── Front Desk: GET, POST, PUT, DELETE + status
├── Departments: GET, POST, PUT, DELETE
├── Announcements: GET, POST, PUT, DELETE + toggle
├── Messages: GET, POST, GET/{id}, DELETE + status
├── Reports: GET
└── My Account: GET, PUT + password

ADMIN (/admin)
├── Dashboard, Profile, Users, Reports
├── Companies (CRUD, switch, consolidated)
├── Intercompany (CRUD, eliminate)
├── Approval Workflows (CRUD, approve, reject)
├── Fleet (CRUD, maintenance, fuel)
├── Fixed Assets (CRUD, depreciate, dispose)
├── Documents (CRUD, download, sign, decline)
├── Call Center (campaigns, calls)
├── Audit Logs (list, filter)
├── ERP Modules (30+ resources via ErpController)
├── Extended ERP (40+ resources via ErpExtendedController)
├── Roles & Permissions (CRUD)
├── User Management (CRUD, password, login history, impersonate)
└── Business Flow (20+ resources via BusinessFlowController)
```

### Request Flow:
```
Browser → Route → Middleware (auth, role, company context, audit log)
       → Controller → Model (Eloquent with scopes) → Database
       → View (Blade with Livewire/Tailwind) → Response
```

### Audit Log Middleware
- Auto-logs all CRUD actions to `audit_logs` table
- Tracks: user, action, module, IP, user agent, URL, method
- Skips: audit-logs routes, livewire*, horizon*, telescope*

---

## 25. API Endpoints

### API Routes Summary (175 lines, Sanctum auth)

```
PUBLIC
├── POST /api/login
└── POST /api/register

AUTHENTICATED (auth:sanctum)
├── GET /api/user, POST /api/logout
├── Dashboard: role, notifications, mark read
├── Companies: apiResource + consolidated
├── Employees: apiResource + attendance/payroll/leaves
├── Attendance: index, today, clock-in, clock-out, store, delete
├── Leaves: index, store, approve, reject, delete
├── Payroll: index, show, generate, approve
├── POS: products, sell, sales, today-summary, show
├── Fleet: CRUD + maintenance + fuel
├── Expenses: index, store, delete
├── Revenues: index, store, delete
├── Bank Accounts: index, store, delete
├── Financial Summary
├── CRM: leads/deals/contacts/contracts CRUD + convert
├── Business Flow: tenders, quotations, LPOs, GRNs,
│   delivery-notes, vendor-invoices, office-expenses (+ approve/reject),
│   client-receipts, proposals, budgets (+ approve)
├── Projects: apiResource + tasks + budget + profitability
├── Products: apiResource + low-stock + stock-movements
├── Invoices: sales/purchase invoices
├── Customers: legacy leads/deals
├── Helpdesk Tickets: apiResource + reply
├── Reports: financial, sales, project, employee, inventory
└── Dashboard KPI & Charts
```

---

## 26. Mobile App (Flutter)

### Location: `mobile/`

### Supported Platforms
- Android
- iOS
- Windows
- macOS
- Linux
- Web

### Architecture (Flutter)
```
mobile/
├── lib/
│   ├── main.dart                       # App entry point
│   ├── core/
│   │   ├── constants.dart              # App constants
│   │   ├── theme.dart                  # App theme
│   │   ├── models/user_model.dart      # User data model
│   │   ├── providers/auth_provider.dart # Auth state management
│   │   ├── services/
│   │   │   ├── api_service.dart        # HTTP client (API calls)
│   │   │   └── auth_service.dart       # Auth token management
│   │   ├── utils/helpers.dart          # Utility functions
│   │   └── widgets/
│   │       ├── app_widgets.dart        # Reusable widgets
│   │       └── kpi_card.dart           # KPI display card
│   └── screens/
│       ├── auth/login_screen.dart
│       ├── crm/crm_screen.dart
│       ├── dashboard/home_screen.dart
│       ├── employees/employees_screen.dart
│       ├── finance/finance_screen.dart
│       ├── fleet/fleet_screen.dart
│       ├── helpdesk/helpdesk_screen.dart
│       ├── inventory/inventory_screen.dart
│       ├── onboarding/onboarding_screen.dart
│       ├── pos/pos_screen.dart
│       ├── projects/projects_screen.dart
│       ├── reports/reports_screen.dart
│       └── settings/settings_screen.dart
```

### API Communication
- Connects to Laravel backend via REST API (`/api/*`)
- Uses Sanctum tokens for authentication
- All web routes mirror mobile API consumption

---

## 27. Role-Based Dashboards

### Structure
```
resources/views/roles/{role-slug}/
├── dashboard.blade.php
└── pages/
    ├── employees.blade.php (example)
    ├── projects.blade.php
    └── (role-specific pages)
```

### 18 Roles with Custom Dashboards:

| Role | Key KPIs | Pages |
|------|----------|-------|
| **admin** / **administrator** | Users, Sales, Expenses, Tickets, Employees, Projects | 8 pages |
| **admin_manager** | Users, Employees, Attendance, Leaves, Reports | 8 pages |
| **director** | Revenue, Expenses, Outstanding, Projects, Proposals | 7 pages |
| **finance_officer** | Sales, Outstanding, Expenses, Revenues, Bills | 8 pages |
| **auditor** | Sales, Purchases, Expenses, POS Sales, Bank | 7 pages |
| **hr_officer** | Employees, Attendance, Payroll, Leaves, Performance | 10 pages |
| **legal_officer** | Contracts, Projects | 4 pages |
| **receptionist** | Leads, Tickets, Contacts + Reception modules | 15 pages |
| **logistics_officer** | Products, Warehouses, Stock, Suppliers, Transfers | 7 pages |
| **technical_manager** | Tickets, Projects, Timesheets, Bugs, Employees | 5 pages |
| **technician** | My Tickets, Projects, Timesheets, Bugs | 4 pages |
| **ict_officer** | Tickets, Projects, Bugs, Assets, Employees | 5 pages |
| **ict_engineer** | Tickets, Projects, Bugs, Assets, Settings | 5 pages |
| **project_manager** | Projects, Timesheets, Bugs, Deals, Reports | 5 pages |
| **operations_manager** | Products, Warehouses, Sales, Purchases, Projects | 8 pages |
| **cashier** | POS Sales, Invoices, Products, Revenues | 5 pages |
| **supervisor** | Attendance, Leaves, POS, Products, Projects | 7 pages |
| **call_center_agent** | Leads, Contacts, Tickets | 3 pages |

### DashboardController
- Failsafe: always returns a valid dashboard even if errors occur
- AI Insights per role based on stats
- 14-day chart trends for each role

---

## 28. Public Pages

### Landing Page
```
resources/views/landing/partials/
├── hero.blade.php           # Hero section with CTAs
├── features.blade.php       # Key features highlight
├── modules.blade.php        # ERP modules showcase
├── about.blade.php          # About the company
├── core-services.blade.php  # Services offered
├── sectors-experience.blade.php # Industry sectors
├── workflow.blade.php       # How it works
├── company-profile.blade.php # Company profile
├── partners.blade.php       # Partners/affiliations
├── testimonials.blade.php   # Client testimonials
├── faq.blade.php            # FAQ section
├── cta.blade.php            # Call to action
├── header.blade.php         # Navigation header
└── footer.blade.php         # Footer
```

### Static Pages
```
/              → welcome.blade.php
/about         → pages.about
/services      → pages.services
/sectors       → pages.sectors
/why-asyx      → pages.why-asyx
/contact       → pages.contact
/hosting       → pages.hosting
/careers       → careers.jobs.index
/careers/{id}/apply → careers.jobs.apply
```

### PDF Templates
```
resources/views/pdf/
├── deal.blade.php
├── invoice.blade.php
├── lead.blade.php
├── payslip.blade.php
├── project.blade.php
├── quotation.blade.php
├── receipt.blade.php
├── receipt-compact.blade.php
└── role-report.blade.php
```

---

## 29. System Configuration

### Config Files (15)
```
config/
├── app.php          # App name, URL, timezone, locale
├── auth.php         # Auth guards, providers, passwords
├── cache.php        # Cache driver config
├── database.php     # Database connections
├── dompdf.php       # PDF generation options
├── filesystems.php  # Storage disks (local, public, s3)
├── fortify.php      # Fortify auth features
├── livewire.php     # Livewire component settings
├── logging.php      # Log channels
├── mail.php         # Mail driver (SMTP config)
├── permission.php   # Permission config
├── queue.php        # Queue driver
├── sanctum.php      # Sanctum token config
├── services.php     # Third-party services
└── session.php      # Session driver and config
```

### Environment Variables (.env)
Key env vars:
```
APP_NAME=Djanproject
APP_URL=http://localhost
DB_DATABASE=djanproject
DB_USERNAME=root
DB_PASSWORD=
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

---

## 30. Database Migrations

### Migration Execution Order (52 files)

```
PHASE 1 - CORE (Laravel defaults)
├── 0001_01_01_000000_create_users_table
├── 0001_01_01_000001_create_cache_table
├── 0001_01_01_000002_create_jobs_table

PHASE 2 - AUTH & NOTIFICATIONS
├── 2024_12_01_000001_create_notification_templates_table
├── 2024_12_02_000001_create_erp_extended_modules_tables
├── 2024_12_03_000001_create_roles_permissions_tables
├── 2024_12_03_000002_add_user_extra_columns
├── 2024_12_03_000003_add_employee_extra_columns
├── 2024_12_03_000004_create_business_flow_tables

PHASE 3 - ERP CORE
├── 2025_07_01_000001_create_warehouses_table
├── 2025_07_01_000002_create_settings_table
├── 2025_07_01_000003_create_plans_table
├── 2025_07_01_000004_create_helpdesk_tables
├── 2025_07_01_000005_create_media_tables
├── 2025_07_01_000006_create_add_ons_tables
├── 2025_07_01_000007_create_email_templates_and_notifications
├── 2025_07_01_000008_create_orders_coupons_tables
├── 2025_07_01_000009_create_login_histories_and_bank_transfers
├── 2025_07_01_000010_create_transfers_and_messenger_tables
├── 2025_07_01_000011_create_purchase_invoice_tables
├── 2025_07_01_000012_create_sales_invoice_tables
├── 2025_07_01_000013_create_sales_proposal_tables

PHASE 4 - COMPANIES & FEATURES
├── 2026_06_27_170715_create_personal_access_tokens_table
├── 2026_06_27_170746_create_permission_tables
├── 2026_06_27_170820_add_two_factor_columns_to_users_table
├── 2026_06_27_200000_add_user_profile_columns
├── 2026_06_28_000000_create_bank_transfer_accs_table
├── 2026_06_28_000001_add_code_to_password_reset_tokens
├── 2026_06_28_100000_create_companies_table
├── 2026_06_28_100001_create_intercompany_transactions_table
├── 2026_06_28_100002_add_company_id_to_business_tables

PHASE 5 - ADVANCED MODULES
├── 2026_06_28_110000_create_approval_workflow_tables
├── 2026_06_28_110100_create_fleet_management_tables
├── 2026_06_28_110200_create_fixed_assets_tables
├── 2026_06_28_110300_create_document_management_tables
├── 2026_06_28_110400_create_call_center_tables
├── 2026_06_28_110500_create_audit_logs_table
├── 2026_06_28_120000_add_clock_fields_to_attendance_table
├── 2026_06_28_210000_add_project_links_to_projects_and_sales_invoices
├── 2026_06_28_213000_add_employee_deduction_fields_and_salary_advances
├── 2026_06_29_000000_create_job_applications_tables
├── 2026_06_29_000100_add_company_id_to_job_postings
├── 2026_06_29_204500_add_reception_permissions

PHASE 6 - RECEPTION MODULE
├── 2026_06_30_000001_create_visitors_table
├── 2026_06_30_000002_create_appointments_table
├── 2026_06_30_000003_create_calls_table
├── 2026_06_30_000004_create_correspondence_table
├── 2026_06_30_000005_create_parcels_table
├── 2026_06_30_000006_create_front_desks_table
├── 2026_06_30_000007_create_departments_table
├── 2026_06_30_000008_create_announcements_table
└── 2026_06_30_000009_create_messages_table
```

---

## 31. Seeders & Default Data

### 7 Seeders

#### 1. `CompanySeeder`
Creates ASYX Group ecosystem:
```
ASYX Group (Parent/Group)
├── ASYX Solutions
├── Parktech
├── Motisha
├── DITTA
└── AJIRA
```

#### 2. `AdminUserSeeder`
Default admin account:
- Email: `admin@djanproject.com`
- Password: `admin12345`

#### 3. `RoleSeeder`
Creates 18 roles with granular permissions assigned to each role:

| Role | Permissions |
|------|------------|
| director | Dashboard + Reports + Sales/Purchase/Expense views |
| admin_manager | Full management access |
| administrator | Full system access |
| finance_officer | Financial dashboard + Invoices + Expenses + Revenues + Bank |
| auditor | Read-only financial access |
| hr_officer | Employees + Attendance + Payroll + Leaves + Performance + Training + Recruitment |
| legal_officer | Contracts + Projects + Reports |
| ict_officer | Tickets + Projects + Bugs + Assets + Employees |
| ict_engineer | Tickets + Projects + Bugs + Assets + Settings |
| receptionist | Leads + Tickets + Contacts + Sales Invoices |
| cashier | POS + Sales Invoices + Products + Revenues |
| call_center_agent | Leads + Contacts + Tickets |
| logistics_officer | Products + Warehouses + Stock + Suppliers + Transfers |
| operations_manager | Products + Warehouses + Sales + Purchases + Projects |
| project_manager | Projects + Timesheets + Bugs + Deals |
| technical_manager | Tickets + Projects + Timesheets + Bugs + Employees |
| supervisor | Attendance + Leaves + POS + Products + Projects |
| technician | My Tickets + Projects + Timesheets + Bugs |

#### 4. `UserRoleSeeder`
Assigns admin role to the admin user

#### 5. `ApprovalWorkflowSeeder`
Default approval workflows

#### 6. `MasterDataSeeder`
Reference/master data for business operations

#### 7. `DatabaseSeeder`
Master seeder — calls all seeders in correct order

### Running Seeders:
```bash
php artisan db:seed
# OR
php artisan db:seed --class=DatabaseSeeder
```

---

## APPENDIX

### A. Key Helper Functions

**`app/Helpers/currency.php`**
```php
tzs($amount, $withSymbol = true)     // TZS 1,500,000
tzs_short($amount)                   // TZS 1.5M, TZS 500K
```

### B. Key Traits

**`app/Traits/BelongsToCompany.php`**
- Global scope for company data isolation
- Auto-sets company_id on creation
- Methods: company(), scopeForCompany(), scopeAllCompanies()

### C. Middleware Stack

```
1. SetCompanyContext   - Sets current company in session/views
2. RoleMiddleware      - Role-based access control
3. AuditLogMiddleware  - Auto-log all CRUD actions
4. RedirectIfAuthenticated - Redirect guard
```

### D. External Packages (composer.json key packages)
- `laravel/fortify` — Authentication scaffolding
- `laravel/sanctum` — API token authentication
- `barryvdh/laravel-dompdf` — PDF generation
- `chatify` — Real-time messaging
- `livewire/livewire` — Dynamic UI components

### E. Front-end Libraries (CDN)
- Tailwind CSS (CDN)
- SweetAlert2 (CDN)
- Nunito Font (Bunny Fonts)

---

*Documentation generated from full system cross-check — June 2026*
*ASYX Group ERP System — Djanproject*
