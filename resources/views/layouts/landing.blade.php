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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        .animate-fade-up { animation: fadeInUp 0.6s ease-out both; }
        .animate-fade { animation: fadeIn 0.8s ease-out both; }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .hero-gradient { background: linear-gradient(135deg, #1B3A5C 0%, #163049 50%, #112637 100%); }
        .cta-gradient { background: linear-gradient(135deg, #C81E3A 0%, #5B2A6E 100%); }
        .text-gradient { background: linear-gradient(135deg, #C81E3A, #5B2A6E); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .glass { background: rgba(255,255,255,0.08); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.12); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4,0,0.2,1); }
        .card-hover:hover { transform: translateY(-6px); box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15); }
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
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
    </style>
</head>
<body class="font-sans antialiased bg-white text-[#222222] overflow-x-hidden">

    @yield('content')

    @stack('scripts')
</body>
</html>
