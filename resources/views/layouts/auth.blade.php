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
        .ajax-loader { position:fixed; top:0; left:0; right:0; height:3px; background: linear-gradient(90deg, #5A0917, #F6891F, #5A0917); background-size: 200% 100%; animation: ajaxProgress 1s linear infinite; z-index:9999; display:none; }
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
                        emerald: { 50:'#FBEDEF',100:'#F5D0D6',200:'#E8A1AD',300:'#D4738A',400:'#C55B6E',500:'#7C1528',600:'#6B0F22',700:'#5A0917',800:'#4A0712',900:'#3A050E' },
                        gold: { 50:'#FFF4E6',100:'#FFE0BF',200:'#FCC78F',300:'#F9A54E',400:'#F6891F',500:'#D66F0E',600:'#B85A0A',700:'#9A4808',800:'#7A3D05',900:'#5C2E03' }
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
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/90 via-emerald-800/70 to-emerald-950/90"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/95 via-transparent to-emerald-900/50"></div>
            <div class="absolute inset-0 brand-bg opacity-40"></div>

            {{-- Floating orbs --}}
            <div class="absolute top-1/4 left-10 w-64 h-64 bg-gold-400/10 rounded-full blur-3xl animate-pulse" style="animation-duration: 6s;"></div>
            <div class="absolute bottom-1/4 right-10 w-80 h-80 bg-emerald-400/8 rounded-full blur-3xl animate-pulse" style="animation-duration: 8s;"></div>

            {{-- Top brand bar --}}
            <div class="absolute top-0 left-0 right-0 p-8 flex items-center justify-between z-10">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20">
                        <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX" class="w-8 h-8 object-contain">
                    </div>
                    <div>
                        <span class="text-white font-extrabold text-lg tracking-tight">ASYX</span><span class="text-gold-400 font-extrabold text-lg tracking-tight"> GROUP</span>
                        <p class="text-emerald-200/60 text-[10px] font-medium tracking-wide uppercase">Enterprise Resource Planning</p>
                    </div>
                </div>
                <div class="glass rounded-full px-4 py-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-emerald-100 text-[10px] font-semibold uppercase tracking-wider">System Online</span>
                </div>
            </div>

            {{-- Slide text content --}}
            <div class="absolute inset-0 p-12 flex flex-col justify-end pb-20 z-10">
                <div id="slideText" class="max-w-lg transition-all duration-700 ease-in-out" style="opacity: 1; transform: translateY(0);">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gold-400/20 border border-gold-400/30 text-gold-300 text-xs font-bold uppercase tracking-wider mb-5">
                        <span class="w-2 h-2 rounded-full bg-gold-400 animate-pulse"></span>
                        ASYX ERP Platform
                    </div>
                    <h2 id="slideTitle" class="text-5xl font-extrabold text-white leading-tight tracking-tight">Powerful Business Operations</h2>
                    <p id="slideDesc" class="mt-4 text-white/80 text-lg leading-relaxed font-light">Streamline your entire organization with one integrated ERP system built for growth.</p>

                    {{-- Dynamic feature card --}}
                    <div id="slideFeature" class="mt-8 glass rounded-2xl p-5 max-w-sm">
                        <div class="flex items-start gap-4">
                            <div id="slideIcon" class="w-12 h-12 rounded-xl bg-gold-400/20 flex items-center justify-center flex-shrink-0 border border-gold-400/20">
                                <svg class="w-6 h-6 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <div>
                                <p id="slideFeatureTitle" class="text-white font-bold text-base">Business Intelligence</p>
                                <p id="slideFeatureDesc" class="text-emerald-100/70 text-sm mt-1 font-light">Real-time dashboards & reports across all companies.</p>
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
                <button type="button" class="slide-dot w-2.5 h-2.5 rounded-full bg-white/40 transition-all duration-300" data-index="4"></button>
                <button type="button" class="slide-dot w-2.5 h-2.5 rounded-full bg-white/40 transition-all duration-300" data-index="5"></button>
                <button type="button" class="slide-dot w-2.5 h-2.5 rounded-full bg-white/40 transition-all duration-300" data-index="6"></button>
                <button type="button" class="slide-dot w-2.5 h-2.5 rounded-full bg-white/40 transition-all duration-300" data-index="7"></button>
                <button type="button" class="slide-dot w-2.5 h-2.5 rounded-full bg-white/40 transition-all duration-300" data-index="8"></button>
                <button type="button" class="slide-dot w-2.5 h-2.5 rounded-full bg-white/40 transition-all duration-300" data-index="9"></button>
            </div>

            <div class="absolute bottom-8 left-12 text-emerald-200/50 text-xs z-10">
                &copy; {{ date('Y') }} ASYX Group. All rights reserved.
            </div>
        </div>

        {{-- Right side: auth card --}}
        <div class="w-full lg:w-1/2 min-h-screen flex items-center justify-center p-6 sm:p-12 relative overflow-y-auto bg-gradient-to-br from-gray-50 via-white to-emerald-50/30">
            <div class="absolute inset-0" style="background-image: radial-gradient(rgba(90,9,23,0.04) 2px, transparent 2.5px); background-size: 20px 20px;"></div>
            <div class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-emerald-500/4 rounded-full blur-3xl"></div>
            <div class="absolute bottom-[-10%] left-[-10%] w-[500px] h-[500px] bg-gold-500/4 rounded-full blur-3xl"></div>

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
                title: 'Welcome to ASYX ERP',
                desc: 'Your all-in-one enterprise platform — managing operations, finance, HR, projects, and more across the entire ASYX Group.',
                featureTitle: 'Integrated Platform',
                featureDesc: 'One system. Every department. Total visibility.',
                icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'
            },
            {
                title: 'Excellence in Service Delivery',
                desc: 'From CCTV installations to IT infrastructure, we deliver quality solutions that keep Tanzania connected and secure.',
                featureTitle: 'Our Mission',
                featureDesc: 'Reliable infrastructure. Trusted partnerships. Lasting impact.',
                icon: 'M13 10V3L4 14h7v7l9-11h-7z'
            },
            {
                title: 'Your Role, Your Dashboard',
                desc: 'Every employee gets a tailored experience — with modules, tools, and insights designed specifically for their role.',
                featureTitle: 'Role-Based Access',
                featureDesc: 'Personalized dashboards for finance, HR, technical, operations, and more.',
                icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'
            },
            {
                title: 'Real-Time Business Intelligence',
                desc: 'Live dashboards and comprehensive reports give you the data you need to make informed decisions — anytime, anywhere.',
                featureTitle: 'Smart Analytics',
                featureDesc: 'Track performance, monitor KPIs, and spot trends across all companies.',
                icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'
            },
            {
                title: 'Multi-Company Management',
                desc: 'Seamlessly manage multiple subsidiaries with consolidated reports, intercompany transactions, and unified operations.',
                featureTitle: 'Group Consolidation',
                featureDesc: 'Switch between companies, view group-wide performance, and streamline intercompany processes.',
                icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'
            },
            {
                title: 'Project Management Made Simple',
                desc: 'Plan, track, and deliver projects on time and within budget — from tenders and quotations to final delivery and invoicing.',
                featureTitle: 'Complete Project Lifecycle',
                featureDesc: 'Tenders, budgets, timesheets, tasks, milestones, and reporting — all in one place.',
                icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'
            },
            {
                title: 'Human Resources, Simplified',
                desc: 'Manage employees, attendance, leave, payroll, performance, recruitment, and training — all from a single, intuitive interface.',
                featureTitle: 'Complete HR Suite',
                featureDesc: 'From hiring to retirement — every employee lifecycle stage covered.',
                icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'
            },
            {
                title: 'Financial Control & Visibility',
                desc: 'Full accounting suite — invoices, expenses, budgets, bank reconciliation, tax management, and financial reporting at your fingertips.',
                featureTitle: 'Enterprise Accounting',
                featureDesc: 'Journal entries, petty cash, cost centres, payroll, and real-time financial reports.',
                icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
            },
            {
                title: 'Secure & Audited',
                desc: 'Enterprise-grade security with full audit trails, login history, and granular permission controls keep your data safe and compliant.',
                featureTitle: 'Bank-Grade Security',
                featureDesc: 'Authentication, role-based permissions, audit logs, and activity tracking.',
                icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
            },
            {
                title: 'Built for Tanzania, Ready for the World',
                desc: 'ASYX Group delivers technology solutions across telecommunications, security, IT, and infrastructure — proudly serving Tanzania and beyond.',
                featureTitle: 'Our Heritage',
                featureDesc: 'Local expertise. Global standards. Trusted by leading organizations.',
                icon: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
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
        const interval = 4500;
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
