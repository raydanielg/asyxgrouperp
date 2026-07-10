@extends('layouts.admin')
@section('title', 'SGR Action Points Import - ' . config('app.name'))
@section('page_title', 'SGR Action Points Import')
@section('content')
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-amber-50 text-amber-700 text-sm border border-amber-100">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100">{{ session('error') }}</div>
@endif

<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.sgr.index') }}" class="text-xs text-gray-500 hover:text-amber-600">&larr; Dashboard</a>
    <a href="{{ route('admin.sgr.download-template') }}" class="px-4 py-2 bg-amber-600 text-white text-xs font-medium rounded-lg hover:bg-amber-700">Download Template</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4">1. Upload Excel File</h3>
            <form method="POST" action="{{ route('admin.sgr.action-points.upload') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Excel File (.xlsx, .xls, .csv)</label>
                    <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-amber-50 file:text-amber-700 file:text-xs hover:file:bg-amber-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sheet Name (optional)</label>
                    <input type="text" name="sheet_name" placeholder="Leave blank to use first sheet" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none">
                </div>
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700">Upload & Preview</button>
            </form>
        </div>

        @if($preview)
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">2. Map Columns</h3>
                    <p class="text-xs text-gray-500">Sheet: <span class="font-medium">{{ $preview['sheet_name'] }}</span> | Total rows: <span class="font-medium">{{ $preview['total_rows'] }}</span></p>
                </div>
                <a href="{{ route('admin.sgr.action-points.reports') }}" class="text-xs text-amber-600 hover:text-amber-700 font-medium">View Reports</a>
            </div>

            @if(!empty($preview['preview']))
            <div class="overflow-x-auto mb-4 rounded-lg border">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach($preview['headers'] as $header)
                            <th class="px-3 py-2 text-left font-medium text-gray-600 whitespace-nowrap">{{ $header ?? '' }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($preview['preview'] as $row)
                        <tr class="border-t border-gray-100">
                            @foreach($row as $cell)
                            <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $cell ?? '' }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.sgr.action-points.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Header Row *</label>
                        <input type="number" name="header_row" value="1" min="1" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none">
                        <p class="text-[10px] text-gray-400 mt-1">Row number containing column names. Data rows start after this.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Activity Column *</label>
                        <select name="activity_column" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none">
                            <option value="">Select column...</option>
                            @foreach($preview['headers'] as $index => $header)
                            <option value="{{ $header }}" @selected(strtolower($header) === 'activity')>{{ $header ?? 'Column ' . ($index + 1) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Responsible Person Column *</label>
                        <select name="responsible_column" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none">
                            <option value="">Select column...</option>
                            @foreach($preview['headers'] as $index => $header)
                            <option value="{{ $header }}" @selected(stripos($header, 'responsible') !== false)>{{ $header ?? 'Column ' . ($index + 1) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Due Date Column</label>
                        <select name="due_date_column" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none">
                            <option value="">— None —</option>
                            @foreach($preview['headers'] as $index => $header)
                            <option value="{{ $header }}" @selected(stripos($header, 'due') !== false || stripos($header, 'date') !== false)>{{ $header ?? 'Column ' . ($index + 1) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status Column</label>
                        <select name="status_column" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none">
                            <option value="">— None —</option>
                            @foreach($preview['headers'] as $index => $header)
                            <option value="{{ $header }}" @selected(strtolower($header) === 'status')>{{ $header ?? 'Column ' . ($index + 1) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700">Import Action Points</button>
                    <a href="{{ route('admin.sgr.action-points.import') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
                </div>
            </form>
        </div>
        @else
        <div class="bg-white rounded-xl border p-6 text-center">
            <p class="text-sm text-gray-500">Upload an Excel file to see column mapping options.</p>
        </div>
        @endif
    </div>

    @if($isAdmin)
    <div class="bg-white rounded-xl border p-6 h-fit mb-4">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Pending Approvals</h3>
        @forelse($pendingApprovals as $ap)
        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-700 truncate">{{ \Illuminate\Support\Str::limit($ap->activity, 40) }}</p>
                <p class="text-[10px] text-gray-400">{{ $ap->import_batch }} | by {{ $ap->creator?->name ?? '—' }}</p>
            </div>
            <form method="POST" action="{{ route('admin.sgr.action-points.approve') }}" class="flex gap-1">
                @csrf
                <input type="hidden" name="batch" value="{{ $ap->import_batch }}">
                <button type="submit" name="action" value="approve" class="px-2 py-1 bg-emerald-600 text-white text-[10px] font-medium rounded hover:bg-emerald-700">Approve</button>
                <button type="submit" name="action" value="reject" class="px-2 py-1 bg-red-600 text-white text-[10px] font-medium rounded hover:bg-red-700">Reject</button>
            </form>
        </div>
        @empty
        <p class="text-xs text-gray-400 text-center py-4">No pending approvals.</p>
        @endforelse
    </div>
    @endif

    <div class="bg-white rounded-xl border p-6 h-fit">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Recent Imports</h3>
        @forelse($batches as $batch)
        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-700 truncate">{{ $batch->source_filename }}</p>
                <p class="text-[10px] text-gray-400">{{ $batch->import_batch }} | {{ $batch->total }} rows</p>
                <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($batch->imported_at)->diffForHumans() }}</p>
            </div>
            <a href="{{ route('admin.sgr.action-points.reports', ['batch' => $batch->import_batch]) }}" class="text-xs text-amber-600 hover:text-amber-700 whitespace-nowrap">View</a>
        </div>
        @empty
        <p class="text-xs text-gray-400 text-center py-4">No imports yet.</p>
        @endforelse
    </div>
</div>
@endsection
