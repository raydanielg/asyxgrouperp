<section id="core-services" class="py-20 lg:py-28 bg-white relative overflow-hidden">

    {{-- WhatsApp-style icon pattern background --}}
    <div class="absolute inset-0 opacity-[0.03]" style="background-image: url(&quot;data:image/svg+xml,%3Csvg width='120' height='120' viewBox='0 0 120 120' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='%231B3A5C' stroke-width='1.5'%3E%3Cpath d='M60 20L40 50h40L60 20zM60 50v40M30 90h60M20 70l20 20M100 70l-20 20'/%3E%3Ccircle cx='30' cy='30' r='5'/%3E%3Ccircle cx='90' cy='30' r='5'/%3E%3Crect x='50' y='80' width='20' height='20' rx='3'/%3E%3Cpath d='M15 55l10 5 10-5M85 55l10 5 10-5'/%3E%3C/g%3E%3C/svg%3E&quot;); background-size: 120px 120px;"></div>

    {{-- Decorative glows --}}
    <div class="absolute top-0 right-0 w-96 h-96 bg-bronze/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-navy/5 rounded-full blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        {{-- Header --}}
        <div class="text-center max-w-2xl mx-auto mb-16 reveal">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                Core Services
            </span>
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
            <div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm reveal stagger-1 group">
                <div class="aspect-[16/10] overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&q=80" alt="Smart Technologies" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    {{-- Gradient overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-navy/70 via-navy/10 to-transparent"></div>
                    {{-- Icon badge with gradient --}}
                    <div class="absolute top-3 right-3 w-12 h-12 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-300" style="background: linear-gradient(135deg, #A8703A, #8f5e2e);">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/><path stroke-linecap="round" stroke-linejoin="round" d="M5 19l2-2m12 2l-2-2M9 21l1-2m5 2l-1-2" opacity="0.5"/></svg>
                    </div>
                    {{-- Bottom label on image --}}
                    <div class="absolute bottom-3 left-3 right-3">
                        <span class="text-white text-xs font-bold uppercase tracking-wider drop-shadow-lg">IoT &amp; Automation</span>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <h3 class="font-heading text-base sm:text-lg font-bold text-navy mb-2 group-hover:text-bronze transition-colors">Smart Technologies</h3>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">IoT &amp; Smart Systems for intelligent automation and connected infrastructure.</p>
                </div>
            </div>

            {{-- Cyber Security --}}
            <div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm reveal stagger-2 group">
                <div class="aspect-[16/10] overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=600&q=80" alt="Cyber Security" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-crimson/70 via-crimson/10 to-transparent"></div>
                    <div class="absolute top-3 right-3 w-12 h-12 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-300" style="background: linear-gradient(135deg, #C81E3A, #a81830);">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.062-.18-2.087-.514-3.056z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2 2" opacity="0.5"/></svg>
                    </div>
                    <div class="absolute bottom-3 left-3 right-3">
                        <span class="text-white text-xs font-bold uppercase tracking-wider drop-shadow-lg">Enterprise Protection</span>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <h3 class="font-heading text-base sm:text-lg font-bold text-navy mb-2 group-hover:text-crimson transition-colors">Cyber Security</h3>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Enterprise-grade protection for critical systems and government infrastructure.</p>
                </div>
            </div>

            {{-- ICT Infrastructure --}}
            <div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm reveal stagger-3 group">
                <div class="aspect-[16/10] overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=600&q=80" alt="ICT Infrastructure" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-purple/70 via-purple/10 to-transparent"></div>
                    <div class="absolute top-3 right-3 w-12 h-12 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-300" style="background: linear-gradient(135deg, #5B2A6E, #4a2258);">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 6h14M5 12h14M5 18h14M7 6v12M11 6v12M15 6v12M19 6v12"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 4l2 2M21 4l-2 2M3 20l2-2M21 20l-2-2" opacity="0.5"/></svg>
                    </div>
                    <div class="absolute bottom-3 left-3 right-3">
                        <span class="text-white text-xs font-bold uppercase tracking-wider drop-shadow-lg">Network &amp; Systems</span>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <h3 class="font-heading text-base sm:text-lg font-bold text-navy mb-2 group-hover:text-purple transition-colors">ICT Infrastructure</h3>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Network &amp; systems deployment, server management and data center solutions.</p>
                </div>
            </div>

            {{-- Software Solutions --}}
            <div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm reveal stagger-4 group">
                <div class="aspect-[16/10] overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=600&q=80" alt="Software Solutions" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-navy/70 via-navy/10 to-transparent"></div>
                    <div class="absolute top-3 right-3 w-12 h-12 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-300" style="background: linear-gradient(135deg, #1B3A5C, #163049);">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 6l1 1M16 18l1 1M8 18l1-1M16 6l1 1" opacity="0.5"/></svg>
                    </div>
                    <div class="absolute bottom-3 left-3 right-3">
                        <span class="text-white text-xs font-bold uppercase tracking-wider drop-shadow-lg">Custom Development</span>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <h3 class="font-heading text-base sm:text-lg font-bold text-navy mb-2 group-hover:text-navy transition-colors">Software Solutions</h3>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Custom software development for enterprise and government-grade applications.</p>
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
