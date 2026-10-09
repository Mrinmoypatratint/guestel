<!doctype html>
<html lang="en" class="h-full bg-[#e8edf5] text-slate-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SaaS Company Master Hub' }} · Guestel Cloud OS</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/guestel-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; background-color: #e8edf5; color: #1e293b; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full bg-[#e8edf5] text-slate-800 antialiased selection:bg-[#00214D] selection:text-white">

<div class="flex min-h-screen flex-col lg:flex-row">
    <!-- Super Admin SaaS Sidebar (Neumorphic) -->
    <aside class="w-full lg:w-72 flex-shrink-0 bg-[#e8edf5] border-r border-white/80 shadow-[6px_0_20px_rgba(202,211,223,0.35)] flex flex-col justify-between">
        <div>
            <!-- Guestel Brand Header -->
            <div class="flex h-20 items-center justify-between border-b border-slate-300/60 px-6">
                <x-brand-logo size="md" tagline="SaaS Platform Admin" theme="light" :href="route('platform.dashboard')" />
                <span class="neu-pill-inset px-2.5 py-0.5 text-[10px] font-mono font-bold text-[#00214D]">Super Admin</span>
            </div>

            <!-- SaaS Navigation Links (Tactile Neumorphic) -->
            <nav class="p-4 space-y-2">
                <div class="px-3 pb-1 pt-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">SaaS Management</div>

                <a href="{{ route('platform.dashboard') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-xs font-bold transition {{ request()->routeIs('platform.dashboard') ? 'neu-inset text-[#00214D]' : 'text-slate-600 hover:neu-button hover:text-slate-900' }}">
                    <x-icon name="dashboard" class="w-4 h-4 {{ request()->routeIs('platform.dashboard') ? 'text-[#0073E6]' : 'text-slate-500' }}" />
                    <span>SaaS Overview</span>
                </a>

                <a href="{{ route('platform.hotels.index') }}" class="flex items-center justify-between rounded-2xl px-4 py-3 text-xs font-bold transition {{ request()->routeIs('platform.hotels.*') ? 'neu-inset text-[#00214D]' : 'text-slate-600 hover:neu-button hover:text-slate-900' }}">
                    <div class="flex items-center gap-3">
                        <x-icon name="bed" class="w-4 h-4 {{ request()->routeIs('platform.hotels.*') ? 'text-[#0073E6]' : 'text-slate-500' }}" />
                        <span>Hotel Tenants</span>
                    </div>
                </a>

                <div class="px-3 pb-1 pt-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">Finance & Billing</div>

                <a href="{{ route('platform.billing.index') }}" class="flex items-center justify-between rounded-2xl px-4 py-3 text-xs font-bold transition {{ request()->routeIs('platform.billing.*') ? 'neu-inset text-[#00214D]' : 'text-slate-600 hover:neu-button hover:text-slate-900' }}">
                    <div class="flex items-center gap-3">
                        <x-icon name="orders" class="w-4 h-4 {{ request()->routeIs('platform.billing.*') ? 'text-[#0073E6]' : 'text-slate-500' }}" />
                        <span>SaaS Invoices & Bills</span>
                    </div>
                </a>

                <div class="px-3 pb-1 pt-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">Tenant Relations</div>

                <a href="{{ route('platform.communications.index') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-xs font-bold transition {{ request()->routeIs('platform.communications.*') ? 'neu-inset text-[#00214D]' : 'text-slate-600 hover:neu-button hover:text-slate-900' }}">
                    <x-icon name="chat" class="w-4 h-4 {{ request()->routeIs('platform.communications.*') ? 'text-[#0073E6]' : 'text-slate-500' }}" />
                    <span>Broadcast Notices</span>
                </a>

                <div class="px-3 pb-1 pt-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">Operations Hub</div>

                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-xs font-bold text-slate-600 hover:neu-button hover:text-slate-900 transition">
                    <x-icon name="dashboard" class="w-4 h-4 text-slate-500" />
                    <span>Go to Operations Hub →</span>
                </a>
            </nav>
        </div>

        <!-- Super Admin Profile & Sign Out (Neumorphic Card) -->
        <div class="p-4 border-t border-slate-300/60">
            <div class="neu-flat rounded-2xl p-3 flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl neu-button text-[#00214D] text-xs font-extrabold">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'S', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-bold text-slate-900">{{ auth()->user()?->name }}</p>
                        <p class="truncate text-[10px] font-semibold text-slate-500">Platform Admin</p>
                    </div>
                </div>

                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-xl p-2 neu-button text-slate-500 hover:text-rose-600 transition" title="Sign out of Platform">
                        <x-icon name="logout" class="w-4 h-4" />
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main SaaS Content Workspace -->
    <div class="flex flex-1 flex-col overflow-hidden bg-[#e8edf5]">
        <!-- Top SaaS Operations Navbar (Neumorphic) -->
        <header class="flex h-20 items-center justify-between border-b border-slate-300/60 bg-[#e8edf5]/90 px-6 backdrop-blur-md shadow-[0_4px_16px_rgba(202,211,223,0.3)]">
            <div class="flex items-center gap-3">
                <span class="neu-pill px-3 py-1 text-xs font-bold text-slate-700 flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Multi-Tenant Cloud Engine Online</span>
                </span>
                <span class="hidden md:inline text-xs text-slate-400">|</span>
                <span class="hidden md:inline text-xs text-slate-600 font-semibold">Global Currency: <strong class="text-[#00214D] font-mono">INR (₹)</strong></span>
            </div>

            <div class="flex items-center gap-3">
                <!-- Clock -->
                <div class="hidden sm:flex items-center gap-2 rounded-xl neu-flat-sm px-3.5 py-1.5 text-xs font-mono text-slate-700 font-semibold">
                    <x-icon name="clock" class="w-3.5 h-3.5 text-[#0073E6]" />
                    <span>{{ now()->timezone('Asia/Kolkata')->format('d M Y · H:i T') }}</span>
                </div>
            </div>
        </header>

        <!-- Main Workspace -->
        <main class="flex-1 overflow-y-auto bg-[#e8edf5] p-6 lg:p-8">
            <div class="mx-auto max-w-7xl">
                <!-- Flash Status Notification -->
                @if(session('status'))
                <div class="mb-6 flex items-center justify-between rounded-2xl neu-flat border border-emerald-400/40 bg-emerald-50/80 px-5 py-4 text-sm text-emerald-900">
                    <div class="flex items-center gap-3">
                        <x-icon name="check-circle" class="w-5 h-5 text-emerald-600" />
                        <span class="font-semibold">{{ session('status') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900">
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 flex items-center justify-between rounded-2xl neu-flat border border-rose-400/40 bg-rose-50/80 px-5 py-4 text-sm text-rose-900">
                    <div class="flex items-center gap-3">
                        <x-icon name="alert" class="w-5 h-5 text-rose-600" />
                        <span class="font-semibold">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900">
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</div>

</body>
</html>
