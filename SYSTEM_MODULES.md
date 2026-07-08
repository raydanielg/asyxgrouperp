# ASYX Group ERP — System Modules Guide

This document explains every module in the ERP in plain business language. It is written for managers, developers, and support staff who need to understand what each part of the system does and where performance work should be done first.

## What this system does

This is a multi-company ERP. It handles sales, procurement, inventory, finance, payroll, HR, projects, helpdesk, and business workflow from the first customer contact all the way through to project delivery and payment. It also supports role-based dashboards so that each employee sees only what is relevant to their job.

---

## 1. Core Access & Control Modules

These modules manage who can log in, what they can see, and how companies are organized.

- **Admin Dashboard** — The main dashboard for administrators. Shows high-level KPIs for the whole company.
- **Role-Based Dashboard** — A personalized dashboard for each role (ICT Engineer, Finance, HR, etc.). Each role sees different KPI cards, charts, and recent items.
- **Companies** — Manages multiple companies and subsidiaries. Supports switching between companies and consolidated reporting.
- **Intercompany Transactions** — Records money or stock moving between companies in the same group.
- **Users** — Add, edit, deactivate, impersonate, and manage users. Includes login history.
- **Roles & Permissions** — Defines who can access which menu and perform which action.
- **Audit Logs** — Tracks who changed what and when.
- **System Mode & Backup** — Puts the system in maintenance mode and downloads database backups.
- **Profile** — Each user changes their own password, avatar, and profile details.

---

## 2. Sales & Customer Relationship Modules

These modules manage customers from the first lead to the final invoice.

- **Sales Dashboard** — Overview of sales performance.
- **Sales Invoices** — Create customer invoices, post them, print PDFs, and send receipts.
- **Sales Returns** — Handle customer returns and credit notes.
- **Sales Proposals** — Create quotations that can be turned into invoices or projects.
- **CRM Leads** — Track potential customers and their contact details.
- **CRM Deals** — Track sales opportunities and deal value.
- **CRM Contacts** — Store customer and supplier contact information.
- **CRM Contracts** — Store agreements with customers.
- **Quotations** — Formal price quotes that can be converted into deals.
- **Call Center** — Track marketing campaigns and calls made to leads/customers.

---

## 3. Procurement & Inventory Modules

These modules manage buying, stock, warehouses, and supplier records.

- **Purchase Invoices** — Record bills received from suppliers.
- **Purchase Returns** — Record goods returned to suppliers.
- **Products** — Product catalog with stock levels, prices, and categories.
- **Product Categories** — Group products into categories.
- **Warehouses** — Store locations for inventory.
- **Suppliers** — Vendor directory and contact details.
- **Stock Movements** — Log of stock coming in and going out.
- **Transfers** — Move stock between warehouses.
- **LPOs (Local Purchase Orders)** — Internal purchase approval documents.
- **GRNs (Goods Received Notes)** — Confirm goods received from a supplier.
- **Delivery Notes** — Confirm goods delivered to a customer.
- **Vendor Invoices** — Bills from vendors linked to GRNs and LPOs.
- **Vendor Payments** — Record payments made to vendors.
- **POS (Point of Sale)** — Cash sales and daily sales reports.

---

## 4. Finance & Accounting Modules

These modules track money in and out, bank accounts, fixed assets, and payroll.

- **Bank Accounts** — List of company bank accounts and balances.
- **Account Transfers** — Move money between internal accounts.
- **Bank Transfers** — Bulk or single bank transfer records.
- **Expenses** — General business expenses.
- **Revenues** — General revenue entries.
- **Bills** — Accounts payable bills.
- **Estimates** — Cost estimates before a sale is confirmed.
- **Office Expenses** — Expenses that require manager approval before payment.
- **Client Receipts** — Money received from customers.
- **Fixed Assets** — Asset register, depreciation, and disposal.
- **Payroll** — Calculate salaries, generate payslips, and download PDFs.
- **Salary Advance** — Staff can request a salary advance.

---

## 5. Human Resources (HRM) Modules

These modules manage employees, attendance, leave, recruitment, and performance.

- **Employees** — Employee records and personal details.
- **Attendance** — Clock in/out, daily attendance records, and bulk clock-out.
- **Leaves** — Leave requests, approval, and balances.
- **Performance Reviews** — Employee appraisals and reviews.
- **Training** — Training records and schedules.
- **Job Postings** — Open positions advertised internally or externally.
- **Applications** — Job applications received from candidates.
- **Employee Assets** — Company assets assigned to employees (laptops, phones, etc.).
- **HR Events** — Company events and HR calendar.
- **Policies** — Company policies and documents.
- **Bonuses** — Bonus requests and approvals.
- **Payslip Preview/Download** — View or download a single payslip.

---

## 6. Projects & Operations Modules

These modules manage projects, time, support tickets, meetings, and company vehicles.

- **Projects** — Create projects, add tasks, track progress, and generate settlements/invoices.
- **Timesheets** — Record time spent on project tasks.
- **Bugs / Issues** — Track defects or issues on projects.
- **Project Budgets** — Set and approve project budgets.
- **Project Profit** — Compare project income vs cost to see profit.
- **Helpdesk Categories** — Group support tickets by category.
- **Helpdesk Tickets** — Customer or internal support requests with replies and status updates.
- **Meetings** — Schedule meetings, record attendance, and track action points.
- **Fleet** — Manage vehicles, fuel logs, and maintenance records.

---

## 7. Business Flow — The Complete Pipeline

Business Flow is the chain that connects the sales, procurement, inventory, project, and finance modules together.

The normal flow is:

1. **Tender** — A sales opportunity or tender is registered.
2. **Lead** — The tender becomes a CRM lead.
3. **Quotation** — A formal quote is sent to the customer.
4. **Deal** — The customer accepts the quote and it becomes a deal.
5. **Project** — The deal is turned into a project.
6. **Budget** — A project budget is created and approved.
7. **LPO** — A local purchase order is raised for materials or services.
8. **GRN** — Goods are received and recorded.
9. **Delivery Note** — Deliverables are sent to the customer.
10. **Vendor Invoice** — The supplier sends a bill.
11. **Vendor Payment** — The supplier is paid.
12. **Client Receipt** — The customer pays the company.
13. **Office Expense** — Internal expenses are approved and recorded.
14. **Project Profit** — Final profit on the project is calculated.

### Business Flow modules explained

- **Business Flow Dashboard** — A single screen showing how many tenders, leads, quotations, deals, LPOs, GRNs, delivery notes, vendor invoices, and receipts are in each stage.
- **Tenders** — Capture public or private tenders before they become leads.
- **Quotations** — Send quotes to customers and convert them into deals.
- **CRM Leads & Deals** — Manage the sales pipeline.
- **LPOs** — Raise and approve purchase orders.
- **GRNs** — Confirm goods received.
- **Delivery Notes** — Confirm goods/services delivered.
- **Vendor Invoices & Payments** — Pay suppliers.
- **Client Receipts** — Record customer payments.
- **Office Expenses** — Approve and track internal expenses.
- **Project Budgets** — Approve project spending limits.
- **Project Profit** — Report the final profit of a project.

---

## 8. Settings & Utility Modules

- **Settings** — General configuration such as company name, currency, and tax settings.
- **Email Templates** — Templates for system emails.
- **Notification Templates** — SMS/email templates.
- **Add-ons** — Turn optional features on or off.
- **Messenger** — Internal chat between users.
- **Media** — File manager for uploaded documents and images.
- **Documentation** — Built-in documentation editor.

---

## 9. Public / Reception Modules

These modules are used by visitors or reception staff.

- **Careers** — Public job listing page where candidates can apply.
- **Reception Messages** — Log and track visitor messages.
- **Salary Advance (Reception)** — Staff can submit salary advance requests at reception.

---

## 10. Mobile / API Modules

The mobile app and external integrations use these API endpoints.

- **API Authentication** — Login, register, and get user/token details.
- **API Dashboard** — Role-based dashboard data for the mobile app.
- **API Reports** — Financial and inventory KPI endpoints.
- **API Multi-Company** — Switch and manage companies from the mobile app.
- **API HRM** — Attendance, leave, and payroll data for mobile users.
- **API Helpdesk** — View and update support tickets from mobile.
- **API Reports** — Detailed financial and inventory reports.

---

## Performance Status & Priority Plan

### Already optimized

- **Role-Based Dashboard** — Now uses 5-minute caching for stats, recent items, and charts. Chart queries were reduced from 14 separate daily queries to a single grouped query.
- **Login 500 error** — Fixed the broken `audit-logs` route in the admin layout.

### Recommended next optimization order

1. **Business Flow Dashboard** — This is the most cross-module screen and is likely to be slow because it reads from many tables at once (tenders, leads, deals, LPOs, GRNs, delivery notes, vendor invoices, receipts).
2. **Sales Invoices & Sales Dashboard** — High daily transaction volume; good candidates for indexing and caching.
3. **Helpdesk Tickets** — Ticket counts are shown on many dashboards and can be slow with large data.
4. **Projects & Project Profit** — Projects joined with tasks, timesheets, expenses, and invoices can be heavy.
5. **Payroll** — Monthly generation can be slow if it loops over all employees.
6. **Inventory / Stock Movements** — Stock level queries are frequent and should be indexed.
7. **HRM modules** — Attendance, leave, and employee reports are used daily.

### Common optimization techniques to apply

- **Database indexes** on status columns, date columns, and foreign keys.
- **Caching** for dashboard KPIs and reports that do not change every second.
- **Eager loading** (`with(...)`) to avoid loading related records one by one.
- **Grouped queries** instead of looping through dates and running one query per day.
- **Pagination** for any list that could grow beyond a few hundred rows.
- **Background jobs** for heavy reports or payroll generation so the user does not wait.

---

## How to use this guide

1. Pick one module from the list above.
2. Read what it does and who uses it.
3. Check its performance status.
4. Optimize using the techniques listed.
5. Test the module after changes before moving to the next one.
