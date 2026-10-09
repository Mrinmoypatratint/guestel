<!doctype html>
<html lang="en" class="h-full bg-[#e8edf5] text-slate-800">
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; background-color: #e8edf5; color: #1e293b; }
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
                if (this.searchQuery.trim().length < 2) {
                    this.searchResults = [];
                    return;
                }
                this.searching = true;
                try {
                    const res = await fetch(`{{ route('admin.search') }}?q=${encodeURIComponent(this.searchQuery)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
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
<body class="h-full bg-[#e8edf5] text-slate-800 antialiased selection:bg-[#00214D] selection:text-white" x-data="adminApp()" @auth @keydown.window.prevent.ctrl.k="openSearch()" @keydown.window.prevent.cmd.k="openSearch()" @endauth>

@auth
<div class="flex min-h-screen">
    <!-- Desktop Sidebar (Neumorphic) -->
    <aside class="hidden w-72 flex-col bg-[#e8edf5] border-r border-white/80 shadow-[6px_0_20px_rgba(202,211,223,0.35)] lg:flex justify-between">
        <div class="flex-1 flex flex-col min-h-0">
            <!-- Guestel Brand Header -->
            <div class="px-6 py-4 border-b border-slate-300/60 bg-[#e8edf5] flex items-center justify-between">
                <x-brand-logo size="sm" tagline="Operations Cloud" theme="light" :href="route('admin.dashboard')" />
                <span class="neu-pill px-2 py-0.5 text-[10px] font-mono text-emerald-700 font-bold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Live
                </span>
            </div>

            <!-- Property Badge (Neumorphic Well) -->
            <div class="neu-inset mx-4 my-3 p-3 rounded-2xl flex items-center justify-between">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl neu-button text-[#00214D] font-black text-xs">
                        {{ strtoupper(substr($currentHotel->name ?? 'H', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate font-extrabold text-slate-900 text-xs">{{ $currentHotel->name ?? config('app.name') }}</p>
                        <p class="truncate text-[10px] text-slate-500 font-medium">{{ $currentHotel->city ?? 'Active Property' }}</p>
                    </div>
                </div>
                @if($userHotels->count() > 1)
                <button @click="showHotelModal = true" class="neu-button rounded-lg p-1 text-slate-600 hover:text-slate-900 transition" title="Switch property">
                    <x-icon name="chevron-down" class="w-3.5 h-3.5" />
                </button>
                @endif
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 py-2 space-y-1.5">
                @if(auth()->user()->hasPermission('hotel.view', $currentHotel->id))
                <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">Command Center</div>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-xs font-bold transition {{ request()->routeIs('admin.dashboard') ? 'neu-inset text-[#00214D]' : 'text-slate-600 hover:neu-button hover:text-slate-900' }}">
                    <x-icon name="dashboard" class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-[#0073E6]' : 'text-slate-500' }}" />
                    <span>Operations Hub</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('requests.view', $currentHotel->id) || auth()->user()->hasPermission('orders.view', $currentHotel->id) || auth()->user()->hasPermission('chat.manage', $currentHotel->id))
                <div class="pt-4 px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">Live Service Dispatch</div>
                @endif

                @if(auth()->user()->hasPermission('requests.view', $currentHotel->id))
                <a href="{{ route('admin.requests.index') }}" class="flex items-center justify-between rounded-2xl px-3.5 py-2.5 text-xs font-bold transition {{ request()->routeIs('admin.requests.*') ? 'neu-inset text-[#00214D]' : 'text-slate-600 hover:neu-button hover:text-slate-900' }}">
                    <div class="flex items-center gap-3">
                        <x-icon name="requests" class="w-4 h-4 text-blue-600" />
                        <span>{{ auth()->user()->hasPermission('hotel.view', $currentHotel->id) ? 'Guest Requests' : 'Housekeeping Queue' }}</span>
                    </div>
                    @if(($globalPendingRequests ?? 0) > 0)
                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-800 border border-blue-300">{{ $globalPendingRequests }}</span>
                    @endif
                </a>
                @endif

                @if(auth()->user()->hasPermission('orders.view', $currentHotel->id))
                <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between rounded-2xl px-3.5 py-2.5 text-xs font-bold transition {{ request()->routeIs('admin.orders.*') ? 'neu-inset text-[#00214D]' : 'text-slate-600 hover:neu-button hover:text-slate-900' }}">
                    <div class="flex items-center gap-3">
                        <x-icon name="orders" class="w-4 h-4 text-amber-600" />
                        <span>{{ auth()->user()->hasPermission('hotel.view', $currentHotel->id) ? 'In-Room Dining' : 'Kitchen Operations' }}</span>
                    </div>
                    @if(($globalPendingOrders ?? 0) > 0)
                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800 border border-amber-300">{{ $globalPendingOrders }}</span>
                    @endif
                </a>
                @endif

                @if(auth()->user()->hasPermission('chat.manage', $currentHotel->id))
                <a href="{{ route('admin.chat.index') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-xs font-bold transition {{ request()->routeIs('admin.chat.*') ? 'neu-inset text-[#00214D]' : 'text-slate-600 hover:neu-button hover:text-slate-900' }}">
                    <x-icon name="chat" class="w-4 h-4 text-teal-600" />
                    <span>Guest Concierge Chat</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('rooms.view', $currentHotel->id) || auth()->user()->hasPermission('menu.manage', $currentHotel->id) || auth()->user()->hasPermission('settings.manage', $currentHotel->id))
                <div class="pt-4 px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">Inventory & Hospitality</div>
                @endif

                @if(auth()->user()->hasPermission('rooms.view', $currentHotel->id))
                <a href="{{ route('admin.rooms.index') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-xs font-bold transition {{ request()->routeIs('admin.rooms.*') ? 'neu-inset text-[#00214D]' : 'text-slate-600 hover:neu-button hover:text-slate-900' }}">
                    <x-icon name="bed" class="w-4 h-4 text-purple-600" />
                    <span>Rooms & Status</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('qr.manage', $currentHotel->id))
                <a href="{{ route('admin.qr-center.index') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-xs font-bold transition {{ request()->routeIs('admin.qr-center.*') ? 'neu-inset text-[#00214D]' : 'text-slate-600 hover:neu-button hover:text-slate-900' }}">
                    <x-icon name="qr" class="w-4 h-4 text-emerald-600" />
                    <span>QR Print Center</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('menu.manage', $currentHotel->id))
                <a href="{{ route('admin.restaurant.index') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-xs font-bold transition {{ request()->routeIs('admin.restaurant.*') ? 'neu-inset text-[#00214D]' : 'text-slate-600 hover:neu-button hover:text-slate-900' }}">
                    <x-icon name="restaurant" class="w-4 h-4 text-rose-600" />
                    <span>Restaurant & Menu</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('settings.manage', $currentHotel->id))
                <a href="{{ route('admin.gallery.index') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-xs font-bold transition {{ request()->routeIs('admin.gallery.*') ? 'neu-inset text-[#00214D]' : 'text-slate-600 hover:neu-button hover:text-slate-900' }}">
                    <x-icon name="gallery" class="w-4 h-4 text-indigo-600" />
                    <span>Media & Gallery</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('analytics.view', $currentHotel->id) || auth()->user()->hasPermission('notifications.view', $currentHotel->id))
                <div class="pt-4 px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">Intelligence & Logs</div>
                @endif

                @if(auth()->user()->hasPermission('analytics.view', $currentHotel->id))
                <a href="{{ route('admin.analytics') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-xs font-bold transition {{ request()->routeIs('admin.analytics') ? 'neu-inset text-[#00214D]' : 'text-slate-600 hover:neu-button hover:text-slate-900' }}">
                    <x-icon name="analytics" class="w-4 h-4 text-amber-600" />
                    <span>Analytics & SLA</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('notifications.view', $currentHotel->id))
                <a href="{{ route('admin.notifications.index') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-xs font-bold transition {{ request()->routeIs('admin.notifications.*') ? 'neu-inset text-[#00214D]' : 'text-slate-600 hover:neu-button hover:text-slate-900' }}">
                    <x-icon name="notifications" class="w-4 h-4 text-yellow-600" />
                    <span>Operational Alerts</span>
                </a>
                @endif

                @if(auth()->user()?->is_platform_admin)
                <div class="pt-4 px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-[#00214D]">Super Platform</div>
                <a href="{{ route('platform.hotels.index') }}" class="flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-xs font-bold transition {{ request()->routeIs('platform.*') ? 'neu-inset text-[#00214D]' : 'text-slate-600 hover:neu-button hover:text-slate-900' }}">
                    <x-icon name="shield" class="w-4 h-4 text-[#0073E6]" />
                    <span>Hotels & SaaS Tenants</span>
                </a>
                @endif
            </div>
        </div>

        <!-- User Profile Bar (Neumorphic Card) -->
        <div class="p-4 border-t border-slate-300/60">
            <div class="neu-flat rounded-2xl p-3 flex items-center justify-between">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-xl neu-button text-[#00214D] text-xs font-extrabold">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-bold text-slate-900">{{ auth()->user()?->name }}</p>
                        <p class="truncate text-[10px] text-slate-500 font-semibold">{{ auth()->user()?->primaryRole($currentHotel->id ?? null)?->label ?? (auth()->user()?->is_platform_admin ? 'Company Super Admin' : 'Hotel Staff') }}</p>
                    </div>
                </div>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="neu-button rounded-xl p-2 text-slate-500 hover:text-rose-600 transition" title="Sign out">
                        <x-icon name="logout" class="w-4 h-4" />
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex flex-1 flex-col overflow-hidden bg-[#e8edf5]">
        <!-- Top Operational Navbar (Neumorphic) -->
        <header class="flex h-20 items-center justify-between border-b border-slate-300/60 bg-[#e8edf5]/90 px-6 backdrop-blur-md shadow-[0_4px_16px_rgba(202,211,223,0.3)]">
            <!-- Left: Search trigger & property selector -->
            <div class="flex items-center gap-4">
                <button @click="openSearch()" class="flex items-center gap-3 rounded-2xl neu-inset px-4 py-2 text-xs text-slate-600 hover:text-slate-900 transition sm:w-72">
                    <x-icon name="search" class="w-4 h-4 text-slate-500" />
                    <span>Quick search (Rooms, Orders)...</span>
                    <kbd class="ml-auto hidden rounded neu-button px-1.5 py-0.5 text-[10px] font-mono text-slate-600 sm:inline-block">Ctrl K</kbd>
                </button>

                <div class="hidden md:flex items-center gap-2">
                    <span class="text-xs text-slate-500 font-medium">Property:</span>
                    <button @click="showHotelModal = true" class="flex items-center gap-1.5 rounded-xl neu-button px-3 py-1.5 text-xs font-bold text-slate-800">
                        <span>{{ $currentHotel->name ?? 'Choose Property' }}</span>
                        <x-icon name="chevron-down" class="w-3.5 h-3.5 text-slate-500" />
                    </button>
                    <span class="neu-pill-inset px-2.5 py-0.5 text-[10px] font-bold text-[#00214D]">
                        {{ auth()->user()?->primaryRole($currentHotel->id ?? null)?->label ?? 'Staff' }}
                    </span>
                    <a href="{{ route('property.select') }}" class="text-[11px] text-[#0073E6] font-bold hover:underline transition ml-1" title="Switch Workspace">
                        Switch ▾
                    </a>
                </div>
            </div>

            <!-- Right: Live Indicators, Audio alert toggle, & Clock -->
            <div class="flex items-center gap-3">
                @if(auth()->user()?->is_platform_admin)
                    @if(request()->routeIs('platform.*'))
                    <a href="{{ route('admin.dashboard') }}" class="neu-button flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 transition">
                        <x-icon name="dashboard" class="w-4 h-4 text-[#0073E6]" />
                        <span class="hidden sm:inline">Operations Hub</span>
                    </a>
                    @else
                    <a href="{{ route('platform.hotels.index') }}" class="neu-btn-primary flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-bold text-white shadow-md transition">
                        <x-icon name="shield" class="w-4 h-4 text-white" />
                        <span class="hidden sm:inline">Platform Super Admin</span>
                    </a>
                    @endif
                @endif

                <!-- Audio Alert Toggle -->
                <button @click="toggleAudio()" :class="audioEnabled ? 'neu-inset text-emerald-800 font-bold' : 'neu-button text-slate-600'" class="flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold transition" :title="audioEnabled ? 'Sound alerts active' : 'Sound alerts muted'">
                    <span x-show="audioEnabled" class="flex items-center"><x-icon name="volume-on" class="w-4 h-4 text-emerald-600" /></span>
                    <span x-show="!audioEnabled" class="flex items-center"><x-icon name="volume-off" class="w-4 h-4 text-slate-400" /></span>
                    <span class="hidden sm:inline" x-text="audioEnabled ? 'Alerts: On' : 'Alerts: Off'"></span>
                </button>

                <!-- UTC Live Clock -->
                <div class="hidden items-center gap-2 rounded-xl neu-flat-sm px-3.5 py-2 text-xs font-mono text-slate-700 font-semibold md:flex">
                    <x-icon name="clock" class="w-3.5 h-3.5 text-[#0073E6]" />
                    <span x-text="currentTime"></span>
                </div>

                <!-- Notifications Pill -->
                <a href="{{ route('admin.notifications.index') }}" class="relative neu-button rounded-xl p-2.5 text-slate-700 hover:text-slate-900 transition">
                    <x-icon name="notifications" class="w-4 h-4" />
                    @if(($globalPendingRequests + $globalPendingOrders) > 0)
                    <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white shadow-sm">
                        {{ min($globalPendingRequests + $globalPendingOrders, 99) }}
                    </span>
                    @endif
                </a>
            </div>
        </header>

        <!-- Main Workspace -->
        <main class="flex-1 overflow-y-auto bg-[#e8edf5] p-6 lg:p-8">
            <div class="mx-auto max-w-7xl">
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

                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>
    </div>
</div>

<!-- Hotel Switcher Modal (Neumorphic) -->
<div x-show="showHotelModal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
    <div @click.away="showHotelModal = false" class="w-full max-w-md neu-flat-lg rounded-3xl p-6 bg-[#e8edf5] space-y-4">
        <div class="flex items-center justify-between border-b border-slate-300/60 pb-3">
            <h3 class="font-extrabold text-slate-900">Switch Property</h3>
            <button @click="showHotelModal = false" class="neu-button rounded-xl h-8 w-8 flex items-center justify-center text-slate-600 hover:text-slate-900 font-bold">&times;</button>
        </div>
        <div class="space-y-2.5 max-h-80 overflow-y-auto pr-1">
            @foreach($userHotels as $h)
            <form method="post" action="{{ route('admin.switch-hotel', $h->id) }}">
                @csrf
                <button type="submit" class="flex w-full items-center justify-between rounded-2xl p-3 text-left transition {{ ($currentHotel->id ?? 0) === $h->id ? 'neu-inset text-[#00214D]' : 'neu-button text-slate-800' }}">
                    <div>
                        <div class="font-bold text-sm">{{ $h->name }}</div>
                        <div class="text-xs text-slate-500">{{ $h->city ?? 'Location not specified' }}</div>
                    </div>
                    @if(($currentHotel->id ?? 0) === $h->id)
                    <span class="neu-pill-inset px-2.5 py-0.5 text-[10px] font-bold text-[#00214D]">Active</span>
                    @endif
                </button>
            </form>
            @endforeach
            <a href="{{ route('property.select') }}" class="block text-center mt-3 pt-3 border-t border-slate-300/60 text-xs font-bold text-[#0073E6] hover:underline">
                Where are you working today? Switch Workspace →
            </a>
        </div>
    </div>
</div>

<!-- Ctrl+K Quick Search Command Modal (Neumorphic) -->
<div x-show="searchOpen" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-start justify-center bg-slate-900/40 pt-20 p-4 backdrop-blur-sm">
    <div @click.away="searchOpen = false" class="w-full max-w-2xl neu-flat-lg rounded-3xl bg-[#e8edf5] overflow-hidden">
        <div class="flex items-center border-b border-slate-300/60 px-4">
            <x-icon name="search" class="w-5 h-5 text-slate-500 ml-2" />
            <input id="admin-quick-search-input" x-model="searchQuery" @input.debounce.250ms="performSearch()" type="text" placeholder="Type to search rooms, orders, requests, menu items..." class="w-full bg-transparent px-4 py-4 text-sm text-slate-900 placeholder-slate-500 focus:outline-none">
            <kbd @click="searchOpen = false" class="cursor-pointer neu-button rounded-xl px-2.5 py-1 text-xs text-slate-600 font-bold">ESC</kbd>
        </div>

        <div class="max-h-96 overflow-y-auto p-4 space-y-2">
            <div x-show="searching" x-cloak style="display: none;" class="p-6 text-center text-xs text-slate-500">Searching operations database...</div>
            
            <template x-if="!searching && searchResults.length === 0 && searchQuery.length >= 2">
                <div class="p-6 text-center text-xs text-slate-500">No matching operations records found.</div>
            </template>

            <template x-for="item in searchResults" :key="item.url + item.title">
                <a :href="item.url" class="flex items-center justify-between rounded-2xl neu-button p-3 hover:scale-[1.01] transition">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="neu-pill-inset px-2 py-0.5 text-[10px] font-mono font-bold text-[#00214D]" x-text="item.category"></span>
                            <span class="font-bold text-sm text-slate-900 truncate" x-text="item.title"></span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5 truncate" x-text="item.subtitle"></p>
                    </div>
                    <span class="text-xs rounded-full px-2.5 py-0.5 font-bold" :class="item.badge === 'available' || item.badge === 'COMPLETED' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'" x-text="item.badge"></span>
                </a>
            </template>
        </div>
    </div>
</div>
@else
<div class="min-h-screen flex items-center justify-center p-4 bg-[#e8edf5]">
    <div class="w-full max-w-md neu-flat-lg rounded-3xl p-6 sm:p-8">
        @if(session('status'))
        <div class="mb-4 rounded-xl neu-inset p-4 text-xs font-bold text-emerald-800">
            {{ session('status') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mb-4 rounded-xl neu-inset p-4 text-xs font-bold text-rose-800">
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
