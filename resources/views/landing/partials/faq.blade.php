<section id="faq" class="py-20 lg:py-28 relative overflow-hidden bg-white">

    <div class="absolute -top-24 -right-24 w-96 h-96 bg-purple/10 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-bronze/10 rounded-full blur-3xl"></div>
    <div class="absolute top-28 right-10 w-36 h-36 dot-patch opacity-30 rounded-2xl hidden xl:block"></div>

    <style>
        .faq-rv { opacity: 0; transform: translateY(40px); transition: opacity .9s cubic-bezier(.2,.7,.2,1), transform .9s cubic-bezier(.2,.7,.2,1); }
        .faq-rv.in-view { opacity: 1; transform: none; }
        .faq-chip { opacity: 0; transform: scale(.6); transition: opacity .6s, transform .6s cubic-bezier(.34,1.56,.64,1); }
        .faq-rv.in-view .faq-chip, .faq-rv.in-view .faq-chip { opacity: 1; transform: scale(1); }
        .faq-underline { width: 0; height: 5px; border-radius: 99px; background: linear-gradient(90deg,#A8703A,#C0344B,#6B3FA0); margin-top: 14px; transition: width 1.1s cubic-bezier(.7,0,.2,1) .35s; }
        .faq-rv.in-view .faq-underline { width: 110px; }

        .faq-item { position: relative; border-radius: 1rem; background: #F5F6F8; border: 1.5px solid transparent; transition: background .45s, border-color .45s, box-shadow .45s, transform .45s cubic-bezier(.2,.7,.2,1); opacity: 0; transform: translateY(34px); }
        .faq-item.in-view { opacity: 1; transform: none; }
        .faq-item:hover { transform: translateY(-3px); }

        .faq-item::before { content: ''; position: absolute; left: 0; top: 18px; bottom: 18px; width: 4px; border-radius: 0 8px 8px 0; background: linear-gradient(180deg,#A8703A,#C0344B); transform: scaleY(0); transform-origin: top; transition: transform .5s cubic-bezier(.7,0,.2,1); }
        .faq-item.active::before { transform: scaleY(1); }

        .faq-item.active { background: #fff; border-color: rgba(168,112,58,.25); box-shadow: 0 24px 48px -18px rgba(14,42,74,.18); }

        .faq-num { transition: all .45s cubic-bezier(.34,1.56,.64,1); }
        .faq-item.active .faq-num { background: linear-gradient(135deg,#A8703A,#C0344B); color: #fff; transform: rotate(-6deg) scale(1.08); box-shadow: 0 8px 18px -6px rgba(192,52,75,.45); }

        .faq-q { transition: color .35s; }
        .faq-item.active .faq-q { color: #A8703A; }

        .faq-icon { position: relative; width: 34px; height: 34px; border-radius: 99px; border: 1.5px solid rgba(14,42,74,.18); flex-shrink: 0; transition: all .45s cubic-bezier(.34,1.56,.64,1); }
        .faq-icon::before, .faq-icon::after { content: ''; position: absolute; top: 50%; left: 50%; background: #0E2A4A; border-radius: 99px; transition: all .45s cubic-bezier(.34,1.56,.64,1); }
        .faq-icon::before { width: 14px; height: 2.5px; transform: translate(-50%,-50%); }
        .faq-icon::after { width: 2.5px; height: 14px; transform: translate(-50%,-50%); }
        .faq-item.active .faq-icon { background: linear-gradient(135deg,#A8703A,#C0344B); border-color: transparent; transform: rotate(180deg); }
        .faq-item.active .faq-icon::before, .faq-item.active .faq-icon::after { background: #fff; }
        .faq-item.active .faq-icon::after { transform: translate(-50%,-50%) rotate(90deg); }

        .faq-body { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .55s cubic-bezier(.4,0,.2,1); }
        .faq-item.active .faq-body { grid-template-rows: 1fr; }
        .faq-body-inner { overflow: hidden; }
        .faq-answer { opacity: 0; transform: translateY(-8px); transition: opacity .4s ease .15s, transform .4s ease .15s; }
        .faq-item.active .faq-answer { opacity: 1; transform: none; }

        .help-card { position: relative; overflow: hidden; }
        .help-card::after { content: ''; position: absolute; top: -40%; right: -30%; width: 70%; height: 120%; background: radial-gradient(circle, rgba(255,255,255,.14), transparent 65%); pointer-events: none; }
        .pulse-dot { position: relative; }
        .pulse-dot::after { content: ''; position: absolute; inset: 0; border-radius: 99px; background: inherit; animation: faq-ping 1.8s cubic-bezier(0,0,.2,1) infinite; }
        @keyframes faq-ping { 75%, 100% { transform: scale(2.4); opacity: 0; } }

        .btn-cta { position: relative; overflow: hidden; transition: transform .3s, box-shadow .3s; }
        .btn-cta:hover { transform: translateY(-2px); box-shadow: 0 14px 30px -10px rgba(0,0,0,.35); }
        .btn-cta .btn-shine { position: absolute; top: 0; left: -80%; width: 50%; height: 100%; background: linear-gradient(105deg,transparent,rgba(255,255,255,.3),transparent); transform: skewX(-20deg); transition: left .6s ease; }
        .btn-cta:hover .btn-shine { left: 140%; }

        .dot-patch { background-image: radial-gradient(rgba(168,112,58,.45) 2px, transparent 2px); background-size: 16px 16px; }

        @media (prefers-reduced-motion: reduce) {
            .faq-rv, .faq-item, .faq-rv.in-view, .faq-item.in-view { animation: none; opacity: 1; transform: none; transition: none; }
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] gap-14 lg:gap-20 items-start">

            {{-- LEFT: sticky intro + help card --}}
            <div class="lg:sticky lg:top-24 faq-rv" data-faq-rv>
                <span class="faq-chip inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    FAQ
                </span>

                <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-navy leading-tight">
                    Frequently Asked<br>Questions
                </h2>
                <div class="faq-underline"></div>

                <p class="mt-6 text-gray-600 text-lg leading-relaxed">
                    Everything you need to know about ASYX Group's services and solutions. Can't find what you're looking for?
                </p>

                {{-- Help card --}}
                <div class="help-card mt-8 rounded-2xl bg-gradient-to-br from-navy to-[#1a4a7a] p-7 text-white shadow-2xl shadow-navy/25">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-11 h-11 rounded-xl cta-gradient flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636a9 9 0 010 12.728m-3.536-3.536a4 4 0 000-5.656m-7.072 8.485L3 21l3.343-4.657A7.975 7.975 0 015 12a8 8 0 1116 0 7.975 7.975 0 01-1.343 4.343"/></svg>
                        </span>
                        <div>
                            <p class="font-heading font-bold">Still have questions?</p>
                            <p class="text-xs text-white/70 flex items-center gap-1.5 mt-0.5">
                                <span class="relative flex w-2 h-2"><span class="pulse-dot w-2 h-2 rounded-full bg-emerald-400"></span></span>
                                Our team is online 24/7
                            </p>
                        </div>
                    </div>
                    <p class="text-sm text-white/75 leading-relaxed mb-5">
                        Talk to our mission-critical support team and get answers within minutes.
                    </p>
                    <a href="#contact" class="btn-cta cta-gradient inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold">
                        <span class="btn-shine"></span>
                        Contact Our Team
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>

            {{-- RIGHT: accordion --}}
            <div class="space-y-4" id="faqList">

                <div class="faq-item" data-faq-rv data-faq-stagger="0">
                    <button class="faq-toggle w-full flex items-center gap-4 px-6 py-5 text-left">
                        <span class="faq-num w-10 h-10 rounded-xl bg-navy/5 text-navy font-heading font-extrabold text-sm flex items-center justify-center flex-shrink-0">01</span>
                        <span class="faq-q flex-1 font-heading text-base font-bold text-navy">What services does ASYX Group offer?</span>
                        <span class="faq-icon"></span>
                    </button>
                    <div class="faq-body">
                        <div class="faq-body-inner">
                            <div class="faq-answer px-6 pb-6 pl-[4.75rem]">
                                <p class="text-gray-600 text-sm leading-relaxed">ASYX Group offers smart technologies, telematics solutions, cybersecurity, software solutions, ICT infrastructure, hardware distribution, technical support &amp; managed services, labour outsourcing, ICT training, and systems integration &amp; partnerships.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-faq-rv data-faq-stagger="1">
                    <button class="faq-toggle w-full flex items-center gap-4 px-6 py-5 text-left">
                        <span class="faq-num w-10 h-10 rounded-xl bg-navy/5 text-navy font-heading font-extrabold text-sm flex items-center justify-center flex-shrink-0">02</span>
                        <span class="faq-q flex-1 font-heading text-base font-bold text-navy">How long has ASYX Group been operating?</span>
                        <span class="faq-icon"></span>
                    </button>
                    <div class="faq-body">
                        <div class="faq-body-inner">
                            <div class="faq-answer px-6 pb-6 pl-[4.75rem]">
                                <p class="text-gray-600 text-sm leading-relaxed">ASYX Group was established in 2009 and has over 16+ years of experience delivering mission-critical technology solutions across Tanzania and Africa.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-faq-rv data-faq-stagger="2">
                    <button class="faq-toggle w-full flex items-center gap-4 px-6 py-5 text-left">
                        <span class="faq-num w-10 h-10 rounded-xl bg-navy/5 text-navy font-heading font-extrabold text-sm flex items-center justify-center flex-shrink-0">03</span>
                        <span class="faq-q flex-1 font-heading text-base font-bold text-navy">Which sectors does ASYX Group serve?</span>
                        <span class="faq-icon"></span>
                    </button>
                    <div class="faq-body">
                        <div class="faq-body-inner">
                            <div class="faq-answer px-6 pb-6 pl-[4.75rem]">
                                <p class="text-gray-600 text-sm leading-relaxed">We serve energy &amp; utilities (TANESCO, EWURA), transport &amp; aviation (TAA, TCAA, Precision Air), finance &amp; regulation (BOT, TRA, NSSF), government &amp; public institutions, and education &amp; research sectors.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-faq-rv data-faq-stagger="3">
                    <button class="faq-toggle w-full flex items-center gap-4 px-6 py-5 text-left">
                        <span class="faq-num w-10 h-10 rounded-xl bg-navy/5 text-navy font-heading font-extrabold text-sm flex items-center justify-center flex-shrink-0">04</span>
                        <span class="faq-q flex-1 font-heading text-base font-bold text-navy">Does ASYX Group provide 24/7 support?</span>
                        <span class="faq-icon"></span>
                    </button>
                    <div class="faq-body">
                        <div class="faq-body-inner">
                            <div class="faq-answer px-6 pb-6 pl-[4.75rem]">
                                <p class="text-gray-600 text-sm leading-relaxed">Yes. We provide 24/7 mission-critical support with guaranteed response times. Our support team is available round-the-clock to ensure zero downtime for your critical systems.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-faq-rv data-faq-stagger="4">
                    <button class="faq-toggle w-full flex items-center gap-4 px-6 py-5 text-left">
                        <span class="faq-num w-10 h-10 rounded-xl bg-navy/5 text-navy font-heading font-extrabold text-sm flex items-center justify-center flex-shrink-0">05</span>
                        <span class="faq-q flex-1 font-heading text-base font-bold text-navy">Can ASYX Group handle government-grade security requirements?</span>
                        <span class="faq-icon"></span>
                    </button>
                    <div class="faq-body">
                        <div class="faq-body-inner">
                            <div class="faq-answer px-6 pb-6 pl-[4.75rem]">
                                <p class="text-gray-600 text-sm leading-relaxed">Absolutely. We have extensive experience working with government bodies and parastatals, with security and compliance standards built for public-sector and regulated enterprise requirements.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-faq-rv data-faq-stagger="5">
                    <button class="faq-toggle w-full flex items-center gap-4 px-6 py-5 text-left">
                        <span class="faq-num w-10 h-10 rounded-xl bg-navy/5 text-navy font-heading font-extrabold text-sm flex items-center justify-center flex-shrink-0">06</span>
                        <span class="faq-q flex-1 font-heading text-base font-bold text-navy">Where is ASYX Group located?</span>
                        <span class="faq-icon"></span>
                    </button>
                    <div class="faq-body">
                        <div class="faq-body-inner">
                            <div class="faq-answer px-6 pb-6 pl-[4.75rem]">
                                <p class="text-gray-600 text-sm leading-relaxed">Our office is located at Tropical Center, 3rd Floor, New Bagamoyo Road, Plot No. 30/00, House No. 301, Dar es Salaam, Tanzania.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function() {
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (!entry.isIntersecting) return;
            var el = entry.target;
            var stagger = parseInt(el.dataset.faqStagger || 0);
            setTimeout(function(){ el.classList.add('in-view'); }, stagger * 110);
            observer.unobserve(el);
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });
    document.querySelectorAll('[data-faq-rv]').forEach(function(el){ observer.observe(el); });

    var items = document.querySelectorAll('.faq-item');
    items.forEach(function(item) {
        item.querySelector('.faq-toggle').addEventListener('click', function() {
            var isOpen = item.classList.contains('active');
            items.forEach(function(i){ i.classList.remove('active'); });
            if (!isOpen) item.classList.add('active');
        });
    });

    if (items.length) items[0].classList.add('active');
})();
</script>
@endpush
