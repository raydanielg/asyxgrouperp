<section id="clients" class="py-20 lg:py-28 relative overflow-hidden clients-section bg-[#F8F9FB]">
    {{-- Background decorations --}}
    <div class="absolute inset-0 opacity-5 dot-grid-clients"></div>
    <div class="absolute top-0 left-0 w-96 h-96 bg-navy/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-bronze/5 rounded-full blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        {{-- Header --}}
        <div class="text-center max-w-3xl mx-auto mb-12 reveal">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
                Trusted Partnerships
            </span>
            <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-navy leading-tight mb-4">
                Our Clients
            </h2>
            <p class="text-gray-600 text-lg leading-relaxed">
                Proudly trusted by Tanzania's most critical institutions and regulated enterprises to deliver mission-critical technology solutions.
            </p>
        </div>

        {{-- Stats strip --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto mb-14 reveal">
            <div class="bg-white rounded-2xl p-5 text-center shadow-lg shadow-navy/5 border border-gray-100 hover:-translate-y-1 transition-transform duration-300">
                <p class="font-heading text-3xl font-black text-bronze">50+</p>
                <p class="text-xs text-gray-500 mt-1 font-medium uppercase tracking-wide">Enterprise Clients</p>
            </div>
            <div class="bg-white rounded-2xl p-5 text-center shadow-lg shadow-navy/5 border border-gray-100 hover:-translate-y-1 transition-transform duration-300">
                <p class="font-heading text-3xl font-black text-navy">16+</p>
                <p class="text-xs text-gray-500 mt-1 font-medium uppercase tracking-wide">Years Experience</p>
            </div>
            <div class="bg-white rounded-2xl p-5 text-center shadow-lg shadow-navy/5 border border-gray-100 hover:-translate-y-1 transition-transform duration-300">
                <p class="font-heading text-3xl font-black text-purple">15+</p>
                <p class="text-xs text-gray-500 mt-1 font-medium uppercase tracking-wide">Government Bodies</p>
            </div>
            <div class="bg-white rounded-2xl p-5 text-center shadow-lg shadow-navy/5 border border-gray-100 hover:-translate-y-1 transition-transform duration-300">
                <p class="font-heading text-3xl font-black text-crimson">99%</p>
                <p class="text-xs text-gray-500 mt-1 font-medium uppercase tracking-wide">Client Retention</p>
            </div>
        </div>

        {{-- Logo grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 sm:gap-6 reveal" id="clientGrid">
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/tanesco.png') }}" alt="TANESCO" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">TANESCO</p>
            </div>
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/tra.png') }}" alt="TRA" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">TRA</p>
            </div>
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/nssf.gif') }}" alt="NSSF" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">NSSF</p>
            </div>
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/tcra.jfif') }}" alt="TCRA" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">TCRA</p>
            </div>
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/tcaa.png') }}" alt="TCAA" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">TCAA</p>
            </div>
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/tarura.png') }}" alt="TARURA" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">TARURA</p>
            </div>
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/latra.jfif') }}" alt="LATRA" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">LATRA</p>
            </div>
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/ewuralogo.png') }}" alt="EWURA" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">EWURA</p>
            </div>
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/gpsa.jfif') }}" alt="GPSA" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">GPSA</p>
            </div>
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/nectalogo.png') }}" alt="NECTA" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">NECTA</p>
            </div>
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/preccsionair.jfif') }}" alt="Precision Air" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">Precision Air</p>
            </div>
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/udsmlogo.png') }}" alt="UDSM" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">UDSM</p>
            </div>
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/veta.png') }}" alt="VETA" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">VETA</p>
            </div>
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/psbtb logo.jfif') }}" alt="PSBTB" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">PSBTB</p>
            </div>
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/download.jfif') }}" alt="Client" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">Partner</p>
            </div>
            <div class="client-card group bg-white rounded-2xl p-5 sm:p-6 flex flex-col items-center justify-center text-center border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 h-36 sm:h-40">
                <img src="{{ asset('clients/download (1).jfif') }}" alt="Client" class="h-12 sm:h-14 w-auto object-contain mb-2 opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                <p class="text-[10px] sm:text-xs font-bold text-gray-500 group-hover:text-navy transition-colors">Partner</p>
            </div>
        </div>

        {{-- Infinite marquee of client names --}}
        <div class="mt-16 overflow-hidden reveal" id="clientsMarquee">
            <div class="clients-track flex gap-8 items-center font-heading font-bold text-navy/10 text-2xl sm:text-3xl uppercase whitespace-nowrap">
                <span>TANESCO</span><span class="text-bronze/30">•</span>
                <span>TRA</span><span class="text-purple/30">•</span>
                <span>NSSF</span><span class="text-crimson/30">•</span>
                <span>TCRA</span><span class="text-bronze/30">•</span>
                <span>TCAA</span><span class="text-purple/30">•</span>
                <span>TARURA</span><span class="text-crimson/30">•</span>
                <span>LATRA</span><span class="text-bronze/30">•</span>
                <span>EWURA</span><span class="text-purple/30">•</span>
                <span>GPSA</span><span class="text-crimson/30">•</span>
                <span>NECTA</span><span class="text-bronze/30">•</span>
                <span>Precision Air</span><span class="text-purple/30">•</span>
                <span>UDSM</span><span class="text-crimson/30">•</span>
                <span>VETA</span><span class="text-bronze/30">•</span>
                <span>PSBTB</span><span class="text-purple/30">•</span>
                <span>TANESCO</span><span class="text-bronze/30">•</span>
                <span>TRA</span><span class="text-purple/30">•</span>
                <span>NSSF</span><span class="text-crimson/30">•</span>
                <span>TCRA</span><span class="text-bronze/30">•</span>
                <span>TCAA</span><span class="text-purple/30">•</span>
                <span>TARURA</span><span class="text-crimson/30">•</span>
                <span>LATRA</span><span class="text-bronze/30">•</span>
                <span>EWURA</span><span class="text-purple/30">•</span>
                <span>GPSA</span><span class="text-crimson/30">•</span>
                <span>NECTA</span><span class="text-bronze/30">•</span>
                <span>Precision Air</span><span class="text-purple/30">•</span>
                <span>UDSM</span><span class="text-crimson/30">•</span>
                <span>VETA</span><span class="text-bronze/30">•</span>
                <span>PSBTB</span><span class="text-purple/30">•</span>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function() {
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    {{-- Staggered reveal for client cards --}}
    var cards = document.querySelectorAll('#clientGrid .client-card');
    if (cards.length) {
        cards.forEach(function(card, i) {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px) scale(0.95)';
            card.style.transition = 'opacity .6s ease, transform .6s cubic-bezier(.2,.7,.2,1)';
        });

        var gridObserver = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting) {
                cards.forEach(function(card, i) {
                    setTimeout(function() {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0) scale(1)';
                    }, i * 80);
                });
                gridObserver.disconnect();
            }
        }, { threshold: 0.15 });
        gridObserver.observe(document.getElementById('clientGrid'));
    }

    {{-- Infinite marquee for clients --}}
    var track = document.querySelector('.clients-track');
    if (track && !reduceMotion) {
        var duration = 40;
        track.style.animation = 'clientsMarquee ' + duration + 's linear infinite';
    }
})();
</script>
@endpush
