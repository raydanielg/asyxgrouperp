<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel') . ' — ' . __('Authentication'))</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="referrer" content="strict-origin-when-cross-origin">

    <style>
        @keyframes simpleFadeIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeInDown { from { opacity:0; transform:translateY(-24px); } to { opacity:1; transform:translateY(0); } }
        @keyframes slowZoom { 0% { transform:scale(1); } 100% { transform:scale(1.08); } }
        .auth-card-entrance { animation: fadeInUp 0.7s cubic-bezier(0.16,1,0.3,1) both; }
        .auth-header-entrance { animation: fadeInDown 0.6s cubic-bezier(0.16,1,0.3,1) 0.2s both; }
        .auth-field-entrance { animation: simpleFadeIn 0.5s cubic-bezier(0.16,1,0.3,1) both; }
        .auth-btn:hover { animation: pulse 0.8s ease-in-out infinite; }
        .ajax-loader { position:fixed; top:0; left:0; right:0; height:3px; background: linear-gradient(90deg, #024938, #f9ac00, #024938); background-size: 200% 100%; animation: ajaxProgress 1s linear infinite; z-index:9999; display:none; }
        @keyframes ajaxProgress { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }
        .page-transition { animation: simpleFadeIn 0.35s ease-out both; }
        .brand-bg { background-image: radial-gradient(rgba(255,255,255,0.12) 2px, transparent 2.5px); background-size: 22px 22px; }
        .glass { background: rgba(255,255,255,0.08); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.12); }
        @keyframes kenBurns { 0% { transform:scale(1) translate(0,0); } 100% { transform:scale(1.12) translate(-2%,-2%); } }
        .slide-image { animation: kenBurns 16s ease-in-out infinite alternate; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: { 50:'#e6f5f1',100:'#b3e0d4',200:'#80cbc0',300:'#4db5a8',400:'#1a9f8e',500:'#024938',600:'#023d30',700:'#013028',800:'#01241f',900:'#001816' },
                        gold: { 50:'#fff5e0',100:'#ffe6b3',200:'#ffd680',300:'#ffc64d',400:'#ffb71a',500:'#f9ac00',600:'#d49700',700:'#b07c00',800:'#8c6100',900:'#684600' }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-['Nunito',sans-serif] antialiased text-slate-800 min-h-screen">

    <div id="ajaxLoader" class="ajax-loader"></div>

    <main id="authMain" class="relative min-h-screen w-full flex flex-col lg:flex-row overflow-hidden">
        {{-- Left side: animated image slideshow --}}
        <div class="hidden lg:flex lg:w-1/2 relative h-screen overflow-hidden">
            {{-- Background image with slow zoom animation --}}
            <img src="{{ asset('serious-expert-expressing-support-colleague (1).jpg') }}" alt="ASYX ERP" class="absolute inset-0 w-full h-full object-cover slide-image">

            {{-- Gradient overlays --}}
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/85 via-emerald-800/60 to-emerald-900/85"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/90 via-transparent to-emerald-900/40"></div>
            <div class="absolute inset-0 brand-bg opacity-40"></div>

            {{-- Slide text content --}}
            <div class="absolute inset-0 p-12 flex flex-col justify-end pb-20">
                <div id="slideText" class="max-w-lg transition-all duration-700 ease-in-out" style="opacity: 1; transform: translateY(0);">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gold-400/20 border border-gold-400/30 text-gold-300 text-xs font-bold uppercase tracking-wider mb-4">
                        <span class="w-2 h-2 rounded-full bg-gold-400 animate-pulse"></span>
                        ASYX ERP Platform
                    </div>
                    <h2 id="slideTitle" class="text-5xl font-extrabold text-white leading-tight">Powerful Business Operations</h2>
                    <p id="slideDesc" class="mt-4 text-white/90 text-lg leading-relaxed">Streamline your entire organization with one integrated ERP system built for growth.</p>

                    {{-- Dynamic feature card --}}
                    <div id="slideFeature" class="mt-8 glass rounded-xl p-5 max-w-sm">
                        <div class="flex items-start gap-4">
                            <div id="slideIcon" class="w-12 h-12 rounded-lg bg-gold-400/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <div>
                                <p id="slideFeatureTitle" class="text-white font-bold text-base">Business Intelligence</p>
                                <p id="slideFeatureDesc" class="text-emerald-100 text-sm mt-1">Real-time dashboards & reports across all companies.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide indicators --}}
            <div class="absolute bottom-10 right-12 flex gap-2">
                <button type="button" class="slide-dot w-2.5 h-2.5 rounded-full bg-white/40 transition-all duration-300" data-index="0"></button>
                <button type="button" class="slide-dot w-2.5 h-2.5 rounded-full bg-white/40 transition-all duration-300" data-index="1"></button>
                <button type="button" class="slide-dot w-2.5 h-2.5 rounded-full bg-white/40 transition-all duration-300" data-index="2"></button>
                <button type="button" class="slide-dot w-2.5 h-2.5 rounded-full bg-white/40 transition-all duration-300" data-index="3"></button>
            </div>

            <div class="absolute bottom-8 left-12 text-emerald-200/70 text-xs">
                &copy; {{ date('Y') }} ASYX Group. All rights reserved.
            </div>
        </div>

        {{-- Right side: auth card --}}
        <div class="w-full lg:w-1/2 min-h-screen flex items-center justify-center p-6 sm:p-12 relative overflow-y-auto bg-gradient-to-br from-gray-50 via-white to-emerald-50/40">
            <div class="absolute inset-0" style="background-image: radial-gradient(rgba(2,73,56,0.06) 2px, transparent 2.5px); background-size: 18px 18px;"></div>
            <div class="absolute top-[-10%] right-[-10%] w-[400px] h-[400px] bg-emerald-500/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-[-10%] left-[-10%] w-[400px] h-[400px] bg-gold-500/5 rounded-full blur-3xl"></div>

            @yield('content')
        </div>
    </main>

    <script>
    // SweetAlert2 side toasts
    (function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        @if(session('status'))
            Toast.fire({ icon: 'success', title: '{{ session('status') }}' });
        @endif
        @if(session('error'))
            Toast.fire({ icon: 'error', title: '{{ session('error') }}' });
        @endif
        @if(session('warning'))
            Toast.fire({ icon: 'warning', title: '{{ session('warning') }}' });
        @endif
        @if(session('info'))
            Toast.fire({ icon: 'info', title: '{{ session('info') }}' });
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                Toast.fire({ icon: 'error', title: '{{ $error }}' });
            @endforeach
        @endif
    })();

    // Button loading state on all auth forms
    (function() {
        const authMain = document.getElementById('authMain');
        if (!authMain) return;

        authMain.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = form.querySelector('button[type="submit"]');
                if (!btn) return;

                btn.disabled = true;
                const original = btn.innerHTML;
                btn.setAttribute('data-original', original);
                btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-900 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';
                btn.classList.add('cursor-not-allowed', 'opacity-90');
            });
        });
    })();

    // Animate.css entrance animations for auth card
    (function() {
        const card = document.querySelector('#authMain .max-w-md');
        if (!card) return;

        card.style.animation = 'none';
        card.classList.add('auth-card-entrance');

        const header = card.querySelector('.bg-gradient-to-br');
        if (header) header.classList.add('auth-header-entrance');

        const fields = card.querySelectorAll('form > div, form > button');
        fields.forEach((field, i) => {
            field.classList.add('auth-field-entrance');
            field.style.animationDelay = (0.3 + (i * 0.08)) + 's';
        });

        const submitBtn = card.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.classList.add('auth-btn');
    })();

    // Auth slideshow - rotating text over background image
    (function() {
        const slides = [
            {
                title: 'Powerful Business Operations',
                desc: 'Streamline your entire organization with one integrated ERP system built for growth.',
                featureTitle: 'Business Intelligence',
                featureDesc: 'Real-time dashboards & reports across all companies.',
                icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'
            },
            {
                title: 'Multi-Role Workforce',
                desc: 'Every role gets the right tools — from finance and HR to operations and technical teams.',
                featureTitle: 'Role-Based Access',
                featureDesc: 'Permissions, dashboards, and workflows tailored per role.',
                icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'
            },
            {
                title: 'Secure & Reliable',
                desc: 'Enterprise-grade security, audit trails, and controlled access keep your data protected.',
                featureTitle: 'System Security',
                featureDesc: 'Authentication, audit logs, and permission controls.',
                icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
            },
            {
                title: 'Group Companies Ready',
                desc: 'Manage multiple subsidiaries, consolidated reports, and intercompany operations with ease.',
                featureTitle: 'Consolidation',
                featureDesc: 'Group view, company switching, and combined reports.',
                icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'
            }
        ];

        const slideTitle = document.getElementById('slideTitle');
        const slideDesc = document.getElementById('slideDesc');
        const slideFeatureTitle = document.getElementById('slideFeatureTitle');
        const slideFeatureDesc = document.getElementById('slideFeatureDesc');
        const slideIcon = document.querySelector('#slideIcon svg path');
        const slideText = document.getElementById('slideText');
        const dots = document.querySelectorAll('.slide-dot');

        let current = 0;
        const interval = 5000;
        let timer;

        function updateDots(index) {
            dots.forEach((dot, i) => {
                dot.classList.toggle('bg-white', i === index);
                dot.classList.toggle('w-6', i === index);
                dot.classList.toggle('bg-white/40', i !== index);
                dot.classList.toggle('w-2.5', i !== index);
            });
        }

        function showSlide(index) {
            if (!slideText) return;
            current = index;

            slideText.style.opacity = '0';
            slideText.style.transform = 'translateY(16px)';

            setTimeout(() => {
                const s = slides[index];
                if (slideTitle) slideTitle.textContent = s.title;
                if (slideDesc) slideDesc.textContent = s.desc;
                if (slideFeatureTitle) slideFeatureTitle.textContent = s.featureTitle;
                if (slideFeatureDesc) slideFeatureDesc.textContent = s.featureDesc;
                if (slideIcon) slideIcon.setAttribute('d', s.icon);

                slideText.style.opacity = '1';
                slideText.style.transform = 'translateY(0)';
                updateDots(index);
            }, 400);
        }

        function nextSlide() {
            showSlide((current + 1) % slides.length);
        }

        dots.forEach((dot, i) => {
            dot.addEventListener('click', () => {
                showSlide(i);
                resetTimer();
            });
        });

        function resetTimer() {
            clearInterval(timer);
            timer = setInterval(nextSlide, interval);
        }

        updateDots(0);
        timer = setInterval(nextSlide, interval);
    })();
    </script>
</body>
</html>
