<section id="company-profile" class="py-20 lg:py-28 relative overflow-hidden">

    {{-- Animated background dots --}}
    <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(#A8703A 1px, transparent 1px); background-size: 28px 28px;"></div>

    {{-- Decorative glows --}}
    <div class="absolute top-0 left-0 w-96 h-96 bg-navy/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple/10 rounded-full blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        {{-- Company Profile Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16 reveal">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-navy/10 text-navy text-xs font-bold uppercase tracking-wider mb-4">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.238A7.995 7.995 0 0010 16c.5 0 1-.037 1.484-.109a1 1 0 01.89.89 11.115 11.115 0 00-.25 3.762 1 1 0 01-1.115.748 8.967 8.967 0 01-4.27-1.458 1 1 0 01-.633-1.54l.748-1.475z"/></svg>
                ASYX Group Limited
            </span>
            <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-navy leading-tight section-title section-title-center shake-title">
                Established 2009
            </h2>
            <p class="mt-6 text-gray-600 text-lg leading-relaxed">
                We deliver smart technologies, ICT infrastructure, cybersecurity, telematics, software solutions, and managed services for mission-critical environments across Africa.
            </p>
        </div>

        {{-- 16+ Years Badge --}}
        <div class="text-center mb-16 reveal">
            <div class="inline-flex flex-col items-center justify-center w-40 h-40 rounded-full bg-gradient-to-br from-navy to-navy/80 shadow-2xl shadow-navy/20">
                <p class="font-heading text-5xl sm:text-6xl font-black text-white">16+</p>
                <p class="text-sm text-white/80 mt-1">Years</p>
            </div>
            <p class="text-xs text-gray-400 mt-3">Leading technological innovation across Africa</p>
        </div>

        {{-- Mission & Vision - Stacked 2-column cards with scroll transitions --}}
        <div class="mv-stack mb-16">

            {{-- Mission Card: image left, text right --}}
            <div class="mv-card mv-mission group relative overflow-hidden rounded-2xl bg-white shadow-xl" data-mv="0">
                <div class="grid md:grid-cols-2 h-full">
                    <div class="relative h-64 md:h-auto overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&q=80" alt="Our Mission" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-navy/20"></div>
                    </div>
                    <div class="flex flex-col justify-center p-8 sm:p-10 md:p-12 relative">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-bronze/10 rounded-full blur-2xl"></div>
                        <span class="text-bronze text-xs font-bold uppercase tracking-wider mb-2">What drives us</span>
                        <h3 class="font-heading text-2xl sm:text-3xl font-black text-navy mb-4">Our Mission</h3>
                        <p class="text-gray-600 leading-relaxed text-sm sm:text-base mb-6">To introduce creative, innovative, and simplified technological solutions that are tailored to Africa's environmental and infrastructural challenges.</p>
                        <div class="flex items-center gap-3 text-sm text-navy font-semibold">
                            <span class="w-8 h-1 bg-bronze rounded-full"></span>
                            <span>Tailored for Africa</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vision Card: text left, image right --}}
            <div class="mv-card mv-vision group relative overflow-hidden rounded-2xl bg-white shadow-xl" data-mv="1">
                <div class="grid md:grid-cols-2 h-full">
                    <div class="flex flex-col justify-center p-8 sm:p-10 md:p-12 relative order-2 md:order-1">
                        <div class="absolute top-0 left-0 w-32 h-32 bg-purple/10 rounded-full blur-2xl"></div>
                        <span class="text-purple text-xs font-bold uppercase tracking-wider mb-2">Where we're headed</span>
                        <h3 class="font-heading text-2xl sm:text-3xl font-black text-navy mb-4">Our Vision</h3>
                        <p class="text-gray-600 leading-relaxed text-sm sm:text-base mb-6">To become the leading company in empowering African countries to realize the full potential of technology in driving sustainable economic growth.</p>
                        <div class="flex items-center gap-3 text-sm text-navy font-semibold">
                            <span class="w-8 h-1 bg-purple rounded-full"></span>
                            <span>Leading Across Africa</span>
                        </div>
                    </div>
                    <div class="relative h-64 md:h-auto overflow-hidden order-1 md:order-2">
                        <img src="https://images.unsplash.com/photo-1451187580459-9546f8937745?w=800&q=80" alt="Our Vision" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-purple/20"></div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Core Values --}}
        <div class="text-center mb-12 reveal">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                Our Core Values
            </span>
            <h3 class="font-heading text-2xl sm:text-3xl font-black text-navy">The Principles That Guide Everything We Do</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
            {{-- Professionalism --}}
            <div class="value-card-v2 group relative overflow-hidden rounded-2xl h-80 shadow-lg reveal-scale stagger-1">
                <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=700&q=80" alt="Professionalism" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-navy via-navy/80 to-navy/30 transition-opacity duration-500 group-hover:via-navy/90"></div>
                <div class="absolute inset-0 p-6 sm:p-8 flex flex-col justify-end">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-3 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <span class="text-white font-heading font-black text-lg">01</span>
                    </div>
                    <h4 class="font-heading text-lg sm:text-xl font-bold text-white mb-2">Professionalism</h4>
                    <p class="text-sm text-gray-200 leading-relaxed">Delivering services with competence, accountability, and excellence in every engagement.</p>
                </div>
            </div>

            {{-- Integrity --}}
            <div class="value-card-v2 group relative overflow-hidden rounded-2xl h-80 shadow-lg reveal-scale stagger-2">
                <img src="https://images.unsplash.com/photo-1573164713714-d95e436ab8d6?w=700&q=80" alt="Integrity" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-purple via-purple/80 to-purple/30 transition-opacity duration-500 group-hover:via-purple/90"></div>
                <div class="absolute inset-0 p-6 sm:p-8 flex flex-col justify-end">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-3 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <span class="text-white font-heading font-black text-lg">02</span>
                    </div>
                    <h4 class="font-heading text-lg sm:text-xl font-bold text-white mb-2">Integrity</h4>
                    <p class="text-sm text-gray-200 leading-relaxed">Acting honestly, transparently, and ethically at all times with every stakeholder.</p>
                </div>
            </div>

            {{-- Innovation --}}
            <div class="value-card-v2 group relative overflow-hidden rounded-2xl h-80 shadow-lg reveal-scale stagger-3">
                <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=700&q=80" alt="Innovation" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-crimson via-crimson/80 to-crimson/30 transition-opacity duration-500 group-hover:via-crimson/90"></div>
                <div class="absolute inset-0 p-6 sm:p-8 flex flex-col justify-end">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-3 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <span class="text-white font-heading font-black text-lg">03</span>
                    </div>
                    <h4 class="font-heading text-lg sm:text-xl font-bold text-white mb-2">Innovation</h4>
                    <p class="text-sm text-gray-200 leading-relaxed">Embracing creativity, continuous improvement, and practical technology solutions.</p>
                </div>
            </div>

            {{-- Teamwork --}}
            <div class="value-card-v2 group relative overflow-hidden rounded-2xl h-80 shadow-lg sm:col-span-2 lg:col-span-1 reveal-scale stagger-4">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=700&q=80" alt="Teamwork" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-navy via-navy/80 to-navy/30 transition-opacity duration-500 group-hover:via-navy/90"></div>
                <div class="absolute inset-0 p-6 sm:p-8 flex flex-col justify-end">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-3 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <span class="text-white font-heading font-black text-lg">04</span>
                    </div>
                    <h4 class="font-heading text-lg sm:text-xl font-bold text-white mb-2">Teamwork</h4>
                    <p class="text-sm text-gray-200 leading-relaxed">Collaborating effectively across teams to achieve shared goals and mission success.</p>
                </div>
            </div>

            {{-- Compliance --}}
            <div class="value-card-v2 group relative overflow-hidden rounded-2xl h-80 shadow-lg sm:col-span-2 lg:col-span-2 reveal-scale stagger-5">
                <img src="https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=700&q=80" alt="Compliance" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-bronze via-bronze/80 to-bronze/30 transition-opacity duration-500 group-hover:via-bronze/90"></div>
                <div class="absolute inset-0 p-6 sm:p-8 flex flex-col justify-end sm:max-w-md">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-3 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <span class="text-white font-heading font-black text-lg">05</span>
                    </div>
                    <h4 class="font-heading text-lg sm:text-xl font-bold text-white mb-2">Compliance</h4>
                    <p class="text-sm text-gray-200 leading-relaxed">Adhering to applicable laws, international standards, and governance requirements in every solution we deliver.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function() {
    {{-- Mission/Vision cards reveal on scroll --}}
    var mvCards = document.querySelectorAll('.mv-card');
    if (mvCards.length) {
        mvCards.forEach(function(card) {
            card.classList.add('hidden-up');
        });

        var mvObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var index = parseInt(entry.target.dataset.mv || 0);
                    setTimeout(function() {
                        entry.target.classList.remove('hidden-up');
                        entry.target.classList.add('visible');
                    }, index * 400);
                }
            });
        }, { threshold: 0.2, rootMargin: '0px 0px -80px 0px' });

        mvCards.forEach(function(card) {
            mvObserver.observe(card);
        });
    }

    {{-- Core value cards reveal --}}
    var valueCards = document.querySelectorAll('.value-card-v2');
    if (valueCards.length) {
        var valueObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });

        valueCards.forEach(function(card) {
            valueObserver.observe(card);
        });
    }
})();
</script>
@endpush
