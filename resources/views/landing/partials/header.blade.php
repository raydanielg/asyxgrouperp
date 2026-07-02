<header id="header" class="fixed top-3 left-0 right-0 z-50 px-4 transition-all duration-300">
    <div id="header-card" class="max-w-7xl mx-auto rounded-2xl border border-white/10 transition-all duration-300" style="background: rgba(27,58,92,0.85); backdrop-filter: blur(20px);">
        <div class="flex items-center justify-between px-5 py-3">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX Group Logo" class="h-12 md:h-14 w-auto object-contain group-hover:scale-105 transition-transform drop-shadow-lg">
            </a>

            {{-- Desktop Nav --}}
            <nav class="hidden lg:flex items-center gap-7">
                <a href="{{ route('home') }}" class="nav-link text-sm font-semibold text-white/90 hover:text-bronze transition-colors">Home</a>
                <a href="{{ route('about') }}" class="nav-link text-sm font-semibold text-white/90 hover:text-bronze transition-colors">About Us</a>
                <a href="{{ route('hosting') }}" class="nav-link text-sm font-semibold text-white/90 hover:text-bronze transition-colors">Hosting</a>
                <a href="{{ route('services') }}" class="nav-link text-sm font-semibold text-white/90 hover:text-bronze transition-colors">Services</a>
                <a href="{{ url('/careers') }}" class="nav-link text-sm font-semibold text-white/90 hover:text-bronze transition-colors">Careers</a>
                <a href="{{ route('contact') }}" class="nav-link text-sm font-semibold text-white/90 hover:text-bronze transition-colors">Contacts</a>
            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2.5 border border-white/20 text-white text-sm font-semibold rounded-lg hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Login
                </a>
                <a href="{{ route('contact') }}" class="hidden sm:inline-flex items-center gap-1.5 px-6 py-2.5 cta-gradient text-white text-sm font-bold rounded-lg shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                    Contact Us
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                {{-- Mobile toggle --}}
                <button id="mobileToggle" class="lg:hidden p-2 rounded-lg hover:bg-white/10 transition-colors" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobileMenu" class="hidden lg:hidden mt-2 max-w-7xl mx-auto rounded-2xl border border-white/10 overflow-hidden" style="background: rgba(27,58,92,0.95); backdrop-filter: blur(20px);">
        <div class="px-4 py-4 space-y-1">
            <a href="{{ route('home') }}" class="block px-4 py-2.5 text-sm font-semibold text-white/90 hover:bg-white/10 hover:text-bronze rounded-lg transition-colors" onclick="document.getElementById('mobileMenu').classList.add('hidden')">Home</a>
            <a href="{{ route('about') }}" class="block px-4 py-2.5 text-sm font-semibold text-white/90 hover:bg-white/10 hover:text-bronze rounded-lg transition-colors" onclick="document.getElementById('mobileMenu').classList.add('hidden')">About Us</a>
            <a href="{{ route('hosting') }}" class="block px-4 py-2.5 text-sm font-semibold text-white/90 hover:bg-white/10 hover:text-bronze rounded-lg transition-colors" onclick="document.getElementById('mobileMenu').classList.add('hidden')">Hosting</a>
            <a href="{{ route('services') }}" class="block px-4 py-2.5 text-sm font-semibold text-white/90 hover:bg-white/10 hover:text-bronze rounded-lg transition-colors" onclick="document.getElementById('mobileMenu').classList.add('hidden')">Services</a>
            <a href="{{ url('/careers') }}" class="block px-4 py-2.5 text-sm font-semibold text-white/90 hover:bg-white/10 hover:text-bronze rounded-lg transition-colors" onclick="document.getElementById('mobileMenu').classList.add('hidden')">Careers</a>
            <a href="{{ route('contact') }}" class="block px-4 py-2.5 text-sm font-semibold text-white/90 hover:bg-white/10 hover:text-bronze rounded-lg transition-colors" onclick="document.getElementById('mobileMenu').classList.add('hidden')">Contacts</a>
            <div class="pt-2 flex gap-2">
                <a href="{{ route('login') }}" class="flex-1 block px-4 py-2.5 text-sm font-semibold text-white border border-white/20 rounded-lg text-center hover:bg-white/10 transition-colors">Login</a>
                <a href="{{ route('contact') }}" class="flex-1 block px-4 py-2.5 text-sm font-bold text-white cta-gradient rounded-lg text-center">Contact Us</a>
            </div>
        </div>
    </div>
</header>

<script>
(function() {
    var header = document.getElementById('header-card');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 30) {
            header.classList.add('header-scrolled');
            header.style.background = 'rgba(27,58,92,0.98)';
        } else {
            header.classList.remove('header-scrolled');
            header.style.background = 'rgba(27,58,92,0.85)';
        }
    });
})();
</script>