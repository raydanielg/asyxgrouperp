<style>
    .footer-bg { background-image: linear-gradient(to right, rgba(255,255,255,.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,.04) 1px, transparent 1px); background-size: 90px 90px; }
    .footer-beam { position: absolute; top: -160px; width: 1px; height: 160px; pointer-events: none; background: linear-gradient(to bottom, transparent, rgba(255,255,255,.28)); animation: footerBeamDown var(--dur,11s) linear infinite; animation-delay: var(--delay,0s); }
    .footer-beam.bronze { background: linear-gradient(to bottom, transparent, rgba(168,112,58,.7)); }
    .footer-beam-h { position: absolute; left: -220px; height: 1px; width: 220px; pointer-events: none; background: linear-gradient(to right, transparent, rgba(255,255,255,.22)); animation: footerBeamRight var(--dur,16s) linear infinite; animation-delay: var(--delay,0s); }
    @keyframes footerBeamDown { to { transform: translateY(calc(100% + 900px)); } }
    @keyframes footerBeamRight { to { transform: translateX(calc(100vw + 440px)); } }

    .footer-rv { opacity: 0; transform: translateY(36px); transition: opacity .9s cubic-bezier(.2,.7,.2,1), transform .9s cubic-bezier(.2,.7,.2,1); }
    .footer-rv.in-view { opacity: 1; transform: none; }

    .footer-cta { position: relative; overflow: hidden; }
    .footer-cta::after { content: ''; position: absolute; inset: 0; background-image: linear-gradient(to right, rgba(255,255,255,.06) 1px, transparent 1px); background-size: 28px 100%; animation: footerStripLines 16s linear infinite; pointer-events: none; }
    @keyframes footerStripLines { to { background-position: 28px 0; } }

    .footer-btn-cta { position: relative; overflow: hidden; background: #fff; color: #0E2A4A; transition: transform .3s, box-shadow .3s; }
    .footer-btn-cta:hover { transform: translateY(-2px); box-shadow: 0 16px 32px -12px rgba(0,0,0,.4); }
    .footer-btn-cta .btn-shine { position: absolute; top: 0; left: -80%; width: 50%; height: 100%; background: linear-gradient(105deg,transparent,rgba(168,112,58,.18),transparent); transform: skewX(-20deg); transition: left .6s ease; }
    .footer-btn-cta:hover .btn-shine { left: 140%; }
    .footer-btn-cta svg { transition: transform .35s cubic-bezier(.34,1.56,.64,1); }
    .footer-btn-cta:hover svg { transform: translateX(5px); }

    .footer-soc { position: relative; overflow: hidden; transition: transform .35s cubic-bezier(.34,1.56,.64,1), border-color .35s; }
    .footer-soc::before { content: ''; position: absolute; inset: 0; background: #A8703A; transform: translateY(101%); transition: transform .4s cubic-bezier(.7,0,.2,1); }
    .footer-soc:hover::before { transform: translateY(0); }
    .footer-soc:hover { transform: translateY(-4px); border-color: transparent; }
    .footer-soc svg { position: relative; z-index: 1; }

    .footer-link { position: relative; display: inline-flex; align-items: center; gap: .45rem; transition: color .3s, transform .3s; }
    .footer-link::before { content: ''; width: 0; height: 1.5px; background: #A8703A; border-radius: 99px; transition: width .35s cubic-bezier(.7,0,.2,1); }
    .footer-link:hover { color: #fff; transform: translateX(2px); }
    .footer-link:hover::before { width: 14px; }

    .footer-head { position: relative; display: inline-block; padding-bottom: .6rem; }
    .footer-head::after { content: ''; position: absolute; left: 0; bottom: 0; width: 22px; height: 2.5px; border-radius: 99px; background: #A8703A; transform: scaleX(0); transform-origin: left; transition: transform .8s cubic-bezier(.7,0,.2,1) .4s; }
    .footer-rv.in-view .footer-head::after { transform: scaleX(1); }

    .footer-c-row { transition: transform .3s; }
    .footer-c-row:hover { transform: translateX(4px); }
    .footer-c-icon { width: 34px; height: 34px; border-radius: 10px; background: rgba(168,112,58,.14); color: #A8703A; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all .4s cubic-bezier(.34,1.56,.64,1); }
    .footer-c-row:hover .footer-c-icon { background: #A8703A; color: #fff; transform: rotate(-8deg); }

    .footer-pulse { position: relative; }
    .footer-pulse::after { content: ''; position: absolute; inset: 0; border-radius: 99px; background: inherit; animation: footer-ping 2s cubic-bezier(0,0,.2,1) infinite; }
    @keyframes footer-ping { 75%, 100% { transform: scale(2.3); opacity: 0; } }

    .footer-watermark { font-family: 'Sora', sans-serif; font-weight: 800; line-height: .8; letter-spacing: -.02em; font-size: clamp(6rem, 18vw, 16rem); text-align: center; user-select: none; pointer-events: none; color: transparent; -webkit-text-stroke: 1.5px rgba(255,255,255,.07); transform: translateY(60px); opacity: 0; transition: transform 1.3s cubic-bezier(.16,1,.3,1), opacity 1.3s; }
    .footer-rv.in-view .footer-watermark, .footer-watermark.in-view { transform: translateY(18%); opacity: 1; }

    .footer-to-top { transition: transform .35s cubic-bezier(.34,1.56,.64,1), background .35s; }
    .footer-to-top:hover { transform: translateY(-5px); background: #A8703A; }
    .footer-to-top svg { transition: transform .35s; }
    .footer-to-top:hover svg { transform: translateY(-2px); }

    @media (prefers-reduced-motion: reduce) {
        .footer-rv, .footer-beam, .footer-beam-h, .footer-watermark, .footer-cta::after, .footer-soc, .footer-link, .footer-c-row, .footer-to-top { animation: none; opacity: 1; transform: none; transition: none; }
    }
</style>

<footer id="contact-info" class="relative bg-navy text-gray-300 overflow-hidden footer-bg">

    {{-- animated beams --}}
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <span class="footer-beam" style="left:160px; --dur:11s; --delay:0s"></span>
        <span class="footer-beam bronze" style="left:430px; --dur:14s; --delay:3s"></span>
        <span class="footer-beam" style="left:740px; --dur:12s; --delay:6s"></span>
        <span class="footer-beam bronze" style="left:1050px; --dur:13s; --delay:1.5s"></span>
        <span class="footer-beam" style="left:1330px; --dur:10s; --delay:7.5s"></span>
        <span class="footer-beam-h" style="top:240px; --dur:18s; --delay:2s"></span>
        <span class="footer-beam-h" style="top:520px; --dur:22s; --delay:9s"></span>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative pt-20">

        {{-- CTA strip --}}
        <div class="footer-cta footer-rv rounded-2xl bg-gradient-to-r from-bronze to-[#8a5a2e] px-8 sm:px-12 py-9 mb-16 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6" data-footer-rv>
            <div class="relative z-10">
                <h3 class="font-heading text-2xl sm:text-3xl font-extrabold text-white mb-1.5">Ready to build something mission-critical?</h3>
                <p class="text-sm text-white/80">Talk to our team today &mdash; response within one business hour.</p>
            </div>
            <a href="{{ route('contact') }}" class="footer-btn-cta relative z-10 inline-flex items-center gap-2 px-7 py-3.5 rounded-xl font-bold text-sm flex-shrink-0">
                <span class="btn-shine"></span>
                Start a Conversation
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>

        {{-- Main columns --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-10 mb-16 footer-rv" data-footer-rv>

            {{-- Brand --}}
            <div class="col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX Group Logo" class="h-14 w-auto object-contain drop-shadow-lg">
                </div>
                <p class="font-heading text-sm text-white font-bold mb-3">Creative, Innovative and Simplified Technological Solutions.</p>
                <p class="text-sm leading-relaxed max-w-sm mb-7 text-gray-400">
                    ASYX Group Limited &mdash; Established 2009. Empowering Africa with cutting-edge ICT infrastructure, smart technologies, and cybersecurity solutions.
                </p>
                <div class="flex items-center gap-3">
                    <a href="#" class="footer-soc w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center" aria-label="Twitter">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    <a href="#" class="footer-soc w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center" aria-label="Facebook">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                    </a>
                    <a href="#" class="footer-soc w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center" aria-label="Instagram">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="#" class="footer-soc w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center" aria-label="LinkedIn">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Links --}}
            <div>
                <h4 class="footer-head font-heading text-white font-bold text-sm mb-4">Links</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('about') }}" class="footer-link">About Us</a></li>
                    <li><a href="{{ route('hosting') }}" class="footer-link">Hosting</a></li>
                    <li><a href="{{ route('services') }}" class="footer-link">Services</a></li>
                    <li><a href="{{ route('careers') }}" class="footer-link">Careers</a></li>
                    <li><a href="{{ route('contact') }}" class="footer-link">Contact Us</a></li>
                </ul>
            </div>

            {{-- Services --}}
            <div>
                <h4 class="footer-head font-heading text-white font-bold text-sm mb-4">Services</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('services') }}" class="footer-link">Smart Technologies</a></li>
                    <li><a href="{{ route('services') }}" class="footer-link">Cyber Security</a></li>
                    <li><a href="{{ route('services') }}" class="footer-link">ICT Infrastructure</a></li>
                    <li><a href="{{ route('services') }}" class="footer-link">Software Solutions</a></li>
                    <li><a href="{{ route('services') }}" class="footer-link">Managed Services</a></li>
                </ul>
            </div>

            {{-- Address --}}
            <div class="col-span-2 md:col-span-1">
                <h4 class="footer-head font-heading text-white font-bold text-sm mb-4">Address</h4>
                <ul class="space-y-4 text-sm">
                    <li class="footer-c-row flex items-start gap-3">
                        <span class="footer-c-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <span class="text-gray-400 leading-relaxed">Tropical Center, 3rd Floor<br>New Bagamoyo Road<br>Plot No. 30/00 | House No. 301</span>
                    </li>
                    <li class="footer-c-row flex items-center gap-3">
                        <span class="footer-c-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </span>
                        <span class="text-gray-400">+255 22 000 0000</span>
                    </li>
                    <li class="footer-c-row flex items-center gap-3">
                        <span class="footer-c-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        <span class="text-gray-400">info@asyxgroup.co.tz</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Giant watermark --}}
    <div class="relative footer-rv overflow-hidden" data-footer-rv>
        <p class="footer-watermark">ASYX</p>
    </div>

    {{-- Bottom bar --}}
    <div class="relative border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-7 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-gray-500 flex items-center gap-2">
                <span class="relative flex w-2 h-2"><span class="footer-pulse w-2 h-2 rounded-full bg-emerald-400"></span></span>
                &copy; <span id="footer-year">{{ date('Y') }}</span> ASYX Group | All Rights Reserved
            </p>
            <p class="text-xs text-gray-500">Powered by Innovation &bull; Built with Technology</p>
            <div class="flex items-center gap-6 text-xs text-gray-500">
                <a href="#" class="footer-link">Privacy Policy</a>
                <a href="#" class="footer-link">Terms of Service</a>
                <button class="footer-to-top w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center" aria-label="Back to top" onclick="window.scrollTo({top:0,behavior:'smooth'})">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                </button>
            </div>
        </div>
    </div>
</footer>

@push('scripts')
<script>
(function() {
    document.getElementById('footer-year').textContent = new Date().getFullYear();

    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('in-view');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('[data-footer-rv]').forEach(function(el){ observer.observe(el); });
})();
</script>
@endpush
