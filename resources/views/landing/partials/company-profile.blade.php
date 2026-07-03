<section id="company-profile" class="py-20 lg:py-28 relative overflow-hidden profile-animated">

    {{-- Animated dot grid --}}
    <div class="absolute inset-0 opacity-5 dot-grid-drift"></div>

    {{-- Floating orbs --}}
    <div class="orb w-96 h-96 bg-navy -top-20 -left-20"></div>
    <div class="orb orb-2 w-96 h-96 bg-purple -bottom-20 -right-20"></div>
    <div class="orb orb-3 w-72 h-72 bg-crimson top-1/3 right-1/4"></div>

    {{-- Floating tech particles --}}
    <div id="profileParticles" class="absolute inset-0 overflow-hidden pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">

        {{-- Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16 rv-profile" data-rv>
            <span class="chip-pop inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-navy/10 text-navy text-xs font-bold uppercase tracking-wider mb-4" data-rv>
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.238A7.995 7.995 0 0010 16c.5 0 1-.037 1.484-.109a1 1 0 01.89.89 11.115 11.115 0 00-.25 3.762 1 1 0 01-1.115.748 8.967 8.967 0 01-4.27-1.458 1 1 0 01-.633-1.54l.748-1.475z"/></svg>
                ASYX Group Limited
            </span>
            <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black leading-tight shimmer-title">
                Established 2009
            </h2>
            <div class="title-underline"></div>
            <p class="mt-6 text-gray-600 text-lg leading-relaxed">
                We deliver smart technologies, ICT infrastructure, cybersecurity, telematics, software solutions, and managed services for mission-critical environments across Africa.
            </p>
        </div>

        {{-- 16+ Years Badge with orbit --}}
        <div class="text-center mb-20 rv-profile" data-rv>
            <div class="badge-wrap inline-block">
                <div class="badge-ring"></div>
                <div class="badge-ring2"></div>
                <div class="badge-orbit-dot"></div>
                <div class="badge-core inline-flex flex-col items-center justify-center w-40 h-40 rounded-full bg-gradient-to-br from-navy to-navy/80">
                    <p class="font-heading text-5xl sm:text-6xl font-black text-white"><span id="yearsCounter">0</span>+</p>
                    <p class="text-sm text-white/80 mt-1">Years</p>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-8">Leading technological innovation across Africa</p>
        </div>

        {{-- Mission & Vision --}}
        <div class="space-y-10 mb-20" style="perspective:1400px;">

            {{-- Mission --}}
            <div class="mv-card-tilt group relative overflow-hidden rounded-2xl bg-white shadow-xl" data-rv>
                <div class="grid md:grid-cols-2 h-full">
                    <div class="relative h-64 md:h-auto overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=900&q=80" alt="Our Mission" class="kenburns absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-tr from-navy/50 via-navy/10 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 flex items-center gap-2 bg-white/15 backdrop-blur px-3 py-1.5 rounded-full">
                            <span class="w-2 h-2 rounded-full bg-bronze animate-ping"></span>
                            <span class="text-white text-xs font-semibold pl-1">Mission Critical</span>
                        </div>
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

            {{-- Vision --}}
            <div class="mv-card-tilt group relative overflow-hidden rounded-2xl bg-white shadow-xl" data-rv>
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
                        <img src="https://images.unsplash.com/photo-1451187580459-9546f8937745?w=900&q=80" alt="Our Vision" class="kenburns absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-tl from-purple/50 via-purple/10 to-transparent"></div>
                        <div class="absolute bottom-4 right-4 flex items-center gap-2 bg-white/15 backdrop-blur px-3 py-1.5 rounded-full">
                            <span class="text-white text-xs font-semibold">Africa &amp; Beyond</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Marquee strip --}}
        <div class="marquee-strip mb-20 rv-profile" data-rv>
            <div class="marquee-track font-heading font-bold text-navy/15 text-3xl sm:text-4xl uppercase">
                <span>Smart Technologies</span><span class="text-bronze/25">•</span>
                <span>ICT Infrastructure</span><span class="text-purple/25">•</span>
                <span>Cybersecurity</span><span class="text-crimson/25">•</span>
                <span>Telematics</span><span class="text-bronze/25">•</span>
                <span>Software Solutions</span><span class="text-purple/25">•</span>
                <span>Managed Services</span><span class="text-crimson/25">•</span>
                <span>Smart Technologies</span><span class="text-bronze/25">•</span>
                <span>ICT Infrastructure</span><span class="text-purple/25">•</span>
                <span>Cybersecurity</span><span class="text-crimson/25">•</span>
                <span>Telematics</span><span class="text-bronze/25">•</span>
                <span>Software Solutions</span><span class="text-purple/25">•</span>
                <span>Managed Services</span><span class="text-crimson/25">•</span>
            </div>
        </div>

        {{-- Core Values --}}
        <div class="text-center mb-12 rv-profile" data-rv>
            <span class="chip-pop inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-bronze/10 text-bronze text-xs font-bold uppercase tracking-wider mb-4" data-rv>
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                Our Core Values
            </span>
            <h3 class="font-heading text-2xl sm:text-3xl font-black text-navy">The Principles That Guide Everything We Do</h3>
        </div>

        {{-- Core Values Showcase --}}
        <div id="valuesShowcase" class="rv-profile relative rounded-3xl overflow-hidden bg-navy shadow-2xl shadow-navy/30" data-rv>
            <div class="grid lg:grid-cols-2 min-h-[480px] lg:min-h-[520px]">

                {{-- Left: Image stack --}}
                <div class="relative h-72 sm:h-80 lg:h-auto overflow-hidden">
                    <div class="vs-img active" style="background-image:url('https://images.unsplash.com/photo-1560250097-0b93528c311a?w=1000&q=80')"></div>
                    <div class="vs-img" style="background-image:url('https://images.unsplash.com/photo-1573164713714-d95e436ab8d6?w=1000&q=80')"></div>
                    <div class="vs-img" style="background-image:url('https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1000&q=80')"></div>
                    <div class="vs-img" style="background-image:url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1000&q=80')"></div>
                    <div class="vs-img" style="background-image:url('https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=1000&q=80')"></div>

                    <div id="vsVeil" class="absolute inset-0 transition-colors duration-700 bg-navy/40 mix-blend-multiply"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-transparent to-navy/70 hidden lg:block"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-navy/70 to-transparent lg:hidden"></div>

                    <div id="vsGhostNum" class="absolute -bottom-6 left-4 font-heading font-black text-white/10 text-[9rem] sm:text-[11rem] leading-none select-none transition-all duration-700">01</div>

                    <div id="vsIconChip" class="absolute top-6 left-6 w-14 h-14 rounded-2xl cta-gradient flex items-center justify-center text-white shadow-lg shadow-black/30 transition-transform duration-700">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                </div>

                {{-- Right: Text + nav --}}
                <div class="relative flex flex-col justify-center p-8 sm:p-10 lg:p-14 text-white">
                    <div id="vsGlow" class="absolute -top-16 -right-16 w-64 h-64 rounded-full blur-3xl opacity-30 transition-colors duration-700 bg-bronze"></div>

                    <div class="relative">
                        <div class="flex items-center gap-3 mb-4">
                            <span id="vsNum" class="font-heading font-black text-lg px-3 py-1 rounded-lg bg-white/10 backdrop-blur transition-all duration-500">01 / 05</span>
                            <span id="vsTag" class="text-xs font-bold uppercase tracking-widest text-bronze transition-colors duration-500">Core Value</span>
                        </div>

                        <div class="vs-text-wrap overflow-hidden">
                            <h4 id="vsTitle" class="vs-anim font-heading text-3xl sm:text-4xl lg:text-5xl font-black mb-4">Professionalism</h4>
                        </div>
                        <div class="vs-text-wrap overflow-hidden">
                            <p id="vsDesc" class="vs-anim text-white/75 text-base sm:text-lg leading-relaxed max-w-md mb-8">We deliver every project with competence, accountability, and an uncompromising standard of excellence.</p>
                        </div>

                        <div class="flex flex-wrap gap-2 mb-6">
                            <button class="vs-pill active" data-vs="0">Professionalism</button>
                            <button class="vs-pill" data-vs="1">Integrity</button>
                            <button class="vs-pill" data-vs="2">Innovation</button>
                            <button class="vs-pill" data-vs="3">Teamwork</button>
                            <button class="vs-pill" data-vs="4">Compliance</button>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-1.5 rounded-full bg-white/15 overflow-hidden max-w-xs">
                                <div id="vsProgress" class="h-full rounded-full cta-gradient" style="width:0%"></div>
                            </div>
                            <div class="flex gap-2">
                                <button id="vsPrev" aria-label="Previous value" class="w-10 h-10 rounded-full border border-white/25 flex items-center justify-center hover:bg-white/10 active:scale-90 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <button id="vsNext" aria-label="Next value" class="w-10 h-10 rounded-full border border-white/25 flex items-center justify-center hover:bg-white/10 active:scale-90 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </button>
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
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    {{-- Floating tech particles --}}
    if (!reduceMotion) {
        var symbols = ['</>','{ }','01','#','∆','⚡','◆','101','&&','=>'];
        var colors = ['#A8703A','#5B2A6E','#C81E3A','#1B3A5C'];
        var box = document.getElementById('profileParticles');
        if (box) {
            for (var i = 0; i < 16; i++) {
                var p = document.createElement('span');
                p.className = 'profile-particle';
                p.textContent = symbols[i % symbols.length];
                p.style.left = (Math.random() * 100) + '%';
                p.style.bottom = '-40px';
                p.style.fontSize = (12 + Math.random() * 16) + 'px';
                p.style.color = colors[i % colors.length];
                p.style.animationDuration = (12 + Math.random() * 14) + 's';
                p.style.animationDelay = (Math.random() * 14) + 's';
                box.appendChild(p);
            }
        }
    }

    {{-- Reveal on scroll (with stagger) --}}
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (!entry.isIntersecting) return;
            var el = entry.target;
            var stagger = parseInt(el.dataset.stagger || 0);
            setTimeout(function(){ el.classList.add('in-view'); }, stagger * 130);
            observer.unobserve(el);
        });
    }, { threshold: 0.18, rootMargin: '0px 0px -60px 0px' });

    document.querySelectorAll('.rv-profile').forEach(function(el){ observer.observe(el); });

    {{-- 16+ Counter --}}
    var counterEl = document.getElementById('yearsCounter');
    if (counterEl) {
        var counted = false;
        var counterObs = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !counted) {
                    counted = true;
                    var target = 16, dur = 1600, start = null;
                    function step(ts) {
                        if (!start) start = ts;
                        var prog = Math.min((ts - start) / dur, 1);
                        var eased = 1 - Math.pow(1 - prog, 3);
                        counterEl.textContent = Math.round(eased * target);
                        if (prog < 1) requestAnimationFrame(step);
                    }
                    requestAnimationFrame(step);
                }
            });
        }, { threshold: 0.5 });
        counterObs.observe(counterEl);
    }

    {{-- Core Values Showcase --}}
    var VALUES = [
        { title:'Professionalism', desc:'We deliver every project with competence, accountability, and an uncompromising standard of excellence.', tag:'Core Value · 01', color:'#A8703A', veil:'rgba(27,58,92,.45)',
          icon:'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
        { title:'Integrity', desc:'We are honest, transparent, and ethical in every engagement — with clients, partners, and each other.', tag:'Core Value · 02', color:'#5B2A6E', veil:'rgba(91,42,110,.45)',
          icon:'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' },
        { title:'Innovation', desc:'We embrace creativity and continuous improvement, building solutions shaped for Africa\u2019s realities.', tag:'Core Value · 03', color:'#C81E3A', veil:'rgba(200,30,58,.45)',
          icon:'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z' },
        { title:'Teamwork', desc:'We collaborate across disciplines and borders, because shared success is the only success that lasts.', tag:'Core Value · 04', color:'#1B3A5C', veil:'rgba(27,58,92,.45)',
          icon:'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
        { title:'Compliance', desc:'We operate within laws, standards, and strong governance — earning trust in mission-critical environments.', tag:'Core Value · 05', color:'#A8703A', veil:'rgba(168,112,58,.45)',
          icon:'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z' }
    ];

    var showcase = document.getElementById('valuesShowcase');
    if (showcase) {
        var imgs = showcase.querySelectorAll('.vs-img');
        var pills = showcase.querySelectorAll('.vs-pill');
        var elTitle = document.getElementById('vsTitle');
        var elDesc = document.getElementById('vsDesc');
        var elNum = document.getElementById('vsNum');
        var elTag = document.getElementById('vsTag');
        var elGhost = document.getElementById('vsGhostNum');
        var elVeil = document.getElementById('vsVeil');
        var elGlow = document.getElementById('vsGlow');
        var elChip = document.getElementById('vsIconChip');
        var elProg = document.getElementById('vsProgress');
        var textSide = showcase.querySelector('.relative.flex');

        var current = 0, DURATION = 5200, elapsed = 0, last = null, paused = false, switching = false;

        function pad(n){ return (n<10?'0':'')+n; }

        function goTo(next) {
            if (switching || next === current) return;
            switching = true;
            var idx = (next + VALUES.length) % VALUES.length;
            var v = VALUES[idx];

            {{-- image crossfade --}}
            imgs[current].classList.add('leaving');
            imgs[current].classList.remove('active');
            imgs[idx].classList.add('active');
            setTimeout(function(){ imgs.forEach(function(im,i){ if(i!==idx) im.classList.remove('leaving'); }); }, 1100);

            {{-- text out --}}
            textSide.classList.add('vs-out');
            elGhost.classList.add('switching');
            elChip.classList.add('switching');

            setTimeout(function() {
                elTitle.textContent = v.title;
                elDesc.textContent = v.desc;
                elNum.textContent = pad(idx+1) + ' / ' + pad(VALUES.length);
                elTag.textContent = v.tag;
                elTag.style.color = v.color;
                elGhost.textContent = pad(idx+1);
                elVeil.style.backgroundColor = v.veil;
                elGlow.style.backgroundColor = v.color;
                elChip.querySelector('path').setAttribute('d', v.icon);

                textSide.classList.remove('vs-out');
                textSide.classList.add('vs-in');
                elGhost.classList.remove('switching');
                elChip.classList.remove('switching');

                pills.forEach(function(p,i){ p.classList.toggle('active', i===idx); });

                setTimeout(function(){ textSide.classList.remove('vs-in'); switching = false; }, 650);
            }, 360);

            current = idx;
            elapsed = 0;
        }

        pills.forEach(function(p){
            p.addEventListener('click', function(){ goTo(parseInt(p.dataset.vs)); });
        });
        document.getElementById('vsNext').addEventListener('click', function(){ goTo(current+1); });
        document.getElementById('vsPrev').addEventListener('click', function(){ goTo(current-1); });

        var hovered = false, offscreen = false;
        function updatePaused(){ paused = hovered || offscreen; }
        showcase.addEventListener('mouseenter', function(){ hovered = true; updatePaused(); });
        showcase.addEventListener('mouseleave', function(){ hovered = false; updatePaused(); });
        var visObs = new IntersectionObserver(function(en){
            en.forEach(function(e){ offscreen = !e.isIntersecting; updatePaused(); });
        }, {threshold:.15});
        visObs.observe(showcase);

        function tick(ts) {
            if (last === null) last = ts;
            var dt = ts - last; last = ts;
            if (!paused && !switching && !reduceMotion) {
                elapsed += dt;
                elProg.style.width = Math.min(elapsed / DURATION * 100, 100) + '%';
                if (elapsed >= DURATION) goTo(current + 1);
            }
            requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
    }

    {{-- 3D tilt on Mission/Vision cards --}}
    if (!reduceMotion && matchMedia('(pointer:fine)').matches) {
        document.querySelectorAll('.mv-card-tilt').forEach(function(card) {
            card.addEventListener('mousemove', function(e) {
                var r = card.getBoundingClientRect();
                var x = (e.clientX - r.left) / r.width - 0.5;
                var y = (e.clientY - r.top) / r.height - 0.5;
                card.style.transform = 'rotateY(' + (x * 6) + 'deg) rotateX(' + (-y * 6) + 'deg) translateY(-4px)';
                card.style.transition = 'transform .1s';
            });
            card.addEventListener('mouseleave', function() {
                card.style.transition = 'transform .6s cubic-bezier(.2,.7,.2,1)';
                card.style.transform = 'none';
            });
        });
    }
})();
</script>
@endpush
