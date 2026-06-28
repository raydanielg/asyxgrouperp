<section id="home" class="hero-gradient relative min-h-screen flex items-center pt-20 overflow-hidden">
    {{-- Background image --}}
    <div class="absolute inset-0 opacity-20">
        <img src="https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=1920&q=80" alt="Server room" class="w-full h-full object-cover">
    </div>
    <div class="absolute inset-0 bg-gradient-to-br from-navy via-navy/90 to-purple/30"></div>

    {{-- Decorative elements --}}
    <div class="absolute top-20 right-10 w-72 h-72 bg-purple/20 rounded-full blur-3xl animate-float"></div>
    <div class="absolute bottom-10 left-10 w-96 h-96 bg-crimson/10 rounded-full blur-3xl"></div>

    {{-- Grid overlay --}}
    <div class="absolute inset-0 opacity-[0.04]" style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cpath d=%22M0 0h60v60H0z%22 fill=%22none%22 stroke=%22white%22 stroke-width=%221%22/%3E%3C/svg%3E');"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32 w-full">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            {{-- Left: Content --}}
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full glass mb-6 animate-fade-up">
                    <span class="w-2 h-2 rounded-full bg-bronze animate-pulse"></span>
                    <span class="text-xs font-semibold text-bronze tracking-wide uppercase">Since 2009 - 16+ Years of Excellence</span>
                </div>

                <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight animate-fade-up delay-100">
                    Smart Technology.<br>
                    Secure Infrastructure.<br>
                    <span class="text-gradient">Sustainable Growth.</span>
                </h1>

                <p class="mt-6 text-lg text-gray-300 max-w-xl mx-auto lg:mx-0 animate-fade-up delay-200 leading-relaxed">
                    16+ years powering Tanzania's mission-critical systems. From power utilities to public transport to financial regulation - ASYX Group is the trusted technology partner behind the nation's most critical infrastructure.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start animate-fade-up delay-300">
                    <a href="{{ route('services') }}" class="btn-primary inline-flex items-center justify-center gap-2 px-7 py-3.5 text-base">
                        Explore Our Services
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 border-2 border-white/30 text-white font-bold rounded-lg hover:bg-white/10 transition-all text-base">
                        Talk to an Expert
                    </a>
                </div>

                {{-- Trust indicators --}}
                <div class="mt-10 flex flex-wrap items-center gap-6 justify-center lg:justify-start animate-fade-up delay-400">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="text-sm text-gray-300">Government &amp; Parastatal Trusted</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="text-sm text-gray-300">ISO-Grade Standards</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="text-sm text-gray-300">24/7 Mission-Critical Support</span>
                    </div>
                </div>
            </div>

            {{-- Right: Visual --}}
            <div class="relative animate-fade-up delay-300 hidden lg:block">
                {{-- Main image --}}
                <div class="rounded-2xl overflow-hidden shadow-2xl">
                    <div class="aspect-[4/5] relative">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=600&q=80" alt="ASYX Group technology professional" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-navy/90 via-navy/20 to-transparent"></div>

                        {{-- Name overlay --}}
                        <div class="absolute bottom-0 left-0 right-0 p-6">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full cta-gradient flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <div>
                                    <p class="font-heading font-bold text-white text-lg">Expert Engineering Team</p>
                                    <p class="text-sm text-gray-300">Mission-Critical Systems Division</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Floating stat card: Uptime --}}
                <div class="absolute -top-5 -left-5 glass rounded-xl px-5 py-3 shadow-xl animate-float">
                    <p class="text-[10px] text-gray-300 uppercase tracking-wide">Systems Online</p>
                    <p class="font-heading text-2xl font-black text-bronze">99.9%</p>
                    <p class="text-[10px] text-bronze/70">Uptime Guaranteed</p>
                </div>

                {{-- Floating stat card: Years --}}
                <div class="absolute top-1/2 -right-5 glass rounded-xl px-5 py-3 shadow-xl animate-float" style="animation-delay: 1s;">
                    <p class="text-[10px] text-gray-300 uppercase tracking-wide">Experience</p>
                    <p class="font-heading text-2xl font-black text-white">16+</p>
                    <p class="text-[10px] text-gray-400">Years Since 2009</p>
                </div>

                {{-- Floating stat card: Clients --}}
                <div class="absolute -bottom-5 left-1/2 -translate-x-1/2 glass rounded-xl px-5 py-3 shadow-xl animate-float" style="animation-delay: 2s;">
                    <p class="text-[10px] text-gray-300 uppercase tracking-wide">Enterprise Clients</p>
                    <p class="font-heading text-2xl font-black text-crimson">50+</p>
                    <p class="text-[10px] text-gray-400">Active Partnerships</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Wave separator --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto" preserveAspectRatio="none">
            <path d="M0 120L60 110C120 100 240 80 360 75C480 70 600 80 720 85C840 90 960 90 1080 85C1200 80 1320 70 1380 65L1440 60V120H0Z" fill="white"/>
        </svg>
    </div>
</section>