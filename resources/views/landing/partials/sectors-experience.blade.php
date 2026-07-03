<section id="sectors-experience" class="py-20 lg:py-28 relative overflow-hidden bg-lines">

    <style>
        .sector-bg {
            background-image:
                linear-gradient(to right, rgba(14,42,74,.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(14,42,74,.05) 1px, transparent 1px);
            background-size: 90px 90px;
        }
        .sector-beam { position: absolute; top: -160px; width: 1px; height: 160px; pointer-events: none; background: linear-gradient(to bottom, transparent, rgba(14,42,74,.35)); animation: sectorBeamDown var(--dur,10s) linear infinite; animation-delay: var(--delay,0s); }
        .sector-beam.bronze { background: linear-gradient(to bottom, transparent, rgba(168,112,58,.55)); }
        .sector-beam-h { position: absolute; left: -220px; height: 1px; width: 220px; pointer-events: none; background: linear-gradient(to right, transparent, rgba(14,42,74,.3)); animation: sectorBeamRight var(--dur,14s) linear infinite; animation-delay: var(--delay,0s); }
        @keyframes sectorBeamDown { to { transform: translateY(calc(100vh + 320px)); } }
        @keyframes sectorBeamRight { to { transform: translateX(calc(100vw + 440px)); } }

        .sector-rv { opacity: 0; transform: translateY(40px); transition: opacity .9s cubic-bezier(.2,.7,.2,1), transform .9s cubic-bezier(.2,.7,.2,1); }
        .sector-rv.in-view { opacity: 1; transform: none; }
        .sector-underline { width: 0; height: 4px; border-radius: 99px; background: #A8703A; margin: 14px auto 0; transition: width 1s cubic-bezier(.7,0,.2,1) .3s; }
        .sector-rv.in-view .sector-underline { width: 96px; }

        .sector-card { position: relative; overflow: hidden; background: #fff; border-radius: 1.25rem; border: 1px solid rgba(14,42,74,.07); box-shadow: 0 8px 22px -14px rgba(14,42,74,.12); opacity: 0; transform: translateY(38px); transition: opacity .8s cubic-bezier(.2,.7,.2,1), transform .8s cubic-bezier(.2,.7,.2,1), box-shadow .45s; }
        .sector-card.in-view { opacity: 1; transform: none; }
        .sector-card:hover { transform: translateY(-8px); box-shadow: 0 30px 55px -20px rgba(14,42,74,.35); }
        .sector-card::before { content: ''; position: absolute; inset: 0; background: #0E2A4A; transform: translateY(101%); transition: transform .55s cubic-bezier(.7,0,.2,1); z-index: 0; }
        .sector-card:hover::before { transform: translateY(0); }
        .sector-card > * { position: relative; z-index: 1; }

        .sec-idx { position: absolute; top: .9rem; right: 1.2rem; font-family: 'Sora', sans-serif; font-weight: 800; font-size: 2.6rem; line-height: 1; color: rgba(14,42,74,.06); transition: color .45s; z-index: 1; }
        .sector-card:hover .sec-idx { color: rgba(255,255,255,.1); }

        .sec-icon { width: 52px; height: 52px; border-radius: 14px; background: rgba(168,112,58,.1); color: #A8703A; display: flex; align-items: center; justify-content: center; transition: all .5s cubic-bezier(.34,1.56,.64,1); }
        .sector-card:hover .sec-icon { background: #A8703A; color: #fff; transform: rotate(-8deg) scale(1.08); }

        .sec-title { transition: color .45s; }
        .sector-card:hover .sec-title { color: #fff; }

        .sector-chip { display: inline-flex; align-items: center; padding: .3rem .7rem; border-radius: 99px; font-size: .72rem; font-weight: 600; background: rgba(14,42,74,.05); color: #0E2A4A; border: 1px solid transparent; transition: all .45s; }
        .sector-card:hover .sector-chip { background: rgba(255,255,255,.1); color: rgba(255,255,255,.92); border-color: rgba(255,255,255,.14); }

        .sec-line { position: absolute; left: 0; right: 0; bottom: 0; height: 3px; background: #A8703A; transform: scaleX(0); transform-origin: left; transition: transform .55s cubic-bezier(.7,0,.2,1) .08s; z-index: 2; }
        .sector-card:hover .sec-line { transform: scaleX(1); }

        .sec-arrow { opacity: 0; transform: translateX(-8px); transition: all .45s .1s; color: #A8703A; }
        .sector-card:hover .sec-arrow { opacity: 1; transform: none; }

        .sector-cta { position: relative; overflow: hidden; background: #0E2A4A; border-radius: 1.25rem; opacity: 0; transform: translateY(38px); transition: opacity .8s cubic-bezier(.2,.7,.2,1), transform .8s cubic-bezier(.2,.7,.2,1), box-shadow .45s; }
        .sector-cta.in-view { opacity: 1; transform: none; }
        .sector-cta:hover { transform: translateY(-8px); box-shadow: 0 30px 55px -20px rgba(14,42,74,.45); }
        .sector-cta .cta-arrow { transition: transform .4s cubic-bezier(.34,1.56,.64,1); }
        .sector-cta:hover .cta-arrow { transform: translateX(6px); }
        .sector-cta::after { content: ''; position: absolute; inset: 0; background-image: linear-gradient(to right, rgba(255,255,255,.05) 1px, transparent 1px); background-size: 26px 100%; animation: sectorCtaLines 14s linear infinite; pointer-events: none; }
        @keyframes sectorCtaLines { to { background-position: 26px 0; } }

        @media (prefers-reduced-motion: reduce) {
            .sector-rv, .sector-card, .sector-cta, .sector-beam, .sector-beam-h { animation: none; opacity: 1; transform: none; transition: none; }
        }
    </style>

    {{-- animated beams --}}
    <div class="absolute inset-0 pointer-events-none sector-bg" aria-hidden="true">
        <span class="sector-beam" style="left:180px; --dur:10s; --delay:0s"></span>
        <span class="sector-beam bronze" style="left:450px; --dur:13s; --delay:3s"></span>
        <span class="sector-beam" style="left:720px; --dur:11s; --delay:5.5s"></span>
        <span class="sector-beam bronze" style="left:1080px; --dur:12s; --delay:1.5s"></span>
        <span class="sector-beam" style="left:1350px; --dur:9.5s; --delay:7s"></span>
        <span class="sector-beam-h" style="top:220px; --dur:16s; --delay:2s"></span>
        <span class="sector-beam-h" style="top:520px; --dur:19s; --delay:8s"></span>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">

        {{-- Header --}}
        <div class="text-center max-w-2xl mx-auto mb-16 sector-rv" data-sector-rv>
            <span class="inline-flex items-center gap-2.5 text-bronze text-xs font-bold uppercase tracking-[0.22em] mb-5 justify-center">
                <span class="w-8 h-px bg-bronze"></span>
                Our Experience
                <span class="w-8 h-px bg-bronze"></span>
            </span>
            <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-navy leading-tight">
                Sector Experience &amp; Clients
            </h2>
            <div class="sector-underline"></div>
            <p class="mt-6 text-gray-600 text-lg leading-relaxed">
                ASYX has delivered solutions for a wide range of institutions across regulated and high-accountability environments.
            </p>
        </div>

        {{-- Cards grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">

            {{-- Energy & Utilities --}}
            <div class="sector-card p-7" data-sector-rv data-sector-stagger="0">
                <span class="sec-idx">01</span>
                <div class="sec-icon mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="sec-title font-heading text-lg font-bold text-navy mb-1.5 flex items-center gap-2">
                    Energy &amp; Utilities
                    <svg class="sec-arrow w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </h3>
                <p class="sec-title text-xs text-gray-400 mb-4 transition-colors">Powering national infrastructure</p>
                <div class="flex flex-wrap gap-1.5">
                    <span class="sector-chip">TANESCO</span><span class="sector-chip">EWURA</span><span class="sector-chip">TPDC</span><span class="sector-chip">GASCO</span>
                </div>
                <span class="sec-line"></span>
            </div>

            {{-- Transport & Aviation --}}
            <div class="sector-card p-7" data-sector-rv data-sector-stagger="1">
                <span class="sec-idx">02</span>
                <div class="sec-icon mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
                <h3 class="sec-title font-heading text-lg font-bold text-navy mb-1.5 flex items-center gap-2">
                    Transport &amp; Aviation
                    <svg class="sec-arrow w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </h3>
                <p class="sec-title text-xs text-gray-400 mb-4 transition-colors">Keeping the nation moving</p>
                <div class="flex flex-wrap gap-1.5">
                    <span class="sector-chip">TAA</span><span class="sector-chip">TCAA</span><span class="sector-chip">TANROADS</span><span class="sector-chip">LATRA</span><span class="sector-chip">Precision Air</span>
                </div>
                <span class="sec-line"></span>
            </div>

            {{-- Finance & Regulation --}}
            <div class="sector-card p-7" data-sector-rv data-sector-stagger="2">
                <span class="sec-idx">03</span>
                <div class="sec-icon mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="sec-title font-heading text-lg font-bold text-navy mb-1.5 flex items-center gap-2">
                    Finance &amp; Regulation
                    <svg class="sec-arrow w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </h3>
                <p class="sec-title text-xs text-gray-400 mb-4 transition-colors">Securing financial systems</p>
                <div class="flex flex-wrap gap-1.5">
                    <span class="sector-chip">BOT</span><span class="sector-chip">TRA</span><span class="sector-chip">NSSF</span><span class="sector-chip">PSSSF</span><span class="sector-chip">NHIF</span>
                </div>
                <span class="sec-line"></span>
            </div>

            {{-- Government & Public --}}
            <div class="sector-card p-7" data-sector-rv data-sector-stagger="3">
                <span class="sec-idx">04</span>
                <div class="sec-icon mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="sec-title font-heading text-lg font-bold text-navy mb-1.5 flex items-center gap-2">
                    Government &amp; Public
                    <svg class="sec-arrow w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </h3>
                <p class="sec-title text-xs text-gray-400 mb-4 transition-colors">Serving public institutions</p>
                <div class="flex flex-wrap gap-1.5">
                    <span class="sector-chip">Ministries</span><span class="sector-chip">Agencies</span><span class="sector-chip">Authorities</span><span class="sector-chip">LGAs</span>
                </div>
                <span class="sec-line"></span>
            </div>

            {{-- Education & Research --}}
            <div class="sector-card p-7" data-sector-rv data-sector-stagger="4">
                <span class="sec-idx">05</span>
                <div class="sec-icon mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <h3 class="sec-title font-heading text-lg font-bold text-navy mb-1.5 flex items-center gap-2">
                    Education &amp; Research
                    <svg class="sec-arrow w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </h3>
                <p class="sec-title text-xs text-gray-400 mb-4 transition-colors">Enabling knowledge systems</p>
                <div class="flex flex-wrap gap-1.5">
                    <span class="sector-chip">UDSM</span><span class="sector-chip">VETA</span><span class="sector-chip">NECTA</span><span class="sector-chip">NIMR</span><span class="sector-chip">TOSCI</span>
                </div>
                <span class="sec-line"></span>
            </div>

            {{-- CTA tile --}}
            <div class="sector-cta p-7 flex flex-col justify-between text-white" data-sector-rv data-sector-stagger="5">
                <div>
                    <div class="w-[52px] h-[52px] rounded-[14px] bg-white/10 border border-white/15 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold mb-2">Your Sector Next</h3>
                    <p class="text-sm text-white/65 leading-relaxed">
                        <span class="font-heading font-extrabold text-white"><span class="count" data-target="50">0</span>+</span> institutions trust ASYX. Let's discuss what we can build for yours.
                    </p>
                </div>
                <a href="#contact" class="inline-flex items-center gap-2 text-sm font-bold text-white mt-6 group">
                    <span class="border-b-2 border-bronze pb-0.5">Talk to Our Team</span>
                    <svg class="cta-arrow w-4 h-4 text-bronze" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
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
            var el = entry.target;
            var stagger = parseInt(el.dataset.sectorStagger || 0);
            setTimeout(function(){
                el.classList.add('in-view');
                runCounters(el);
            }, stagger * 110);
            observer.unobserve(el);
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });
    document.querySelectorAll('[data-sector-rv]').forEach(function(el){ observer.observe(el); });

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
