@extends('layouts.admin')
@section('title', 'Generate Payroll - ' . config('app.name'))
@section('page_title', 'Generate Payroll')
@section('content')

<div class="max-w-3xl mx-auto">
    {{-- Info Card --}}
    <div class="bg-white rounded-xl border p-6 mb-6">
        <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">Bulk Payroll Generation</h3>
                <p class="text-xs text-gray-500 mt-1">Generate payroll records for all active employees for a selected month and year. Deductions (8%) and allowances (20%) are calculated automatically based on basic salary.</p>
                <div class="flex items-center gap-4 mt-3">
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>{{ $activeEmployees }} active employees</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <span>{{ $existingCounts->sum('cnt') }} existing records</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Generate Form --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/30">
            <h3 class="text-sm font-bold text-gray-800">Select Period</h3>
        </div>
        <form method="POST" action="{{ route('admin.payroll.generate') }}" class="p-6">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Month *</label>
                    <select name="month" required class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white">
                        @foreach($months as $m)
                            <option value="{{ $m }}" @selected($m == date('F'))>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Year *</label>
                    <select name="year" required class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white">
                        @foreach($years as $y)
                            <option value="{{ $y }}" @selected($y == date('Y'))>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 p-4 bg-amber-50 rounded-xl border border-amber-200">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    <div>
                        <p class="text-xs font-semibold text-amber-800">Important Note</p>
                        <p class="text-[11px] text-amber-700 mt-0.5">This will generate payroll for <strong>all active employees</strong>. Employees who already have a payroll record for the selected month/year will be skipped.</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <a href="{{ route('admin.payroll.index') }}" class="px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Generate Payroll
                </button>
            </div>
        </form>
    </div>

    {{-- Existing Records --}}
    @if($existingCounts->isNotEmpty())
    <div class="bg-white rounded-xl border overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/30">
            <h3 class="text-sm font-bold text-gray-800">Previously Generated Payrolls</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b border-gray-100">
                        <th class="px-5 py-3 font-semibold">Period</th>
                        <th class="px-5 py-3 font-semibold text-right">Records</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($existingCounts as $ec)
                    <tr class="border-t border-gray-50 hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-xs font-medium text-gray-700">{{ $ec->month }} {{ $ec->year }}</td>
                        <td class="px-5 py-3 text-xs text-right text-gray-600">{{ $ec->cnt }} records</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@endsection
