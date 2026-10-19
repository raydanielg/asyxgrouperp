@extends('layouts.admin')
@section('title', 'Add User - ' . config('app.name'))
@section('page_title', 'Add User')
@section('content')
<div class="max-w-4xl">
  <div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Users</a>
  </div>

  <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
    @csrf

    {{-- User Information --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">User Information</h4></div>
      <div class="p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
            <input name="name" required value="{{ old('name') }}" placeholder="John Doe" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
            @error('name')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Email Address <span class="text-red-500">*</span></label>
            <input name="email" type="email" required value="{{ old('email') }}" placeholder="john@asyxgroup.tz" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
            @error('email')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Phone Number</label>
            <input name="phone" value="{{ old('phone') }}" placeholder="+255 712 345 678" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">System Role <span class="text-red-500">*</span></label>
            <select name="role" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
              <option value="user" @selected(old('role')==='user')>User</option>
              <option value="admin" @selected(old('role')==='admin')>Admin</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Company</label>
            <select name="company_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
              <option value="">— No company —</option>
              @foreach($companies as $company)
              <option value="{{ $company->id }}" @selected(old('company_id')==$company->id)>{{ $company->name }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Password <span class="text-red-500">*</span></label>
            <input name="password" type="password" required minlength="6" placeholder="Min. 6 characters" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
            @error('password')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Confirm Password <span class="text-red-500">*</span></label>
            <input name="password_confirmation" type="password" required minlength="6" placeholder="Repeat password" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
          </div>
        </div>
        <label class="inline-flex items-center gap-2.5 mt-4 cursor-pointer">
          <input type="checkbox" name="is_enable_login" value="1" checked class="w-4 h-4 rounded border-gray-300 text-gray-700 focus:ring-gray-400">
          <span class="text-xs font-medium text-gray-700">Enable Login <span class="font-normal text-gray-400">— user can access the system immediately</span></span>
        </label>
      </div>
    </div>

    {{-- Roles --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Roles &amp; Permissions</h4></div>
      <div class="p-5">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
          @foreach($roles as $role)
          <label class="flex items-start gap-2.5 p-3 rounded border border-gray-200 cursor-pointer hover:border-gray-300 has-[:checked]:border-gray-400 has-[:checked]:bg-gray-50">
            <input type="checkbox" name="assigned_roles[]" value="{{ $role->id }}" {{ in_array($role->id, old('assigned_roles', [])) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded border-gray-300 text-gray-700 focus:ring-gray-400">
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-gray-900 leading-tight">{{ $role->label }}</p>
              <p class="text-[10px] text-gray-400 mt-0.5">{{ $role->permissions->count() }} permission{{ $role->permissions->count() !== 1 ? 's' : '' }}</p>
            </div>
          </label>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-between gap-3 bg-white rounded-md border border-gray-200 px-5 py-3.5">
      <a href="{{ route('admin.users.index') }}" class="text-xs text-gray-500 hover:text-gray-800">Cancel</a>
      <button type="submit" class="px-5 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Create User</button>
    </div>
  </form>
</div>
@endsection
