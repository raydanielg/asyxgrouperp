<section id="home" class="hero-v2" style="position:relative; min-height:100vh; display:flex; align-items:center; overflow:hidden; background:#0D3E63; padding-top:80px;">

    {{-- Sliding background images --}}
    <div class="hero-v2-bg" style="position:absolute; inset:0; z-index:0;">
        @php
            $heroImages = [
                'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=1920&q=80',
                'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1920&q=80',
                'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=1920&q=80',
                'https://images.unsplash.com/photo-1561070791-2526d30994da?w=1920&q=80',
                'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=1920&q=80',
            ];
        @endphp
        @foreach ($heroImages as $img)
            <img src="{{ $img }}" alt="" class="hero-v2-slide {{ $loop->first ? 'active' : '' }}" data-index="{{ $loop->index }}" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:0; transform:scale(1.08); transition: opacity 1.8s cubic-bezier(.4,0,.2,1), transform 6s ease-out;">
        @endforeach
    </div>

    {{-- Gradient overlay --}}
    <div style="position:absolute; inset:0; z-index:1; background: linear-gradient(135deg, rgba(13,62,99,0.92) 0%, rgba(13,62,99,0.78) 50%, rgba(13,62,99,0.55) 100%);"></div>
    <div style="position:absolute; inset:0; z-index:1; background: linear-gradient(to top, rgba(13,62,99,0.6) 0%, transparent 40%, rgba(13,62,99,0.3) 100%);"></div>

    {{-- Slide indicators --}}
    <div style="position:absolute; bottom:40px; right:40px; z-index:5; display:flex; gap:8px;">
        @foreach ($heroImages as $img)
            <button class="hero-v2-dot {{ $loop->first ? 'dot-active' : '' }}" data-index="{{ $loop->index }}" style="width:8px; height:8px; border-radius:50%; border:none; cursor:pointer; background:rgba(250,248,244,0.3); transition:all 0.3s ease; padding:0;"></button>
        @endforeach
    </div>

    {{-- Content --}}
    <div class="hero-v2-grid" style="position:relative; z-index:2; display:grid; grid-template-columns:1.05fr 0.95fr; align-items:center; gap:40px; width:100%; max-width:1180px; margin:0 auto; padding:0 32px;">

        {{-- Left: Text --}}
        <div>
            <div class="hero-v2-eyebrow" style="display:flex; align-items:center; gap:10px; font-size:13px; font-weight:600; letter-spacing:0.14em; text-transform:uppercase; color:rgba(250,248,244,0.7); margin-bottom:26px; opacity:0; animation: hero-v2-rise .7s ease forwards; animation-delay:0.5s;">
                <span style="width:26px; height:2px; background:#EC2226; display:inline-block;"></span>
                16+ Years of Excellence
            </div>

            <h1 style="font-family:'Space Grotesk',sans-serif; font-size:clamp(38px,5vw,64px); line-height:1.06; font-weight:700; color:#FAF8F4; letter-spacing:-0.02em; opacity:0; animation: hero-v2-rise .8s ease forwards; animation-delay:0.65s;">
                Next-Gen
                <span class="hero-v2-rotator" style="position:relative; display:inline-block; height:1.06em; overflow:hidden; vertical-align:top; min-width:9.4ch;">
                    <ul style="list-style:none; margin:0; padding:0;">
                        <li style="color:#EC2226; animation: hero-v2-cycle 6.4s infinite;">Technology.</li>
                        <li style="color:#EC2226; animation: hero-v2-cycle 6.4s infinite; animation-delay:1.6s;">Cybersecurity.</li>
                        <li style="color:#EC2226; animation: hero-v2-cycle 6.4s infinite; animation-delay:3.2s;">Infrastructure.</li>
                        <li style="color:#EC2226; animation: hero-v2-cycle 6.4s infinite; animation-delay:4.8s;">Innovation.</li>
                    </ul>
                </span><br>Solutions
            </h1>

            <p style="margin-top:24px; font-size:18px; line-height:1.65; color:rgba(250,248,244,0.75); max-width:480px; opacity:0; animation: hero-v2-rise .8s ease forwards; animation-delay:0.8s;">
                Empowering Africa with cutting-edge ICT infrastructure, smart technologies, and cybersecurity solutions for mission-critical environments.
            </p>

            <div style="margin-top:38px; display:flex; align-items:center; gap:28px; opacity:0; animation: hero-v2-rise .8s ease forwards; animation-delay:0.95s;">
                <a href="{{ route('services') }}" style="background:#FAF8F4; color:#0D3E63; padding:16px 30px; border-radius:100px; font-weight:600; font-size:15px; display:inline-flex; align-items:center; gap:10px; transition:transform .25s ease, background .25s ease;" onmouseover="this.style.background='#EC2226'; this.style.color='#FAF8F4'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#FAF8F4'; this.style.color='#0D3E63'; this.style.transform='translateY(0)';">
                    Explore Services →
                </a>
                <a href="{{ route('contact') }}" style="font-weight:600; font-size:15px; color:#FAF8F4; border-bottom:2px solid rgba(250,248,244,0.5); padding-bottom:3px; transition:border-color .2s ease, color .2s ease;" onmouseover="this.style.color='#EC2226'; this.style.borderColor='#EC2226';" onmouseout="this.style.color='#FAF8F4'; this.style.borderColor='rgba(250,248,244,0.5)';">
                    Get in Touch
                </a>
            </div>

            {{-- Stats --}}
            <div style="margin-top:48px; display:flex; gap:40px; opacity:0; animation: hero-v2-rise .8s ease forwards; animation-delay:1.1s;">
                <div>
                    <div style="font-family:'Space Grotesk',sans-serif; font-size:32px; font-weight:700; color:#FAF8F4;">500<span style="color:#EC2226;">+</span></div>
                    <div style="font-size:12px; font-weight:500; letter-spacing:0.08em; text-transform:uppercase; color:rgba(250,248,244,0.5); margin-top:4px;">Projects Delivered</div>
                </div>
                <div>
                    <div style="font-family:'Space Grotesk',sans-serif; font-size:32px; font-weight:700; color:#FAF8F4;">100<span style="color:#EC2226;">+</span></div>
                    <div style="font-size:12px; font-weight:500; letter-spacing:0.08em; text-transform:uppercase; color:rgba(250,248,244,0.5); margin-top:4px;">Enterprise Clients</div>
                </div>
                <div>
                    <div style="font-family:'Space Grotesk',sans-serif; font-size:32px; font-weight:700; color:#FAF8F4;">24/7</div>
                    <div style="font-size:12px; font-weight:500; letter-spacing:0.08em; text-transform:uppercase; color:rgba(250,248,244,0.5); margin-top:4px;">Support Coverage</div>
                </div>
            </div>
        </div>

        {{-- Right: 3D Rings --}}
        <div class="hero-v2-ring-stage" style="position:relative; height:520px; display:flex; align-items:center; justify-content:center; perspective:1200px; opacity:0; animation: hero-v2-rise 1s ease forwards; animation-delay:0.35s;">
            <div class="hero-v2-ring-orbit" style="position:relative; width:380px; height:380px; transform-style:preserve-3d; animation: hero-v2-spin 22s linear infinite;">
                <div style="position:absolute; inset:0; border-radius:50%; border:26px solid transparent; -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 26px), #000 calc(100% - 26px)); border-top-color:#A56035; border-right-color:#A56035; transform:rotateX(90deg) rotateZ(20deg);"></div>
                <div style="position:absolute; inset:0; border-radius:50%; border:26px solid transparent; -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 26px), #000 calc(100% - 26px)); border-top-color:#FAF8F4; border-left-color:#FAF8F4; transform:rotateY(90deg) rotateZ(-15deg);"></div>
                <div style="position:absolute; inset:0; border-radius:50%; border:26px solid transparent; -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 26px), #000 calc(100% - 26px)); border-bottom-color:#632871; border-right-color:#632871; transform:rotateX(30deg) rotateY(60deg);"></div>
                <div style="position:absolute; inset:0; border-radius:50%; border:26px solid transparent; -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 26px), #000 calc(100% - 26px)); border-bottom-color:#EC2226; border-left-color:#EC2226; transform:rotateX(-30deg) rotateY(-60deg);"></div>
                <div style="position:absolute; inset:0; margin:auto; width:150px; height:150px; border-radius:50%; background:rgba(250,248,244,0.95); backdrop-filter:blur(4px); box-shadow:0 20px 60px rgba(13,62,99,0.4); display:flex; align-items:center; justify-content:center; transform:translateZ(40px);">
                    <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX mark" style="width:92px; height:auto;">
                </div>
            </div>
            <div style="position:absolute; bottom:14px; left:50%; width:260px; height:34px; background:radial-gradient(closest-side, rgba(0,0,0,0.25), transparent 72%); transform:translateX(-50%); filter:blur(2px);"></div>
        </div>
    </div>
</section>

<style>
.hero-v2-slide.active {
    opacity: 1 !important;
    transform: scale(1) !important;
}
.hero-v2-dot.dot-active {
    width: 24px !important;
    border-radius: 4px !important;
    background: #EC2226 !important;
}
@keyframes hero-v2-spin {
    from { transform: rotateY(0deg) rotateX(12deg); }
    to { transform: rotateY(360deg) rotateX(12deg); }
}
@keyframes hero-v2-cycle {
    0% { transform: translateY(110%); opacity:0; }
    4% { transform: translateY(0%); opacity:1; }
    22% { transform: translateY(0%); opacity:1; }
    27% { transform: translateY(-110%); opacity:0; }
    100% { transform: translateY(-110%); opacity:0; }
}
@keyframes hero-v2-rise {
    from { opacity:0; transform: translateY(16px); }
    to { opacity:1; transform: translateY(0); }
}
@media (max-width: 860px) {
    .hero-v2-grid { grid-template-columns:1fr !important; text-align:left; }
    .hero-v2-ring-stage { height:340px !important; margin-top:20px; }
    .hero-v2-ring-orbit { width:260px !important; height:260px !important; }
    .hero-v2-ring-orbit > div:nth-child(5) { width:100px !important; height:100px !important; }
    .hero-v2-ring-orbit > div:nth-child(5) img { width:60px !important; }
    .hero-v2-rotator { min-width:8ch !important; }
}
@media (prefers-reduced-motion: reduce) {
    .hero-v2-ring-orbit { animation: none !important; }
    .hero-v2-rotator li { animation: none !important; }
    .hero-v2-slide { transition: opacity 0.4s ease !important; }
}
</style>

<script>
(function() {
    var slides = document.querySelectorAll('.hero-v2-slide');
    var dots = document.querySelectorAll('.hero-v2-dot');
    var idx = 0;

    function goTo(n) {
        slides.forEach(function(s) { s.classList.remove('active'); });
        dots.forEach(function(d) { d.classList.remove('dot-active'); });
        slides[n].classList.add('active');
        dots[n].classList.add('dot-active');
        idx = n;
    }

    function next() {
        goTo((idx + 1) % slides.length);
    }

    if (slides.length > 1) {
        setInterval(next, 5000);
    }

    dots.forEach(function(dot) {
        dot.addEventListener('click', function() {
            goTo(parseInt(this.dataset.index));
        });
    });
})();
</script>