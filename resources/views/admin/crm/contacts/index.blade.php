@extends('layouts.admin')
@section('title', 'CRM Contacts - ' . config('app.name'))
@section('page_title', 'Customers / Contacts')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage customer and client contacts</p>
    <button onclick="openContactModal()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Contact
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm" id="contactsTable">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Name</th><th class="px-5 py-3 font-medium">Company</th><th class="px-5 py-3 font-medium">Position</th><th class="px-5 py-3 font-medium">Email</th><th class="px-5 py-3 font-medium">Phone</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody id="contactsBody">
        @forelse($contacts as $c)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50" id="contact-row-{{ $c->id }}">
            <td class="px-5 py-3 text-xs font-medium text-gray-900 contact-name">{{ $c->full_name }}</td>
            <td class="px-5 py-3 text-xs text-gray-500 contact-company">{{ $c->company ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500 contact-position">{{ $c->position ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-700 contact-email">{{ $c->email ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500 contact-phone">{{ $c->phone ?? '—' }}</td>
            <td class="px-5 py-3">
                <div class="flex items-center gap-2">
                    <button onclick="editContact({{ $c->id }})" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-all" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                    <button onclick="deleteContact({{ $c->id }})" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-all" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr id="noContactsRow"><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No contacts found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $contacts->links() }}</div>
</div>

<div id="contactModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)closeContactModal()">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 id="contactModalTitle" class="text-lg font-bold text-gray-900 mb-4">New Contact</h3>
        <form id="contactForm" class="space-y-3">
            @csrf
            <input type="hidden" id="contactId" name="contact_id" value="">
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">First Name *</label><input id="firstName" name="first_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Last Name</label><input id="lastName" name="last_name" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Company</label><input id="company" name="company" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Position</label><input id="position" name="position" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label><input id="email" name="email" type="email" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input id="phone" name="phone" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Address</label><textarea id="address" name="address" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea id="notes" name="notes" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeContactModal()" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Save</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const storeUrl = '{{ route('admin.crm-contacts.store') }}';

function openContactModal() {
    document.getElementById('contactModalTitle').textContent = 'New Contact';
    document.getElementById('contactForm').reset();
    document.getElementById('contactId').value = '';
    document.getElementById('contactModal').classList.remove('hidden');
}
function closeContactModal() {
    document.getElementById('contactModal').classList.add('hidden');
}

function fullName(contact) {
    return (contact.first_name + ' ' + (contact.last_name || '')).trim();
}

function updateRow(contact) {
    const row = document.getElementById('contact-row-' + contact.id);
    if (row) {
        row.querySelector('.contact-name').textContent = fullName(contact);
        row.querySelector('.contact-company').textContent = contact.company || 'N/A';
        row.querySelector('.contact-position').textContent = contact.position || 'N/A';
        row.querySelector('.contact-email').textContent = contact.email || '—';
        row.querySelector('.contact-phone').textContent = contact.phone || '—';
    }
}

function addRow(contact) {
    const tbody = document.getElementById('contactsBody');
    const noRow = document.getElementById('noContactsRow');
    if (noRow) noRow.remove();
    const tr = document.createElement('tr');
    tr.className = 'border-t border-gray-100 hover:bg-gray-50/50';
    tr.id = 'contact-row-' + contact.id;
    tr.innerHTML = `<td class="px-5 py-3 text-xs font-medium text-gray-900 contact-name">${fullName(contact)}</td>
        <td class="px-5 py-3 text-xs text-gray-500 contact-company">${contact.company || 'N/A'}</td>
        <td class="px-5 py-3 text-xs text-gray-500 contact-position">${contact.position || 'N/A'}</td>
        <td class="px-5 py-3 text-xs text-gray-700 contact-email">${contact.email || '—'}</td>
        <td class="px-5 py-3 text-xs text-gray-500 contact-phone">${contact.phone || '—'}</td>
        <td class="px-5 py-3">
            <div class="flex items-center gap-2">
                <button onclick="editContact(${contact.id})" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-all" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                <button onclick="deleteContact(${contact.id})" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-all" title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </td>`;
    tbody.insertBefore(tr, tbody.firstChild);
}

function editContact(id) {
    fetch('{{ url('admin/crm-contacts') }}/' + id + '/edit', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(data => {
        const c = data.contact;
        document.getElementById('contactModalTitle').textContent = 'Edit Contact';
        document.getElementById('contactId').value = c.id;
        document.getElementById('firstName').value = c.first_name || '';
        document.getElementById('lastName').value = c.last_name || '';
        document.getElementById('company').value = c.company || '';
        document.getElementById('position').value = c.position || '';
        document.getElementById('email').value = c.email || '';
        document.getElementById('phone').value = c.phone || '';
        document.getElementById('address').value = c.address || '';
        document.getElementById('notes').value = c.notes || '';
        document.getElementById('contactModal').classList.remove('hidden');
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
        confirmButtonText: 'Yes, delete it',
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
                    Swal.fire({ icon: 'success', title: 'Deleted', text: 'Contact has been removed.', confirmButtonColor: '#024938', timer: 2000 });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not delete contact.', confirmButtonColor: '#024938' });
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
    let method = 'POST';
    if (id) {
        url = '{{ url('admin/crm-contacts') }}/' + id;
        formData.append('_method', 'PATCH');
    }
    fetch(url, {
        method: method,
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.contact) {
            if (id) {
                updateRow(data.contact);
                Swal.fire({ icon: 'success', title: 'Updated', text: fullName(data.contact) + ' has been updated.', confirmButtonColor: '#024938', timer: 2000 });
            } else {
                addRow(data.contact);
                Swal.fire({ icon: 'success', title: 'Created', text: fullName(data.contact) + ' has been added.', confirmButtonColor: '#024938', timer: 2000 });
            }
            closeContactModal();
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Could not save contact.', confirmButtonColor: '#024938' });
        }
    })
    .catch(err => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.', confirmButtonColor: '#024938' });
    });
});
</script>
@endpush
@endsection
