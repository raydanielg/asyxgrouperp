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

| Role | Email | Module Access |
|---|---|---|
| Super Admin | `admin@djanproject.com` | All modules |
| Director | `director@djanproject.com` | Reports, Projects, Sales Dashboard, Employees, Invoices, Expenses, Helpdesk |
| Admin Manager | `admin.manager@djanproject.com` | Users, Roles, Employees, Attendance, Leaves, Reports, Settings |
| Administrator | `administrator@djanproject.com` | Users, Roles, Employees, Projects, Products, Settings, Reports |
| Finance Officer | `finance@djanproject.com` | Sales/Purchase Invoices, Expenses, Revenues, Bills, Bank Accounts, Transfers, Reports |
| Auditor | `auditor@djanproject.com` | Sales/Purchase Invoices, Expenses, Revenues, Bills, Bank Accounts, Reports |
| HR Officer | `hr@djanproject.com` | Employees, Attendance, Payroll, Leaves, Performance, Training, Recruitment, Assets, Policies |
| Legal Officer | `legal@djanproject.com` | Contracts, Contacts, Projects, Reports |
| Receptionist | `receptionist@djanproject.com` | Leads, Contacts, Tickets |
| Logistics Officer | `logistics@djanproject.com` | Products, Warehouses, Stock Movements, Suppliers, Transfers, Purchases |
| Technical Manager | `tech.manager@djanproject.com` | Tickets, Projects, Timesheets, Bugs, Employees |
| Technician | `technician@djanproject.com` | Tickets, Projects, Timesheets, Bugs |
| ICT Officer | `ict.officer@djanproject.com` | Tickets, Projects, Bugs, Assets, Employees |
| ICT Engineer | `ict.engineer@djanproject.com` | Tickets, Projects, Bugs, Assets, Settings |
| Project Manager | `project.manager@djanproject.com` | Projects, Timesheets, Bugs, Deals, Reports |
| Operations Manager | `operations@djanproject.com` | Products, Warehouses, Stock, Sales, Purchases, Projects, Reports |
| Call Center Agent | `callcenter@djanproject.com` | Leads, Contacts, Tickets |
| Cashier | `cashier@djanproject.com` | POS Terminal, POS Reports, Sales Invoices, Products, Revenues |
| Supervisor | `supervisor@djanproject.com` | Employees, Attendance, Leaves, Projects, POS, Products, Reports |

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
