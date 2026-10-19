<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Payslip — {{ $payroll->payroll_number }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; color: #111; font-size: 12px; background: #f3f4f6; padding: 24px; }
    .sheet { background: #fff; max-width: 760px; margin: 0 auto; padding: 36px 40px; }
    @media print { body { background: #fff; padding: 0; } .no-print { display: none !important; } }

    .hdr { overflow: hidden; padding-bottom: 16px; border-bottom: 2px solid #111; }
    .hdr .left { float: left; }
    .hdr .right { float: right; text-align: right; }
    .co-name { font-size: 16px; font-weight: bold; letter-spacing: .02em; }
    .co-meta { font-size: 10px; color: #555; margin-top: 3px; line-height: 1.6; }
    .doc { font-size: 20px; font-weight: bold; letter-spacing: .14em; }
    .doc-meta { font-size: 10px; color: #555; margin-top: 3px; }
    .stamp { display: inline-block; margin-top: 6px; padding: 2px 10px; border: 1.5px solid; font-size: 9px; font-weight: bold; letter-spacing: .15em; text-transform: uppercase; }
    .stamp-paid, .stamp-pending, .stamp-cancelled { color: #111; border-color: #111; }

    .info { width: 100%; border-collapse: collapse; margin-top: 18px; }
    .info td { vertical-align: top; font-size: 11px; padding: 0; }
    .lbl { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #777; font-weight: bold; margin-bottom: 4px; }
    .val { font-size: 12.5px; font-weight: bold; color: #111; }
    .sub { font-size: 10.5px; color: #555; line-height: 1.6; margin-top: 2px; }

    table.lines { width: 48%; border-collapse: collapse; margin-top: 22px; }
    table.lines th { text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: .08em; color: #555; border-bottom: 1.5px solid #111; padding: 0 4px 7px; }
    table.lines th.r, table.lines td.r { text-align: right; }
    table.lines td { padding: 8px 4px; border-bottom: 1px solid #ddd; font-size: 11px; }
    table.lines td.neg { color: #111; }
    table.lines tr.total td { font-weight: bold; border-bottom: none; border-top: 1.5px solid #111; padding-top: 9px; }

    .netbar { margin-top: 26px; border-top: 2px solid #111; border-bottom: 2px solid #111; padding: 12px 4px; overflow: hidden; }
    .netbar .l { float: left; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .12em; color: #111; padding-top: 4px; }
    .netbar .r { float: right; font-size: 18px; font-weight: bold; }
    .netbar .r small { font-size: 10px; font-weight: normal; color: #777; }

    .sigs { margin-top: 56px; overflow: hidden; }
    .sig { width: 40%; float: left; }
    .sig:last-child { float: right; }
    .sig .line { border-bottom: 1px solid #888; height: 26px; }
    .sig .cap { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #777; margin-top: 4px; }

    .foot { margin-top: 26px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>

<div class="sheet">
    <div class="hdr">
        <div class="left">
            <div class="co-name">{{ $company?->legal_name ?? $company?->name ?? config('app.name') }}</div>
            <div class="co-meta">
                {{ trim(($company?->address ?? 'Dar es Salaam') . ($company?->city ? ', ' . $company->city : '')) }}
                @if($company?->tin) &middot; TIN: {{ $company->tin }}@endif
            </div>
        </div>
        <div class="right">
            <div class="doc">PAYSLIP</div>
            <div class="doc-meta">{{ $payroll->payroll_number }}</div>
            <div><span class="stamp stamp-{{ $payroll->status }}">{{ $payroll->status }}</span></div>
        </div>
    </div>

    <table class="info">
        <tr>
            <td style="width:40%;">
                <div class="lbl">Employee</div>
                <div class="val">{{ $payroll->employee?->full_name ?? 'N/A' }}</div>
                <div class="sub">{{ $payroll->employee?->designation ?? '' }}{{ $payroll->employee?->designation && $payroll->employee?->department ? ' — ' : '' }}{{ $payroll->employee?->department ?? '' }}<br>ID: {{ $payroll->employee?->employee_id ?? '—' }}</div>
            </td>
            <td style="width:30%;">
                <div class="lbl">Period</div>
                <div class="val">{{ $payroll->month }} {{ $payroll->year }}</div>
            </td>
            <td style="width:30%;">
                <div class="lbl">Pay Date</div>
                <div class="val">{{ $payroll->created_at?->format('d M Y') ?? now()->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="lines" style="float:left;">
        <tr><th>Earnings</th><th class="r">Amount (TZS)</th></tr>
        <tr><td>Basic Salary</td><td class="r">{{ number_format($payroll->basic_salary, 2) }}</td></tr>
        @if(($payroll->overtime ?? 0) > 0)
        <tr><td>Overtime</td><td class="r">{{ number_format($payroll->overtime, 2) }}</td></tr>
        @endif
        @if(($payroll->allowances ?? 0) > 0)
        <tr><td>Allowances</td><td class="r">{{ number_format($payroll->allowances, 2) }}</td></tr>
        @endif
        <tr class="total"><td>Gross Earnings</td><td class="r">{{ number_format($payroll->basic_salary + ($payroll->allowances ?? 0) + ($payroll->overtime ?? 0), 2) }}</td></tr>
    </table>

    <table class="lines" style="float:right;">
        <tr><th>Deductions</th><th class="r">Amount (TZS)</th></tr>
        @if(($payroll->deductions ?? 0) > 0)
        <tr><td>PAYE</td><td class="r">&minus;{{ number_format($payroll->deductions * 0.6, 2) }}</td></tr>
        <tr><td>NSSF (10%)</td><td class="r">&minus;{{ number_format($payroll->deductions * 0.2, 2) }}</td></tr>
        <tr><td>NHIF</td><td class="r">&minus;{{ number_format($payroll->deductions * 0.1, 2) }}</td></tr>
        <tr><td>WCF / Other</td><td class="r">&minus;{{ number_format($payroll->deductions * 0.1, 2) }}</td></tr>
        @else
        <tr><td colspan="2" style="color:#999;">No deductions</td></tr>
        @endif
        <tr class="total"><td>Total Deductions</td><td class="r neg">&minus;{{ number_format($payroll->deductions ?? 0, 2) }}</td></tr>
    </table>

    <div style="clear:both;"></div>

    <div class="netbar">
        <span class="l">Net Pay</span>
        <span class="r">{{ number_format($payroll->net_salary, 2) }} <small>TZS</small></span>
    </div>

    <div class="sigs">
        <div class="sig"><div class="line"></div><div class="cap">Employee signature</div></div>
        <div class="sig"><div class="line"></div><div class="cap">Employer signature / stamp</div></div>
    </div>

    <div class="foot">
        {{ $company?->legal_name ?? config('app.name') }} &middot; Payslip generated {{ now()->format('d M Y') }} &middot; ASYX ERP
    </div>
</div>
</body>
</html>
