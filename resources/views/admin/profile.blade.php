@php
$title = 'My Profile';
$description = 'Manage your profile, password and preferences.';
@endphp
@extends('layouts.admin')
@section('title', $title)
@section('page_title', $title)
@section('content')


<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Account</h4></div>
            <div class="p-5">
                <div class="flex items-center gap-4">
                    <div class="relative flex-shrink-0">
                        @if($user->avatar)
                        <img id="profileAvatar" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full object-cover border border-gray-200">
                        @else
                        <div id="profileAvatarFallback" class="w-16 h-16 rounded-full bg-slate-700 flex items-center justify-center text-white text-xl font-bold">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                        @endif
                        <button onclick="openAvatarModal()" class="absolute -bottom-0.5 -right-0.5 w-6 h-6 rounded-full bg-gray-900 text-white hover:bg-gray-700 flex items-center justify-center transition-colors border-2 border-white" title="Change photo">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 truncate">{{ $user->name ?? 'User' }}</h3>
                        <p class="text-xs text-gray-400 truncate">{{ $user->email ?? '-' }}</p>
                    </div>
                </div>
                <dl class="mt-5 space-y-2.5 border-t border-gray-100 pt-4">
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Role</dt><dd class="text-xs text-gray-700 capitalize">{{ $user->isAdmin() ? 'Administrator' : $user->role }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Company</dt><dd class="text-xs text-gray-700">{{ $user->company?->name ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Member since</dt><dd class="text-xs text-gray-700">{{ $user->created_at?->format('M d, Y') ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Login</dt><dd class="text-xs text-gray-700 inline-flex items-center gap-1.5"><span style="width:6px;height:6px;border-radius:99px;background:{{ ($user->is_enable_login ?? true) ? '#10b981' : '#ef4444' }}"></span>{{ ($user->is_enable_login ?? true) ? 'Enabled' : 'Disabled' }}</dd></div>
                </dl>
            </div>
        </div>

        {{-- Avatar Upload Modal --}}
        <div id="avatarModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4" onclick="if(event.target===this)closeAvatarModal()">
            <div class="bg-white rounded-md shadow-2xl max-w-sm w-full p-6" onclick="event.stopPropagation()">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div><h3 class="text-base font-bold text-gray-900">Update Avatar</h3><p class="text-xs text-gray-500">JPG, PNG, GIF, WebP up to 2MB</p></div>
                    </div>
                    <button onclick="closeAvatarModal()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                </div>
                <form id="avatarForm" enctype="multipart/form-data">
                    @csrf
                    <div class="border border-dashed border-gray-200 rounded-md p-6 text-center hover:border-gray-400 transition-colors cursor-pointer" onclick="document.getElementById('avatarInput').click()">
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                        <img id="avatarPreview" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-3 {{ $user->avatar ? '' : 'hidden' }}">
                        <div id="avatarPlaceholder" class="{{ $user->avatar ? 'hidden' : '' }}">
                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="text-xs text-gray-500">Click to upload photo</p>
                        </div>
                        <p id="avatarFileName" class="text-xs text-gray-600 font-medium mt-2"></p>
                    </div>
                    <div class="flex gap-2 pt-4">
                        <button type="button" onclick="closeAvatarModal()" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded hover:bg-gray-50">Cancel</button>
                        <button type="submit" id="avatarSaveBtn" class="flex-1 px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700">Save Avatar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-4">Profile Information</h3>
            <form id="profileForm" onsubmit="saveProfile(event)">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ $user->name ?? '' }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">First Name</label>
                        <input type="text" name="first_name" value="{{ $user->first_name ?? '' }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Last Name</label>
                        <input type="text" name="last_name" value="{{ $user->last_name ?? '' }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ $user->email ?? '' }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ $user->phone ?? '' }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end">
                    <button type="submit" id="saveProfileBtn" class="px-4 py-2 bg-gray-900 text-white rounded text-sm font-medium hover:bg-gray-700 transition-colors">Save Profile</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-md border border-gray-200 p-5">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-4">Change Password</h3>
            <form id="passwordForm" onsubmit="changePassword(event)">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Current Password <span class="text-red-500">*</span></label>
                        <input type="password" name="current_password" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">New Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Confirm New Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end">
                    <button type="submit" id="changePasswordBtn" class="px-4 py-2 bg-gray-900 text-white rounded text-sm font-medium hover:bg-gray-700 transition-colors">Change Password</button>
                </div>
            </form>
        </div>

        {{-- My Permissions --}}
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-4">My Permissions</h3>
            @php
                $userPerms = $user->permissionNames();
                $permGroups = [];
                foreach ($userPerms as $perm) {
                    $parts = explode('-', $perm);
                    $action = array_shift($parts);
                    $module = implode(' ', $parts);
                    $module = ucwords($module);
                    if (!isset($permGroups[$module])) $permGroups[$module] = [];
                    $permGroups[$module][] = $action;
                }
                ksort($permGroups);
            @endphp
            <div class="flex flex-wrap gap-2">
                @foreach ($permGroups as $module => $actions)
                    <div class="border border-gray-200 rounded px-3 py-2">
                        <p class="text-[10px] font-bold text-gray-700 uppercase tracking-wider">{{ $module }}</p>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach ($actions as $action)
                                <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-medium bg-gray-100 text-gray-600">{{ ucfirst($action) }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            @if (empty($userPerms))
                <p class="text-xs text-gray-400 text-center py-4">No specific permissions assigned. You may have full admin access.</p>
            @endif
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

function openAvatarModal() {
    document.getElementById('avatarModal').classList.remove('hidden');
}
function closeAvatarModal() {
    document.getElementById('avatarModal').classList.add('hidden');
}
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
            document.getElementById('avatarPreview').classList.remove('hidden');
            document.getElementById('avatarPlaceholder').classList.add('hidden');
            document.getElementById('avatarFileName').textContent = input.files[0].name;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('avatarForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const input = document.getElementById('avatarInput');
    if (!input.files[0]) {
        Swal.fire({ icon: 'warning', title: 'No Image', text: 'Please select an image first.', confirmButtonColor: '#024938' });
        return;
    }
    const btn = document.getElementById('avatarSaveBtn');
    btn.disabled = true;
    btn.textContent = 'Uploading...';
    const formData = new FormData(form);
    fetch('{{ route('admin.profile.avatar') }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    })
    .then(async r => {
        const text = await r.text();
        try { return JSON.parse(text); }
        catch (e) { throw new Error('Server returned invalid response: ' + text.substring(0, 200)); }
    })
    .then(res => {
        btn.disabled = false;
        btn.textContent = 'Save Avatar';
        if (res.success) {
            closeAvatarModal();
            let img = document.getElementById('profileAvatar');
            if (!img) {
                const fallback = document.getElementById('profileAvatarFallback');
                if (fallback) {
                    img = document.createElement('img');
                    img.id = 'profileAvatar';
                    img.src = res.avatar_url;
                    img.alt = document.querySelector('h3')?.textContent || 'User';
                    img.className = 'w-16 h-16 rounded-full object-cover border border-gray-200';
                    fallback.parentNode.replaceChild(img, fallback);
                }
            } else {
                img.src = res.avatar_url + '?t=' + Date.now();
            }
            updateSidebarAvatar(res.avatar_url + '?t=' + Date.now());
            Swal.fire({ icon: 'success', title: 'Avatar Updated', text: res.message, timer: 2000, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to upload avatar.', confirmButtonColor: '#024938' });
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Save Avatar';
        Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Network error. Please try again.', confirmButtonColor: '#024938' });
    });
});

function updateSidebarAvatar(url) {
    const sidebarImg = document.querySelector('#adminSidebar img[src*="avatars"]');
    if (sidebarImg) sidebarImg.src = url;
    const sidebarFallback = document.querySelector('#adminSidebar .w-9.h-9.rounded-full');
    if (sidebarFallback && !sidebarImg) {
        const parent = sidebarFallback.parentNode;
        const img = document.createElement('img');
        img.src = url;
        img.className = 'w-9 h-9 rounded-full object-cover border-2 border-white';
        parent.replaceChild(img, sidebarFallback);
    }
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