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
                    <div class="aspect-[4/3] relative">
                        <img src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=800&q=80" alt="ASYX Group professionals at work" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-navy/80 via-navy/20 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-8">
                            <div class="w-20 h-20 rounded-full cta-gradient flex items-center justify-center mb-4 shadow-xl">
                                <span class="font-heading font-black text-2xl text-white">2009</span>
                            </div>
                            <p class="font-heading text-xl font-bold text-white mb-1">Since 2009</p>
                            <p class="text-sm text-gray-200">Powering Tanzania's mission-critical systems for 16+ years</p>
                        </div>
                    </div>
                </div>
                {{-- Floating badge --}}
                <div class="absolute -bottom-4 -left-4 bg-white rounded-xl px-6 py-3 shadow-xl">
                    <p class="font-heading text-sm font-bold text-navy">Mission-Critical Ready</p>
                    <p class="text-xs text-gray-500">24/7 Support Available</p>
                </div>
                {{-- Floating stat card --}}
                <div class="absolute -top-4 -right-4 cta-gradient rounded-xl px-5 py-3 shadow-xl">
                    <p class="font-heading text-2xl font-black text-white">50+</p>
                    <p class="text-xs text-white/80">Enterprise Clients</p>
                </div>
            </div>
        </div>
    </div>
</section>
