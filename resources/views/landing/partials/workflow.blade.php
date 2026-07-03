<section class="py-20 lg:py-28 relative overflow-hidden bg-lines">

    <style>
        .workflow-bg {
            background-image:
                linear-gradient(to right, rgba(14,42,74,.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(14,42,74,.05) 1px, transparent 1px);
            background-size: 90px 90px;
        }
        .workflow-beam { position: absolute; top: -160px; width: 1px; height: 160px; pointer-events: none; background: linear-gradient(to bottom, transparent, rgba(14,42,74,.35)); animation: beamDown var(--dur,9s) linear infinite; animation-delay: var(--delay,0s); }
        .workflow-beam.bronze { background: linear-gradient(to bottom, transparent, rgba(168,112,58,.55)); }
        .workflow-beam-h { position: absolute; left: -220px; height: 1px; width: 220px; pointer-events: none; background: linear-gradient(to right, transparent, rgba(14,42,74,.3)); animation: beamRight var(--dur,12s) linear infinite; animation-delay: var(--delay,0s); }
        @keyframes beamDown { to { transform: translateY(calc(100vh + 320px)); } }
        @keyframes beamRight { to { transform: translateX(calc(100vw + 440px)); } }

        .wf-rv-l { opacity: 0; transform: translateX(-50px); transition: opacity 1s cubic-bezier(.16,1,.3,1), transform 1s cubic-bezier(.16,1,.3,1); }
        .wf-rv-r { opacity: 0; transform: translateX(50px); transition: opacity 1s cubic-bezier(.16,1,.3,1), transform 1s cubic-bezier(.16,1,.3,1); }
        .wf-rv-l.in-view, .wf-rv-r.in-view { opacity: 1; transform: none; }

        .wf-stagger > * { opacity: 0; transform: translateY(24px); transition: opacity .8s cubic-bezier(.2,.7,.2,1), transform .8s cubic-bezier(.2,.7,.2,1); }
        .wf-stagger.in-view > *:nth-child(1) { transition-delay: .05s; }
        .wf-stagger.in-view > *:nth-child(2) { transition-delay: .15s; }
        .wf-stagger.in-view > *:nth-child(3) { transition-delay: .25s; }
        .wf-stagger.in-view > *:nth-child(4) { transition-delay: .35s; }
        .wf-stagger.in-view > *:nth-child(5) { transition-delay: .45s; }
        .wf-stagger.in-view > * { opacity: 1; transform: none; }

        .wf-underline { width: 0; height: 4px; border-radius: 99px; background: #A8703A; margin-top: 14px; transition: width 1s cubic-bezier(.7,0,.2,1) .3s; }
        .wf-rv-l.in-view .wf-underline { width: 96px; }

        .wf-stat-item { position: relative; }
        .wf-stat-item::after { content: ''; position: absolute; right: -12px; top: 8px; bottom: 8px; width: 1px; background: rgba(14,42,74,.12); }
        .wf-stat-item:last-child::after { display: none; }

        .btn-about { position: relative; overflow: hidden; border: 1.5px solid #0E2A4A; color: #0E2A4A; transition: color .35s, transform .3s, box-shadow .3s; }
        .btn-about::before { content: ''; position: absolute; inset: 0; background: #0E2A4A; transform: scaleX(0); transform-origin: left; transition: transform .4s cubic-bezier(.7,0,.2,1); z-index: 0; }
        .btn-about:hover::before { transform: scaleX(1); }
        .btn-about:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 14px 28px -12px rgba(14,42,74,.4); }
        .btn-about > * { position: relative; z-index: 1; }
        .btn-about svg { transition: transform .35s; }
        .btn-about:hover svg { transform: translateX(5px); }

        .img-main { clip-path: inset(0 100% 0 0); transition: clip-path 1.2s cubic-bezier(.7,0,.2,1) .2s; }
        .in-view .img-main { clip-path: inset(0 0 0 0); }
        .kenburns { animation: kb 22s ease-in-out infinite alternate; }
        @keyframes kb { from { transform: scale(1); } to { transform: scale(1.12) translate(-1.5%,1.5%); } }

        .frame-line { position: absolute; inset: -14px; border-radius: 1.6rem; pointer-events: none; }
        .frame-line rect { fill: none; stroke: #A8703A; stroke-width: 2; stroke-dasharray: 1600; stroke-dashoffset: 1600; transition: stroke-dashoffset 2s cubic-bezier(.6,0,.2,1) .6s; }
        .in-view .frame-line rect { stroke-dashoffset: 0; }

        .img-small { opacity: 0; transform: translate(-26px, 26px); transition: all 1s cubic-bezier(.2,.7,.2,1) .55s; }
        .in-view .img-small { opacity: 1; transform: none; }

        .fcard { opacity: 0; transform: translateY(-20px); transition: all .8s cubic-bezier(.34,1.56,.64,1) .85s; }
        .in-view .fcard { opacity: 1; transform: none; }
        .float-slow { animation: floatY 7s ease-in-out infinite; }
        @keyframes floatY { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

        .wf-pulse-dot { position: relative; }
        .wf-pulse-dot::after { content: ''; position: absolute; inset: 0; border-radius: 99px; background: inherit; animation: wf-ping 2s cubic-bezier(0,0,.2,1) infinite; }
        @keyframes wf-ping { 75%, 100% { transform: scale(2.3); opacity: 0; } }

        .ring-spin { animation: spin 20s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (prefers-reduced-motion: reduce) {
            .wf-rv-l, .wf-rv-r, .wf-stagger > *, .img-main, .img-small, .fcard, .frame-line rect, .workflow-beam, .workflow-beam-h, .kenburns, .float-slow, .ring-spin { animation: none; opacity: 1; transform: none; clip-path: none; transition: none; stroke-dashoffset: 0; }
        }
    </style>

    {{-- Animated line beams --}}
    <div class="absolute inset-0 pointer-events-none workflow-bg" aria-hidden="true">
        <span class="workflow-beam" style="left:90px; --dur:9s; --delay:0s"></span>
        <span class="workflow-beam bronze" style="left:360px; --dur:12s; --delay:2.5s"></span>
        <span class="workflow-beam" style="left:630px; --dur:10s; --delay:5s"></span>
        <span class="workflow-beam" style="left:900px; --dur:13s; --delay:1.2s"></span>
        <span class="workflow-beam bronze" style="left:1170px; --dur:11s; --delay:6.5s"></span>
        <span class="workflow-beam" style="left:1440px; --dur:9.5s; --delay:3.8s"></span>
        <span class="workflow-beam-h" style="top:180px; --dur:14s; --delay:1s"></span>
        <span class="workflow-beam-h" style="top:450px; --dur:17s; --delay:6s"></span>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid lg:grid-cols-2 gap-16 xl:gap-20 items-center">

            {{-- LEFT: Content --}}
            <div class="wf-rv-l" data-wf-rv>
                <div class="wf-stagger" data-wf-rv>
                    <span class="inline-flex items-center gap-2.5 text-bronze text-xs font-bold uppercase tracking-[0.22em] mb-5">
                        <span class="w-8 h-px bg-bronze"></span>
                        01 &middot; About ASYX
                    </span>

                    <div>
                        <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-navy leading-tight">
                            Tanzania's Trusted<br>Technology Partner
                        </h2>
                        <div class="wf-underline"></div>
                    </div>

                    <p class="text-gray-600 text-lg leading-relaxed mt-8 mb-5">
                        Since 2009, ASYX Group has been the trusted technology partner behind Tanzania's most critical systems &mdash; from power utilities to public transport to financial regulation.
                    </p>
                    <p class="text-gray-600 leading-relaxed mb-9">
                        We serve government bodies, parastatals and regulated enterprises with smart technology, secure infrastructure, and sustainable growth solutions. Our 16+ years of experience means we understand the unique challenges of mission-critical operations in the Tanzanian context.
                    </p>

                    <div class="grid grid-cols-3 gap-6 mb-10">
                        <div class="wf-stat-item">
                            <p class="font-heading text-3xl sm:text-4xl font-black text-navy"><span class="count" data-target="16">0</span>+</p>
                            <p class="text-xs text-gray-500 mt-1.5">Years of Service</p>
                        </div>
                        <div class="wf-stat-item">
                            <p class="font-heading text-3xl sm:text-4xl font-black text-navy"><span class="count" data-target="50">0</span>+</p>
                            <p class="text-xs text-gray-500 mt-1.5">Enterprise Clients</p>
                        </div>
                        <div class="wf-stat-item">
                            <p class="font-heading text-3xl sm:text-4xl font-black text-navy"><span class="count" data-target="9">0</span></p>
                            <p class="text-xs text-gray-500 mt-1.5">Service Pillars</p>
                        </div>
                    </div>

                    <a href="{{ route('about') }}" class="btn-about inline-flex items-center justify-center gap-2 px-7 py-3.5 text-base font-bold rounded-xl">
                        <span>Learn More About Us</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>

            {{-- RIGHT: Visual --}}
            <div class="relative wf-rv-r" data-wf-rv>

                {{-- thin bronze frame that draws itself --}}
                <svg class="frame-line" aria-hidden="true">
                    <rect x="1" y="1" width="100%" height="100%" rx="24"></rect>
                </svg>

                {{-- MAIN image with wipe reveal --}}
                <div class="img-main relative rounded-3xl overflow-hidden shadow-2xl shadow-navy/20">
                    <div class="aspect-[4/3] relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=1000&q=80" alt="ASYX Group professionals at work" class="kenburns w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-navy/85 via-navy/20 to-transparent"></div>

                        <div class="absolute bottom-0 left-0 right-0 p-7 sm:p-8 flex items-end justify-between gap-4">
                            <div>
                                <p class="font-heading text-xl sm:text-2xl font-bold text-white mb-1">Since 2009</p>
                                <p class="text-sm text-gray-200">Powering Tanzania's mission-critical systems for 16+ years</p>
                            </div>
                            <div class="relative w-20 h-20 flex-shrink-0 hidden sm:block">
                                <svg class="ring-spin absolute inset-0 w-full h-full" viewBox="0 0 80 80" fill="none">
                                    <circle cx="40" cy="40" r="38" stroke="rgba(255,255,255,.35)" stroke-width="1.5" stroke-dasharray="6 8"/>
                                </svg>
                                <div class="absolute inset-2 rounded-full bg-white/10 border border-white/25 backdrop-blur flex items-center justify-center">
                                    <span class="font-heading font-extrabold text-white text-lg">2009</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SMALL overlapping image --}}
                <div class="img-small absolute -bottom-9 -left-5 sm:-left-9 w-40 sm:w-52 rounded-2xl overflow-hidden shadow-2xl shadow-navy/25 border-4 border-white hidden sm:block">
                    <div class="aspect-[5/4] relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1573164713988-8665fc963095?w=500&q=80" alt="Engineering team at work" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-navy/30 mix-blend-multiply"></div>
                    </div>
                </div>

                {{-- ONE floating card --}}
                <div class="fcard float-slow absolute -top-6 right-5 sm:-right-5 bg-white rounded-2xl pl-4 pr-6 py-3.5 shadow-xl shadow-navy/15 border border-navy/5 flex items-center gap-3">
                    <span class="relative flex w-2.5 h-2.5">
                        <span class="wf-pulse-dot w-2.5 h-2.5 rounded-full bg-bronze"></span>
                    </span>
                    <div>
                        <p class="font-heading text-sm font-bold text-navy leading-tight">Mission-Critical Ready</p>
                        <p class="text-xs text-gray-500 mt-0.5">24/7 Support Available</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function() {
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('in-view');
            runCounters(entry.target);
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.2, rootMargin: '0px 0px -60px 0px' });
    document.querySelectorAll('[data-wf-rv]').forEach(function(el){ observer.observe(el); });

    function runCounters(scope) {
        scope.querySelectorAll('.count').forEach(function(el) {
            if (el.dataset.done) return;
            el.dataset.done = '1';
            var target = parseInt(el.dataset.target), dur = 1500, start = null;
            if (reduceMotion) { el.textContent = target; return; }
            function step(ts) {
                if (!start) start = ts;
                var prog = Math.min((ts - start) / dur, 1);
                el.textContent = Math.round((1 - Math.pow(1 - prog, 3)) * target);
                if (prog < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        });
    }
})();
</script>
@endpush
