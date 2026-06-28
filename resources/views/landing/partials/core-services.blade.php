<section id="core-services" class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center max-w-2xl mx-auto mb-16 reveal">
            <span class="inline-block px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">Core Services</span>
            <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-navy leading-tight section-title section-title-center">
                Active Service Pillars
            </h2>
            <p class="mt-6 text-gray-600 text-lg leading-relaxed">
                Explore ASYX's core service pillars, from smart technologies and cybersecurity to ICT infrastructure and managed services.
            </p>
        </div>

        {{-- 4 Core Service Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-12">
            {{-- Smart Technologies --}}
            <div class="card-hover bg-[#F2F2F2] rounded-2xl overflow-hidden border border-gray-100 shadow-sm reveal stagger-1">
                <div class="aspect-[16/10] overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?w=400&q=80" alt="Smart Technologies" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-3 right-3 w-10 h-10 rounded-lg bg-bronze flex items-center justify-center shadow-lg">
                        <span class="text-white text-lg">⚡</span>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <h3 class="font-heading text-base sm:text-lg font-bold text-navy mb-2">Smart Technologies</h3>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">IoT &amp; Smart Systems for intelligent automation.</p>
                </div>
            </div>

            {{-- Cyber Security --}}
            <div class="card-hover bg-[#F2F2F2] rounded-2xl overflow-hidden border border-gray-100 shadow-sm reveal stagger-2">
                <div class="aspect-[16/10] overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=400&q=80" alt="Cyber Security" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-3 right-3 w-10 h-10 rounded-lg bg-crimson flex items-center justify-center shadow-lg">
                        <span class="text-white text-lg">🛡️</span>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <h3 class="font-heading text-base sm:text-lg font-bold text-navy mb-2">Cyber Security</h3>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Enterprise Protection for critical systems.</p>
                </div>
            </div>

            {{-- ICT Infrastructure --}}
            <div class="card-hover bg-[#F2F2F2] rounded-2xl overflow-hidden border border-gray-100 shadow-sm reveal stagger-3">
                <div class="aspect-[16/10] overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=400&q=80" alt="ICT Infrastructure" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-3 right-3 w-10 h-10 rounded-lg bg-purple flex items-center justify-center shadow-lg">
                        <span class="text-white text-lg">🔗</span>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <h3 class="font-heading text-base sm:text-lg font-bold text-navy mb-2">ICT Infrastructure</h3>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Network &amp; Systems deployment.</p>
                </div>
            </div>

            {{-- Software Solutions --}}
            <div class="card-hover bg-[#F2F2F2] rounded-2xl overflow-hidden border border-gray-100 shadow-sm reveal stagger-4">
                <div class="aspect-[16/10] overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=400&q=80" alt="Software Solutions" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-3 right-3 w-10 h-10 rounded-lg bg-navy flex items-center justify-center shadow-lg">
                        <span class="text-white text-lg">💻</span>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <h3 class="font-heading text-base sm:text-lg font-bold text-navy mb-2">Software Solutions</h3>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Custom Development for enterprise.</p>
                </div>
            </div>
        </div>

        {{-- View All Button --}}
        <div class="text-center reveal">
            <a href="{{ route('services') }}" class="btn-primary inline-flex items-center justify-center gap-2 px-8 py-3.5 text-base">
                View All Services
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    </div>
</section>
