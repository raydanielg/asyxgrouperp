<section id="testimonials" class="py-20 lg:py-28 relative overflow-hidden testimonials-section bg-[#F8F9FB]">
    {{-- Ambient background --}}
    <div class="absolute inset-0 opacity-5 dot-grid-testimonials"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-purple/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-navy/10 rounded-full blur-3xl"></div>

    <div class="relative">
        {{-- Header --}}
        <div class="max-w-3xl mx-auto px-4 text-center mb-10 rv-testimonials" data-rv>
            <span class="chip-pop-testimonials inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-purple/10 text-purple text-xs font-bold uppercase tracking-wider mb-4" data-rv>
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M7.17 6C5.42 7.17 4 9.25 4 11.5c0 .41.06.8.14 1.18A3.5 3.5 0 117.5 17 3.48 3.48 0 014 13.5C4 9.86 6.13 6.9 9.03 5.3L7.17 6zm10 0C15.42 7.17 14 9.25 14 11.5c0 .41.06.8.14 1.18A3.5 3.5 0 1117.5 17a3.48 3.48 0 01-3.5-3.5c0-3.64 2.13-6.6 5.03-8.2L17.17 6z"/></svg>
                What Our Clients Say
            </span>
            <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-navy leading-tight">Trusted Across Africa</h2>
            <div class="title-underline-testimonials"></div>
            <p class="mt-6 text-gray-600 text-lg leading-relaxed">
                From governments to enterprises, organizations rely on ASYX for mission-critical technology.
            </p>
        </div>

        {{-- Marquee rows --}}
        <div class="tm-marquee rv-testimonials" data-rv>
            <div class="tm-track" id="tmRow1" style="--speed:42s"></div>
        </div>
        <div class="tm-marquee rv-testimonials" data-rv>
            <div class="tm-track rev" id="tmRow2" style="--speed:50s"></div>
        </div>
    </div>
</section>

<div class="h-40"></div>

@push('scripts')
<script>
(function() {
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    {{-- Testimonials data --}}
    var ROW1 = [
        { q:'ASYX transformed our fleet operations. The telematics platform gave us visibility we never thought possible.', n:'Daniel Mwakasege', r:'Fleet Operations Director', c:'National Logistics Co.', a:'https://randomuser.me/api/portraits/men/32.jpg', accent:'linear-gradient(135deg,#A8703A,#C81E3A)' },
        { q:'Their cybersecurity team hardened our infrastructure ahead of a national audit. Always two steps ahead.', n:'Amina Hassan', r:'Chief Information Officer', c:'Coastal Bank Group', a:'https://randomuser.me/api/portraits/women/44.jpg', accent:'linear-gradient(135deg,#5B2A6E,#1B3A5C)' },
        { q:'From network design to managed services, ASYX is the backbone of our digital transformation.', n:'Joseph Banda', r:'Head of ICT', c:'Ministry of Infrastructure', a:'https://randomuser.me/api/portraits/men/67.jpg', accent:'linear-gradient(135deg,#C81E3A,#5B2A6E)' },
        { q:'The custom software simplified processes we had struggled with for years. Built for our reality.', n:'Grace Njoroge', r:'Operations Manager', c:'AgriTrade East Africa', a:'https://randomuser.me/api/portraits/women/68.jpg', accent:'linear-gradient(135deg,#1B3A5C,#A8703A)' },
        { q:'Reliable, innovative, and genuinely invested in our success. Their experience shows in every deployment.', n:'Samuel Otieno', r:'Managing Director', c:'TransCity Holdings', a:'https://randomuser.me/api/portraits/men/85.jpg', accent:'linear-gradient(135deg,#A8703A,#5B2A6E)' }
    ];

    var ROW2 = [
        { q:'Deployment was seamless from day one. Their team anticipated issues before we even noticed them.', n:'Neema Joseph', r:'IT Manager', c:'Kilima Insurance', a:'https://randomuser.me/api/portraits/women/12.jpg', accent:'linear-gradient(135deg,#5B2A6E,#C81E3A)' },
        { q:'Response time is simply unmatched. Critical issues resolved in minutes, not days.', n:'Peter Kimaro', r:'Systems Administrator', c:'Umoja Telecom', a:'https://randomuser.me/api/portraits/men/22.jpg', accent:'linear-gradient(135deg,#A8703A,#1B3A5C)' },
        { q:'True partners, not just another vendor. They sit with us, plan with us, and deliver with us.', n:'Fatma Ally', r:'Project Lead', c:'Bahari Ports Authority', a:'https://randomuser.me/api/portraits/women/25.jpg', accent:'linear-gradient(135deg,#C81E3A,#A8703A)' },
        { q:'Our uptime improved within the first month of their managed services. The numbers speak.', n:'John Mushi', r:'Network Engineer', c:'SafiNet Solutions', a:'https://randomuser.me/api/portraits/men/41.jpg', accent:'linear-gradient(135deg,#1B3A5C,#5B2A6E)' },
        { q:'Security audits are finally stress-free. Compliance reports are ready before we ask.', n:'Rehema Said', r:'Compliance Officer', c:'Highland Microfinance', a:'https://randomuser.me/api/portraits/women/57.jpg', accent:'linear-gradient(135deg,#5B2A6E,#A8703A)' }
    ];

    var starSvg = '<svg class="star w-4 h-4 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';

    function card(t) {
        return '<div class="tm-card" style="--accent:' + t.accent + '">' +
            '<div class="tm-qicon">' +
                '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M7.17 6C5.42 7.17 4 9.25 4 11.5c0 .41.06.8.14 1.18A3.5 3.5 0 117.5 17 3.48 3.48 0 014 13.5C4 9.86 6.13 6.9 9.03 5.3L7.17 6zm10 0C15.42 7.17 14 9.25 14 11.5c0 .41.06.8.14 1.18A3.5 3.5 0 1117.5 17a3.48 3.48 0 01-3.5-3.5c0-3.64 2.13-6.6 5.03-8.2L17.17 6z"/></svg>' +
            '</div>' +
            '<div class="flex gap-1 mb-4">' + starSvg + starSvg + starSvg + starSvg + starSvg + '</div>' +
            '<p class="font-quote italic text-navy/85 text-[15.5px] leading-relaxed mb-5 pr-6">&ldquo;' + t.q + '&rdquo;</p>' +
            '<div class="flex items-center gap-3 pt-4 border-t border-navy/5">' +
                '<span class="tm-av"><img src="' + t.a + '" alt="' + t.n + '" loading="lazy"></span>' +
                '<div class="min-w-0">' +
                    '<p class="text-sm font-bold text-navy leading-tight truncate">' + t.n + '</p>' +
                    '<p class="text-xs text-gray-400 truncate">' + t.r + '</p>' +
                '</div>' +
                '<span class="ml-auto inline-flex items-center gap-1.5 text-[11px] font-bold text-navy/60 bg-navy/5 px-2.5 py-1 rounded-full whitespace-nowrap">' +
                    '<svg class="w-3 h-3 text-bronze" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/></svg>' +
                    t.c +
                '</span>' +
            '</div>' +
        '</div>';
    }

    {{-- Duplicate rows for seamless loop --}}
    var row1 = document.getElementById('tmRow1');
    var row2 = document.getElementById('tmRow2');
    if (row1) row1.innerHTML = ROW1.concat(ROW1).map(card).join('');
    if (row2) row2.innerHTML = ROW2.concat(ROW2).map(card).join('');

    {{-- Reveal on scroll --}}
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('in-view');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });
    document.querySelectorAll('[data-rv]').forEach(function(el){ observer.observe(el); });

    {{-- Pause row on card hover --}}
    if (!reduceMotion) {
        document.querySelectorAll('.tm-card').forEach(function(c) {
            c.addEventListener('mouseenter', function() {
                var track = c.closest('.tm-track');
                if (track) track.style.animationPlayState = 'paused';
            });
            c.addEventListener('mouseleave', function() {
                var track = c.closest('.tm-track');
                if (track) track.style.animationPlayState = 'running';
            });
        });
    }
})();
</script>
@endpush
