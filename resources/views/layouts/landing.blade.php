<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ASYX Group - Smart Technology. Secure Infrastructure. Sustainable Growth.')</title>
    <meta name="description" content="ASYX Group - Trusted technology partner for government, parastatals and regulated enterprises in Tanzania. Smart Technology, Secure Infrastructure, Sustainable Growth.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;1,500;1,600&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { DEFAULT: '#1B3A5C', 50:'#eef3f8', 100:'#d4e0ec', 200:'#a8c1d9', 300:'#6d97bd', 400:'#3d6b9a', 500:'#1B3A5C', 600:'#163049', 700:'#112637', 800:'#0c1c26', 900:'#07121a' },
                        bronze: { DEFAULT: '#A8703A', 50:'#faf5ef', 100:'#f0e0d0', 200:'#e0c0a0', 300:'#d0a070', 400:'#A8703A', 500:'#8f5e2e', 600:'#744c25', 700:'#5a3a1c', 800:'#3f2813', 900:'#251709' },
                        purple: { DEFAULT: '#5B2A6E', 50:'#f8f0fa', 100:'#e8d0ec', 200:'#d0a0d9', 300:'#b070c6', 400:'#5B2A6E', 500:'#4a2258', 600:'#3a1b47', 700:'#2a1435', 800:'#1a0d23', 900:'#0a0612' },
                        crimson: { DEFAULT: '#C81E3A', 50:'#fdf0f2', 100:'#fcdde1', 200:'#f9bcc4', 300:'#f48ba0', 400:'#C81E3A', 500:'#a81830', 600:'#881226', 700:'#680c1c', 800:'#480612', 900:'#280008' },
                    },
                    fontFamily: {
                        heading: ['Montserrat', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --navy: #1B3A5C;
            --bronze: #A8703A;
            --purple: #5B2A6E;
            --crimson: #C81E3A;
            --light-grey: #F2F2F2;
            --dark-text: #222222;
        }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        @keyframes float { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-12px); } }
        @keyframes shakeDown { 0% { opacity:0; transform:translateY(-40px); } 50% { transform:translateY(10px); } 70% { transform:translateY(-5px); } 100% { opacity:1; transform:translateY(0); } }
        .animate-fade-up { animation: fadeInUp 0.6s ease-out both; }
        .animate-fade { animation: fadeIn 0.8s ease-out both; }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .shake-title { animation: shakeDown 0.8s cubic-bezier(0.34,1.56,0.64,1) both; }
        .hero-gradient { background: linear-gradient(135deg, #1B3A5C 0%, #163049 50%, #112637 100%); }
        .cta-gradient { background: linear-gradient(135deg, #C81E3A 0%, #5B2A6E 100%); }
        .text-gradient { background: linear-gradient(135deg, #C81E3A, #5B2A6E); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .glass { background: rgba(255,255,255,0.08); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.12); }
        .glass-light { background: rgba(255,255,255,0.12); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.15); }
        .hero-bg-image { transition: opacity 1.2s ease-in-out; }
        .hero-dot { transition: all 0.3s ease; cursor: pointer; }
        .hero-dot-active { width: 24px !important; border-radius: 9999px; }
        .header-scrolled { background: rgba(27,58,92,0.95); backdrop-filter: blur(20px); box-shadow: 0 10px 40px -10px rgba(0,0,0,0.3); }
        .scroll-reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.8s cubic-bezier(0.4,0,0.2,1), transform 0.8s cubic-bezier(0.4,0,0.2,1); }
        .scroll-reveal.revealed { opacity: 1; transform: translateY(0); }
        {{-- Drag-down reveal effect --}}
        .reveal-down {
            opacity: 0;
            transform: translateY(-80px) scale(0.9);
            transition: opacity 0.9s cubic-bezier(0.4,0,0.2,1), transform 0.9s cubic-bezier(0.34,1.56,0.64,1);
        }
        .reveal-down.revealed {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        {{-- Mission/Vision stacked cards --}}
        .mv-stack { position: relative; }
        .mv-card { min-height: 320px; margin-bottom: 2rem; transition: all 0.8s cubic-bezier(0.4,0,0.2,1); }
        .mv-card.hidden-up { opacity: 0; transform: translateY(60px) scale(0.96); }
        .mv-card.visible { opacity: 1; transform: translateY(0) scale(1); }
        {{-- Core values v2 cards --}}
        .value-card-v2 { transition: all 0.4s cubic-bezier(0.4,0,0.2,1); }
        .value-card-v2:hover { transform: translateY(-10px) scale(1.02); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .value-card-v2:hover img { transform: scale(1.1); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4,0,0.2,1); position: relative; }
        .card-hover:hover { transform: translateY(-6px); box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15); }

        {{-- Animated company profile v3 section --}}
        .profile-v3 .dot-grid-v3 { background-image:radial-gradient(#A8703A 1.2px, transparent 1.2px); background-size:28px 28px; animation:gridDrift 24s linear infinite; }
        @keyframes gridDrift { from{background-position:0 0} to{background-position:56px 56px} }
        .profile-v3 .orb-v3 { position:absolute; border-radius:9999px; filter:blur(60px); opacity:.14; animation:orbFloat 14s ease-in-out infinite; }
        .profile-v3 .orb-v3-2 { animation-delay:-5s; animation-duration:18s; }
        .profile-v3 .orb-v3-3 { animation-delay:-9s; animation-duration:22s; }
        @keyframes orbFloat { 0%,100%{transform:translate(0,0) scale(1)} 33%{transform:translate(40px,-50px) scale(1.15)} 66%{transform:translate(-30px,40px) scale(.9)} }
        .profile-v3 .profile-particle-v3 { position:absolute; font-family:monospace; font-weight:700; opacity:0; animation:particleRise linear infinite; pointer-events:none; user-select:none; }
        @keyframes particleRise { 0%{transform:translateY(40px) rotate(0);opacity:0} 12%{opacity:.5} 88%{opacity:.5} 100%{transform:translateY(-90vh) rotate(20deg);opacity:0} }
        .profile-v3 .shimmer-title-v3 { background:linear-gradient(110deg,#1B3A5C 35%,#A8703A 50%,#1B3A5C 65%); background-size:220% 100%; -webkit-background-clip:text; background-clip:text; color:transparent; animation:shimmer 4.5s ease-in-out infinite; }
        @keyframes shimmer { 0%{background-position:120% 0} 100%{background-position:-120% 0} }
        .profile-v3 .title-underline-v3 { width:0; height:5px; border-radius:99px; background:linear-gradient(90deg,#A8703A,#C81E3A,#5B2A6E); margin:14px auto 0; transition:width 1.1s cubic-bezier(.7,0,.2,1) .3s; }
        .profile-v3 .rv-v3.in-view .title-underline-v3 { width:120px; }
        .profile-v3 .badge-wrap-v3 { position:relative; }
        .profile-v3 .badge-ring-v3 { position:absolute; inset:-14px; border-radius:9999px; border:2.5px dashed rgba(168,112,58,.55); animation:spin 16s linear infinite; }
        .profile-v3 .badge-ring2-v3 { position:absolute; inset:-28px; border-radius:9999px; border:1.5px dashed rgba(91,42,110,.35); animation:spin 26s linear infinite reverse; }
        @keyframes spin { to{transform:rotate(360deg)} }
        .profile-v3 .badge-core-v3 { animation:badgePulse 3.2s ease-in-out infinite; }
        @keyframes badgePulse { 0%,100%{box-shadow:0 22px 50px -12px rgba(27,58,92,.45),0 0 0 0 rgba(168,112,58,.35)} 50%{box-shadow:0 22px 50px -12px rgba(27,58,92,.45),0 0 0 22px rgba(168,112,58,0)} }
        .profile-v3 .badge-orbit-dot-v3 { position:absolute; inset:-14px; animation:spin 8s linear infinite; }
        .profile-v3 .badge-orbit-dot-v3::after { content:''; position:absolute; top:-6px; left:50%; width:12px; height:12px; border-radius:99px; background:linear-gradient(135deg,#A8703A,#C81E3A); transform:translateX(-50%); box-shadow:0 0 14px rgba(200,30,58,.8); }
        .profile-v3 .rv-v3 { opacity:0; transform:translateY(46px); transition:opacity .9s cubic-bezier(.2,.7,.2,1), transform .9s cubic-bezier(.2,.7,.2,1); }
        .profile-v3 .rv-v3.in-view { opacity:1; transform:none; }
        .profile-v3 .chip-pop-v3 { opacity:0; transform:scale(.6); transition:opacity .6s, transform .6s cubic-bezier(.34,1.56,.64,1); }
        .profile-v3 .rv-v3.in-view .chip-pop-v3 { opacity:1; transform:scale(1); }
        .profile-v3 .mv-panel { opacity:0; transition:opacity 1.1s cubic-bezier(.16,1,.3,1), transform 1.1s cubic-bezier(.16,1,.3,1); will-change:transform; }
        .profile-v3 .mv-left { transform:translateX(-70px); }
        .profile-v3 .mv-right { transform:translateX(70px); }
        .profile-v3 .mv-panel.in-view { opacity:1; transform:none; }
        .profile-v3 .kenburns-v3 { animation:kenburns 18s ease-in-out infinite alternate; }
        @keyframes kenburns { from{transform:scale(1) translate(0,0)} to{transform:scale(1.16) translate(-2%,2%)} }
        .profile-v3 .quote-mark-v3 { position:absolute; font-family:'Playfair Display',serif; font-size:11rem; line-height:1; opacity:.08; user-select:none; pointer-events:none; }
        .profile-v3 .quote-block-v3 { position:relative; padding-left:1.75rem; }
        .profile-v3 .quote-block-v3::before { content:''; position:absolute; left:0; top:6px; bottom:6px; width:5px; border-radius:99px; background:linear-gradient(180deg,#A8703A,#C81E3A); transform:scaleY(0); transform-origin:top; transition:transform 1s cubic-bezier(.7,0,.2,1) .5s; }
        .profile-v3 .quote-block-v3.q-purple-v3::before { background:linear-gradient(180deg,#5B2A6E,#1B3A5C); }
        .profile-v3 .in-view .quote-block-v3::before { transform:scaleY(1); }
        .profile-v3 .qword-v3 { display:inline-block; opacity:0; transform:translateY(18px); transition:opacity .5s ease, transform .5s cubic-bezier(.2,.7,.2,1); }
        .profile-v3 .in-view .qword-v3 { opacity:1; transform:none; }
        .profile-v3 .img-chip-v3 { backdrop-filter:blur(8px); animation:chipFloat 5s ease-in-out infinite; }
        @keyframes chipFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        .profile-v3 .marquee-v3 { overflow:hidden; -webkit-mask-image:linear-gradient(90deg,transparent,#000 12%,#000 88%,transparent); mask-image:linear-gradient(90deg,transparent,#000 12%,#000 88%,transparent); }
        .profile-v3 .marquee-track-v3 { display:flex; gap:3.5rem; width:max-content; animation:marquee 26s linear infinite; }
        .profile-v3 .marquee-v3:hover .marquee-track-v3 { animation-play-state:paused; }
        @keyframes marquee { to{transform:translateX(-50%)} }
        .profile-v3 .vs-img-v3 { position:absolute; inset:0; background-size:cover; background-position:center; opacity:0; transform:scale(1.12); transition:opacity 1s ease, transform 1.4s ease; clip-path:circle(0% at 30% 50%); }
        .profile-v3 .vs-img-v3.active { opacity:1; transform:scale(1); clip-path:circle(150% at 30% 50%); z-index:1; transition:opacity .9s ease, transform 6s linear, clip-path 1.1s cubic-bezier(.7,0,.2,1); }
        .profile-v3 .vs-img-v3.leaving { opacity:0; transform:scale(1.08); clip-path:circle(150% at 30% 50%); z-index:0; }
        .profile-v3 .vs-out .vs-anim-v3 { animation:vsTextOut .35s ease forwards; }
        .profile-v3 .vs-in .vs-anim-v3 { animation:vsTextIn .6s cubic-bezier(.2,.7,.2,1) both; }
        .profile-v3 #vsDesc.vs-anim-v3 { animation-delay:.08s; }
        @keyframes vsTextOut { to{opacity:0; transform:translateY(-26px)} }
        @keyframes vsTextIn { from{opacity:0; transform:translateY(34px)} to{opacity:1; transform:none} }
        .profile-v3 .vs-pill-v3 { padding:.45rem 1rem; border-radius:99px; font-size:.78rem; font-weight:700; border:1.5px solid rgba(255,255,255,.22); color:rgba(255,255,255,.75); transition:all .35s cubic-bezier(.2,.7,.2,1); cursor:pointer; background:transparent; }
        .profile-v3 .vs-pill-v3:hover { border-color:rgba(255,255,255,.55); color:#fff; transform:translateY(-2px); }
        .profile-v3 .vs-pill-v3.active { background:linear-gradient(135deg,#A8703A,#C81E3A); border-color:transparent; color:#fff; box-shadow:0 8px 20px -6px rgba(200,30,58,.55); transform:translateY(-2px); }
        .profile-v3 #vsProgress { transition:width .1s linear; }
        .profile-v3 #vsGhostNum { transition:all .45s ease; }
        .profile-v3 #vsGhostNum.switching { transform:translateY(30px); opacity:0; }
        .profile-v3 #vsIconChip { transition:all .45s ease; }
        .profile-v3 #vsIconChip.switching { transform:rotate(-14deg) scale(.6); opacity:0; }
        @media (prefers-reduced-motion: reduce) {
            .profile-v3 *, .profile-v3 *::before, .profile-v3 *::after { animation-duration:.01ms !important; animation-iteration-count:1 !important; transition-duration:.01ms !important; }
        }

        {{-- Clients section --}}
        .clients-section .dot-grid-clients { background-image:radial-gradient(#1B3A5C 1px, transparent 1px); background-size:24px 24px; animation:clientsGridDrift 20s linear infinite; }
        @keyframes clientsGridDrift { from{background-position:0 0} to{background-position:24px 24px} }
        .clients-section .client-card { position:relative; overflow:hidden; }
        .clients-section .client-card::before { content:''; position:absolute; inset:0; background:linear-gradient(135deg,transparent 0%,rgba(168,112,58,.04) 100%); opacity:0; transition:opacity .3s; }
        .clients-section .client-card:hover::before { opacity:1; }
        .clients-section .clients-track { will-change:transform; }
        @keyframes clientsMarquee { from{transform:translateX(0)} to{transform:translateX(-50%)} }
        @media (prefers-reduced-motion: reduce) {
            .clients-section .clients-track { animation:none !important; }
        }

        {{-- Testimonials section --}}
        .testimonials-section .dot-grid-testimonials { background-image:radial-gradient(#A8703A 1.2px, transparent 1.2px); background-size:28px 28px; animation:gridDrift 24s linear infinite; }
        .testimonials-section .rv-testimonials { opacity:0; transform:translateY(46px); transition:opacity .9s cubic-bezier(.2,.7,.2,1), transform .9s cubic-bezier(.2,.7,.2,1); }
        .testimonials-section .rv-testimonials.in-view { opacity:1; transform:none; }
        .testimonials-section .chip-pop-testimonials { opacity:0; transform:scale(.6); transition:opacity .6s, transform .6s cubic-bezier(.34,1.56,.64,1); }
        .testimonials-section .rv-testimonials.in-view .chip-pop-testimonials { opacity:1; transform:scale(1); }
        .testimonials-section .title-underline-testimonials { width:0; height:5px; border-radius:99px; background:linear-gradient(90deg,#A8703A,#C81E3A,#5B2A6E); margin:14px auto 0; transition:width 1.1s cubic-bezier(.7,0,.2,1) .3s; }
        .testimonials-section .rv-testimonials.in-view .title-underline-testimonials { width:120px; }
        .testimonials-section .tm-marquee { overflow:hidden; -webkit-mask-image:linear-gradient(90deg,transparent,#000 7%,#000 93%,transparent); mask-image:linear-gradient(90deg,transparent,#000 7%,#000 93%,transparent); }
        .testimonials-section .tm-track { display:flex; gap:1.5rem; width:max-content; animation:tmScroll var(--speed,40s) linear infinite; padding:1.25rem 0; }
        .testimonials-section .tm-track.rev { animation-name:tmScrollRev; }
        .testimonials-section .tm-marquee:hover .tm-track { animation-play-state:paused; }
        @keyframes tmScroll { to{transform:translateX(-50%)} }
        @keyframes tmScrollRev { from{transform:translateX(-50%)} to{transform:translateX(0)} }
        .testimonials-section .tm-card { width:360px; flex-shrink:0; position:relative; background:#fff; border-radius:1.25rem; padding:1.6rem; border:1px solid rgba(14,42,74,.07); box-shadow:0 10px 28px -14px rgba(27,58,92,.14); transition:transform .45s cubic-bezier(.2,.7,.2,1), box-shadow .45s; }
        .testimonials-section .tm-card:hover { transform:translateY(-10px) scale(1.02); box-shadow:0 30px 55px -18px rgba(27,58,92,.32); z-index:5; }
        .testimonials-section .tm-card::before { content:''; position:absolute; top:0; left:1.6rem; right:1.6rem; height:4px; border-radius:0 0 8px 8px; background:var(--accent, linear-gradient(90deg,#A8703A,#C81E3A)); transform:scaleX(.35); transform-origin:left; transition:transform .5s cubic-bezier(.2,.7,.2,1); }
        .testimonials-section .tm-card:hover::before { transform:scaleX(1); }
        .testimonials-section .tm-qicon { position:absolute; top:1.2rem; right:1.4rem; width:38px; height:38px; border-radius:12px; background:var(--accent, linear-gradient(135deg,#A8703A,#C81E3A)); display:flex; align-items:center; justify-content:center; color:#fff; box-shadow:0 8px 18px -6px rgba(27,58,92,.35); transition:transform .5s cubic-bezier(.34,1.56,.64,1); }
        .testimonials-section .tm-card:hover .tm-qicon { transform:rotate(-10deg) scale(1.12); }
        .testimonials-section .tm-av { position:relative; border-radius:9999px; padding:2.5px; background:transparent; transition:background .4s; }
        .testimonials-section .tm-card:hover .tm-av { background:var(--accent, linear-gradient(135deg,#A8703A,#C81E3A)); }
        .testimonials-section .tm-av img { display:block; width:44px; height:44px; border-radius:9999px; object-fit:cover; border:2.5px solid #fff; }
        .testimonials-section .tm-card .star { transition:transform .3s cubic-bezier(.34,1.56,.64,1); }
        .testimonials-section .tm-card:hover .star:nth-child(1){ transform:scale(1.25); transition-delay:0s }
        .testimonials-section .tm-card:hover .star:nth-child(2){ transform:scale(1.25); transition-delay:.05s }
        .testimonials-section .tm-card:hover .star:nth-child(3){ transform:scale(1.25); transition-delay:.1s }
        .testimonials-section .tm-card:hover .star:nth-child(4){ transform:scale(1.25); transition-delay:.15s }
        .testimonials-section .tm-card:hover .star:nth-child(5){ transform:scale(1.25); transition-delay:.2s }
        @media (max-width:640px){ .testimonials-section .tm-card{ width:300px; } }
        @media (prefers-reduced-motion: reduce) {
            .testimonials-section .tm-track { animation:none !important; }
        }

        {{-- Animated rotating border on hover --}}
        .service-card { position: relative; overflow: hidden; }
        .service-card::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 1rem;
            background: conic-gradient(from 0deg, transparent 0deg, #A8703A 60deg, #C81E3A 120deg, #5B2A6E 180deg, #1B3A5C 240deg, transparent 300deg, transparent 360deg);
            opacity: 0;
            z-index: 0;
            animation: border-spin 3s linear infinite;
            transition: opacity 0.4s ease;
        }
        .service-card:hover::before { opacity: 1; }
        .service-card::after {
            content: '';
            position: absolute;
            inset: 2px;
            border-radius: 0.875rem;
            z-index: 0;
        }
        .service-card > * { position: relative; z-index: 1; }
        @keyframes border-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        {{-- Auto-sliding images --}}
        .slide-track {
            display: flex;
            transition: transform 0.6s cubic-bezier(0.4,0,0.2,1);
        }
        .slide-track > * { flex-shrink: 0; width: 100%; }
        .nav-link { position: relative; }
        .nav-link::after { content:''; position:absolute; bottom:-2px; left:0; width:0; height:2px; background:#A8703A; transition: width 0.3s ease; }
        .nav-link:hover::after { width:100%; }
        .section-title { position: relative; display: inline-block; }
        .section-title::after { content:''; display:block; width:60px; height:4px; background:#A8703A; margin-top:8px; border-radius:2px; }
        .section-title-center::after { margin-left:auto; margin-right:auto; }
        .btn-primary { background: linear-gradient(135deg, #C81E3A, #5B2A6E); color:#fff; font-weight:700; border-radius:8px; transition: all 0.3s ease; }
        .btn-primary:hover { transform: scale(1.03); box-shadow: 0 10px 30px -5px rgba(200,30,58,0.4); }
        .btn-secondary { border: 2px solid #1B3A5C; color: #1B3A5C; font-weight:700; border-radius:8px; transition: all 0.3s ease; background:transparent; }
        .btn-secondary:hover { background: #1B3A5C; color: #fff; }
        .logo-greyscale { filter: grayscale(100%); opacity:0.6; transition: all 0.3s ease; }
        .logo-greyscale:hover { filter: grayscale(0%); opacity:1; }
        .rotator-wrap { min-height: 1.2em; overflow: hidden; vertical-align: bottom; display: inline-block; }
        .rotator-text {
            display: inline-block;
            animation: rotatorIn 0.6s cubic-bezier(0.4,0,0.2,1) both;
        }
        .rotator-text.out {
            animation: rotatorOut 0.5s cubic-bezier(0.4,0,0.2,1) both;
        }
        @keyframes rotatorIn {
            0% { opacity: 0; transform: translateY(100%) rotateX(90deg); }
            100% { opacity: 1; transform: translateY(0) rotateX(0); }
        }
        @keyframes rotatorOut {
            0% { opacity: 1; transform: translateY(0) rotateX(0); }
            100% { opacity: 0; transform: translateY(-100%) rotateX(-90deg); }
        }

        {{-- 3D Marquee --}}
        .marquee-3d-container {
            perspective: 1000px;
            perspective-origin: 50% 50%;
        }
        .marquee-3d {
            transform: rotateX(55deg) rotateZ(0deg);
            transform-style: preserve-3d;
            height: 600px;
            width: 200%;
            position: absolute;
            left: -50%;
            top: 50%;
            margin-top: -300px;
        }
        .marquee-row {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
            width: max-content;
            animation: marquee-scroll-left 40s linear infinite;
        }
        .marquee-row.reverse {
            animation: marquee-scroll-right 40s linear infinite;
        }
        .marquee-row:nth-child(2) { animation-duration: 50s; }
        .marquee-row:nth-child(3) { animation-duration: 35s; }
        .marquee-row:nth-child(4) { animation-duration: 45s; }
        .marquee-row:nth-child(5) { animation-duration: 38s; }
        .marquee-item {
            width: 200px;
            height: 140px;
            border-radius: 12px;
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .marquee-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        @keyframes marquee-scroll-left {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }
        @keyframes marquee-scroll-right {
            from { transform: translateX(-50%); }
            to { transform: translateX(0); }
        }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }

        {{-- Scroll reveal --}}
        .reveal { opacity: 0; transform: translateY(40px); transition: opacity 0.7s cubic-bezier(0.4,0,0.2,1), transform 0.7s cubic-bezier(0.4,0,0.2,1); }
        .reveal.active { opacity: 1; transform: translateY(0); }
        .reveal-left { opacity: 0; transform: translateX(-50px); transition: opacity 0.7s cubic-bezier(0.4,0,0.2,1), transform 0.7s cubic-bezier(0.4,0,0.2,1); }
        .reveal-left.active { opacity: 1; transform: translateX(0); }
        .reveal-right { opacity: 0; transform: translateX(50px); transition: opacity 0.7s cubic-bezier(0.4,0,0.2,1), transform 0.7s cubic-bezier(0.4,0,0.2,1); }
        .reveal-right.active { opacity: 1; transform: translateX(0); }
        .reveal-scale { opacity: 0; transform: scale(0.85); transition: opacity 0.6s cubic-bezier(0.4,0,0.2,1), transform 0.6s cubic-bezier(0.4,0,0.2,1); }
        .reveal-scale.active { opacity: 1; transform: scale(1); }
        .stagger-1 { transition-delay: 0.1s; }
        .stagger-2 { transition-delay: 0.2s; }
        .stagger-3 { transition-delay: 0.3s; }
        .stagger-4 { transition-delay: 0.4s; }
        .stagger-5 { transition-delay: 0.5s; }
        .stagger-6 { transition-delay: 0.6s; }
        .stagger-7 { transition-delay: 0.7s; }
        .stagger-8 { transition-delay: 0.8s; }
        .stagger-9 { transition-delay: 0.9s; }
    </style>
</head>
<body class="font-sans antialiased bg-white text-[#222222] overflow-x-hidden">

    @yield('content')

    @stack('scripts')

    <script>
    (function() {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });

        document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale').forEach(function(el) {
            observer.observe(el);
        });
    })();
    </script>
</body>
</html>
