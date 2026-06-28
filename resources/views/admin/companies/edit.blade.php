@extends('layouts.admin')
@section('title', 'Edit Company - ' . config('app.name'))
@section('page_title', 'Edit Company')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.companies.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">&larr; Back to Companies</a>
</div>

<div class="bg-white rounded-xl border p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.companies.update', $company) }}">
        @csrf @method('PATCH')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $company->name) }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Legal Name <span class="text-red-500">*</span></label>
                <input type="text" name="legal_name" value="{{ old('legal_name', $company->legal_name) }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Short Code <span class="text-red-500">*</span></label>
                <input type="text" name="short_code" value="{{ old('short_code', $company->short_code) }}" required maxlength="10" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 uppercase">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Currency <span class="text-red-500">*</span></label>
                <input type="text" name="currency" value="{{ old('currency', $company->currency) }}" required maxlength="3" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Registration Number</label>
                <input type="text" name="registration_number" value="{{ old('registration_number', $company->registration_number) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tax ID</label>
                <input type="text" name="tax_id" value="{{ old('tax_id', $company->tax_id) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $company->email) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                <textarea name="address" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('address', $company->address) }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">City</label>
                <input type="text" name="city" value="{{ old('city', $company->city) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Country</label>
                <input type="text" name="country" value="{{ old('country', $company->country) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Parent Company</label>
                <select name="parent_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">— None —</option>
                    @foreach($parents as $id => $name)
                    <option value="{{ $id }}" @selected(old('parent_id', $company->parent_id) == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Website</label>
                <input type="text" name="website" value="{{ old('website', $company->website) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2 flex items-center gap-6 pt-2">
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="is_group" value="1" @checked(old('is_group', $company->is_group)) class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    Is Group Company
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $company->is_active)) class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    Active
                </label>
            </div>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="px-5 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">Update Company</button>
            <a href="{{ route('admin.companies.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">Cancel</a>
        </div>
    </form>
</div>
@endsection
