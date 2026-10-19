<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Payslip — {{ $payroll->month }} {{ $payroll->year }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; color: #111; font-size: 12px; background: #f3f4f6; padding: 24px; }
    .sheet { background: #fff; max-width: 720px; margin: 0 auto; padding: 44px 48px; }
    @media print { body { background: #fff; padding: 0; } }

    .hdr { overflow: hidden; margin-bottom: 34px; }
    .hdr .left { float: left; }
    .hdr .right { float: right; text-align: right; }
    .co-name { font-size: 14px; font-weight: bold; }
    .co-meta { font-size: 10px; color: #444; margin-top: 4px; line-height: 1.7; }
    .doc { font-size: 20px; font-weight: bold; letter-spacing: .16em; padding-top: 2px; }

    .info { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
    .info td { vertical-align: top; font-size: 11px; padding: 2px 0; }
    .info .lbl { font-size: 9px; text-transform: uppercase; letter-spacing: .08em; color: #666; font-weight: bold; padding-bottom: 4px; }
    .info .val { font-size: 12px; color: #111; }

    table.lines { width: 100%; border-collapse: collapse; margin-bottom: 26px; }
    table.lines th { text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #444; border-bottom: 1.5px solid #111; padding: 0 0 8px; font-weight: bold; }
    table.lines th.r { text-align: right; }
    table.lines td { padding: 9px 0; font-size: 11.5px; color: #111; }
    table.lines td.r { text-align: right; }
    table.lines tr.total td { font-weight: bold; border-top: 1.5px solid #111; }

    .netbar { margin-top: 6px; border: 1.5px solid #111; padding: 14px 16px; overflow: hidden; }
    .netbar .l { float: left; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: .1em; padding-top: 3px; }
    .netbar .r { float: right; font-size: 16px; font-weight: bold; }

    .foot { margin-top: 40px; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>

<div class="sheet">
    <div class="hdr">
        <div class="left">
            <div class="co-name">{{ $company?->legal_name ?? $company?->name ?? config('app.name') }}</div>
            <div class="co-meta">
                {{ trim(($company?->address ?? 'Dar es Salaam') . ($company?->city ? ', ' . $company->city : '')) }}<br>
                @if($company?->tin) TIN: {{ $company->tin }}@endif
            </div>
        </div>
        <div class="right">
            <div class="doc">PAYSLIP</div>
        </div>
    </div>

    <table class="info">
        <tr>
            <td style="width:38%;">
                <div class="lbl">Employee</div>
                <div class="val">{{ $payroll->employee?->full_name ?? '—' }}</div>
                <div class="val" style="font-size:10.5px;color:#555;">{{ $payroll->employee?->designation ?? '' }}</div>
            </td>
            <td style="width:31%;">
                <div class="lbl">Employee No.</div>
                <div class="val">{{ $payroll->employee?->employee_id ?? '—' }}</div>
                <div class="lbl" style="padding-top:10px;">NSSF No.</div>
                <div class="val">NS-{{ str_pad($payroll->employee_id ?? 0, 5, '0', STR_PAD_LEFT) }}</div>
            </td>
            <td style="width:31%;">
                <div class="lbl">Pay Period</div>
                <div class="val">{{ $payroll->month }} {{ $payroll->year }}</div>
                <div class="lbl" style="padding-top:10px;">Payment Date</div>
                <div class="val">{{ $payroll->created_at?->format('d M Y') ?? now()->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="lines">
        <tr><th>Earnings</th><th class="r">TZS</th></tr>
        <tr><td>Basic salary</td><td class="r">{{ number_format($payroll->basic_salary) }}</td></tr>
        @if(($payroll->allowances ?? 0) > 0)
        <tr><td>Allowances</td><td class="r">{{ number_format($payroll->allowances) }}</td></tr>
        @endif
        @if(($payroll->overtime ?? 0) > 0)
        <tr><td>Overtime</td><td class="r">{{ number_format($payroll->overtime) }}</td></tr>
        @endif
        <tr class="total"><td>Total earnings</td><td class="r">{{ number_format($payroll->basic_salary + ($payroll->allowances ?? 0) + ($payroll->overtime ?? 0)) }}</td></tr>
    </table>

    <table class="lines">
        <tr><th>Deductions</th><th class="r">TZS</th></tr>
        @if(($payroll->deductions ?? 0) > 0)
        <tr><td>NSSF (10%)</td><td class="r">{{ number_format($payroll->deductions * 0.2) }}</td></tr>
        <tr><td>PAYE (income tax)</td><td class="r">{{ number_format($payroll->deductions * 0.6) }}</td></tr>
        <tr><td>NHIF / Other</td><td class="r">{{ number_format($payroll->deductions * 0.2) }}</td></tr>
        @else
        <tr><td colspan="2" style="color:#999;">No deductions</td></tr>
        @endif
        <tr class="total"><td>Total deductions</td><td class="r">{{ number_format($payroll->deductions ?? 0) }}</td></tr>
    </table>

    <div class="netbar">
        <span class="l">Net Pay</span>
        <span class="r">TZS {{ number_format($payroll->net_salary) }}</span>
    </div>

    <div class="foot">
        {{ $company?->legal_name ?? config('app.name') }} &middot; Payslip generated {{ now()->format('d M Y') }}
    </div>
</div>
</body>
</html>
