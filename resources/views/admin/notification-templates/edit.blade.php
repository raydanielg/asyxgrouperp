@extends('layouts.admin')
@section('title', 'Edit Notification Template - ' . config('app.name'))
@section('page_title', 'Edit Notification Template')
@section('content')
@php
    $hintMap = [
        'registration' => ['user_name', 'user_email', 'app_name'],
        'auth' => ['user_name', 'reset_code'],
        'member' => ['user_name', 'invited_by', 'company_name', 'app_name'],
        'onboarding' => ['user_name', 'company_name', 'plan_name', 'order_id'],
        'sale' => ['sale_number', 'cashier', 'currency', 'total', 'payment_method', 'change'],
        'invoice' => ['invoice_number', 'customer_name', 'currency', 'total', 'due_date', 'amount', 'balance'],
        'quotation' => ['quotation_number', 'customer_name', 'currency', 'total', 'valid_until'],
        'approval' => ['approver_name', 'requester_name', 'request_title', 'module', 'currency', 'amount', 'level', 'total_levels', 'reason'],
        'purchase' => ['po_number', 'supplier_name', 'currency', 'total', 'status'],
        'inventory' => ['product_name', 'warehouse', 'quantity', 'reorder_level', 'transfer_id', 'from_warehouse', 'to_warehouse'],
        'payroll' => ['employee_name', 'period', 'currency', 'net_pay'],
        'hr' => ['employee_name', 'leave_type', 'start_date', 'end_date', 'reason', 'approver_name'],
        'helpdesk' => ['ticket_id', 'ticket_title', 'priority', 'requester_name', 'replier_name', 'reply_excerpt', 'resolver_name'],
        'project' => ['assignee_name', 'assigned_by', 'task_title', 'project_name', 'due_date', 'milestone_name'],
        'billing' => ['company_name', 'plan_name', 'currency', 'amount', 'next_date', 'days_left', 'expiry_date', 'order_id', 'user_name'],
    ];
    $vars = $hintMap[$notificationTemplate->type] ?? ['user_name', 'company_name', 'app_name'];
@endphp

<a href="{{ route('admin.notification-templates.index') }}" class="text-xs text-gray-400 hover:text-gray-600">← Notification Templates</a>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-3">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Edit Template</h3>
                <span class="inline-flex items-center gap-1.5 text-[10px] text-gray-500"><span style="width:6px;height:6px;border-radius:99px;background:{{ $notificationTemplate->is_active ? '#10b981' : '#d1d5db' }}"></span>{{ $notificationTemplate->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
            <form method="POST" action="{{ route('admin.notification-templates.update', $notificationTemplate) }}" class="p-5 space-y-4">
                @csrf @method('PATCH')
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label><input name="name" value="{{ old('name', $notificationTemplate->name) }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Type</label><input name="type" value="{{ old('type', $notificationTemplate->type) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 capitalize"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Subject</label><input name="subject" id="tplSubject" value="{{ old('subject', $notificationTemplate->subject) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Content</label><textarea name="content" id="tplContent" rows="11" class="w-full px-3 py-2 text-sm font-mono border border-gray-200 rounded outline-none focus:border-gray-400 leading-relaxed">{{ old('content', $notificationTemplate->content) }}</textarea></div>
                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer"><input type="checkbox" name="is_active" {{ old('is_active', $notificationTemplate->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">Active</label>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.notification-templates.index') }}" class="text-xs font-medium text-gray-500 hover:text-gray-800">Cancel</a>
                        <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Update Template</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Variables</h3></div>
            <div class="p-4">
                <p class="text-[10px] text-gray-400 mb-3">Click to insert into content</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($vars as $v)
                    <button type="button" onclick="insertVar('{{ '{' . $v . '}' }}')" class="px-2 py-1 text-[10px] font-mono bg-gray-100 hover:bg-gray-900 hover:text-white text-gray-700 rounded transition-colors">{{ '{' . $v . '}' }}</button>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Preview</h3></div>
            <div class="p-4">
                <p class="text-xs font-semibold text-gray-900 mb-2" id="prevSubject">{{ $notificationTemplate->subject }}</p>
                <p class="text-[11px] text-gray-600 whitespace-pre-wrap leading-relaxed" id="prevContent">{{ $notificationTemplate->content }}</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function insertVar(v) {
    const ta = document.getElementById('tplContent');
    const start = ta.selectionStart, end = ta.selectionEnd;
    ta.value = ta.value.substring(0, start) + v + ta.value.substring(end);
    ta.focus(); ta.selectionStart = ta.selectionEnd = start + v.length;
    ta.dispatchEvent(new Event('input'));
}
document.getElementById('tplSubject').addEventListener('input', e => document.getElementById('prevSubject').textContent = e.target.value);
document.getElementById('tplContent').addEventListener('input', e => document.getElementById('prevContent').textContent = e.target.value);
</script>
@endpush
@endsection
