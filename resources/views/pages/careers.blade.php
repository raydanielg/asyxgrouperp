@extends('layouts.landing')

@section('title', 'Careers - ASYX Group')

@section('content')
    @include('landing.partials.header')

    {{-- Page hero --}}
    <section class="hero-gradient relative pt-32 pb-20 overflow-hidden">
        <div class="absolute inset-0 opacity-15">
            <img src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=1920&q=80" alt="Team collaboration" class="w-full h-full object-cover">
        </div>
        <div class="absolute inset-0 bg-gradient-to-br from-navy via-navy/90 to-purple/20"></div>
        <div class="absolute top-20 right-10 w-72 h-72 bg-bronze/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-crimson/10 rounded-full blur-3xl"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block px-4 py-1.5 rounded-full glass text-bronze text-xs font-bold uppercase tracking-wider mb-4">Join Our Team</span>
            <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-4">
                Build Your <span class="text-gradient">Career</span> at ASYX
            </h1>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto leading-relaxed">
                Be part of a team that's shaping Africa's technological future. We invest in our people and empower them to innovate.
            </p>
        </div>
    </section>

    {{-- Why Work With Us --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16 reveal">
                <span class="inline-block px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">Why ASYX</span>
                <h2 class="font-heading text-3xl sm:text-4xl font-black text-navy section-title section-title-center">Why Work With Us?</h2>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-[#F2F2F2] rounded-2xl p-6 text-center reveal stagger-1">
                    <div class="w-14 h-14 mx-auto rounded-xl bg-bronze/10 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-heading text-base font-bold text-navy mb-2">Innovation</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Work on cutting-edge projects with the latest technologies.</p>
                </div>
                <div class="bg-[#F2F2F2] rounded-2xl p-6 text-center reveal stagger-2">
                    <div class="w-14 h-14 mx-auto rounded-xl bg-purple/10 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                    <h3 class="font-heading text-base font-bold text-navy mb-2">Growth</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Continuous learning and professional development opportunities.</p>
                </div>
                <div class="bg-[#F2F2F2] rounded-2xl p-6 text-center reveal stagger-3">
                    <div class="w-14 h-14 mx-auto rounded-xl bg-crimson/10 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-crimson" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="font-heading text-base font-bold text-navy mb-2">Teamwork</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Collaborative culture with talented professionals.</p>
                </div>
                <div class="bg-[#F2F2F2] rounded-2xl p-6 text-center reveal stagger-4">
                    <div class="w-14 h-14 mx-auto rounded-xl bg-navy/10 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-heading text-base font-bold text-navy mb-2">Benefits</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Competitive compensation and comprehensive benefits.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Open Positions --}}
    <section class="py-20 lg:py-28 bg-[#F2F2F2]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal">
                <span class="inline-block px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">Open Positions</span>
                <h2 class="font-heading text-3xl sm:text-4xl font-black text-navy section-title section-title-center">Current Job Openings</h2>
                <p class="mt-6 text-gray-600 text-lg">Find your next opportunity and apply today.</p>
            </div>

            <div class="space-y-4">
                <div class="bg-white rounded-2xl p-6 shadow-sm card-hover reveal stagger-1">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 rounded-full bg-bronze/10 text-bronze text-xs font-bold">Full-Time</span>
                                <span class="text-xs text-gray-500">Dar es Salaam, Tanzania</span>
                            </div>
                            <h3 class="font-heading text-lg font-bold text-navy">Senior Network Engineer</h3>
                            <p class="text-sm text-gray-600 mt-1">Design and deploy enterprise network infrastructure for mission-critical clients.</p>
                        </div>
                        <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm whitespace-nowrap">Apply Now</a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm card-hover reveal stagger-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 rounded-full bg-purple/10 text-purple text-xs font-bold">Full-Time</span>
                                <span class="text-xs text-gray-500">Dar es Salaam, Tanzania</span>
                            </div>
                            <h3 class="font-heading text-lg font-bold text-navy">Cybersecurity Analyst</h3>
                            <p class="text-sm text-gray-600 mt-1">Monitor, detect, and respond to security threats across client environments.</p>
                        </div>
                        <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm whitespace-nowrap">Apply Now</a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm card-hover reveal stagger-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 rounded-full bg-crimson/10 text-crimson text-xs font-bold">Contract</span>
                                <span class="text-xs text-gray-500">Remote / Dar es Salaam</span>
                            </div>
                            <h3 class="font-heading text-lg font-bold text-navy">Software Developer</h3>
                            <p class="text-sm text-gray-600 mt-1">Build custom web and mobile applications for enterprise clients.</p>
                        </div>
                        <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm whitespace-nowrap">Apply Now</a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm card-hover reveal stagger-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 rounded-full bg-bronze/10 text-bronze text-xs font-bold">Full-Time</span>
                                <span class="text-xs text-gray-500">Dar es Salaam, Tanzania</span>
                            </div>
                            <h3 class="font-heading text-lg font-bold text-navy">ICT Support Specialist</h3>
                            <p class="text-sm text-gray-600 mt-1">Provide technical support and managed services to enterprise clients.</p>
                        </div>
                        <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm whitespace-nowrap">Apply Now</a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm card-hover reveal stagger-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 rounded-full bg-purple/10 text-purple text-xs font-bold">Full-Time</span>
                                <span class="text-xs text-gray-500">Dar es Salaam, Tanzania</span>
                            </div>
                            <h3 class="font-heading text-lg font-bold text-navy">Project Manager</h3>
                            <p class="text-sm text-gray-600 mt-1">Lead technology deployment projects from planning to delivery.</p>
                        </div>
                        <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm whitespace-nowrap">Apply Now</a>
                    </div>
                </div>
            </div>

            <div class="mt-12 text-center reveal">
                <div class="bg-navy rounded-2xl p-8">
                    <h3 class="font-heading text-xl font-bold text-white mb-3">Don't see the right fit?</h3>
                    <p class="text-gray-300 text-sm mb-6 max-w-xl mx-auto">Send us your CV and we'll keep you in mind for future opportunities that match your skills.</p>
                    <a href="{{ route('contact') }}" class="btn-primary inline-flex items-center gap-2 px-8 py-3.5 text-base">
                        Submit Your CV
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    @include('landing.partials.cta')
    @include('landing.partials.footer')
@endsection
