<section id="home" class="hero-gradient relative min-h-screen flex items-center pt-20 overflow-hidden">
    {{-- Decorative elements --}}
    <div class="absolute top-20 right-10 w-72 h-72 bg-purple/20 rounded-full blur-3xl animate-float"></div>
    <div class="absolute bottom-10 left-10 w-96 h-96 bg-crimson/10 rounded-full blur-3xl"></div>

    {{-- Grid overlay --}}
    <div class="absolute inset-0 opacity-[0.04]" style="background-image: url(''data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cpath d=%22M0 0h60v60H0z%22 fill=%22none%22 stroke=%22white%22 stroke-width=%221%22/%3E%3C/svg%3E'');"></div>

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
                    16+ years powering Tanzania''s mission-critical systems. From power utilities to public transport to financial regulation - ASYX Group is the trusted technology partner behind the nation''s most critical infrastructure.
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
                <div class="glass rounded-2xl p-6 shadow-2xl">
                    {{-- Mock dashboard --}}
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-crimson"></div>
                            <div class="w-3 h-3 rounded-full bg-bronze"></div>
                            <div class="w-3 h-3 rounded-full bg-purple"></div>
                        </div>
                        <div class="text-xs text-gray-400 font-mono">asyxgroup.co.tz/dashboard</div>
                    </div>

                    {{-- Stats cards --}}
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wide">Systems Online</p>
                            <p class="text-lg font-heading font-black text-bronze">99.9%</p>
                            <p class="text-[10px] text-bronze/70">Uptime</p>
                        </div>
                        <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wide">Clients</p>
                            <p class="text-lg font-heading font-black text-white">50+</p>
                            <p class="text-[10px] text-gray-400">Active</p>
                        </div>
                        <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wide">Years</p>
                            <p class="text-lg font-heading font-black text-white">16+</p>
                            <p class="text-[10px] text-gray-400">Since 2009</p>
                        </div>
                    </div>

                    {{-- Chart mock --}}
                    <div class="bg-white/5 rounded-xl p-4 border border-white/10 mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-gray-300">Network Performance</p>
                            <span class="text-[10px] text-bronze bg-bronze/20 px-2 py-0.5 rounded-full">Live</span>
                        </div>
                        <div class="flex items-end gap-2 h-24">
                            <div class="flex-1 bg-crimson/40 rounded-t" style="height: 40%"></div>
                            <div class="flex-1 bg-crimson/50 rounded-t" style="height: 55%"></div>
                            <div class="flex-1 bg-purple/50 rounded-t" style="height: 35%"></div>
                            <div class="flex-1 bg-purple/60 rounded-t" style="height: 70%"></div>
                            <div class="flex-1 bg-bronze/60 rounded-t" style="height: 60%"></div>
                            <div class="flex-1 bg-bronze/70 rounded-t" style="height: 85%"></div>
                            <div class="flex-1 bg-bronze rounded-t" style="height: 95%"></div>
                        </div>
                    </div>

                    {{-- Activity feed --}}
                    <div class="space-y-2">
                        <div class="flex items-center gap-3 bg-white/5 rounded-lg p-2.5 border border-white/10">
                            <div class="w-8 h-8 rounded-lg bg-bronze/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="flex-1"><p class="text-xs text-white font-medium">TANESCO grid monitoring active</p><p class="text-[10px] text-gray-500">Real-time</p></div>
                        </div>
                        <div class="flex items-center gap-3 bg-white/5 rounded-lg p-2.5 border border-white/10">
                            <div class="w-8 h-8 rounded-lg bg-purple/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                            </div>
                            <div class="flex-1"><p class="text-xs text-white font-medium">Precision Air telematics synced</p><p class="text-[10px] text-gray-500">2 min ago</p></div>
                        </div>
                    </div>
                </div>

                {{-- Floating badge --}}
                <div class="absolute -top-4 -right-4 glass rounded-xl px-4 py-2 shadow-xl animate-float">
                    <p class="text-xs text-white font-bold">Mission-Critical Ready</p>
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