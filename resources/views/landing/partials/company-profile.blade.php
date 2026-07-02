<section id="company-profile" class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Company Profile Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16 reveal">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full cta-gradient mb-6">
                <span class="text-xs font-bold text-white tracking-wide uppercase">ASYX Group Limited</span>
            </div>
            <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-navy leading-tight section-title section-title-center">
                Established 2009
            </h2>
            <p class="mt-6 text-gray-600 text-lg leading-relaxed">
                We deliver smart technologies, ICT infrastructure, cybersecurity, telematics, software solutions, and managed services for mission-critical environments across Africa.
            </p>
            <p class="mt-4 text-gray-500 leading-relaxed">
                Partnering with public institutions, parastatals, and private organizations to design, deploy, and support systems that enhance efficiency, security, and service delivery.
            </p>
            <div class="mt-6">
                <span class="inline-block px-5 py-2 rounded-full bg-bronze/10 text-bronze text-sm font-bold">Company Profile 2026</span>
            </div>
        </div>

        {{-- 16+ Years Badge --}}
        <div class="text-center mb-16 reveal">
            <div class="inline-flex flex-col items-center">
                <p class="font-heading text-5xl sm:text-6xl font-black text-gradient">16+</p>
                <p class="text-sm text-gray-500 mt-1">Years of Excellence</p>
                <p class="text-xs text-gray-400 mt-1">Leading technological innovation across Africa</p>
            </div>
        </div>

        {{-- Mission & Vision --}}
        <div id="mission-vision" class="relative rounded-3xl overflow-hidden mb-16">
            {{-- Navy gradient background --}}
            <div class="absolute inset-0 hero-gradient"></div>
            {{-- Animated dots overlay --}}
            <div class="absolute inset-0 animated-dots-bg"></div>
            {{-- Decorative glows --}}
            <div class="absolute top-10 right-10 w-72 h-72 bg-bronze/20 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-10 left-10 w-80 h-80 bg-purple/20 rounded-full blur-3xl"></div>

            <div class="relative z-10 p-8 sm:p-12 lg:p-16">
                {{-- Section label --}}
                <div class="text-center mb-10">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4 backdrop-blur-sm">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                        Who We Are
                    </span>
                    <h3 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-black text-white">Our Mission &amp; Vision</h3>
                </div>

                {{-- Cards --}}
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Mission Card --}}
                    <div class="drag-card group relative rounded-2xl overflow-hidden h-72" id="mission-card">
                        <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b42?w=800&q=80" alt="Our Mission" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-bronze via-bronze/70 to-bronze/20"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-6 sm:p-8">
                            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <h4 class="font-heading text-xl sm:text-2xl font-bold text-white mb-2">Our Mission</h4>
                            <p class="text-sm sm:text-base text-white/90 leading-relaxed">To introduce creative, innovative, and simplified technological solutions that are tailored to Africa's environmental and infrastructural challenges.</p>
                        </div>
                    </div>

                    {{-- Vision Card --}}
                    <div class="drag-card drag-card-2 group relative rounded-2xl overflow-hidden h-72" id="vision-card">
                        <img src="https://images.unsplash.com/photo-1532012194066-903394ce0a91?w=800&q=80" alt="Our Vision" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-purple via-purple/70 to-purple/20"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-6 sm:p-8">
                            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </div>
                            <h4 class="font-heading text-xl sm:text-2xl font-bold text-white mb-2">Our Vision</h4>
                            <p class="text-sm sm:text-base text-white/90 leading-relaxed">To become the leading company in empowering African countries to realize the full potential of technology in driving sustainable economic growth.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
        (function() {
            var missionCard = document.getElementById('mission-card');
            var visionCard = document.getElementById('vision-card');
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('dragged-in');
                    }
                });
            }, { threshold: 0.3 });
            if (missionCard) observer.observe(missionCard);
            if (visionCard) observer.observe(visionCard);
        })();
        </script>

        {{-- Core Values --}}
        <div class="text-center mb-12 reveal">
            <span class="inline-block px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">Our Core Values</span>
            <h3 class="font-heading text-2xl sm:text-3xl font-black text-navy">The Principles That Guide Everything We Do</h3>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6">
            <div class="bg-[#F2F2F2] rounded-2xl p-5 sm:p-6 text-center reveal-scale stagger-1">
                <div class="w-14 h-14 mx-auto rounded-full cta-gradient flex items-center justify-center mb-4 text-white font-heading font-black text-xl">01</div>
                <h4 class="font-heading text-sm sm:text-base font-bold text-navy mb-2">Professionalism</h4>
                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Delivering services with competence, accountability, and excellence.</p>
            </div>
            <div class="bg-[#F2F2F2] rounded-2xl p-5 sm:p-6 text-center reveal-scale stagger-2">
                <div class="w-14 h-14 mx-auto rounded-full cta-gradient flex items-center justify-center mb-4 text-white font-heading font-black text-xl">02</div>
                <h4 class="font-heading text-sm sm:text-base font-bold text-navy mb-2">Integrity</h4>
                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Acting honestly, transparently, and ethically at all times.</p>
            </div>
            <div class="bg-[#F2F2F2] rounded-2xl p-5 sm:p-6 text-center reveal-scale stagger-3">
                <div class="w-14 h-14 mx-auto rounded-full cta-gradient flex items-center justify-center mb-4 text-white font-heading font-black text-xl">03</div>
                <h4 class="font-heading text-sm sm:text-base font-bold text-navy mb-2">Innovation</h4>
                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Embracing creativity, continuous improvement, and practical solutions.</p>
            </div>
            <div class="bg-[#F2F2F2] rounded-2xl p-5 sm:p-6 text-center reveal-scale stagger-4">
                <div class="w-14 h-14 mx-auto rounded-full cta-gradient flex items-center justify-center mb-4 text-white font-heading font-black text-xl">04</div>
                <h4 class="font-heading text-sm sm:text-base font-bold text-navy mb-2">Teamwork</h4>
                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Collaborating effectively to achieve shared goals.</p>
            </div>
            <div class="bg-[#F2F2F2] rounded-2xl p-5 sm:p-6 text-center reveal-scale stagger-5">
                <div class="w-14 h-14 mx-auto rounded-full cta-gradient flex items-center justify-center mb-4 text-white font-heading font-black text-xl">05</div>
                <h4 class="font-heading text-sm sm:text-base font-bold text-navy mb-2">Compliance</h4>
                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Adhering to applicable laws, standards, and governance requirements.</p>
            </div>
        </div>
    </div>
</section>
