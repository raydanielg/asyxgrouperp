@extends('layouts.admin')
@section('title', 'Add User - ' . config('app.name'))
@section('page_title', 'Add User')
@section('content')
<div class="animate-fade max-w-4xl">
  <div class="mb-4 flex items-center gap-2">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-emerald-600 transition-colors">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Back to Users
    </a>
  </div>

  <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
    @csrf
    {{-- User Information --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-white border-b border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
          <div><h3 class="text-sm font-bold text-gray-900">User Information</h3><p class="text-[11px] text-gray-500">Basic account details</p></div>
        </div>
      </div>
      <div class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Full Name <span class="text-red-400">*</span></label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <input name="name" required value="{{ old('name') }}" placeholder="John Doe" class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            @error('name')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email Address <span class="text-red-400">*</span></label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              <input name="email" type="email" required value="{{ old('email') }}" placeholder="john@asyxgroup.tz" class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            @error('email')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone Number</label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
              <input name="phone" value="{{ old('phone') }}" placeholder="+255 712 345 678" class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">System Role <span class="text-red-400">*</span></label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <select name="role" required class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all appearance-none bg-white">
                <option value="user" @selected(old('role')==='user')>User</option>
                <option value="admin" @selected(old('role')==='admin')>Admin</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Password <span class="text-red-400">*</span></label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              <input name="password" type="password" required minlength="6" class="w-full pl-9 pr-10 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" id="createPass">
              <button type="button" onclick="const e=document.getElementById('createPass');e.type=e.type==='password'?'text':'password'" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
            </div>
            @error('password')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Confirm Password <span class="text-red-400">*</span></label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              <input name="password_confirmation" type="password" required minlength="6" class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
          </div>
        </div>
        <label class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-lg border border-gray-100 bg-gray-50/50 cursor-pointer hover:bg-emerald-50/30 transition-colors">
          <input type="checkbox" name="is_enable_login" value="1" checked class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
          <div><span class="text-xs font-medium text-gray-700">Enable Login</span><p class="text-[10px] text-gray-400">User can access the system immediately</p></div>
        </label>
      </div>
    </div>

    {{-- Roles Assignment --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="px-6 py-4 bg-gradient-to-r from-violet-50 to-white border-b border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center"><svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
          <div><h3 class="text-sm font-bold text-gray-900">Assign Roles & Permissions</h3><p class="text-[11px] text-gray-500">Select which roles this user should have</p></div>
        </div>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
          @foreach($roles as $role)
          <label class="relative flex items-start gap-3 p-3.5 rounded-xl border border-gray-100 cursor-pointer transition-all hover:border-emerald-200 hover:bg-emerald-50/30 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 has-[:checked]:ring-1 has-[:checked]:ring-emerald-200">
            <input type="checkbox" name="assigned_roles[]" value="{{ $role->id }}" {{ in_array($role->id, old('assigned_roles', [])) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
            <div class="flex-1 min-w-0">
              <p class="text-xs font-semibold text-gray-900">{{ $role->label }}</p>
              <p class="text-[10px] text-gray-400 mt-0.5">{{ $role->permissions->count() }} permission{{ $role->permissions->count() !== 1 ? 's' : '' }}</p>
            </div>
          </label>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-between gap-3 bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-4">
      <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Cancel
      </a>
      <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-bold rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-sm shadow-emerald-200 inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        Create User
      </button>
    </div>
  </form>
</div>
@endsection