@extends('layouts.landing')

@section('title', 'Why ASYX - ASYX Group')

@section('content')
    @include('landing.partials.header')

    {{-- Page hero --}}
    <section class="hero-gradient relative pt-32 pb-20 overflow-hidden">
        <div class="absolute top-20 right-10 w-72 h-72 bg-purple/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-crimson/10 rounded-full blur-3xl"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block px-4 py-1.5 rounded-full glass text-bronze text-xs font-bold uppercase tracking-wider mb-4">04 Why ASYX</span>
            <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-4">
                Why Choose <span class="text-gradient">ASYX Group</span>
            </h1>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto leading-relaxed">
                Five differentiators that make us Tanzania's most trusted technology partner for mission-critical systems.
            </p>
        </div>
    </section>

    {{-- Differentiators timeline --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="space-y-8">
                {{-- 01 --}}
                <div class="flex gap-6 items-start">
                    <div class="w-16 h-16 rounded-full cta-gradient flex items-center justify-center flex-shrink-0 shadow-lg">
                        <span class="font-heading font-black text-sm text-white">01</span>
                    </div>
                    <div class="flex-1 bg-[#F2F2F2] rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-bronze/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <h3 class="font-heading text-xl font-bold text-navy">16+ Years of Trust</h3>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">Since 2009, we've powered Tanzania's most critical systems with zero compromise on reliability. Our track record speaks for itself - 16+ years of uninterrupted service to the nation's most important institutions.</p>
                    </div>
                </div>

                {{-- 02 --}}
                <div class="flex gap-6 items-start">
                    <div class="w-16 h-16 rounded-full cta-gradient flex items-center justify-center flex-shrink-0 shadow-lg">
                        <span class="font-heading font-black text-sm text-white">02</span>
                    </div>
                    <div class="flex-1 bg-[#F2F2F2] rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-purple/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <h3 class="font-heading text-xl font-bold text-navy">Government-Grade Security</h3>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">Security and compliance standards built for public-sector and regulated enterprise requirements. We understand the sensitivity of government data and maintain the highest protection protocols.</p>
                    </div>
                </div>

                {{-- 03 --}}
                <div class="flex gap-6 items-start">
                    <div class="w-16 h-16 rounded-full cta-gradient flex items-center justify-center flex-shrink-0 shadow-lg">
                        <span class="font-heading font-black text-sm text-white">03</span>
                    </div>
                    <div class="flex-1 bg-[#F2F2F2] rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-crimson/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-crimson" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <h3 class="font-heading text-xl font-bold text-navy">Rapid Response</h3>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">24/7 support with guaranteed response times - because mission-critical means zero downtime. Our support teams are always on standby to ensure your systems never miss a beat.</p>
                    </div>
                </div>

                {{-- 04 --}}
                <div class="flex gap-6 items-start">
                    <div class="w-16 h-16 rounded-full cta-gradient flex items-center justify-center flex-shrink-0 shadow-lg">
                        <span class="font-heading font-black text-sm text-white">04</span>
                    </div>
                    <div class="flex-1 bg-[#F2F2F2] rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-navy/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="font-heading text-xl font-bold text-navy">Local Expertise</h3>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">Deep understanding of Tanzanian regulatory landscape, infrastructure, and institutional workflows. We're locally rooted with global standards - proud of our Tanzanian presence while delivering enterprise-grade international quality.</p>
                    </div>
                </div>

                {{-- 05 --}}
                <div class="flex gap-6 items-start">
                    <div class="w-16 h-16 rounded-full cta-gradient flex items-center justify-center flex-shrink-0 shadow-lg">
                        <span class="font-heading font-black text-sm text-white">05</span>
                    </div>
                    <div class="flex-1 bg-[#F2F2F2] rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-bronze/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <h3 class="font-heading text-xl font-bold text-navy">End-to-End Capability</h3>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">From infrastructure to software to training - one partner, complete solutions, no gaps. We cover the entire technology stack so you never need to juggle multiple vendors for your mission-critical needs.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('landing.partials.cta')
    @include('landing.partials.footer')
@endsection
