<!doctype html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In · Hotel Guest Platform Operations</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
        .font-serif-luxury { font-family: 'Playfair Display', Georgia, serif; }
        [x-cloak] { display: none !important; }
        .gold-gradient { background: linear-gradient(135deg, #fbbf24 0%, #d97706 50%, #b45309 100%); }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 antialiased" x-data="{
    selectedRole: 'hotel',
    email: '{{ old('email', 'admin@example.com') }}',
    password: '{{ app()->environment('local', 'testing') ? 'Admin12345!' : '' }}',
    roleMeta: {
        platform: {
            title: 'Company Super Admin',
            tagline: 'SaaS Platform Governance',
            desc: 'Multi-tenant hotel onboarding, SaaS subscriptions, GST invoicing and company notices.',
            destination: '/platform',
            email: 'admin@example.com',
            accent: 'border-amber-500/50 text-amber-400 bg-amber-500/10'
        },
        hotel: {
            title: 'Hotel Operations Admin',
            tagline: 'Property Executive & Front Desk',
            desc: 'Manage rooms, guest requests, dining orders, housekeeping assignments, and live chat.',
            destination: '/admin',
            email: 'admin@example.com',
            accent: 'border-blue-500/50 text-blue-400 bg-blue-500/10'
        },
        housekeeping: {
            title: 'Housekeeping Lead',
            tagline: 'Room Cleaning & SLA Queue',
            desc: 'Operational room readiness, linens, towels, amenities, and prioritized SLA tickets.',
            destination: '/admin/requests',
            email: 'maria.santos@grandazure.com',
            accent: 'border-purple-500/50 text-purple-400 bg-purple-500/10'
        },
        restaurant: {
            title: 'Executive Chef / F&B',
            tagline: 'Kitchen Operations & KDS',
            desc: 'Live order board, preparation timers, Kitchen Display Mode, menu status, and rush pauses.',
            destination: '/admin/orders',
            email: 'chef.marcus@grandazure.com',
            accent: 'border-rose-500/50 text-rose-400 bg-rose-500/10'
        }
    },
    selectRole(role) {
        this.selectedRole = role;
        @if(app()->environment('local', 'testing'))
        this.email = this.roleMeta[role].email;
        this.password = 'Admin12345!';
        @endif
    }
}">

<div class="min-h-screen w-full flex flex-col lg:flex-row bg-slate-950" style="position: relative;">

    <!-- Left Screen: Luxury Hospitality Visual Canvas (Isolated & Contained) -->
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-10 xl:p-14 border-r border-slate-800/80" 
         style="position: relative; overflow: hidden; isolation: isolate; background-color: #0b1120;">
        
        <!-- Isolated Background Image -->
        <div style="position: absolute; inset: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none;">
            <img src="{{ asset('storage/hotels/1/hero_cover.jpg') }}" 
                 alt="Grand Azure Resort" 
                 style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; opacity: 0.38; filter: saturate(1.15);" />
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, #020617 0%, rgba(2, 6, 23, 0.75) 50%, rgba(2, 6, 23, 0.45) 100%);"></div>
        </div>

        <!-- Top Left Brand -->
        <div class="flex items-center gap-3" style="position: relative; z-index: 10;">
            <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl gold-gradient text-slate-950 font-black text-xl shadow-lg shadow-amber-950/50 group-hover:scale-105 transition">
                    H
                </div>
                <div>
                    <span class="block text-base font-bold tracking-tight text-white group-hover:text-amber-300 transition">Hotel Guest Platform</span>
                    <span class="text-[10px] font-mono uppercase tracking-widest text-amber-400">Hospitality Cloud OS</span>
                </div>
            </a>
        </div>

        <!-- Middle Quote / Editorial Hospitality Branding -->
        <div class="max-w-lg space-y-4" style="position: relative; z-index: 10;">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xs font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Operational Command Center</span>
            </div>
            <h2 class="text-3xl xl:text-4xl font-serif-luxury font-medium text-white leading-tight">
                "Precision in service. Calm in operations."
            </h2>
            <p class="text-xs xl:text-sm text-slate-300 leading-relaxed font-light">
                Empowering hotel general managers, executive chefs, and housekeeping teams with instant, role-scoped situational awareness across every property.
            </p>

            <!-- Property Pulse Mini Card -->
            <div class="rounded-2xl border border-slate-700/60 bg-slate-900/80 backdrop-blur-md p-4 space-y-2 max-w-sm mt-4">
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                    <span class="font-semibold text-white">Grand Azure Hotel & Resort</span>
                    <span class="text-emerald-400 font-mono">Live Sync</span>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="rounded-lg bg-slate-800/80 p-2">
                        <span class="block text-white font-bold">24</span>
                        <span class="text-[10px] text-slate-400">Rooms</span>
                    </div>
                    <div class="rounded-lg bg-slate-800/80 p-2">
                        <span class="block text-white font-bold">12</span>
                        <span class="text-[10px] text-slate-400">Orders</span>
                    </div>
                    <div class="rounded-lg bg-slate-800/80 p-2">
                        <span class="block text-emerald-400 font-bold">99.4%</span>
                        <span class="text-[10px] text-slate-400">SLA Met</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Left Security Assurances -->
        <div class="pt-6 border-t border-slate-800/80 text-[11px] text-slate-400 flex items-center justify-between" style="position: relative; z-index: 10;">
            <span>&copy; {{ date('Y') }} Hotel Guest Platform Technologies</span>
            <span class="font-mono text-emerald-400 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Zero-Trust RBAC & Session Protection</span>
            </span>
        </div>
    </div>

    <!-- Right Screen: Role-Aware Login & Authentication -->
    <div class="flex-1 flex flex-col justify-between p-6 sm:p-10 lg:p-12 xl:p-14 overflow-y-auto" 
         style="position: relative; background-color: #020617; z-index: 20;">
        
        <!-- Top Nav / Back to Landing -->
        <div class="flex items-center justify-between pb-6">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Return to Overview</span>
            </a>

            <span class="text-xs text-slate-400">Need credentials? <a href="mailto:support@hotel-guest-platform.com" class="text-amber-400 hover:underline">Contact Admin</a></span>
        </div>

        <!-- Form Body Container -->
        <div class="max-w-md w-full mx-auto space-y-6 my-auto py-2">
            <!-- Header -->
            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-mono uppercase tracking-widest font-bold">
                    <span>Secure Workspace Gateway</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Welcome back</h1>
                <p class="text-xs sm:text-sm text-slate-400">Select how you work with the platform to configure your workspace.</p>
            </div>

            <!-- Role Selection Interface (4 Distinct Luxury Cards) -->
            <div class="space-y-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Choose Operational Workspace</label>
                <div class="grid grid-cols-2 gap-2.5">
                    <!-- 1. Company Admin -->
                    <button 
                        type="button" 
                        @click="selectRole('platform')"
                        :class="selectedRole === 'platform' ? 'border-amber-500 bg-amber-500/10 shadow-lg shadow-amber-500/10' : 'border-slate-800 bg-slate-900/60 hover:border-slate-700'"
                        class="text-left p-3 rounded-2xl border transition duration-200 cursor-pointer">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-amber-500/20 text-amber-400">
                                <x-icon name="shield" class="w-3.5 h-3.5" />
                            </span>
                            <span class="text-xs font-bold text-white leading-tight">Company Admin</span>
                        </div>
                        <p class="text-[10px] text-slate-400 line-clamp-1">SaaS & Billing</p>
                    </button>

                    <!-- 2. Hotel Admin -->
                    <button 
                        type="button" 
                        @click="selectRole('hotel')"
                        :class="selectedRole === 'hotel' ? 'border-blue-500 bg-blue-500/10 shadow-lg shadow-blue-500/10' : 'border-slate-800 bg-slate-900/60 hover:border-slate-700'"
                        class="text-left p-3 rounded-2xl border transition duration-200 cursor-pointer">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-500/20 text-blue-400">
                                <x-icon name="dashboard" class="w-3.5 h-3.5" />
                            </span>
                            <span class="text-xs font-bold text-white leading-tight">Hotel Operations</span>
                        </div>
                        <p class="text-[10px] text-slate-400 line-clamp-1">Rooms & General Ops</p>
                    </button>

                    <!-- 3. Housekeeping -->
                    <button 
                        type="button" 
                        @click="selectRole('housekeeping')"
                        :class="selectedRole === 'housekeeping' ? 'border-purple-500 bg-purple-500/10 shadow-lg shadow-purple-500/10' : 'border-slate-800 bg-slate-900/60 hover:border-slate-700'"
                        class="text-left p-3 rounded-2xl border transition duration-200 cursor-pointer">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-purple-500/20 text-purple-400">
                                <x-icon name="bed" class="w-3.5 h-3.5" />
                            </span>
                            <span class="text-xs font-bold text-white leading-tight">Housekeeping</span>
                        </div>
                        <p class="text-[10px] text-slate-400 line-clamp-1">Cleaning & Linens</p>
                    </button>

                    <!-- 4. Restaurant -->
                    <button 
                        type="button" 
                        @click="selectRole('restaurant')"
                        :class="selectedRole === 'restaurant' ? 'border-rose-500 bg-rose-500/10 shadow-lg shadow-rose-500/10' : 'border-slate-800 bg-slate-900/60 hover:border-slate-700'"
                        class="text-left p-3 rounded-2xl border transition duration-200 cursor-pointer">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-rose-500/20 text-rose-400">
                                <x-icon name="restaurant" class="w-3.5 h-3.5" />
                            </span>
                            <span class="text-xs font-bold text-white leading-tight">Restaurant / F&B</span>
                        </div>
                        <p class="text-[10px] text-slate-400 line-clamp-1">Kitchen & Orders</p>
                    </button>
                </div>
            </div>

            <!-- Role Context Alert Card -->
            <div class="rounded-xl border p-3 space-y-1 transition duration-200" :class="roleMeta[selectedRole].accent">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-white" x-text="roleMeta[selectedRole].title"></span>
                    <span class="text-[10px] font-mono text-slate-400" x-text="'Workspace: ' + roleMeta[selectedRole].destination"></span>
                </div>
                <p class="text-[11px] text-slate-300" x-text="roleMeta[selectedRole].desc"></p>
            </div>

            <!-- Actual Authentication Form -->
            <form method="post" action="{{ route('login.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Work Email Address *</label>
                    <input 
                        type="email" 
                        name="email" 
                        x-model="email" 
                        required 
                        placeholder="name@property.com" 
                        class="w-full rounded-xl border border-slate-800 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none transition">
                    @error('email')
                    <p class="mt-1.5 text-xs font-medium text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-400">Password *</label>
                        <a href="{{ route('password.request') }}" class="text-xs text-amber-400 hover:underline">Forgot password?</a>
                    </div>
                    <input 
                        type="password" 
                        name="password" 
                        x-model="password" 
                        required 
                        placeholder="••••••••••••" 
                        class="w-full rounded-xl border border-slate-800 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none transition">
                    @error('password')
                    <p class="mt-1.5 text-xs font-medium text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400 pt-0.5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-800 bg-slate-900 text-amber-500 focus:ring-0">
                        <span>Keep me signed in</span>
                    </label>
                </div>

                <button 
                    type="submit" 
                    class="w-full rounded-xl gold-gradient py-3 text-sm font-bold text-slate-950 shadow-lg shadow-amber-500/20 hover:brightness-110 transition cursor-pointer flex items-center justify-center gap-2">
                    <span>Continue to Workspace</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            @if(app()->environment('local', 'testing'))
            <!-- DEVELOPMENT ONLY DEMO QUICK-FILL PANEL -->
            <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-3.5 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-amber-400">⚡ Demo Accounts (Development Only)</span>
                    <span class="text-[10px] text-slate-400">Local Environment</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-[11px]">
                    <button type="button" @click="selectRole('platform')" class="text-left p-2 rounded-lg bg-slate-900/90 hover:bg-slate-800 text-slate-300 border border-slate-800/80 transition">
                        <strong class="text-amber-300 block text-[11px]">Company Admin</strong>
                        <span class="text-[10px] text-slate-400 font-mono">admin@example.com</span>
                    </button>
                    <button type="button" @click="selectRole('hotel')" class="text-left p-2 rounded-lg bg-slate-900/90 hover:bg-slate-800 text-slate-300 border border-slate-800/80 transition">
                        <strong class="text-blue-300 block text-[11px]">Hotel Admin</strong>
                        <span class="text-[10px] text-slate-400 font-mono">admin@example.com</span>
                    </button>
                    <button type="button" @click="selectRole('housekeeping')" class="text-left p-2 rounded-lg bg-slate-900/90 hover:bg-slate-800 text-slate-300 border border-slate-800/80 transition">
                        <strong class="text-purple-300 block text-[11px]">Housekeeping Lead</strong>
                        <span class="text-[10px] text-slate-400 font-mono truncate block">maria.santos@...</span>
                    </button>
                    <button type="button" @click="selectRole('restaurant')" class="text-left p-2 rounded-lg bg-slate-900/90 hover:bg-slate-800 text-slate-300 border border-slate-800/80 transition">
                        <strong class="text-rose-300 block text-[11px]">Executive Chef</strong>
                        <span class="text-[10px] text-slate-400 font-mono truncate block">chef.marcus@...</span>
                    </button>
                </div>
            </div>
            @endif
        </div>

        <!-- Footer Notice -->
        <div class="text-center text-[11px] text-slate-500 pt-6 border-t border-slate-900">
            Protected by server-enforced RBAC, rate-limiting, and cryptographic session tokens.
        </div>
    </div>
</div>

</body>
</html>
