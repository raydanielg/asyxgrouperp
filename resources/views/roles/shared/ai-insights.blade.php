@if(!empty($aiInsights['message']) || !empty($aiInsights['suggestions']))
<div class="bg-gradient-to-r from-indigo-600 to-violet-700 rounded-xl p-5 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="relative z-10 flex items-start gap-3">
        <div class="bg-white/20 p-2 rounded-lg shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <div>
            <h3 class="text-sm font-bold text-white mb-1">AI Insights</h3>
            <p class="text-xs text-white/90 mb-2">{{ $aiInsights['message'] }}</p>
            @if(!empty($aiInsights['suggestions']))
            <ul class="space-y-1">
                @foreach($aiInsights['suggestions'] as $suggestion)
                <li class="flex items-start gap-2 text-xs text-white/85">
                    <span class="inline-block w-1 h-1 rounded-full bg-yellow-300 mt-1.5"></span>
                    {{ $suggestion }}
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endif
