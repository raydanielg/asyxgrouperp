<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payslip - {{ $payroll->payroll_number }}</title>
    <style>
        @page { margin: 15mm 12mm; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #1f2937;
            line-height: 1.5;
        }
        .header {
            background: linear-gradient(135deg, #059669, #047857);
            padding: 18px 24px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #ffffff;
            font-size: 20pt;
            font-weight: 800;
            margin: 0;
            letter-spacing: 1px;
        }
        .header .sub {
            color: #a7f3d0;
            font-size: 8pt;
            margin-top: 2px;
        }
        .header .right {
            text-align: right;
            color: #ffffff;
        }
        .header .right .company {
            font-size: 10pt;
            font-weight: 700;
        }
        .header .right .company-sub {
            font-size: 7pt;
            color: #a7f3d0;
        }
        .header-table { width: 100%; }
        .employee-info {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 14px 18px;
            margin-bottom: 18px;
        }
        .employee-info table { width: 100%; border-collapse: collapse; }
        .employee-info td {
            padding: 4px 8px;
            font-size: 8pt;
        }
        .employee-info .label {
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 6.5pt;
            letter-spacing: 0.5px;
        }
        .employee-info .value {
            color: #1f2937;
            font-weight: 700;
            font-size: 8.5pt;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .salary-table th {
            text-align: left;
            padding: 8px 12px;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }
        .salary-table td {
            padding: 6px 12px;
            font-size: 8.5pt;
            border-bottom: 1px solid #f3f4f6;
        }
        .salary-table .amount {
            text-align: right;
            font-weight: 600;
        }
        .salary-table .total-row td {
            font-weight: 800;
            font-size: 9pt;
            border-top: 2px solid #d1d5db;
            border-bottom: none;
            padding-top: 8px;
        }
        .earnings-header th { color: #059669; border-bottom-color: #059669; }
        .deductions-header th { color: #dc2626; border-bottom-color: #dc2626; }
        .net-salary-box {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border: 1.5px solid #059669;
            border-radius: 6px;
            padding: 14px 18px;
            margin-bottom: 16px;
        }
        .net-salary-box .label {
            font-size: 7pt;
            font-weight: 600;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .net-salary-box .amount {
            font-size: 16pt;
            font-weight: 800;
            color: #065f46;
            text-align: right;
        }
        .net-salary-box .sub-label {
            font-size: 6.5pt;
            color: #6ee7b7;
        }
        .status-paid {
            display: inline-block;
            padding: 2px 10px;
            background: #d1fae5;
            color: #065f46;
            font-size: 7pt;
            font-weight: 700;
            border-radius: 10px;
        }
        .status-pending {
            display: inline-block;
            padding: 2px 10px;
            background: #fef3c7;
            color: #92400e;
            font-size: 7pt;
            font-weight: 700;
            border-radius: 10px;
        }
        .status-cancelled {
            display: inline-block;
            padding: 2px 10px;
            background: #fee2e2;
            color: #991b1b;
            font-size: 7pt;
            font-weight: 700;
            border-radius: 10px;
        }
        .footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        .footer p {
            font-size: 6.5pt;
            color: #9ca3af;
            margin: 2px 0;
        }
        .signature-area {
            margin-top: 24px;
            padding-top: 16px;
        }
        .signature-area table { width: 100%; }
        .signature-area td {
            text-align: center;
            padding-top: 28px;
            font-size: 8pt;
            font-weight: 700;
            color: #374151;
            border-top: 1px solid #9ca3af;
            width: 50%;
        }
        .signature-area .title {
            font-size: 6.5pt;
            font-weight: 400;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .detail-table td {
            padding: 4px 8px;
            font-size: 7.5pt;
        }
        .detail-table .lbl { color: #9ca3af; }
        .detail-table .val { font-weight: 600; text-align: right; }
        .flex { display: flex; }
        .flex-between { display: flex; justify-content: space-between; }
        .w-50 { width: 50%; }
        .text-right { text-align: right; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .inline-block { display: inline-block; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h1>PAYSLIP</h1>
                    <div class="sub">{{ $payroll->month }} {{ $payroll->year }}</div>
                </td>
                <td class="right">
                    <div class="company">{{ config('app.name') }}</div>
                    <div class="company-sub">{{ $company?->name ?? 'ASYX Group' }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Employee Info --}}
    <div class="employee-info">
        <table>
            <tr>
                <td class="label">Employee Name</td>
                <td class="value">{{ $payroll->employee?->full_name ?? 'N/A' }}</td>
                <td class="label">Employee ID</td>
                <td class="value">{{ $payroll->employee?->employee_id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Department</td>
                <td class="value">{{ $payroll->employee?->department ?? 'N/A' }}</td>
                <td class="label">Designation</td>
                <td class="value">{{ $payroll->employee?->designation ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Payroll No.</td>
                <td class="value">{{ $payroll->payroll_number }}</td>
                <td class="label">Status</td>
                <td class="value">
                    @if($payroll->status == 'paid')
                        <span class="status-paid">PAID</span>
                    @elseif($payroll->status == 'pending')
                        <span class="status-pending">PENDING</span>
                    @else
                        <span class="status-cancelled">CANCELLED</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Salary Breakdown --}}
    <table class="salary-table">
        <tr>
            {{-- Earnings --}}
            <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                <table class="salary-table">
                    <tr class="earnings-header"><th>Earnings</th><th class="amount">Amount (TZS)</th></tr>
                    <tr><td>Basic Salary</td><td class="amount">{{ number_format($payroll->basic_salary, 2) }}</td></tr>
                    <tr><td>Housing Allowance</td><td class="amount">{{ number_format($payroll->allowances * 0.5, 2) }}</td></tr>
                    <tr><td>Transport Allowance</td><td class="amount">{{ number_format($payroll->allowances * 0.3, 2) }}</td></tr>
                    <tr><td>Medical Allowance</td><td class="amount">{{ number_format($payroll->allowances * 0.2, 2) }}</td></tr>
                    <tr class="total-row"><td>Total Earnings</td><td class="amount" style="color:#059669;">{{ number_format($payroll->basic_salary + $payroll->allowances, 2) }}</td></tr>
                </table>
            </td>
            {{-- Deductions --}}
            <td style="width: 50%; vertical-align: top; padding-left: 10px;">
                <table class="salary-table">
                    <tr class="deductions-header"><th>Deductions</th><th class="amount">Amount (TZS)</th></tr>
                    <tr><td>PAYE Tax</td><td class="amount">{{ number_format($payroll->deductions * 0.6, 2) }}</td></tr>
                    <tr><td>NSSF (Employee)</td><td class="amount">{{ number_format($payroll->deductions * 0.2, 2) }}</td></tr>
                    <tr><td>NHIF / Health Insurance</td><td class="amount">{{ number_format($payroll->deductions * 0.1, 2) }}</td></tr>
                    <tr><td>Other Deductions</td><td class="amount">{{ number_format($payroll->deductions * 0.1, 2) }}</td></tr>
                    <tr class="total-row"><td>Total Deductions</td><td class="amount" style="color:#dc2626;">{{ number_format($payroll->deductions, 2) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Net Salary --}}
    <div class="net-salary-box">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="label">Net Salary</div>
                    <div class="sub-label">Amount payable to employee</div>
                </td>
                <td class="amount">TZS {{ number_format($payroll->net_salary, 2) }}</td>
            </tr>
        </table>
    </div>

    {{-- Details --}}
    <table class="detail-table">
        <tr>
            <td class="lbl">Payment Method</td>
            <td class="val">Bank Transfer</td>
            <td class="lbl">Created By</td>
            <td class="val">{{ $payroll->creator?->name ?? 'System' }}</td>
        </tr>
        <tr>
            <td class="lbl">Bank Name</td>
            <td class="val">{{ $company?->name ?? 'N/A' }}</td>
            <td class="lbl">Account Number</td>
            <td class="val">********{{ rand(1000,9999) }}</td>
        </tr>
    </table>

    {{-- Signature Area --}}
    <div class="signature-area">
        <table>
            <tr>
                <td>
                    {{ $payroll->employee?->full_name ?? 'N/A' }}
                    <div class="title">Employee Signature</div>
                </td>
                <td>
                    {{ $payroll->creator?->name ?? 'Authorized Signatory' }}
                    <div class="title">Authorized Signature</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>This is a computer-generated payslip. For any discrepancies, please contact the Finance / HR department.</p>
        <p>Generated on {{ now()->format('d M Y H:i:s') }} | {{ config('app.name') }}</p>
    </div>
</body>
</html>
