@extends('layouts.auth')

@section('title', 'Company Setup - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-3xl" style="animation: simpleFadeIn 0.4s ease-out both;">

    {{-- Stepper --}}
    <div class="flex items-center justify-center gap-2 mb-6">
        @php $steps = ['Account', 'Company Details', 'Select Plan']; @endphp
        @foreach($steps as $i => $label)
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-full {{ $i === 0 ? 'bg-gray-900' : ($i === 1 ? 'bg-gray-900' : 'bg-white/20 border border-white/40') }} flex items-center justify-center text-[11px] font-bold text-white">
                @if($i === 0)<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else{{ $i + 1 }}@endif
            </span>
            <span class="text-[11px] font-semibold {{ $i <= 1 ? 'text-white' : 'text-white/60' }}">{{ $label }}</span>
        </div>
        @if(!$loop->last)<div class="w-8 h-px bg-white/30"></div>@endif
        @endforeach
    </div>

    <form method="POST" action="{{ route('onboarding.store') }}">
        @csrf
        <div class="bg-white/95 backdrop-blur-xl rounded-md shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)] border border-white/60 overflow-hidden">
            <div class="px-7 py-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Set up your company</h2>
                <p class="text-xs text-gray-400 mt-0.5">Tell us about your business and choose a plan</p>
            </div>

            <div class="p-7 space-y-7">
                @if($errors->any())
                <div class="px-4 py-3 rounded bg-red-50 border border-red-200 text-xs text-red-600">{{ $errors->first() }}</div>
                @endif

                {{-- Company details --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Company Details</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Company Name <span class="text-red-500">*</span></label><input name="company_name" value="{{ old('company_name') }}" required placeholder="e.g. Mwanza General Stores Ltd" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Legal Name</label><input name="legal_name" value="{{ old('legal_name') }}" placeholder="Registered legal name" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Company Email</label><input name="email" type="email" value="{{ old('email', auth()->user()->email) }}" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" value="{{ old('phone') }}" placeholder="+255 700 000 000" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Website</label><input name="website" value="{{ old('website') }}" placeholder="https://" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400"></div>
                        <div class="sm:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Address</label><input name="address" value="{{ old('address') }}" placeholder="P.O. Box / Street" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">City</label><input name="city" value="{{ old('city') }}" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Country</label><input name="country" value="{{ old('country', 'Tanzania') }}" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">TIN</label><input name="tax_id" value="{{ old('tax_id') }}" placeholder="Tax Identification Number" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">VRN</label><input name="vrn" value="{{ old('vrn') }}" placeholder="VAT Registration Number" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400"></div>
                    </div>
                </div>

                {{-- Billing cycle --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Billing Cycle</p>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cycle-option cursor-pointer">
                            <input type="radio" name="billing_cycle" value="monthly" checked class="peer sr-only">
                            <div class="border border-gray-200 rounded px-4 py-3 text-center peer-checked:border-gray-900 peer-checked:bg-gray-50 transition-all">
                                <p class="text-xs font-bold text-gray-900">Monthly</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Pay per month</p>
                            </div>
                        </label>
                        <label class="cycle-option cursor-pointer">
                            <input type="radio" name="billing_cycle" value="yearly" class="peer sr-only">
                            <div class="border border-gray-200 rounded px-4 py-3 text-center peer-checked:border-gray-900 peer-checked:bg-gray-50 transition-all">
                                <p class="text-xs font-bold text-gray-900">Yearly</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Billed once a year</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Plan cards --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Choose a Plan</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($plans as $i => $plan)
                        <label class="cursor-pointer">
                            <input type="radio" name="plan_id" value="{{ $plan->id }}" {{ $i === 1 ? 'checked' : '' }} class="peer sr-only" required>
                            <div class="border border-gray-200 rounded-md p-4 h-full peer-checked:border-gray-900 peer-checked:bg-gray-50 transition-all hover:border-gray-400">
                                <div class="flex items-start justify-between mb-2">
                                    <p class="text-xs font-bold text-gray-900">{{ $plan->name }}</p>
                                    @if($plan->trial)<span class="text-[9px] font-bold text-amber-600 uppercase">{{ $plan->trial_days }}d trial</span>@endif
                                </div>
                                <p class="text-lg font-bold text-gray-900 font-mono"><span class="plan-price" data-monthly="{{ number_format($plan->package_price_monthly) }}" data-yearly="{{ number_format($plan->package_price_yearly) }}">{{ number_format($plan->package_price_monthly) }}</span> <span class="text-[10px] font-normal text-gray-400">TZS<span class="plan-period">/mo</span></span></p>
                                <div class="mt-2 space-y-1 text-[10px] text-gray-500">
                                    <p>{{ $plan->number_of_users }} users</p>
                                    <p>{{ number_format($plan->storage_limit) }} KB storage</p>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="px-7 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <p class="text-[11px] text-gray-400">Your request will be reviewed by our team</p>
                <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded hover:bg-gray-700 transition-colors">Request Access →</button>
            </div>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('input[name="billing_cycle"]').forEach(r => {
    r.addEventListener('change', () => {
        const cycle = document.querySelector('input[name="billing_cycle"]:checked').value;
        document.querySelectorAll('.plan-price').forEach(el => {
            el.textContent = cycle === 'yearly' ? el.dataset.yearly : el.dataset.monthly;
        });
        document.querySelectorAll('.plan-period').forEach(el => { el.textContent = cycle === 'yearly' ? '/yr' : '/mo'; });
    });
});
</script>
@endsection
