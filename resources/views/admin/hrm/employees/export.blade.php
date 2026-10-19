<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Employees Report — {{ $company->legal_name ?? 'ASYX Group Company Limited' }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: #fff !important; }
        .sheet { box-shadow: none !important; margin: 0 !important; }
    }
    body { background: #f3f4f6; font-family: ui-sans-serif, system-ui, sans-serif; }
    .sheet { background: #fff; max-width: 1000px; margin: 24px auto; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
</style>
</head>
<body class="py-6">

<div class="no-print max-w-[1000px] mx-auto mb-3 flex items-center justify-between px-2">
    <a href="{{ route('admin.employees.index') }}" class="text-xs text-gray-500 hover:text-gray-800">&larr; Back to Employees</a>
    <button onclick="window.print()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Print / Save as PDF</button>
</div>

<div class="sheet px-10 py-10">

    {{-- Government-style report header --}}
    <div class="text-center border-b-2 border-gray-800 pb-5 mb-6">
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-500">The United Republic of Tanzania</p>
        <h1 class="text-lg font-bold uppercase tracking-wide text-gray-900 mt-1">{{ $company->legal_name ?? 'ASYX Group Company Limited' }}</h1>
        <p class="text-[11px] text-gray-500 mt-0.5">
            {{ $company->address ?? '' }}@if($company->city ?? null), {{ $company->city }}@endif
            @if($company->phone ?? null) · Tel: {{ $company->phone }}@endif
            @if($company->email ?? null) · {{ $company->email }}@endif
        </p>
        @if($company->tin ?? null || $company->vrn ?? null)
        <p class="text-[11px] text-gray-500">@if($company->tin ?? null)TIN: {{ $company->tin }}@endif @if($company->vrn ?? null) · VRN: {{ $company->vrn }}@endif</p>
        @endif
        <div class="mt-4">
            <h2 class="text-sm font-bold uppercase tracking-widest text-gray-900 border-t border-b border-gray-800 py-1.5">Employees Report</h2>
            <p class="text-[10px] text-gray-500 mt-1.5">
                Generated: {{ now()->format('d M Y, H:i') }}
                @if($filters['search']) · Search: "{{ $filters['search'] }}"@endif
                @if($filters['department']) · Department: {{ $filters['department'] }}@endif
                @if($filters['status']) · Status: {{ ucfirst(str_replace('_',' ',$filters['status'])) }}@endif
                · Total: {{ $employees->count() }} employee{{ $employees->count() !== 1 ? 's' : '' }}
            </p>
        </div>
    </div>

    <table class="w-full text-[11px]">
        <thead>
            <tr class="border-y-2 border-gray-800 text-left">
                <th class="py-2 pr-3 font-bold uppercase tracking-wider text-gray-800">#</th>
                <th class="py-2 pr-3 font-bold uppercase tracking-wider text-gray-800">Emp. ID</th>
                <th class="py-2 pr-3 font-bold uppercase tracking-wider text-gray-800">Name</th>
                <th class="py-2 pr-3 font-bold uppercase tracking-wider text-gray-800">Department</th>
                <th class="py-2 pr-3 font-bold uppercase tracking-wider text-gray-800">Designation</th>
                <th class="py-2 pr-3 font-bold uppercase tracking-wider text-gray-800">Type</th>
                <th class="py-2 pr-3 font-bold uppercase tracking-wider text-gray-800 text-right">Salary (TZS)</th>
                <th class="py-2 font-bold uppercase tracking-wider text-gray-800">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $i => $e)
            <tr class="border-b border-gray-200">
                <td class="py-2 pr-3 text-gray-500">{{ $i + 1 }}</td>
                <td class="py-2 pr-3 font-mono text-gray-700">{{ $e->employee_id }}</td>
                <td class="py-2 pr-3 font-medium text-gray-900">{{ trim($e->first_name . ' ' . $e->last_name) }}</td>
                <td class="py-2 pr-3 text-gray-600">{{ $e->department ?? '—' }}</td>
                <td class="py-2 pr-3 text-gray-600">{{ $e->designation ?? '—' }}</td>
                <td class="py-2 pr-3 text-gray-600">{{ $e->employment_type ?? '—' }}</td>
                <td class="py-2 pr-3 text-right text-gray-800">{{ number_format($e->salary ?? 0) }}</td>
                <td class="py-2 text-gray-700 capitalize">{{ str_replace('_',' ',$e->status ?? '—') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="border-t-2 border-gray-800">
                <td colspan="6" class="py-2 pr-3 font-bold text-gray-900 text-right">Total Payroll</td>
                <td class="py-2 pr-3 font-bold text-gray-900 text-right">{{ number_format($employees->sum('salary')) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- Signatures --}}
    <div class="mt-16 grid grid-cols-2 gap-20">
        <div>
            <div class="border-b border-gray-500 h-8"></div>
            <p class="text-[10px] uppercase tracking-wider text-gray-500 mt-1">Prepared by</p>
        </div>
        <div>
            <div class="border-b border-gray-500 h-8"></div>
            <p class="text-[10px] uppercase tracking-wider text-gray-500 mt-1">Approved by</p>
        </div>
    </div>

    <p class="text-[9px] text-gray-400 text-center mt-10 border-t border-gray-200 pt-3">
        {{ $company->legal_name ?? 'ASYX Group Company Limited' }} · Generated by ASYX ERP · {{ now()->format('d M Y') }}
    </p>
</div>

</body>
</html>
