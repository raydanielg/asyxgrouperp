@extends('layouts.landing')

@section('title', 'About Us - ASYX Group')

@section('content')
    @include('landing.partials.header')

    {{-- Page hero --}}
    <section class="hero-gradient relative pt-32 pb-20 overflow-hidden">
        <div class="absolute top-20 right-10 w-72 h-72 bg-purple/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-crimson/10 rounded-full blur-3xl"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block px-4 py-1.5 rounded-full glass text-bronze text-xs font-bold uppercase tracking-wider mb-4">About ASYX Group</span>
            <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-4">
                16+ Years of <span class="text-gradient">Trusted Service</span>
            </h1>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto leading-relaxed">
                Since 2009, ASYX Group has been the trusted technology partner behind Tanzania's most critical systems.
            </p>
        </div>
    </section>

    {{-- Company story timeline --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">Our Journey</span>
                <h2 class="font-heading text-3xl sm:text-4xl font-black text-navy section-title section-title-center">Company Story</h2>
            </div>

            <div class="space-y-8">
                {{-- 2009 --}}
                <div class="flex gap-6 items-start">
                    <div class="w-16 h-16 rounded-full cta-gradient flex items-center justify-center flex-shrink-0 shadow-lg">
                        <span class="font-heading font-black text-sm text-white">2009</span>
                    </div>
                    <div class="flex-1 bg-[#F2F2F2] rounded-2xl p-6">
                        <h3 class="font-heading text-lg font-bold text-navy mb-2">Foundation</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">ASYX Group is founded with a vision to bring enterprise-grade technology solutions to Tanzania's public sector and regulated industries.</p>
                    </div>
                </div>

                {{-- 2013 --}}
                <div class="flex gap-6 items-start">
                    <div class="w-16 h-16 rounded-full bg-navy flex items-center justify-center flex-shrink-0 shadow-lg">
                        <span class="font-heading font-black text-sm text-white">2013</span>
                    </div>
                    <div class="flex-1 bg-[#F2F2F2] rounded-2xl p-6">
                        <h3 class="font-heading text-lg font-bold text-navy mb-2">Government Partnerships</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">Secured major contracts with TANESCO and TRA, establishing ASYX as a trusted government technology partner.</p>
                    </div>
                </div>

                {{-- 2018 --}}
                <div class="flex gap-6 items-start">
                    <div class="w-16 h-16 rounded-full bg-bronze flex items-center justify-center flex-shrink-0 shadow-lg">
                        <span class="font-heading font-black text-sm text-white">2018</span>
                    </div>
                    <div class="flex-1 bg-[#F2F2F2] rounded-2xl p-6">
                        <h3 class="font-heading text-lg font-bold text-navy mb-2">Expansion &amp; Growth</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">Expanded service pillars to include telematics, cybersecurity, and managed services. Client roster grows to include NSSF, BOT, and Precision Air.</p>
                    </div>
                </div>

                {{-- 2026 --}}
                <div class="flex gap-6 items-start">
                    <div class="w-16 h-16 rounded-full cta-gradient flex items-center justify-center flex-shrink-0 shadow-lg">
                        <span class="font-heading font-black text-sm text-white">2026</span>
                    </div>
                    <div class="flex-1 bg-[#F2F2F2] rounded-2xl p-6">
                        <h3 class="font-heading text-lg font-bold text-navy mb-2">Today &amp; Beyond</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">With 50+ enterprise clients and 9 service pillars, ASYX Group continues to power Tanzania's mission-critical systems with smart technology, secure infrastructure, and sustainable growth.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Vision / Mission / Values --}}
    <section class="py-20 lg:py-28 bg-[#F2F2F2]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">What Drives Us</span>
                <h2 class="font-heading text-3xl sm:text-4xl font-black text-navy section-title section-title-center">Vision, Mission &amp; Values</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                {{-- Vision --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm card-hover border-t-4 border-navy">
                    <div class="w-14 h-14 rounded-xl bg-navy/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-navy mb-3">Our Vision</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">To be East Africa's most trusted technology partner for government and regulated enterprises - enabling smart, secure, and sustainable digital transformation.</p>
                </div>

                {{-- Mission --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm card-hover border-t-4 border-bronze">
                    <div class="w-14 h-14 rounded-xl bg-bronze/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-navy mb-3">Our Mission</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">To deliver mission-critical technology solutions that empower our clients to serve the public with excellence - through innovation, reliability, and unwavering commitment.</p>
                </div>

                {{-- Values --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm card-hover border-t-4 border-purple">
                    <div class="w-14 h-14 rounded-xl bg-purple/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-navy mb-3">Our Values</h3>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li class="flex items-start gap-2"><svg class="w-4 h-4 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Trust &amp; Integrity</li>
                        <li class="flex items-start gap-2"><svg class="w-4 h-4 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Excellence in Delivery</li>
                        <li class="flex items-start gap-2"><svg class="w-4 h-4 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Local Rooted, Global Standards</li>
                        <li class="flex items-start gap-2"><svg class="w-4 h-4 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Innovation &amp; Adaptability</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Governance --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">Governance &amp; Compliance</span>
            <h2 class="font-heading text-3xl sm:text-4xl font-black text-navy section-title section-title-center mb-6">Committed to the Highest Standards</h2>
            <p class="text-gray-600 text-lg leading-relaxed mb-12 max-w-2xl mx-auto">
                ASYX Group operates with full compliance to Tanzanian regulatory requirements and international best practices. We maintain rigorous governance frameworks to ensure our solutions meet the exacting standards of government and regulated enterprise clients.
            </p>
            <div class="grid sm:grid-cols-3 gap-6">
                <div class="bg-[#F2F2F2] rounded-xl p-6">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-navy/10 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h4 class="font-heading font-bold text-navy mb-2">ISO-Grade</h4>
                    <p class="text-sm text-gray-600">Quality management standards</p>
                </div>
                <div class="bg-[#F2F2F2] rounded-xl p-6">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-bronze/10 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h4 class="font-heading font-bold text-navy mb-2">Data Security</h4>
                    <p class="text-sm text-gray-600">Enterprise-grade protection</p>
                </div>
                <div class="bg-[#F2F2F2] rounded-xl p-6">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-purple/10 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h4 class="font-heading font-bold text-navy mb-2">Compliance</h4>
                    <p class="text-sm text-gray-600">Full regulatory adherence</p>
                </div>
            </div>
        </div>
    </section>

    @include('landing.partials.cta')
    @include('landing.partials.footer')
@endsection
