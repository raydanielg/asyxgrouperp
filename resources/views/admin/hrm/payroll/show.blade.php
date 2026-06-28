@extends('layouts.admin')
@section('title', 'Payslip - ' . $payroll->payroll_number)
@section('page_title', '')
@section('content')

<div class="max-w-[760px] mx-auto">
    {{-- Toolbar --}}
    <div class="flex items-center justify-between mb-4 max-w-[760px] no-print">
        <div class="text-xs" style="color:#6E7570;">
            Payslip <b style="color:#1C2321;">{{ $payroll->month }} {{ $payroll->year }}</b> &middot; {{ config('app.name') }}
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2.5 text-xs font-bold rounded-lg transition-all flex items-center gap-2" style="background:#C9A227;color:#23270F;" onmouseover="this.style.background='#B8941F'" onmouseout="this.style.background='#C9A227'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </button>
        </div>
    </div>

    {{-- ═══ PAYSLIP A4 ═══ --}}
    <div id="payslip-a4" class="sheet" style="background:#fff;border-radius:6px;box-shadow:0 18px 40px -10px rgba(15,61,62,.25),0 0 0 1px #E3DDCB;overflow:hidden;position:relative;">

        {{-- Stamp --}}
        @if($payroll->status == 'paid')
        <div style="position:absolute;top:26px;right:-46px;background:#2F7A3D;color:#fff;font-size:12px;font-weight:700;letter-spacing:.12em;padding:6px 62px;transform:rotate(35deg);box-shadow:0 4px 10px rgba(0,0,0,.15);z-index:10;">PAID</div>
        @elseif($payroll->status == 'pending')
        <div style="position:absolute;top:26px;right:-46px;background:#C9A227;color:#23270F;font-size:12px;font-weight:700;letter-spacing:.12em;padding:6px 62px;transform:rotate(35deg);box-shadow:0 4px 10px rgba(0,0,0,.15);z-index:10;">PENDING</div>
        @else
        <div style="position:absolute;top:26px;right:-46px;background:#B23A2E;color:#fff;font-size:12px;font-weight:700;letter-spacing:.12em;padding:6px 62px;transform:rotate(35deg);box-shadow:0 4px 10px rgba(0,0,0,.15);z-index:10;">CANCELLED</div>
        @endif

        {{-- Head --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:38px 44px 26px;border-bottom:1px solid #E3DDCB;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:38px;height:38px;border-radius:10px;background:conic-gradient(from 90deg,#C9A227,#8C5E2A,#0F3D3E,#C9A227);flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                    <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX" style="width:30px;height:30px;object-fit:contain;border-radius:4px;">
                </div>
                <div>
                    <div style="font-family:'Fraunces',serif;font-weight:700;font-size:17px;color:#0F3D3E;">{{ config('app.name') }}</div>
                    <div style="font-size:11.5px;color:#6E7570;margin-top:3px;line-height:1.5;">
                        {{ auth()->user()->company?->name ?? 'ASYX Group' }}<br>
                        Dar es Salaam, Tanzania
                    </div>
                </div>
            </div>
            <div style="text-align:right;">
                <h1 style="font-family:'Fraunces',serif;font-size:24px;margin:0 0 8px;color:#1C2321;">Payslip</h1>
                <div style="font-size:12px;color:#6E7570;line-height:1.6;">
                    Kipindi: <b style="color:#1C2321;">{{ $payroll->month }} {{ $payroll->year }}</b><br>
                    Tarehe ya Malipo: <b style="color:#1C2321;">{{ $payroll->created_at?->format('M d, Y') ?? now()->format('M d, Y') }}</b>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div style="padding:30px 44px;">

            {{-- Employee / Employer Row --}}
            <div style="display:flex;justify-content:space-between;margin-bottom:28px;gap:24px;">
                <div>
                    <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.1em;color:#6E7570;margin-bottom:6px;">Mfanyakazi</div>
                    <b style="display:block;font-size:14.5px;color:#1C2321;">{{ $payroll->employee?->full_name ?? 'N/A' }}</b>
                    <div style="font-size:12.5px;color:#6E7570;line-height:1.6;margin-top:2px;">
                        {{ $payroll->employee?->designation ?? '' }} &mdash; {{ $payroll->employee?->department ?? '' }}<br>
                        Employee ID: {{ $payroll->employee?->employee_id ?? 'N/A' }}
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.1em;color:#6E7570;margin-bottom:6px;">Mwajiri</div>
                    <b style="display:block;font-size:14.5px;color:#1C2321;">{{ config('app.name') }}</b>
                    <div style="font-size:12.5px;color:#6E7570;line-height:1.6;margin-top:2px;">
                        TIN: 109-XXX-XXX<br>
                        NSSF No: NS-{{ str_pad($payroll->employee_id ?? 0, 5, '0', STR_PAD_LEFT) }}<br>
                        PAYE No: PY-{{ str_pad($payroll->id, 5, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
            </div>

            {{-- Earnings & Deductions --}}
            <div style="display:flex;gap:36px;margin-top:6px;">
                {{-- Earnings --}}
                <div style="flex:1;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:8px;">
                        <tr>
                            <th style="text-align:left;font-size:10.5px;text-transform:uppercase;letter-spacing:.08em;color:#6E7570;border-bottom:1.5px solid #1C2321;padding:0 4px 10px;">Mapato</th>
                            <th style="text-align:right;font-size:10.5px;text-transform:uppercase;letter-spacing:.08em;color:#6E7570;border-bottom:1.5px solid #1C2321;padding:0 4px 10px;">Kiasi</th>
                        </tr>
                        <tr><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;color:#1C2321;">Mshahara wa Msingi</td><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;text-align:right;color:#1C2321;font-weight:600;">{{ number_format($payroll->basic_salary, 2) }} Tsh</td></tr>
                        <tr><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;color:#1C2321;">Posho ya Usafiri</td><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;text-align:right;color:#1C2321;font-weight:600;">{{ number_format($payroll->allowances * 0.5, 2) }} Tsh</td></tr>
                        <tr><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;color:#1C2321;">Posho ya Mawasiliano</td><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;text-align:right;color:#1C2321;font-weight:600;">{{ number_format($payroll->allowances * 0.3, 2) }} Tsh</td></tr>
                        <tr><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;color:#1C2321;">Posho ya Matibabu</td><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;text-align:right;color:#1C2321;font-weight:600;">{{ number_format($payroll->allowances * 0.2, 2) }} Tsh</td></tr>
                        <tr><td style="padding:14px 4px 4px;font-weight:700;color:#0F3D3E;border-bottom:none;">Jumla ya Mapato</td><td style="padding:14px 4px 4px;font-weight:700;color:#0F3D3E;text-align:right;border-bottom:none;">{{ number_format($payroll->basic_salary + $payroll->allowances, 2) }} Tsh</td></tr>
                    </table>
                </div>
                {{-- Deductions --}}
                <div style="flex:1;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:8px;">
                        <tr>
                            <th style="text-align:left;font-size:10.5px;text-transform:uppercase;letter-spacing:.08em;color:#6E7570;border-bottom:1.5px solid #1C2321;padding:0 4px 10px;">Makato</th>
                            <th style="text-align:right;font-size:10.5px;text-transform:uppercase;letter-spacing:.08em;color:#6E7570;border-bottom:1.5px solid #1C2321;padding:0 4px 10px;">Kiasi</th>
                        </tr>
                        <tr><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;color:#1C2321;">PAYE</td><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;text-align:right;color:#B23A2E;font-weight:600;">&minus;{{ number_format($payroll->deductions * 0.6, 2) }} Tsh</td></tr>
                        <tr><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;color:#1C2321;">NSSF (10%)</td><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;text-align:right;color:#B23A2E;font-weight:600;">&minus;{{ number_format($payroll->deductions * 0.2, 2) }} Tsh</td></tr>
                        <tr><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;color:#1C2321;">NHIF</td><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;text-align:right;color:#B23A2E;font-weight:600;">&minus;{{ number_format($payroll->deductions * 0.1, 2) }} Tsh</td></tr>
                        <tr><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;color:#1C2321;">WCF / Other</td><td style="padding:12px 4px;border-bottom:1px solid #E3DDCB;text-align:right;color:#B23A2E;font-weight:600;">&minus;{{ number_format($payroll->deductions * 0.1, 2) }} Tsh</td></tr>
                        <tr><td style="padding:14px 4px 4px;font-weight:700;color:#B23A2E;border-bottom:none;">Jumla ya Makato</td><td style="padding:14px 4px 4px;font-weight:700;color:#B23A2E;text-align:right;border-bottom:none;">&minus;{{ number_format($payroll->deductions, 2) }} Tsh</td></tr>
                    </table>
                </div>
            </div>

            {{-- Net Pay Bar --}}
            <div style="margin-top:24px;padding:18px 22px;border-radius:10px;background:#E2F0E5;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;color:#2F7A3D;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Net Pay</span>
                <b style="font-family:'JetBrains Mono',monospace;font-size:18px;color:#2F7A3D;">{{ number_format($payroll->net_salary, 2) }} Tsh</b>
            </div>
        </div>

        {{-- Footer --}}
        <div style="padding:18px 44px;border-top:1px solid #E3DDCB;background:#FBF9F2;font-size:11px;color:#6E7570;text-align:center;">
            Payslip Generated on {{ now()->format('l, F jS, Y') }} &middot; {{ config('app.name') }}
        </div>
    </div>
</div>

<style>
#payslip-a4 { font-family: 'Inter','Nunito',system-ui,sans-serif; }
#payslip-a4 h1 { font-family: 'Fraunces','Georgia',serif; }
@media print {
    @page { margin: 0; size: A4; }
    body { background: #fff !important; padding: 0 !important; }
    body * { visibility: hidden; }
    #payslip-a4, #payslip-a4 * { visibility: visible; }
    #payslip-a4 { position: absolute; left: 0; top: 0; width: 210mm; min-height: 297mm; box-shadow: none !important; border-radius: 0 !important; }
    .no-print { display: none !important; }
    nav, header, .sidebar, .no-print { display: none !important; }
}
</style>

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
@endpush
@endsection
