<!doctype html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? ($currentHotel->name ?? config('app.name')) }} · Operations Command</title>
    
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/guestel-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    <script>
    function adminApp() {
        return {
            showHotelModal: false,
            searchOpen: false,
            searchQuery: '',
            searching: false,
            searchResults: [],
            audioEnabled: localStorage.getItem('hospitality_audio') === '1',
            currentTime: '',

            init() {
                this.updateClock();
                setInterval(() => this.updateClock(), 1000);
            },

            updateClock() {
                const now = new Date();
                this.currentTime = now.toTimeString().split(' ')[0] + ' UTC';
            },

            toggleAudio() {
                this.audioEnabled = !this.audioEnabled;
                localStorage.setItem('hospitality_audio', this.audioEnabled ? '1' : '0');
                if (this.audioEnabled) {
                    this.playChime();
                }
            },

            playChime() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(587.33, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15);
                    gain.gain.setValueAtTime(0.2, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.35);
                } catch (e) {
                    console.warn('Audio synthesis disabled', e);
                }
            },

            openSearch() {
                this.searchOpen = true;
                this.searchQuery = '';
                this.searchResults = [];
                setTimeout(() => {
                    const el = document.getElementById('admin-quick-search-input');
                    if (el) el.focus();
                }, 50);
            },

            async performSearch() {
                if (this.searchQuery.length < 2) {
                    this.searchResults = [];
                    return;
                }
                this.searching = true;
                try {
                    const res = await fetch(`{{ route('admin.search') }}?q=${encodeURIComponent(this.searchQuery)}`);
                    const data = await res.json();
                    this.searchResults = data.results || [];
                } catch (e) {
                    console.error('Search error', e);
                } finally {
                    this.searching = false;
                }
            }
        };
    }
    </script>
</head>
<body class="h-full bg-slate-950 text-slate-100 antialiased" x-data="adminApp()" @auth @keydown.window.prevent.ctrl.k="openSearch()" @keydown.window.prevent.cmd.k="openSearch()" @endauth>

@auth
<div class="flex min-h-screen">
    <!-- Desktop Sidebar -->
    <aside class="hidden w-72 flex-col border-r border-slate-800/80 bg-slate-900/90 backdrop-blur-xl lg:flex">
        <!-- Guestel Brand Header -->
        <div class="px-6 py-3.5 border-b border-slate-800/80 bg-slate-950/70 flex items-center justify-between">
            <x-brand-logo size="sm" tagline="Operations Cloud" theme="dark" :href="route('admin.dashboard')" />
            <span class="rounded bg-emerald-500/10 px-2 py-0.5 text-[10px] font-mono text-emerald-400 border border-emerald-500/20 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Live
            </span>
        </div>

        <!-- Property Badge -->
        <div class="flex items-center justify-between border-b border-slate-800/80 px-6 py-3 bg-slate-900/60">
            <div class="flex items-center gap-3 min-w-0">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 via-amber-600 to-amber-800 shadow-md text-slate-950 font-bold text-sm">
                    {{ strtoupper(substr($currentHotel->name ?? 'H', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="truncate font-semibold tracking-tight text-white text-xs">{{ $currentHotel->name ?? config('app.name') }}</p>
                    <p class="truncate text-[10px] text-slate-400">{{ $currentHotel->city ?? 'Active Property' }}</p>
                </div>
            </div>
            @if($userHotels->count() > 1)
            <button @click="showHotelModal = true" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-800 hover:text-white transition" title="Switch property">
                <x-icon name="chevron-down" class="w-4 h-4" />
            </button>
            @endif
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto px-4 py-6 space-y-1">
            @if(auth()->user()->hasPermission('hotel.view', $currentHotel->id))
            <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Command Center</div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                <x-icon name="dashboard" class="w-5 h-5 text-amber-400" />
                <span>Operations Hub</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('requests.view', $currentHotel->id) || auth()->user()->hasPermission('orders.view', $currentHotel->id) || auth()->user()->hasPermission('chat.manage', $currentHotel->id))
            <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Live Service Dispatch</div>
            @endif

            @if(auth()->user()->hasPermission('requests.view', $currentHotel->id))
            <a href="{{ route('admin.requests.index') }}" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.requests.*') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                <div class="flex items-center gap-3">
                    <x-icon name="requests" class="w-5 h-5 text-blue-400" />
                    <span>{{ auth()->user()->hasPermission('hotel.view', $currentHotel->id) ? 'Guest Requests' : 'Housekeeping Queue' }}</span>
                </div>
                @if(($globalPendingRequests ?? 0) > 0)
                <span class="rounded-full bg-blue-500/20 px-2 py-0.5 text-xs font-semibold text-blue-300 border border-blue-500/30">{{ $globalPendingRequests }}</span>
                @endif
            </a>
            @endif

            @if(auth()->user()->hasPermission('orders.view', $currentHotel->id))
            <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.orders.*') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                <div class="flex items-center gap-3">
                    <x-icon name="orders" class="w-5 h-5 text-amber-400" />
                    <span>{{ auth()->user()->hasPermission('hotel.view', $currentHotel->id) ? 'In-Room Dining' : 'Kitchen Operations' }}</span>
                </div>
                @if(($globalPendingOrders ?? 0) > 0)
                <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-xs font-semibold text-amber-300 border border-amber-500/30">{{ $globalPendingOrders }}</span>
                @endif
            </a>
            @endif

            @if(auth()->user()->hasPermission('chat.manage', $currentHotel->id))
            <a href="{{ route('admin.chat.index') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.chat.*') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                <x-icon name="chat" class="w-5 h-5 text-teal-400" />
                <span>Guest Concierge Chat</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('rooms.view', $currentHotel->id) || auth()->user()->hasPermission('menu.manage', $currentHotel->id) || auth()->user()->hasPermission('settings.manage', $currentHotel->id))
            <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Inventory & Hospitality</div>
            @endif

            @if(auth()->user()->hasPermission('rooms.view', $currentHotel->id))
            <a href="{{ route('admin.rooms.index') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.rooms.*') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                <x-icon name="bed" class="w-5 h-5 text-purple-400" />
                <span>Rooms & Status</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('qr.manage', $currentHotel->id))
            <a href="{{ route('admin.qr-center.index') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.qr-center.*') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                <x-icon name="qr" class="w-5 h-5 text-emerald-400" />
                <span>QR Print Center</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('menu.manage', $currentHotel->id))
            <a href="{{ route('admin.restaurant.index') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.restaurant.*') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                <x-icon name="restaurant" class="w-5 h-5 text-rose-400" />
                <span>Restaurant & Menu</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('settings.manage', $currentHotel->id))
            <a href="{{ route('admin.gallery.index') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.gallery.*') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                <x-icon name="gallery" class="w-5 h-5 text-indigo-400" />
                <span>Media & Gallery</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('analytics.view', $currentHotel->id) || auth()->user()->hasPermission('notifications.view', $currentHotel->id))
            <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Intelligence & Logs</div>
            @endif

            @if(auth()->user()->hasPermission('analytics.view', $currentHotel->id))
            <a href="{{ route('admin.analytics') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.analytics') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                <x-icon name="analytics" class="w-5 h-5 text-amber-400" />
                <span>Analytics & SLA</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('notifications.view', $currentHotel->id))
            <a href="{{ route('admin.notifications.index') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.notifications.*') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                <x-icon name="notifications" class="w-5 h-5 text-yellow-400" />
                <span>Operational Alerts</span>
            </a>
            @endif

            @if(auth()->user()?->is_platform_admin)
            <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-amber-400">Super Platform</div>
            <a href="{{ route('platform.hotels.index') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('platform.*') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                <x-icon name="shield" class="w-5 h-5 text-amber-400" />
                <span>Hotels & SaaS Tenants</span>
            </a>
            @endif
        </div>

        <!-- User Profile Bar -->
        <div class="border-t border-slate-800/80 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-800 text-slate-300 text-sm font-semibold">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-semibold text-white">{{ auth()->user()?->name }}</p>
                        <p class="truncate text-[10px] text-amber-400 font-semibold">{{ auth()->user()?->primaryRole($currentHotel->id ?? null)?->label ?? (auth()->user()?->is_platform_admin ? 'Company Super Admin' : 'Hotel Staff') }}</p>
                    </div>
                </div>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-lg p-2 text-slate-400 hover:bg-slate-800 hover:text-rose-400 transition" title="Sign out">
                        <x-icon name="logout" class="w-4 h-4" />
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex flex-1 flex-col overflow-hidden">
        <!-- Top Operational Navbar -->
        <header class="flex h-20 items-center justify-between border-b border-slate-800/80 bg-slate-900/60 px-6 backdrop-blur-xl">
            <!-- Left: Search trigger & property selector -->
            <div class="flex items-center gap-4">
                <button @click="openSearch()" class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-900/80 px-4 py-2.5 text-xs text-slate-400 shadow-inner hover:border-slate-700 hover:text-slate-200 transition sm:w-72">
                    <x-icon name="search" class="w-4 h-4 text-slate-500" />
                    <span>Quick search (Rooms, Orders)...</span>
                    <kbd class="ml-auto hidden rounded bg-slate-800 px-1.5 py-0.5 text-[10px] font-mono text-slate-400 sm:inline-block">Ctrl K</kbd>
                </button>

                <div class="hidden md:flex items-center gap-2">
                    <span class="text-xs text-slate-500">Property:</span>
                    <button @click="showHotelModal = true" class="flex items-center gap-1.5 rounded-lg border border-slate-800 bg-slate-900 px-3 py-1.5 text-xs font-medium text-slate-200 hover:border-slate-700">
                        <span>{{ $currentHotel->name ?? 'Choose Property' }}</span>
                        <x-icon name="chevron-down" class="w-3.5 h-3.5 text-slate-400" />
                    </button>
                    <span class="rounded-full bg-slate-800/80 px-2.5 py-0.5 text-[10px] font-semibold text-amber-400 border border-slate-700">
                        {{ auth()->user()?->primaryRole($currentHotel->id ?? null)?->label ?? 'Staff' }}
                    </span>
                    <a href="{{ route('property.select') }}" class="text-[11px] text-slate-400 hover:text-white transition ml-1" title="Switch Workspace">
                        Switch ▾
                    </a>
                </div>

                <!-- ⚡ Quick Test Persona Switcher -->
                <div class="hidden xl:flex items-center gap-1.5 bg-slate-800/80 border border-slate-700/80 px-2 py-1 rounded-xl">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mr-1">Switch:</span>
                    <form method="POST" action="{{ route('login.test') }}" class="inline">
                        @csrf
                        <input type="hidden" name="role" value="super_admin">
                        <button type="submit" class="px-2 py-0.5 rounded-lg text-[10px] font-bold hover:bg-amber-500/20 text-amber-300 transition cursor-pointer">🏢 Super Admin</button>
                    </form>
                    <form method="POST" action="{{ route('login.test') }}" class="inline">
                        @csrf
                        <input type="hidden" name="role" value="hotel_admin">
                        <button type="submit" class="px-2 py-0.5 rounded-lg text-[10px] font-bold hover:bg-blue-500/20 text-blue-300 transition cursor-pointer">🏨 Hotel GM</button>
                    </form>
                    <form method="POST" action="{{ route('login.test') }}" class="inline">
                        @csrf
                        <input type="hidden" name="role" value="housekeeping">
                        <button type="submit" class="px-2 py-0.5 rounded-lg text-[10px] font-bold hover:bg-purple-500/20 text-purple-300 transition cursor-pointer">🧹 Housekeeping</button>
                    </form>
                    <form method="POST" action="{{ route('login.test') }}" class="inline">
                        @csrf
                        <input type="hidden" name="role" value="chef">
                        <button type="submit" class="px-2 py-0.5 rounded-lg text-[10px] font-bold hover:bg-rose-500/20 text-rose-300 transition cursor-pointer">👨‍🍳 Chef</button>
                    </form>
                </div>
            </div>

            <!-- Right: Live Indicators, Audio alert toggle, & Clock -->
            <div class="flex items-center gap-3">
                @if(auth()->user()?->is_platform_admin)
                    @if(request()->routeIs('platform.*'))
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 rounded-xl border border-slate-700 bg-slate-800/90 px-3 py-2 text-xs font-semibold text-slate-200 hover:border-slate-600 hover:text-white transition">
                        <x-icon name="dashboard" class="w-4 h-4 text-amber-400" />
                        <span class="hidden sm:inline">Operations Hub</span>
                    </a>
                    @else
                    <a href="{{ route('platform.hotels.index') }}" class="flex items-center gap-1.5 rounded-xl border border-amber-500/30 bg-amber-500/10 px-3 py-2 text-xs font-semibold text-amber-300 hover:bg-amber-500/20 transition">
                        <x-icon name="shield" class="w-4 h-4 text-amber-400" />
                        <span class="hidden sm:inline">Platform Super Admin</span>
                    </a>
                    @endif
                @endif

                <!-- Audio Alert Toggle -->
                <button @click="toggleAudio()" :class="audioEnabled ? 'border-amber-500/40 bg-amber-500/10 text-amber-300' : 'border-slate-800 text-slate-400'" class="flex items-center gap-2 rounded-xl border px-3 py-2 text-xs font-medium transition" :title="audioEnabled ? 'Sound alerts active' : 'Sound alerts muted'">
                    <span x-show="audioEnabled" class="flex items-center"><x-icon name="volume-on" class="w-4 h-4" /></span>
                    <span x-show="!audioEnabled" class="flex items-center"><x-icon name="volume-off" class="w-4 h-4" /></span>
                    <span class="hidden sm:inline" x-text="audioEnabled ? 'Audio Alerts: On' : 'Audio Alerts: Off'"></span>
                </button>

                <!-- UTC Live Clock -->
                <div class="hidden items-center gap-2 rounded-xl border border-slate-800 bg-slate-900/60 px-3.5 py-2 text-xs font-mono text-slate-300 md:flex">
                    <x-icon name="clock" class="w-3.5 h-3.5 text-amber-400" />
                    <span x-text="currentTime"></span>
                </div>

                <!-- Notifications Pill -->
                <a href="{{ route('admin.notifications.index') }}" class="relative rounded-xl border border-slate-800 p-2.5 text-slate-300 hover:border-slate-700 transition">
                    <x-icon name="notifications" class="w-4 h-4" />
                    @if(($globalPendingRequests + $globalPendingOrders) > 0)
                    <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white ring-2 ring-slate-950">
                        {{ min($globalPendingRequests + $globalPendingOrders, 99) }}
                    </span>
                    @endif
                </a>
            </div>
        </header>

        <!-- Main Workspace -->
        <main class="flex-1 overflow-y-auto bg-slate-950 p-6 lg:p-8">
            <div class="mx-auto max-w-7xl">
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

                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>
    </div>
</div>

<!-- Hotel Switcher Modal -->
<div x-show="showHotelModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm">
    <div @click.away="showHotelModal = false" class="w-full max-w-md rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <h3 class="font-semibold text-white">Switch Property</h3>
            <button @click="showHotelModal = false" class="text-slate-400 hover:text-white">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>
        <div class="mt-4 space-y-2 max-h-80 overflow-y-auto">
            @foreach($userHotels as $h)
            <form method="post" action="{{ route('admin.switch-hotel', $h->id) }}">
                @csrf
                <button type="submit" class="flex w-full items-center justify-between rounded-xl p-3 text-left transition {{ ($currentHotel->id ?? 0) === $h->id ? 'bg-amber-500/10 border border-amber-500/30 text-amber-300' : 'bg-slate-800/60 hover:bg-slate-800 text-slate-200' }}">
                    <div>
                        <div class="font-medium text-sm">{{ $h->name }}</div>
                        <div class="text-xs text-slate-400">{{ $h->city ?? 'Location not specified' }}</div>
                    </div>
                    @if(($currentHotel->id ?? 0) === $h->id)
                    <span class="rounded bg-amber-500/20 px-2 py-0.5 text-[10px] font-semibold text-amber-300">Active</span>
                    @endif
                </button>
            </form>
            @endforeach
            <a href="{{ route('property.select') }}" class="block text-center mt-3 pt-3 border-t border-slate-800 text-xs text-amber-400 hover:underline">
                Where are you working today? Switch Workspace →
            </a>
        </div>
    </div>
</div>

<!-- Ctrl+K Quick Search Command Modal -->
<div x-show="searchOpen" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-start justify-center bg-black/80 pt-20 p-4 backdrop-blur-sm">
    <div @click.away="searchOpen = false" class="w-full max-w-2xl rounded-3xl border border-slate-800 bg-slate-900 shadow-2xl overflow-hidden">
        <div class="flex items-center border-b border-slate-800 px-4">
            <x-icon name="search" class="w-5 h-5 text-slate-400 ml-2" />
            <input id="admin-quick-search-input" x-model="searchQuery" @input.debounce.250ms="performSearch()" type="text" placeholder="Type to search rooms, orders, requests, menu items..." class="w-full bg-transparent px-4 py-4 text-sm text-white placeholder-slate-500 focus:outline-none">
            <kbd @click="searchOpen = false" class="cursor-pointer rounded border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-400">ESC</kbd>
        </div>

        <div class="max-h-96 overflow-y-auto p-4 space-y-2">
            <div x-show="searching" x-cloak style="display: none;" class="p-6 text-center text-xs text-slate-500">Searching operations database...</div>
            
            <template x-if="!searching && searchResults.length === 0 && searchQuery.length >= 2">
                <div class="p-6 text-center text-xs text-slate-500">No matching operations records found.</div>
            </template>

            <template x-for="item in searchResults" :key="item.url + item.title">
                <a :href="item.url" class="flex items-center justify-between rounded-xl bg-slate-800/40 p-3 hover:bg-slate-800 transition">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="rounded bg-slate-700/60 px-1.5 py-0.5 text-[10px] font-mono text-amber-300" x-text="item.category"></span>
                            <span class="font-semibold text-sm text-white truncate" x-text="item.title"></span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5 truncate" x-text="item.subtitle"></p>
                    </div>
                    <span class="text-xs rounded-full px-2 py-0.5 font-medium" :class="item.badge === 'available' || item.badge === 'COMPLETED' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-slate-700 text-slate-300'" x-text="item.badge"></span>
                </a>
            </template>
        </div>
    </div>
</div>
@else
<div class="min-h-screen flex items-center justify-center p-4 bg-slate-950">
    <div class="w-full max-w-md">
        @if(session('status'))
        <div class="mb-4 rounded-xl border border-emerald-500/30 bg-emerald-950/40 p-4 text-xs text-emerald-300">
            {{ session('status') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mb-4 rounded-xl border border-rose-500/30 bg-rose-950/40 p-4 text-xs text-rose-300">
            {{ session('error') }}
        </div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </div>
</div>
@endauth

@livewireScripts


</body>
</html>
