@extends('layouts.landing')

@section('title', 'Hosting - ASYX Group')

@section('content')
    @include('landing.partials.header')

    {{-- Page hero --}}
    <section class="hero-gradient relative pt-32 pb-20 overflow-hidden">
        <div class="absolute inset-0 opacity-15">
            <img src="https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=1920&q=80" alt="Data center hosting" class="w-full h-full object-cover">
        </div>
        <div class="absolute inset-0 bg-gradient-to-br from-navy via-navy/90 to-purple/20"></div>
        <div class="absolute top-20 right-10 w-72 h-72 bg-purple/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-crimson/10 rounded-full blur-3xl"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block px-4 py-1.5 rounded-full glass text-bronze text-xs font-bold uppercase tracking-wider mb-4">Hosting Solutions</span>
            <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-4">
                Reliable <span class="text-gradient">Hosting</span> Services
            </h1>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto leading-relaxed">
                Enterprise-grade hosting infrastructure with 99.9% uptime guarantee, built for mission-critical applications.
            </p>
        </div>
    </section>

    {{-- Hosting Plans --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16 reveal">
                <span class="inline-block px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">Hosting Plans</span>
                <h2 class="font-heading text-3xl sm:text-4xl font-black text-navy section-title section-title-center">Choose Your Plan</h2>
                <p class="mt-6 text-gray-600 text-lg">Flexible hosting packages designed for every business size.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                {{-- Starter --}}
                <div class="bg-[#F2F2F2] rounded-2xl p-8 reveal stagger-1 border-2 border-transparent hover:border-bronze transition-all">
                    <div class="w-14 h-14 rounded-xl bg-bronze/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-navy mb-2">Starter</h3>
                    <p class="text-gray-500 text-sm mb-6">Perfect for small websites</p>
                    <ul class="space-y-3 text-sm text-gray-600 mb-8">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> 10 GB SSD Storage</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> 5 Email Accounts</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> 99.9% Uptime</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> 24/7 Support</li>
                    </ul>
                    <a href="{{ route('contact') }}" class="btn-secondary w-full justify-center inline-flex items-center gap-2 px-6 py-3 text-sm">Get Started</a>
                </div>

                {{-- Professional --}}
                <div class="bg-navy rounded-2xl p-8 reveal stagger-2 border-2 border-bronze relative scale-105 shadow-2xl">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1.5 rounded-full cta-gradient text-white text-xs font-bold uppercase tracking-wide">Most Popular</div>
                    <div class="w-14 h-14 rounded-xl bg-bronze/20 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-white mb-2">Professional</h3>
                    <p class="text-gray-400 text-sm mb-6">For growing businesses</p>
                    <ul class="space-y-3 text-sm text-gray-300 mb-8">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> 50 GB SSD Storage</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> 25 Email Accounts</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> 99.99% Uptime SLA</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Free SSL Certificate</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Daily Backups</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Priority 24/7 Support</li>
                    </ul>
                    <a href="{{ route('contact') }}" class="btn-primary w-full justify-center inline-flex items-center gap-2 px-6 py-3 text-sm">Get Started</a>
                </div>

                {{-- Enterprise --}}
                <div class="bg-[#F2F2F2] rounded-2xl p-8 reveal stagger-3 border-2 border-transparent hover:border-purple transition-all">
                    <div class="w-14 h-14 rounded-xl bg-purple/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-navy mb-2">Enterprise</h3>
                    <p class="text-gray-500 text-sm mb-6">For mission-critical systems</p>
                    <ul class="space-y-3 text-sm text-gray-600 mb-8">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Unlimited SSD Storage</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Unlimited Email Accounts</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> 99.99% Uptime SLA</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Dedicated Resources</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Advanced DDoS Protection</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Dedicated Support Team</li>
                    </ul>
                    <a href="{{ route('contact') }}" class="btn-secondary w-full justify-center inline-flex items-center gap-2 px-6 py-3 text-sm">Contact Sales</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="py-20 bg-[#F2F2F2]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal">
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-navy mb-4">Why Choose Our Hosting?</h2>
                <p class="text-gray-600 text-lg">Enterprise infrastructure trusted by Tanzania's critical institutions.</p>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl p-6 text-center reveal stagger-1">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-bronze/10 flex items-center justify-center mb-4"><svg class="w-6 h-6 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
                    <h3 class="font-heading text-sm font-bold text-navy mb-2">99.9% Uptime</h3>
                    <p class="text-xs text-gray-600">Guaranteed availability</p>
                </div>
                <div class="bg-white rounded-2xl p-6 text-center reveal stagger-2">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-crimson/10 flex items-center justify-center mb-4"><svg class="w-6 h-6 text-crimson" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
                    <h3 class="font-heading text-sm font-bold text-navy mb-2">SSL Security</h3>
                    <p class="text-xs text-gray-600">Free encryption included</p>
                </div>
                <div class="bg-white rounded-2xl p-6 text-center reveal stagger-3">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-purple/10 flex items-center justify-center mb-4"><svg class="w-6 h-6 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></div>
                    <h3 class="font-heading text-sm font-bold text-navy mb-2">Daily Backups</h3>
                    <p class="text-xs text-gray-600">Automatic data protection</p>
                </div>
                <div class="bg-white rounded-2xl p-6 text-center reveal stagger-4">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-navy/10 flex items-center justify-center mb-4"><svg class="w-6 h-6 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <h3 class="font-heading text-sm font-bold text-navy mb-2">24/7 Support</h3>
                    <p class="text-xs text-gray-600">Always here to help</p>
                </div>
            </div>
        </div>
    </section>

    @include('landing.partials.cta')
    @include('landing.partials.footer')
@endsection
