<!doctype html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SaaS Company Master Hub' }} · Hotel Guest Platform Provider</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 antialiased selection:bg-amber-500 selection:text-slate-950">

<div class="flex min-h-screen flex-col lg:flex-row">
    <!-- Super Admin SaaS Sidebar -->
    <aside class="w-full lg:w-72 flex-shrink-0 border-r border-slate-800/80 bg-slate-900/90 backdrop-blur-xl flex flex-col justify-between">
        <div>
            <!-- SaaS Brand Header -->
            <div class="flex h-20 items-center justify-between border-b border-slate-800/80 px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 via-amber-600 to-amber-800 text-slate-950 font-black text-xl shadow-lg shadow-amber-950/50">
                        S
                    </div>
                    <div>
                        <p class="font-bold text-sm tracking-tight text-white">SaaS Master Engine</p>
                        <span class="rounded bg-amber-500/10 px-1.5 py-0.5 text-[10px] font-mono text-amber-400 border border-amber-500/20">Company Platform Admin</span>
                    </div>
                </div>
            </div>

            <!-- SaaS Navigation Links -->
            <nav class="p-4 space-y-1.5">
                <div class="px-3 pb-1 pt-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">SaaS Management</div>

                <a href="{{ route('platform.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('platform.dashboard') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20 font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                    <x-icon name="dashboard" class="w-5 h-5 text-amber-400" />
                    <span>SaaS Overview</span>
                </a>

                <a href="{{ route('platform.hotels.index') }}" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('platform.hotels.*') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20 font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                    <div class="flex items-center gap-3">
                        <x-icon name="bed" class="w-5 h-5 text-blue-400" />
                        <span>Hotel Onboarding & Tenants</span>
                    </div>
                </a>

                <div class="px-3 pb-1 pt-4 text-[10px] font-bold uppercase tracking-wider text-slate-400">Finance & Billing</div>

                <a href="{{ route('platform.billing.index') }}" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('platform.billing.*') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20 font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                    <div class="flex items-center gap-3">
                        <x-icon name="orders" class="w-5 h-5 text-emerald-400" />
                        <span>SaaS Invoices & Bills</span>
                    </div>
                </a>

                <div class="px-3 pb-1 pt-4 text-[10px] font-bold uppercase tracking-wider text-slate-400">Tenant Relations</div>

                <a href="{{ route('platform.communications.index') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('platform.communications.*') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20 font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                    <x-icon name="chat" class="w-5 h-5 text-purple-400" />
                    <span>Send Mail to Hotels & Outlets</span>
                </a>
            </nav>
        </div>

        <!-- Super Admin Profile & Sign Out -->
        <div class="border-t border-slate-800/80 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-800 text-amber-400 text-sm font-bold border border-slate-700">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'S', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-semibold text-white">{{ auth()->user()?->name }}</p>
                        <p class="truncate text-[10px] text-amber-400">Platform Company Admin</p>
                    </div>
                </div>

                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-xl p-2 text-slate-400 hover:bg-slate-800 hover:text-rose-400 transition" title="Sign out of Platform">
                        <x-icon name="logout" class="w-4 h-4" />
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main SaaS Content Workspace -->
    <div class="flex flex-1 flex-col overflow-hidden">
        <!-- Top SaaS Operations Navbar -->
        <header class="flex h-20 items-center justify-between border-b border-slate-800/80 bg-slate-900/60 px-6 backdrop-blur-xl">
            <div class="flex items-center gap-3">
                <span class="rounded-full bg-emerald-500/20 px-2.5 py-0.5 text-xs font-semibold text-emerald-400 border border-emerald-500/30 flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Multi-Tenant Cloud Engine Online
                </span>
                <span class="hidden md:inline text-xs text-slate-500">|</span>
                <span class="hidden md:inline text-xs text-slate-400">Global Currency: <strong class="text-white font-mono">INR (₹)</strong></span>
            </div>

            <div class="flex items-center gap-3">
                <!-- Clock -->
                <div class="hidden sm:flex items-center gap-2 rounded-xl border border-slate-800 bg-slate-900/60 px-3.5 py-2 text-xs font-mono text-slate-300">
                    <x-icon name="clock" class="w-3.5 h-3.5 text-amber-400" />
                    <span>{{ now()->timezone('Asia/Kolkata')->format('d M Y · H:i T') }}</span>
                </div>
            </div>
        </header>

        <!-- Main Workspace -->
        <main class="flex-1 overflow-y-auto bg-slate-950 p-6 lg:p-8">
            <div class="mx-auto max-w-7xl">
                <!-- Flash Status Notification -->
                @if(session('status'))
                <div class="mb-6 flex items-center justify-between rounded-2xl border border-emerald-500/30 bg-emerald-950/40 px-5 py-4 text-sm text-emerald-300 backdrop-blur">
                    <div class="flex items-center gap-3">
                        <x-icon name="check-circle" class="w-5 h-5 text-emerald-400" />
                        <span>{{ session('status') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 flex items-center justify-between rounded-2xl border border-rose-500/30 bg-rose-950/40 px-5 py-4 text-sm text-rose-300 backdrop-blur">
                    <div class="flex items-center gap-3">
                        <x-icon name="alert" class="w-5 h-5 text-rose-400" />
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white">
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
