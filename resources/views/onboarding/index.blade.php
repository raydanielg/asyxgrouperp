@extends('layouts.auth')

@section('title', 'Company Setup - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-3xl" style="animation: simpleFadeIn 0.4s ease-out both;">

    {{-- Stepper --}}
    <div class="flex items-center justify-center gap-2 mb-6" id="stepper">
        @foreach(['Account', 'Company Details', 'Select Plan', 'Review'] as $i => $label)
        <div class="flex items-center gap-2 step-item" data-step="{{ $i }}">
            <span class="step-dot w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold text-white transition-all">{{ $i + 1 }}</span>
            <span class="step-label text-[11px] font-semibold hidden sm:inline">{{ $label }}</span>
        </div>
        @if(!$loop->last)<div class="step-line w-8 h-px transition-all"></div>@endif
        @endforeach
    </div>

    <form method="POST" action="{{ route('onboarding.store') }}" id="wizardForm" novalidate>
        @csrf
        <div class="bg-white/95 backdrop-blur-xl rounded-md shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)] border border-white/60 overflow-hidden">

            {{-- STEP 1: Company Details --}}
            <div class="wizard-step" data-step="1">
                <div class="px-7 py-5 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900">Company Details</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Basic information about your business</p>
                </div>
                <div class="p-7">
                    @if($errors->any())
                    <div class="px-4 py-3 rounded bg-red-50 border border-red-200 text-xs text-red-600 mb-4">{{ $errors->first() }}</div>
                    @endif
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Company Name <span class="text-red-500">*</span></label><input name="company_name" id="companyName" value="{{ old('company_name') }}" required placeholder="e.g. Mwanza General Stores Ltd" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400 transition-all"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Legal Name</label><input name="legal_name" value="{{ old('legal_name') }}" placeholder="Registered legal name" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400 transition-all"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Company Email <span class="text-red-500">*</span></label><input name="email" id="companyEmail" type="email" value="{{ old('email', auth()->user()->email) }}" required placeholder="info@company.com" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400 transition-all"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" value="{{ old('phone') }}" placeholder="+255 712 345 678" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400 transition-all"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Website</label><input name="website" value="{{ old('website') }}" placeholder="https://yourcompany.co.tz" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400 transition-all"></div>
                        <div class="sm:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Address</label><input name="address" value="{{ old('address') }}" placeholder="P.O. Box 1234, Street name" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400 transition-all"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">City</label><input name="city" id="companyCity" value="{{ old('city') }}" placeholder="Dar es Salaam" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400 transition-all"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Country</label><input name="country" value="{{ old('country', 'Tanzania') }}" placeholder="Tanzania" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400 transition-all"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">TIN</label><input name="tax_id" value="{{ old('tax_id') }}" placeholder="123-456-789" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400 transition-all"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">VRN</label><input name="vrn" value="{{ old('vrn') }}" placeholder="40-012345-X" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded bg-gray-50 focus:bg-white outline-none focus:border-gray-400 transition-all"></div>
                    </div>
                </div>
            </div>

            {{-- STEP 2: Plan --}}
            <div class="wizard-step hidden" data-step="2">
                <div class="px-7 py-5 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900">Choose a Plan</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Select billing cycle and a package that fits your business</p>
                </div>
                <div class="p-7 space-y-6">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Billing Cycle</p>
                        <div class="grid grid-cols-2 gap-3 max-w-sm">
                            <label class="cursor-pointer">
                                <input type="radio" name="billing_cycle" value="monthly" checked class="peer sr-only">
                                <div class="border border-gray-200 rounded px-4 py-3 text-center peer-checked:border-gray-900 peer-checked:bg-gray-50 transition-all">
                                    <p class="text-xs font-bold text-gray-900">Monthly</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Pay per month</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="billing_cycle" value="yearly" class="peer sr-only">
                                <div class="border border-gray-200 rounded px-4 py-3 text-center peer-checked:border-gray-900 peer-checked:bg-gray-50 transition-all">
                                    <p class="text-xs font-bold text-gray-900">Yearly</p>
                                    <p class="text-[10px] text-amber-600 font-bold mt-0.5">Save ~17%</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Package</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @foreach($plans as $i => $plan)
                            <label class="cursor-pointer">
                                <input type="radio" name="plan_id" value="{{ $plan->id }}" data-plan-name="{{ $plan->name }}" data-users="{{ $plan->number_of_users }}" data-monthly="{{ number_format($plan->package_price_monthly) }}" data-yearly="{{ number_format($plan->package_price_yearly) }}" {{ $i === 1 ? 'checked' : '' }} class="peer sr-only" required>
                                <div class="border border-gray-200 rounded-md p-4 h-full peer-checked:border-gray-900 peer-checked:bg-gray-50 peer-checked:ring-2 peer-checked:ring-gray-200 transition-all hover:border-gray-400 relative">
                                    <div class="plan-check absolute top-3 right-3 w-4 h-4 rounded-full bg-gray-900 hidden items-center justify-center"><svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>
                                    <p class="text-xs font-bold text-gray-900 mb-1.5">{{ $plan->name }}</p>
                                    <p class="text-lg font-bold text-gray-900 font-mono leading-none"><span class="plan-price">{{ number_format($plan->package_price_monthly) }}</span> <span class="text-[10px] font-normal text-gray-400">TZS<span class="plan-period">/mo</span></span></p>
                                    <div class="mt-3 space-y-1 text-[10px] text-gray-500">
                                        <p>{{ $plan->number_of_users }} users</p>
                                        <p>{{ number_format($plan->storage_limit / 1024) }} MB storage</p>
                                        @if($plan->trial)<p class="text-amber-600 font-semibold">{{ $plan->trial_days }} days free trial</p>@endif
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 3: Review --}}
            <div class="wizard-step hidden" data-step="3">
                <div class="px-7 py-5 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900">Review & Submit</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Confirm your details before requesting access</p>
                </div>
                <div class="p-7">
                    <div class="border border-gray-200 rounded-md divide-y divide-gray-100" id="reviewBox"></div>
                    <p class="text-[11px] text-gray-400 mt-4 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Your request will be reviewed by our team before activation
                    </p>
                </div>
            </div>

            {{-- Footer nav --}}
            <div class="px-7 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <button type="button" id="backBtn" class="text-xs font-medium text-gray-500 hover:text-gray-800 invisible">← Back</button>
                <button type="button" id="nextBtn" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded hover:bg-gray-700 transition-colors">Continue →</button>
                <button type="submit" id="submitBtn" class="hidden px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded hover:bg-gray-700 transition-colors">Request Access →</button>
            </div>
        </div>
    </form>
</div>

<script>
(function() {
    const steps = document.querySelectorAll('.wizard-step');
    const stepItems = document.querySelectorAll('.step-item');
    const stepLines = document.querySelectorAll('.step-line');
    const backBtn = document.getElementById('backBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    let current = 1;

    function render() {
        steps.forEach(s => s.classList.toggle('hidden', +s.dataset.step !== current));
        stepItems.forEach((el, i) => {
            const dot = el.querySelector('.step-dot');
            const label = el.querySelector('.step-label');
            if (i === 0 || i + 1 < current) {
                dot.className = 'step-dot w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold text-white transition-all bg-gray-900';
                dot.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
                label.className = 'step-label text-[11px] font-semibold hidden sm:inline text-white';
            } else if (i === current) {
                dot.className = 'step-dot w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold text-white transition-all bg-gray-900';
                dot.textContent = i + 1;
                label.className = 'step-label text-[11px] font-semibold hidden sm:inline text-white';
            } else {
                dot.className = 'step-dot w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold text-white transition-all bg-white/20 border border-white/40';
                dot.textContent = i + 1;
                label.className = 'step-label text-[11px] font-semibold hidden sm:inline text-white/60';
            }
        });
        stepLines.forEach((l, i) => l.className = 'step-line w-8 h-px transition-all ' + (i < current ? 'bg-white/70' : 'bg-white/30'));
        backBtn.classList.toggle('invisible', current === 1);
        nextBtn.classList.toggle('hidden', current === 3);
        submitBtn.classList.toggle('hidden', current !== 3);
        if (current === 3) buildReview();
    }

    function buildReview() {
        const get = id => (document.getElementById(id)?.value || '—');
        const f = n => document.querySelector(`[name="${n}"]`)?.value || '—';
        const plan = document.querySelector('input[name="plan_id"]:checked');
        const cycle = document.querySelector('input[name="billing_cycle"]:checked')?.value || 'monthly';
        const price = plan ? (cycle === 'yearly' ? plan.dataset.yearly : plan.dataset.monthly) : '—';
        const rows = [
            ['Company', get('companyName')],
            ['Email', get('companyEmail')],
            ['Phone', f('phone')],
            ['Location', [f('city'), f('country')].filter(x => x !== '—').join(', ') || '—'],
            ['TIN / VRN', [f('tax_id'), f('vrn')].filter(x => x !== '—').join(' · ') || '—'],
            ['Plan', plan ? plan.dataset.planName : '—'],
            ['Billing', cycle.charAt(0).toUpperCase() + cycle.slice(1)],
            ['Amount', price !== '—' ? 'TZS ' + price : '—'],
        ];
        document.getElementById('reviewBox').innerHTML = rows.map(([k, v]) =>
            `<div class="px-5 py-3 flex items-center justify-between"><span class="text-[11px] text-gray-400">${k}</span><span class="text-xs font-medium text-gray-900 ${k === 'Amount' ? 'font-mono font-bold' : ''}">${v}</span></div>`
        ).join('');
    }

    nextBtn.addEventListener('click', () => {
        if (current === 1) {
            const name = document.getElementById('companyName');
            const email = document.getElementById('companyEmail');
            let ok = true;
            [name, email].forEach(el => { el.classList.remove('border-red-400', 'ring-2', 'ring-red-100'); if (!el.value.trim() || (el === email && !/^\S+@\S+\.\S+$/.test(el.value))) { el.classList.add('border-red-400', 'ring-2', 'ring-red-100'); ok = false; } });
            if (!ok) return;
        }
        if (current < 3) { current++; render(); }
    });
    backBtn.addEventListener('click', () => { if (current > 1) { current--; render(); } });

    document.querySelectorAll('input[name="billing_cycle"]').forEach(r => {
        r.addEventListener('change', () => {
            const cycle = document.querySelector('input[name="billing_cycle"]:checked').value;
            document.querySelectorAll('input[name="plan_id"]').forEach(p => {
                const card = p.closest('label').querySelector('.plan-price');
                card.textContent = cycle === 'yearly' ? p.dataset.yearly : p.dataset.monthly;
            });
            document.querySelectorAll('.plan-period').forEach(el => el.textContent = cycle === 'yearly' ? '/yr' : '/mo');
        });
    });
    document.querySelectorAll('input[name="plan_id"]').forEach(p => {
        p.addEventListener('change', () => {
            document.querySelectorAll('.plan-check').forEach(c => c.classList.replace('flex', 'hidden'));
            p.closest('label').querySelector('.plan-check').classList.replace('hidden', 'flex');
        });
    });
    document.querySelector('input[name="plan_id"]:checked')?.dispatchEvent(new Event('change'));
    render();
})();
</script>
@endsection
