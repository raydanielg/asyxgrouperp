<header id="header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" data-scrolled="false">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 md:h-20">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                    <span class="text-white font-black text-lg">A</span>
                </div>
                <div class="flex flex-col leading-none">
                    <span class="font-black text-lg text-gray-900">ASYX<span class="text-emerald-500">Group</span></span>
                    <span class="text-[10px] font-medium text-gray-400 tracking-wider uppercase">Enterprise Suite</span>
                </div>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden lg:flex items-center gap-8">
                <a href="#home" class="nav-link text-sm font-semibold text-gray-700 hover:text-emerald-600 transition-colors">Home</a>
                <a href="#features" class="nav-link text-sm font-semibold text-gray-700 hover:text-emerald-600 transition-colors">Features</a>
                <a href="#modules" class="nav-link text-sm font-semibold text-gray-700 hover:text-emerald-600 transition-colors">Modules</a>
                <a href="#about" class="nav-link text-sm font-semibold text-gray-700 hover:text-emerald-600 transition-colors">About</a>
                <a href="#contact" class="nav-link text-sm font-semibold text-gray-700 hover:text-emerald-600 transition-colors">Contact</a>
            </div>

            {{-- CTA Buttons --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="hidden sm:inline-flex px-5 py-2 text-sm font-bold text-emerald-700 hover:text-emerald-800 transition-colors">
                    Sign In
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all hover:-translate-y-0.5">
                    Get Started
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                {{-- Mobile toggle --}}
                <button id="mobileToggle" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </nav>

    {{-- Mobile Menu --}}
    <div id="mobileMenu" class="hidden lg:hidden border-t border-gray-100 bg-white/95 backdrop-blur-lg">
        <div class="px-4 py-4 space-y-1">
            <a href="#home" class="block px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg transition-colors" onclick="document.getElementById('mobileMenu').classList.add('hidden')">Home</a>
            <a href="#features" class="block px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg transition-colors" onclick="document.getElementById('mobileMenu').classList.add('hidden')">Features</a>
            <a href="#modules" class="block px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg transition-colors" onclick="document.getElementById('mobileMenu').classList.add('hidden')">Modules</a>
            <a href="#about" class="block px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg transition-colors" onclick="document.getElementById('mobileMenu').classList.add('hidden')">About</a>
            <a href="#contact" class="block px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg transition-colors" onclick="document.getElementById('mobileMenu').classList.add('hidden')">Contact</a>
            <a href="{{ route('login') }}" class="block px-4 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-lg text-center mt-2">Sign In</a>
        </div>
    </div>
</header>

<script>
    window.addEventListener('scroll', function() {
        var header = document.getElementById('header');
        if (window.scrollY > 20) {
            header.classList.add('bg-white/95', 'backdrop-blur-lg', 'shadow-md');
            header.setAttribute('data-scrolled', 'true');
        } else {
            header.classList.remove('bg-white/95', 'backdrop-blur-lg', 'shadow-md');
            header.setAttribute('data-scrolled', 'false');
        }
    });
</script>
