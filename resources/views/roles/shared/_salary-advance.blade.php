<div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="relative z-10">
        <h2 class="text-2xl font-bold">Salary Advance</h2>
        <p class="text-emerald-100 text-sm mt-1">Request and track salary advance.</p>
    </div>
</div>

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Pending</p>
        <p class="text-2xl font-bold text-gray-900 mt-1" id="pendingCount">{{ $pendingCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Approved</p>
        <p class="text-2xl font-bold text-gray-900 mt-1" id="approvedCount">{{ $approvedCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase">Total Requested</p>
        <p class="text-2xl font-bold text-gray-900 mt-1" id="totalRequested">{{ $money($totalRequested ?? 0) }}</p>
    </div>
</div>

<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-2">
        <input type="text" id="searchAdvance" placeholder="Search requests..." class="px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-500 w-64">
        <select id="statusAdvance" class="px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
        <button onclick="loadAdvances()" class="px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700">Search</button>
    </div>
    <button onclick="openAdvanceModal()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Request
    </button>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Reason</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody id="advanceTableBody" class="divide-y divide-gray-100">
                <tr><td colspan="5" class="px-4 py-8 text-center text-xs text-gray-400">Loading...</td></tr>
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t flex items-center justify-between">
        <span class="text-xs text-gray-500" id="advanceTotal">Total records: 0</span>
        <div class="flex items-center gap-2" id="advancePagination"></div>
    </div>
</div>

<!-- Request Modal -->
<div id="advanceModal" class="fixed inset-0 z-50 hidden" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="closeAdvanceModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-emerald-700 px-4 py-3 sm:px-6">
                    <h3 class="text-base font-semibold text-white">Request Salary Advance</h3>
                </div>
                <form id="advanceForm" onsubmit="submitAdvance(event)" class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Amount <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.01" name="amount" id="advanceAmount" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Requested Date <span class="text-rose-500">*</span></label>
                            <input type="date" name="requested_date" id="advanceDate" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Reason</label>
                            <textarea name="reason" id="advanceReason" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeAdvanceModal()" class="px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" id="advanceSubmitBtn" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
const advanceIndexUrl = '{{ route('reception.salary-advance.index') }}';
const advanceStoreUrl = '{{ route('reception.salary-advance.store') }}';
const advanceDestroyUrl = '{{ route('reception.salary-advance.destroy', ['salaryAdvanceRequest' => '__ID__']) }}';
const advanceStatusUrl = '{{ route('reception.salary-advance.status', ['salaryAdvanceRequest' => '__ID__']) }}';

function statusBadge(status) {
    const map = {
        pending: 'bg-amber-50 text-amber-700 border border-amber-200',
        approved: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        rejected: 'bg-rose-50 text-rose-700 border border-rose-200',
    };
    return `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium ${map[status] || map.pending}">${status}</span>`;
}

function money(n) {
    return 'TZS ' + Number(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function loadAdvances(page = 1) {
    const search = document.getElementById('searchAdvance').value;
    const status = document.getElementById('statusAdvance').value;
    const url = new URL(advanceIndexUrl, window.location.origin);
    url.searchParams.append('page', page);
    if (search) url.searchParams.append('search', search);
    if (status) url.searchParams.append('status', status);

    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            document.getElementById('pendingCount').textContent = res.pendingCount ?? 0;
            document.getElementById('approvedCount').textContent = res.approvedCount ?? 0;
            document.getElementById('totalRequested').textContent = money(res.totalRequested ?? 0);
            const tbody = document.getElementById('advanceTableBody');
            const data = res.requests?.data ?? [];
            if (!data.length) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-xs text-gray-400">No salary advance requests found.</td></tr>';
            } else {
                tbody.innerHTML = data.map(item => `
                    <tr class="hover:bg-gray-50/50" id="advanceRow${item.id}">
                        <td class="px-4 py-3 text-xs text-gray-600">${item.requested_date ? new Date(item.requested_date).toLocaleDateString() : '-'}</td>
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">${money(item.amount)}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">${item.reason || '-'}</td>
                        <td class="px-4 py-3 text-xs">${statusBadge(item.status)}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                ${['approved','rejected'].includes(item.status) ? '' : `<button onclick="approveAdvance(${item.id})" class="text-emerald-600 hover:text-emerald-800 p-1 rounded hover:bg-emerald-50" title="Approve"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></button>`}
                                <button onclick="deleteAdvance(${item.id})" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            }
            document.getElementById('advanceTotal').textContent = 'Total records: ' + (res.requests?.total ?? 0);
            document.getElementById('advancePagination').innerHTML = res.requests?.links ? res.requests.links.replace(/href="[^"]*\?page=([0-9]+)[^"]*"/g, 'href="javascript:loadAdvances($1)"') : '';
        })
        .catch(() => {
            document.getElementById('advanceTableBody').innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-xs text-red-400">Failed to load requests.</td></tr>';
        });
}

function openAdvanceModal() {
    document.getElementById('advanceAmount').value = '';
    document.getElementById('advanceDate').value = new Date().toISOString().slice(0, 10);
    document.getElementById('advanceReason').value = '';
    document.getElementById('advanceModal').classList.remove('hidden');
}

function closeAdvanceModal() {
    document.getElementById('advanceModal').classList.add('hidden');
}

function submitAdvance(e) {
    e.preventDefault();
    const btn = document.getElementById('advanceSubmitBtn');
    const data = {
        amount: document.getElementById('advanceAmount').value,
        requested_date: document.getElementById('advanceDate').value,
        reason: document.getElementById('advanceReason').value,
    };
    btn.disabled = true;
    btn.textContent = 'Submitting...';
    fetch(advanceStoreUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.textContent = 'Submit Request';
        if (res.success) {
            closeAdvanceModal();
            Swal.fire({ icon: 'success', title: 'Submitted!', text: res.message, timer: 1500, showConfirmButton: false });
            loadAdvances();
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to submit.', confirmButtonColor: '#024938' });
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Submit Request';
        Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' });
    });
}

function approveAdvance(id) {
    Swal.fire({
        title: 'Approve request?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Approve',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = advanceStatusUrl.replace('__ID__', id);
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ status: 'approved' })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Approved!', text: res.message, timer: 1500, showConfirmButton: false });
                loadAdvances();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to approve.', confirmButtonColor: '#024938' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error.', confirmButtonColor: '#024938' }));
    });
}

function deleteAdvance(id) {
    Swal.fire({
        title: 'Delete request?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = advanceDestroyUrl.replace('__ID__', id);
        fetch(url, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false });
                loadAdvances();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to delete.', confirmButtonColor: '#024938' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error.', confirmButtonColor: '#024938' }));
    });
}

document.addEventListener('DOMContentLoaded', loadAdvances);
</script>
@endpush
