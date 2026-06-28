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
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Lpo;
use App\Models\LpoItem;
use App\Models\Grn;
use App\Models\GrnItem;
use App\Models\DeliveryNote;
use App\Models\VendorInvoice;
use App\Models\VendorPayment;
use App\Models\OfficeExpense;
use App\Models\ClientReceipt;
use App\Models\FixedAsset;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Models\FuelLog;
use App\Models\Document;
use App\Models\CallLog;
use App\Models\HelpdeskCategory;
use App\Models\HelpdeskTicket;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\SalesProposal;
use App\Models\SalesProposalItem;
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

        // ═══ EMPLOYEES & USERS ═══
        $empData = [
            ['fname'=>'John','lname'=>'Mushi','email'=>'john.mushi@asyxgroup.co.tz','phone'=>'255712000001','dept'=>'Management','desig'=>'Managing Director','salary'=>8500000],
            ['fname'=>'Sarah','lname'=>'Mkono','email'=>'sarah.mkono@asyxgroup.co.tz','phone'=>'255712000002','dept'=>'Finance','desig'=>'Finance Manager','salary'=>5500000],
            ['fname'=>'Peter','lname'=>'Kavuma','email'=>'peter.kavuma@parktech.co.tz','phone'=>'255712000003','dept'=>'Technical','desig'=>'Technical Manager','salary'=>4800000],
            ['fname'=>'Grace','lname'=>'Mwangi','email'=>'grace.mwangi@asyxgroup.co.tz','phone'=>'255712000004','dept'=>'Human Resources','desig'=>'HR Officer','salary'=>3200000],
            ['fname'=>'David','lname'=>'Ochieng','email'=>'david.ochieng@motisha.co.tz','phone'=>'255712000005','dept'=>'Projects','desig'=>'Project Manager','salary'=>5200000],
            ['fname'=>'Mary','lname'=>'Ndung\'u','email'=>'mary.ndungu@terkmark.co.tz','phone'=>'255712000006','dept'=>'Finance','desig'=>'Accountant','salary'=>2800000],
            ['fname'=>'Joseph','lname'=>'Mbwana','email'=>'joseph.mbwana@glovin.co.tz','phone'=>'255712000007','dept'=>'Sales','desig'=>'Sales Executive','salary'=>2500000],
            ['fname'=>'Anna','lname'=>'Kipingu','email'=>'anna.kipingu@asyxgroup.co.tz','phone'=>'255712000008','dept'=>'Technical','desig'=>'Support Engineer','salary'=>2200000],
            ['fname'=>'Robert','lname'=>'Kato','email'=>'robert.kato@parktech.co.tz','phone'=>'255712000009','dept'=>'Technical','desig'=>'Technician','salary'=>1800000],
            ['fname'=>'Elizabeth','lname'=>'Sanga','email'=>'elizabeth.sanga@asyxgroup.co.tz','phone'=>'255712000010','dept'=>'Admin','desig'=>'Receptionist','salary'=>1500000],
            ['fname'=>'Michael','lname'=>'Nkya','email'=>'michael.nkya@motisha.co.tz','phone'=>'255712000011','dept'=>'Support','desig'=>'Call Center Agent','salary'=>1600000],
            ['fname'=>'Catherine','lname'=>'Lema','email'=>'catherine.lema@terkmark.co.tz','phone'=>'255712000012','dept'=>'Procurement','desig'=>'Procurement Officer','salary'=>2600000],
            ['fname'=>'Samuel','lname'=>'Shayo','email'=>'samuel.shayo@glovin.co.tz','phone'=>'255712000013','dept'=>'Logistics','desig'=>'Logistics Officer','salary'=>2100000],
            ['fname'=>'Esther','lname'=>'Mlay','email'=>'esther.mlay@asyxgroup.co.tz','phone'=>'255712000014','dept'=>'Finance','desig'=>'Payroll Officer','salary'=>2400000],
            ['fname'=>'Patrick','lname'=>'Kimaro','email'=>'patrick.kimaro@parktech.co.tz','phone'=>'255712000015','dept'=>'Technical','desig'=>'Systems Engineer','salary'=>3500000],
        ];

        foreach ($empData as $i => $e) {
            $company = $companies->get($i % $companies->count());
            $emp = Employee::create([
                'company_id' => $company->id,
                'employee_id' => 'EMP-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'first_name' => $e['fname'],
                'last_name' => $e['lname'],
                'email' => $e['email'],
                'phone' => $e['phone'],
                'department' => $e['dept'],
                'designation' => $e['desig'],
                'salary' => $e['salary'],
                'status' => 'active',
                'joining_date' => $now->subYears(rand(1, 5))->subDays(rand(0, 365)),
                'gender' => $i % 2 == 0 ? 'male' : 'female',
                'employment_type' => 'full_time',
            ]);

            $user = User::create([
                'company_id' => $company->id,
                'name' => $e['fname'] . ' ' . $e['lname'],
                'first_name' => $e['fname'],
                'last_name' => $e['lname'],
                'email' => $e['email'],
                'phone' => $e['phone'],
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'role' => 'user',
            ]);

            $employees[] = $emp;
            $users[] = $user;

            Attendance::create([
                'employee_id' => $emp->id,
                'date' => $now->format('Y-m-d'),
                'check_in' => $now->copy()->setHour(8)->setMinute(rand(0, 30))->format('H:i:s'),
                'status' => 'present',
            ]);

            for ($d = 1; $d <= 5; $d++) {
                $date = $now->copy()->subDays($d);
                Attendance::create([
                    'employee_id' => $emp->id,
                    'date' => $date->format('Y-m-d'),
                    'check_in' => $date->setHour(8)->setMinute(rand(0, 45))->format('H:i:s'),
                    'check_out' => $date->setHour(17)->setMinute(rand(0, 30))->format('H:i:s'),
                    'status' => rand(0, 10) > 2 ? 'present' : 'absent',
                ]);
            }
        }

        // ═══ PRODUCT CATEGORIES & PRODUCTS ═══
        $catNames = ['ICT Equipment','Software','Networking','Office Supplies','Furniture','Security Systems','Cabling'];
        foreach ($catNames as $cn) ProductCategory::create(['name' => $cn, 'slug' => Str::slug($cn)]);
        $categories = ProductCategory::all();

        $warehouseNames = ['Main Warehouse - Dar','Parktech Warehouse','Motisha Storage','Terkmark Warehouse','Glovin Inventory'];
        foreach ($warehouseNames as $i => $wn) {
            $wh = Warehouse::create([
                'company_id' => $companies->get($i % $companies->count())->id,
                'name' => $wn,
                'address' => 'Dar es Salaam',
                'is_active' => true,
            ]);
        }

        $prodData = [
            ['n'=>'HP ProBook 450 G10','sku'=>'HP-PB450','cat'=>'ICT Equipment','p'=>3200000,'c'=>2500000,'s'=>45],
            ['n'=>'Dell Latitude 5540','sku'=>'DELL-LAT5540','cat'=>'ICT Equipment','p'=>3800000,'c'=>2900000,'s'=>30],
            ['n'=>'Cisco Catalyst 9200','sku'=>'CIS-CAT9200','cat'=>'Networking','p'=>8500000,'c'=>6200000,'s'=>15],
            ['n'=>'Ubiquiti UniFi AP Pro','sku'=>'UBI-UAP-PRO','cat'=>'Networking','p'=>850000,'c'=>550000,'s'=>120],
            ['n'=>'Microsoft 365 Business Premium','sku'=>'MS-365-BP','cat'=>'Software','p'=>35000,'c'=>25000,'s'=>999],
            ['n'=>'Windows Server 2022','sku'=>'MS-WS2022','cat'=>'Software','p'=>1200000,'c'=>850000,'s'=>50],
            ['n'=>'CAT6 UTP Cable 305m','sku'=>'CBL-CAT6-305','cat'=>'Cabling','p'=>450000,'c'=>320000,'s'=>200],
            ['n'=>'APC Smart-UPS 1500VA','sku'=>'APC-SU1500','cat'=>'ICT Equipment','p'=>2100000,'c'=>1500000,'s'=>25],
            ['n'=>'Office Desk 120x60cm','sku'=>'FUR-DESK120','cat'=>'Furniture','p'=>450000,'c'=>280000,'s'=>60],
            ['n'=>'Ergonomic Office Chair','sku'=>'FUR-CHAIR-ERG','cat'=>'Furniture','p'=>650000,'c'=>400000,'s'=>40],
            ['n'=>'Hikvision 4MP IP Camera','sku'=>'HIK-4MP-IPC','cat'=>'Security Systems','p'=>350000,'c'=>220000,'s'=>150],
            ['n'=>'Hikvision NVR 16CH','sku'=>'HIK-NVR16','cat'=>'Security Systems','p'=>2800000,'c'=>1900000,'s'=>20],
            ['n'=>'Cisco ISR 4321 Router','sku'=>'CIS-ISR4321','cat'=>'Networking','p'=>12500000,'c'=>9200000,'s'=>8],
            ['n'=>'Staples A4 Paper 5000pk','sku'=>'OFF-PAPER-A4','cat'=>'Office Supplies','p'=>45000,'c'=>32000,'s'=>500],
            ['n'=>'HP LaserJet Pro M404dn','sku'=>'HP-LJ-M404','cat'=>'ICT Equipment','p'=>1800000,'c'=>1200000,'s'=>18],
        ];
        foreach ($prodData as $pd) {
            $cat = $categories->where('name', $pd['cat'])->first();
            $products[] = Product::create([
                'company_id' => $companies->random()->id,
                'category_id' => $cat->id,
                'name' => $pd['n'],
                'product_code' => $pd['sku'],
                'sale_price' => $pd['p'],
                'purchase_price' => $pd['c'],
                'stock_quantity' => $pd['s'],
                'reorder_level' => 10,
                'is_active' => true,
            ]);
        }

        // ═══ SUPPLIERS ═══
        $supData = [
            ['n'=>'TechMart Tanzania Ltd','p'=>'255712100001','e'=>'info@techmart.co.tz'],
            ['n'=>'CompSys East Africa','p'=>'255712100002','e'=>'sales@compsys.co.tz'],
            ['n'=>'NetConnect Solutions','p'=>'255712100003','e'=>'info@netconnect.co.tz'],
            ['n'=>'OfficePro Supplies Ltd','p'=>'255712100004','e'=>'orders@officepro.co.tz'],
            ['n'=>'SecureTech Systems','p'=>'255712100005','e'=>'info@securetech.co.tz'],
        ];
        foreach ($supData as $sd) {
            $suppliers[] = Supplier::create([
                'company_id' => $companies->random()->id,
                'name' => $sd['n'],
                'contact_person' => 'Contact',
                'phone' => $sd['p'],
                'email' => $sd['e'],
                'address' => 'Dar es Salaam',
                'is_active' => true,
            ]);
        }

        // ═══ TENDERS ═══
        $tenderTitles = ['Supply of ICT Equipment - Phase 1','Network Infrastructure Upgrade','CCTV Installation - Bank Branch','Office Furniture Supply','Software License Renewal','Data Center Migration','Helpdesk Outsourcing','Managed Services Contract'];
        $tenderOrgs = ['CRDB Bank','NMB Bank','Tanzania Ports Authority','TPA','Vodacom TZ','Airtel TZ','TANESCO','DIT'];
        $tenderStatus = ['received','under_review','submitted','won','lost','converted','received','under_review'];
        foreach ($tenderTitles as $i => $tt) {
            Tender::create([
                'company_id' => $companies->random()->id,
                'tender_number' => 'TND-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'title' => $tt,
                'client_name' => 'Client ' . chr(65 + $i),
                'client_organization' => $tenderOrgs[$i],
                'client_email' => 'client' . ($i + 1) . '@example.com',
                'description' => 'Tender description for ' . $tt,
                'estimated_value' => rand(20000000, 500000000),
                'submission_date' => $now->subDays(rand(10, 60)),
                'closing_date' => $now->addDays(rand(5, 30)),
                'status' => $tenderStatus[$i],
                'assigned_to' => $users[array_rand($users)]->id,
                'created_by' => $admin->id,
            ]);
        }

        // ═══ CRM LEADS ═══
        $leadData = [
            ['f'=>'James','l'=>'Mwakalobo','c'=>'CRDB Bank Plc','e'=>'james@crdb.co.tz','p'=>'255713000001'],
            ['f'=>'Mercy','l'=>'Kessy','c'=>'NMB Bank Plc','e'=>'mercy@nmb.co.tz','p'=>'255713000002'],
            ['f'=>'Hassan','l'=>'Said','c'=>'Vodacom TZ','e'=>'hassan@vodacom.co.tz','p'=>'255713000003'],
            ['f'=>'Amina','l'=>'Juma','c'=>'TANESCO','e'=>'amina@tanesco.co.tz','p'=>'255713000004'],
            ['f'=>'Charles','l'=>'Massawe','c'=>'Tanzania Ports','e'=>'charles@ports.go.tz','p'=>'255713000005'],
            ['f'=>'Diana','l'=>'Mwaikambo','c'=>'DIT','e'=>'diana@dit.ac.tz','p'=>'255713000006'],
            ['f'=>'Emmanuel','l'=>'Nchimbi','c'=>'Airtel TZ','e'=>'emmanuel@airtel.co.tz','p'=>'255713000007'],
            ['f'=>'Fatma','l'=>'Salim','c'=>'TPA','e'=>'fatma@tpa.go.tz','p'=>'255713000008'],
        ];
        $leads = [];
        foreach ($leadData as $i => $ld) {
            $lead = CrmLead::create([
                'company_id' => $companies->random()->id,
                'lead_number' => 'LEAD-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'first_name' => $ld['f'],
                'last_name' => $ld['l'],
                'email' => $ld['e'],
                'phone' => $ld['p'],
                'company' => $ld['c'],
                'source' => ['Website','Referral','Tender','Call Center'][rand(0,3)],
                'status' => $i < 4 ? 'converted' : 'qualified',
                'notes' => 'Lead from ' . $ld['c'],
                'assigned_to' => $users[array_rand($users)]->id,
                'created_by' => $admin->id,
            ]);
            $leads[] = $lead;

            CrmContact::create([
                'company_id' => $companies->random()->id,
                'lead_id' => $lead->id,
                'first_name' => $ld['f'],
                'last_name' => $ld['l'],
                'email' => $ld['e'],
                'phone' => $ld['p'],
                'company' => $ld['c'],
                'position' => 'Manager',
            ]);
        }

        // ═══ CRM DEALS & CONTRACTS ═══
        $dealValues = [150000000,85000000,45000000,250000000,120000000,95000000,32000000,78000000];
        $deals = [];
        foreach ($leads as $i => $lead) {
            if ($lead->status !== 'converted') continue;
            $deal = CrmDeal::create([
                'company_id' => $lead->company_id,
                'lead_id' => $lead->id,
                'deal_number' => 'DEAL-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'title' => 'Deal with ' . $lead->company,
                'value' => $dealValues[$i],
                'stage' => $i < 3 ? 'closed_won' : 'negotiation',
                'status' => $i < 3 ? 'won' : 'open',
                'expected_close_date' => $now->addDays(rand(15, 60)),
                'assigned_to' => $users[array_rand($users)]->id,
            ]);
            $deals[] = $deal;

            if ($i < 3) {
                CrmContract::create([
                    'company_id' => $deal->company_id,
                    'deal_id' => $deal->id,
                    'contract_number' => 'CTR-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                    'title' => 'Service Contract - ' . $lead->company,
                    'client_name' => $lead->company,
                    'value' => $dealValues[$i],
                    'start_date' => $now,
                    'end_date' => $now->addYear(),
                    'status' => 'active',
                    'terms' => 'Standard terms and conditions apply.',
                ]);
            }
        }

        // ═══ PROJECTS ═══
        $projData = [
            ['n'=>'CRDB ICT Equipment Supply','b'=>150000000,'s'=>'in_progress'],
            ['n'=>'NMB Network Upgrade Phase 2','b'=>85000000,'s'=>'in_progress'],
            ['n'=>'Vodacom CCTV Installation','b'=>45000000,'s'=>'completed'],
            ['n'=>'TPA Data Center Migration','b'=>250000000,'s'=>'planning'],
            ['n'=>'DIT Campus Network','b'=>120000000,'s'=>'in_progress'],
            ['n'=>'TANESCO Managed Services','b'=>95000000,'s'=>'in_progress'],
        ];
        $taskNames = ['Site Survey','Requirements','Design','Procurement','Installation','Testing','Commissioning','Handover'];
        foreach ($projData as $i => $pd) {
            $proj = Project::create([
                'company_id' => $companies->random()->id,
                'deal_id' => $deals[$i] ?? null,
                'project_number' => 'PRJ-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'name' => $pd['n'],
                'description' => 'Project: ' . $pd['n'],
                'budget' => $pd['b'],
                'start_date' => $now->subDays(rand(15, 90)),
                'due_date' => $now->addDays(rand(30, 180)),
                'status' => $pd['s'],
                'priority' => ['high','medium','low'][rand(0,2)],
                'manager_id' => $users[array_rand($users)]->id,
            ]);
            $projects[] = $proj;

            foreach (array_rand(array_flip($taskNames), 4) as $tk) {
                ProjectTask::create([
                    'project_id' => $proj->id,
                    'title' => $taskNames[$tk],
                    'description' => 'Task for ' . $proj->name,
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
                    'hours' => rand(4, 10),
                    'description' => 'Work on ' . $proj->name,
                ]);
            }
        }

        // ═══ SALES INVOICES ═══
        $statusOpts = ['draft','posted','paid','overdue','partial'];
        for ($i = 1; $i <= 15; $i++) {
            $status = $statusOpts[array_rand($statusOpts)];
            $total = rand(500000, 50000000);
            $paid = $status === 'paid' ? $total : ($status === 'partial' ? $total * rand(1, 9) / 10 : 0);
            $inv = SalesInvoice::create([
                'company_id' => $companies->random()->id,
                'invoice_number' => 'INV-' . $now->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_name' => 'Customer ' . $i,
                'customer_email' => 'customer' . $i . '@example.com',
                'invoice_date' => $now->subDays(rand(1, 60)),
                'due_date' => $now->addDays(rand(1, 30)),
                'total_amount' => $total,
                'paid_amount' => $paid,
                'balance_amount' => $total - $paid,
                'status' => $status,
                'created_by' => $admin->id,
            ]);
            SalesInvoiceItem::create([
                'sales_invoice_id' => $inv->id,
                'description' => 'Item for invoice ' . $inv->invoice_number,
                'quantity' => rand(1, 10),
                'unit_price' => $total / rand(1, 10),
                'total' => $total,
            ]);
        }

        // ═══ PURCHASE INVOICES ═══
        for ($i = 1; $i <= 12; $i++) {
            $status = $statusOpts[array_rand($statusOpts)];
            $total = rand(300000, 30000000);
            $paid = $status === 'paid' ? $total : ($status === 'partial' ? $total * rand(1, 9) / 10 : 0);
            $inv = PurchaseInvoice::create([
                'company_id' => $companies->random()->id,
                'invoice_number' => 'PINV-' . $now->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'vendor_name' => 'Vendor ' . $i,
                'invoice_date' => $now->subDays(rand(1, 45)),
                'due_date' => $now->addDays(rand(1, 30)),
                'total_amount' => $total,
                'paid_amount' => $paid,
                'balance_amount' => $total - $paid,
                'status' => $status,
                'created_by' => $admin->id,
            ]);
            PurchaseInvoiceItem::create([
                'purchase_invoice_id' => $inv->id,
                'description' => 'Item for purchase ' . $inv->invoice_number,
                'quantity' => rand(1, 5),
                'unit_price' => $total / rand(1, 5),
                'total' => $total,
            ]);
        }

        // ═══ SALES PROPOSALS ═══
        for ($i = 1; $i <= 8; $i++) {
            $total = rand(1000000, 30000000);
            SalesProposal::create([
                'company_id' => $companies->random()->id,
                'proposal_number' => 'PRO-' . $now->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'customer_name' => 'Prospect ' . $i,
                'customer_email' => 'prospect' . $i . '@example.com',
                'proposal_date' => $now->subDays(rand(1, 30)),
                'valid_until' => $now->addDays(rand(7, 30)),
                'subtotal' => $total,
                'total' => $total * 1.18,
                'status' => ['draft','sent','accepted','rejected'][rand(0,3)],
                'created_by' => $admin->id,
            ]);
        }

        // ═══ FINANCIAL RECORDS ═══
        for ($i = 1; $i <= 15; $i++) {
            Expense::create([
                'company_id' => $companies->random()->id,
                'expense_number' => 'EXP-' . $now->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'amount' => rand(50000, 5000000),
                'expense_date' => $now->subDays(rand(0, 60)),
                'category' => ['Operations','Travel','Utilities','Maintenance'][rand(0,3)],
                'payee' => 'Payee ' . $i,
                'payment_method' => 'bank',
                'notes' => 'Expense ' . $i,
                'created_by' => $admin->id,
            ]);

            Revenue::create([
                'company_id' => $companies->random()->id,
                'revenue_number' => 'REV-' . $now->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'amount' => rand(500000, 10000000),
                'revenue_date' => $now->subDays(rand(0, 60)),
                'category' => ['Services','Products','Consulting'][rand(0,2)],
                'description' => 'Revenue ' . $i,
                'created_by' => $admin->id,
            ]);

            if ($i <= 8) {
                Bill::create([
                    'company_id' => $companies->random()->id,
                    'bill_number' => 'BILL-' . $now->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'vendor_name' => 'Vendor ' . $i,
                    'amount' => rand(200000, 3000000),
                    'paid_amount' => rand(0, 3000000),
                    'bill_date' => $now->subDays(rand(1, 30)),
                    'due_date' => $now->addDays(rand(1, 30)),
                    'status' => ['unpaid','partial','paid'][rand(0,2)],
                ]);
            }
        }

        // ═══ BANK ACCOUNTS ═══
        $banks = [
            ['n'=>'CRDB Bank - TZS','b'=>'CRDB Bank','c'=>'TZS'],
            ['n'=>'NMB Bank - USD','b'=>'NMB Bank','c'=>'USD'],
            ['n'=>'NBC Bank - Operations','b'=>'NBC Bank','c'=>'TZS'],
            ['n'=>'Stanbic Bank','b'=>'Stanbic Bank','c'=>'TZS'],
            ['n'=>'Equity Bank TZS','b'=>'Equity Bank','c'=>'TZS'],
        ];
        foreach ($banks as $i => $bk) {
            BankAccount::create([
                'company_id' => $companies->get($i % $companies->count())->id,
                'account_name' => $bk['n'],
                'account_number' => '01' . str_pad((string)rand(10000000, 99999999), 10, '0', STR_PAD_LEFT),
                'bank_name' => $bk['b'],
                'currency' => $bk['c'],
                'opening_balance' => rand(10000000, 100000000),
                'current_balance' => rand(50000000, 500000000),
                'is_active' => true,
            ]);
        }

        // ═══ HR RECORDS ═══
        foreach ($employees as $i => $emp) {
            if ($i % 3 == 0) {
                Leave::create([
                    'employee_id' => $emp->id,
                    'leave_type' => ['annual','sick','personal'][rand(0,2)],
                    'start_date' => $now->subDays(rand(1, 30)),
                    'end_date' => $now->subDays(rand(1, 30))->addDays(rand(1, 5)),
                    'days' => rand(1, 5),
                    'reason' => 'Personal reasons',
                    'status' => ['pending','approved','rejected'][rand(0,2)],
                ]);
            }

            if ($i % 2 == 0) {
                Payroll::create([
                    'employee_id' => $emp->id,
                    'payroll_number' => 'PAY-' . $now->format('Ym') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'month' => $now->format('F'),
                    'year' => $now->year,
                    'basic_salary' => $emp->salary,
                    'allowances' => rand(100000, 500000),
                    'deductions' => rand(50000, 300000),
                    'net_salary' => $emp->salary + rand(100000, 500000) - rand(50000, 300000),
                    'status' => 'paid',
                    'created_by' => $admin->id,
                ]);
            }

            PerformanceReview::create([
                'employee_id' => $emp->id,
                'review_period' => 'Q' . rand(1, 4) . ' ' . $now->year,
                'goals' => 'Achieve departmental targets',
                'achievements' => 'Exceeded expectations',
                'feedback' => 'Good performance overall',
                'rating' => rand(3, 5),
                'reviewer_id' => $users[array_rand($users)]->id,
            ]);
        }

        foreach (['Project Management Professional','Cisco CCNA','ITIL Foundation'] as $tn) {
            Training::create([
                'title' => $tn,
                'description' => 'Professional training course',
                'trainer' => 'External Trainer',
                'start_date' => $now->subMonths(rand(1, 3)),
                'end_date' => $now->subMonths(rand(1, 3))->addDays(rand(2, 10)),
                'status' => 'completed',
            ]);
        }

        $jobTitles = ['Senior Network Engineer','Project Manager','Sales Executive'];
        $jobDepts = ['Technical','Projects','Sales'];
        foreach ($jobTitles as $i => $jt) {
            JobPosting::create([
                'company_id' => $companies->random()->id,
                'title' => $jt,
                'department' => $jobDepts[$i],
                'description' => 'Looking for an experienced professional...',
                'requirements' => "3+ years experience\nDegree in relevant field",
                'location' => 'Dar es Salaam',
                'job_type' => 'full_time',
                'vacancies' => 2,
                'deadline' => $now->addDays(30),
                'status' => 'open',
            ]);
        }

        HrEvent::create(['title'=>'Annual Staff Meeting','description'=>'Yearly staff meeting','event_date'=>$now->addDays(30),'location'=>'Tropical Center, Dar es Salaam','type'=>'company']);
        Policy::create(['title'=>'Employee Code of Conduct','category'=>'HR','content'=>'All employees must adhere to the code of conduct...','is_active'=>true]);

        // ═══ HELPDESK ═══
        $catNames2 = ['Technical Support','Network Issue','Hardware Failure','Software Issue','Account Request','General Inquiry'];
        foreach ($catNames2 as $cn) HelpdeskCategory::create(['name' => $cn]);
        $ticketCats = HelpdeskCategory::all();

        for ($i = 1; $i <= 12; $i++) {
            HelpdeskTicket::create([
                'company_id' => $companies->random()->id,
                'ticket_number' => 'TKT-' . $now->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'title' => ['Internet connectivity','Printer not working','Email setup','Software install','Access request','VPN issue','Server alert','Password reset','System error','Network slow','Account issue','Hardware fault'][$i-1],
                'description' => 'Description of the issue...',
                'category_id' => $ticketCats->random()->id,
                'priority' => ['low','medium','high','critical'][rand(0,3)],
                'status' => ['open','in_progress','resolved','closed'][rand(0,3)],
                'assigned_to' => $users[array_rand($users)]->id,
                'created_by' => $admin->id,
            ]);
        }

        // ═══ FIXED ASSETS ═══
        $assetTypes = ['Laptop','Desktop','Server','Network Switch','Printer','UPS','Projector','Furniture'];
        foreach ($assetTypes as $at) {
            FixedAsset::create([
                'company_id' => $companies->random()->id,
                'asset_code' => 'AST-' . strtoupper(Str::random(6)),
                'name' => $at,
                'type' => $at,
                'purchase_date' => $now->subMonths(rand(3, 36)),
                'purchase_cost' => rand(500000, 5000000),
                'current_value' => rand(200000, 3000000),
                'salvage_value' => rand(50000, 500000),
                'useful_life_years' => rand(3, 10),
                'depreciation_method' => 'straight_line',
                'status' => ['active','under_maintenance','disposed'][rand(0,2)],
                'location' => 'Dar es Salaam',
                'assigned_to' => $employees[array_rand($employees)]->id,
            ]);
        }

        // ═══ FLEET ═══
        $vehData = [
            ['b'=>'Toyota','m'=>'Hilux','p'=>'T 123 ABC','y'=>2021,'f'=>'diesel'],
            ['b'=>'Toyota','m'=>'Land Cruiser','p'=>'T 456 DEF','y'=>2022,'f'=>'diesel'],
            ['b'=>'Isuzu','m'=>'D-Max','p'=>'T 789 GHI','y'=>2020,'f'=>'diesel'],
            ['b'=>'Nissan','m'=>'Navara','p'=>'T 321 JKL','y'=>2023,'f'=>'diesel'],
            ['b'=>'Suzuki','m'=>'Swift','p'=>'T 654 MNO','y'=>2022,'f'=>'petrol'],
        ];
        foreach ($vehData as $vd) {
            $v = Vehicle::create([
                'company_id' => $companies->random()->id,
                'brand' => $vd['b'],
                'model' => $vd['m'],
                'plate_number' => $vd['p'],
                'year' => $vd['y'],
                'fuel_type' => $vd['f'],
                'status' => 'active',
                'assigned_driver' => $employees[array_rand($employees)]->full_name,
                'insurance_expiry' => $now->addMonths(rand(1, 11)),
                'mileage' => rand(5000, 80000),
            ]);

            VehicleMaintenance::create([
                'vehicle_id' => $v->id,
                'description' => 'Regular service',
                'maintenance_date' => $now->subDays(rand(5, 60)),
                'cost' => rand(200000, 1500000),
                'vendor' => 'Auto Service Center',
                'status' => 'completed',
            ]);

            FuelLog::create([
                'vehicle_id' => $v->id,
                'fuel_date' => $now->subDays(rand(1, 14)),
                'liters' => rand(20, 80),
                'cost_per_liter' => 2950,
                'total_cost' => rand(59000, 236000),
                'driver_name' => $employees[array_rand($employees)]->full_name,
            ]);
        }

        // ═══ DOCUMENTS ═══
        $docTypes = ['Contract','Invoice','Report','Proposal','Policy'];
        for ($i = 1; $i <= 8; $i++) {
            Document::create([
                'company_id' => $companies->random()->id,
                'title' => $docTypes[array_rand($docTypes)] . ' - Document ' . $i,
                'description' => 'Description for document ' . $i,
                'type' => $docTypes[array_rand($docTypes)],
                'status' => 'active',
                'file_path' => '/documents/sample-' . $i . '.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => rand(100000, 5000000),
                'version' => 1,
                'created_by' => $admin->id,
            ]);
        }

        // ═══ CALL LOGS ═══
        for ($i = 1; $i <= 15; $i++) {
            CallLog::create([
                'company_id' => $companies->random()->id,
                'caller_name' => 'Caller ' . $i,
                'caller_phone' => '2557' . str_pad((string)rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'direction' => ['inbound','outbound'][rand(0,1)],
                'duration_seconds' => rand(30, 1800),
                'status' => ['completed','missed','in_progress'][rand(0,2)],
                'notes' => 'Call log ' . $i,
                'handled_by' => $users[array_rand($users)]->id,
            ]);
        }

        // ═══ SETTINGS & AUDIT ═══
        Setting::set('app_name', 'ASYX Group ERP');
        Setting::set('currency', 'TZS');
        Setting::set('timezone', 'Africa/Dar_es_Salaam');
        Setting::set('fiscal_year_start', '2025-01-01');

        AuditLog::create([
            'user_id' => $admin->id,
            'company_id' => $group?->id,
            'action' => 'system_seeded',
            'entity_type' => 'System',
            'entity_id' => null,
            'new_values' => json_encode(['message' => 'Master data seeded successfully']),
            'ip_address' => '127.0.0.1',
        ]);
    }
}
