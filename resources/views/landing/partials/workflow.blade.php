<section class="py-20 lg:py-28 bg-[#F2F2F2]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            {{-- Left: Content --}}
            <div>
                <span class="inline-block px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">01 About ASYX</span>
                <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-navy leading-tight section-title mb-6">
                    Tanzania's Trusted Technology Partner
                </h2>
                <p class="text-gray-600 text-lg leading-relaxed mb-6">
                    Since 2009, ASYX Group has been the trusted technology partner behind Tanzania's most critical systems - from power utilities to public transport to financial regulation.
                </p>
                <p class="text-gray-600 leading-relaxed mb-8">
                    We serve government bodies, parastatals and regulated enterprises with smart technology, secure infrastructure, and sustainable growth solutions. Our 16+ years of experience means we understand the unique challenges of mission-critical operations in the Tanzanian context.
                </p>

                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div class="text-center">
                        <p class="font-heading text-3xl font-black text-navy">16+</p>
                        <p class="text-xs text-gray-500 mt-1">Years of Service</p>
                    </div>
                    <div class="text-center">
                        <p class="font-heading text-3xl font-black text-bronze">50+</p>
                        <p class="text-xs text-gray-500 mt-1">Enterprise Clients</p>
                    </div>
                    <div class="text-center">
                        <p class="font-heading text-3xl font-black text-purple">9</p>
                        <p class="text-xs text-gray-500 mt-1">Service Pillars</p>
                    </div>
                </div>

                <a href="{{ route('about') }}" class="btn-secondary inline-flex items-center justify-center gap-2 px-7 py-3 text-base">
                    Learn More About Us
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>

            {{-- Right: Visual --}}
            <div class="relative">
                <div class="rounded-2xl overflow-hidden shadow-2xl">
                    <div class="aspect-[4/3] hero-gradient flex items-center justify-center p-12 relative">
                        <div class="absolute inset-0 opacity-[0.04]" style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cpath d=%22M0 0h60v60H0z%22 fill=%22none%22 stroke=%22white%22 stroke-width=%221%22/%3E%3C/svg%3E');"></div>
                        <div class="relative text-center">
                            <div class="w-24 h-24 mx-auto rounded-full cta-gradient flex items-center justify-center mb-6 shadow-xl">
                                <span class="font-heading font-black text-3xl text-white">2009</span>
                            </div>
                            <p class="font-heading text-xl font-bold text-white mb-2">Since 2009</p>
                            <p class="text-sm text-gray-300 max-w-xs">Powersing Tanzania's mission-critical systems for 16+ years</p>
                        </div>
                    </div>
                </div>
                {{-- Floating badge --}}
                <div class="absolute -bottom-4 -left-4 bg-white rounded-xl px-6 py-3 shadow-xl">
                    <p class="font-heading text-sm font-bold text-navy">Mission-Critical Ready</p>
                    <p class="text-xs text-gray-500">24/7 Support Available</p>
                </div>
            </div>
        </div>
    </div>
</section>
