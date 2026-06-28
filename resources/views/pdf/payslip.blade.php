<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payslip - {{ $payroll->payroll_number }}</title>
    <style>
        @page { margin: 0; size: A4 portrait; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #1f2937;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            width: 210mm;
            min-height: 297mm;
        }
        .page { padding: 32px 36px; }

        .header-gradient {
            background: linear-gradient(135deg, #024938, #047857, #059669);
            padding: 28px 36px;
            margin: -32px -36px 0;
        }
        .header-table { width: 100%; }
        .header-table td { vertical-align: top; }
        .logo-box {
            width: 48px; height: 48px;
            background: rgba(255,255,255,0.12);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .header-title { font-size: 22pt; font-weight: 900; color: #ffffff; margin: 0; letter-spacing: 0.5px; }
        .header-sub { font-size: 7.5pt; color: #a7f3d0; font-weight: 600; margin: 2px 0 0; }
        .header-right { text-align: right; color: #ffffff; }
        .header-right .name { font-size: 10pt; font-weight: 800; }
        .header-right .meta { font-size: 6.5pt; color: #a7f3d0; margin-top: 2px; }

        .section-title {
            font-size: 7pt; font-weight: 700; color: #6b7280;
            text-transform: uppercase; letter-spacing: 1.5px;
        }

        .employee-card {
            margin: 20px 0;
        }
        .employee-card .name {
            font-size: 16pt; font-weight: 800; color: #111827;
        }
        .employee-card .badge {
            display: inline-block;
            padding: 2px 10px;
            background: #f3f4f6;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: 600;
            color: #6b7280;
            margin-right: 6px;
        }
        .employee-card .status-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 8pt;
            font-weight: 800;
        }
        .employee-card .status-paid { background: #d1fae5; color: #065f46; }
        .employee-card .status-pending { background: #fef3c7; color: #92400e; }
        .employee-card .status-cancelled { background: #fee2e2; color: #991b1b; }

        .salary-grid { margin: 20px 0; }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
        }
        .salary-table th {
            padding: 8px 14px;
            font-size: 6.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            text-align: left;
        }
        .salary-table td {
            padding: 6px 14px;
            font-size: 8pt;
            border-bottom: 1px solid #f3f4f6;
        }
        .salary-table .amt { text-align: right; font-weight: 600; }
        .salary-table .total td { font-weight: 900; font-size: 8.5pt; padding: 8px 14px; }

        .earnings-table { background: #f0fdf4; border: 1px solid #bbf7d0; }
        .earnings-table th { background: #dcfce7; color: #166534; border-bottom: 1px solid #bbf7d0; }
        .earnings-table .total { background: #dcfce7; color: #166534; }
        .earnings-table .total td { border-top: 1px solid #bbf7d0; }

        .deductions-table { background: #fef2f2; border: 1px solid #fecaca; }
        .deductions-table th { background: #fee2e2; color: #991b1b; border-bottom: 1px solid #fecaca; }
        .deductions-table .total { background: #fee2e2; color: #991b1b; }
        .deductions-table .total td { border-top: 1px solid #fecaca; }

        .net-salary-box {
            background: linear-gradient(135deg, #059669, #047857, #024938);
            border-radius: 10px;
            padding: 18px 24px;
            margin: 20px 0;
        }
        .net-salary-box .label {
            font-size: 7pt; font-weight: 700; color: #a7f3d0;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .net-salary-box .sub-label { font-size: 7pt; color: #6ee7b7; }
        .net-salary-box .amount {
            font-size: 22pt; font-weight: 900; color: #ffffff; text-align: right;
        }
        .net-salary-box .period { font-size: 7pt; color: #6ee7b7; text-align: right; }

        .detail-grid { margin: 16px 0; }
        .detail-box {
            background: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 8px;
            padding: 10px 14px;
            display: inline-block;
            width: 30%;
            margin-right: 2%;
            vertical-align: top;
        }
        .detail-box .lbl { font-size: 6pt; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; }
        .detail-box .val { font-size: 8.5pt; font-weight: 700; color: #1f2937; margin-top: 2px; }

        .signature-area { margin: 24px 0 16px; }
        .signature-table { width: 100%; }
        .signature-table td {
            text-align: center;
            padding-top: 8px;
            width: 50%;
        }
        .signature-table .line {
            width: 160px; height: 1px;
            background: #9ca3af;
            margin: 0 auto 8px;
        }
        .signature-table .name { font-size: 9pt; font-weight: 800; color: #374151; }
        .signature-table .role { font-size: 6.5pt; color: #9ca3af; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #f3f4f6;
        }
        .footer p { font-size: 6pt; color: #9ca3af; margin: 0; }
        .footer .pull-right { text-align: right; }
    </style>
</head>
<body>
    <div class="page">
        {{-- Header --}}
        <div class="header-gradient">
            <table class="header-table">
                <tr>
                    <td style="width: 60%;">
                        <table>
                            <tr>
                                <td style="vertical-align: middle; padding-right: 14px;">
                                    <div class="logo-box">
                                        <svg width="30" height="30" viewBox="0 0 200 60">
                                            <rect x="0" y="0" width="200" height="60" rx="8" fill="rgba(255,255,255,0.2)"/>
                                            <text x="16" y="38" font-family="Arial Black, sans-serif" font-size="20" font-weight="900" fill="#f9ac00">ASYX</text>
                                            <text x="96" y="38" font-family="Arial, sans-serif" font-size="11" font-weight="600" fill="#ffffff">GROUP</text>
                                            <text x="16" y="52" font-family="Arial, sans-serif" font-size="7" fill="#a7f3d0" letter-spacing="1.5">ERP SYSTEM</text>
                                        </svg>
                                    </div>
                                </td>
                                <td style="vertical-align: middle;">
                                    <h1 class="header-title">PAYSLIP</h1>
                                    <p class="header-sub">{{ $payroll->month }} {{ $payroll->year }} &bull; {{ $payroll->payroll_number }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="header-right">
                        <div class="name">{{ config('app.name') }}</div>
                        <div class="meta">{{ $company?->name ?? 'Enterprise ERP' }}<br>{{ now()->format('d/m/Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Employee Info --}}
        <div class="employee-card">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 55%;">
                        <div class="section-title">Employee</div>
                        <div class="name">{{ $payroll->employee?->full_name ?? 'N/A' }}</div>
                        <div style="margin-top: 6px;">
                            <span class="badge">{{ $payroll->employee?->employee_id ?? 'N/A' }}</span>
                            <span class="badge">{{ $payroll->employee?->department ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td style="width: 25%;">
                        <div class="section-title">Designation</div>
                        <div style="font-size: 9pt; font-weight: 700; margin-top: 2px;">{{ $payroll->employee?->designation ?? 'N/A' }}</div>
                    </td>
                    <td style="width: 20%; text-align: right;">
                        <div class="section-title">Status</div>
                        <div style="margin-top: 4px;">
                            @php $s = $payroll->status @endphp
                            <span class="status-badge status-{{ $s }}">{{ strtoupper($s) }}</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Salary Breakdown Grid --}}
        <div class="salary-grid">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 48%; vertical-align: top; padding-right: 2%;">
                        <table class="salary-table earnings-table">
                            <thead><tr><th>Earnings</th><th class="amt">Amount (TZS)</th></tr></thead>
                            <tbody>
                                <tr><td>Basic Salary</td><td class="amt">{{ number_format($payroll->basic_salary, 2) }}</td></tr>
                                <tr><td>Housing Allowance</td><td class="amt">{{ number_format($payroll->allowances * 0.5, 2) }}</td></tr>
                                <tr><td>Transport Allowance</td><td class="amt">{{ number_format($payroll->allowances * 0.3, 2) }}</td></tr>
                                <tr><td>Medical Allowance</td><td class="amt">{{ number_format($payroll->allowances * 0.2, 2) }}</td></tr>
                                <tr class="total"><td>Total Earnings</td><td class="amt">{{ number_format($payroll->basic_salary + $payroll->allowances, 2) }}</td></tr>
                            </tbody>
                        </table>
                    </td>
                    <td style="width: 48%; vertical-align: top; padding-left: 2%;">
                        <table class="salary-table deductions-table">
                            <thead><tr><th>Deductions</th><th class="amt">Amount (TZS)</th></tr></thead>
                            <tbody>
                                <tr><td>PAYE Tax</td><td class="amt">{{ number_format($payroll->deductions * 0.6, 2) }}</td></tr>
                                <tr><td>NSSF (Employee)</td><td class="amt">{{ number_format($payroll->deductions * 0.2, 2) }}</td></tr>
                                <tr><td>NHIF / Health Insurance</td><td class="amt">{{ number_format($payroll->deductions * 0.1, 2) }}</td></tr>
                                <tr><td>Other Deductions</td><td class="amt">{{ number_format($payroll->deductions * 0.1, 2) }}</td></tr>
                                <tr class="total"><td>Total Deductions</td><td class="amt">{{ number_format($payroll->deductions, 2) }}</td></tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Net Salary Box --}}
        <div class="net-salary-box">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 60%;">
                        <div class="label">Net Salary</div>
                        <div class="sub-label">Amount payable to employee</div>
                    </td>
                    <td style="width: 40%;">
                        <div class="amount">TZS {{ number_format($payroll->net_salary, 2) }}</div>
                        <div class="period">{{ ucfirst($payroll->month) }} {{ $payroll->year }}</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Payment Details --}}
        <div class="detail-grid">
            <div class="detail-box">
                <div class="lbl">Payment Method</div>
                <div class="val">Bank Transfer</div>
            </div>
            <div class="detail-box">
                <div class="lbl">Created By</div>
                <div class="val">{{ $payroll->creator?->name ?? 'System' }}</div>
            </div>
            <div class="detail-box" style="margin-right: 0;">
                <div class="lbl">Payment Date</div>
                <div class="val">{{ $payroll->created_at?->format('d M Y') ?? now()->format('d M Y') }}</div>
            </div>
        </div>

        {{-- Signatures --}}
        <div class="signature-area">
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="line"></div>
                        <div class="name">{{ $payroll->employee?->full_name ?? 'N/A' }}</div>
                        <div class="role">Employee Signature</div>
                    </td>
                    <td>
                        <div class="line"></div>
                        <div class="name">{{ $payroll->creator?->name ?? 'Authorized Signatory' }}</div>
                        <div class="role">Authorized Signature</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <table style="width: 100%;">
                <tr>
                    <td><p>This is a computer-generated payslip. For discrepancies contact Finance/HR.</p></td>
                    <td class="pull-right"><p>Generated {{ now()->format('d M Y H:i:s') }}</p></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
