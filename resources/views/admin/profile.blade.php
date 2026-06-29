@php
$title = 'My Profile';
$description = 'Manage your profile, password and preferences.';
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl border p-6 text-center">
            <div class="w-24 h-24 mx-auto rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-2xl font-bold mb-4">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            </div>
            <h3 class="text-lg font-bold text-gray-900">{{ $user->name ?? 'User' }}</h3>
            <p class="text-sm text-gray-500">{{ $user->email ?? '-' }}</p>
            <p class="text-xs text-emerald-600 mt-2 inline-flex items-center px-2 py-1 rounded-full bg-emerald-50 border border-emerald-200 capitalize">
                {{ $user->isAdmin() ? 'Administrator' : $user->role }}
            </p>
            <p class="text-xs text-gray-400 mt-4">Member since {{ $user->created_at?->format('M d, Y') ?? '-' }}</p>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Profile Information
            </h3>
            <form id="profileForm" onsubmit="saveProfile(event)">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ $user->name ?? '' }}" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" name="first_name" value="{{ $user->first_name ?? '' }}" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" value="{{ $user->last_name ?? '' }}" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ $user->email ?? '' }}" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ $user->phone ?? '' }}" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end">
                    <button type="submit" id="saveProfileBtn" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">Save Profile</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl border p-6">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Change Password
            </h3>
            <form id="passwordForm" onsubmit="changePassword(event)">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Current Password <span class="text-rose-500">*</span></label>
                        <input type="password" name="current_password" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">New Password <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Confirm New Password <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end">
                    <button type="submit" id="changePasswordBtn" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

function saveProfile(e) {
    e.preventDefault();
    const form = document.getElementById('profileForm');
    const btn = document.getElementById('saveProfileBtn');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    btn.disabled = true;
    btn.textContent = 'Saving...';

    fetch('{{ route('admin.profile.update') }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.textContent = 'Save Profile';
        if (res.success) {
            Swal.fire({ icon: 'success', title: 'Saved!', text: res.message, timer: 1500, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to update profile.', confirmButtonColor: '#024938' });
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Save Profile';
        Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' });
    });
}

function changePassword(e) {
    e.preventDefault();
    const form = document.getElementById('passwordForm');
    const btn = document.getElementById('changePasswordBtn');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    if (data.password !== data.password_confirmation) {
        Swal.fire({ icon: 'warning', title: 'Mismatch', text: 'New password and confirmation do not match.', confirmButtonColor: '#024938' });
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Updating...';

    fetch('{{ route('admin.profile.password') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.textContent = 'Change Password';
        if (res.success) {
            form.reset();
            Swal.fire({ icon: 'success', title: 'Updated!', text: res.message, timer: 1500, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to change password.', confirmButtonColor: '#024938' });
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Change Password';
        Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#024938' });
    });
}
</script>
@endpush
@endsection