@extends('layouts.admin')
@section('title', $document->title . ' - ' . config('app.name'))
@section('page_title', $document->title)
@section('content')
<div class="mb-4"><a href="{{ route('admin.documents.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">&larr; Back to Documents</a></div>
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100">{{ session('error') }}</div>
@endif
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="font-bold text-gray-800 mb-4">{{ $document->title }}</h3>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-400">Number</dt><dd class="text-gray-700">{{ $document->document_number }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Category</dt><dd class="text-gray-700 capitalize">{{ str_replace('_', ' ', $document->category ?? '—') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Version</dt><dd class="text-gray-700">v{{ $document->version }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">File Type</dt><dd class="text-gray-700 uppercase">{{ $document->file_type }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Size</dt><dd class="text-gray-700">{{ number_format($document->file_size / 1024, 0) }} KB</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Uploaded By</dt><dd class="text-gray-700">{{ $document->uploadedBy?->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Uploaded</dt><dd class="text-gray-700">{{ $document->created_at->format('d M Y') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Status</dt><dd><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $document->status === 'signed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : ($document->status === 'pending_signature' ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-gray-50 text-gray-500 border border-gray-100') }}">{{ $document->status }}</span></dd></div>
        </dl>
        @if($document->description)
        <div class="mt-4 pt-4 border-t"><p class="text-xs text-gray-400 mb-1">Description</p><p class="text-sm text-gray-700">{{ $document->description }}</p></div>
        @endif
        <div class="mt-4 flex gap-2">
            <a href="{{ route('admin.documents.download', $document) }}" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700">Download</a>
            @if($document->signatures()->where('signer_id', auth()->id())->where('status', 'pending')->exists())
            <form method="POST" action="{{ route('admin.documents.sign', $document) }}">@csrf<button class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700">Sign</button></form>
            @endif
        </div>
    </div>
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border overflow-hidden">
            <div class="px-5 py-3 border-b bg-gray-50/50"><h4 class="text-sm font-bold text-gray-700">E-Signatures</h4></div>
            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Signer</th><th class="px-4 py-2 font-medium">Email</th><th class="px-4 py-2 font-medium">Status</th><th class="px-4 py-2 font-medium">Signed At</th></tr></thead>
                <tbody>
                @forelse($document->signatures as $sig)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-2 text-xs text-gray-700">{{ $sig->signer_name }}</td>
                    <td class="px-4 py-2 text-xs text-gray-500">{{ $sig->signer_email }}</td>
                    <td class="px-4 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $sig->status === 'signed' ? 'bg-emerald-50 text-emerald-700' : ($sig->status === 'declined' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ $sig->status }}</span></td>
                    <td class="px-4 py-2 text-xs text-gray-400">{{ $sig->signed_at?->format('d M Y H:i') ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">No signature requests</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>
        <div class="bg-white rounded-xl border overflow-hidden">
            <div class="px-5 py-3 border-b bg-gray-50/50"><h4 class="text-sm font-bold text-gray-700">Access History</h4></div>
            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">User</th><th class="px-4 py-2 font-medium">Action</th><th class="px-4 py-2 font-medium">IP</th><th class="px-4 py-2 font-medium">Time</th></tr></thead>
                <tbody>
                @forelse($document->accessLogs->take(15) as $log)
                <tr class="border-t border-gray-100"><td class="px-4 py-2 text-xs text-gray-700">{{ $log->user?->name ?? 'Unknown' }}</td><td class="px-4 py-2 text-xs capitalize">{{ $log->action }}</td><td class="px-4 py-2 text-xs text-gray-400">{{ $log->ip_address }}</td><td class="px-4 py-2 text-xs text-gray-400">{{ $log->created_at->format('d M Y H:i') }}</td></tr>
                @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">No access logs</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>
    </div>
</div>
@endsection
