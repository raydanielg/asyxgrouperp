<footer id="contact-info" class="bg-navy text-gray-300 pt-20 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 mb-12">
            {{-- Brand --}}
            <div class="col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX Group Logo" class="h-14 w-auto object-contain drop-shadow-lg">
                </div>
                <p class="text-sm leading-relaxed max-w-sm mb-6">
                    Since 2009, ASYX Group has been the trusted technology partner behind Tanzania's most critical systems - from power utilities to public transport to financial regulation.
                </p>
                <div class="flex items-center gap-3">
                    <a href="#" class="w-9 h-9 rounded-lg bg-white/5 hover:bg-bronze flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-white/5 hover:bg-bronze flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-white/5 hover:bg-bronze flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Services --}}
            <div>
                <h4 class="font-heading text-white font-bold text-sm mb-4">Services</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('services') }}" class="hover:text-bronze transition-colors">Smart Technology</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-bronze transition-colors">Telematics</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-bronze transition-colors">Cyber Security</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-bronze transition-colors">ICT Infrastructure</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-bronze transition-colors">Managed Services</a></li>
                </ul>
            </div>

            {{-- Company --}}
            <div>
                <h4 class="font-heading text-white font-bold text-sm mb-4">Company</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-bronze transition-colors">About Us</a></li>
                    <li><a href="{{ route('why-asyx') }}" class="hover:text-bronze transition-colors">Why ASYX</a></li>
                    <li><a href="{{ route('sectors') }}" class="hover:text-bronze transition-colors">Sectors &amp; Clients</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-bronze transition-colors">Contact Us</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h4 class="font-heading text-white font-bold text-sm mb-4">Get in Touch</h4>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Tropical Center, New Bagamoyo Road, Dar es Salaam, Tanzania</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>+255 22 000 0000</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>info@asyxgroup.co.tz</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-bronze mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Mon - Fri: 8:00 AM - 5:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-gray-400">
                &copy; {{ date('Y') }} ASYX Group. All rights reserved. Let's Achieve Together.
            </p>
            <div class="flex items-center gap-6 text-xs text-gray-400">
                <a href="#" class="hover:text-bronze transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-bronze transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
