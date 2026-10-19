<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('shield.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('shield.png') }}">

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
        @keyframes floatOrb { 0%,100% { transform:translateY(0) scale(1); } 50% { transform:translateY(-20px) scale(1.05); } }
        .auth-card-entrance { animation: fadeInUp 0.7s cubic-bezier(0.16,1,0.3,1) both; }
        .auth-header-entrance { animation: fadeInDown 0.6s cubic-bezier(0.16,1,0.3,1) 0.2s both; }
        .auth-field-entrance { animation: simpleFadeIn 0.5s cubic-bezier(0.16,1,0.3,1) both; }
        .auth-btn:hover { animation: pulse 0.8s ease-in-out infinite; }
        .ajax-loader { position:fixed; top:0; left:0; right:0; height:3px; background: linear-gradient(90deg, #5A0917, #F6891F, #5A0917); background-size: 200% 100%; animation: ajaxProgress 1s linear infinite; z-index:9999; display:none; }
        @keyframes ajaxProgress { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }
        .page-transition { animation: simpleFadeIn 0.35s ease-out both; }
        .brand-bg { background-image: radial-gradient(rgba(255,255,255,0.12) 2px, transparent 2.5px); background-size: 22px 22px; }
        .glass { background: rgba(255,255,255,0.08); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.12); }
        .glass-strong { background: rgba(255,255,255,0.12); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.18); }
        @keyframes kenBurns { 0% { transform:scale(1) translate(0,0); } 100% { transform:scale(1.12) translate(-2%,-2%); } }
        .slide-image { animation: kenBurns 16s ease-in-out infinite alternate; }
        .float-anim { animation: floatOrb 8s ease-in-out infinite; }
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

    {{-- Full-screen background photo --}}
    <div class="fixed inset-0 overflow-hidden">
        <img src="{{ asset('view-city-square.jpg') }}" alt="" class="w-full h-full object-cover slide-image">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-950/80 via-emerald-900/55 to-black/75"></div>
        <div class="absolute inset-0 brand-bg opacity-15"></div>
        <div class="absolute inset-0" style="background: radial-gradient(ellipse at center, transparent 0%, rgba(0,0,0,0.5) 100%);"></div>
        <div class="absolute top-1/3 left-[-8%] w-[420px] h-[420px] bg-gold-400/10 rounded-full blur-3xl float-anim"></div>
        <div class="absolute bottom-[-10%] right-[-8%] w-[480px] h-[480px] bg-emerald-400/10 rounded-full blur-3xl float-anim" style="animation-delay: 3s;"></div>
    </div>

    <main id="authMain" class="relative min-h-screen w-full flex flex-col">
        {{-- Auth card docked to the right over the photo --}}
        <div class="flex-1 flex items-center justify-center lg:justify-end px-5 py-8 lg:pr-16 xl:pr-24">
            @yield('content')
        </div>

        {{-- Footer --}}
        <div class="w-full px-6 sm:px-8 pb-6 flex items-end justify-between text-white/40 text-xs">
            <p>&copy; {{ date('Y') }} ASYX Group Company Limited. All rights reserved.</p>
        </div>
    </main>

    {{-- Minimal toast container --}}
    <div id="toastContainer" class="fixed top-5 right-5 z-[70] flex flex-col gap-2 w-full max-w-sm pointer-events-none"></div>
    <style>
        @keyframes authToastIn { from { opacity:0; transform:translateX(24px); } to { opacity:1; transform:translateX(0); } }
        @keyframes authToastOut { from { opacity:1; } to { opacity:0; transform:translateX(24px); } }
        .auth-toast-in { animation: authToastIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
        .auth-toast-out { animation: authToastOut 0.25s ease-in both; }
    </style>
    <script>
    // Minimal side toasts
    (function() {
        const container = document.getElementById('toastContainer');
        function fire(icon, title) {
            const dot = { success: '#10b981', error: '#ef4444', warning: '#f59e0b', info: '#3b82f6' }[icon] || '#6b7280';
            const toast = document.createElement('div');
            toast.className = 'auth-toast-in pointer-events-auto flex items-center gap-2.5 px-4 py-3 rounded-md bg-white border border-gray-100 shadow-md';
            toast.innerHTML =
                '<span class="flex-shrink-0" style="width:7px;height:7px;border-radius:99px;background:' + dot + '"></span>' +
                '<p class="flex-1 min-w-0 text-[13px] text-gray-700 leading-snug">' + title + '</p>' +
                '<button onclick="this.parentElement.classList.add(\'auth-toast-out\'); setTimeout(()=>this.parentElement.remove(), 250)" class="flex-shrink-0 -mr-1 text-gray-300 hover:text-gray-500 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
            container.appendChild(toast);
            setTimeout(() => { toast.classList.add('auth-toast-out'); setTimeout(() => toast.remove(), 250); }, 4000);
        }

        @if(session('status'))
            fire('success', '{{ session('status') }}');
        @endif
        @if(session('error'))
            fire('error', '{{ session('error') }}');
        @endif
        @if(session('warning'))
            fire('warning', '{{ session('warning') }}');
        @endif
        @if(session('info'))
            fire('info', '{{ session('info') }}');
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                fire('error', '{{ $error }}');
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

    </script>
</body>
</html>
