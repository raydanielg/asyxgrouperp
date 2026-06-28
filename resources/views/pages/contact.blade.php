@extends('layouts.landing')

@section('title', 'Contact Us - ASYX Group')

@section('content')
    @include('landing.partials.header')

    {{-- Page hero --}}
    <section class="hero-gradient relative pt-32 pb-20 overflow-hidden">
        <div class="absolute inset-0 opacity-15">
            <img src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=1920&q=80" alt="Corporate office" class="w-full h-full object-cover">
        </div>
        <div class="absolute inset-0 bg-gradient-to-br from-navy via-navy/90 to-purple/20"></div>
        <div class="absolute top-20 right-10 w-72 h-72 bg-purple/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-crimson/10 rounded-full blur-3xl"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block px-4 py-1.5 rounded-full glass text-bronze text-xs font-bold uppercase tracking-wider mb-4">06 Contact Us</span>
            <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-4">
                Get in <span class="text-gradient">Touch</span>
            </h1>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto leading-relaxed">
                Let's build something reliable together. Reach out to discuss your mission-critical technology needs.
            </p>
        </div>
    </section>

    {{-- Contact section --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12">
                {{-- Left: Contact form --}}
                <div>
                    <h2 class="font-heading text-2xl sm:text-3xl font-black text-navy mb-6 section-title">Send Us a Message</h2>
                    <form action="#" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-navy mb-2">Full Name</label>
                            <input type="text" name="name" required placeholder="Your full name" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-bronze focus:ring-2 focus:ring-bronze/20 outline-none transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy mb-2">Email Address</label>
                            <input type="email" name="email" required placeholder="you@example.com" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-bronze focus:ring-2 focus:ring-bronze/20 outline-none transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy mb-2">Subject</label>
                            <input type="text" name="subject" required placeholder="How can we help?" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-bronze focus:ring-2 focus:ring-bronze/20 outline-none transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy mb-2">Message</label>
                            <textarea name="message" rows="5" required placeholder="Tell us about your project or inquiry..." class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-bronze focus:ring-2 focus:ring-bronze/20 outline-none transition-all text-sm resize-none"></textarea>
                        </div>
                        <button type="submit" class="btn-primary w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 text-base">
                            Send Message
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </form>
                </div>

                {{-- Right: Contact info + Map --}}
                <div>
                    <h2 class="font-heading text-2xl sm:text-3xl font-black text-navy mb-6 section-title">Contact Information</h2>

                    <div class="space-y-5 mb-8">
                        <div class="flex items-start gap-4 bg-[#F2F2F2] rounded-xl p-5">
                            <div class="w-12 h-12 rounded-xl bg-bronze/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-heading font-bold text-navy text-sm mb-1">Office Address</h4>
                                <p class="text-sm text-gray-600">Tropical Center, New Bagamoyo Road<br>Dar es Salaam, Tanzania</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 bg-[#F2F2F2] rounded-xl p-5">
                            <div class="w-12 h-12 rounded-xl bg-purple/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-heading font-bold text-navy text-sm mb-1">Phone</h4>
                                <p class="text-sm text-gray-600">+255 22 000 0000</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 bg-[#F2F2F2] rounded-xl p-5">
                            <div class="w-12 h-12 rounded-xl bg-crimson/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-crimson" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-heading font-bold text-navy text-sm mb-1">Email</h4>
                                <p class="text-sm text-gray-600">info@asyxgroup.co.tz</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 bg-[#F2F2F2] rounded-xl p-5">
                            <div class="w-12 h-12 rounded-xl bg-navy/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-heading font-bold text-navy text-sm mb-1">Office Hours</h4>
                                <p class="text-sm text-gray-600">Monday - Friday: 8:00 AM - 5:00 PM<br>Saturday: 8:00 AM - 1:00 PM</p>
                            </div>
                        </div>
                    </div>

                    {{-- Map --}}
                    <div class="rounded-2xl overflow-hidden shadow-lg">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.951234567890!2d39.2667!3d-6.8235!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwNDknMjQuNiJTIDM5wrAxNiczMC4xIkU!5e0!3m2!1sen!2stz!4v1700000000000!5m2!1sen!2stz"
                            width="100%"
                            height="300"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('landing.partials.footer')
@endsection
