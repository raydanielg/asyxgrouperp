# ASYX Group ERP — Development Instructions

> Chanzo: ASYX Group SRS Document (Enterprise Resource Planning System Requirements Specification)
> Faili hili linaorodhesha **kila functionality** iliyoainishwa kwenye SRS, ikiwa imepangwa kwa mfumo wa maelekezo ya ujenzi (build instructions) kwa timu ya maendeleo (developers), pamoja na muhtasari (summary) mwishoni.

---

## 0. Lengo Kuu la Mfumo

Jenga mfumo mmoja wa ERP (single integrated platform) kwa ajili ya ASYX Group Company Limited na makampuni tanzu yake, unaotoa **single source of truth**, unaosaidia michakato yote ya biashara kutoka Tender hadi Malipo ya Mteja (Tender-to-Cash), na unaoruhusu udhibiti wa kati (centralized governance) huku kila kampuni tanzu ikiwa na uhuru wa uendeshaji (operational autonomy).

---

## 1. MODULE: Multi-Company / Group Structure

**Jenga uwezo wa:**
- Kusajili makampuni mengi ndani ya group moja (multi-company architecture)
- Kampuni za awali za kusanidi:
  1. ASYX Group Company Limited
  2. Parktech Tanzania Limited
  3. Motisha Company Limited
  4. Terkmark Limited
  5. Glovin Limited
- Kuongeza kampuni/subsidiaries/joint ventures mpya baadaye (extensible)

**Kila kampuni iwe na data zake huru (independent):**
- Chart of Accounts, Customers, Vendors, Employees, Payroll, Projects, Contracts, Inventory, Assets, Bank Accounts, Budgets, Financial Statements

**Group Consolidation — jenga taarifa za pamoja:**
- Consolidated P&L, Balance Sheet, Cash Flow Statement, Trial Balance
- Group Budget Reports, Consolidated Project Profitability, Executive Dashboards

**Intercompany Transactions — jenga uwezo wa:**
- Intercompany invoicing, purchases/sales, loans/advances
- Expense allocations, journal entries, reconciliations
- Automatic elimination entries (kuondoa double-counting wakati wa consolidation)

**Shared Services (huduma za pamoja kati ya makampuni):**
- Finance & Accounting, HR, Procurement, ICT Support, Project Management, Legal & Administration

**Cross-Company Projects:**
- Shared project teams, shared resources, cost allocation, revenue sharing, joint profitability analysis

**User Access (company-scoped):**
- Employee → kampuni moja tu
- Manager → makampuni kadhaa
- Executive → makampuni yote

**Multi-Branch Capability (kwa kila kampuni):**
- Branches, Warehouses, Project Sites, Offices

**Group Executive Dashboard — onyesha:**
- Revenue/Profitability by company, Group revenue/profitability, Receivables by company, Cash position by company, Company comparisons

---

## 2. MODULE: Dashboards & Executive Insights

**Executive Dashboard — vipengele:**
- Revenue by Project/Company, Profitability by Project
- Outstanding Receivables, Cash Flow, Budget Variance
- Tender Win Ratio, Staff Utilization, Project Status

**Department Dashboards (moja kwa kila idara):**
- Finance, HR, CRM, Procurement, Project, Service Desk, Inventory

---

## 3. MODULE: Tender Management

**Features za kujenga:**
- Tender registration, Tender calendar, Tender document repository
- Tender committee management, Bid/No-Bid workflow
- Tender costing, Competitor analysis
- Tender award tracking, Tender reports

**Workflow ya lazima:**
`Tender → Opportunity → Proposal → Quotation → Deal → Project`

---

## 4. MODULE: CRM, Sales & Contract Management

### 4.1 CRM
- Lead Management, Opportunity Management, Deal Pipeline
- Activity Tracking, Customer Communication History, Sales Forecasting

### 4.2 Quotations & Proposals
- Proposal creation, Quotation generation, Version control
- Approval workflow, PDF generation, Email dispatch

### 4.3 Contract Management
- Contract repository, Contract approval workflow, Amendments tracking
- Expiry alerts, Renewal alerts, SLA management
- Performance Bond tracking, Insurance Bond tracking, Retention management

---

## 5. MODULE: Project Management

**Features za kujenga:**
- Project registration, Project budgeting, Resource allocation
- Task management, Milestones, Gantt Charts, Kanban Boards
- Timesheets, Issue/Bug tracking
- Project profitability analysis, Client portal, Project document repository

---

## 6. MODULE: Technical Services & Managed Services

**Features za kujenga:**
- Customer Asset Register, Preventive Maintenance, Corrective Maintenance
- Site Visits, Engineer Dispatch, Service Contracts
- SLA Monitoring, Escalation Matrix, Service Reports
- Warranty Tracking, Recurring Maintenance Jobs

---

## 7. MODULE: Helpdesk & Ticketing

**Features za kujenga:**
- Ticket creation, Ticket assignment, Prioritization
- Escalation workflows, SLA monitoring
- Knowledge base, Customer portal, Response time tracking

---

## 8. MODULE: Procurement & Vendor Management

**Features za kujenga:**
- Vendor Database, RFQ Management, Quotation Comparison
- Purchase Requisitions, Purchase Approvals
- Local Purchase Orders (LPO), Goods Received Notes (GRN), Delivery Notes
- Purchase Invoices, Vendor Payments, Purchase Returns

**Workflow ya lazima:**
`RFQ → Quotation → Approval → LPO → GRN → Invoice → Payment`

---

## 9. MODULE: Inventory & Warehousing

**Features za kujenga:**
- Product Catalogue, Service Catalogue
- Multi-Warehouse Management, Stock Transfers, Reorder Levels
- Batch/Serial Tracking, Barcode Management, Inventory Valuation

---

## 10. MODULE: Fixed Assets Management

**Features za kujenga:**
- Asset Register, Asset Assignment, Asset Transfers
- Depreciation, Asset Maintenance, Asset Disposal, Barcode/QR Tracking

---

## 11. MODULE: Fleet Management

**Features za kujenga:**
- Vehicle Register, Driver Management, Fuel Management
- Trip Management, Insurance Tracking, Vehicle Maintenance, Mileage Monitoring

---

## 12. MODULE: Accounting & Finance

**Features za kujenga:**
- Chart of Accounts, General Ledger
- Accounts Payable, Accounts Receivable
- Cash & Bank Management, Bank Reconciliation
- Budgeting, Cost Centres, Project Accounting
- Tax Management, Financial Statements

**Ripoti za lazima:**
- Trial Balance, Profit & Loss Statement, Balance Sheet
- Cash Flow Statement, Budget vs Actual

---

## 13. MODULE: Human Resources & Payroll

**Features za kujenga:**
- Employee Records, Organizational Structure
- Recruitment, Onboarding
- Attendance, Leave Management, Shift Management, Overtime Management
- Payroll Processing, Loans Management
- Training Management, Performance Appraisals, Disciplinary Management
- Employee Self-Service

---

## 14. MODULE: Call Center Operations

**Features za kujenga:**
- Agent Management, Shift Scheduling, Call Statistics
- Performance Monitoring, SLA Monitoring, Attendance Tracking

---

## 15. MODULE: Document Management System

**Features za kujenga:**
- Central Repository, Version Control, Approval Workflow
- Check-in/Check-out, Electronic Signatures, Document Expiry Alerts

---

## 16. User Roles & Access Control (RBAC)

Jenga mfumo wa **Role-Based Access Control** wenye makundi yafuatayo ya roles:

| Kundi | Roles |
|---|---|
| System Administration | ERP Super Administrator, ERP Administrator, ICT Administrator, Auditor |
| Executive | Managing Director, General Manager, Technical Manager, Operations Manager |
| Finance | Finance Manager, Chief Accountant, Accountant, AR Officer, AP Officer, Cashier, Payroll Officer, Budget Officer, Credit Controller |
| Procurement | Procurement Manager, Procurement Officer, Tender Officer |
| Inventory | Store Manager, Storekeeper, Inventory Controller, Asset Officer |
| Sales | Sales Manager, Business Development Manager, Sales Executive, CRM Officer, Marketing Officer |
| Project | Project Director, Project Manager, Technical Projects Manager, Project Coordinator, Project Engineer, Site Supervisor, Team Leader, Project Accountant |
| Technical | Senior Systems Engineer, Systems Engineer, Network Engineer, Software Engineer, Cybersecurity Engineer, Support Engineer, Field Technician, NOC Engineer |
| Service Desk | Service Desk Manager, Helpdesk Supervisor, Helpdesk Officer, Call Center Supervisor, Call Center Agent |
| HR | HR Manager, HR Officer, Recruitment Officer, Training Officer, Time & Attendance Officer |
| Operations | Operations Officer, Fleet Manager, Logistics Officer |
| Self-Service | Employee Self-Service User, Manager Self-Service User |

---

## 17. Approval Workflow Hierarchy

**Muundo wa msingi (default hierarchy):**
`Employee → Team Leader/Supervisor → Department Manager → Finance Manager → Managing Director`

**Mifano ya workflows maalum ya kujenga:**
- Expense Request: `Supervisor → Manager → Finance → MD`
- LPO Approval: `Procurement → Technical Manager → Finance → MD`
- Leave Approval: `Supervisor → HR`
- Payroll Approval: `HR → Finance → MD`

---

## 18. Security Requirements

Jenga na uhakikishe mfumo unasaidia:
- Role-Based Access Control (RBAC)
- Two-Factor Authentication (2FA)
- Password Policies, Session Timeout
- Audit Logs, Login History
- Data Encryption, IP Restrictions
- Automated Backups, Disaster Recovery

---

## 19. Technical Requirements (Developer/Infrastructure)

- Web-Based Architecture
- Cloud Deployment Option + On-Premise Deployment Option
- REST APIs
- Mobile Application Support
- Third-Party Integration Capability
- Backup Architecture
- Database Technology Details (ainisha teknolojia ya database itakayotumika)
- High Availability Architecture

---

## 20. Non-Functional Requirements

Mfumo lazima uonyeshe:
- Multi-Company Management, Multi-Branch Management
- Group Consolidation, Intercompany Accounting
- High Availability (99.9%)
- Scalability, High Performance
- Responsive User Interface (mobile/desktop friendly)
- Multi-Currency Support
- Auditability

---

## 21. Expected Benefits (Malengo ya Mwisho)

- Single Source of Truth
- End-to-End Business Automation
- Improved Governance and Compliance
- Better Financial Control
- Improved Decision Making
- Enhanced Customer Experience
- Improved Profitability Monitoring
- Improved Group Visibility Across Subsidiaries

---

## SUMMARY (Muhtasari)

ASYX Group Company Limited inahitaji mfumo mmoja wa ERP unaounganisha shughuli zote za kampuni tano (ASYX Group, Parktech Tanzania, Motisha, Terkmark, na Glovin) chini ya jukwaa moja. Mfumo huu unatakiwa kuwa na jumla ya **moduli 14 kuu za kifanikazi** (Tender, CRM/Sales, Contract, Project, Technical/Managed Services, Helpdesk, Procurement, Inventory, Fixed Assets, Fleet, Accounting, HR/Payroll, Call Center, na Document Management), zote zikiwa zimeunganishwa kwenye muundo wa **Multi-Company Group Management** wenye uwezo wa consolidation, intercompany transactions, na shared services.

Mfumo unahitaji udhibiti mkali wa upatikanaji (RBAC yenye zaidi ya makundi 12 ya roles), mfumo wa approval wa ngazi tano (Employee → Supervisor → Manager → Finance → MD), na vipengele imara vya usalama (2FA, encryption, audit logs, backups). Kiufundi, mfumo unatakiwa uwe wa msingi wa wavuti (web-based), na chaguo la cloud au on-premise, wenye REST APIs, msaada wa simu (mobile), na uwezo wa kuunganishwa na mifumo mingine ya nje.

Lengo la mwisho ni kutoa **single source of truth** kwa Group nzima — kuanzia hatua ya Tender mpaka Malipo ya Mteja (Tender-to-Cash) — huku kila kampuni tanzu ikiendelea kuwa na uhuru wake wa uendeshaji wa kila siku, na uongozi mkuu (Executives) wakiwa na taswira kamili ya kifedha na kiutendaji ya makampuni yote kupitia dashboards za pamoja.
