# ASYX Group ERP — Role-Based Sidebar Menu and Access Matrix

This document defines the sidebar menu, functional scope, permissions, and limitations for every operational role in the ASYX Group ERP system. System Administration roles (ERP Super Administrator, ERP Administrator, ICT Administrator, Auditor) are excluded, as they operate under full system-level access rather than the scoped operational access described here.

Each role is documented using the following structure:

- **Sidebar Menu** — the menu items visible to that role
- **Menu Description** — what each menu item contains and does
- **Permissions** — what the role is allowed to do
- **Limitations** — what the role cannot see or do

**General rule on Payroll and Personal Data:** No role, regardless of seniority, can view another employee's individual salary, payslip, or personal HR file unless that access is an explicit part of their job function (HR Manager, HR Officer, Payroll Officer, Finance Manager). Every employee — including managers and executives — can only view their own payroll information through the Self-Service menu, unless they are logged in specifically under an HR/Payroll/Finance role.

---

## Section A — Executive Roles

### 1. Managing Director

**Sidebar Menu:**
Group Executive Dashboard, All Companies Overview, Reports Center, Final Approvals Queue, Tender Overview, Contracts Overview, HR Overview, My Profile (Self-Service)

**Menu Description:**
- Group Executive Dashboard: revenue and profitability across all Group companies, cash position, receivables by company, and company-to-company comparisons.
- Reports Center: consolidated financial reports (P&L, Balance Sheet, Cash Flow, Budget vs Actual) at both company and Group level.
- Final Approvals Queue: requests that have passed all lower approval stages and require final sign-off (large expenses, LPOs, payroll, contracts).
- Tender/Contracts Overview: high-level status of active tenders and major contracts, without day-to-day operational detail.

**Permissions:**
- Final approval authority across all workflows (expense, LPO, payroll, contracts)
- View access to all companies within the Group
- View access to consolidated and individual company financial reports
- Switch between companies within the Group

**Limitations:**
- No access to system configuration or settings
- Cannot create, edit, or delete user accounts
- Does not perform daily data entry; view and approval only
- Can only view own payroll/payslip through Self-Service, not other employees' payroll details

---

### 2. General Manager

**Sidebar Menu:**
Company Dashboard, Department Reports, Departmental Approvals, Projects Overview, Sales and CRM Overview, HR Overview, My Profile

**Menu Description:**
- Company Dashboard: performance summary of the company they are assigned to (multiple companies only if explicitly granted).
- Department Reports: summarized reports from Finance, Sales, Project, and HR.
- Departmental Approvals: mid-level approvals before requests are escalated to the Managing Director.

**Permissions:**
- Mid-level approval authority (before MD)
- View reports across all departments in their assigned company
- Monitor overall project and sales performance

**Limitations:**
- Cannot view other Group companies unless explicitly granted access
- Cannot grant final approval on requests requiring MD sign-off
- No access to system settings
- Can only view own payroll, not that of other employees

---

### 3. Technical Manager

**Sidebar Menu:**
Technical Projects Dashboard, Project List, Technical Team Management, Service Delivery Overview, LPO Approval Queue (Technical Stage), My Profile

**Menu Description:**
- Technical Projects Dashboard: status of all technical projects, resource allocation, and milestones.
- Technical Team Management: engineers and their current assignments.
- LPO Approval Queue: technical equipment purchase requests awaiting technical sign-off before Finance review.

**Permissions:**
- Approve LPOs at the technical review stage
- Allocate technical resources across projects
- Monitor performance of Technical and Support Engineers

**Limitations:**
- Cannot grant final payment approval (Finance/MD only)
- No visibility into HR records of other departments
- No direct access to the Accounting module
- Can only view own payroll

---

### 4. Operations Manager

**Sidebar Menu:**
Operations Dashboard, Fleet Overview, Logistics Overview, Service Desk Overview, Inventory Summary, My Profile

**Menu Description:**
- Operations Dashboard: summary of fleet, logistics, and field service activity.
- Fleet/Logistics Overview: vehicle and shipment tracking.

**Permissions:**
- Monitor and approve day-to-day operational activity (fleet, logistics)
- View overall inventory levels

**Limitations:**
- Cannot modify the Chart of Accounts or financial records
- No visibility into employee salaries
- Can only view own payroll

---

## Section B — Finance Roles

### 5. Finance Manager

**Sidebar Menu:**
Finance Dashboard, General Ledger, Accounts Payable, Accounts Receivable, Bank and Cash Management, Budgeting, Financial Reports, Finance Approvals Queue, My Profile

**Menu Description:**
- Finance Dashboard: cash flow, receivables, payables, and budget variance.
- Finance Approvals Queue: requests awaiting finance-stage approval before MD sign-off (expense, LPO, payroll).
- Financial Reports: Trial Balance, P&L, Balance Sheet, Cash Flow, Budget vs Actual.

**Permissions:**
- Approve financial requests before they reach the MD
- Add or modify Chart of Accounts entries (subject to internal policy)
- Close accounting periods
- View all financial reports for their assigned company

**Limitations:**
- Cannot approve requests they themselves initiated (segregation of duties)
- No Group-level consolidation view unless granted multi-company access
- Cannot alter employee salary structures without HR authorization
- Can view payroll totals for approval purposes, but individual payslip detail remains within the Payroll module workflow

---

### 6. Chief Accountant

**Sidebar Menu:**
General Ledger, Journal Entries, Bank Reconciliation, Financial Statements, Tax Management, My Profile

**Menu Description:**
- Journal Entries: creation and review of accounting entries.
- Bank Reconciliation: matching bank records against system records.

**Permissions:**
- Create and edit journal entries
- Perform bank reconciliation
- Prepare financial statements for Finance Manager review

**Limitations:**
- Cannot give final approval on financial statements
- Cannot perform year-end closing without authorization
- Can only view own payroll

---

### 7. Accountant

**Sidebar Menu:**
Journal Entries (Data Entry), Invoices, Expense Records, Cost Centres, My Profile

**Menu Description:**
Daily bookkeeping and transaction entry at operational level.

**Permissions:**
- Enter routine accounting transactions
- Create internal invoices

**Limitations:**
- No approval authority
- Cannot delete posted entries
- No access to Group-level reports
- Can only view own payroll

---

### 8. Accounts Receivable Officer

**Sidebar Menu:**
Customer Invoices, Receivables Aging Report, Payment Receipts, Credit Notes, My Profile

**Menu Description:**
Tracks customer debts and incoming payments.

**Permissions:**
- Issue customer invoices
- Record incoming payments
- Track overdue receivables

**Limitations:**
- Cannot approve large credit notes without Finance Manager sign-off
- No access to Accounts Payable or Payroll
- Can only view own payroll

---

### 9. Accounts Payable Officer

**Sidebar Menu:**
Vendor Invoices, Payment Requests, Payables Aging Report, My Profile

**Menu Description:**
Manages vendor invoices and outgoing payment requests.

**Permissions:**
- Record vendor invoices
- Initiate payment requests (subject to Finance approval)

**Limitations:**
- Cannot release payments without Finance Manager/Cashier authorization
- No access to Accounts Receivable module
- Can only view own payroll

---

### 10. Cashier

**Sidebar Menu:**
Cash Book, Petty Cash, Daily Collections, Payment Vouchers, My Profile

**Menu Description:**
Manages day-to-day cash transactions.

**Permissions:**
- Handle cash receipts and disbursements within an assigned transaction limit
- Prepare payment vouchers

**Limitations:**
- Subject to a daily/transaction cash limit set by Finance Manager
- Cannot approve large payments
- No access to the full General Ledger
- Can only view own payroll

---

### 11. Payroll Officer

**Sidebar Menu:**
Payroll Processing, Employee Salary Records, Statutory Deductions, Payslips, My Profile

**Menu Description:**
Manages the end-to-end monthly payroll process.

**Permissions:**
- Process monthly payroll
- Generate payslips
- Calculate statutory deductions (NSSF, PAYE, WCF, etc.)

**Limitations:**
- Cannot release salary payments without HR, Finance, and MD approval in sequence
- No visibility into Accounts Payable/Receivable for customers/vendors
- Views employee salary data only as required to process payroll; cannot alter salary structures without HR approval

---

### 12. Budget Officer

**Sidebar Menu:**
Budget Preparation, Budget vs Actual Reports, Cost Centre Budgets, My Profile

**Menu Description:**
Prepares and monitors departmental and project budgets.

**Permissions:**
- Prepare annual/project budgets
- Track budget variance

**Limitations:**
- Cannot approve spending that exceeds budget (Finance Manager/MD approval required)
- Can only view own payroll

---

### 13. Credit Controller

**Sidebar Menu:**
Customer Credit Limits, Overdue Accounts, Collection Follow-ups, My Profile

**Menu Description:**
Monitors customer credit exposure and overdue collections.

**Permissions:**
- Set/adjust customer credit limits within company policy
- Place credit holds on customers with excessive overdue balances

**Limitations:**
- Cannot write off customer debt without Finance Manager approval
- Can only view own payroll

---

## Section C — Procurement Roles

### 14. Procurement Manager

**Sidebar Menu:**
Procurement Dashboard, Vendor Management, RFQ Management, Purchase Approvals (Procurement Stage), Purchase Reports, My Profile

**Menu Description:**
Oversees the entire procurement process.

**Permissions:**
- Approve Purchase Requisitions before Technical/Finance review
- Add or remove vendors from the database
- Compare vendor quotations

**Limitations:**
- Cannot release direct payments to vendors (Finance handles disbursement)
- No access to Payroll or HR records
- Can only view own payroll

---

### 15. Procurement Officer

**Sidebar Menu:**
RFQ Creation, Purchase Requisitions, Local Purchase Orders, Goods Received Notes, My Profile

**Menu Description:**
Handles day-to-day procurement operations.

**Permissions:**
- Create RFQs and LPOs
- Record Goods Received Notes upon delivery

**Limitations:**
- Cannot approve their own LPOs (requires Procurement Manager sign-off)
- Cannot alter vendor payment terms
- Can only view own payroll

---

### 16. Tender Officer

**Sidebar Menu:**
Tender Registration, Tender Calendar, Bid Documents Repository, Tender Costing, My Profile

**Menu Description:**
Manages tender registration and bid preparation.

**Permissions:**
- Register new tenders
- Prepare tender costing and bid documentation

**Limitations:**
- Cannot make Bid/No-Bid decisions alone (requires Tender Committee approval)
- No access to detailed company financial data
- Can only view own payroll

---

## Section D — Inventory Roles

### 17. Store Manager

**Sidebar Menu:**
Inventory Dashboard, Warehouse Management, Stock Transfers, Reorder Levels, Inventory Valuation Reports, My Profile

**Menu Description:**
Oversees all warehouses within their company.

**Permissions:**
- Approve stock transfers between warehouses
- Set reorder levels
- View inventory valuation

**Limitations:**
- Cannot alter procurement pricing
- No access to Fixed Assets Register unless separately granted
- Can only view own payroll

---

### 18. Storekeeper

**Sidebar Menu:**
Stock In/Out, Goods Received Notes, Stock Count, My Profile

**Menu Description:**
Handles daily receipt and dispatch of goods.

**Permissions:**
- Record stock movements
- Perform physical stock counts

**Limitations:**
- Cannot approve stock transfers between warehouses (Store Manager approval required)
- Cannot alter product pricing
- Can only view own payroll

---

### 19. Inventory Controller

**Sidebar Menu:**
Product Catalogue, Batch/Serial Tracking, Barcode Management, Stock Variance Reports, My Profile

**Menu Description:**
Maintains data accuracy across the inventory system.

**Permissions:**
- Manage batch/serial numbers
- Track stock variance between system and physical counts

**Limitations:**
- Cannot approve new procurement
- No access to company financial reports
- Can only view own payroll

---

### 20. Asset Officer

**Sidebar Menu:**
Asset Register, Asset Assignment, Asset Maintenance, Asset Disposal Requests, My Profile

**Menu Description:**
Manages company assets from registration to disposal.

**Permissions:**
- Register new assets
- Assign assets to employees
- Submit asset disposal requests (requiring higher approval)

**Limitations:**
- Cannot approve disposal independently (requires Finance/MD approval)
- No access to detailed depreciation calculations (managed under Accounting)
- Can only view own payroll

---

## Section E — Sales Roles

### 21. Sales Manager

**Sidebar Menu:**
Sales Dashboard, Deal Pipeline (Team View), Sales Forecasting, Quotation Approvals, Sales Team Performance, My Profile

**Menu Description:**
Oversees the entire sales team.

**Permissions:**
- Approve quotations before they are sent to clients
- View the full team pipeline
- Perform sales forecasting

**Limitations:**
- Cannot alter product/service pricing in the system (Inventory/Finance manage this)
- No access to HR records of other staff
- Can only view own payroll

---

### 22. Business Development Manager

**Sidebar Menu:**
Lead Management, Opportunity Pipeline, Market/Competitor Analysis, My Profile

**Menu Description:**
Focused on generating new clients and business opportunities.

**Permissions:**
- Create new leads and opportunities
- Track competitor activity

**Limitations:**
- Cannot finalize contract approval independently (Contract Management workflow applies)
- No access to detailed company financial reports
- Can only view own payroll

---

### 23. Sales Executive

**Sidebar Menu:**
My Leads, My Deals, Quotations (Draft), Customer Communication Log, My Profile

**Menu Description:**
Handles day-to-day sales activity at the individual level.

**Permissions:**
- Create own leads and opportunities
- Prepare draft quotations

**Limitations:**
- Cannot send final quotations to clients without Sales Manager approval
- Cannot view other salespeople's deals/leads unless part of their team
- Can only view own payroll

---

### 24. CRM Officer

**Sidebar Menu:**
Customer Database, Activity Tracking, Communication History, My Profile

**Menu Description:**
Maintains the accuracy and quality of customer data in the CRM.

**Permissions:**
- Update customer records
- Log communications (calls, emails, meetings)

**Limitations:**
- Cannot delete customer records without approval
- No access to customer financial data (invoices/payments)
- Can only view own payroll

---

### 25. Marketing Officer

**Sidebar Menu:**
Campaigns, Lead Source Reports, Marketing Materials Repository, My Profile

**Menu Description:**
Manages marketing activities and lead generation tracking.

**Permissions:**
- Create and track campaigns
- Upload marketing materials

**Limitations:**
- No detailed visibility into individual customer sales pipelines
- Cannot alter product/service pricing
- Can only view own payroll

---

## Section F — Project Roles

### 26. Project Director

**Sidebar Menu:**
Portfolio Dashboard, Project Profitability Overview, Resource Allocation Overview, My Profile

**Menu Description:**
Oversees the entire project portfolio at company level.

**Permissions:**
- Approve project budgets
- View profitability across all projects

**Limitations:**
- Does not manage day-to-day task-level detail (handled by Project Managers)
- Cannot modify the Chart of Accounts
- Can only view own payroll

---

### 27. Project Manager

**Sidebar Menu:**
My Projects, Task Management, Gantt Chart/Kanban Board, Milestones, Project Budget, Client Portal Access, My Profile

**Menu Description:**
Manages one or more assigned projects.

**Permissions:**
- Create and update tasks and milestones
- Monitor spending against project budget
- Approve timesheets for their project team

**Limitations:**
- Cannot view projects managed by other Project Managers unless granted access
- Cannot approve project payments (Finance approval required)
- Can only view own payroll

---

### 28. Technical Projects Manager

**Sidebar Menu:**
Technical Project List, Engineer Allocation, Technical Milestones, My Profile

**Menu Description:**
Equivalent to Project Manager, focused on technical/technology projects.

**Permissions:**
- Assign engineers to projects
- Track technical milestones

**Limitations:**
- Cannot approve commercial contract terms
- Can only view own payroll

---

### 29. Project Coordinator

**Sidebar Menu:**
Task Tracker, Project Document Repository, Meeting Schedules, My Profile

**Menu Description:**
Supports the Project Manager with day-to-day coordination.

**Permissions:**
- Update task status
- Schedule project meetings

**Limitations:**
- Cannot approve budget changes
- Cannot close/complete a project
- Can only view own payroll

---

### 30. Project Engineer

**Sidebar Menu:**
My Tasks, Site Reports, My Timesheet, My Profile

**Menu Description:**
Executes technical work on-site for assigned projects.

**Permissions:**
- Submit progress/site reports
- Complete own timesheet

**Limitations:**
- No visibility into project budget
- Cannot reassign tasks belonging to colleagues
- Can only view own payroll

---

### 31. Site Supervisor

**Sidebar Menu:**
Site Attendance, Daily Site Reports, Issue/Incident Logging, My Profile

**Menu Description:**
Manages daily on-site activity.

**Permissions:**
- Record site attendance
- Log site issues/incidents

**Limitations:**
- Cannot approve additional site expenditure
- No access to client contract details
- Can only view own payroll

---

### 32. Team Leader

**Sidebar Menu:**
Team Task Board, Team Attendance, Team Timesheets (First Approval), My Profile

**Menu Description:**
First-line supervision of a small team.

**Permissions:**
- Approve team timesheets (first stage of the approval workflow)
- Assign work within the team

**Limitations:**
- Cannot approve leave requests independently (HR completes final approval after supervisor)
- No visibility into team members' salaries
- Can only view own payroll

---

### 33. Project Accountant

**Sidebar Menu:**
Project Budget vs Actual, Project Invoicing, Cost Allocation (Per Project), My Profile

**Menu Description:**
Manages the financial side of a specific project.

**Permissions:**
- Track project spending against budget
- Prepare project invoices

**Limitations:**
- Cannot give final payment approval (Finance Manager/MD required)
- No access to the company's full General Ledger
- Can only view own payroll

---

## Section G — Technical Roles

### 34. Senior Systems Engineer

**Sidebar Menu:**
Assigned Projects/Tickets, System Architecture Documentation, Team Task Review, My Profile

**Menu Description:**
Senior technical role overseeing complex work and junior engineers.

**Permissions:**
- Approve technical work completed by junior team members
- Access detailed technical documentation

**Limitations:**
- Cannot alter commercial contract terms
- No access to client financial data
- Can only view own payroll

---

### 35. Systems Engineer / Network Engineer / Software Engineer / Cybersecurity Engineer

**Sidebar Menu:**
My Tickets/Tasks, Technical Asset/System Register, Maintenance Logs, My Profile

**Menu Description:**
Each engineer sees only items relevant to their area of specialization (network, software, or security).

**Permissions:**
- Close/update assigned tickets
- Record maintenance and incident logs

**Limitations:**
- Cannot view tickets outside their assigned specialization
- Cannot independently finalize a Service Contract
- Can only view own payroll

---

### 36. Support Engineer / Field Technician

**Sidebar Menu:**
Assigned Site Visits, Service Reports, Customer Asset History, My Profile

**Menu Description:**
Delivers direct technical support to customers on-site.

**Permissions:**
- Submit service reports after visits
- Update ticket status following completed work

**Limitations:**
- Cannot alter SLA terms
- No access to client contract/financial details
- Can only view own payroll

---

### 37. NOC Engineer

**Sidebar Menu:**
Network Monitoring Dashboard, Incident Log, Escalation Queue, My Profile

**Menu Description:**
Monitors network and systems on a continuous basis.

**Permissions:**
- Open and escalate incidents
- Track system uptime/downtime

**Limitations:**
- Cannot close a ticket without confirmation from client/supervisor
- No visibility into commercial project details
- Can only view own payroll

---

## Section H — Service Desk Roles

### 38. Service Desk Manager

**Sidebar Menu:**
Service Desk Dashboard, SLA Performance Report, Team Performance, Escalation Overview, My Profile

**Menu Description:**
Oversees the entire customer service department.

**Permissions:**
- Approve critical escalations
- Monitor helpdesk/call center team performance

**Limitations:**
- Cannot alter SLA terms in customer contracts (managed under Contract Management)
- No access to company financial data
- Can only view own payroll

---

### 39. Helpdesk Supervisor

**Sidebar Menu:**
Ticket Queue (Team), Agent Performance, Escalation Handling, My Profile

**Menu Description:**
Directly supervises helpdesk staff.

**Permissions:**
- Assign tickets to agents
- Escalate problematic tickets

**Limitations:**
- Cannot delete historical tickets
- No access to reports from other departments
- Can only view own payroll

---

### 40. Helpdesk Officer

**Sidebar Menu:**
My Tickets, Knowledge Base, Customer Contact Log, My Profile

**Menu Description:**
Receives and resolves day-to-day customer requests.

**Permissions:**
- Open and update tickets
- Use the knowledge base to respond to inquiries

**Limitations:**
- Cannot close complex tickets without Supervisor approval
- Cannot view tickets assigned to other agents
- Can only view own payroll

---

### 41. Call Center Supervisor

**Sidebar Menu:**
Call Statistics (Team), Shift Scheduling, SLA Monitoring (Calls), My Profile

**Menu Description:**
Supervises call center agents.

**Permissions:**
- Schedule agent shifts
- Monitor team call performance

**Limitations:**
- Cannot alter agent compensation structure
- No detailed HR salary visibility
- Can only view own payroll

---

### 42. Call Center Agent

**Sidebar Menu:**
My Call Log, My Shift Schedule, My Performance Stats, My Profile

**Menu Description:**
Direct customer-facing telephone role.

**Permissions:**
- Record own call logs
- View own work schedule

**Limitations:**
- Cannot view other agents' statistics
- Cannot change shift assignments without Supervisor approval
- Can only view own payroll

---

## Section I — HR Roles

### 43. HR Manager

**Sidebar Menu:**
HR Dashboard, Employee Records (All), Recruitment Overview, Leave Approvals, Payroll Approval Queue, Disciplinary Cases, Performance Appraisals, My Profile

**Menu Description:**
Oversees the entire HR department.

**Permissions:**
- Approve leave and payroll (before Finance/MD)
- Manage disciplinary cases
- View records for all employees in the company

**Limitations:**
- Cannot give final payroll payment approval (Finance/MD are the final approvers)
- No access to company financial data unrelated to payroll
- Views employee records as part of HR duties, but individual payslip release still follows the Payroll workflow

---

### 44. HR Officer

**Sidebar Menu:**
Employee Records (Data Entry), Attendance Records, Leave Requests (Processing), My Profile

**Menu Description:**
Handles day-to-day HR operations.

**Permissions:**
- Enter and update employee records
- Process leave requests prior to HR Manager approval

**Limitations:**
- Cannot approve leave independently
- No access to actual employee salary figures (Payroll module)
- Can only view own payroll

---

### 45. Recruitment Officer

**Sidebar Menu:**
Job Postings, Candidate Pipeline, Onboarding Checklist, My Profile

**Menu Description:**
Manages the hiring process for new employees.

**Permissions:**
- Create job postings
- Track candidates through the pipeline
- Initiate onboarding

**Limitations:**
- Cannot approve employment contracts independently (HR Manager approval required)
- No access to existing employees' salary data
- Can only view own payroll

---

### 46. Training Officer

**Sidebar Menu:**
Training Calendar, Training Records, Certification Tracking, My Profile

**Menu Description:**
Manages employee training programs.

**Permissions:**
- Schedule training sessions
- Track employee certifications

**Limitations:**
- Cannot alter performance appraisal criteria
- No access to disciplinary case records
- Can only view own payroll

---

### 47. Time and Attendance Officer

**Sidebar Menu:**
Attendance Dashboard, Shift Records, Overtime Tracking, My Profile

**Menu Description:**
Manages attendance and working hours.

**Permissions:**
- Update attendance records
- Verify overtime hours before Payroll processing

**Limitations:**
- Cannot approve overtime payment (Finance/HR Manager approval required)
- No access to salary data
- Can only view own payroll

---

## Section J — Operations Roles

### 48. Operations Officer

**Sidebar Menu:**
Daily Operations Log, Task Assignments (Operations), My Profile

**Menu Description:**
Manages daily operational activity.

**Permissions:**
- Log daily operational activity
- Assign urgent operational tasks

**Limitations:**
- Cannot approve new procurement
- No access to financial reports
- Can only view own payroll

---

### 49. Fleet Manager

**Sidebar Menu:**
Vehicle Register, Driver Assignment, Fuel and Maintenance Logs, Trip Scheduling, My Profile

**Menu Description:**
Manages the entire company vehicle fleet.

**Permissions:**
- Assign drivers to vehicles/trips
- Approve vehicle maintenance services
- Monitor fuel consumption

**Limitations:**
- Cannot purchase a new vehicle without Procurement/Finance approval
- No access to driver salary information
- Can only view own payroll

---

### 50. Logistics Officer

**Sidebar Menu:**
Delivery Schedules, Shipment Tracking, Route Planning, My Profile

**Menu Description:**
Manages transportation of goods and materials.

**Permissions:**
- Plan delivery schedules
- Track shipment status

**Limitations:**
- Cannot alter established shipping cost structures
- No access to detailed Inventory Valuation
- Can only view own payroll

---

## Section K — Self-Service Roles

### 51. Employee Self-Service User

**Sidebar Menu:**
My Profile, My Payslips, Apply for Leave, My Attendance, My Timesheets, Company Announcements

**Menu Description:**
This is the baseline menu available to every employee in the company, regardless of department, enabling self-management of personal records.

**Permissions:**
- Apply for leave
- Download own payslip
- Update personal information (phone number, address, etc.)
- Complete own timesheet

**Limitations:**
- Cannot view other employees' records
- Cannot alter own salary
- No access to company financial data

---

### 52. Manager Self-Service User

**Sidebar Menu:**
My Team Overview, Team Leave Approvals, Team Attendance, Team Timesheets (Approval), plus the standard Employee Self-Service menu

**Menu Description:**
An extension of the Self-Service menu for anyone with direct reports, regardless of department.

**Permissions:**
- Approve leave requests for their direct reports (first stage)
- Approve timesheets for their direct reports

**Limitations:**
- Cannot view salary information for their team (HR/Payroll only)
- Cannot approve leave for employees outside their reporting line
- Can only view own payroll

---

## Summary of Design Principles

The sidebar menus and permissions above follow six consistent rules across all 52 roles:

1. **Least privilege by default.** Each role sees only what is required for their function. No operational role has visibility into detailed company financial data unless finance is their function.

2. **Three access tiers.**
   - Manager-level roles (Finance Manager, HR Manager, Sales Manager, Procurement Manager, etc.) see department-wide dashboards and hold approval authority.
   - Officer/Specialist-level roles (Accountant, HR Officer, Procurement Officer, etc.) see operational data entry screens without significant approval power.
   - Individual contributor roles (Sales Executive, Call Center Agent, Field Technician, etc.) see only their own tasks and records ("My Tasks", "My Tickets", "My Reports").

3. **Approval authority follows the defined workflow hierarchy** (Employee → Team Leader → Department Manager → Finance Manager → Managing Director). No role can approve its own request, and every role has a defined ceiling on which approval stage it can act at.

4. **Self-Service is universal.** Every employee, regardless of department, has the baseline Self-Service menu (My Profile, My Payslip, Apply for Leave, My Timesheet) layered on top of their role-specific menu.

5. **Payroll and personal data are strictly self-scoped.** No employee, manager, or executive can view another individual's payslip or salary detail outside of the HR, Payroll, and Finance roles whose job function explicitly requires it. Everyone else sees only their own payroll information.

6. **Cross-department visibility is intentionally restricted**, and **company scoping applies to every role**: employees see one company only, managers may see multiple companies if explicitly granted, and only Executive roles see the full Group by default. This protects data confidentiality across departments and across companies within the Group.
