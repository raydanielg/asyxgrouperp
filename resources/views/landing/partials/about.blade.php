<section id="why-asyx" class="py-20 lg:py-28 relative overflow-hidden">

    {{-- Animated dots background --}}
    <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(#1B3A5C 1px, transparent 1px); background-size: 30px 30px;"></div>

    {{-- Decorative glows --}}
    <div class="absolute top-1/2 left-1/4 w-96 h-96 bg-bronze/10 rounded-full blur-3xl -translate-y-1/2"></div>
    <div class="absolute top-1/2 right-1/4 w-96 h-96 bg-crimson/10 rounded-full blur-3xl -translate-y-1/2"></div>

    <style>
        @keyframes why-float-1 { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-14px) rotate(1.2deg); } }
        @keyframes why-float-2 { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-10px) rotate(-1deg); } }
        @keyframes why-float-3 { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-16px) rotate(0.6deg); } }
        @keyframes why-float-4 { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-8px) rotate(-0.8deg); } }
        @keyframes why-float-5 { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-12px) rotate(1deg); } }

        @keyframes why-glow { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }

        @keyframes why-in-up    { 0% { opacity: 0; transform: translateY(70px) scale(0.85); } 100% { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes why-in-right { 0% { opacity: 0; transform: translateX(-70px) scale(0.85); } 100% { opacity: 1; transform: translateX(0) scale(1); } }
        @keyframes why-in-left  { 0% { opacity: 0; transform: translateX(70px) scale(0.85); } 100% { opacity: 1; transform: translateX(0) scale(1); } }
        @keyframes why-in-zoom  { 0% { opacity: 0; transform: scale(0.5) rotate(-8deg); } 100% { opacity: 1; transform: scale(1) rotate(0deg); } }
        @keyframes why-in-flip  { 0% { opacity: 0; transform: perspective(600px) rotateY(-90deg); } 100% { opacity: 1; transform: perspective(600px) rotateY(0deg); } }

        .why-card {
            transform-style: preserve-3d;
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.35s ease;
            opacity: 0;
        }
        .why-card.revealed { opacity: 1; }

        .why-card-1.revealed { animation: why-in-right 0.85s cubic-bezier(0.22, 1, 0.36, 1) forwards, why-float-1 5.2s ease-in-out infinite 0.85s; }
        .why-card-2.revealed { animation: why-in-zoom  0.85s cubic-bezier(0.22, 1, 0.36, 1) 0.12s forwards, why-float-2 6.0s ease-in-out infinite 0.97s; }
        .why-card-3.revealed { animation: why-in-up    0.85s cubic-bezier(0.22, 1, 0.36, 1) 0.24s forwards, why-float-3 4.6s ease-in-out infinite 1.09s; }
        .why-card-4.revealed { animation: why-in-left  0.85s cubic-bezier(0.22, 1, 0.36, 1) 0.36s forwards, why-float-4 5.5s ease-in-out infinite 1.21s; }
        .why-card-5.revealed { animation: why-in-flip  0.95s cubic-bezier(0.22, 1, 0.36, 1) 0.48s forwards, why-float-5 4.9s ease-in-out infinite 1.43s; }

        .why-card::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 1.25rem;
            background: linear-gradient(60deg, #C9A96E, #1B3A5C, #C9A96E, #8B2D3B, #C9A96E);
            background-size: 400% 400%;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.4s ease;
            animation: why-glow 4s ease infinite;
        }
        .why-card:hover::before { opacity: 1; }
        .why-card:hover { box-shadow: 0 30px 60px -12px rgba(27, 58, 92, 0.35); }

        .why-card .shine {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 0%, rgba(255,255,255,0.25) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
            z-index: 10;
        }
        .why-card:hover .shine { opacity: 1; }

        .why-card .number-badge {
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), background 0.4s ease, box-shadow 0.4s ease;
        }
        .why-card:hover .number-badge {
            transform: scale(1.2) rotate(10deg);
            box-shadow: 0 10px 25px -5px rgba(201, 169, 110, 0.5);
        }

        @media (prefers-reduced-motion: reduce) {
            .why-card,
            .why-card.revealed,
            .why-card-1.revealed,
            .why-card-2.revealed,
            .why-card-3.revealed,
            .why-card-4.revealed,
            .why-card-5.revealed {
                animation: none;
                opacity: 1;
                transform: none;
            }
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        {{-- Section header --}}
        <div class="text-center max-w-2xl mx-auto mb-16 reveal">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                04 Why ASYX
            </span>
            <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-navy leading-tight section-title section-title-center">
                Why Choose ASYX Group
            </h2>
            <p class="mt-6 text-gray-600 text-lg leading-relaxed">
                Five differentiators that make us Tanzania's most trusted technology partner for mission-critical systems.
            </p>
        </div>

        {{-- 5 Differentiator cards - 2 cols on mobile, 5 on desktop --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6" style="perspective: 1000px;">
            {{-- 1 --}}
            <div class="why-card why-card-1 card-hover group overflow-hidden rounded-2xl h-80 relative">
                <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=600&q=80" alt="16+ Years of Trust" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-navy via-navy/70 to-transparent"></div>
                <div class="shine"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-end p-5 sm:p-6 text-center">
                    <div class="number-badge w-12 h-12 rounded-full cta-gradient flex items-center justify-center mb-3 text-white font-heading font-black text-lg shadow-lg">01</div>
                    <h3 class="font-heading text-sm sm:text-base font-bold text-white mb-2">16+ Years of Trust</h3>
                    <p class="text-xs sm:text-sm text-gray-200 leading-relaxed">Since 2009, we've powered Tanzania's most critical systems with zero compromise on reliability.</p>
                </div>
            </div>

            {{-- 2 --}}
            <div class="why-card why-card-2 card-hover group overflow-hidden rounded-2xl h-80 relative">
                <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?w=600&q=80" alt="Government-Grade Security" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-purple via-purple/70 to-transparent"></div>
                <div class="shine"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-end p-5 sm:p-6 text-center">
                    <div class="number-badge w-12 h-12 rounded-full cta-gradient flex items-center justify-center mb-3 text-white font-heading font-black text-lg shadow-lg">02</div>
                    <h3 class="font-heading text-sm sm:text-base font-bold text-white mb-2">Government-Grade Security</h3>
                    <p class="text-xs sm:text-sm text-gray-200 leading-relaxed">Security and compliance standards built for public-sector and regulated enterprise requirements.</p>
                </div>
            </div>

            {{-- 3 --}}
            <div class="why-card why-card-3 card-hover group overflow-hidden rounded-2xl h-80 relative">
                <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=600&q=80" alt="Rapid Response" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-crimson via-crimson/70 to-transparent"></div>
                <div class="shine"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-end p-5 sm:p-6 text-center">
                    <div class="number-badge w-12 h-12 rounded-full cta-gradient flex items-center justify-center mb-3 text-white font-heading font-black text-lg shadow-lg">03</div>
                    <h3 class="font-heading text-sm sm:text-base font-bold text-white mb-2">Rapid Response</h3>
                    <p class="text-xs sm:text-sm text-gray-200 leading-relaxed">24/7 support with guaranteed response times - because mission-critical means zero downtime.</p>
                </div>
            </div>

            {{-- 4 --}}
            <div class="why-card why-card-4 card-hover group overflow-hidden rounded-2xl h-80 relative">
                <img src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=600&q=80" alt="Local Expertise" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-navy via-navy/70 to-transparent"></div>
                <div class="shine"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-end p-5 sm:p-6 text-center">
                    <div class="number-badge w-12 h-12 rounded-full cta-gradient flex items-center justify-center mb-3 text-white font-heading font-black text-lg shadow-lg">04</div>
                    <h3 class="font-heading text-sm sm:text-base font-bold text-white mb-2">Local Expertise</h3>
                    <p class="text-xs sm:text-sm text-gray-200 leading-relaxed">Deep understanding of Tanzanian regulatory landscape, infrastructure, and institutional workflows.</p>
                </div>
            </div>

            {{-- 5 --}}
            <div class="why-card why-card-5 card-hover group overflow-hidden rounded-2xl h-80 relative">
                <img src="https://images.unsplash.com/photo-1531498860502-7c67cf02f657?w=600&q=80" alt="End-to-End Capability" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-bronze via-bronze/70 to-transparent"></div>
                <div class="shine"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-end p-5 sm:p-6 text-center">
                    <div class="number-badge w-12 h-12 rounded-full cta-gradient flex items-center justify-center mb-3 text-white font-heading font-black text-lg shadow-lg">05</div>
                    <h3 class="font-heading text-sm sm:text-base font-bold text-white mb-2">End-to-End Capability</h3>
                    <p class="text-xs sm:text-sm text-gray-200 leading-relaxed">From infrastructure to software to training - one partner, complete solutions, no gaps.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function() {
    var cards = document.querySelectorAll('.why-card');
    if (cards.length) {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });

        cards.forEach(function(card) {
            observer.observe(card);

            // 3D tilt effect on mouse move
            card.addEventListener('mousemove', function(e) {
                var rect = card.getBoundingClientRect();
                var x = e.clientX - rect.left;
                var y = e.clientY - rect.top;
                var centerX = rect.width / 2;
                var centerY = rect.height / 2;
                var rotateX = ((y - centerY) / centerY) * -8;
                var rotateY = ((x - centerX) / centerX) * 8;
                card.style.transform = 'perspective(600px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) translateY(-10px) scale(1.03)';
            });

            card.addEventListener('mouseleave', function() {
                card.style.transform = '';
            });
        });
    }
})();
</script>
@endpush
