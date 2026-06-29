@php
$title = 'Expenses';
$description = 'Track company expenses by category.';
@endphp
@extends('layouts.admin')
@section('title', $title)
@section('page_title', $title)
@section('content')
<div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="relative z-10">
        <h2 class="text-2xl font-bold">{{ $title }}</h2>
        <p class="text-emerald-100 text-sm mt-1">{{ $description }}</p>
    </div>
</div>

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Total Expenses</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $money($totalExpenses ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">This Month</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $money($monthExpenses ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Records</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $expenses->total() ?? 0 }}</p>
    </div>
</div>

@include('roles.finance-officer.pages._actions', ['module' => 'expenses'])

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Category</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Description</th>
                    <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Account</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($expenses as $expense)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $expense->expense_date?->format('M d, Y') }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $expense->category ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-900">{{ $expense->description ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-900 text-right">{{ $money($expense->amount) }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $expense->bankAccount?->account_name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-xs text-gray-400">No expenses found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t flex items-center justify-between">
        <span class="text-xs text-gray-500">Total records: {{ $expenses->total() ?? 0 }}</span>
        <div class="flex items-center gap-2">{{ $expenses->links() }}</div>
    </div>
</div>
@endsection