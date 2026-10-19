# DjanProject — ERP & Business Management System

Mfumo kamili wa ERP (Enterprise Resource Planning) wenye moduli 30+ na Role-Based Access Control (RBAC) kwa role 18. Mfumo huu unahudumia biashara zenye mahitaji ya usimamizi wa fedha, wafanyakazi, hisa, mauzo, miradi, CRM, helpdesk, na zaidi.

---

## Technology Stack

- **Backend:** Laravel 10 (PHP 8.2+)
- **Frontend:** Blade Templating + TailwindCSS + Vite
- **Database:** SQLite (dev) / MySQL (production)
- **Charts:** Chart.js + Consoletvs/Charts
- **Auth:** Laravel UI (Spatie Permission package installed)
- **Icons:** Heroicons (SVG inline)

---

## Installation

```bash
# 1. Clone project
git clone <repo-url> djanproject
cd djanproject

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Environment setup
cp .env.example .env
php artisan key:generate

# 5. Database setup (fresh migrate + seed)
php artisan migrate:fresh --seed

# 6. Build frontend assets
npm run dev    # development
npm run build  # production

# 7. Start server
php artisan serve
```

---

## Default Login Credentials

Password ya kila account ni: **`password123`**

Super administrator: `admin@djanproject.com` (full access to everything).
Group super admin: `superadmin@djanproject.com`.

One demo account is seeded per role in the ASYX role catalog. Emails follow the
pattern `<role.name.with.dots>@djanproject.com`, e.g.:

| Role | Email |
|---|---|
| ERP Super Administrator | `superadmin@djanproject.com` |
| ERP Administrator | `admin@djanproject.com` |
| ICT Administrator | `ict.administrator@djanproject.com` |
| Auditor | `auditor@djanproject.com` |
| Managing Director | `managing.director@djanproject.com` |
| General Manager | `general.manager@djanproject.com` |
| Technical Manager | `technical.manager@djanproject.com` |
| Operations Manager | `operations.manager@djanproject.com` |
| Finance Manager | `finance.manager@djanproject.com` |
| Chief Accountant | `chief.accountant@djanproject.com` |
| Accountant | `accountant@djanproject.com` |
| Accounts Receivable Officer | `accounts.receivable.officer@djanproject.com` |
| Accounts Payable Officer | `accounts.payable.officer@djanproject.com` |
| Cashier | `cashier@djanproject.com` |
| Payroll Officer | `payroll.officer@djanproject.com` |
| Budget Officer | `budget.officer@djanproject.com` |
| Credit Controller | `credit.controller@djanproject.com` |
| Procurement Manager | `procurement.manager@djanproject.com` |
| Procurement Officer | `procurement.officer@djanproject.com` |
| Tender Officer | `tender.officer@djanproject.com` |
| Store Manager | `store.manager@djanproject.com` |
| Storekeeper | `storekeeper@djanproject.com` |
| Inventory Controller | `inventory.controller@djanproject.com` |
| Asset Officer | `asset.officer@djanproject.com` |
| Sales Manager | `sales.manager@djanproject.com` |
| Business Development Manager | `business.development.manager@djanproject.com` |
| Sales Executive | `sales.executive@djanproject.com` |
| CRM Officer | `crm.officer@djanproject.com` |
| Marketing Officer | `marketing.officer@djanproject.com` |
| Project Director | `project.director@djanproject.com` |
| Project Manager | `project.manager@djanproject.com` |
| Technical Projects Manager | `technical.projects.manager@djanproject.com` |
| Project Coordinator | `project.coordinator@djanproject.com` |
| Project Engineer | `project.engineer@djanproject.com` |
| Site Supervisor | `site.supervisor@djanproject.com` |
| Team Leader | `team.leader@djanproject.com` |
| Project Accountant | `project.accountant@djanproject.com` |
| Senior Systems Engineer | `senior.systems.engineer@djanproject.com` |
| Systems Engineer | `systems.engineer@djanproject.com` |
| Network Engineer | `network.engineer@djanproject.com` |
| Software Engineer | `software.engineer@djanproject.com` |
| Cybersecurity Engineer | `cybersecurity.engineer@djanproject.com` |
| Support Engineer | `support.engineer@djanproject.com` |
| Field Technician | `field.technician@djanproject.com` |
| NOC Engineer | `noc.engineer@djanproject.com` |
| Service Desk Manager | `service.desk.manager@djanproject.com` |
| Helpdesk Supervisor | `helpdesk.supervisor@djanproject.com` |
| Helpdesk Officer | `helpdesk.officer@djanproject.com` |
| Call Center Supervisor | `call.center.supervisor@djanproject.com` |
| Call Center Agent | `call.center.agent@djanproject.com` |
| HR Manager | `hr.manager@djanproject.com` |
| HR Officer | `hr.officer@djanproject.com` |
| Recruitment Officer | `recruitment.officer@djanproject.com` |
| Training Officer | `training.officer@djanproject.com` |
| Time & Attendance Officer | `time.attendance.officer@djanproject.com` |
| Operations Officer | `operations.officer@djanproject.com` |
| Fleet Manager | `fleet.manager@djanproject.com` |
| Logistics Officer | `logistics.officer@djanproject.com` |
| Employee Self-Service | `employee.self.service@djanproject.com` |
| Manager Self-Service | `manager.self.service@djanproject.com` |

Seeded ASYX staff accounts (MasterDataSeeder) use `@asyxgroup.co.tz` /
subsidiary domains, e.g. `john.mushi@asyxgroup.co.tz` (Managing Director),
`sarah.mkono@asyxgroup.co.tz` (Finance Manager), etc.

Role menus, module access and permissions are driven by a single source of
truth: `app/Support/RolePermissions.php` (permissions per role) and
`app/Support/RoleModules.php` (sidebar modules per role).

---

## System Modules (30+)

### Finance & Accounting
- **Sales Invoices** — Create, track, and manage sales invoices with line items, tax, and discounts
- **Purchase Invoices** — Manage vendor purchase invoices
- **Expenses** — Track and categorize business expenses
- **Revenues** — Record and categorize revenue streams
- **Bills** — Manage payable bills with due dates
- **Bank Accounts** — Track multiple bank account balances
- **Account Transfers** — Record inter-account fund transfers
- **Reports** — Comprehensive financial reports with KPIs

### HR & Payroll
- **Employees** — Full employee management (personal info, department, position, salary)
- **Attendance** — Daily attendance tracking (present/absent/late)
- **Payroll** — Salary management and payroll processing
- **Leaves** — Leave requests with approval workflow
- **Performance** — Employee performance reviews
- **Training** — Training programs and tracking
- **Recruitment** — Job postings and candidate management
- **Assets** — Employee asset assignments
- **Policies** — Company policy management

### Inventory & Warehouse
- **Products** — Product catalog with SKU, pricing, and stock levels
- **Warehouses** — Multi-warehouse management
- **Stock Movements** — Track all stock in/out movements
- **Suppliers** — Supplier management
- **Inventory Transfers** — Inter-warehouse stock transfers

### Sales & CRM
- **POS Terminal** — Point of sale with product grid and cart
- **POS Reports** — Daily and monthly POS sales reports
- **Sales Dashboard** — Proposals and invoices overview
- **CRM Leads** — Lead tracking and conversion
- **CRM Deals** — Deal pipeline with values
- **CRM Contacts** — Contact management
- **CRM Contracts** — Contract management

### Project Management
- **Projects** — Project tracking with status and due dates
- **Timesheets** — Time tracking per project
- **Bugs** — Bug tracking and resolution

### Helpdesk
- **Tickets** — Support ticket system with priority and status

### Administration
- **Users** — User account management
- **Roles & Permissions** — RBAC with 18 roles and granular permissions
- **Settings** — System configuration

---

## Architecture & Key Files

### Controllers

| File | Purpose |
|---|---|
| `app/Http/Controllers/Admin/DashboardController.php` | Admin dashboard with global stats |
| `app/Http/Controllers/Admin/ErpController.php` | All ERP module CRUD operations |
| `app/Http/Controllers/RoleDashboardController.php` | Role-specific dashboard data (KPIs, charts, quick actions) |
| `app/Http/Controllers/RolePageController.php` | Renders role-specific module pages |
| `app/Http/Controllers/Auth/LoginController.php` | Auth with role-based redirect |

### Routes

```
routes/web.php
├── /dashboard              → RoleDashboardController@index (role dashboard)
├── /role/{module}          → RolePageController@page (role-specific pages)
└── /admin/*                → Admin ERP routes (admin users only)
```

### Views Structure

```
resources/views/
├── layouts/
│   └── admin.blade.php          → Main layout with role-aware sidebar
├── admin/                       → Admin-only views (ERP modules)
│   ├── dashboard.blade.php
│   ├── invoices/
│   ├── pos/
│   ├── accounting/
│   └── ...
├── dashboard/
│   └── role.blade.php           → Generic role dashboard (fallback)
├── roles/                       → Role-specific views
│   ├── director/
│   │   ├── dashboard.blade.php  → Director dashboard with KPIs & charts
│   │   └── pages/               → Director module pages
│   ├── finance-officer/
│   │   ├── dashboard.blade.php
│   │   └── pages/
│   ├── hr-officer/
│   │   ├── dashboard.blade.php
│   │   └── pages/
│   ├── ... (18 roles total)
│   └── shared/
│       └── page.blade.php       → Shared module page template (all modules)
```

### Models

```
app/Models/
├── User.php              → Auth user with role/permission relationships
├── Role.php              → Role with permissions() and users() relationships
├── Permission.php        → Permission model
├── Employee.php          → HR employee records
├── Product.php           → Inventory products
├── SalesInvoice.php      → Sales invoices with items
├── PurchaseInvoice.php   → Purchase invoices with items
├── Expense.php           → Expense tracking
├── Revenue.php           → Revenue tracking
├── Project.php           → Project management
├── HelpdeskTicket.php    → Support tickets
├── CrmLead.php           → CRM leads
├── CrmDeal.php           → CRM deals
├── CrmContact.php        → CRM contacts
├── CrmContract.php       → CRM contracts
├── PosSale.php           → POS sales
├── Warehouse.php         → Warehouse locations
├── StockMovement.php     → Stock movement log
├── Supplier.php          → Suppliers
├── Bill.php              → Payable bills
├── BankAccount.php       → Bank accounts
├── AccTransfer.php       → Account transfers
├── Attendance.php        → Attendance records
├── Leave.php             → Leave requests
└── ... (30+ models total)
```

### Database Seeders

| Seeder | Purpose |
|---|---|
| `AdminUserSeeder` | Creates super admin account |
| `RoleSeeder` | Creates 18 roles with permission assignments |
| `UserRoleSeeder` | Creates test users for each role with login credentials |
| `DatabaseSeeder` | Orchestrates all seeders in order |

---

## Role-Based Access Control (RBAC)

### How It Works

1. **User Login** — `LoginController` checks user's role and redirects:
   - Admin → `/admin/dashboard`
   - Other roles → `/dashboard` (role-specific dashboard)

2. **Sidebar Menu** — `admin.blade.php` renders role-specific menu items:
   - Admin users see full ERP menu (all modules)
   - Non-admin users see only their role's permitted modules
   - Menu items link to `/role/{module}` route

3. **Page Rendering** — `RolePageController` handles `/role/{module}`:
   - Detects user's role
   - Loads module-specific data from database
   - Renders `roles/{role-slug}/pages/{module}.blade.php`
   - Falls back to `roles/shared/page.blade.php` if role-specific page doesn't exist

4. **Permission Enforcement** — Each role has granular permissions:
   - Permissions stored in `permissions` table
   - Role-permission mapping in `role_permission` pivot table
   - User-role mapping in `role_user` pivot table
   - `User::hasPermission()` and `User::hasRole()` methods for checks

### 18 Roles

```
admin, director, admin_manager, administrator, finance_officer, auditor,
hr_officer, legal_officer, receptionist, logistics_officer,
technical_manager, technician, ict_officer, ict_engineer,
project_manager, operations_manager, call_center_agent, cashier, supervisor
```

---

## Currency

All monetary values use **Tanzanian Shillings (TZS)** formatting:

```php
// PHP
'TZS ' . number_format($amount)

// JavaScript
'TZS ' + amount.toLocaleString()
```

---

## Development Commands

```bash
# Database
php artisan migrate:fresh --seed     # Reset DB + seed all data
php artisan migrate                  # Run pending migrations
php artisan migrate:status           # Check migration status

# Cache
php artisan cache:clear              # Clear application cache
php artisan view:clear               # Clear compiled views
php artisan route:clear              # Clear route cache
php artisan config:clear             # Clear config cache

# Frontend
npm run dev                          # Start Vite dev server
npm run build                        # Build production assets

# Server
php artisan serve                    # Start dev server (port 8000)
```

---

## Project Structure Overview

```
DjanProject/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              → Admin controllers (Dashboard, ERP)
│   │   │   ├── Auth/               → Login, Register controllers
│   │   │   ├── RoleDashboardController.php
│   │   │   └── RolePageController.php
│   │   └── Middleware/
│   │       └── RoleMiddleware.php   → Role-based route protection
│   └── Models/                      → 30+ Eloquent models
├── bootstrap/app.php                → Middleware registration
├── database/
│   ├── migrations/                  → 25+ migration files
│   └── seeders/
│       ├── AdminUserSeeder.php
│       ├── RoleSeeder.php
│       ├── UserRoleSeeder.php
│       └── DatabaseSeeder.php
├── resources/views/
│   ├── layouts/admin.blade.php      → Main layout (role-aware sidebar)
│   ├── admin/                       → Admin ERP views
│   └── roles/                       → Role-specific dashboards & pages
├── routes/web.php                   → All route definitions
├── .env                             → Environment configuration
└── composer.json                    → PHP dependencies
```

---

## License

This project is proprietary software. All rights reserved.
