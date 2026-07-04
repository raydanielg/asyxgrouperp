@if($users instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $users->lastPage() > 1)
<div class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
  <div class="text-xs text-gray-500">
    Showing <span class="font-medium text-gray-700">{{ $users->firstItem() ?? 0 }}</span> to <span class="font-medium text-gray-700">{{ $users->lastItem() ?? 0 }}</span> of <span class="font-medium text-gray-700">{{ $users->total() }}</span> users
  </div>
  <div class="ajax-pagination flex items-center gap-1">
    @if($users->onFirstPage())
    <span class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-300 text-xs cursor-not-allowed">Previous</span>
    @else
    <button onclick="loadUsersPage('{{ $users->previousPageUrl() }}')" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 text-xs hover:bg-gray-50 transition-colors">Previous</button>
    @endif

    @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
    @if($page == $users->currentPage())
    <span class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-medium">{{ $page }}</span>
    @else
    <button onclick="loadUsersPage('{{ $url }}')" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 text-xs hover:bg-gray-50 transition-colors">{{ $page }}</button>
    @endif
    @endforeach

    @if($users->hasMorePages())
    <button onclick="loadUsersPage('{{ $users->nextPageUrl() }}')" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 text-xs hover:bg-gray-50 transition-colors">Next</button>
    @else
    <span class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-300 text-xs cursor-not-allowed">Next</span>
    @endif
  </div>
</div>
@endif
