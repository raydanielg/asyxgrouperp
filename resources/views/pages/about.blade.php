@extends('layouts.landing')

@section('title', 'About Us - ASYX Group')

@push('styles')
<style>
    @keyframes about-fade-up { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
    @keyframes about-fade-left { from { opacity:0; transform:translateX(-40px); } to { opacity:1; transform:translateX(0); } }
    @keyframes about-fade-right { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
    @keyframes about-scale-in { from { opacity:0; transform:scale(0.9); } to { opacity:1; transform:scale(1); } }
    @keyframes about-float { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-12px); } }
    @keyframes about-pulse-glow { 0%,100% { box-shadow:0 0 0 0 rgba(168,112,58,0.3); } 50% { box-shadow:0 0 30px 8px rgba(168,112,58,0.15); } }
    @keyframes about-line-grow { from { height:0; } to { height:100%; } }
    @keyframes about-shimmer { 0% { background-position:-200% 0; } 100% { background-position:200% 0; } }

    .about-reveal { opacity:0; }
    .about-reveal.revealed { animation: about-fade-up 0.8s cubic-bezier(0.16,1,0.3,1) forwards; }
    .about-reveal-left { opacity:0; }
    .about-reveal-left.revealed { animation: about-fade-left 0.8s cubic-bezier(0.16,1,0.3,1) forwards; }
    .about-reveal-right { opacity:0; }
    .about-reveal-right.revealed { animation: about-fade-right 0.8s cubic-bezier(0.16,1,0.3,1) forwards; }
    .about-reveal-scale { opacity:0; }
    .about-reveal-scale.revealed { animation: about-scale-in 0.6s cubic-bezier(0.16,1,0.3,1) forwards; }
    .about-stagger-1.revealed { animation-delay: 0.1s; }
    .about-stagger-2.revealed { animation-delay: 0.2s; }
    .about-stagger-3.revealed { animation-delay: 0.3s; }
    .about-stagger-4.revealed { animation-delay: 0.4s; }
    .about-stagger-5.revealed { animation-delay: 0.5s; }

    .about-hero-bg { background: linear-gradient(135deg, #0D3E63 0%, #0A2E4A 40%, #0D3E63 100%); }
    .about-hero-grid {
        background-image: linear-gradient(rgba(250,248,244,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(250,248,244,0.04) 1px, transparent 1px);
        background-size: 50px 50px;
    }
    .about-stat-card { transition: all 0.4s cubic-bezier(0.16,1,0.3,1); }
    .about-stat-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(13,62,99,0.15); }
    .about-value-card { transition: all 0.4s cubic-bezier(0.16,1,0.3,1); position: relative; overflow: hidden; }
    .about-value-card::before {
        content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px;
        background: linear-gradient(90deg, #A56035, #EC2226, #632871);
        transform: scaleX(0); transform-origin: left; transition: transform 0.5s cubic-bezier(0.16,1,0.3,1);
    }
    .about-value-card:hover::before { transform: scaleX(1); }
    .about-value-card:hover { transform: translateY(-4px); box-shadow: 0 16px 32px rgba(13,62,99,0.12); }
    .about-timeline-line { position: absolute; left: 31px; top: 0; width: 2px; background: linear-gradient(to bottom, #A56035, #0D3E63, #632871, #EC2226); animation: about-line-grow 1.5s ease-out forwards; }
    .about-timeline-dot { animation: about-pulse-glow 3s ease-in-out infinite; }
    .about-service-pill { transition: all 0.35s cubic-bezier(0.16,1,0.3,1); }
    .about-service-pill:hover { transform: translateY(-4px) scale(1.02); box-shadow: 0 12px 28px rgba(13,62,99,0.15); }
    .about-shimmer-text {
        background: linear-gradient(90deg, #FAF8F4 0%, #A56035 25%, #EC2226 50%, #A56035 75%, #FAF8F4 100%);
        background-size: 200% auto; -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
        animation: about-shimmer 4s linear infinite;
    }
    .about-sector-card { transition: all 0.4s cubic-bezier(0.16,1,0.3,1); }
    .about-sector-card:hover { transform: translateY(-4px); border-color: #A56035; }
    .about-float { animation: about-float 4s ease-in-out infinite; }
    .about-counter { font-variant-numeric: tabular-nums; }
    @media (max-width: 640px) {
        .about-hero-stats { grid-template-columns: repeat(2, 1fr) !important; }
    }
</style>
@endpush

@section('content')
    @include('landing.partials.header')

    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden" style="min-height:90vh; display:flex; align-items:center; padding-top:100px; padding-bottom:60px;">
        {{-- Background image --}}
        <div style="position:absolute; inset:0; z-index:0;">
            <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?w=1920&q=80" alt="" style="width:100%; height:100%; object-fit:cover; opacity:0.15;">
        </div>
        {{-- Gradient overlay --}}
        <div style="position:absolute; inset:0; z-index:1; background:linear-gradient(135deg, rgba(13,62,99,0.95) 0%, rgba(10,46,74,0.92) 50%, rgba(13,62,99,0.88) 100%);"></div>
        {{-- Grid pattern --}}
        <div style="position:absolute; inset:0; z-index:1; background-image:linear-gradient(rgba(250,248,244,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(250,248,244,0.03) 1px, transparent 1px); background-size:50px 50px;"></div>
        {{-- Glow orbs --}}
        <div class="about-float" style="position:absolute; top:80px; right:80px; width:300px; height:300px; border-radius:50%; filter:blur(80px); background:rgba(99,40,113,0.2); z-index:1;"></div>
        <div style="position:absolute; bottom:40px; left:40px; width:350px; height:350px; border-radius:50%; filter:blur(80px); background:rgba(236,34,38,0.1); z-index:1; animation: about-float 5s ease-in-out infinite 1s;"></div>

        <div class="relative w-full" style="z-index:2; max-width:1100px; margin:0 auto; padding:0 24px;">
            <div style="text-align:center; max-width:720px; margin:0 auto;" class="about-reveal">
                {{-- Badge --}}
                <span style="display:inline-flex; align-items:center; gap:8px; padding:8px 18px; border-radius:100px; font-size:12px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; margin-bottom:28px; background:rgba(236,34,38,0.12); color:#FAF8F4; border:1px solid rgba(236,34,38,0.35);">
                    <span style="width:6px; height:6px; border-radius:50%; background:#EC2226; box-shadow:0 0 10px #EC2226;"></span>
                    About ASYX Group
                </span>

                {{-- Heading --}}
                <h1 style="font-family:'Space Grotesk',sans-serif; font-size:clamp(36px,5.5vw,60px); font-weight:700; line-height:1.1; margin-bottom:24px; color:#FAF8F4; letter-spacing:-0.02em;">
                    <span style="color:#EC2226;">16+</span> Years of<br>
                    <span class="about-shimmer-text">Trusted Service</span>
                </h1>

                {{-- Description --}}
                <p style="font-size:18px; line-height:1.65; margin-bottom:36px; max-width:560px; margin-left:auto; margin-right:auto; color:rgba(250,248,244,0.75);">
                    Since 2009, ASYX Group has been the trusted technology partner behind Tanzania's most critical systems. We deliver smart technologies, ICT infrastructure, cybersecurity, and managed services for mission-critical environments.
                </p>

                {{-- Buttons --}}
                <div style="display:flex; flex-wrap:wrap; justify-content:center; gap:16px; margin-bottom:56px;">
                    <a href="{{ route('contact') }}" style="display:inline-flex; align-items:center; gap:8px; padding:15px 30px; border-radius:100px; font-weight:600; font-size:15px; background:#EC2226; color:#FAF8F4; transition:all 0.3s ease; box-shadow:0 8px 24px rgba(236,34,38,0.3);" onmouseover="this.style.background='#FAF8F4'; this.style.color='#EC2226'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 32px rgba(236,34,38,0.4)';" onmouseout="this.style.background='#EC2226'; this.style.color='#FAF8F4'; this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 24px rgba(236,34,38,0.3)';">
                        Company Profile 2026 →
                    </a>
                    <a href="{{ route('services') }}" style="display:inline-flex; align-items:center; gap:8px; padding:15px 30px; border-radius:100px; font-weight:600; font-size:15px; background:transparent; color:#FAF8F4; border:2px solid rgba(250,248,244,0.25); transition:all 0.3s ease;" onmouseover="this.style.borderColor='#A56035'; this.style.color='#A56035'; this.style.transform='translateY(-3px)';" onmouseout="this.style.borderColor='rgba(250,248,244,0.25)'; this.style.color='#FAF8F4'; this.style.transform='translateY(0)';">
                        Our Services
                    </a>
                </div>
            </div>

            {{-- Stats bar --}}
            <div class="about-hero-stats about-reveal" style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; max-width:800px; margin:0 auto;">
                <div class="about-stat-card about-stagger-1" style="text-align:center; padding:24px 16px; border-radius:16px; background:rgba(250,248,244,0.06); border:1px solid rgba(250,248,244,0.1); backdrop-filter:blur(8px);">
                    <div class="about-counter" style="font-family:'Space Grotesk',sans-serif; font-size:32px; font-weight:700; color:#EC2226;" data-target="500" data-suffix="+">0</div>
                    <div style="font-size:11px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; margin-top:6px; color:rgba(250,248,244,0.6);">Projects Delivered</div>
                </div>
                <div class="about-stat-card about-stagger-2" style="text-align:center; padding:24px 16px; border-radius:16px; background:rgba(250,248,244,0.06); border:1px solid rgba(250,248,244,0.1); backdrop-filter:blur(8px);">
                    <div class="about-counter" style="font-family:'Space Grotesk',sans-serif; font-size:32px; font-weight:700; color:#A56035;" data-target="100" data-suffix="+">0</div>
                    <div style="font-size:11px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; margin-top:6px; color:rgba(250,248,244,0.6);">Enterprise Clients</div>
                </div>
                <div class="about-stat-card about-stagger-3" style="text-align:center; padding:24px 16px; border-radius:16px; background:rgba(250,248,244,0.06); border:1px solid rgba(250,248,244,0.1); backdrop-filter:blur(8px);">
                    <div class="about-counter" style="font-family:'Space Grotesk',sans-serif; font-size:32px; font-weight:700; color:#632871;" data-target="9" data-suffix="">0</div>
                    <div style="font-size:11px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; margin-top:6px; color:rgba(250,248,244,0.6);">Service Pillars</div>
                </div>
                <div class="about-stat-card about-stagger-4" style="text-align:center; padding:24px 16px; border-radius:16px; background:rgba(250,248,244,0.06); border:1px solid rgba(250,248,244,0.1); backdrop-filter:blur(8px);">
                    <div style="font-family:'Space Grotesk',sans-serif; font-size:32px; font-weight:700; color:#EC2226;">24/7</div>
                    <div style="font-size:11px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; margin-top:6px; color:rgba(250,248,244,0.6);">Support Coverage</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ COMPANY STORY ============ --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 about-reveal">
                <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-4" style="background:rgba(168,112,58,0.1); color:#A56035;">Our Journey</span>
                <h2 class="font-heading text-3xl sm:text-4xl font-black mb-4" style="color:#0D3E63;">Company Story</h2>
                <p class="text-gray-600 max-w-xl mx-auto">From a bold vision in 2009 to becoming Tanzania's most trusted technology partner.</p>
            </div>

            <div class="relative">
                <div class="about-timeline-line"></div>
                <div class="space-y-10">
                    <div class="flex gap-6 items-start about-reveal about-stagger-1">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg about-timeline-dot" style="background:linear-gradient(135deg,#A56035,#EC2226);">
                            <span class="font-heading font-black text-sm text-white">2009</span>
                        </div>
                        <div class="flex-1 rounded-2xl p-6 transition-all" style="background:#F8F7F4; border:1px solid rgba(13,62,99,0.06);" onmouseover="this.style.transform='translateX(6px)'; this.style.boxShadow='0 8px 24px rgba(13,62,99,0.08)';" onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='none';">
                            <h3 class="font-heading text-lg font-bold mb-2" style="color:#0D3E63;">Foundation</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">ASYX Group is founded with a vision to bring enterprise-grade technology solutions to Tanzania's public sector and regulated industries.</p>
                        </div>
                    </div>
                    <div class="flex gap-6 items-start about-reveal about-stagger-2">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg about-timeline-dot" style="background:#0D3E63; animation-delay:0.5s;">
                            <span class="font-heading font-black text-sm text-white">2013</span>
                        </div>
                        <div class="flex-1 rounded-2xl p-6 transition-all" style="background:#F8F7F4; border:1px solid rgba(13,62,99,0.06);" onmouseover="this.style.transform='translateX(6px)'; this.style.boxShadow='0 8px 24px rgba(13,62,99,0.08)';" onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='none';">
                            <h3 class="font-heading text-lg font-bold mb-2" style="color:#0D3E63;">Government Partnerships</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">Secured major contracts with TANESCO and TRA, establishing ASYX as a trusted government technology partner.</p>
                        </div>
                    </div>
                    <div class="flex gap-6 items-start about-reveal about-stagger-3">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg about-timeline-dot" style="background:#A56035; animation-delay:1s;">
                            <span class="font-heading font-black text-sm text-white">2018</span>
                        </div>
                        <div class="flex-1 rounded-2xl p-6 transition-all" style="background:#F8F7F4; border:1px solid rgba(13,62,99,0.06);" onmouseover="this.style.transform='translateX(6px)'; this.style.boxShadow='0 8px 24px rgba(13,62,99,0.08)';" onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='none';">
                            <h3 class="font-heading text-lg font-bold mb-2" style="color:#0D3E63;">Expansion &amp; Growth</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">Expanded service pillars to include telematics, cybersecurity, and managed services. Client roster grows to include NSSF, BOT, and Precision Air.</p>
                        </div>
                    </div>
                    <div class="flex gap-6 items-start about-reveal about-stagger-4">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg about-timeline-dot" style="background:linear-gradient(135deg,#632871,#EC2226); animation-delay:1.5s;">
                            <span class="font-heading font-black text-sm text-white">2026</span>
                        </div>
                        <div class="flex-1 rounded-2xl p-6 transition-all" style="background:#F8F7F4; border:1px solid rgba(13,62,99,0.06);" onmouseover="this.style.transform='translateX(6px)'; this.style.boxShadow='0 8px 24px rgba(13,62,99,0.08)';" onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='none';">
                            <h3 class="font-heading text-lg font-bold mb-2" style="color:#0D3E63;">Today &amp; Beyond</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">With 100+ enterprise clients and 9 service pillars, ASYX Group continues to power Tanzania's mission-critical systems with smart technology, secure infrastructure, and sustainable growth.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ VISION / MISSION ============ --}}
    <section class="py-20 lg:py-28" style="background:#F8F7F4;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 about-reveal">
                <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-4" style="background:rgba(168,112,58,0.1); color:#A56035;">What Drives Us</span>
                <h2 class="font-heading text-3xl sm:text-4xl font-black mb-4" style="color:#0D3E63;">Vision &amp; Mission</h2>
                <p class="text-gray-600 max-w-xl mx-auto">Leading technological innovation across Africa.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 mb-20">
                <div class="about-value-card about-reveal about-stagger-1 rounded-2xl p-8 bg-white shadow-sm" style="border-top:4px solid #0D3E63;">
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background:rgba(13,62,99,0.08);">
                            <svg class="w-7 h-7" style="color:#0D3E63;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <h3 class="font-heading text-xl font-bold" style="color:#0D3E63;">Our Vision</h3>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">To become the leading company in empowering African countries to realize the full potential of technology in driving sustainable economic growth.</p>
                </div>
                <div class="about-value-card about-reveal about-stagger-2 rounded-2xl p-8 bg-white shadow-sm" style="border-top:4px solid #A56035;">
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background:rgba(168,112,58,0.08);">
                            <svg class="w-7 h-7" style="color:#A56035;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="font-heading text-xl font-bold" style="color:#0D3E63;">Our Mission</h3>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">To introduce creative, innovative, and simplified technological solutions that are tailored to Africa's environmental and infrastructural challenges.</p>
                </div>
            </div>

            {{-- Core Values --}}
            <div class="text-center mb-12 about-reveal">
                <h3 class="font-heading text-2xl sm:text-3xl font-black mb-3" style="color:#0D3E63;">Our Core Values</h3>
                <p class="text-gray-600">The principles that guide everything we do.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="about-value-card about-reveal about-stagger-1 rounded-2xl p-6 bg-white shadow-sm text-center">
                    <div class="text-3xl font-black mb-3" style="color:rgba(168,112,58,0.2);">01</div>
                    <h4 class="font-heading font-bold mb-2" style="color:#0D3E63;">Professionalism</h4>
                    <p class="text-xs text-gray-600 leading-relaxed">Delivering services with competence, accountability, and excellence.</p>
                </div>
                <div class="about-value-card about-reveal about-stagger-2 rounded-2xl p-6 bg-white shadow-sm text-center">
                    <div class="text-3xl font-black mb-3" style="color:rgba(13,62,99,0.2);">02</div>
                    <h4 class="font-heading font-bold mb-2" style="color:#0D3E63;">Integrity</h4>
                    <p class="text-xs text-gray-600 leading-relaxed">Acting honestly, transparently, and ethically at all times.</p>
                </div>
                <div class="about-value-card about-reveal about-stagger-3 rounded-2xl p-6 bg-white shadow-sm text-center">
                    <div class="text-3xl font-black mb-3" style="color:rgba(99,40,113,0.2);">03</div>
                    <h4 class="font-heading font-bold mb-2" style="color:#0D3E63;">Innovation</h4>
                    <p class="text-xs text-gray-600 leading-relaxed">Embracing creativity, continuous improvement, and practical solutions.</p>
                </div>
                <div class="about-value-card about-reveal about-stagger-4 rounded-2xl p-6 bg-white shadow-sm text-center">
                    <div class="text-3xl font-black mb-3" style="color:rgba(236,34,38,0.2);">04</div>
                    <h4 class="font-heading font-bold mb-2" style="color:#0D3E63;">Teamwork</h4>
                    <p class="text-xs text-gray-600 leading-relaxed">Collaborating effectively to achieve shared goals.</p>
                </div>
                <div class="about-value-card about-reveal about-stagger-5 rounded-2xl p-6 bg-white shadow-sm text-center">
                    <div class="text-3xl font-black mb-3" style="color:rgba(168,112,58,0.2);">05</div>
                    <h4 class="font-heading font-bold mb-2" style="color:#0D3E63;">Compliance</h4>
                    <p class="text-xs text-gray-600 leading-relaxed">Adhering to applicable laws, standards, and governance requirements.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ PRODUCTS & SERVICES ============ --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 about-reveal">
                <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-4" style="background:rgba(168,112,58,0.1); color:#A56035;">What We Offer</span>
                <h2 class="font-heading text-3xl sm:text-4xl font-black mb-4" style="color:#0D3E63;">Products &amp; Services</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Explore ASYX's core service pillars, from smart technologies and cybersecurity to ICT infrastructure and managed services, designed for regulated and enterprise environments.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $services = [
                        ['Smart Technologies', 'IoT & smart systems for mission-critical operations', '#A56035'],
                        ['Telematics Solutions', 'GPS tracking, fleet management, and vehicle telemetry', '#0D3E63'],
                        ['Cyber Security', 'Enterprise-grade protection and threat management', '#EC2226'],
                        ['Software Solutions', 'Custom development and enterprise application integration', '#632871'],
                        ['ICT Infrastructure', 'Network design, systems deployment, and data center solutions', '#0D3E63'],
                        ['Hardware Distribution', 'Authorized partner for leading technology brands', '#A56035'],
                        ['Technical Support & Managed Services', '24/7 proactive monitoring and maintenance', '#EC2226'],
                        ['Labour Outsourcing', 'Skilled IT personnel deployment for client operations', '#632871'],
                        ['ICT Training', 'Professional certification and capacity building programs', '#0D3E63'],
                    ];
                @endphp
                @foreach ($services as $service)
                    <div class="about-service-pill about-reveal about-stagger-{{ ($loop->index % 5) + 1 }} rounded-2xl p-6 bg-white" style="border:1px solid rgba(13,62,99,0.08);">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:{{ $service[2] }}15;">
                                <div style="width:10px; height:10px; border-radius:50%; background:{{ $service[2] }};"></div>
                            </div>
                            <div>
                                <h4 class="font-heading font-bold mb-1" style="color:#0D3E63;">{{ $service[0] }}</h4>
                                <p class="text-xs text-gray-600 leading-relaxed">{{ $service[1] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-12 about-reveal">
                <a href="{{ route('services') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full font-semibold text-sm transition-all" style="background:#0D3E63; color:#FAF8F4;" onmouseover="this.style.background='#EC2226'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#0D3E63'; this.style.transform='translateY(0)';">
                    View All Services →
                </a>
            </div>
        </div>
    </section>

    {{-- ============ SECTOR EXPERIENCE ============ --}}
    <section class="py-20 lg:py-28" style="background:#F8F7F4;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 about-reveal">
                <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-4" style="background:rgba(168,112,58,0.1); color:#A56035;">Our Experience</span>
                <h2 class="font-heading text-3xl sm:text-4xl font-black mb-4" style="color:#0D3E63;">Sector Experience &amp; Clients</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">ASYX has delivered solutions for a wide range of institutions across regulated and high-accountability environments.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @php
                    $sectors = [
                        ['Energy & Utilities', 'TANESCO, EWURA, TPDC, GASCO', '#A56035'],
                        ['Transport & Aviation', 'TAA, TCAA, TANROADS, LATRA, Precision Air', '#0D3E63'],
                        ['Finance & Regulation', 'BOT, TRA, NSSF, PSSSF, NHIF', '#EC2226'],
                        ['Government & Public', 'Ministries, Agencies, Authorities, LGAs', '#632871'],
                        ['Education & Research', 'UDSM, VETA, NECTA, NIMR, TOSCI', '#0D3E63'],
                    ];
                @endphp
                @foreach ($sectors as $sector)
                    <div class="about-sector-card about-reveal about-stagger-{{ ($loop->index % 5) + 1 }} rounded-2xl p-6 bg-white" style="border:1px solid rgba(13,62,99,0.08);">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-4" style="background:{{ $sector[2] }}10;">
                            <div style="width:8px; height:8px; border-radius:50%; background:{{ $sector[2] }};"></div>
                        </div>
                        <h4 class="font-heading font-bold text-sm mb-2" style="color:#0D3E63;">{{ $sector[0] }}</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $sector[1] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ TEAM ============ --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 about-reveal">
                <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-4" style="background:rgba(168,112,58,0.1); color:#A56035;">Our People</span>
                <h2 class="font-heading text-3xl sm:text-4xl font-black mb-4" style="color:#0D3E63;">The Team Behind ASYX</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Our strength lies in our people — experienced engineers, project managers, and technology professionals dedicated to delivering mission-critical solutions.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="about-reveal-scale about-stagger-1 rounded-2xl overflow-hidden shadow-sm transition-all" style="border:1px solid rgba(13,62,99,0.06);" onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 16px 32px rgba(13,62,99,0.12)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&q=80" alt="Executive leader" class="w-full h-full object-cover transition-transform" style="transition:transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';">
                    </div>
                    <div class="p-5 text-center">
                        <h4 class="font-heading font-bold" style="color:#0D3E63;">Managing Director</h4>
                        <p class="text-sm font-semibold mt-1" style="color:#A56035;">Leadership &amp; Strategy</p>
                    </div>
                </div>
                <div class="about-reveal-scale about-stagger-2 rounded-2xl overflow-hidden shadow-sm transition-all" style="border:1px solid rgba(13,62,99,0.06);" onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 16px 32px rgba(13,62,99,0.12)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1573497069938-5a6e5a6b4b3e?w=400&q=80" alt="Engineer" class="w-full h-full object-cover transition-transform" style="transition:transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';">
                    </div>
                    <div class="p-5 text-center">
                        <h4 class="font-heading font-bold" style="color:#0D3E63;">Chief Engineer</h4>
                        <p class="text-sm font-semibold mt-1" style="color:#A56035;">ICT Infrastructure</p>
                    </div>
                </div>
                <div class="about-reveal-scale about-stagger-3 rounded-2xl overflow-hidden shadow-sm transition-all" style="border:1px solid rgba(13,62,99,0.06);" onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 16px 32px rgba(13,62,99,0.12)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658ab059152?w=400&q=80" alt="Project manager" class="w-full h-full object-cover transition-transform" style="transition:transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';">
                    </div>
                    <div class="p-5 text-center">
                        <h4 class="font-heading font-bold" style="color:#0D3E63;">Project Manager</h4>
                        <p class="text-sm font-semibold mt-1" style="color:#A56035;">Delivery &amp; Operations</p>
                    </div>
                </div>
                <div class="about-reveal-scale about-stagger-4 rounded-2xl overflow-hidden shadow-sm transition-all" style="border:1px solid rgba(13,62,99,0.06);" onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 16px 32px rgba(13,62,99,0.12)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1580489944761-15a32d82e28f?w=400&q=80" alt="Technology specialist" class="w-full h-full object-cover transition-transform" style="transition:transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';">
                    </div>
                    <div class="p-5 text-center">
                        <h4 class="font-heading font-bold" style="color:#0D3E63;">Security Lead</h4>
                        <p class="text-sm font-semibold mt-1" style="color:#A56035;">Cybersecurity</p>
                    </div>
                </div>
            </div>

            {{-- Office/gallery strip --}}
            <div class="mt-12 grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="about-reveal about-stagger-1 rounded-xl overflow-hidden shadow-md aspect-[4/3]">
                    <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=400&q=80" alt="Office workspace" class="w-full h-full object-cover transition-transform" style="transition:transform 0.5s ease;" onmouseover="this.style.transform='scale(1.08)';" onmouseout="this.style.transform='scale(1)';">
                </div>
                <div class="about-reveal about-stagger-2 rounded-xl overflow-hidden shadow-md aspect-[4/3]">
                    <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?w=400&q=80" alt="Team collaboration" class="w-full h-full object-cover transition-transform" style="transition:transform 0.5s ease;" onmouseover="this.style.transform='scale(1.08)';" onmouseout="this.style.transform='scale(1)';">
                </div>
                <div class="about-reveal about-stagger-3 rounded-xl overflow-hidden shadow-md aspect-[4/3]">
                    <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=400&q=80" alt="Control room" class="w-full h-full object-cover transition-transform" style="transition:transform 0.5s ease;" onmouseover="this.style.transform='scale(1.08)';" onmouseout="this.style.transform='scale(1)';">
                </div>
                <div class="about-reveal about-stagger-4 rounded-xl overflow-hidden shadow-md aspect-[4/3]">
                    <img src="https://images.unsplash.com/photo-1561070791-2526d30994da?w=400&q=80" alt="Network infrastructure" class="w-full h-full object-cover transition-transform" style="transition:transform 0.5s ease;" onmouseover="this.style.transform='scale(1.08)';" onmouseout="this.style.transform='scale(1)';">
                </div>
            </div>
        </div>
    </section>

    @include('landing.partials.cta')
    @include('landing.partials.footer')
@endsection

@push('scripts')
<script>
(function() {
    // Scroll reveal
    var revealEls = document.querySelectorAll('.about-reveal, .about-reveal-left, .about-reveal-right, .about-reveal-scale');
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    revealEls.forEach(function(el) { observer.observe(el); });

    // Counter animation
    var counters = document.querySelectorAll('.about-counter');
    var counterObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                var el = entry.target;
                var target = parseInt(el.dataset.target);
                var suffix = el.dataset.suffix || '';
                var duration = 1800;
                var startTime = null;
                function animate(ts) {
                    if (!startTime) startTime = ts;
                    var progress = Math.min((ts - startTime) / duration, 1);
                    var eased = 1 - Math.pow(1 - progress, 3);
                    el.textContent = Math.floor(eased * target) + suffix;
                    if (progress < 1) requestAnimationFrame(animate);
                    else el.textContent = target + suffix;
                }
                requestAnimationFrame(animate);
                counterObserver.unobserve(el);
            }
        });
    }, { threshold: 0.5 });
    counters.forEach(function(c) { counterObserver.observe(c); });
})();
</script>
@endpush
