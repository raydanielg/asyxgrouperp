@extends('layouts.landing')

@section('title', 'Sectors & Clients - ASYX Group')

@section('content')
    @include('landing.partials.header')

    {{-- Page hero --}}
    <section class="hero-gradient relative pt-32 pb-20 overflow-hidden">
        <div class="absolute top-20 right-10 w-72 h-72 bg-purple/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-crimson/10 rounded-full blur-3xl"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block px-4 py-1.5 rounded-full glass text-bronze text-xs font-bold uppercase tracking-wider mb-4">Sectors &amp; Clients</span>
            <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-4">
                Sectors We Serve &amp;<br><span class="text-gradient">Clients We Power</span>
            </h1>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto leading-relaxed">
                From energy and utilities to transport, finance, and government - ASYX Group is the trusted partner behind Tanzania's most critical institutions.
            </p>
        </div>
    </section>

    {{-- Sector tabs --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">Industries</span>
                <h2 class="font-heading text-3xl sm:text-4xl font-black text-navy section-title section-title-center">Sectors We Serve</h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Energy & Utilities --}}
                <div class="card-hover bg-[#F2F2F2] rounded-2xl p-7">
                    <div class="w-14 h-14 rounded-xl bg-bronze/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy mb-2">Energy &amp; Utilities</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">Power grid monitoring, smart metering, and infrastructure management for utilities like TANESCO and DAWASA.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="text-xs font-semibold text-bronze bg-bronze/10 px-3 py-1 rounded-full">TANESCO</span>
                        <span class="text-xs font-semibold text-bronze bg-bronze/10 px-3 py-1 rounded-full">DAWASA</span>
                    </div>
                </div>

                {{-- Transport & Aviation --}}
                <div class="card-hover bg-[#F2F2F2] rounded-2xl p-7">
                    <div class="w-14 h-14 rounded-xl bg-purple/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-purple" fill="currentColor" viewBox="0 0 24 24"><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy mb-2">Transport &amp; Aviation</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">Fleet telematics, tracking systems, and aviation technology solutions for transport and airline operators.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="text-xs font-semibold text-purple bg-purple/10 px-3 py-1 rounded-full">Precision Air</span>
                    </div>
                </div>

                {{-- Finance & Regulation --}}
                <div class="card-hover bg-[#F2F2F2] rounded-2xl p-7">
                    <div class="w-14 h-14 rounded-xl bg-crimson/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-crimson" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy mb-2">Finance &amp; Regulation</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">Secure financial systems, regulatory compliance platforms, and tax management for financial institutions.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="text-xs font-semibold text-crimson bg-crimson/10 px-3 py-1 rounded-full">BOT</span>
                        <span class="text-xs font-semibold text-crimson bg-crimson/10 px-3 py-1 rounded-full">TRA</span>
                    </div>
                </div>

                {{-- Government --}}
                <div class="card-hover bg-[#F2F2F2] rounded-2xl p-7">
                    <div class="w-14 h-14 rounded-xl bg-navy/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy mb-2">Government</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">E-government platforms, digital public services, and citizen-facing technology for ministries and agencies.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="text-xs font-semibold text-navy bg-navy/10 px-3 py-1 rounded-full">Multiple Ministries</span>
                    </div>
                </div>

                {{-- Education & Research --}}
                <div class="card-hover bg-[#F2F2F2] rounded-2xl p-7">
                    <div class="w-14 h-14 rounded-xl bg-bronze/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy mb-2">Education &amp; Research</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">Campus networks, research computing infrastructure, and e-learning platforms for educational institutions.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="text-xs font-semibold text-bronze bg-bronze/10 px-3 py-1 rounded-full">Universities</span>
                        <span class="text-xs font-semibold text-bronze bg-bronze/10 px-3 py-1 rounded-full">Research Institutes</span>
                    </div>
                </div>

                {{-- Social Security --}}
                <div class="card-hover bg-[#F2F2F2] rounded-2xl p-7">
                    <div class="w-14 h-14 rounded-xl bg-purple/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy mb-2">Social Security</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">Member management systems, contribution tracking, and benefits administration for social security funds.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="text-xs font-semibold text-purple bg-purple/10 px-3 py-1 rounded-full">NSSF</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Client logo wall --}}
    <section class="py-20 bg-[#F2F2F2]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-block px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">Our Clients</span>
                <h2 class="font-heading text-3xl font-black text-navy section-title section-title-center">Client Roster</h2>
                <p class="mt-6 text-gray-600 max-w-xl mx-auto">Trusted by Tanzania's most critical institutions across every sector.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-8 items-center">
                <div class="flex items-center justify-center logo-greyscale">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto rounded-full bg-navy/10 flex items-center justify-center mb-2">
                            <svg class="w-8 h-8 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <p class="font-heading text-xs font-bold text-navy">TANESCO</p>
                    </div>
                </div>
                <div class="flex items-center justify-center logo-greyscale">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto rounded-full bg-navy/10 flex items-center justify-center mb-2">
                            <svg class="w-8 h-8 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="font-heading text-xs font-bold text-navy">TRA</p>
                    </div>
                </div>
                <div class="flex items-center justify-center logo-greyscale">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto rounded-full bg-navy/10 flex items-center justify-center mb-2">
                            <svg class="w-8 h-8 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="font-heading text-xs font-bold text-navy">NSSF</p>
                    </div>
                </div>
                <div class="flex items-center justify-center logo-greyscale">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto rounded-full bg-navy/10 flex items-center justify-center mb-2">
                            <svg class="w-8 h-8 text-navy" fill="currentColor" viewBox="0 0 24 24"><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg>
                        </div>
                        <p class="font-heading text-xs font-bold text-navy">Precision Air</p>
                    </div>
                </div>
                <div class="flex items-center justify-center logo-greyscale">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto rounded-full bg-navy/10 flex items-center justify-center mb-2">
                            <svg class="w-8 h-8 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <p class="font-heading text-xs font-bold text-navy">BOT</p>
                    </div>
                </div>
                <div class="flex items-center justify-center logo-greyscale">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto rounded-full bg-navy/10 flex items-center justify-center mb-2">
                            <svg class="w-8 h-8 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <p class="font-heading text-xs font-bold text-navy">DAWASA</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('landing.partials.cta')
    @include('landing.partials.footer')
@endsection
