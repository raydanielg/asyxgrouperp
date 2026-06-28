<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\CrmContact;
use App\Models\CrmDeal;
use App\Models\CrmLead;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\HelpdeskCategory;
use App\Models\HelpdeskTicket;
use App\Models\Leave;
use App\Models\OfficeExpense;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Revenue;
use App\Models\Supplier;
use App\Models\Timesheet;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Payroll;
use App\Models\PerformanceReview;
use App\Models\Training;
use App\Models\JobPosting;
use App\Models\Policy;
use App\Models\HrEvent;
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
use App\Models\ClientReceipt;
use App\Models\FixedAsset;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Models\FuelLog;
use App\Models\Document;
use App\Models\CallLog;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\SalesProposal;
use App\Models\SalesProposalItem;
use App\Models\CrmContract;
use App\Models\ProjectBudget;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Master Data for ASYX Group ERP...');

        $companies = \App\Models\Company::where('is_group', false)->get();
        $group = \App\Models\Company::where('is_group', true)->first();
        $admin = User::where('email', 'admin@djanproject.com')->first();

        $employees = [];
        $users = [];
        $leads = [];
        $deals = [];
        $projects = [];
        $products = [];
        $warehouses = [];
        $suppliers = [];

        $now = now();

        $this->command->info('Creating employees and users...');
        $employeeData = [
            ['first_name'=>'John','last_name'=>'Mushi','email'=>'john.mushi@asyxgroup.co.tz','phone'=>'255712000001','position'=>'Managing Director','department'=>'Executive','salary'=>8500000],
            ['first_name'=>'Sarah','last_name='Mkono','email'=>'sarah.mkono@asyxgroup.co.tz','phone'=>'255712000002','position'=>'Finance Manager','department'=>'Finance','salary'=>5500000],
            ['first_name'=>'Peter','last_name'=>'Kavuma','email'=>'peter.kavuma@parktech.co.tz','phone'=>'255712000003','position'=>'Technical Manager','department'=>'Technical','salary'=>4800000],
            ['first_name'=>'Grace','last_name'=>'Mwangi','email'=>'grace.mwangi@asyxgroup.co.tz','phone'=>'255712000004','position'=>'HR Officer','department'=>'Human Resources','salary'=>3200000],
            ['first_name'=>'David','last_name'=>'Ochieng','email'=>'david.ochieng@motisha.co.tz','phone'=>'255712000005','position'=>'Project Manager','department'=>'Projects','salary'=>5200000],
            ['first_name'=>'Mary','last_name'=>'Ndung\'u','email'=>'mary.ndungu@terkmark.co.tz','phone'=>'255712000006','position'=>'Accountant','department'=>'Finance','salary'=>2800000],
            ['first_name'=>'Joseph','last_name'=>'Mbwana','email'=>'joseph.mbwana@glovin.co.tz','phone'=>'255712000007','position'=>'Sales Executive','department'=>'Sales','salary'=>2500000],
            ['first_name'=>'Anna','last_name'=>'Kipingu','email'=>'anna.kipingu@asyxgroup.co.tz','phone'=>'255712000008','position'=>'Support Engineer','department'=>'Technical','salary'=>2200000],
            ['first_name'=>'Robert','last_name'=>'Kato','email'=>'robert.kato@parktech.co.tz','phone'=>'255712000009','position'=>'Technician','department'=>'Technical','salary'=>1800000],
            ['first_name'=>'Elizabeth','last_name'=>'Sanga','email'=>'elizabeth.sanga@asyxgroup.co.tz','phone'=>'255712000010','position'=>'Receptionist','department'=>'Admin','salary'=>1500000],
            ['first_name'=>'Michael','last_name'=>'Nkya','email'=>'michael.nkya@motisha.co.tz','phone'=>'255712000011','position'=>'Call Center Agent','department'=>'Customer Support','salary'=>1600000],
            ['first_name'=>'Catherine','last_name'=>'Lema','email'=>'catherine.lema@terkmark.co.tz','phone'=>'255712000012','position'=>'Procurement Officer','department'=>'Procurement','salary'=>2600000],
            ['first_name'=>'Samuel','last_name'=>'Shayo','email'=>'samuel.shayo@glovin.co.tz','phone'=>'255712000013','position'=>'Logistics Officer','department'=>'Logistics','salary'=>2100000],
            ['first_name'=>'Esther','last_name'=>'Mlay','email'=>'esther.mlay@asyxgroup.co.tz','phone'=>'255712000014','position'=>'Payroll Officer','department'=>'Finance','salary'=>2400000],
            ['first_name'=>'Patrick','last_name'=>'Kimaro','email'=>'patrick.kimaro@parktech.co.tz','phone'=>'255712000015','position'=>'Systems Engineer','department'=>'Technical','salary'=>3500000],
        ];

        foreach ($employeeData as $i => $ed) {
            $company = $companies->get($i % $companies->count());

            $employee = Employee::create([
                'company_id' => $company->id,
                'first_name' => $ed['first_name'],
                'last_name' => $ed['last_name'],
                'email' => $ed['email'],
                'phone' => $ed['phone'],
                'position' => $ed['position'],
                'department' => $ed['department'],
                'salary' => $ed['salary'],
                'status' => 'active',
                'joining_date' => $now->subYears(rand(1, 5))->subDays(rand(0, 365)),
                'gender' => $i % 2 == 0 ? 'male' : 'female',
            ]);

            $user = User::create([
                'company_id' => $company->id,
                'employee_id' => $employee->id,
                'name' => $ed['first_name'] . ' ' . $ed['last_name'],
                'first_name' => $ed['first_name'],
                'last_name' => $ed['last_name'],
                'email' => $ed['email'],
                'phone' => $ed['phone'],
                'password' => Hash::make('password123'),
                'email_verified_at' => $now,
                'role' => 'user',
            ]);

            $employees[] = $employee;
            $users[] = $user;

            Attendance::create([
                'employee_id' => $employee->id,
                'company_id' => $company->id,
                'date' => $now->format('Y-m-d'),
                'clock_in_at' => $now->copy()->setHour(8)->setMinute(rand(0, 30)),
                'status' => 'present',
                'type' => 'office',
            ]);

            for ($d = 1; $d <= 5; $d++) {
                $date = $now->copy()->subDays($d);
                Attendance::create([
                    'employee_id' => $employee->id,
                    'company_id' => $company->id,
                    'date' => $date->format('Y-m-d'),
                    'clock_in_at' => $date->setHour(8)->setMinute(rand(0, 45)),
                    'clock_out_at' => $date->setHour(17)->setMinute(rand(0, 30)),
                    'status' => rand(0, 10) > 2 ? 'present' : 'absent',
                    'type' => 'office',
                ]);
            }
        }

        $this->command->info('Creating departments, categories and warehouses...');
        $warehouseNames = [
            ['name'=>'Main Warehouse - Dar','code'=>'WH-DAR'],
            ['name'=>'Parktech Warehouse','code'=>'WH-PTK'],
            ['name'=>'Motisha Storage','code'=>'WH-MTS'],
            ['name'=>'Terkmark Warehouse','code'=>'WH-TRM'],
            ['name'=>'Glovin Inventory','code'=>'WH-GLV'],
        ];
        foreach ($warehouseNames as $i => $wd) {
            $warehouses[] = Warehouse::create([
                'company_id' => $companies->get($i % $companies->count())->id,
                'name' => $wd['name'],
                'code' => $wd['code'],
                'address' => 'Tropical Center, Dar es Salaam',
                'is_active' => true,
            ]);
        }

        $catNames = ['ICT Equipment', 'Software', 'Networking', 'Office Supplies', 'Furniture', 'Security Systems', 'Cabling'];
        foreach ($catNames as $cn) {
            ProductCategory::create(['name' => $cn, 'description' => "$cn category"]);
        }
        $categories = ProductCategory::all();

        $this->command->info('Creating products...');
        $productData = [
            ['name'=>'HP ProBook 450 G10','sku'=>'HP-PB450','category'=>'ICT Equipment','price'=>3200000,'cost'=>2500000,'stock'=>45],
            ['name'=>'Dell Latitude 5540','sku'=>'DELL-LAT5540','category'=>'ICT Equipment','price'=>3800000,'cost'=>2900000,'stock'=>30],
            ['name'=>'Cisco Catalyst 9200 Switch','sku'=>'CIS-CAT9200','category'=>'Networking','price'=>8500000,'cost'=>6200000,'stock'=>15],
            ['name'=>'Ubiquiti UniFi AP Pro','sku'=>'UBI-UAP-PRO','category'=>'Networking','price'=>850000,'cost'=>550000,'stock'=>120],
            ['name'=>'Microsoft 365 Business Premium','sku'=>'MS-365-BP','category'=>'Software','price'=>35000,'cost'=>25000,'stock'=>999],
            ['name'=>'Windows Server 2022 Standard','sku'=>'MS-WS2022','category'=>'Software','price'=>1200000,'cost'=>850000,'stock'=>50],
            ['name'=>'CAT6 UTP Cable 305m','sku'=>'CBL-CAT6-305','category'=>'Cabling','price'=>450000,'cost'=>320000,'stock'=>200],
            ['name'=>'APC Smart-UPS 1500VA','sku'=>'APC-SU1500','category'=>'ICT Equipment','price'=>2100000,'cost'=>1500000,'stock'=>25],
            ['name'=>'Office Desk 120x60cm','sku'=>'FUR-DESK120','category'=>'Furniture','price'=>450000,'cost'=>280000,'stock'=>60],
            ['name'=>'Ergonomic Office Chair','sku'=>'FUR-CHAIR-ERG','category'=>'Furniture','price'=>650000,'cost'=>400000,'stock'=>40],
            ['name'=>'Hikvision 4MP IP Camera','sku'=>'HIK-4MP-IPC','category'=>'Security Systems','price'=>350000,'cost'=>220000,'stock'=>150],
            ['name'=>'Hikvision NVR 16CH','sku'=>'HIK-NVR16','category'=>'Security Systems','price'=>2800000,'cost'=>1900000,'stock'=>20],
            ['name'=>'Cisco ISR 4321 Router','sku'=>'CIS-ISR4321','category'=>'Networking','price'=>12500000,'cost'=>9200000,'stock'=>8],
            ['name'=>'Staples A4 Paper 5000pk','sku'=>'OFF-PAPER-A4','category'=>'Office Supplies','price'=>45000,'cost'=>32000,'stock'=>500],
            ['name'=>'HP LaserJet Pro M404dn','sku'=>'HP-LJ-M404','category'=>'ICT Equipment','price'=>1800000,'cost'=>1200000,'stock'=>18],
        ];
        foreach ($productData as $pd) {
            $cat = $categories->where('name', $pd['category'])->first();
            $prod = Product::create([
                'company_id' => $companies->random()->id,
                'category_id' => $cat ? $cat->id : null,
                'name' => $pd['name'],
                'sku' => $pd['sku'],
                'description' => $pd['name'],
                'unit_price' => $pd['price'],
                'cost_price' => $pd['cost'],
                'stock_quantity' => $pd['stock'],
                'reorder_level' => 10,
                'is_active' => true,
            ]);
            $products[] = $prod;
        }

        $this->command->info('Creating suppliers...');
        $supplierData = [
            ['name'=>'TechMart Tanzania Ltd','contact'=>'255712100001','email'=>'info@techmart.co.tz'],
            ['name'=>'CompSys East Africa','contact'=>'255712100002','email'=>'sales@compsys.co.tz'],
            ['name'=>'NetConnect Solutions','contact'=>'255712100003','email'=>'info@netconnect.co.tz'],
            ['name'=>'OfficePro Supplies Ltd','contact'=>'255712100004','email'=>'orders@officepro.co.tz'],
            ['name'=>'SecureTech Systems Ltd','contact'=>'255712100005','email'=>'info@securetech.co.tz'],
        ];
        foreach ($supplierData as $sd) {
            $suppliers[] = Supplier::create([
                'company_id' => $companies->random()->id,
                'name' => $sd['name'],
                'contact_person' => $sd['name'],
                'phone' => $sd['contact'],
                'email' => $sd['email'],
                'address' => 'Dar es Salaam, Tanzania',
                'status' => 'active',
            ]);
        }

        $this->command->info('Creating tenders...');
        for ($i = 1; $i <= 8; $i++) {
            Tender::create([
                'company_id' => $companies->random()->id,
                'tender_number' => 'TND-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'title' => ['Supply of ICT Equipment - Phase ' . $i, 'Network Infrastructure Upgrade', 'CCTV Installation - Bank Branch', 'Office Furniture Supply', 'Software License Renewal', 'Data Center Migration', 'Helpdesk Outsourcing', 'Managed Services Contract'][$i-1],
                'client_name' => 'Client ' . chr(64 + $i),
                'client_organization' => ['CRDB Bank', 'NMB Bank', 'Tanzania Ports Authority', 'TPA', 'Vodacom TZ', 'Airtel TZ', 'TANESCO', 'DIT'][$i-1],
                'description' => 'Tender description for project ' . $i,
                'estimated_value' => rand(20000000, 500000000),
                'submission_date' => $now->subDays(rand(10, 60)),
                'closing_date' => $now->addDays(rand(5, 30)),
                'status' => ['received', 'under_review', 'submitted', 'won', 'lost', 'converted', 'received', 'under_review'][$i-1],
                'created_by' => $admin->id,
                'assigned_to' => $users[array_rand($users)]->id,
            ]);
        }

        $this->command->info('Creating CRM leads...');
        $leadNames = [
            ['first'=>'James','last'=>'Mwakalobo','company'=>'CRDB Bank Plc','email'=>'james@crdb.co.tz','phone'=>'255713000001'],
            ['first'=>'Mercy','last'=>'Kessy','company'=>'NMB Bank Plc','email'=>'mercy@nmb.co.tz','phone'=>'255713000002'],
            ['first'=>'Hassan','last'=>'Said','company'=>'Vodacom TZ','email'=>'hassan@vodacom.co.tz','phone'=>'255713000003'],
            ['first'=>'Amina','last'=>'Juma','company'=>'TANESCO','email'=>'amina@tanesco.co.tz','phone'=>'255713000004'],
            ['first'=>'Charles','last'=>'Massawe','company'=>'Tanzania Ports','email'=>'charles@ports.go.tz','phone'=>'255713000005'],
            ['first'=>'Diana','last'=>'Mwaikambo','company'=>'DIT','email'=>'diana@dit.ac.tz','phone'=>'255713000006'],
            ['first'=>'Emmanuel','last'=>'Nchimbi','company'=>'Airtel TZ','email'=>'emmanuel@airtel.co.tz','phone'=>'255713000007'],
            ['first'=>'Fatma','last'=>'Salim','company'=>'TPA','email'=>'fatma@tpa.go.tz','phone'=>'255713000008'],
        ];
        foreach ($leadNames as $i => $ld) {
            $lead = CrmLead::create([
                'company_id' => $companies->random()->id,
                'lead_number' => 'LEAD-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'first_name' => $ld['first'],
                'last_name' => $ld['last'],
                'email' => $ld['email'],
                'phone' => $ld['phone'],
                'company' => $ld['company'],
                'source' => ['Website','Referral','Tender','Call Center','Trade Show'][rand(0,4)],
                'status' => $i < 4 ? 'converted' : 'qualified',
                'notes' => 'Lead from ' . $ld['company'],
                'assigned_to' => $users[array_rand($users)]->id,
                'created_by' => $admin->id,
            ]);
            $leads[] = $lead;

            CrmContact::create([
                'company_id' => $companies->random()->id,
                'lead_id' => $lead->id,
                'first_name' => $ld['first'],
                'last_name' => $ld['last'],
                'email' => $ld['email'],
                'phone' => $ld['phone'],
                'position' => 'Manager',
                'is_primary' => true,
            ]);
        }

        $this->command->info('Creating deals and contracts...');
        $dealValues = [150000000, 85000000, 45000000, 250000000, 120000000, 95000000, 32000000, 78000000];
        foreach ($leads as $i => $lead) {
            if ($lead->status !== 'converted') continue;

            $deal = CrmDeal::create([
                'company_id' => $lead->company_id,
                'lead_id' => $lead->id,
                'title' => 'Deal with ' . $lead->company . ' - ' . ['ICT Supply','Network Project','CCTV Installation','Managed Services','Software Solution','Infrastructure','Maintenance Contract','Consultancy'][$i],
                'value' => $dealValues[$i],
                'currency' => 'TZS',
                'stage' => $i < 3 ? 'closed_won' : 'negotiation',
                'status' => $i < 3 ? 'won' : 'open',
                'expected_close_date' => $now->addDays(rand(15, 60)),
                'assigned_to' => $users[array_rand($users)]->id,
                'created_by' => $admin->id,
            ]);
            $deals[] = $deal;

            if ($i < 3) {
                CrmContract::create([
                    'company_id' => $deal->company_id,
                    'deal_id' => $deal->id,
                    'contract_number' => 'CTR-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                    'title' => 'Service Contract - ' . $lead->company,
                    'value' => $dealValues[$i],
                    'start_date' => $now,
                    'end_date' => $now->addYear(),
                    'status' => 'active',
                    'sla' => 'Standard SLA with 4hr response',
                    'created_by' => $admin->id,
                ]);
            }
        }

        $this->command->info('Creating projects...');
        $projectData = [
            ['name'=>'CRDB ICT Equipment Supply','budget'=>150000000,'status'=>'in_progress'],
            ['name'=>'NMB Network Upgrade Phase 2','budget'=>85000000,'status'=>'in_progress'],
            ['name'=>'Vodacom CCTV Installation','budget'=>45000000,'status'=>'completed'],
            ['name'=>'TPA Data Center Migration','budget'=>250000000,'status'=>'planning'],
            ['name'=>'DIT Campus Network','budget'=>120000000,'status'=>'in_progress'],
            ['name'=>'TANESCO Managed Services','budget'=>95000000,'status'=>'in_progress'],
        ];
        $statuses = ['planning','in_progress','on_hold','completed','cancelled'];
        foreach ($projectData as $i => $pd) {
            $project = Project::create([
                'company_id' => $companies->random()->id,
                'deal_id' => $deals[$i] ?? null,
                'name' => $pd['name'],
                'description' => 'Project: ' . $pd['name'],
                'budget' => $pd['budget'],
                'start_date' => $now->subDays(rand(15, 90)),
                'due_date' => $now->addDays(rand(30, 180)),
                'status' => $pd['status'],
                'priority' => ['high','medium','low'][rand(0,2)],
                'manager_id' => $users[array_rand($users)]->id,
            ]);
            $projects[] = $project;

            ProjectBudget::create([
                'company_id' => $project->company_id,
                'project_id' => $project->id,
                'category' => 'Equipment',
                'amount' => $pd['budget'] * 0.6,
                'spent' => $pd['budget'] * 0.3 * rand(1, 9) / 10,
            ]);
            ProjectBudget::create([
                'company_id' => $project->company_id,
                'project_id' => $project->id,
                'category' => 'Labor',
                'amount' => $pd['budget'] * 0.25,
                'spent' => $pd['budget'] * 0.1 * rand(1, 9) / 10,
            ]);
            ProjectBudget::create([
                'company_id' => $project->company_id,
                'project_id' => $project->id,
                'category' => 'Miscellaneous',
                'amount' => $pd['budget'] * 0.15,
                'spent' => $pd['budget'] * 0.05 * rand(1, 9) / 10,
            ]);

            $taskNames = ['Site Survey', 'Requirements Gathering', 'Design & Planning', 'Procurement', 'Installation', 'Testing', 'Commissioning', 'Handover', 'Training', 'Documentation'];
            foreach (array_rand(array_flip($taskNames), 4) as $tn) {
                ProjectTask::create([
                    'project_id' => $project->id,
                    'title' => $taskNames[$tn],
                    'description' => 'Task: ' . $taskNames[$tn] . ' for ' . $project->name,
                    'assigned_to' => $users[array_rand($users)]->id,
                    'due_date' => $now->addDays(rand(5, 90)),
                    'priority' => ['high','medium','low'][rand(0,2)],
                    'status' => ['pending','in_progress','completed'][rand(0,2)],
                ]);
            }

            for ($d = 0; $d < 5; $d++) {
                Timesheet::create([
                    'employee_id' => $employees[array_rand($employees)]->id,
                    'project_id' => $project->id,
                    'company_id' => $project->company_id,
                    'date' => $now->copy()->subDays($d),
                    'hours' => rand(4, 10),
                    'description' => 'Work on ' . $project->name,
                ]);
            }
        }

        $this->command->info('Creating invoices (sales & purchase)...');
        $statusOpts = ['draft','posted','paid','overdue','partial'];
        for ($i = 1; $i <= 15; $i++) {
            $status = $statusOpts[array_rand($statusOpts)];
            $total = rand(500000, 50000000);
            $paid = $status === 'paid' ? $total : ($status === 'partial' ? $total * rand(1, 9) / 10 : 0);
            $inv = SalesInvoice::create([
                'company_id' => $companies->random()->id,
                'invoice_number' => 'INV-' . $now->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_name' => 'Customer ' . chr(64 + $i),
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

            if ($i <= 12) {
                $pStatus = $statusOpts[array_rand($statusOpts)];
                $pTotal = rand(300000, 30000000);
                $pPaid = $pStatus === 'paid' ? $pTotal : ($pStatus === 'partial' ? $pTotal * rand(1, 9) / 10 : 0);
                $pInv = PurchaseInvoice::create([
                    'company_id' => $companies->random()->id,
                    'invoice_number' => 'PINV-' . $now->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'vendor_name' => 'Vendor ' . chr(64 + $i),
                    'vendor_email' => 'vendor' . $i . '@example.com',
                    'invoice_date' => $now->subDays(rand(1, 45)),
                    'due_date' => $now->addDays(rand(1, 30)),
                    'total_amount' => $pTotal,
                    'paid_amount' => $pPaid,
                    'balance_amount' => $pTotal - $pPaid,
                    'status' => $pStatus,
                    'created_by' => $admin->id,
                ]);
                PurchaseInvoiceItem::create([
                    'purchase_invoice_id' => $pInv->id,
                    'description' => 'Item for purchase ' . $pInv->invoice_number,
                    'quantity' => rand(1, 5),
                    'unit_price' => $pTotal / rand(1, 5),
                    'total' => $pTotal,
                ]);
            }
        }

        $this->command->info('Creating proposals...');
        for ($i = 1; $i <= 10; $i++) {
            $total = rand(1000000, 30000000);
            $prop = SalesProposal::create([
                'company_id' => $companies->random()->id,
                'proposal_number' => 'PRO-' . $now->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'customer_name' => 'Prospect ' . chr(64 + $i),
                'customer_email' => 'prospect' . $i . '@example.com',
                'proposal_date' => $now->subDays(rand(1, 30)),
                'valid_until' => $now->addDays(rand(7, 30)),
                'subtotal' => $total,
                'total' => $total * 1.18,
                'status' => ['draft','sent','accepted','rejected','negotiation'][rand(0,4)],
                'created_by' => $admin->id,
            ]);
            SalesProposalItem::create([
                'sales_proposal_id' => $prop->id,
                'description' => 'Service item for proposal',
                'quantity' => 1,
                'unit_price' => $total,
                'total' => $total,
            ]);
        }

        $this->command->info('Creating procurement documents...');
        foreach ($projects as $i => $project) {
            if ($i >= 4) continue;
            $supplier = $suppliers[array_rand($suppliers)];
            $lpo = Lpo::create([
                'company_id' => $project->company_id,
                'project_id' => $project->id,
                'supplier_id' => $supplier->id,
                'lpo_number' => 'LPO-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'total' => rand(5000000, 50000000),
                'status' => ['draft','approved','ordered','received','cancelled'][rand(0,3)],
                'created_by' => $admin->id,
            ]);
            LpoItem::create([
                'lpo_id' => $lpo->id,
                'description' => 'Equipment for ' . $project->name,
                'quantity' => rand(2, 20),
                'unit_price' => $lpo->total / rand(2, 20),
                'total' => $lpo->total,
            ]);

            if (rand(0, 1)) {
                $grn = Grn::create([
                    'company_id' => $project->company_id,
                    'lpo_id' => $lpo->id,
                    'supplier_id' => $supplier->id,
                    'grn_number' => 'GRN-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                    'received_date' => $now->subDays(rand(1, 14)),
                    'status' => 'received',
                    'notes' => 'Goods received in good condition',
                    'created_by' => $admin->id,
                ]);
                GrnItem::create([
                    'grn_id' => $grn->id,
                    'description' => 'Received items',
                    'quantity_ordered' => 10,
                    'quantity_received' => 10,
                    'unit_price' => 500000,
                ]);
            }

            DeliveryNote::create([
                'company_id' => $project->company_id,
                'project_id' => $project->id,
                'delivery_note_number' => 'DN-' . $now->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'customer_name' => 'Customer for ' . $project->name,
                'delivery_date' => $now->subDays(rand(1, 10)),
                'total' => rand(1000000, 10000000),
                'status' => 'delivered',
                'created_by' => $admin->id,
            ]);
        }

        $this->command->info('Creating financial records...');
        for ($i = 1; $i <= 20; $i++) {
            Expense::create([
                'company_id' => $companies->random()->id,
                'description' => ['Office Supplies', 'Travel Expense', 'Utility Bill', 'Internet & Comms', 'Maintenance', 'Consulting Fees', 'Training Costs', 'Vehicle Fuel'][rand(0,7)],
                'amount' => rand(50000, 5000000),
                'expense_date' => $now->subDays(rand(0, 60)),
                'category' => ['Operations','Travel','Utilities','Maintenance','Consulting'][rand(0,4)],
                'status' => 'approved',
                'created_by' => $admin->id,
            ]);

            Revenue::create([
                'company_id' => $companies->random()->id,
                'description' => ['Service Fee', 'Product Sale', 'Consultancy Income', 'Maintenance Income', 'Managed Services'][rand(0,4)],
                'amount' => rand(500000, 10000000),
                'revenue_date' => $now->subDays(rand(0, 60)),
                'category' => ['Services','Products','Consulting','Maintenance'][rand(0,3)],
                'created_by' => $admin->id,
            ]);

            if ($i <= 10) {
                Bill::create([
                    'company_id' => $companies->random()->id,
                    'bill_number' => 'BILL-' . $now->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'vendor_name' => 'Vendor ' . chr(64 + $i),
                    'amount' => rand(200000, 3000000),
                    'paid_amount' => rand(0, 3000000),
                    'bill_date' => $now->subDays(rand(1, 30)),
                    'due_date' => $now->addDays(rand(1, 30)),
                    'status' => ['unpaid','partial','paid'][rand(0,2)],
                ]);

                OfficeExpense::create([
                    'company_id' => $companies->random()->id,
                    'project_id' => $projects->count() > 0 ? $projects->random()->id : null,
                    'description' => 'Office expense ' . $i,
                    'amount' => rand(100000, 2000000),
                    'expense_date' => $now->subDays(rand(1, 15)),
                    'status' => ['pending','approved','rejected'][rand(0,2)],
                    'created_by' => $admin->id,
                ]);

                ClientReceipt::create([
                    'company_id' => $companies->random()->id,
                    'receipt_number' => 'RCP-' . $now->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'customer_name' => 'Customer ' . chr(64 + $i),
                    'amount' => rand(500000, 10000000),
                    'payment_date' => $now->subDays(rand(0, 20)),
                    'payment_method' => ['cash','bank','mobile'][rand(0,2)],
                    'notes' => 'Payment received',
                    'created_by' => $admin->id,
                ]);
            }
        }

        $this->command->info('Creating bank accounts...');
        $bankNames = ['CRDB Bank - TZS Account', 'NMB Bank - USD Account', 'NBC Bank - Operations', 'Stanbic Bank - TZS', 'Equity Bank'];
        foreach ($bankNames as $i => $bn) {
            BankAccount::create([
                'company_id' => $companies->get($i % $companies->count())->id,
                'account_name' => $bn,
                'account_number' => '01' . str_pad((string)rand(10000000, 99999999), 10, '0', STR_PAD_LEFT),
                'bank_name' => explode(' - ', $bn)[0],
                'balance' => rand(50000000, 500000000),
                'currency' => $i === 1 ? 'USD' : 'TZS',
                'is_active' => true,
            ]);
        }

        $this->command->info('Creating HR records...');
        foreach ($employees as $i => $emp) {
            if ($i % 3 == 0 && $i > 0) {
                Leave::create([
                    'company_id' => $emp->company_id,
                    'employee_id' => $emp->id,
                    'type' => ['annual','sick','personal'][rand(0,2)],
                    'start_date' => $now->subDays(rand(1, 30)),
                    'end_date' => $now->subDays(rand(1, 30))->addDays(rand(1, 5)),
                    'reason' => 'Personal reasons',
                    'status' => ['pending','approved','rejected'][rand(0,2)],
                    'applied_on' => $now->subDays(rand(5, 30)),
                ]);
            }

            if ($i % 2 == 0) {
                Payroll::create([
                    'company_id' => $emp->company_id,
                    'employee_id' => $emp->id,
                    'basic_salary' => $emp->salary,
                    'allowances' => rand(100000, 500000),
                    'deductions' => rand(50000, 300000),
                    'net_pay' => $emp->salary + rand(100000, 500000) - rand(50000, 300000),
                    'pay_period' => $now->format('Y-m'),
                    'payment_date' => $now->copy()->startOfMonth()->addDays(rand(1, 5)),
                    'status' => 'paid',
                ]);
            }

            PerformanceReview::create([
                'company_id' => $emp->company_id,
                'employee_id' => $emp->id,
                'review_date' => $now->subMonths(rand(1, 6)),
                'rating' => rand(3, 5),
                'reviewer_id' => $users[array_rand($users)]->id,
                'notes' => 'Good performance overall',
                'status' => 'completed',
            ]);

            if ($i % 4 == 0) {
                Training::create([
                    'company_id' => $emp->company_id,
                    'employee_id' => $emp->id,
                    'title' => ['Project Management Professional','Cisco CCNA','ITIL Foundation','AWS Cloud Practitioner','Cybersecurity Fundamentals'][rand(0,4)],
                    'provider' => ['LBS','Cisco Academy','AXELOS','AWS','CompTIA'][rand(0,4)],
                    'start_date' => $now->subMonths(rand(1, 3)),
                    'end_date' => $now->subMonths(rand(1, 3))->addDays(rand(2, 10)),
                    'cost' => rand(500000, 3000000),
                    'status' => 'completed',
                ]);
            }
        }

        for ($i = 0; $i < 3; $i++) {
            JobPosting::create([
                'company_id' => $companies->random()->id,
                'title' => ['Senior Network Engineer','Project Manager','Sales Executive'][$i],
                'department' => ['Technical','Projects','Sales'][$i],
                'description' => 'We are looking for an experienced professional...',
                'requirements' => '- 3+ years experience\n- Degree in relevant field\n- Strong communication skills',
                'location' => 'Dar es Salaam',
                'type' => 'full_time',
                'status' => 'open',
                'closing_date' => $now->addDays(30),
                'created_by' => $admin->id,
            ]);
        }

        Policy::create([
            'company_id' => $companies->first()->id,
            'title' => 'Employee Code of Conduct',
            'description' => 'Standard code of conduct for all employees',
            'content' => 'All employees must adhere to the company code of conduct...',
            'status' => 'active',
        ]);

        HrEvent::create([
            'company_id' => $companies->first()->id,
            'title' => 'Annual Staff Meeting',
            'description' => 'Yearly staff meeting for all employees',
            'event_date' => $now->addDays(30),
            'location' => 'Tropical Center, Dar es Salaam',
            'type' => 'company',
        ]);

        $this->command->info('Creating helpdesk tickets...');
        $categories = ['Technical Support','Network Issue','Hardware Failure','Software Issue','Account Request','General Inquiry'];
        foreach ($categories as $cn) {
            HelpdeskCategory::create(['name' => $cn]);
        }
        $ticketCategories = HelpdeskCategory::all();

        for ($i = 1; $i <= 15; $i++) {
            HelpdeskTicket::create([
                'company_id' => $companies->random()->id,
                'ticket_number' => 'TKT-' . $now->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'title' => ['Internet connectivity issue','Printer not working','Email account setup','Software installation required','System access request','VPN connection problem','Server alert notification','Password reset request'][rand(0,7)],
                'description' => 'Detailed description of the issue...',
                'category_id' => $ticketCategories->random()->id,
                'priority' => ['low','medium','high','critical'][rand(0,3)],
                'status' => ['open','in_progress','resolved','closed'][rand(0,3)],
                'assigned_to' => $users[array_rand($users)]->id,
                'created_by' => $admin->id,
                'created_at' => $now->subDays(rand(0, 20)),
            ]);
        }

        $this->command->info('Creating fixed assets...');
        $assetTypes = ['Laptop','Desktop','Server','Network Switch','Printer','UPS','Projector','Furniture'];
        foreach ($assetTypes as $at) {
            FixedAsset::create([
                'company_id' => $companies->random()->id,
                'asset_code' => 'AST-' . strtoupper(Str::random(6)),
                'name' => $at . ' - ' . fake()->company(),
                'type' => $at,
                'purchase_date' => $now->subMonths(rand(3, 36)),
                'purchase_cost' => rand(500000, 5000000),
                'current_value' => rand(200000, 3000000),
                'salvage_value' => rand(50000, 500000),
                'useful_life_years' => rand(3, 10),
                'depreciation_method' => 'straight_line',
                'status' => ['active','under_maintenance','disposed'][rand(0,2)],
                'location' => 'Dar es Salaam',
                'assigned_to' => $employees->random()->id,
            ]);
        }

        $this->command->info('Creating fleet records...');
        $vehicles = [];
        $vehicleData = [
            ['brand'=>'Toyota','model'=>'Hilux','plate'=>'T 123 ABC','year'=>2021,'fuel'=>'diesel'],
            ['brand'=>'Toyota','model'=>'Land Cruiser','plate'=>'T 456 DEF','year'=>2022,'fuel'=>'diesel'],
            ['brand'=>'Isuzu','model'=>'D-Max','plate'=>'T 789 GHI','year'=>2020,'fuel'=>'diesel'],
            ['brand'=>'Nissan','model'=>'Navara','plate'=>'T 321 JKL','year'=>2023,'fuel'=>'diesel'],
            ['brand'=>'Suzuki','model'=>'Swift','plate'=>'T 654 MNO','year'=>2022,'fuel'=>'petrol'],
        ];
        foreach ($vehicleData as $vd) {
            $vehicle = Vehicle::create([
                'company_id' => $companies->random()->id,
                'brand' => $vd['brand'],
                'model' => $vd['model'],
                'plate_number' => $vd['plate'],
                'year' => $vd['year'],
                'fuel_type' => $vd['fuel'],
                'status' => 'active',
                'assigned_driver' => $employees->random()->full_name,
                'insurance_expiry' => $now->addMonths(rand(1, 11)),
                'mileage' => rand(5000, 80000),
            ]);
            $vehicles[] = $vehicle;

            VehicleMaintenance::create([
                'vehicle_id' => $vehicle->id,
                'description' => 'Regular service',
                'maintenance_date' => $now->subDays(rand(5, 60)),
                'cost' => rand(200000, 1500000),
                'vendor' => 'Auto Service Center',
                'status' => 'completed',
            ]);

            FuelLog::create([
                'vehicle_id' => $vehicle->id,
                'fuel_date' => $now->subDays(rand(1, 14)),
                'liters' => rand(20, 80),
                'cost_per_liter' => 2950,
                'total_cost' => rand(59000, 236000),
                'driver_name' => $employees->random()->full_name,
            ]);
        }

        $this->command->info('Creating documents...');
        $docTypes = ['Contract','Invoice','Report','Proposal','Policy','Certificate'];
        for ($i = 1; $i <= 10; $i++) {
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

        $this->command->info('Creating call logs...');
        for ($i = 1; $i <= 20; $i++) {
            CallLog::create([
                'company_id' => $companies->random()->id,
                'caller_name' => 'Caller ' . $i,
                'caller_phone' => '2557' . str_pad((string)rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'direction' => ['inbound','outbound'][rand(0,1)],
                'duration_seconds' => rand(30, 1800),
                'status' => ['completed','missed','in_progress'][rand(0,2)],
                'notes' => 'Call notes for log ' . $i,
                'handled_by' => $users[array_rand($users)]->id,
            ]);
        }

        $this->command->info('Creating settings and audit logs...');
        Setting::set('app_name', 'ASYX Group ERP');
        Setting::set('company_tagline', 'Enterprise Resource Planning System');
        Setting::set('currency', 'TZS');
        Setting::set('timezone', 'Africa/Dar_es_Salaam');
        Setting::set('fiscal_year_start', '2025-01-01');

        AuditLog::create([
            'user_id' => $admin->id,
            'company_id' => $group?->id,
            'action' => 'system_seeded',
            'entity_type' => 'System',
            'entity_id' => null,
            'old_values' => null,
            'new_values' => json_encode(['message' => 'Master data seeded successfully']),
            'ip_address' => '127.0.0.1',
        ]);

        $this->command->info('Master data seeding completed successfully!');
        $this->command->info('Created:');
        $this->command->info('  - ' . count($employees) . ' employees');
        $this->command->info('  - ' . count($users) . ' users');
        $this->command->info('  - ' . count($products) . ' products');
        $this->command->info('  - ' . count($suppliers) . ' suppliers');
        $this->command->info('  - ' . count($leads) . ' CRM leads');
        $this->command->info('  - ' . count($deals) . ' CRM deals');
        $this->command->info('  - ' . count($projects) . ' projects');
        $this->command->info('  - 20+ expenses, revenues, and financial records');
        $this->command->info('  - Fleet, assets, documents, and helpdesk tickets');
        $this->command->info('');
        $this->command->info('Default admin login: admin@djanproject.com / admin12345');
        $this->command->info('User login: [firstname.lastname@company.co.tz] / password123');
    }
}
