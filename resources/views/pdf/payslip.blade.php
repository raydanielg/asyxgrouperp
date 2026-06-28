<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<title>Payslip &mdash; {{ $payroll->payroll_number }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap');

  *{box-sizing:border-box;}
  body{
    margin:36px auto;
    background:#EDE9DD;
    color:#1C2321;
    font-family:'Inter',sans-serif;
    max-width:760px;
  }

  .sheet{
    background:#fff;
    border-radius:6px;
    box-shadow:0 18px 40px -10px rgba(15,61,62,.25),0 0 0 1px #E3DDCB;
    position:relative;
    overflow:hidden;
  }
  .stamp{
    position:absolute;top:26px;right:-46px;
    font-size:12px;font-weight:700;letter-spacing:.12em;
    padding:6px 62px;
    transform:rotate(35deg);
    box-shadow:0 4px 10px rgba(0,0,0,.15);
    z-index:10;
  }
  .stamp-paid{background:#2F7A3D;color:#fff;}
  .stamp-pending{background:#C9A227;color:#23270F;}
  .stamp-cancelled{background:#B23A2E;color:#fff;}

  .head{
    display:flex;justify-content:space-between;align-items:flex-start;
    padding:38px 44px 26px;
    border-bottom:1px solid #E3DDCB;
  }
  .co-mark{display:flex;align-items:center;gap:12px;}
  .co-icon{
    width:38px;height:38px;border-radius:10px;
    background:conic-gradient(from 90deg,#C9A227,#8C5E2A,#0F3D3E,#C9A227);
    flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center;
  }
  .co-icon img{width:30px;height:30px;object-fit:contain;border-radius:4px;}
  .co-name{font-family:'Fraunces',serif;font-weight:700;font-size:17px;color:#0F3D3E;}
  .co-addr{font-size:11.5px;color:#6E7570;margin-top:3px;line-height:1.5;}

  .doc-title{text-align:right;}
  .doc-title h1{font-family:'Fraunces',serif;font-size:24px;margin:0 0 8px;color:#1C2321;}
  .doc-title .meta{font-size:12px;color:#6E7570;line-height:1.6;}
  .doc-title .meta b{color:#1C2321;}

  .body{padding:30px 44px;}

  .bill-row{display:flex;justify-content:space-between;margin-bottom:28px;gap:24px;}
  .bill-to .lbl{font-size:10.5px;text-transform:uppercase;letter-spacing:.1em;color:#6E7570;margin-bottom:6px;}
  .bill-to b{display:block;font-size:14.5px;color:#1C2321;}
  .bill-to .addr{font-size:12.5px;color:#6E7570;line-height:1.6;margin-top:2px;}
  .bill-to.right{text-align:right;}

  .cols{display:flex;gap:36px;margin-top:6px;}
  .col{flex:1;}

  table.lines{width:100%;border-collapse:collapse;font-size:13px;margin-bottom:8px;}
  table.lines th{
    text-align:left;font-size:10.5px;text-transform:uppercase;letter-spacing:.08em;
    color:#6E7570;border-bottom:1.5px solid #1C2321;padding:0 4px 10px;
  }
  table.lines th.r, table.lines td.r{text-align:right;}
  table.lines td{padding:12px 4px;border-bottom:1px solid #E3DDCB;color:#1C2321;}
  table.lines td.neg{color:#B23A2E;}
  table.lines tr.subtotal td{font-weight:700;border-bottom:none;padding-top:14px;color:#0F3D3E;}

  .net-bar{
    margin-top:24px;padding:18px 22px;border-radius:10px;
    background:#E2F0E5;display:flex;justify-content:space-between;align-items:center;
  }
  .net-bar span{font-size:12px;color:#2F7A3D;font-weight:600;text-transform:uppercase;letter-spacing:.04em;}
  .net-bar b{font-family:'JetBrains Mono',monospace;font-size:18px;color:#2F7A3D;}

  .foot{
    padding:18px 44px;border-top:1px solid #E3DDCB;
    background:#FBF9F2;
    font-size:11px;color:#6E7570;text-align:center;
  }
</style>
</head>
<body>
  <div class="sheet">
    @php $s = $payroll->status @endphp
    <div class="stamp stamp-{{ $s }}">{{ strtoupper($s) }}</div>

    <div class="head">
      <div class="co-mark">
        <div class="co-icon">
          <img src="{{ public_path('asyxgrouplogo.png') }}" alt="ASYX">
        </div>
        <div>
          <div class="co-name">{{ config('app.name') }}</div>
          <div class="co-addr">{{ $company?->name ?? 'ASYX Group' }}<br>Dar es Salaam, Tanzania</div>
        </div>
      </div>
      <div class="doc-title">
        <h1>Payslip</h1>
        <div class="meta">
          Kipindi: <b>{{ $payroll->month }} {{ $payroll->year }}</b><br>
          Tarehe ya Malipo: <b>{{ $payroll->created_at?->format('M d, Y') ?? now()->format('M d, Y') }}</b>
        </div>
      </div>
    </div>

    <div class="body">
      <div class="bill-row">
        <div class="bill-to">
          <div class="lbl">Mfanyakazi</div>
          <b>{{ $payroll->employee?->full_name ?? 'N/A' }}</b>
          <div class="addr">
            {{ $payroll->employee?->designation ?? '' }} &mdash; {{ $payroll->employee?->department ?? '' }}<br>
            Employee ID: {{ $payroll->employee?->employee_id ?? 'N/A' }}
          </div>
        </div>
        <div class="bill-to right">
          <div class="lbl">Mwajiri</div>
          <b>{{ config('app.name') }}</b>
          <div class="addr">
            TIN: 109-XXX-XXX<br>
            NSSF No: NS-{{ str_pad($payroll->employee_id ?? 0, 5, '0', STR_PAD_LEFT) }}<br>
            PAYE No: PY-{{ str_pad($payroll->id, 5, '0', STR_PAD_LEFT) }}
          </div>
        </div>
      </div>

      <div class="cols">
        <div class="col">
          <table class="lines">
            <tr><th>Mapato</th><th class="r">Kiasi</th></tr>
            <tr><td>Mshahara wa Msingi</td><td class="r">{{ number_format($payroll->basic_salary, 2) }} Tsh</td></tr>
            <tr><td>Posho ya Usafiri</td><td class="r">{{ number_format($payroll->allowances * 0.5, 2) }} Tsh</td></tr>
            <tr><td>Posho ya Mawasiliano</td><td class="r">{{ number_format($payroll->allowances * 0.3, 2) }} Tsh</td></tr>
            <tr><td>Posho ya Matibabu</td><td class="r">{{ number_format($payroll->allowances * 0.2, 2) }} Tsh</td></tr>
            <tr class="subtotal"><td>Jumla ya Mapato</td><td class="r">{{ number_format($payroll->basic_salary + $payroll->allowances, 2) }} Tsh</td></tr>
          </table>
        </div>
        <div class="col">
          <table class="lines">
            <tr><th>Makato</th><th class="r">Kiasi</th></tr>
            <tr><td>PAYE</td><td class="r neg">&minus;{{ number_format($payroll->deductions * 0.6, 2) }} Tsh</td></tr>
            <tr><td>NSSF (10%)</td><td class="r neg">&minus;{{ number_format($payroll->deductions * 0.2, 2) }} Tsh</td></tr>
            <tr><td>NHIF</td><td class="r neg">&minus;{{ number_format($payroll->deductions * 0.1, 2) }} Tsh</td></tr>
            <tr><td>WCF / Other</td><td class="r neg">&minus;{{ number_format($payroll->deductions * 0.1, 2) }} Tsh</td></tr>
            <tr class="subtotal" style="color:#B23A2E;"><td>Jumla ya Makato</td><td class="r neg">&minus;{{ number_format($payroll->deductions, 2) }} Tsh</td></tr>
          </table>
        </div>
      </div>

      <div class="net-bar">
        <span>Net Pay</span>
        <b>{{ number_format($payroll->net_salary, 2) }} Tsh</b>
      </div>
    </div>

    <div class="foot">
      Payslip Generated on {{ now()->format('l, F jS, Y') }} &middot; {{ config('app.name') }}
    </div>
  </div>
</body>
</html>
