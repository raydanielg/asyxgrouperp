<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Payslip — {{ $payroll->month }} {{ $payroll->year }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; color: #111; font-size: 12px; background: #f3f4f6; padding: 24px; }
    .sheet { background: #fff; max-width: 720px; margin: 0 auto; padding: 40px 44px; }
    @media print { body { background: #fff; padding: 0; } }

    .co { text-align: center; margin-bottom: 24px; }
    .co .name { font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: .04em; }
    .co .meta { font-size: 9.5px; color: #555; margin-top: 3px; }

    .topline { overflow: hidden; margin-bottom: 6px; font-size: 12px; }
    .topline .l { float: left; font-weight: bold; }
    .topline .r { float: right; }

    .empname { font-size: 15px; font-weight: bold; text-transform: uppercase; clear: both; }
    .empsub { font-size: 10px; color: #555; margin-top: 2px; margin-bottom: 18px; }

    .period { overflow: hidden; margin-bottom: 20px; font-size: 11px; }
    .period .l { float: left; }
    .period .r { float: right; font-weight: bold; }

    table.lines { width: 100%; border-collapse: collapse; }
    table.lines th { text-align: left; font-size: 10px; font-weight: bold; border-bottom: 1.5px solid #111; padding: 0 0 6px; }
    table.lines th.r { text-align: right; }
    table.lines td { padding: 8px 0; font-size: 12px; color: #111; }
    table.lines td.r { text-align: right; }
    table.lines tr.total td { font-weight: bold; border-top: 1.5px solid #111; border-bottom: 1.5px solid #111; }

    .foot { margin-top: 36px; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>

<div class="sheet">

    <div class="co">
        <div class="name">{{ $company?->legal_name ?? $company?->name ?? 'ASYX Group Company Limited' }}</div>
        <div class="meta">
            {{ trim(($company?->address ?? 'Dar es Salaam') . ($company?->city ? ', ' . $company->city : '')) }}
            @if($company?->tin) &middot; TIN: {{ $company->tin }}@endif
        </div>
    </div>

    <div class="topline">
        <span class="l">PAYSLIP</span>
        <span class="r">{{ $payroll->payroll_number }}</span>
    </div>

    <div class="empname">{{ $payroll->employee?->full_name ?? 'N/A' }}</div>
    <div class="empsub">
        {{ $payroll->employee?->employee_id ?? '—' }}
        @if($payroll->employee?->designation) &middot; {{ $payroll->employee->designation }}@endif
        @if($payroll->employee?->department) &middot; {{ $payroll->employee->department }}@endif
    </div>

    <div class="period">
        <span class="l">Pay Period</span>
        <span class="r">{{ $payroll->month }} {{ $payroll->year }}</span>
    </div>

    <table class="lines">
        <tr>
            <th>DESCRIPTION</th>
            <th class="r" style="width:130px;">BALANCE</th>
            <th class="r" style="width:130px;">AMOUNT</th>
        </tr>
        <tr>
            <td>Basic Salary</td>
            <td class="r"></td>
            <td class="r">{{ number_format($payroll->basic_salary, 2) }}</td>
        </tr>
        @if(($payroll->allowances ?? 0) > 0)
        <tr>
            <td>Allowances</td>
            <td class="r"></td>
            <td class="r">{{ number_format($payroll->allowances, 2) }}</td>
        </tr>
        @endif
        @if(($payroll->overtime ?? 0) > 0)
        <tr>
            <td>Overtime</td>
            <td class="r"></td>
            <td class="r">{{ number_format($payroll->overtime, 2) }}</td>
        </tr>
        @endif
        @if(($payroll->deductions ?? 0) > 0)
        <tr>
            <td>Deductions (Taxes &amp; Contributions)</td>
            <td class="r"></td>
            <td class="r">-{{ number_format($payroll->deductions, 2) }}</td>
        </tr>
        @endif
        <tr class="total">
            <td>NET AMOUNT DUE</td>
            <td class="r">{{ ($payroll->paid_at ?? $payroll->created_at)?->format('d/m/Y') }}</td>
            <td class="r">{{ number_format($payroll->net_salary, 2) }}</td>
        </tr>
    </table>

    <div class="foot">
        {{ $company?->legal_name ?? config('app.name') }} &middot; Payslip generated {{ now()->format('d M Y') }}
    </div>
</div>
</body>
</html>
