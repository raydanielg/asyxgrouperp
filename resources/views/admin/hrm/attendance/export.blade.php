<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Attendance Report — {{ $company->legal_name ?? 'ASYX Group Company Limited' }}</title>
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
    <a href="{{ route('admin.attendance.index') }}" class="text-xs text-gray-500 hover:text-gray-800">&larr; Back to Attendance</a>
    <button onclick="window.print()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Print / Save as PDF</button>
</div>

<div class="sheet px-10 py-10">

    <div class="text-center border-b-2 border-gray-800 pb-5 mb-6">
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-500">The United Republic of Tanzania</p>
        <h1 class="text-lg font-bold uppercase tracking-wide text-gray-900 mt-1">{{ $company->legal_name ?? 'ASYX Group Company Limited' }}</h1>
        <p class="text-[11px] text-gray-500 mt-0.5">
            {{ $company->address ?? '' }}@if($company->city ?? null), {{ $company->city }}@endif
            @if($company->phone ?? null) · Tel: {{ $company->phone }}@endif
            @if($company->email ?? null) · {{ $company->email }}@endif
        </p>
        <div class="mt-4">
            <h2 class="text-sm font-bold uppercase tracking-widest text-gray-900 border-t border-b border-gray-800 py-1.5">Daily Attendance Report</h2>
            <p class="text-[10px] text-gray-500 mt-1.5">
                Date: {{ \Carbon\Carbon::parse($filters['date'])->format('d M Y') }}
                · Generated: {{ now()->format('d M Y, H:i') }}
                @if($filters['search']) · Search: "{{ $filters['search'] }}"@endif
                @if($filters['status']) · Status: {{ ucfirst(str_replace('_',' ',$filters['status'])) }}@endif
                · Records: {{ $records->count() }}
            </p>
        </div>
    </div>

    @php
        $present = $records->where('status','present')->count();
        $absent = $records->where('status','absent')->count();
        $late = $records->where('status','late')->count();
        $remote = $records->where('status','remote')->count();
    @endphp
    <div class="flex justify-center gap-8 mb-6 text-[11px]">
        <span class="text-gray-600">Present: <b class="text-gray-900">{{ $present }}</b></span>
        <span class="text-gray-600">Absent: <b class="text-gray-900">{{ $absent }}</b></span>
        <span class="text-gray-600">Late: <b class="text-gray-900">{{ $late }}</b></span>
        <span class="text-gray-600">Remote: <b class="text-gray-900">{{ $remote }}</b></span>
    </div>

    <table class="w-full text-[11px]">
        <thead>
            <tr class="border-y-2 border-gray-800 text-left">
                <th class="py-2 pr-3 font-bold uppercase tracking-wider text-gray-800">#</th>
                <th class="py-2 pr-3 font-bold uppercase tracking-wider text-gray-800">Employee</th>
                <th class="py-2 pr-3 font-bold uppercase tracking-wider text-gray-800">Emp. ID</th>
                <th class="py-2 pr-3 font-bold uppercase tracking-wider text-gray-800">Clock In</th>
                <th class="py-2 pr-3 font-bold uppercase tracking-wider text-gray-800">Clock Out</th>
                <th class="py-2 pr-3 font-bold uppercase tracking-wider text-gray-800 text-right">Work Hrs</th>
                <th class="py-2 pr-3 font-bold uppercase tracking-wider text-gray-800 text-right">OT</th>
                <th class="py-2 font-bold uppercase tracking-wider text-gray-800">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $i => $a)
            <tr class="border-b border-gray-200">
                <td class="py-2 pr-3 text-gray-500">{{ $i + 1 }}</td>
                <td class="py-2 pr-3 font-medium text-gray-900">{{ trim(($a->employee?->first_name ?? '') . ' ' . ($a->employee?->last_name ?? '')) ?: '—' }}</td>
                <td class="py-2 pr-3 font-mono text-gray-600">{{ $a->employee?->employee_id ?? '—' }}</td>
                <td class="py-2 pr-3 text-gray-700">{{ $a->clock_in_at?->format('H:i:s') ?? $a->check_in ?? '—' }}</td>
                <td class="py-2 pr-3 text-gray-700">{{ $a->clock_out_at?->format('H:i:s') ?? $a->check_out ?? ($a->clock_in_at ? 'Working…' : '—') }}</td>
                <td class="py-2 pr-3 text-right text-gray-700">{{ $a->work_hours > 0 ? sprintf('%02d:%02d', floor($a->work_hours), round(($a->work_hours - floor($a->work_hours)) * 60)) : '—' }}</td>
                <td class="py-2 pr-3 text-right text-gray-700">{{ $a->overtime_hours > 0 ? sprintf('%02d:%02d', floor($a->overtime_hours), round(($a->overtime_hours - floor($a->overtime_hours)) * 60)) : '—' }}</td>
                <td class="py-2 text-gray-700 capitalize">{{ str_replace('_',' ',$a->status ?? '—') }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="py-8 text-center text-gray-400">No attendance records for this date</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-16 grid grid-cols-2 gap-20">
        <div><div class="border-b border-gray-500 h-8"></div><p class="text-[10px] uppercase tracking-wider text-gray-500 mt-1">Prepared by</p></div>
        <div><div class="border-b border-gray-500 h-8"></div><p class="text-[10px] uppercase tracking-wider text-gray-500 mt-1">Approved by</p></div>
    </div>

    <p class="text-[9px] text-gray-400 text-center mt-10 border-t border-gray-200 pt-3">
        {{ $company->legal_name ?? 'ASYX Group Company Limited' }} · Generated by ASYX ERP · {{ now()->format('d M Y') }}
    </p>
</div>

</body>
</html>
