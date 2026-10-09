<!doctype html>
<html lang="en" class="h-full scroll-smooth bg-[#e8edf5] text-slate-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Guestel · Cloud Hospitality Operating System</title>
    <meta name="description" content="One intelligent platform to run your hotel, restaurant, housekeeping, guest services, and operations. Built for boutique hotels, luxury resorts, and dining venues.">
    <meta name="keywords" content="hotel management platform, guest experience app, restaurant pos, housekeeping management, hospitality saas, room service qr, hotel operations software">
    <meta name="author" content="Talisha Software">
    <link rel="canonical" href="{{ url('/') }}">

    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Guestel · Cloud Hospitality Operating System">
    <meta property="og:description" content="From guest requests and housekeeping to dining, payments, staff operations and hotel intelligence — connected in one tactile workspace.">
    <meta property="og:image" content="{{ asset('storage/hotels/1/hero_cover.jpg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; background-color: #e8edf5; color: #1e293b; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-full bg-[#e8edf5] text-slate-800 antialiased selection:bg-slate-800 selection:text-white" x-data="{ mobileMenuOpen: false }">

    <!-- Top Neumorphic Navigation Bar -->
    <header class="fixed top-0 inset-x-0 z-50 bg-[#e8edf5]/90 backdrop-blur-md border-b border-white/60 shadow-[0_4px_16px_rgba(202,211,223,0.4)] transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl neu-button text-slate-800 font-extrabold text-xl group-hover:scale-105 transition">
                    G
                </div>
                <div class="leading-none">
                    <span class="block text-base font-extrabold tracking-tight text-slate-900">Guestel</span>
                    <span class="text-[10px] font-mono uppercase tracking-widest text-slate-500 font-semibold">Hospitality Cloud OS</span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#test-login" class="text-blue-600 hover:text-blue-700 font-bold transition flex items-center gap-1.5">
                    <span class="inline-block w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    <span>Test Login</span>
                </a>
                <a href="#platform" class="hover:text-slate-900 transition">Platform</a>
                <a href="#guest-experience" class="hover:text-slate-900 transition">Guest QR</a>
                <a href="#hotel-operations" class="hover:text-slate-900 transition">Operations</a>
                <a href="#restaurant" class="hover:text-slate-900 transition">Kitchen KDS</a>
                <a href="#housekeeping" class="hover:text-slate-900 transition">Housekeeping</a>
                <a href="#security" class="hover:text-slate-900 transition">Security</a>
            </nav>

            <!-- Action CTAs -->
            <div class="hidden sm:flex items-center gap-4">
                <a href="#test-login" class="px-5 py-2.5 text-xs font-bold text-white neu-btn-blue rounded-xl flex items-center gap-2">
                    <span>⚡ Quick Test Login</span>
                </a>
                <a href="#platform" class="px-4 py-2 text-xs font-semibold text-slate-700 hover:text-slate-900 transition">
                    Explore Platform
                </a>
                <a href="{{ route('login') }}" class="px-5 py-2.5 text-xs font-bold text-slate-800 neu-button rounded-xl transition">
                    Sign In
                </a>
            </div>

            <!-- Mobile Hamburger -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="sm:hidden p-2 rounded-xl neu-button text-slate-700 hover:text-slate-900 transition">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-cloak x-show="mobileMenuOpen" class="sm:hidden border-b border-white/60 bg-[#e8edf5] px-6 py-6 space-y-4 shadow-lg">
            <div class="flex flex-col gap-3 text-sm font-semibold">
                <a @click="mobileMenuOpen = false" href="#test-login" class="text-blue-600 flex items-center gap-2">⚡ 1-Click Test Login</a>
                <a @click="mobileMenuOpen = false" href="#platform" class="text-slate-700 hover:text-slate-900">Platform Overview</a>
                <a @click="mobileMenuOpen = false" href="#guest-experience" class="text-slate-700 hover:text-slate-900">Guest Experience</a>
                <a @click="mobileMenuOpen = false" href="#hotel-operations" class="text-slate-700 hover:text-slate-900">Hotel Operations</a>
                <a @click="mobileMenuOpen = false" href="#restaurant" class="text-slate-700 hover:text-slate-900">Kitchen & F&B</a>
                <a @click="mobileMenuOpen = false" href="#housekeeping" class="text-slate-700 hover:text-slate-900">Housekeeping</a>
                <a @click="mobileMenuOpen = false" href="#security" class="text-slate-700 hover:text-slate-900">Security</a>
            </div>
            <div class="pt-4 border-t border-slate-300 flex flex-col gap-3">
                <a href="#test-login" @click="mobileMenuOpen = false" class="w-full text-center py-3 text-xs font-bold text-white neu-btn-blue rounded-xl">⚡ Quick Test Login</a>
                <a href="{{ route('login') }}" class="w-full text-center py-3 text-xs font-bold text-slate-800 neu-button rounded-xl">Sign In</a>
            </div>
        </div>
    </header>

    <main>
        <!-- 1. LANDING PAGE HERO SECTION -->
        <section class="relative pt-32 pb-16 lg:pt-40 lg:pb-24 overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 sm:px-8">
                <div class="text-center max-w-4xl mx-auto space-y-6">
                    <!-- Eyebrow Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full neu-pill text-slate-700 text-xs font-bold tracking-wide">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Enterprise Cloud Hospitality Operating System</span>
                    </div>

                    <!-- Main H1 Headline -->
                    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-slate-900 leading-[1.1]">
                        Hospitality, <br>
                        <span class="text-slate-700 font-bold">beautifully connected.</span>
                    </h1>

                    <!-- Supporting Paragraph -->
                    <p class="text-base sm:text-xl text-slate-600 max-w-2xl mx-auto font-normal leading-relaxed">
                        One simple, unified platform to run your hotel front desk, housekeeping SLA dispatch, kitchen operations, contactless guest dining, and multi-tenant SaaS governance.
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                        <a href="#test-login" class="w-full sm:w-auto px-8 py-4 text-sm font-bold text-white neu-btn-blue rounded-2xl flex items-center justify-center gap-2 group">
                            <span>⚡ 1-Click Test Login</span>
                            <svg class="w-4 h-4 transform group-hover:translate-y-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </a>
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 text-sm font-bold text-slate-800 neu-button rounded-2xl transition flex items-center justify-center gap-2">
                            <span>Manual Credentials Sign In</span>
                        </a>
                    </div>
                </div>

                <!-- ==================================================================== -->
                <!-- ⚡ PROMINENT 1-CLICK INSTANT TEST LOGIN SECTION (NEUMORPHIC) -->
                <!-- ==================================================================== -->
                <div class="mt-16 sm:mt-20 max-w-5xl mx-auto" id="test-login">
                    <div class="neu-flat-lg rounded-3xl p-6 sm:p-10 space-y-8">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-300 pb-6">
                            <div>
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full neu-inset-sm text-blue-700 text-xs font-bold mb-2">
                                    <span>⚡ Instant Access · No Password Needed</span>
                                </div>
                                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                                    Select a Role to Test Now
                                </h2>
                                <p class="text-xs sm:text-sm text-slate-600 mt-1">
                                    Click any button below to immediately enter that operational dashboard with pre-loaded demo data.
                                </p>
                            </div>
                            <div class="text-left md:text-right">
                                <span class="text-xs font-mono text-slate-500 block">Default Password:</span>
                                <span class="text-xs font-mono font-bold text-slate-800 neu-inset-sm px-2.5 py-1 rounded-lg inline-block">Admin12345!</span>
                            </div>
                        </div>

                        <!-- 4 Neumorphic 1-Click Role Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Card 1: Company Super Admin -->
                            <div class="neu-flat rounded-2xl p-5 flex flex-col justify-between space-y-4 hover:translate-y-[-2px] transition">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl neu-button text-amber-600 font-extrabold">
                                            🏢
                                        </div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 neu-inset-sm px-2 py-0.5 rounded-full">SaaS Master</span>
                                    </div>
                                    <h3 class="text-base font-extrabold text-slate-900">Company Super Admin</h3>
                                    <p class="text-xs text-slate-600 leading-relaxed">
                                        Tenant onboarding, SaaS subscriptions, ₹ INR GST invoicing & platform broadcasts.
                                    </p>
                                    <div class="text-[11px] font-mono text-slate-500 pt-1">
                                        admin@example.com
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('login.test') }}">
                                    @csrf
                                    <input type="hidden" name="role" value="super_admin">
                                    <button type="submit" class="w-full py-3 text-xs font-bold text-white neu-btn-primary rounded-xl cursor-pointer">
                                        ⚡ Enter as Super Admin
                                    </button>
                                </form>
                            </div>

                            <!-- Card 2: Hotel Operations Admin -->
                            <div class="neu-flat rounded-2xl p-5 flex flex-col justify-between space-y-4 hover:translate-y-[-2px] transition">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl neu-button text-blue-600 font-extrabold">
                                            🏨
                                        </div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700 neu-inset-sm px-2 py-0.5 rounded-full">Hotel GM</span>
                                    </div>
                                    <h3 class="text-base font-extrabold text-slate-900">Hotel Operations</h3>
                                    <p class="text-xs text-slate-600 leading-relaxed">
                                        Grand Azure 24 rooms status, QR center, concierge chat, and live occupancy metrics.
                                    </p>
                                    <div class="text-[11px] font-mono text-slate-500 pt-1">
                                        admin@example.com
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('login.test') }}">
                                    @csrf
                                    <input type="hidden" name="role" value="hotel_admin">
                                    <button type="submit" class="w-full py-3 text-xs font-bold text-white neu-btn-blue rounded-xl cursor-pointer">
                                        ⚡ Enter as Hotel Admin
                                    </button>
                                </form>
                            </div>

                            <!-- Card 3: Housekeeping Lead -->
                            <div class="neu-flat rounded-2xl p-5 flex flex-col justify-between space-y-4 hover:translate-y-[-2px] transition">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl neu-button text-purple-600 font-extrabold">
                                            🧹
                                        </div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 neu-inset-sm px-2 py-0.5 rounded-full">Housekeeping</span>
                                    </div>
                                    <h3 class="text-base font-extrabold text-slate-900">Housekeeping Lead</h3>
                                    <p class="text-xs text-slate-600 leading-relaxed">
                                        Live SLA urgency countdowns (Urgent, High, Normal), room sanitation, linens & amenities.
                                    </p>
                                    <div class="text-[11px] font-mono text-slate-500 pt-1">
                                        maria.santos@...
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('login.test') }}">
                                    @csrf
                                    <input type="hidden" name="role" value="housekeeping">
                                    <button type="submit" class="w-full py-3 text-xs font-bold text-slate-900 neu-button hover:bg-slate-200 rounded-xl cursor-pointer">
                                        ⚡ Enter as Housekeeper
                                    </button>
                                </form>
                            </div>

                            <!-- Card 4: Executive Chef -->
                            <div class="neu-flat rounded-2xl p-5 flex flex-col justify-between space-y-4 hover:translate-y-[-2px] transition">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl neu-button text-rose-600 font-extrabold">
                                            👨‍🍳
                                        </div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700 neu-inset-sm px-2 py-0.5 rounded-full">Kitchen KDS</span>
                                    </div>
                                    <h3 class="text-base font-extrabold text-slate-900">Executive Chef</h3>
                                    <p class="text-xs text-slate-600 leading-relaxed">
                                        6-stage Kanban board, Kitchen Display System (KDS), dish availability & rush delays.
                                    </p>
                                    <div class="text-[11px] font-mono text-slate-500 pt-1">
                                        chef.marcus@...
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('login.test') }}">
                                    @csrf
                                    <input type="hidden" name="role" value="chef">
                                    <button type="submit" class="w-full py-3 text-xs font-bold text-white neu-btn-primary rounded-xl cursor-pointer">
                                        ⚡ Enter as Chef
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- In-Room Guest QR 1-Click Tests -->
                        <div class="pt-6 border-t border-slate-300">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                                <div>
                                    <h4 class="text-sm font-extrabold text-slate-900">Test In-Room Guest Portals (Direct Simulated QR Scans)</h4>
                                    <p class="text-xs text-slate-500">No login required — tests cryptographic token guest session resolution.</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <a href="{{ url('/g/fd3ywa4gfuhv9comqlawfuhsmtuvx1bstmwg5wtwbcjrjpls') }}" target="_blank" class="p-3 rounded-xl neu-button text-center group">
                                    <span class="block text-xs font-bold text-slate-900 group-hover:text-blue-600">Room 101</span>
                                    <span class="text-[10px] text-slate-500">Deluxe King</span>
                                </a>
                                <a href="{{ url('/g/bpfm6jnpflrjdksyrhbjr5et6fkmmvpvnsf9zbu9zd8zhvxb') }}" target="_blank" class="p-3 rounded-xl neu-button text-center group">
                                    <span class="block text-xs font-bold text-slate-900 group-hover:text-blue-600">Room 102</span>
                                    <span class="text-[10px] text-slate-500">Deluxe Twin</span>
                                </a>
                                <a href="{{ url('/g/hujzsjsi4asbruflxrg2yuhvt7tvveoh3jgmmo0q6rhawhiz') }}" target="_blank" class="p-3 rounded-xl neu-button text-center group">
                                    <span class="block text-xs font-bold text-slate-900 group-hover:text-blue-600">Room 201</span>
                                    <span class="text-[10px] text-slate-500">Ocean Suite</span>
                                </a>
                                <a href="{{ url('/g/lez3e0xarusxidarsywocmqzdra09jsxfdzuywhk5r2d9vbq') }}" target="_blank" class="p-3 rounded-xl neu-button text-center group">
                                    <span class="block text-xs font-bold text-slate-900 group-hover:text-blue-600">Hotel Lobby</span>
                                    <span class="text-[10px] text-slate-500">Property QR</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. INTERACTIVE LIVE OPERATIONS STREAM (NEUMORPHIC) -->
                <div class="mt-16 max-w-5xl mx-auto" id="operations">
                    <div class="neu-flat rounded-3xl p-6 sm:p-8 space-y-6">
                        <!-- Top Chrome Bar -->
                        <div class="flex items-center justify-between pb-4 border-b border-slate-300 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                <span class="ml-3 font-mono font-bold text-slate-700">Grand Azure Resort · Live Operations Stream</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 text-emerald-700 neu-inset-sm px-2.5 py-1 rounded-full font-bold text-[11px]">
                                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                                    <span>Real-Time SLA Engine</span>
                                </span>
                            </div>
                        </div>

                        <!-- Operations Feed -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Feed Item 1 -->
                            <div class="neu-inset-sm rounded-2xl p-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg neu-button text-blue-700 font-bold text-xs">204</span>
                                        <div>
                                            <h4 class="text-xs font-extrabold text-slate-900">Extra Towels & Linens</h4>
                                            <p class="text-[11px] text-slate-500">Room 204 · Ocean Suite</p>
                                        </div>
                                    </div>
                                    <span class="neu-pill text-[10px] font-bold text-amber-700 px-2 py-0.5 rounded-full">
                                        Pending · 2m
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-600 pt-2 border-t border-slate-200">
                                    Assigned: Housekeeping · Target SLA: 15 min
                                </p>
                            </div>

                            <!-- Feed Item 2 -->
                            <div class="neu-inset-sm rounded-2xl p-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg neu-button text-rose-700 font-bold text-xs">315</span>
                                        <div>
                                            <h4 class="text-xs font-extrabold text-slate-900">Climate Control Check</h4>
                                            <p class="text-[11px] text-slate-500">Room 315 · Executive King</p>
                                        </div>
                                    </div>
                                    <span class="neu-pill text-[10px] font-bold text-blue-700 px-2 py-0.5 rounded-full">
                                        Assigned · 8m
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-600 pt-2 border-t border-slate-200">
                                    Assigned: Maintenance · Technician En Route
                                </p>
                            </div>

                            <!-- Feed Item 3 -->
                            <div class="neu-inset-sm rounded-2xl p-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg neu-button text-amber-700 font-bold text-xs">102</span>
                                        <div>
                                            <h4 class="text-xs font-extrabold text-slate-900">Dining: Wagyu Burger & Fries</h4>
                                            <p class="text-[11px] text-slate-500">Room 102 · Order #1082 (₹4,828)</p>
                                        </div>
                                    </div>
                                    <span class="neu-pill text-[10px] font-bold text-orange-700 px-2 py-0.5 rounded-full">
                                        Preparing · 6m
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-600 pt-2 border-t border-slate-200">
                                    Station: The Azure Grill · Chef Marcus
                                </p>
                            </div>

                            <!-- Feed Item 4 -->
                            <div class="neu-inset-sm rounded-2xl p-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg neu-button text-emerald-700 font-bold text-xs">412</span>
                                        <div>
                                            <h4 class="text-xs font-extrabold text-slate-900">Full Departure Cleaning</h4>
                                            <p class="text-[11px] text-slate-500">Room 412 · Penthouse Suite</p>
                                        </div>
                                    </div>
                                    <span class="neu-pill text-[10px] font-bold text-emerald-700 px-2 py-0.5 rounded-full">
                                        Completed · 12m
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-600 pt-2 border-t border-slate-200">
                                    Status: Inspected & Ready · Lead Maria
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. TRUSTED HOSPITALITY METRICS -->
        <section class="py-12 border-y border-slate-300">
            <div class="max-w-7xl mx-auto px-6 sm:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    <div class="neu-flat rounded-2xl p-6">
                        <p class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-mono">99.98%</p>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 font-semibold">Service Uptime</p>
                    </div>
                    <div class="neu-flat rounded-2xl p-6">
                        <p class="text-3xl sm:text-4xl font-extrabold text-blue-600 font-mono">&lt; 12 min</p>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 font-semibold">Average Fulfillment</p>
                    </div>
                    <div class="neu-flat rounded-2xl p-6">
                        <p class="text-3xl sm:text-4xl font-extrabold text-emerald-600 font-mono">100%</p>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 font-semibold">Contactless QR</p>
                    </div>
                    <div class="neu-flat rounded-2xl p-6">
                        <p class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-mono">₹ INR</p>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 font-semibold">18% GST Invoicing</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. OPERATIONAL MODULES (NEUMORPHIC CARDS) -->
        <section class="py-20 lg:py-28" id="platform">
            <div class="max-w-7xl mx-auto px-6 sm:px-8 space-y-16">
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto space-y-4">
                    <span class="text-xs font-mono uppercase tracking-widest text-blue-600 font-bold neu-pill px-3 py-1 rounded-full">Unified Architecture</span>
                    <h2 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight">One platform. Every operation.</h2>
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                        Rather than fragmenting operations across disparate point solutions, Guestel unifies guest concierge, kitchen dispatch, room readiness, billing, and staff collaboration into a unified, secure cloud.
                    </p>
                </div>

                <!-- Modules Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Module 1: Guest Experience -->
                    <div class="neu-flat rounded-3xl p-8 space-y-4 hover:translate-y-[-2px] transition" id="guest-experience">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl neu-button text-amber-600 font-extrabold text-xl">
                            📱
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Guest Experience</h3>
                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-700">Digital In-Room Concierge</p>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            No app downloads required. Guests scan room-specific cryptographic QR tokens to view amenities, place in-room dining orders, request toiletries, and message staff directly.
                        </p>
                    </div>

                    <!-- Module 2: Hotel Operations -->
                    <div class="neu-flat rounded-3xl p-8 space-y-4 hover:translate-y-[-2px] transition" id="hotel-operations">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl neu-button text-blue-600 font-extrabold text-xl">
                            🏨
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Hotel Operations</h3>
                        <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">Front Desk & Room Occupancy</p>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Real-time room occupancy, live arrival tracking, SLA monitoring, and department coordination. Hotel GMs manage rooms, printable keycard QR sheets, and staff assignments in seconds.
                        </p>
                    </div>

                    <!-- Module 3: Housekeeping -->
                    <div class="neu-flat rounded-3xl p-8 space-y-4 hover:translate-y-[-2px] transition" id="housekeeping">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl neu-button text-purple-600 font-extrabold text-xl">
                            🧹
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Housekeeping Hub</h3>
                        <p class="text-xs font-semibold uppercase tracking-wider text-purple-700">SLA Priority Queues</p>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Operational cleaning queues categorized by Urgent, High, and Normal priorities. Staff mark rooms Vacant, Dirty, Cleaning, Inspected, and Ready with live status syncing.
                        </p>
                    </div>

                    <!-- Module 4: Restaurant & F&B -->
                    <div class="neu-flat rounded-3xl p-8 space-y-4 hover:translate-y-[-2px] transition" id="restaurant">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl neu-button text-rose-600 font-extrabold text-xl">
                            👨‍🍳
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Restaurant & Kitchen</h3>
                        <p class="text-xs font-semibold uppercase tracking-wider text-rose-700">6-Stage Kanban & KDS</p>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Live kitchen ticket queue, preparation timers, special dietary instructions, and fullscreen Kitchen Display System (KDS) for back-of-house staff.
                        </p>
                    </div>

                    <!-- Module 5: Real-Time Service Management -->
                    <div class="neu-flat rounded-3xl p-8 space-y-4 hover:translate-y-[-2px] transition">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl neu-button text-emerald-600 font-extrabold text-xl">
                            ⏱️
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">SLA Tracking</h3>
                        <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Automated Dispatch</p>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Automatic routing to Front Desk, Housekeeping, Concierge, Maintenance, or Kitchen with automated response and completion SLA timers to eliminate missed requests.
                        </p>
                    </div>

                    <!-- Module 6: Payments & Billing -->
                    <div class="neu-flat rounded-3xl p-8 space-y-4 hover:translate-y-[-2px] transition">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl neu-button text-indigo-600 font-extrabold text-xl">
                            💳
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Billing & GST</h3>
                        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700">₹ INR Multi-Tenant Invoicing</p>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Server-authoritative billing in INR (₹) with 18% GST calculation, UPI QR, bank transfers, and automated printable GST tax invoices for properties and dining outlets.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. SECURITY & TRUST ARCHITECTURE -->
        <section class="py-16 border-t border-slate-300" id="security">
            <div class="max-w-7xl mx-auto px-6 sm:px-8 space-y-10">
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <span class="text-xs font-mono uppercase tracking-widest text-emerald-700 font-bold neu-pill px-3 py-1 rounded-full">Zero-Trust Standard</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Multi-Tenant Isolation & Protection</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="neu-flat rounded-2xl p-6 space-y-2">
                        <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Tenant Isolation
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Strict global query scoping ensures no property or staff member can ever query or view records belonging to another hotel or restaurant.
                        </p>
                    </div>

                    <div class="neu-flat rounded-2xl p-6 space-y-2">
                        <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Server-Side Authorization
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Every request checks User → Property → Role → Granular Permission. The frontend never dictates security privileges.
                        </p>
                    </div>

                    <div class="neu-flat rounded-2xl p-6 space-y-2">
                        <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            IDOR Defense
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Manipulating entity IDs in URLs or form submissions fails immediately with 403 Forbidden. Client inputs are never implicitly trusted.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="border-t border-slate-300 py-12 text-xs text-slate-600">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-xl neu-button text-slate-800 font-extrabold text-sm">
                    G
                </div>
                <span class="font-bold text-slate-800">Guestel · Talisha Software</span>
            </div>
            <p class="text-slate-500">
                &copy; {{ date('Y') }} Guestel Hospitality Platform. All rights reserved.
            </p>
            <div class="flex items-center gap-4">
                <a href="#test-login" class="text-blue-600 font-bold hover:underline">1-Click Test Login</a>
                <a href="{{ route('login') }}" class="hover:text-slate-900">Sign In</a>
            </div>
        </div>
    </footer>

</body>
</html>
