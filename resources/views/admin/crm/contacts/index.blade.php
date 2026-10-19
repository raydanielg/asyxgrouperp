@extends('layouts.admin')
@section('title', 'CRM Contacts - ' . config('app.name'))
@section('page_title', 'Customers / Contacts')
@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">Manage customer and client contacts</span>
        <button type="button" onclick="openContactDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Contact</button>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm" id="contactsTable">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Name</th>
                <th class="px-5 py-3 font-semibold">Company</th>
                <th class="px-5 py-3 font-semibold">Position</th>
                <th class="px-5 py-3 font-semibold">Email</th>
                <th class="px-5 py-3 font-semibold">Phone</th>
                <th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody id="contactsBody" class="divide-y divide-gray-100">
        @forelse($contacts as $c)
        <tr class="hover:bg-gray-50/60" id="contact-row-{{ $c->id }}">
            <td class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-[10px] uppercase">{{ substr($c->first_name ?? '?', 0, 1) }}{{ substr($c->last_name ?? '', 0, 1) }}</div>
                    <span class="text-xs font-medium text-gray-900 contact-name">{{ $c->full_name }}</span>
                </div>
            </td>
            <td class="px-5 py-3.5 text-xs text-gray-500 contact-company">{{ $c->company ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500 contact-position">{{ $c->position ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-700 contact-email">{{ $c->email ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500 contact-phone">{{ $c->phone ?? '—' }}</td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <button onclick="editContact({{ $c->id }})" class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</button>
                    <button onclick="deleteContact({{ $c->id }})" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                </div>
            </td>
        </tr>
        @empty
        <tr id="noContactsRow"><td colspan="6" class="px-5 py-10 text-center text-xs text-gray-400">No contacts found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($contacts->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $contacts->links() }}</div>
    @endif
</div>

{{-- Contact Drawer --}}
<div id="contactDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeContactDrawer()"></div>
    <div id="contactDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 id="contactDrawerTitle" class="text-sm font-bold text-white">New Contact</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Customer or client contact</p>
            </div>
            <button type="button" onclick="closeContactDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="contactForm" class="flex-1 overflow-y-auto p-5">
            @csrf
            <input type="hidden" id="contactId" name="contact_id" value="">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">First Name <span class="text-red-500">*</span></label><input id="firstName" name="first_name" required placeholder="e.g. John" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Last Name</label><input id="lastName" name="last_name" placeholder="e.g. Mushi" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Company</label><input id="company" name="company" placeholder="e.g. Acme Ltd" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Position</label><input id="position" name="position" placeholder="e.g. Procurement Manager" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label><input id="email" name="email" type="email" placeholder="name@company.com" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input id="phone" name="phone" placeholder="+255 700 000 000" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Address</label><textarea id="address" name="address" rows="2" placeholder="Physical address..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea id="notes" name="notes" rows="3" placeholder="Context, preferences..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Save Contact</button>
                <button type="button" onclick="closeContactDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const storeUrl = '{{ route('admin.crm-contacts.store') }}';

function openContactDrawer() {
    document.getElementById('contactDrawerTitle').textContent = 'New Contact';
    document.getElementById('contactForm').reset();
    document.getElementById('contactId').value = '';
    document.getElementById('contactDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('contactDrawerPanel').classList.remove('-translate-x-full'));
}
function closeContactDrawer() {
    document.getElementById('contactDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('contactDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeContactDrawer(); });

function fullName(contact) {
    return (contact.first_name + ' ' + (contact.last_name || '')).trim();
}

function updateRow(contact) {
    const row = document.getElementById('contact-row-' + contact.id);
    if (row) {
        row.querySelector('.contact-name').textContent = fullName(contact);
        row.querySelector('.contact-company').textContent = contact.company || '—';
        row.querySelector('.contact-position').textContent = contact.position || '—';
        row.querySelector('.contact-email').textContent = contact.email || '—';
        row.querySelector('.contact-phone').textContent = contact.phone || '—';
    }
}

function addRow(contact) {
    const tbody = document.getElementById('contactsBody');
    const noRow = document.getElementById('noContactsRow');
    if (noRow) noRow.remove();
    const initials = ((contact.first_name||'?')[0] + (contact.last_name||'')[0]).toUpperCase();
    const tr = document.createElement('tr');
    tr.className = 'hover:bg-gray-50/60';
    tr.id = 'contact-row-' + contact.id;
    tr.innerHTML = `<td class="px-5 py-3.5"><div class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-[10px] uppercase">${initials}</div><span class="text-xs font-medium text-gray-900 contact-name">${fullName(contact)}</span></div></td>
        <td class="px-5 py-3.5 text-xs text-gray-500 contact-company">${contact.company || '—'}</td>
        <td class="px-5 py-3.5 text-xs text-gray-500 contact-position">${contact.position || '—'}</td>
        <td class="px-5 py-3.5 text-xs text-gray-700 contact-email">${contact.email || '—'}</td>
        <td class="px-5 py-3.5 text-xs text-gray-500 contact-phone">${contact.phone || '—'}</td>
        <td class="px-5 py-3.5">
            <div class="flex items-center justify-end gap-4">
                <button onclick="editContact(${contact.id})" class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</button>
                <button onclick="deleteContact(${contact.id})" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
            </div>
        </td>`;
    tbody.insertBefore(tr, tbody.firstChild);
}

function editContact(id) {
    fetch('{{ url('admin/crm-contacts') }}/' + id + '/edit', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(data => {
        const c = data.contact;
        document.getElementById('contactDrawerTitle').textContent = 'Edit Contact';
        document.getElementById('contactId').value = c.id;
        document.getElementById('firstName').value = c.first_name || '';
        document.getElementById('lastName').value = c.last_name || '';
        document.getElementById('company').value = c.company || '';
        document.getElementById('position').value = c.position || '';
        document.getElementById('email').value = c.email || '';
        document.getElementById('phone').value = c.phone || '';
        document.getElementById('address').value = c.address || '';
        document.getElementById('notes').value = c.notes || '';
        document.getElementById('contactDrawer').classList.remove('hidden');
        requestAnimationFrame(() => document.getElementById('contactDrawerPanel').classList.remove('-translate-x-full'));
    });
}

function deleteContact(id) {
    Swal.fire({
        title: 'Delete Contact?',
        text: 'This contact will be permanently removed.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Deleting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch('{{ url('admin/crm-contacts') }}/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const row = document.getElementById('contact-row-' + id);
                    if (row) row.remove();
                    Swal.fire({ icon: 'success', title: 'Deleted', text: 'Contact has been removed.', confirmButtonColor: '#374151', timer: 2000 });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not delete contact.', confirmButtonColor: '#374151' });
                }
            });
        }
    });
}

document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const id = document.getElementById('contactId').value;
    const formData = new FormData(form);
    let url = storeUrl;
    if (id) {
        url = '{{ url('admin/crm-contacts') }}/' + id;
        formData.append('_method', 'PATCH');
    }
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.contact) {
            if (id) {
                updateRow(data.contact);
                Swal.fire({ icon: 'success', title: 'Updated', text: fullName(data.contact) + ' has been updated.', confirmButtonColor: '#374151', timer: 2000 });
            } else {
                addRow(data.contact);
                Swal.fire({ icon: 'success', title: 'Created', text: fullName(data.contact) + ' has been added.', confirmButtonColor: '#374151', timer: 2000 });
            }
            closeContactDrawer();
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Could not save contact.', confirmButtonColor: '#374151' });
        }
    })
    .catch(err => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.', confirmButtonColor: '#374151' });
    });
});
</script>
@endpush
@endsection
