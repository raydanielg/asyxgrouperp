<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Leave;
use App\Models\PerformanceReview;
use App\Models\Training;
use App\Models\JobPosting;
use App\Models\HrEvent;
use App\Models\Policy;
use App\Models\CrmLead;
use App\Models\CrmDeal;
use App\Models\CrmContract;
use App\Models\CrmContact;
use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\Revenue;
use App\Models\Bill;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Timesheet;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Tender;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\SalesProposal;
use App\Models\FixedAsset;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Models\FuelLog;
use App\Models\Document;
use App\Models\CallLog;
use App\Models\HelpdeskCategory;
use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Models\Setting;
use App\Models\AuditLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $companies = \App\Models\Company::where('is_group', false)->get();
        $group = \App\Models\Company::where('is_group', true)->first();
        $admin = User::where('email', 'admin@djanproject.com')->first();
        $now = now();
        $employees = []; $users = []; $products = []; $suppliers = []; $projects = [];

        // ═══════════════════════════════════════
        // EMPLOYEES & USERS
        // ═══════════════════════════════════════
        $empData = [
            ['f'=>'John','l'=>'Mushi','e'=>'john.mushi@asyxgroup.co.tz','p'=>'255712000001','d'=>'Management','sig'=>'Managing Director','sal'=>8500000],
            ['f'=>'Sarah','l'=>'Mkono','e'=>'sarah.mkono@asyxgroup.co.tz','p'=>'255712000002','d'=>'Finance','sig'=>'Finance Manager','sal'=>5500000],
            ['f'=>'Peter','l'=>'Kavuma','e'=>'peter.kavuma@parktech.co.tz','p'=>'255712000003','d'=>'Technical','sig'=>'Technical Manager','sal'=>4800000],
            ['f'=>'Grace','l'=>'Mwangi','e'=>'grace.mwangi@asyxgroup.co.tz','p'=>'255712000004','d'=>'HR','sig'=>'HR Officer','sal'=>3200000],
            ['f'=>'David','l'=>'Ochieng','e'=>'david.ochieng@motisha.co.tz','p'=>'255712000005','d'=>'Projects','sig'=>'Project Manager','sal'=>5200000],
            ['f'=>'Mary','l'=>'Ndugu','e'=>'mary.ndugu@terkmark.co.tz','p'=>'255712000006','d'=>'Finance','sig'=>'Accountant','sal'=>2800000],
            ['f'=>'Joseph','l'=>'Mbwana','e'=>'joseph.mbwana@glovin.co.tz','p'=>'255712000007','d'=>'Sales','sig'=>'Sales Executive','sal'=>2500000],
            ['f'=>'Anna','l'=>'Kipingu','e'=>'anna.kipingu@asyxgroup.co.tz','p'=>'255712000008','d'=>'Technical','sig'=>'Support Engineer','sal'=>2200000],
            ['f'=>'Robert','l'=>'Kato','e'=>'robert.kato@parktech.co.tz','p'=>'255712000009','d'=>'Technical','sig'=>'Technician','sal'=>1800000],
            ['f'=>'Elizabeth','l'=>'Sanga','e'=>'elizabeth.sanga@asyxgroup.co.tz','p'=>'255712000010','d'=>'Admin','sig'=>'Receptionist','sal'=>1500000],
            ['f'=>'Michael','l'=>'Nkya','e'=>'michael.nkya@motisha.co.tz','p'=>'255712000011','d'=>'Support','sig'=>'Call Center Agent','sal'=>1600000],
            ['f'=>'Catherine','l'=>'Lema','e'=>'catherine.lema@terkmark.co.tz','p'=>'255712000012','d'=>'Procurement','sig'=>'Procurement Officer','sal'=>2600000],
            ['f'=>'Samuel','l'=>'Shayo','e'=>'samuel.shayo@glovin.co.tz','p'=>'255712000013','d'=>'Logistics','sig'=>'Logistics Officer','sal'=>2100000],
            ['f'=>'Esther','l'=>'Mlay','e'=>'esther.mlay@asyxgroup.co.tz','p'=>'255712000014','d'=>'Finance','sig'=>'Payroll Officer','sal'=>2400000],
            ['f'=>'Patrick','l'=>'Kimaro','e'=>'patrick.kimaro@parktech.co.tz','p'=>'255712000015','d'=>'Technical','sig'=>'Systems Engineer','sal'=>3500000],
        ];

        foreach ($empData as $i => $e) {
            $company = $companies->get($i % $companies->count());
            $empId = 'EMP-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $emp = Employee::updateOrCreate(
                ['employee_id' => $empId],
                [
                    'company_id' => $company->id,
                    'first_name' => $e['f'],
                    'last_name' => $e['l'],
                    'email' => $e['e'],
                    'phone' => $e['p'],
                    'department' => $e['d'],
                    'designation' => $e['sig'],
                    'salary' => $e['sal'],
                    'status' => 'active',
                    'joining_date' => $now->copy()->subYears(rand(1, 5))->subDays(rand(0, 365)),
                    'gender' => $i % 2 == 0 ? 'male' : 'female',
                    'employment_type' => 'full_time',
                ]
            );
            $employees[] = $emp;

            $user = User::updateOrCreate(
                ['email' => $e['e']],
                [
                    'company_id' => $company->id,
                    'name' => $e['f'] . ' ' . $e['l'],
                    'first_name' => $e['f'],
                    'last_name' => $e['l'],
                    'phone' => $e['p'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => $now,
                    'role' => 'user',
                ]
            );
            $users[] = $user;

            Attendance::create([
                'employee_id' => $emp->id,
                'date' => $now->format('Y-m-d'),
                'check_in' => $now->copy()->setHour(8)->setMinute(rand(0, 30))->format('H:i:s'),
                'status' => 'present',
            ]);
            for ($d = 1; $d <= 5; $d++) {
                $dt = $now->copy()->subDays($d);
                Attendance::create([
                    'employee_id' => $emp->id, 'date' => $dt->format('Y-m-d'),
                    'check_in' => $dt->setHour(8)->setMinute(rand(0, 45))->format('H:i:s'),
                    'check_out' => $dt->setHour(17)->setMinute(rand(0, 30))->format('H:i:s'),
                    'status' => rand(0, 10) > 2 ? 'present' : 'absent',
                ]);
            }
        }

        // ═══════════════════════════════════════
        // PRODUCTS & INVENTORY
        // ═══════════════════════════════════════
        foreach (['ICT Equipment','Software','Networking','Office Supplies','Furniture','Security Systems','Cabling'] as $cn) {
            ProductCategory::updateOrCreate(['slug' => Str::slug($cn)], ['name' => $cn, 'slug' => Str::slug($cn)]);
        }
        $cats = ProductCategory::all();

        foreach (['Main Warehouse - Dar','Parktech Warehouse','Motisha Storage','Terkmark Warehouse','Glovin Inventory'] as $i => $wn) {
            Warehouse::updateOrCreate([
                'name' => $wn,
            ], [
                'company_id' => $companies->get($i % $companies->count())->id,
                'name' => $wn, 'address' => 'Dar es Salaam', 'city' => 'Dar es Salaam', 'zip_code' => '14111',
                'creator_id' => $admin->id, 'created_by' => $admin->id, 'is_active' => true,
            ]);
        }

        $prodData = [
            ['name'=>'HP ProBook 450 G10','code'=>'HP-PB450','cat'=>'ICT Equipment','sp'=>3200000,'pp'=>2500000,'q'=>45],
            ['name'=>'Dell Latitude 5540','code'=>'DELL-LAT5540','cat'=>'ICT Equipment','sp'=>3800000,'pp'=>2900000,'q'=>30],
            ['name'=>'Cisco Catalyst 9200','code'=>'CIS-CAT9200','cat'=>'Networking','sp'=>8500000,'pp'=>6200000,'q'=>15],
            ['name'=>'Ubiquiti UniFi AP Pro','code'=>'UBI-UAP-PRO','cat'=>'Networking','sp'=>850000,'pp'=>550000,'q'=>120],
            ['name'=>'Microsoft 365 Business','code'=>'MS-365-BP','cat'=>'Software','sp'=>35000,'pp'=>25000,'q'=>999],
            ['name'=>'Windows Server 2022','code'=>'MS-WS2022','cat'=>'Software','sp'=>1200000,'pp'=>850000,'q'=>50],
            ['name'=>'CAT6 UTP Cable 305m','code'=>'CBL-CAT6-305','cat'=>'Cabling','sp'=>450000,'pp'=>320000,'q'=>200],
            ['name'=>'APC Smart-UPS 1500VA','code'=>'APC-SU1500','cat'=>'ICT Equipment','sp'=>2100000,'pp'=>1500000,'q'=>25],
            ['name'=>'Office Desk 120x60cm','code'=>'FUR-DESK120','cat'=>'Furniture','sp'=>450000,'pp'=>280000,'q'=>60],
            ['name'=>'Ergonomic Chair','code'=>'FUR-CHAIR-ERG','cat'=>'Furniture','sp'=>650000,'pp'=>400000,'q'=>40],
            ['name'=>'Hikvision 4MP Camera','code'=>'HIK-4MP-IPC','cat'=>'Security Systems','sp'=>350000,'pp'=>220000,'q'=>150],
            ['name'=>'Hikvision NVR 16CH','code'=>'HIK-NVR16','cat'=>'Security Systems','sp'=>2800000,'pp'=>1900000,'q'=>20],
            ['name'=>'Cisco ISR 4321 Router','code'=>'CIS-ISR4321','cat'=>'Networking','sp'=>12500000,'pp'=>9200000,'q'=>8],
            ['name'=>'Staples A4 Paper 5000pk','code'=>'OFF-PAPER-A4','cat'=>'Office Supplies','sp'=>45000,'pp'=>32000,'q'=>500],
            ['name'=>'HP LaserJet M404dn','code'=>'HP-LJ-M404','cat'=>'ICT Equipment','sp'=>1800000,'pp'=>1200000,'q'=>18],
            ['name'=>'Lenovo ThinkPad T14','code'=>'LEN-T14','cat'=>'ICT Equipment','sp'=>4200000,'pp'=>3300000,'q'=>20],
            ['name'=>'MikroTik CRS328 Switch','code'=>'MT-CRS328','cat'=>'Networking','sp'=>2700000,'pp'=>2100000,'q'=>22],
            ['name'=>'Brother HL-L2375DW','code'=>'BRO-HL2375','cat'=>'ICT Equipment','sp'=>650000,'pp'=>480000,'q'=>35],
        ];
        foreach ($prodData as $pd) {
            $cat = $cats->where('name', $pd['cat'])->first();
            $products[] = Product::updateOrCreate([
                'product_code' => $pd['code'],
            ], [
                'company_id' => $companies->random()->id, 'category_id' => $cat->id,
                'name' => $pd['name'], 'product_code' => $pd['code'],
                'sale_price' => $pd['sp'], 'purchase_price' => $pd['pp'],
                'stock_quantity' => $pd['q'], 'reorder_level' => 10, 'is_active' => true,
            ]);
        }

        foreach ([
            ['n'=>'TechMart Tanzania Ltd','p'=>'255712100001','e'=>'info@techmart.co.tz'],
            ['n'=>'CompSys East Africa','p'=>'255712100002','e'=>'sales@compsys.co.tz'],
            ['n'=>'ICT Solutions Ltd','p'=>'255712100003','e'=>'info@ictsolutions.co.tz'],
            ['n'=>'Tanzania Office Supplies','p'=>'255712100004','e'=>'sales@tos.co.tz'],
        ] as $sd) {
            $suppliers[] = Supplier::updateOrCreate([
                'email' => $sd['e'],
            ], [
                'company_id' => $companies->random()->id, 'name' => $sd['n'],
                'contact_person' => 'Contact', 'phone' => $sd['p'], 'email' => $sd['e'],
                'address' => 'Dar es Salaam', 'is_active' => true,
            ]);
        }

        // ═══════════════════════════════════════
        // TENDERS
        // ═══════════════════════════════════════
        $titles = ['Supply of ICT Equipment','Network Infrastructure','CCTV Installation','Office Furniture Supply','Software License Renewal','Data Center Migration'];
        $orgs = ['CRDB Bank','NMB Bank','TPA','Vodacom','TANESCO','DIT'];
        foreach ($titles as $i => $t) {
            Tender::create([
                'company_id' => $companies->random()->id,
                'tender_number' => 'TND-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'title' => $t, 'client_name' => 'Client ' . chr(65+$i),
                'client_organization' => $orgs[$i], 'estimated_value' => rand(20000000, 500000000),
                'submission_date' => $now->subDays(rand(10, 60)),
                'closing_date' => $now->addDays(rand(5, 30)),
                'status' => ['received','under_review','submitted','won','lost','converted'][$i],
                'assigned_to' => $users[array_rand($users)]->id, 'created_by' => $admin->id,
            ]);
        }

        // ═══════════════════════════════════════
        // CRM
        // ═══════════════════════════════════════
        $leadData = [
            ['f'=>'James','l'=>'Mwakalobo','c'=>'CRDB Bank','e'=>'james@crdb.co.tz','p'=>'255713000001'],
            ['f'=>'Mercy','l'=>'Kessy','c'=>'NMB Bank','e'=>'mercy@nmb.co.tz','p'=>'255713000002'],
            ['f'=>'Hassan','l'=>'Said','c'=>'Vodacom','e'=>'hassan@vodacom.co.tz','p'=>'255713000003'],
            ['f'=>'Amina','l'=>'Juma','c'=>'TANESCO','e'=>'amina@tanesco.co.tz','p'=>'255713000004'],
        ];
        $leads = [];
        foreach ($leadData as $i => $ld) {
            $lead = CrmLead::create([
                'company_id' => $companies->random()->id,
                'lead_number' => 'LEAD-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'first_name' => $ld['f'],'last_name' => $ld['l'],'email' => $ld['e'],'phone' => $ld['p'],
                'company' => $ld['c'],'source' => ['Website','Referral','Tender'][rand(0,2)],
                'status' => $i < 2 ? 'converted' : 'qualified',
                'assigned_to' => $users[array_rand($users)]->id,'created_by' => $admin->id,
            ]);
            $leads[] = $lead;

            CrmContact::create([
                'first_name' => $ld['f'],'last_name' => $ld['l'],'email' => $ld['e'],'phone' => $ld['p'],
                'company' => $ld['c'],'position' => 'Manager',
            ]);
        }

        $dealValues = [150000000,85000000,45000000,250000000];
        foreach ($leads as $i => $lead) {
            if ($lead->status !== 'converted') continue;
            $deal = CrmDeal::create([
                'company_id' => $lead->company_id,'lead_id' => $lead->id,
                'deal_number' => 'DEAL-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'title' => 'Deal - ' . $lead->company,
                'value' => $dealValues[$i],'stage' => $i < 1 ? 'closed_won' : 'negotiation',
                'status' => $i < 1 ? 'won' : 'open',
                'expected_close_date' => $now->addDays(rand(15, 60)),
                'assigned_to' => $users[array_rand($users)]->id,
            ]);
            $deals[] = $deal;

            if ($i < 2) {
                CrmContract::create([
                    'company_id' => $deal->company_id,'deal_id' => $deal->id,
                    'contract_number' => 'CTR-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                    'title' => 'Contract - ' . $lead->company,'client_name' => $lead->company,
                    'value' => $dealValues[$i],'start_date' => $now,'end_date' => $now->addYear(),
                    'status' => 'active','terms' => 'Standard terms apply.',
                ]);
            }
        }

        // ═══════════════════════════════════════
        // PROJECTS
        // ═══════════════════════════════════════
        $projData = [
            ['t'=>'CRDB ICT Equipment Supply','b'=>150000000,'s'=>'in_progress'],
            ['t'=>'NMB Network Upgrade Phase 2','b'=>85000000,'s'=>'in_progress'],
            ['t'=>'Vodacom CCTV Installation','b'=>45000000,'s'=>'completed'],
            ['t'=>'TPA Data Center Migration','b'=>250000000,'s'=>'planning'],
            ['t'=>'DIT Campus Network','b'=>120000000,'s'=>'in_progress'],
            ['t'=>'TANESCO Managed Services','b'=>95000000,'s'=>'in_progress'],
        ];
        $taskNames = ['Site Survey','Requirements','Design','Procurement','Installation','Testing','Handover'];
        foreach ($projData as $i => $pd) {
            $proj = Project::create([
                'company_id' => $companies->random()->id,
                'project_number' => 'PRJ-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'title' => $pd['t'], 'budget' => $pd['b'],
                'description' => 'Project: ' . $pd['t'],
                'start_date' => $now->subDays(rand(15, 90)), 'due_date' => $now->addDays(rand(30, 180)),
                'status' => $pd['s'], 'priority' => ['high','medium','low'][rand(0,2)],
                'manager_id' => $users[array_rand($users)]->id,
                'deal_id' => isset($deals[$i]) ? $deals[$i]->id : null,
            ]);
            $projects[] = $proj;

            foreach (array_rand($taskNames, 4) as $tk) {
                ProjectTask::create([
                    'project_id' => $proj->id, 'title' => $taskNames[$tk],
                    'assigned_to' => $users[array_rand($users)]->id,
                    'due_date' => $now->addDays(rand(5, 90)),
                    'priority' => ['high','medium','low'][rand(0,2)],
                    'status' => ['pending','in_progress','completed'][rand(0,2)],
                ]);
            }
            for ($d = 0; $d < 5; $d++) {
                Timesheet::create([
                    'employee_id' => $employees[array_rand($employees)]->id,
                    'project_id' => $proj->id,
                    'date' => $now->copy()->subDays($d)->format('Y-m-d'),
                    'hours' => rand(4, 10), 'description' => 'Work on ' . $pd['t'],
                ]);
            }
        }

        // ═══════════════════════════════════════
        // SALES INVOICES
        // ═══════════════════════════════════════
        $sOpts = ['draft','posted','paid','overdue','partial'];
        for ($i = 1; $i <= 30; $i++) {
            $st = $sOpts[array_rand($sOpts)]; $total = rand(500000, 50000000);
            $paid = $st === 'paid' ? $total : ($st === 'partial' ? $total * rand(1, 9) / 10 : 0);
            $inv = SalesInvoice::create([
                'company_id' => $companies->random()->id,
                'customer_id' => $users[array_rand($users)]->id,
                'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'invoice_date' => now()->subDays(rand(1, 60)),
                'due_date' => now()->addDays(rand(1, 30)),
                'subtotal' => $total, 'total_amount' => $total,
                'paid_amount' => $paid, 'balance_amount' => $total - $paid,
                'status' => $st, 'creator_id' => $admin->id, 'created_by' => $admin->id,
            ]);
            SalesInvoiceItem::create([
                'invoice_id' => $inv->id, 'product_name' => 'Item',
                'quantity' => rand(1, 10), 'unit_price' => $total / rand(1, 10), 'total_amount' => $total,
            ]);
        }

        // PURCHASE INVOICES
        for ($i = 1; $i <= 20; $i++) {
            $st = $sOpts[array_rand($sOpts)]; $total = rand(300000, 30000000);
            $paid = $st === 'paid' ? $total : ($st === 'partial' ? $total * rand(1, 9) / 10 : 0);
            PurchaseInvoice::create([
                'company_id' => $companies->random()->id,
                'vendor_id' => $users[array_rand($users)]->id,
                'invoice_number' => 'PINV-' . now()->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'invoice_date' => now()->subDays(rand(1, 45)),
                'due_date' => now()->addDays(rand(1, 30)),
                'subtotal' => $total, 'total_amount' => $total,
                'paid_amount' => $paid, 'balance_amount' => $total - $paid,
                'status' => $st, 'creator_id' => $admin->id, 'created_by' => $admin->id,
            ]);
        }

        // SALES PROPOSALS
        for ($i = 1; $i <= 12; $i++) {
            $total = rand(1000000, 30000000);
            SalesProposal::create([
                'company_id' => $companies->random()->id,
                'customer_id' => $users[array_rand($users)]->id,
                'proposal_number' => 'PRO-' . now()->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'proposal_date' => now()->subDays(rand(1, 30)),
                'due_date' => now()->addDays(rand(7, 30)),
                'subtotal' => $total, 'total_amount' => $total * 1.18,
                'status' => ['draft','sent','accepted','rejected'][rand(0,3)],
                'creator_id' => $admin->id, 'created_by' => $admin->id,
            ]);
        }

        // ═══════════════════════════════════════
        // FINANCIAL
        // ═══════════════════════════════════════
        for ($i = 1; $i <= 12; $i++) {
            Expense::create([
                'company_id' => $companies->random()->id,
                'expense_number' => 'EXP-' . now()->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'amount' => rand(50000, 5000000), 'expense_date' => now()->subDays(rand(0, 60)),
                'category' => ['Operations','Travel','Utilities','Maintenance'][rand(0,3)],
                'payee' => 'Payee ' . $i, 'payment_method' => 'bank', 'created_by' => $admin->id,
            ]);
            Revenue::create([
                'company_id' => $companies->random()->id,
                'revenue_number' => 'REV-' . now()->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'amount' => rand(500000, 10000000), 'revenue_date' => now()->subDays(rand(0, 60)),
                'category' => ['Services','Products','Consulting'][rand(0,2)], 'payer' => 'Client ' . $i,
                'notes' => 'Revenue ' . $i, 'created_by' => $admin->id,
            ]);
            Bill::create([
                'company_id' => $companies->random()->id,
                'bill_number' => 'BILL-' . now()->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'vendor_name' => 'Vendor ' . $i, 'amount' => rand(200000, 3000000),
                'paid_amount' => rand(0, 3000000), 'bill_date' => now()->subDays(rand(1, 30)),
                'due_date' => now()->addDays(rand(1, 30)),
                'status' => ['unpaid','partial','paid'][rand(0,2)],
            ]);
        }

        $banks = [['n'=>'CRDB Bank - TZS','b'=>'CRDB Bank','c'=>'TZS'],['n'=>'NMB Bank - USD','b'=>'NMB Bank','c'=>'USD'],['n'=>'NBC Bank - Ops','b'=>'NBC Bank','c'=>'TZS']];
        foreach ($banks as $i => $bk) {
            BankAccount::create([
                'company_id' => $companies->get($i % $companies->count())->id,
                'account_name' => $bk['n'], 'account_number' => '01' . str_pad((string)rand(10000000,99999999),10,'0',STR_PAD_LEFT),
                'bank_name' => $bk['b'], 'currency' => $bk['c'],
                'opening_balance' => rand(10000000, 100000000),
                'current_balance' => rand(50000000, 500000000), 'is_active' => true,
            ]);
        }

        // ═══════════════════════════════════════
        // HR
        // ═══════════════════════════════════════
        foreach ($employees as $i => $emp) {
            if ($i % 3 == 0) Leave::create([
                'employee_id' => $emp->id, 'leave_type' => ['annual','sick','personal'][rand(0,2)],
                'start_date' => $now->subDays(rand(1, 30)),
                'end_date' => $now->subDays(rand(1, 30))->addDays(rand(1, 5)),
                'days' => rand(1, 5), 'status' => ['pending','approved','rejected'][rand(0,2)],
            ]);
            if ($i % 2 == 0) {
                $pnum = 'PAY-' . $now->format('Ym') . '-' . str_pad($i+1,4,'0',STR_PAD_LEFT);
                Payroll::updateOrCreate(
                    ['payroll_number' => $pnum],
                    [
                        'employee_id' => $emp->id,
                        'month' => $now->format('F'), 'year' => $now->year,
                        'basic_salary' => $emp->salary, 'allowances' => rand(100000, 500000),
                        'deductions' => rand(50000, 300000),
                        'net_salary' => $emp->salary + rand(100000, 500000) - rand(50000, 300000),
                        'status' => 'paid', 'created_by' => $admin->id,
                    ]
                );
            }
            PerformanceReview::create([
                'employee_id' => $emp->id, 'review_period' => 'Q' . rand(1,4) . ' ' . $now->year,
                'goals' => 'Achieve targets', 'achievements' => 'Exceeded expectations',
                'rating' => rand(3, 5), 'reviewer_id' => $users[array_rand($users)]->id,
            ]);
        }

        foreach (['PMP Certification','Cisco CCNA','ITIL Foundation'] as $tn) {
            Training::create(['title' => $tn, 'trainer' => 'External','start_date' => $now->subMonths(rand(1,3)), 'status' => 'completed']);
        }

        foreach (['Senior Network Engineer','Project Manager','Sales Executive'] as $i => $jt) {
            JobPosting::create([
                'company_id' => $companies->random()->id, 'title' => $jt,
                'department' => ['Technical','Projects','Sales'][$i],
                'description' => 'Looking for an experienced professional',
                'requirements' => "3+ years experience\nDegree required",
                'location' => 'Dar es Salaam', 'job_type' => 'full_time',
                'vacancies' => 2, 'deadline' => $now->addDays(30), 'status' => 'open',
            ]);
        }

        HrEvent::create(['title'=>'Staff Meeting','description'=>'Annual meeting','event_date'=>$now->addDays(30),'location'=>'Dar es Salaam','type'=>'company']);
        Policy::create(['title'=>'Code of Conduct','category'=>'HR','content'=>'All employees must adhere...','is_active'=>true]);

        // ═══════════════════════════════════════
        // HELPDESK
        // ═══════════════════════════════════════
        foreach (['Technical Support','Network Issue','Hardware','Software','Account Issue'] as $cn) {
            HelpdeskCategory::create(['name' => $cn, 'creator_id' => $admin->id, 'created_by' => $admin->id]);
        }
        $tCats = HelpdeskCategory::all();
        for ($i = 1; $i <= 20; $i++) {
            $titles = ['Internet issue','Printer problem','Email setup','Software install','Access request','VPN issue','Server alert','Password reset','System error','Network slow'];
            HelpdeskTicket::create([
                'company_id' => $companies->random()->id,
                'ticket_id' => 'TKT-' . $now->format('Ymd') . '-' . str_pad($i,4,'0',STR_PAD_LEFT),
                'title' => $titles[($i - 1) % count($titles)],
                'description' => 'Issue description', 'category_id' => $tCats->random()->id,
                'priority' => ['low','medium','high','urgent'][rand(0,3)],
                'status' => ['open','in_progress','resolved','closed'][rand(0,3)],
                'created_by' => $users[array_rand($users)]->id,
            ]);
        }

        // ═══════════════════════════════════════
        // FIXED ASSETS
        // ═══════════════════════════════════════
        foreach (['Laptop','Server','Switch','Printer','UPS','Furniture'] as $at) {
            FixedAsset::create([
                'company_id' => $companies->random()->id,
                'asset_number' => 'AST-' . strtoupper(Str::random(8)),
                'asset_tag' => 'TAG-' . strtoupper(Str::random(6)),
                'name' => $at, 'category' => $at,
                'acquisition_date' => $now->subMonths(rand(3, 36)),
                'acquisition_cost' => rand(500000, 5000000),
                'salvage_value' => rand(50000, 500000),
                'useful_life_years' => rand(3, 10),
                'depreciation_method' => 'straight_line',
                'accumulated_depreciation' => rand(100000, 1000000),
                'net_book_value' => rand(200000, 3000000),
                'status' => ['active','under_maintenance','disposed'][rand(0,2)],
                'location' => 'Dar es Salaam',
                'assigned_to' => $employees[array_rand($employees)]->id,
                'created_by' => $admin->id,
            ]);
        }

        // ═══════════════════════════════════════
        // FLEET
        // ═══════════════════════════════════════
        foreach ([
            ['mk'=>'Toyota','md'=>'Hilux','reg'=>'T 123 ABC','y'=>2021,'ft'=>'diesel'],
            ['mk'=>'Toyota','md'=>'Land Cruiser','reg'=>'T 456 DEF','y'=>2022,'ft'=>'diesel'],
            ['mk'=>'Isuzu','md'=>'D-Max','reg'=>'T 789 GHI','y'=>2020,'ft'=>'diesel'],
        ] as $vd) {
            $v = Vehicle::updateOrCreate(
                ['registration_number' => $vd['reg']],
                [
                    'company_id' => $companies->random()->id,
                    'vehicle_number' => 'VEH-' . strtoupper(Str::random(6)),
                    'make' => $vd['mk'], 'model' => $vd['md'],
                    'year' => $vd['y'], 'fuel_type' => $vd['ft'],
                    'status' => 'active', 'odometer_reading' => rand(5000, 80000),
                    'insurance_expiry' => $now->addMonths(rand(1, 11)),
                    'assigned_to' => $employees[array_rand($employees)]->id,
                ]
            );
            VehicleMaintenance::create(['vehicle_id'=>$v->id,'maintenance_type'=>'service','description'=>'Regular service','service_date'=>$now->subDays(rand(5,60)),'cost'=>rand(200000,1500000),'service_provider'=>'Auto Center','status'=>'completed']);
            FuelLog::create(['vehicle_id'=>$v->id,'fuel_date'=>$now->subDays(rand(1,14)),'litres'=>rand(20,80),'cost_per_litre'=>2950,'total_cost'=>rand(59000,236000),'fuel_station'=>'Total Energies']);
        }

        // ═══════════════════════════════════════
        // DOCUMENTS
        // ═══════════════════════════════════════
        foreach (['Contract','Invoice','Report','Proposal','Policy'] as $i => $dt) {
            Document::create([
                'company_id' => $companies->random()->id,
                'document_number' => 'DOC-' . $now->format('Ymd') . '-' . str_pad($i+1, 3, '0', STR_PAD_LEFT),
                'title' => $dt . ' ' . ($i+1), 'category' => strtolower($dt),
                'status' => 'active', 'file_path' => '/documents/sample-' . ($i+1) . '.pdf',
                'file_type' => 'application/pdf', 'file_size' => rand(100000, 5000000),
                'version' => '1.0', 'uploaded_by' => $admin->id,
            ]);
        }

        // ═══════════════════════════════════════
        // CALL LOGS
        // ═══════════════════════════════════════
        for ($i = 1; $i <= 10; $i++) {
            CallLog::create([
                'company_id' => $companies->random()->id,
                'caller_name' => 'Caller ' . $i,
                'caller_phone' => '2557' . str_pad((string)rand(10000000,99999999),8,'0',STR_PAD_LEFT),
                'call_direction' => ['inbound','outbound'][rand(0,1)],
                'call_start' => $now->subHours(rand(1, 72)),
                'duration_seconds' => rand(30, 1800),
                'status' => ['completed','missed','failed'][rand(0,2)],
                'agent_id' => $users[array_rand($users)]->id,
            ]);
        }

        // ═══════════════════════════════════════
        // SETTINGS & AUDIT
        // ═══════════════════════════════════════
        Setting::set('app_name', 'ASYX Group ERP');
        Setting::set('currency', 'TZS');
        Setting::set('timezone', 'Africa/Dar_es_Salaam');

        AuditLog::create([
            'user_id' => $admin->id, 'company_id' => $group?->id,
            'action' => 'system_seeded', 'module' => 'System',
            'new_values' => json_encode(['message' => 'Master data seeded']),
            'ip_address' => '127.0.0.1',
        ]);
    }
}
