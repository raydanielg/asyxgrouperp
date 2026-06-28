@extends('layouts.admin')
@section('title', 'New Workflow - ' . config('app.name'))
@section('page_title', 'Create Approval Workflow')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.approvals.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">&larr; Back to Workflows</a>
</div>
<div class="bg-white rounded-xl border p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.approvals.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Workflow Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Module <span class="text-red-500">*</span></label>
                <select name="module" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">— Select —</option>
                    @foreach($modules as $value => $label)
                    <option value="{{ $value }}" @selected(old('module') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('description') }}</textarea>
            </div>
            <div class="col-span-2">
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    Active
                </label>
            </div>
        </div>

        <div class="mt-6 border-t pt-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-bold text-gray-700">Approval Steps</h4>
                <button type="button" onclick="addStep()" class="text-xs text-emerald-600 hover:text-emerald-700">+ Add Step</button>
            </div>
            <div id="stepsContainer" class="space-y-3"></div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="px-5 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">Create Workflow</button>
            <a href="{{ route('admin.approvals.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">Cancel</a>
        </div>
    </form>
</div>
<script>
let stepIndex = 0;
function addStep() {
    stepIndex++;
    const html = `
    <div class="grid grid-cols-12 gap-2 items-start border rounded-lg p-3 bg-gray-50/50" id="step-${stepIndex}">
        <div class="col-span-4"><input type="text" name="steps[${stepIndex}][name]" placeholder="Step name (e.g. Procurement Approval)" required class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg"></div>
        <div class="col-span-3"><select name="steps[${stepIndex}][approver_type]" class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg"><option value="role">By Role</option><option value="user">Specific User</option><option value="manager">Manager</option></select></div>
        <div class="col-span-3"><input type="text" name="steps[${stepIndex}][approver_role]" placeholder="Role name" class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg"></div>
        <div class="col-span-1 flex items-center"><label class="flex items-center text-[10px] text-gray-500"><input type="checkbox" name="steps[${stepIndex}][is_final]" value="1" class="rounded border-gray-300 text-emerald-600"> Final</label></div>
        <div class="col-span-1"><button type="button" onclick="document.getElementById('step-${stepIndex}').remove()" class="text-xs text-red-500">Remove</button></div>
    </div>`;
    document.getElementById('stepsContainer').insertAdjacentHTML('beforeend', html);
}
addStep();
</script>
@endsection
