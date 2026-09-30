<!doctype html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Where are you working today? · Hotel Guest Platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
        .font-serif-luxury { font-family: 'Playfair Display', Georgia, serif; }
        [x-cloak] { display: none !important; }
        .gold-gradient { background: linear-gradient(135deg, #fbbf24 0%, #d97706 50%, #b45309 100%); }
        .glass-panel { background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 antialiased" x-data="{
    searchQuery: '',
    properties: {{ json_encode($properties) }},
    get filteredProperties() {
        if (!this.searchQuery) return this.properties;
        const q = this.searchQuery.toLowerCase();
        return this.properties.filter(p => p.name.toLowerCase().includes(q) || p.city.toLowerCase().includes(q) || p.role.toLowerCase().includes(q));
    }
}">

<div class="min-h-full flex flex-col justify-between py-12 px-6 sm:px-12 max-w-5xl mx-auto">
    <!-- Top Header -->
    <div class="flex items-center justify-between border-b border-slate-800/80 pb-6">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-2xl gold-gradient text-slate-950 font-black text-lg shadow-lg shadow-amber-950/40">
                H
            </div>
            <div>
                <span class="block text-sm font-bold text-white leading-none">Hotel Guest Platform</span>
                <span class="text-[10px] font-mono text-amber-400">Workspace Selection</span>
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs">
            <div class="text-right hidden sm:block">
                <p class="font-semibold text-white">{{ $user->name }}</p>
                <p class="text-[11px] text-slate-400">{{ $user->email }}</p>
            </div>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900 text-slate-400 hover:text-white hover:bg-slate-800 transition">
                    Sign Out
                </button>
            </form>
        </div>
    </div>

    <!-- Main Workspace Prompt -->
    <div class="py-10 space-y-8">
        <div class="text-center max-w-xl mx-auto space-y-2">
            <span class="text-xs font-mono uppercase tracking-widest text-amber-400 font-bold">Multi-Property Workspace Access</span>
            <h1 class="text-3xl sm:text-4xl font-serif-luxury font-medium text-white">Where are you working today?</h1>
            <p class="text-xs sm:text-sm text-slate-400">Select an authorized hotel property or dining venue to enter your operations hub.</p>
        </div>

        <!-- Live Search Field -->
        <div class="max-w-md mx-auto">
            <div class="relative">
                <svg class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    placeholder="Search hotel or restaurant name, city, role..." 
                    class="w-full rounded-2xl border border-slate-800 bg-slate-900/90 pl-11 pr-4 py-3.5 text-sm text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none shadow-xl transition">
            </div>
        </div>

        <!-- Property Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
            <template x-for="p in filteredProperties" :key="p.type + '-' + p.id">
                <div class="rounded-3xl glass-panel p-6 sm:p-8 space-y-6 hover:border-amber-500/50 transition duration-300 relative group overflow-hidden">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="rounded-full px-2 py-0.5 text-[10px] font-mono font-bold uppercase tracking-wider"
                                      :class="p.type === 'hotel' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30'"
                                      x-text="p.type === 'hotel' ? 'Hotel & Resort' : 'Restaurant & Dining'">
                                </span>
                                <span class="text-xs text-slate-400" x-text="p.city"></span>
                            </div>
                            <h3 class="text-xl font-bold text-white group-hover:text-amber-300 transition" x-text="p.name"></h3>
                            <p class="text-xs text-slate-400">Assigned Role: <span class="font-semibold text-slate-200" x-text="p.role"></span></p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-800 text-amber-400 font-bold text-lg border border-slate-700" x-text="p.name.charAt(0)">
                        </div>
                    </div>

                    <!-- Activity Badges -->
                    <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs">
                        <div class="text-slate-400">
                            <template x-if="p.type === 'hotel'">
                                <span><strong class="text-white" x-text="p.rooms_count || '24'"></strong> Rooms · <span class="text-amber-400 font-medium" x-text="(p.active_requests || '0') + ' Active Requests'"></span></span>
                            </template>
                            <template x-if="p.type === 'restaurant'">
                                <span><strong class="text-white" x-text="p.active_orders || '2'"></strong> Live Kitchen Orders · <span class="text-emerald-400 font-medium">Kitchen Live</span></span>
                            </template>
                        </div>
                    </div>

                    <!-- Enter Workspace Form -->
                    <form method="post" action="{{ route('property.switch') }}">
                        @csrf
                        <input type="hidden" name="property_type" :value="p.type">
                        <input type="hidden" name="property_id" :value="p.id">

                        <button type="submit" class="w-full py-3 px-4 rounded-xl gold-gradient text-xs font-bold text-slate-950 hover:brightness-110 shadow-lg shadow-amber-500/20 transition flex items-center justify-center gap-2 cursor-pointer">
                            <span>Enter Workspace</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </template>
        </div>

        <div x-show="filteredProperties.length === 0" class="text-center py-12 text-sm text-slate-400">
            No properties matching "<span x-text="searchQuery"></span>" found.
        </div>
    </div>

    <!-- Footer Note -->
    <div class="text-center text-[11px] text-slate-400 border-t border-slate-800/80 pt-6">
        Every workspace switch is strictly validated server-side by multi-tenant RBAC policies.
    </div>
</div>

</body>
</html>
