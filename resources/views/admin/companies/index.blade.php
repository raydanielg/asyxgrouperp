@extends('layouts.admin')
@section('title', 'Companies - ' . config('app.name'))
@section('page_title', 'Multi-Company Management')
@section('content')
@php
    $isSuper = auth()->user()?->isSuperAdmin();
@endphp

<div class="mb-4 flex items-center justify-between gap-3">
    <form method="GET" class="flex items-center gap-2 flex-1 max-w-sm">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search companies..." class="w-full px-3 py-2 rounded border border-gray-200 text-sm focus:border-gray-400 focus:ring-0 outline-none">
        <button class="px-3 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.companies.index') }}" class="text-xs text-gray-400 hover:text-gray-600">Clear</a>
        @endif
    </form>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.companies.export') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export
        </a>
        <button type="button" onclick="openCompanyDrawer()" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded hover:bg-gray-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Company
        </button>
    </div>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded bg-red-50 text-red-700 text-sm border border-red-100">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left border-b border-gray-200 bg-gray-50/50">
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Company</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Code</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Type</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Subscription</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Users</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($companies as $company)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5">
                <a href="{{ route('admin.companies.show', $company) }}" class="font-medium text-gray-900 hover:underline">{{ $company->legal_name }}</a>
                <p class="text-gray-400 text-xs mt-0.5">{{ $company->address ?? '—' }}</p>
            </td>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-500">{{ $company->short_code }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $company->is_group ? 'Group' : 'Operating' }}</td>
            <td class="px-5 py-3.5">
                <p class="text-xs font-medium text-gray-700">{{ \App\Models\Company::PLANS[$company->subscription_plan] ?? ucfirst($company->subscription_plan ?? '—') }}</p>
                <p class="text-[11px] text-gray-400">
                    @if($company->mrr){{ number_format($company->mrr, 0) }}/{{ $company->billing_cycle === 'yearly' ? 'yr' : 'mo' }} · @endif
                    @if($company->subscription_ends_at)ends {{ $company->subscription_ends_at->format('d M Y') }}@else no expiry @endif
                </p>
            </td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $company->users()->count() }}@if($company->max_users)/{{ $company->max_users }}@endif</td>
            <td class="px-5 py-3.5">
                @php
                    $st = !$company->is_active ? ['Inactive','#9ca3af']
                        : ($company->subscription_status === 'suspended' ? ['Suspended','#ef4444']
                        : ($company->subscription_status === 'past_due' ? ['Past Due','#f59e0b']
                        : ($company->subscription_status === 'trial' ? ['Trial','#8b5cf6']
                        : ($company->subscription_status === 'cancelled' ? ['Cancelled','#ef4444']
                        : ['Active','#10b981']))));
                @endphp
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-700">
                    <span style="width:6px;height:6px;border-radius:99px;background:{{ $st[1] }}"></span>{{ $st[0] }}
                </span>
            </td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.companies.show', $company) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Manage</a>
                    <button type="button" onclick="openCompanyDrawer({{ $company->id }})" class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</button>
                    @if($isSuper && !$company->is_group)
                        @if($company->subscription_status === 'suspended')
                        <form method="POST" action="{{ route('admin.companies.activate', $company) }}" class="inline" onsubmit="return confirmAction(this, {title:'Reactivate Company?', text:'{{ addslashes($company->name) }} — all {{ $company->users()->count() }} user(s) will be able to log in again.', icon:'question', confirmText:'Yes, activate', color:'#059669'})">@csrf<button class="text-xs font-medium text-gray-600 hover:text-gray-900">Activate</button></form>
                        @else
                        <form method="POST" action="{{ route('admin.companies.suspend', $company) }}" class="inline" onsubmit="return confirmAction(this, {title:'Suspend Company?', text:'{{ addslashes($company->name) }} — ALL {{ $company->users()->count() }} of its users will be locked out immediately.', confirmText:'Yes, suspend', color:'#d97706'})">@csrf<button class="text-xs font-medium text-amber-600 hover:text-amber-800">Suspend</button></form>
                        @endif
                    @endif
                    @if(!$company->is_group)
                    <form method="POST" action="{{ route('admin.companies.destroy', $company) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete Company?', text:'{{ addslashes($company->name) }} will be permanently removed. This cannot be undone.', confirmText:'Yes, delete'})">@csrf @method('DELETE')<button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No companies found</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>

<div class="mt-4">
    <a href="{{ route('admin.companies.consolidated') }}" class="inline-flex items-center gap-2 text-xs font-medium text-gray-500 hover:text-gray-800">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        View Consolidated Report →
    </a>
</div>

{{-- ═══ Add Company Drawer (slides from left) ═══ --}}
<div id="companyDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeCompanyDrawer()"></div>
    <div id="companyDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white" id="drawerTitle">New Company</h3>
                <p class="text-[11px] text-slate-400 mt-0.5" id="drawerSubtitle">Register a subsidiary under ASYX Group</p>
            </div>
            <button type="button" onclick="closeCompanyDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="companyForm" method="POST" action="{{ route('admin.companies.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST" disabled>

            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Identity</p>
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" id="f_name" name="name" required placeholder="e.g. Parktech Tanzania" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Legal Name <span class="text-red-500">*</span></label>
                    <input type="text" id="f_legal_name" name="legal_name" required placeholder="e.g. Parktech Tanzania Limited" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Short Code <span class="text-red-500">*</span></label>
                        <input type="text" id="f_short_code" name="short_code" required maxlength="10" placeholder="PARKTECH" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 uppercase font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Currency <span class="text-red-500">*</span></label>
                        <input type="text" id="f_currency" name="currency" required maxlength="3" placeholder="TZS" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Registration Number</label>
                    <input type="text" id="f_reg" name="registration_number" placeholder="e.g. REG-2024-001" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Parent Company</label>
                    <select id="f_parent" name="parent_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                        <option value="">— None —</option>
                        @foreach(\App\Models\Company::where('is_group', true)->pluck('legal_name', 'id') as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Tax &amp; Contact</p>
            <div class="space-y-3 mb-5">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tax ID (TIN)</label>
                        <input type="text" id="f_tax_id" name="tax_id" placeholder="e.g. 100-222-333" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">VRN</label>
                        <input type="text" id="f_vrn" name="vrn" placeholder="e.g. 40-012345-A" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                    <input type="text" id="f_phone" name="phone" placeholder="e.g. +255 22 000 0000" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                    <input type="email" id="f_email" name="email" placeholder="e.g. info@company.co.tz" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Website</label>
                    <input type="text" id="f_website" name="website" placeholder="https://" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
            </div>

            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Location</p>
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                    <textarea id="f_address" name="address" rows="2" placeholder="Street, area, P.O. Box" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">City</label>
                        <input type="text" id="f_city" name="city" placeholder="Dar es Salaam" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Country</label>
                        <input type="text" id="f_country" name="country" placeholder="Tanzania" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-6 mb-5">
                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" id="f_is_group" name="is_group" value="1" class="rounded border-gray-300 text-gray-900 focus:ring-gray-400"> Group Company
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" id="f_is_active" name="is_active" value="1" checked class="rounded border-gray-300 text-gray-900 focus:ring-gray-400"> Active
                </label>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" id="drawerSubmit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Company</button>
                <button type="button" onclick="closeCompanyDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
@php
    $drawerCompanies = $companies->keyBy('id')->map(fn($c) => $c->only(['id','name','legal_name','short_code','currency','registration_number','parent_id','tax_id','vrn','phone','email','website','address','city','country','is_group','is_active']));
@endphp
const companiesData = @json($drawerCompanies);

function openCompanyDrawer(id = null) {
    const form = document.getElementById('companyForm');
    const method = document.getElementById('formMethod');
    const title = document.getElementById('drawerTitle');
    const sub = document.getElementById('drawerSubtitle');
    const btn = document.getElementById('drawerSubmit');

    if (id && companiesData[id]) {
        const c = companiesData[id];
        title.textContent = 'Edit Company';
        sub.textContent = 'Update ' + c.name;
        btn.textContent = 'Save Changes';
        form.action = '{{ url("/admin/companies") }}/' + id;
        method.disabled = false;
        method.value = 'PATCH';

        f_name.value = c.name || ''; f_legal_name.value = c.legal_name || '';
        f_short_code.value = c.short_code || ''; f_currency.value = c.currency || 'TZS';
        f_reg.value = c.registration_number || ''; f_parent.value = c.parent_id || '';
        f_tax_id.value = c.tax_id || ''; f_vrn.value = c.vrn || '';
        f_phone.value = c.phone || ''; f_email.value = c.email || ''; f_website.value = c.website || '';
        f_address.value = c.address || ''; f_city.value = c.city || ''; f_country.value = c.country || '';
        f_is_group.checked = !!c.is_group; f_is_active.checked = !!c.is_active;
    } else {
        title.textContent = 'New Company';
        sub.textContent = 'Register a subsidiary under ASYX Group';
        btn.textContent = 'Create Company';
        form.action = '{{ route("admin.companies.store") }}';
        method.disabled = true;
        method.value = 'POST';
        form.reset();
        f_currency.value = 'TZS'; f_city.value = 'Dar es Salaam'; f_country.value = 'Tanzania';
        f_is_active.checked = true;
    }

    const d = document.getElementById('companyDrawer');
    d.classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('companyDrawerPanel').classList.remove('-translate-x-full'));
}
function closeCompanyDrawer() {
    const p = document.getElementById('companyDrawerPanel');
    p.classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('companyDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCompanyDrawer(); });
</script>
@endsection
