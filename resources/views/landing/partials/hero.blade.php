<section id="home" class="relative min-h-screen flex items-center pt-28 pb-20 overflow-hidden">

    {{-- Rotating background images --}}
    <div class="absolute inset-0">
        @php
            $heroImages = [
                'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=1920&q=80',
                'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1920&q=80',
                'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=1920&q=80',
                'https://images.unsplash.com/photo-1561070791-2526d30994da?w=1920&q=80',
                'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=1920&q=80',
            ];
        @endphp
        @foreach ($heroImages as $img)
            <img src="{{ $img }}" alt="Technology infrastructure" class="hero-bg-image absolute inset-0 w-full h-full object-cover {{ $loop->first ? 'opacity-100' : 'opacity-0' }}" data-index="{{ $loop->index }}">
        @endforeach
    </div>

    {{-- Gradient overlay - dark on left fading to transparent on right --}}
    <div class="absolute inset-0 bg-gradient-to-r from-navy via-navy/80 to-navy/40"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-navy/80 via-transparent to-navy/30"></div>

    {{-- Subtle dot pattern --}}
    <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(rgba(168,112,58,0.4) 1px, transparent 1px); background-size: 32px 32px;"></div>

    {{-- Decorative glows --}}
    <div class="absolute top-20 right-10 w-72 h-72 bg-purple/20 rounded-full blur-3xl animate-float"></div>
    <div class="absolute bottom-10 left-10 w-96 h-96 bg-crimson/10 rounded-full blur-3xl"></div>

    {{-- Content - text on left with backdrop --}}
    <div class="relative z-20 max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="flex justify-start">
            <div class="w-full lg:max-w-2xl text-left scroll-reveal relative">
                {{-- Backdrop behind text for readability --}}
                <div class="absolute -inset-6 rounded-3xl glass-light"></div>

                <div class="relative">
                    {{-- Badge --}}
                    <a href="{{ route('about') }}" class="inline-flex justify-between items-center py-1 px-1 pr-4 mb-7 text-sm text-white bg-white/10 backdrop-blur-sm rounded-full hover:bg-white/15 transition-colors">
                        <span class="text-xs bg-bronze rounded-full text-white px-4 py-1.5 mr-3 font-bold">Since 2009</span>
                        <span class="text-sm font-medium">16+ Years of Excellence in Tanzania</span>
                        <svg class="ml-2 w-5 h-5 text-white/70" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    </a>

                    <h1 class="mb-4 text-4xl font-extrabold tracking-tight leading-tight text-white md:text-5xl lg:text-6xl drop-shadow-lg font-heading">
                        We Deliver<br>
                        <span class="text-gradient" id="hero-rotating-text">Smart Technology</span>
                    </h1>
                    <p class="mb-8 text-lg font-normal text-gray-200 max-w-lg drop-shadow leading-relaxed">
                        16+ years powering Tanzania's mission-critical systems. From power utilities to public transport to financial regulation — ASYX Group is the trusted technology partner behind the nation's most critical infrastructure.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 mb-10">
                        <a href="{{ route('services') }}" class="btn-primary inline-flex justify-center items-center gap-2 py-4 px-8 text-lg">
                            Explore Our Services
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <a href="{{ route('contact') }}" class="inline-flex justify-center items-center gap-2 py-3.5 px-6 text-base font-bold text-white rounded-lg border-2 border-white/30 hover:bg-white/10 backdrop-blur-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            Talk to an Expert
                        </a>
                    </div>

                    {{-- Trust indicators --}}
                    <div class="flex flex-wrap items-center gap-6 text-gray-300">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-sm font-medium">Government &amp; Parastatal Trusted</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-sm font-medium">ISO-Grade Standards</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-bronze" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-sm font-medium">24/7 Mission-Critical Support</span>
                        </div>
                    </div>

                    {{-- Image dots indicator --}}
                    <div class="flex items-center gap-2 mt-8">
                        @foreach ($heroImages as $img)
                            <button class="hero-dot w-2 h-2 rounded-full bg-white/30 hover:bg-bronze {{ $loop->first ? 'hero-dot-active bg-bronze' : '' }}" data-index="{{ $loop->index }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Wave separator --}}
    <div class="absolute bottom-0 left-0 right-0 z-20">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto" preserveAspectRatio="none">
            <path d="M0 120L60 110C120 100 240 80 360 75C480 70 600 80 720 85C840 90 960 90 1080 85C1200 80 1320 70 1380 65L1440 60V120H0Z" fill="white"/>
        </svg>
    </div>
</section>

@push('scripts')
<script>
(function() {
    {{-- Rotating text --}}
    var words = [
        'Smart Technology',
        'Secure Infrastructure',
        'Sustainable Growth',
        'Mission-Critical Reliability',
        'Government-Grade Security',
        'Telematics & IoT Solutions',
        'Enterprise Software Systems',
        '24/7 Managed Services'
    ];
    var textEl = document.getElementById('hero-rotating-text');
    var textIndex = 0;
    if (textEl) {
        function rotateText() {
            textEl.style.opacity = '0';
            textEl.style.transform = 'translateY(10px)';
            textEl.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            setTimeout(function() {
                textIndex = (textIndex + 1) % words.length;
                textEl.textContent = words[textIndex];
                textEl.style.opacity = '1';
                textEl.style.transform = 'translateY(0)';
            }, 300);
        }
        setInterval(rotateText, 3000);
    }

    {{-- Rotating background images --}}
    var images = document.querySelectorAll('.hero-bg-image');
    var dots = document.querySelectorAll('.hero-dot');
    var imgIndex = 0;

    function rotateImage() {
        images.forEach(function(img) { img.classList.remove('opacity-100'); img.classList.add('opacity-0'); });
        dots.forEach(function(dot) { dot.classList.remove('hero-dot-active', 'bg-bronze'); dot.classList.add('bg-white/30'); });

        imgIndex = (imgIndex + 1) % images.length;
        images[imgIndex].classList.remove('opacity-0');
        images[imgIndex].classList.add('opacity-100');
        dots[imgIndex].classList.remove('bg-white/30');
        dots[imgIndex].classList.add('hero-dot-active', 'bg-bronze');
    }
    setInterval(rotateImage, 4500);

    {{-- Click on dots to jump to image --}}
    dots.forEach(function(dot) {
        dot.addEventListener('click', function() {
            var idx = parseInt(this.dataset.index);
            images.forEach(function(img) { img.classList.remove('opacity-100'); img.classList.add('opacity-0'); });
            dots.forEach(function(d) { d.classList.remove('hero-dot-active', 'bg-bronze'); d.classList.add('bg-white/30'); });
            images[idx].classList.remove('opacity-0');
            images[idx].classList.add('opacity-100');
            dots[idx].classList.remove('bg-white/30');
            dots[idx].classList.add('hero-dot-active', 'bg-bronze');
            imgIndex = idx;
        });
    });

    {{-- Scroll reveal --}}
    var scrollEl = document.querySelector('.scroll-reveal');
    if (scrollEl) {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                }
            });
        }, { threshold: 0.15 });
        observer.observe(scrollEl);
    }
})();
</script>
@endpush