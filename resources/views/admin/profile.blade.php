@extends('layouts.admin')
@section('title', 'Profile - ' . config('app.name'))
@section('page_title', 'My Profile')
@section('content')
<div class="animate-fade max-w-5xl">
  {{-- Profile Header --}}
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-5">
    <div class="h-24 bg-gradient-to-r from-emerald-700 via-emerald-600 to-emerald-500 relative">
      <div class="absolute -bottom-10 left-8">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-white font-bold text-3xl shadow-lg border-4 border-white">
          {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->name ?? '', strpos($user->name ?? ' ') ?: 1, 1)) }}
        </div>
      </div>
    </div>
    <div class="pt-12 px-8 pb-6">
      <div class="flex items-start justify-between">
        <div>
          <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
          <p class="text-sm text-gray-500">{{ $user->email }}</p>
          <div class="flex items-center gap-2 mt-2">
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100 capitalize">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
              {{ $user->isAdmin() ? 'Administrator' : $user->role }}
            </span>
            @if($user->phone)
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
              {{ $user->phone }}
            </span>
            @endif
          </div>
        </div>
        <div class="text-right">
          <p class="text-[10px] text-gray-400">Member since</p>
          <p class="text-xs font-semibold text-gray-600">{{ $user->created_at->format('d M Y') }}</p>
        </div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
    {{-- Profile Information --}}
    <div class="lg:col-span-3 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-white border-b border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
          <div><h3 class="text-sm font-bold text-gray-900">Profile Information</h3><p class="text-[11px] text-gray-500">Update your personal details</p></div>
        </div>
      </div>
      <form method="POST" action="{{ route('admin.profile.update') }}" class="p-6 space-y-4">
        @csrf @method('PATCH')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Full Name <span class="text-red-400">*</span></label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <input name="name" value="{{ old('name', $user->name) }}" required class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            @error('name')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email Address <span class="text-red-400">*</span></label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              <input name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            @error('email')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone Number</label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
              <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+255 712 345 678" class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
          </div>
        </div>
        <div class="pt-4 border-t border-gray-50 flex justify-end">
          <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-bold rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-sm shadow-emerald-200 inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Save Changes
          </button>
        </div>
      </form>
    </div>

    {{-- Change Password --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-white border-b border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center"><svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
          <div><h3 class="text-sm font-bold text-gray-900">Security</h3><p class="text-[11px] text-gray-500">Change your password</p></div>
        </div>
      </div>
      <form method="POST" action="{{ route('admin.password.update') }}" class="p-6 space-y-4">
        @csrf @method('PATCH')
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Current Password <span class="text-red-400">*</span></label>
          <div class="relative"><input name="current_password" type="password" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none pr-10"><button type="button" onclick="const e=this.previousElementSibling;e.type=e.type==='password'?'text':'password'" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button></div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">New Password <span class="text-red-400">*</span></label>
          <input name="password" type="password" required minlength="6" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
          @error('password')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Confirm New Password <span class="text-red-400">*</span></label>
          <input name="password_confirmation" type="password" required minlength="6" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        </div>
        <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-bold rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-sm shadow-emerald-200 inline-flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          Update Password
        </button>
      </form>
    </div>
  </div>
</div>
@endsection