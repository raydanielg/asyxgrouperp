@extends('layouts.admin')

@section('title', 'Branches')

@section('content')
<div class="max-w-6xl mx-auto space-y-4">
  {{-- Header --}}
  <div class="bg-white rounded-md border border-gray-200 px-5 py-4 flex items-center justify-between">
    <div>
      <h3 class="text-sm font-bold text-gray-900">Branches</h3>
      <p class="text-[11px] text-gray-400 mt-0.5">{{ $branches->total() }} branch{{ $branches->total() === 1 ? '' : 'es' }} · assign users to restrict their organizational scope</p>
    </div>
    <button onclick="openDrawer('createBranch')" class="px-4 py-2 bg-slate-900 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">+ New Branch</button>
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <table class="w-full text-xs">
      <thead>
        <tr class="bg-gray-50 border-b border-gray-200 text-left text-[10px] uppercase tracking-widest text-gray-500">
          <th class="px-5 py-3 font-semibold">Branch</th>
          <th class="px-5 py-3 font-semibold">Code</th>
          @if(auth()->user()->isSuperAdmin())<th class="px-5 py-3 font-semibold">Company</th>@endif
          <th class="px-5 py-3 font-semibold">Users</th>
          <th class="px-5 py-3 font-semibold">Status</th>
          <th class="px-5 py-3 font-semibold text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($branches as $branch)
        <tr class="hover:bg-gray-50/60">
          <td class="px-5 py-3">
            <div class="font-semibold text-gray-900">{{ $branch->name }}</div>
            @if($branch->address)<div class="text-[10px] text-gray-400">{{ $branch->address }}</div>@endif
          </td>
          <td class="px-5 py-3 font-mono text-gray-600">{{ $branch->code ?? '—' }}</td>
          @if(auth()->user()->isSuperAdmin())<td class="px-5 py-3 text-gray-600">{{ $branch->company?->name }}</td>@endif
          <td class="px-5 py-3 font-mono text-gray-600">{{ $branch->users_count }}</td>
          <td class="px-5 py-3">
            <span class="inline-flex items-center gap-1.5 text-[11px]">
              <span class="w-1.5 h-1.5 rounded-full {{ $branch->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
              {{ $branch->is_active ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td class="px-5 py-3 text-right whitespace-nowrap">
            <button onclick='editBranch(@json($branch))' class="text-xs font-medium text-gray-600 hover:text-gray-900 mr-3">Edit</button>
            <form method="POST" action="{{ route('admin.branches.destroy', $branch) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Delete branch {{ $branch->name }}?'})">
              @csrf @method('DELETE')
              <button class="text-xs font-medium text-gray-500 hover:text-red-600">Delete</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-xs">No branches yet — create one to enable branch-level scoping.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($branches->hasPages())<div class="px-5 py-3 border-t border-gray-100">{{ $branches->links() }}</div>@endif
  </div>
</div>

{{-- Create drawer --}}
<div id="createBranch" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/30" onclick="closeDrawer('createBranch')"></div>
  <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-xl flex flex-col">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
      <h4 class="text-sm font-bold text-gray-900">New Branch</h4>
      <button onclick="closeDrawer('createBranch')" class="text-gray-400 hover:text-gray-700 text-lg">&times;</button>
    </div>
    <form method="POST" action="{{ route('admin.branches.store') }}" class="p-5 space-y-4 overflow-y-auto flex-1">
      @csrf
      <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
        <input name="name" required placeholder="e.g. Dar es Salaam HQ" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Code</label>
          <input name="code" placeholder="DSM" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 font-mono">
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
          <input name="phone" placeholder="+255 22 000 000" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
        </div>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
        <input name="address" placeholder="Street, city" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
      </div>
      @if(auth()->user()->isSuperAdmin())
      <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Company</label>
        <select name="company_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
          <option value="">— My current company —</option>
          @foreach(\App\Models\Company::orderBy('name')->get() as $c)
          <option value="{{ $c->id }}">{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      @endif
      <p class="text-[10px] text-gray-400">Users with no branch assignment keep company-wide access. Assign users to restrict them.</p>
      <button type="submit" class="w-full py-2.5 bg-slate-900 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">Create Branch</button>
    </form>
  </div>
</div>

{{-- Edit drawer --}}
<div id="editBranch" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/30" onclick="closeDrawer('editBranch')"></div>
  <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-xl flex flex-col">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
      <h4 class="text-sm font-bold text-gray-900">Edit Branch</h4>
      <button onclick="closeDrawer('editBranch')" class="text-gray-400 hover:text-gray-700 text-lg">&times;</button>
    </div>
    <form id="editBranchForm" method="POST" class="p-5 space-y-4 overflow-y-auto flex-1">
      @csrf @method('PATCH')
      <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
        <input id="ebName" name="name" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Code</label>
          <input id="ebCode" name="code" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 font-mono">
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
          <input id="ebPhone" name="phone" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
        </div>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
        <input id="ebAddress" name="address" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
      </div>
      <label class="flex items-center gap-2 text-xs text-gray-700">
        <input type="checkbox" name="is_active" value="1" id="ebActive" class="rounded border-gray-300"> Active
      </label>
      <button type="submit" class="w-full py-2.5 bg-slate-900 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">Save Changes</button>
    </form>
  </div>
</div>

<script>
function openDrawer(id){document.getElementById(id).classList.remove('hidden')}
function closeDrawer(id){document.getElementById(id).classList.add('hidden')}
function editBranch(b){
  document.getElementById('editBranchForm').action='/admin/branches/'+b.id;
  document.getElementById('ebName').value=b.name||'';
  document.getElementById('ebCode').value=b.code||'';
  document.getElementById('ebPhone').value=b.phone||'';
  document.getElementById('ebAddress').value=b.address||'';
  document.getElementById('ebActive').checked=!!b.is_active;
  openDrawer('editBranch');
}
</script>
@endsection
