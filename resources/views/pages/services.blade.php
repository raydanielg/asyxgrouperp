@extends('layouts.landing')

@section('title', 'Services - ASYX Group')

@section('content')
    @include('landing.partials.header')

    {{-- Page hero --}}
    <section class="hero-gradient relative pt-32 pb-20 overflow-hidden">
        <div class="absolute top-20 right-10 w-72 h-72 bg-purple/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-crimson/10 rounded-full blur-3xl"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block px-4 py-1.5 rounded-full glass text-bronze text-xs font-bold uppercase tracking-wider mb-4">03 Core Service Pillars</span>
            <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-4">
                Our <span class="text-gradient">Service Pillars</span>
            </h1>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto leading-relaxed">
                Nine specialised service pillars delivering end-to-end technology solutions for government, parastatals and regulated enterprises.
            </p>
        </div>
    </section>

    {{-- Services grid --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- 1. Smart Technology --}}
                <div class="card-hover bg-[#F2F2F2] rounded-2xl p-7 border border-gray-100">
                    <div class="w-14 h-14 rounded-xl bg-bronze/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy mb-2">Smart Technology</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">IoT, AI-driven analytics, and intelligent automation systems that optimise operations across critical infrastructure.</p>
                    <ul class="text-xs text-gray-500 space-y-1.5">
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>IoT sensor networks</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>AI &amp; machine learning</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Process automation</li>
                    </ul>
                </div>

                {{-- 2. Telematics --}}
                <div class="card-hover bg-[#F2F2F2] rounded-2xl p-7 border border-gray-100">
                    <div class="w-14 h-14 rounded-xl bg-purple/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy mb-2">Telematics</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">Real-time vehicle tracking, fleet management, and geospatial intelligence for transport and aviation clients.</p>
                    <ul class="text-xs text-gray-500 space-y-1.5">
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>GPS fleet tracking</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Geospatial analytics</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Fleet optimisation</li>
                    </ul>
                </div>

                {{-- 3. Cybersecurity --}}
                <div class="card-hover bg-[#F2F2F2] rounded-2xl p-7 border border-gray-100">
                    <div class="w-14 h-14 rounded-xl bg-crimson/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-crimson" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy mb-2">Cyber Security</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">Enterprise-grade protection for networks, endpoints and critical data - safeguarding mission-critical systems.</p>
                    <ul class="text-xs text-gray-500 space-y-1.5">
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Network security</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Endpoint protection</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Threat intelligence</li>
                    </ul>
                </div>

                {{-- 4. Software Development --}}
                <div class="card-hover bg-[#F2F2F2] rounded-2xl p-7 border border-gray-100">
                    <div class="w-14 h-14 rounded-xl bg-navy/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy mb-2">Software Development</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">Custom enterprise applications, ERP systems, and digital transformation platforms built for scale and reliability.</p>
                    <ul class="text-xs text-gray-500 space-y-1.5">
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Custom ERP systems</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Web &amp; mobile apps</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>API integrations</li>
                    </ul>
                </div>

                {{-- 5. ICT Infrastructure --}}
                <div class="card-hover bg-[#F2F2F2] rounded-2xl p-7 border border-gray-100">
                    <div class="w-14 h-14 rounded-xl bg-bronze/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 6H3a2 2 0 00-2 2v6a2 2 0 002 2h2m0-10V4a2 2 0 012-2h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2H7a2 2 0 01-2-2v-2m10-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2m0-10V4a2 2 0 012-2h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy mb-2">ICT Infrastructure</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">Server rooms, fibre/network cabling, data centres, and connectivity solutions engineered for mission-critical uptime.</p>
                    <ul class="text-xs text-gray-500 space-y-1.5">
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Data centre design</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Network cabling</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Connectivity solutions</li>
                    </ul>
                </div>

                {{-- 6. Hardware Supply --}}
                <div class="card-hover bg-[#F2F2F2] rounded-2xl p-7 border border-gray-100">
                    <div class="w-14 h-14 rounded-xl bg-purple/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy mb-2">Hardware Supply</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">Servers, networking equipment, workstations, and specialised hardware procurement with enterprise-grade warranties.</p>
                    <ul class="text-xs text-gray-500 space-y-1.5">
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Server procurement</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Networking equipment</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Workstation supply</li>
                    </ul>
                </div>

                {{-- 7. Managed Services --}}
                <div class="card-hover bg-[#F2F2F2] rounded-2xl p-7 border border-gray-100">
                    <div class="w-14 h-14 rounded-xl bg-crimson/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-crimson" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy mb-2">Managed Services</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">24/7 monitoring, maintenance, and support - ensuring your systems run at peak performance without interruption.</p>
                    <ul class="text-xs text-gray-500 space-y-1.5">
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>24/7 monitoring</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Preventive maintenance</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>SLA-backed support</li>
                    </ul>
                </div>

                {{-- 8. Outsourcing --}}
                <div class="card-hover bg-[#F2F2F2] rounded-2xl p-7 border border-gray-100">
                    <div class="w-14 h-14 rounded-xl bg-navy/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy mb-2">Outsourcing</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">Skilled IT personnel deployment, technical staffing, and outsourced operations management for any scale.</p>
                    <ul class="text-xs text-gray-500 space-y-1.5">
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>IT staff deployment</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Technical staffing</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Operations management</li>
                    </ul>
                </div>

                {{-- 9. Training --}}
                <div class="card-hover bg-[#F2F2F2] rounded-2xl p-7 border border-gray-100">
                    <div class="w-14 h-14 rounded-xl bg-bronze/10 flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy mb-2">Training</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">Technology capacity building, certification programs, and skills transfer for government and enterprise teams.</p>
                    <ul class="text-xs text-gray-500 space-y-1.5">
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Capacity building</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Certification programs</li>
                        <li class="flex items-start gap-2"><svg class="w-3.5 h-3.5 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Skills transfer</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    @include('landing.partials.cta')
    @include('landing.partials.footer')
@endsection
