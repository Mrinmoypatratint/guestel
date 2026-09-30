<!doctype html>
<html lang="en" class="h-full scroll-smooth bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Hospitality, Beautifully Connected · Hotel Guest Platform</title>
    <meta name="description" content="One intelligent platform to run your hotel, restaurant, housekeeping, guest services, and operations. Built for independent boutique hotels, luxury resorts, and hospitality groups.">
    <meta name="keywords" content="hotel management platform, guest experience app, restaurant pos, housekeeping management, hospitality saas, room service qr, hotel operations software">
    <meta name="author" content="Hotel Guest Platform Technologies">
    <link rel="canonical" href="{{ url('/') }}">

    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Hospitality, Beautifully Connected · Hotel Guest Platform">
    <meta property="og:description" content="From guest requests and housekeeping to dining, payments, staff operations and hotel intelligence — connected in one place.">
    <meta property="og:image" content="{{ asset('storage/hotels/1/hero_cover.jpg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
        .font-serif-luxury { font-family: 'Playfair Display', Georgia, serif; }
        [x-cloak] { display: none !important; }
        .glass-panel { background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .gold-gradient { background: linear-gradient(135deg, #fbbf24 0%, #d97706 50%, #b45309 100%); }
        .text-gold-gradient { background: linear-gradient(135deg, #fef3c7 0%, #f59e0b 50%, #d97706 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>
</head>
<body class="min-h-full bg-slate-950 text-slate-100 antialiased selection:bg-amber-500 selection:text-slate-950" x-data="{ mobileMenuOpen: false }">

    <!-- Top Luxury Navigation Bar -->
    <header class="fixed top-0 inset-x-0 z-50 glass-panel border-b border-slate-800/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl gold-gradient text-slate-950 font-black text-xl shadow-lg shadow-amber-950/50 group-hover:scale-105 transition">
                    H
                </div>
                <div class="leading-none">
                    <span class="block text-base font-bold tracking-tight text-white group-hover:text-amber-300 transition">Hotel Guest Platform</span>
                    <span class="text-[10px] font-mono uppercase tracking-widest text-amber-400">Hospitality Cloud OS</span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden lg:flex items-center gap-8 text-sm font-medium text-slate-300">
                <a href="#platform" class="hover:text-amber-400 transition">Platform</a>
                <a href="#guest-experience" class="hover:text-amber-400 transition">Guest Experience</a>
                <a href="#hotel-operations" class="hover:text-amber-400 transition">Hotel Operations</a>
                <a href="#restaurant" class="hover:text-amber-400 transition">Restaurant & F&B</a>
                <a href="#housekeeping" class="hover:text-amber-400 transition">Housekeeping</a>
                <a href="#security" class="hover:text-amber-400 transition">Security</a>
                <a href="#pricing" class="hover:text-amber-400 transition">Pricing</a>
            </nav>

            <!-- Action CTAs -->
            <div class="hidden sm:flex items-center gap-4">
                <a href="{{ route('login') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-200 hover:text-white rounded-xl border border-slate-800 hover:border-slate-700 bg-slate-900/60 transition">
                    Sign In
                </a>
                <a href="#demo" class="px-5 py-2.5 text-xs font-bold text-slate-950 gold-gradient rounded-xl shadow-lg shadow-amber-500/20 hover:brightness-110 transition">
                    Explore Platform
                </a>
            </div>

            <!-- Mobile Hamburger -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="sm:hidden p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-cloak x-show="mobileMenuOpen" class="sm:hidden border-b border-slate-800 bg-slate-950/95 px-6 py-6 space-y-4">
            <div class="flex flex-col gap-3 text-sm font-medium">
                <a @click="mobileMenuOpen = false" href="#platform" class="text-slate-300 hover:text-amber-400">Platform Overview</a>
                <a @click="mobileMenuOpen = false" href="#guest-experience" class="text-slate-300 hover:text-amber-400">Guest Experience</a>
                <a @click="mobileMenuOpen = false" href="#hotel-operations" class="text-slate-300 hover:text-amber-400">Hotel Operations</a>
                <a @click="mobileMenuOpen = false" href="#restaurant" class="text-slate-300 hover:text-amber-400">Restaurant & F&B</a>
                <a @click="mobileMenuOpen = false" href="#housekeeping" class="text-slate-300 hover:text-amber-400">Housekeeping</a>
                <a @click="mobileMenuOpen = false" href="#security" class="text-slate-300 hover:text-amber-400">Security & Isolation</a>
            </div>
            <div class="pt-4 border-t border-slate-800 flex flex-col gap-2.5">
                <a href="{{ route('login') }}" class="w-full text-center py-3 text-xs font-semibold text-slate-200 rounded-xl border border-slate-800 bg-slate-900">Sign In</a>
                <a href="#demo" @click="mobileMenuOpen = false" class="w-full text-center py-3 text-xs font-bold text-slate-950 gold-gradient rounded-xl">Explore Platform</a>
            </div>
        </div>
    </header>

    <main>
        <!-- 1. LANDING PAGE HERO SECTION -->
        <section class="relative pt-32 pb-20 lg:pt-44 lg:pb-32 overflow-hidden">
            <!-- Subtle Ambient Background Glows -->
            <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[500px] bg-amber-600/10 rounded-full blur-[140px] pointer-events-none"></div>
            <div class="absolute top-1/3 left-10 w-[400px] h-[400px] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-6 sm:px-8">
                <div class="text-center max-w-4xl mx-auto space-y-6">
                    <!-- Eyebrow Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-amber-500/20 bg-amber-500/10 text-amber-300 text-xs font-medium tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>The Modern Operating System for Luxury Hospitality</span>
                    </div>

                    <!-- Main H1 Headline -->
                    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-serif-luxury font-medium tracking-tight text-white leading-[1.1]">
                        Hospitality, <br>
                        <span class="font-sans font-black italic tracking-tighter text-gold-gradient">beautifully connected.</span>
                    </h1>

                    <!-- Supporting Paragraph -->
                    <p class="text-base sm:text-xl text-slate-300 max-w-2xl mx-auto font-normal leading-relaxed">
                        One platform to run your hotel, restaurant, guest services and operations. From guest requests and housekeeping to dining, payments, staff operations and hotel intelligence — everything connected in one place.
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                        <a href="#operations" class="w-full sm:w-auto px-8 py-4 text-sm font-bold text-slate-950 gold-gradient rounded-2xl shadow-xl shadow-amber-500/25 hover:brightness-110 transition flex items-center justify-center gap-2 group">
                            <span>Explore Platform</span>
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 text-sm font-semibold text-slate-200 hover:text-white glass-panel hover:bg-slate-800/80 rounded-2xl transition flex items-center justify-center gap-2">
                            <span>Sign In to Workspace</span>
                        </a>
                    </div>
                </div>

                <!-- 2. INTERACTIVE LIVE HOTEL OPERATIONS SHOWCASE -->
                <div class="mt-16 sm:mt-24 max-w-5xl mx-auto" id="operations">
                    <div class="relative rounded-3xl p-2 sm:p-4 glass-panel border border-slate-700/60 shadow-2xl overflow-hidden">
                        <!-- Top Chrome Bar -->
                        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800/80 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500/80"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500/80"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500/80"></span>
                                <span class="ml-3 font-mono text-slate-400">Grand Azure Resort · Live Operations Stream</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 text-emerald-400 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                    <span>Real-Time SLA Engine</span>
                                </span>
                            </div>
                        </div>

                        <!-- Operations Interactive Feed Simulation -->
                        <div class="p-6 sm:p-8 space-y-4 bg-slate-950/60">
                            <div class="flex items-center justify-between text-xs uppercase tracking-wider text-slate-400 font-bold px-2">
                                <span>Recent Guest & Operations Activity</span>
                                <span>Live Dispatch Status</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Card 1 -->
                                <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 hover:border-amber-500/40 transition duration-300 hover:-translate-y-1">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-500/10 text-blue-400 font-bold text-xs border border-blue-500/20">204</span>
                                            <div>
                                                <h4 class="text-sm font-bold text-white">Extra Towels & Fresh Linens</h4>
                                                <p class="text-xs text-slate-400">Room 204 · Deluxe Ocean Suite</p>
                                            </div>
                                        </div>
                                        <span class="rounded-full bg-amber-500/10 px-2.5 py-1 text-xs font-semibold text-amber-400 border border-amber-500/20">
                                            Pending · 2m
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-3 pt-3 border-t border-slate-800/80">
                                        Assigned: Housekeeping · Target Completion: 15 min
                                    </p>
                                </div>

                                <!-- Card 2 -->
                                <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 hover:border-amber-500/40 transition duration-300 hover:-translate-y-1">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-500/10 text-rose-400 font-bold text-xs border border-rose-500/20">315</span>
                                            <div>
                                                <h4 class="text-sm font-bold text-white">Climate Control / AC Check</h4>
                                                <p class="text-xs text-slate-400">Room 315 · Executive King</p>
                                            </div>
                                        </div>
                                        <span class="rounded-full bg-blue-500/10 px-2.5 py-1 text-xs font-semibold text-blue-400 border border-blue-500/20">
                                            Assigned · 8m
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-3 pt-3 border-t border-slate-800/80">
                                        Assigned: Maintenance Team · Technician Dispatched
                                    </p>
                                </div>

                                <!-- Card 3 -->
                                <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 hover:border-amber-500/40 transition duration-300 hover:-translate-y-1">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400 font-bold text-xs border border-amber-500/20">102</span>
                                            <div>
                                                <h4 class="text-sm font-bold text-white">In-Room Dining: Wagyu Burger</h4>
                                                <p class="text-xs text-slate-400">Room 102 · Order #ORD-1082 (₹4,828.25)</p>
                                            </div>
                                        </div>
                                        <span class="rounded-full bg-orange-500/10 px-2.5 py-1 text-xs font-semibold text-orange-400 border border-orange-500/20">
                                            Preparing · 6m
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-3 pt-3 border-t border-slate-800/80">
                                        Kitchen Station: The Azure Grill · Chef Marcus
                                    </p>
                                </div>

                                <!-- Card 4 -->
                                <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 hover:border-amber-500/40 transition duration-300 hover:-translate-y-1">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-400 font-bold text-xs border border-emerald-500/20">412</span>
                                            <div>
                                                <h4 class="text-sm font-bold text-white">Full Departure Cleaning</h4>
                                                <p class="text-xs text-slate-400">Room 412 · Penthouse Suite</p>
                                            </div>
                                        </div>
                                        <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-400 border border-emerald-500/20">
                                            Completed · 12m
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-3 pt-3 border-t border-slate-800/80">
                                        Status: Inspected & Ready · Housekeeping Lead Maria
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. TRUSTED HOSPITALITY METRICS -->
        <section class="border-y border-slate-800/80 bg-slate-900/40 py-12">
            <div class="max-w-7xl mx-auto px-6 sm:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <p class="text-3xl sm:text-4xl font-black text-white font-mono">99.98%</p>
                        <p class="text-xs sm:text-sm text-slate-400 mt-1">Platform Service Uptime</p>
                    </div>
                    <div>
                        <p class="text-3xl sm:text-4xl font-black text-amber-400 font-mono">&lt; 12 min</p>
                        <p class="text-xs sm:text-sm text-slate-400 mt-1">Average Request Fulfillment</p>
                    </div>
                    <div>
                        <p class="text-3xl sm:text-4xl font-black text-emerald-400 font-mono">100%</p>
                        <p class="text-xs sm:text-sm text-slate-400 mt-1">Contactless QR Accessibility</p>
                    </div>
                    <div>
                        <p class="text-3xl sm:text-4xl font-black text-white font-mono">₹ Multi-Tier</p>
                        <p class="text-xs sm:text-sm text-slate-400 mt-1">INR Pricing & 18% GST Compliant</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. ONE PLATFORM. EVERY OPERATION. (7 DISTINCT MODULES) -->
        <section class="py-24 lg:py-32" id="platform">
            <div class="max-w-7xl mx-auto px-6 sm:px-8 space-y-20">
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto space-y-4">
                    <span class="text-xs font-mono uppercase tracking-widest text-amber-400 font-bold">Complete Hospitality Architecture</span>
                    <h2 class="text-3xl sm:text-5xl font-serif-luxury font-medium text-white">One platform. Every operation.</h2>
                    <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                        Rather than fragmenting operations across separate vendors, Hotel Guest Platform unifies guest concierge, kitchen dispatch, room status, billing, and staff collaboration into a unified, secure cloud.
                    </p>
                </div>

                <!-- Modules Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Module 1: Guest Experience -->
                    <div class="rounded-3xl glass-panel p-8 space-y-4 hover:border-amber-500/40 transition group" id="guest-experience">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-400 border border-amber-500/20 group-hover:scale-110 transition">
                            <x-icon name="qr" class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-white">Guest Experience</h3>
                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-400">"Turn every room into a digital concierge."</p>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            No app downloads required. Guests scan room-specific cryptographic QR tokens to view amenities, place in-room dining orders, request toiletries, and message staff directly.
                        </p>
                    </div>

                    <!-- Module 2: Hotel Operations -->
                    <div class="rounded-3xl glass-panel p-8 space-y-4 hover:border-blue-500/40 transition group" id="hotel-operations">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-500/10 text-blue-400 border border-blue-500/20 group-hover:scale-110 transition">
                            <x-icon name="dashboard" class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-white">Hotel Operations</h3>
                        <p class="text-xs font-semibold uppercase tracking-wider text-blue-400">"Know what's happening across your property."</p>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Real-time room occupancy, live arrival tracking, SLA monitoring, and department coordination. Hotel GMs manage rooms, keycard QR print sheets, and staff assignments in seconds.
                        </p>
                    </div>

                    <!-- Module 3: Housekeeping -->
                    <div class="rounded-3xl glass-panel p-8 space-y-4 hover:border-purple-500/40 transition group" id="housekeeping">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-500/10 text-purple-400 border border-purple-500/20 group-hover:scale-110 transition">
                            <x-icon name="bed" class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-white">Housekeeping</h3>
                        <p class="text-xs font-semibold uppercase tracking-wider text-purple-400">"Keep every room moving."</p>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Operational cleaning queues categorized by Urgent, High, and Normal priorities. Staff mark rooms Vacant, Dirty, Cleaning, Inspected, and Ready with live status syncing.
                        </p>
                    </div>

                    <!-- Module 4: Restaurant & F&B -->
                    <div class="rounded-3xl glass-panel p-8 space-y-4 hover:border-rose-500/40 transition group" id="restaurant">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-500/10 text-rose-400 border border-rose-500/20 group-hover:scale-110 transition">
                            <x-icon name="restaurant" class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-white">Restaurant & F&B</h3>
                        <p class="text-xs font-semibold uppercase tracking-wider text-rose-400">"From order to kitchen to delivery."</p>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Live kitchen ticket queue, preparation timers, special dietary instructions, and Kitchen Display Mode for back-of-house staff with instant order alert chimes.
                        </p>
                    </div>

                    <!-- Module 5: Real-Time Service Management -->
                    <div class="rounded-3xl glass-panel p-8 space-y-4 hover:border-emerald-500/40 transition group">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 group-hover:scale-110 transition">
                            <x-icon name="requests" class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-white">Service Management</h3>
                        <p class="text-xs font-semibold uppercase tracking-wider text-emerald-400">"Every request. Assigned. Tracked. Completed."</p>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Automatic routing to Front Desk, Housekeeping, Concierge, Maintenance, or Kitchen with automated response and completion SLA timers to eliminate missed requests.
                        </p>
                    </div>

                    <!-- Module 6: Payments & Billing -->
                    <div class="rounded-3xl glass-panel p-8 space-y-4 hover:border-amber-500/40 transition group" id="pricing">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-400 border border-amber-500/20 group-hover:scale-110 transition">
                            <x-icon name="orders" class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-white">Payments & Billing</h3>
                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-400">"Simple, transparent hospitality commerce."</p>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Server-authoritative billing in INR (₹) with 18% GST calculation, UPI QR, NEFT/RTGS bank transfers, and automated printable GST tax invoices for properties and dining outlets.
                        </p>
                    </div>

                    <!-- Module 7: Analytics & Intelligence -->
                    <div class="rounded-3xl glass-panel p-8 space-y-4 hover:border-teal-500/40 transition group md:col-span-2 lg:col-span-3">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-500/10 text-teal-400 border border-teal-500/20">
                                <x-icon name="analytics" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Analytics & Hospitality Intelligence</h3>
                                <p class="text-xs font-semibold uppercase tracking-wider text-teal-400">"Turn property activity into actionable intelligence."</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Understand average turnaround by department, peak ordering times, most popular menu items, and guest ratings. Make informed staffing and inventory decisions based on real guest data.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. SECURITY & TRUST ARCHITECTURE -->
        <section class="py-20 border-t border-slate-800/80 bg-slate-900/30" id="security">
            <div class="max-w-7xl mx-auto px-6 sm:px-8 space-y-12">
                <div class="text-center max-w-2xl mx-auto space-y-3">
                    <span class="text-xs font-mono uppercase tracking-widest text-emerald-400 font-bold">Enterprise Security Standard</span>
                    <h2 class="text-3xl sm:text-4xl font-serif-luxury text-white">Your property data stays protected.</h2>
                    <p class="text-slate-400 text-sm">
                        Built from day one with multi-tenant isolation, server-side authorization, and immutable audit logging.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 space-y-2">
                        <h4 class="text-sm font-bold text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            Tenant Isolation
                        </h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Strict global query scoping ensures no property or staff member can ever query or view records belonging to another hotel or restaurant.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 space-y-2">
                        <h4 class="text-sm font-bold text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            Server-Side Authorization
                        </h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Every request checks User → Property → Role → Granular Permission. The frontend never dictates security privileges.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 space-y-2">
                        <h4 class="text-sm font-bold text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            IDOR Protection
                        </h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Manipulating entity IDs in URLs or form submissions fails immediately with 403 Forbidden. Client inputs are never implicitly trusted.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 space-y-2">
                        <h4 class="text-sm font-bold text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            Cryptographic QR Tokens
                        </h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Public QR URLs use high-entropy random tokens that do not expose internal database IDs, preventing enumeration attacks.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 space-y-2">
                        <h4 class="text-sm font-bold text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            Audit Logging
                        </h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Every state change, ticket reassignment, order status transition, and payment action is recorded with user identity, timestamp, and IP address.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 space-y-2">
                        <h4 class="text-sm font-bold text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            Secure Sessions & CSRF
                        </h4>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Protected against session fixation, brute-force throttling on login endpoints, and strict CSRF verification on every POST/PATCH request.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. FINAL CALL TO ACTION -->
        <section class="py-24 relative overflow-hidden" id="demo">
            <div class="max-w-5xl mx-auto px-6 sm:px-8 text-center space-y-8 relative z-10">
                <div class="rounded-3xl p-10 sm:p-16 glass-panel border border-amber-500/30 shadow-2xl space-y-6">
                    <span class="text-xs font-mono uppercase tracking-widest text-amber-400 font-bold">Transform Your Property Today</span>
                    <h2 class="text-3xl sm:text-5xl font-serif-luxury font-medium text-white leading-tight">
                        Everything your property needs.<br>
                        One intelligent workspace.
                    </h2>
                    <p class="text-slate-300 text-sm sm:text-base max-w-xl mx-auto">
                        Join leading boutique hotels, beach resorts, and hotel chains running on Hotel Guest Platform.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 text-sm font-bold text-slate-950 gold-gradient rounded-xl shadow-lg shadow-amber-500/20 hover:brightness-110 transition">
                            Sign In to Workspace
                        </a>
                        <a href="#platform" class="w-full sm:w-auto px-8 py-4 text-sm font-semibold text-slate-300 hover:text-white glass-panel rounded-xl transition">
                            Explore Architecture
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- 7. COMPREHENSIVE LUXURY FOOTER -->
    <footer class="border-t border-slate-800/80 bg-slate-950 py-16 text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 grid grid-cols-2 md:grid-cols-5 gap-8">
            <div class="col-span-2 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl gold-gradient text-slate-950 font-black text-sm">
                        H
                    </div>
                    <span class="font-bold text-white text-sm">Hotel Guest Platform</span>
                </div>
                <p class="text-slate-400 max-w-sm text-xs leading-relaxed">
                    The enterprise hospitality operating system connecting guests, hotel management, housekeeping teams, and dining kitchens into one unified real-time cloud.
                </p>
                <p class="text-[11px] text-slate-400">
                    &copy; {{ date('Y') }} Hotel Guest Platform Technologies India Pvt. Ltd. All rights reserved.
                </p>
            </div>

            <div class="space-y-3">
                <h5 class="font-bold uppercase tracking-wider text-slate-200 text-[11px]">Product Modules</h5>
                <ul class="space-y-2">
                    <li><a href="#guest-experience" class="hover:text-amber-400 transition">Guest Experience</a></li>
                    <li><a href="#hotel-operations" class="hover:text-amber-400 transition">Hotel Operations</a></li>
                    <li><a href="#restaurant" class="hover:text-amber-400 transition">Restaurant & F&B</a></li>
                    <li><a href="#housekeeping" class="hover:text-amber-400 transition">Housekeeping Hub</a></li>
                    <li><a href="#operations" class="hover:text-amber-400 transition">SLA Timers</a></li>
                </ul>
            </div>

            <div class="space-y-3">
                <h5 class="font-bold uppercase tracking-wider text-slate-200 text-[11px]">Company & Security</h5>
                <ul class="space-y-2">
                    <li><a href="#security" class="hover:text-amber-400 transition">Security Architecture</a></li>
                    <li><a href="#security" class="hover:text-amber-400 transition">Tenant Isolation</a></li>
                    <li><a href="#pricing" class="hover:text-amber-400 transition">SaaS Subscriptions</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-amber-400 transition">Staff Sign In</a></li>
                </ul>
            </div>

            <div class="space-y-3">
                <h5 class="font-bold uppercase tracking-wider text-slate-200 text-[11px]">Workspaces</h5>
                <ul class="space-y-2">
                    <li><a href="{{ route('login') }}" class="hover:text-amber-400 transition">Company Super Admin</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-amber-400 transition">Hotel Operations</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-amber-400 transition">Housekeeping Lead</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-amber-400 transition">Executive Chef</a></li>
                </ul>
            </div>
        </div>
    </footer>

</body>
</html>
