@extends('layouts.admin')

@section('title', 'Helpdesk Categories - ' . config('app.name'))
@section('page_title', 'Helpdesk Categories')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage ticket categories</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Category
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($categories as $category)
    <div class="bg-white rounded-xl border p-5 hover:shadow-lg transition-shadow">
        <div class="flex items-start justify-between mb-2">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full" style="background: {{ $category->color }}"></span>
                <h3 class="text-sm font-bold text-gray-900">{{ $category->name }}</h3>
            </div>
            <span class="text-xs text-gray-400">{{ $category->tickets_count }} tickets</span>
        </div>
        <p class="text-xs text-gray-400 mb-3">{{ $category->description ?? 'No description' }}</p>
        <form method="POST" action="{{ route('admin.helpdesk-categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Delete this category?')">
            @csrf @method('DELETE')
            <button class="text-red-500 hover:text-red-700 text-xs">Delete</button>
        </form>
    </div>
    @empty
    <div class="col-span-full text-center py-8 text-gray-400 text-sm">No categories found</div>
    @endforelse
</div>
<div class="px-1 mt-4">{{ $categories->links() }}</div>

<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Category</h3>
        <form method="POST" action="{{ route('admin.helpdesk-categories.store') }}" class="space-y-3">
            @csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Name *</label><input name="name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Color *</label><input name="color" type="color" value="#3B82F6" required class="w-full h-10 rounded-lg border border-gray-200 cursor-pointer"></div>
            <div><label class="flex items-center gap-2"><input type="checkbox" name="is_active" checked class="rounded border-gray-300 text-emerald-600"><span class="text-xs text-gray-600">Active</span></label></div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection
