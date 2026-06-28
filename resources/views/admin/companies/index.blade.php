@extends('layouts.admin')
@section('title', 'Companies - ' . config('app.name'))
@section('page_title', 'Multi-Company Management')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage all companies in the ASYX Group</p>
    <a href="{{ route('admin.companies.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Company
    </a>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
            <th class="px-5 py-3 font-medium">Company</th>
            <th class="px-5 py-3 font-medium">Code</th>
            <th class="px-5 py-3 font-medium">Type</th>
            <th class="px-5 py-3 font-medium">Currency</th>
            <th class="px-5 py-3 font-medium">Parent</th>
            <th class="px-5 py-3 font-medium">Status</th>
            <th class="px-5 py-3 font-medium">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($companies as $company)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs">
                <a href="{{ route('admin.companies.show', $company) }}" class="font-medium text-gray-800 hover:text-emerald-600">{{ $company->legal_name }}</a>
                <p class="text-gray-400 text-[10px]">{{ $company->address ?? '—' }}</p>
            </td>
            <td class="px-5 py-3 text-xs"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">{{ $company->short_code }}</span></td>
            <td class="px-5 py-3">
                @if($company->is_group)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gold-50 text-gold-700 border border-gold-100">Group</span>
                @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">Operating</span>
                @endif
            </td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $company->currency }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $company->parent?->short_code ?? '—' }}</td>
            <td class="px-5 py-3">
                @if($company->is_active)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
                @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-500 border border-gray-100">Inactive</span>
                @endif
            </td>
            <td class="px-5 py-3 flex items-center gap-3">
                <a href="{{ route('admin.companies.show', $company) }}" class="text-emerald-600 hover:text-emerald-700 text-xs">View</a>
                <a href="{{ route('admin.companies.edit', $company) }}" class="text-emerald-600 hover:text-emerald-700 text-xs">Edit</a>
                @if(!$company->is_group)
                <form method="POST" action="{{ route('admin.companies.destroy', $company) }}" class="inline" onsubmit="return confirm('Delete this company?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-xs">Delete</button></form>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No companies found</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>

<div class="mt-4">
    <a href="{{ route('admin.companies.consolidated') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        View Consolidated Report
    </a>
</div>
@endsection
