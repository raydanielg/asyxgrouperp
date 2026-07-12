<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ASYX Group — Service Call Report - {{ $jobCard->job_number }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Barlow+Semi+Condensed:wght@500;600;700;800&family=Inter:wght@400;500;600;700&family=Caveat:wght@500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --red:#D91F26;
    --navy:#14235A;
    --navy-2:#1B2F73;
    --ink:#1A1D26;
    --line:#C7CCD8;
    --line-strong:#8F97AC;
    --fill:#F2F4F9;
    --pen:#1D3FBF;
    --paper:#FFFFFF;
  }
  *{margin:0;padding:0;box-sizing:border-box;}
  html{background:#E9EAEE;}
  body{
    font-family:'Inter',sans-serif;
    color:var(--ink);
    font-size:9.2pt;
    line-height:1.35;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
  }
  .sheet{
    width:210mm;
    min-height:297mm;
    margin:10mm auto;
    background:var(--paper);
    box-shadow:0 4px 30px rgba(20,35,90,.18);
    display:flex;
    flex-direction:column;
    overflow:hidden;
  }

  .masthead{position:relative;height:34mm;}
  .band-red{
    position:absolute;top:0;right:0;width:78%;height:12mm;
    background:var(--red);
    clip-path:polygon(6% 0,100% 0,100% 100%,0 100%);
    display:flex;align-items:center;justify-content:flex-end;
    padding-right:10mm;
  }
  .band-red span{
    color:#fff;font-family:'Barlow Semi Condensed',sans-serif;
    font-weight:700;font-size:11.5pt;letter-spacing:.4px;
  }
  .band-navy{
    position:absolute;top:12mm;right:0;width:82%;height:13.5mm;
    background:var(--navy);
    clip-path:polygon(4.5% 0,100% 0,100% 100%,0 100%);
    color:#fff;
    display:flex;align-items:center;justify-content:space-between;
    padding:0 10mm 0 16mm;gap:6mm;
  }
  .contact{display:flex;flex-direction:column;gap:1.2mm;font-size:7.4pt;}
  .contact .row{display:flex;gap:5mm;flex-wrap:wrap;}
  .contact b{color:#FFD8D9;font-weight:600;}
  .addr{font-size:6.4pt;line-height:1.45;text-align:right;color:#C9D2F2;max-width:42mm;}
  .addr b{color:#fff;display:block;font-size:6.8pt;}
  .logo{
    position:absolute;top:4mm;left:10mm;width:34mm;
    display:flex;flex-direction:column;align-items:center;gap:1mm;
  }
  .logo svg{width:19mm;height:19mm;}
  .logo .name{
    font-family:'Barlow Semi Condensed',sans-serif;font-weight:800;
    font-size:16pt;letter-spacing:5px;color:var(--navy);line-height:1;
  }
  .logo .grp{
    font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;
    font-size:7pt;letter-spacing:6px;color:var(--red);
  }

  .content{flex:1;padding:2mm 10mm 4mm;}
  .frame{border:1.3px solid var(--ink);}
  .title{
    text-align:center;font-family:'Barlow Semi Condensed',sans-serif;
    font-weight:800;font-size:12.5pt;letter-spacing:2.5px;
    padding:2.2mm 0;border-bottom:1.3px solid var(--ink);
    color:var(--navy);
  }
  .grid{display:grid;}
  .cell{padding:1.8mm 2.5mm;border-bottom:1px solid var(--line-strong);}
  .cell + .cell{border-left:1px solid var(--line-strong);}
  .lbl{
    font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;
    font-size:8.4pt;letter-spacing:.3px;color:var(--navy);
    text-transform:uppercase;
  }
  .val{
    font-family:'Caveat',cursive;font-weight:600;color:var(--pen);
    font-size:13pt;line-height:1.15;min-height:5mm;
  }
  .inline{display:flex;align-items:baseline;gap:2.5mm;}
  .inline .val{flex:1;border-bottom:1px dotted var(--line);}

  .c-2a{grid-template-columns:1fr 1fr;}
  .c-2b{grid-template-columns:1.15fr .85fr;}
  .c-3{grid-template-columns:1.05fr .85fr 1.1fr;}

  .calltype{display:grid;grid-template-columns:22mm 1fr 1fr;border-bottom:1px solid var(--line-strong);}
  .calltype .lbl-cell{padding:1.8mm 2.5mm;border-right:1px solid var(--line-strong);display:flex;align-items:center;}
  .checks{padding:1.4mm 2.5mm;display:flex;flex-direction:column;gap:1.4mm;justify-content:center;}
  .chk{display:flex;align-items:center;gap:2mm;font-size:8.6pt;font-weight:500;}
  .box{
    width:3.6mm;height:3.6mm;border:1.2px solid var(--ink);flex-shrink:0;
    display:flex;align-items:center;justify-content:center;
    font-family:'Caveat',cursive;color:var(--pen);font-size:11pt;font-weight:700;
    background:#fff;
  }
  .box.on{background:var(--navy);border-color:var(--navy);}
  .box.on::after{content:"✓";color:#fff;font-family:'Inter',sans-serif;font-size:7.5pt;font-weight:700;}

  .sub{
    text-align:center;font-family:'Barlow Semi Condensed',sans-serif;
    font-weight:700;font-size:9.8pt;letter-spacing:1.5px;
    background:var(--fill);padding:1.6mm 0;color:var(--navy);
    border-bottom:1px solid var(--line-strong);text-transform:uppercase;
  }
  .block{padding:1.8mm 2.5mm;border-bottom:1px solid var(--line-strong);}
  .block .val{min-height:12mm;margin-top:.6mm;}
  .block.tall .val{min-height:15mm;}
  ul.pen{list-style:none;}
  ul.pen li{
    font-family:'Caveat',cursive;font-weight:600;color:var(--pen);
    font-size:13pt;line-height:1.3;padding-left:4mm;position:relative;
  }
  ul.pen li::before{content:"–";position:absolute;left:0;}

  table{width:100%;border-collapse:collapse;}
  th{
    font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;
    font-size:8.4pt;letter-spacing:.4px;color:var(--navy);
    padding:1.8mm 2mm;border-bottom:1.2px solid var(--ink);
    text-transform:uppercase;background:var(--fill);
  }
  th + th, td + td{border-left:1px solid var(--line-strong);}
  td{height:5.6mm;border-bottom:1px solid var(--line);padding:1mm 2mm;font-family:'Caveat',cursive;font-size:12pt;color:var(--pen);}
  tr:last-child td{border-bottom:none;}
  .w-sno{width:12mm;} .w-qty{width:14mm;} .w-model{width:32mm;} .w-part{width:42mm;}

  .sig-wrap{display:grid;grid-template-columns:1fr 1fr;gap:5mm;margin-top:4mm;}
  .sig-card{border:1.3px solid var(--ink);}
  .sig-card .cell:last-child{border-bottom:none;}
  .signature{
    font-family:'Caveat',cursive;font-size:17pt;color:var(--pen);
    min-height:8mm;font-weight:600;
  }
  .sig-img{max-height:10mm;max-width:100%;}

  .foot{
    margin-top:auto;
    background:var(--navy);
    color:#E8ECFA;position:relative;
    padding:3mm 14mm;
    font-size:6.9pt;line-height:1.55;text-align:center;
  }
  .foot::before,.foot::after{
    content:"";position:absolute;top:0;bottom:0;width:4mm;background:var(--red);
  }
  .foot::before{left:4mm;} .foot::after{right:4mm;}

  @page{size:A4;margin:0;}
  @media print{
    html{background:none;}
    .sheet{margin:0;box-shadow:none;width:210mm;height:297mm;}
  }
</style>
</head>
<body>
<div class="sheet">
  <header class="masthead">
    <div class="logo">
      <svg viewBox="0 0 100 100" aria-hidden="true">
        <path d="M50 6 A44 44 0 1 0 94 50" fill="none" stroke="#D91F26" stroke-width="13" stroke-linecap="round"/>
        <path d="M50 24 A26 26 0 1 1 24 50" fill="none" stroke="#14235A" stroke-width="13" stroke-linecap="round"/>
        <circle cx="50" cy="50" r="7" fill="#D91F26"/>
      </svg>
      <div class="name">ASYX</div>
      <div class="grp">GROUP</div>
    </div>
    <div class="band-red"><span>TIN: 108-800-186&nbsp;&nbsp;|&nbsp;&nbsp;VRN: 40-009570-M</span></div>
    <div class="band-navy">
      <div class="contact">
        <div class="row"><span><b>☎</b> +255 755 432 071</span><span><b>✆</b> +255 625 001 100</span><span><b>✉</b> info@asyx.co.tz</span></div>
        <div class="row"><span><b>◎</b> asyxgroupcompany</span><span><b>🌐</b> www.asyx.co.tz</span></div>
      </div>
      <div class="addr">
        <b>TROPICAL CENTER, 3RD FLOOR</b>
        New Bagamoyo Road · Plot No. 30/00, House No. 301 · P.O. Box 31587 · Dar es Salaam
      </div>
    </div>
  </header>

  <main class="content">
    <div class="frame">
      <div class="title">SERVICE CALL REPORT</div>

      <div class="grid c-2a">
        <div class="cell inline"><span class="lbl">CSR No:</span><span class="val">{{ $jobCard->csr_no ?? $jobCard->job_number }}</span></div>
        <div class="cell inline"><span class="lbl">Date:</span><span class="val">{{ $jobCard->report_date?->format('d / m / Y') ?? $jobCard->created_at->format('d / m / Y') }}</span></div>
      </div>

      <div class="grid c-2b">
        <div class="cell"><div class="lbl">Customer Name</div><div class="val">{{ $jobCard->customer_name ?? '—' }}</div></div>
        <div class="cell"><div class="lbl">Address</div><div class="val">{{ $jobCard->customer_address ?? '—' }}</div></div>
        <div class="cell"><div class="lbl">Branch Name</div><div class="val">{{ $jobCard->branch_name ?? '—' }}</div></div>
        <div class="cell"><div class="lbl">Department</div><div class="val">{{ $jobCard->department ?? '—' }}</div></div>
      </div>

      <div class="cell" style="border-left:none;">
        <div class="lbl">Equipment Type</div>
        <div class="val">{{ $jobCard->equipment_type ?? '—' }}</div>
      </div>

      <div class="grid c-3">
        <div class="cell"><div class="lbl">Make / Brand</div><div class="val">{{ $jobCard->make_brand ?? '—' }}</div></div>
        <div class="cell"><div class="lbl">Model</div><div class="val">{{ $jobCard->model ?? '—' }}</div></div>
        <div class="cell"><div class="lbl">Serial Number</div><div class="val">{!! nl2br(e($jobCard->serial_number ?? '—')) !!}</div></div>
      </div>

      <div class="calltype">
        <div class="lbl-cell"><span class="lbl">Call Type</span></div>
        <div class="checks">
          <div class="chk"><span class="box {{ $jobCard->call_type === 'corrective' ? 'on' : '' }}"></span> Corrective Maintenance</div>
          <div class="chk"><span class="box {{ $jobCard->call_type === 'corrective_preventive' ? 'on' : '' }}"></span> Corrective &amp; Preventive Maintenance</div>
        </div>
        <div class="checks" style="border-left:1px solid var(--line-strong);">
          <div class="chk"><span class="box {{ $jobCard->call_type === 'preventive' ? 'on' : '' }}"></span> Preventive Maintenance</div>
          <div class="chk"><span class="box {{ $jobCard->call_type === 'installation' ? 'on' : '' }}"></span> Installation</div>
        </div>
      </div>

      <div class="sub">Nature of the Problem</div>

      <div class="block">
        <div class="lbl">Problem Reported</div>
        <div class="val">
          @if($jobCard->problem_reported)
          <ul class="pen">
            @foreach(array_filter(explode("\n", $jobCard->problem_reported)) as $line)
            <li>{{ trim($line) }}</li>
            @endforeach
          </ul>
          @else
          —
          @endif
        </div>
      </div>

      <div class="block tall">
        <div class="lbl">Defects Found</div>
        <div class="val">
          @if($jobCard->defects_found)
          <ul class="pen">
            @foreach(array_filter(explode("\n", $jobCard->defects_found)) as $line)
            <li>{{ trim($line) }}</li>
            @endforeach
          </ul>
          @else
          —
          @endif
        </div>
      </div>

      <div class="block">
        <div class="lbl">Action Taken</div>
        <div class="val">
          @if($jobCard->action_taken)
          <ul class="pen">
            @foreach(array_filter(explode("\n", $jobCard->action_taken)) as $line)
            <li>{{ trim($line) }}</li>
            @endforeach
          </ul>
          @else
          —
          @endif
        </div>
      </div>

      <table>
        <thead>
          <tr>
            <th class="w-sno">S/No</th>
            <th>Parts Required / Replaced</th>
            <th class="w-qty">Qty</th>
            <th class="w-model">Model</th>
            <th class="w-part">Part Number</th>
          </tr>
        </thead>
        <tbody>
          @forelse($jobCard->parts as $i => $part)
          <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $part->part_name }}</td>
            <td>{{ $part->quantity }}</td>
            <td>{{ $part->model ?? '' }}</td>
            <td>{{ $part->part_number ?? '' }}</td>
          </tr>
          @empty
          <tr><td></td><td></td><td></td><td></td><td></td></tr>
          <tr><td></td><td></td><td></td><td></td><td></td></tr>
          <tr><td></td><td></td><td></td><td></td><td></td></tr>
          <tr><td></td><td></td><td></td><td></td><td></td></tr>
          <tr><td></td><td></td><td></td><td></td><td></td></tr>
          <tr><td></td><td></td><td></td><td></td><td></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="sig-wrap">
      <div class="sig-card">
        <div class="cell inline" style="border-left:none;"><span class="lbl">Date:</span><span class="val">{{ $jobCard->end_user_signed_at?->format('d / m / Y') ?? $jobCard->report_date?->format('d / m / Y') ?? '—' }}</span></div>
        <div class="cell" style="border-left:none;"><div class="lbl">End-user Name</div><div class="val">{{ $jobCard->end_user_name ?? '—' }}</div></div>
        <div class="cell" style="border-left:none;"><div class="lbl">Signature</div>
          <div class="signature">
            @if($jobCard->end_user_signature)
            <img src="{{ $jobCard->end_user_signature }}" class="sig-img" alt="End-user signature">
            @else
            —
            @endif
          </div>
        </div>
      </div>
      <div class="sig-card">
        <div class="cell inline" style="border-left:none;"><span class="lbl">Date:</span><span class="val">{{ $jobCard->technician_signed_at?->format('d / m / Y') ?? $jobCard->report_date?->format('d / m / Y') ?? '—' }}</span></div>
        <div class="cell" style="border-left:none;"><div class="lbl">Technician Name</div><div class="val">{{ $jobCard->technician_name ?? $jobCard->assignedTo?->name ?? '—' }}</div></div>
        <div class="cell" style="border-left:none;"><div class="lbl">Signature</div>
          <div class="signature">
            @if($jobCard->technician_signature)
            <img src="{{ $jobCard->technician_signature }}" class="sig-img" alt="Technician signature">
            @else
            —
            @endif
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="foot">
    Software and hardware distribution · Customized software and mobile apps development and re-engineering ·
    Enterprise software solutions · IT systems security and auditing · Computerized systems integration ·
    Artificial Intelligence (AI) and Machine Learning · Statistics and Big Data Analytics ·
    Customer-specific Annual Maintenance Support Contracts (AMCs) · ICT consultancy and training
  </footer>
</div>
</body>
</html>
