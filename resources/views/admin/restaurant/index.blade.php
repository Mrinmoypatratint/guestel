@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800/80 pb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-rose-500/10 px-2 py-0.5 text-xs font-mono font-medium text-rose-400 border border-rose-500/20">Culinary Management</span>
                <span class="text-xs text-slate-500">· Menu Engineering & Live Inventory</span>
            </div>
            <h1 class="mt-2 text-2xl lg:text-3xl font-bold tracking-tight text-white">Restaurants & Menu Engineering</h1>
            <p class="mt-1 text-sm text-slate-400">Manage in-room dining menus, categories, dish availability, and tax parameters.</p>
        </div>
    </div>

    <!-- Main Grid: Menus on Left, Management Asides on Right -->
    <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
        <!-- Left: Restaurants & Categorized Menus -->
        <div class="space-y-6">
            @forelse($restaurants as $r)
            <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 lg:p-8 backdrop-blur-xl">
                <!-- Restaurant Banner -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-800/80 pb-6">
                    <div>
                        <span class="rounded-full bg-rose-500/20 text-rose-300 px-3 py-0.5 text-xs font-semibold">Active Outlet</span>
                        <h2 class="mt-2 text-2xl font-bold text-white">{{ $r->name }}</h2>
                        <p class="mt-1 text-xs text-slate-400">{{ $r->description }}</p>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-slate-400">
                        <span class="rounded-xl border border-slate-800 bg-slate-950 px-3 py-1.5 font-mono">Tax: <strong class="text-white">{{ $r->tax_rate }}%</strong></span>
                        <span class="rounded-xl border border-slate-800 bg-slate-950 px-3 py-1.5 font-mono">Currency: <strong class="text-amber-300">{{ $r->currency }}</strong></span>
                    </div>
                </div>

                <!-- Menu Items List -->
                <div class="mt-6 space-y-3">
                    <h3 class="font-bold text-white text-sm">Dishes & Offerings ({{ $r->menuItems->count() }})</h3>
                    <div class="divide-y divide-slate-800/60">
                        @forelse($r->menuItems as $item)
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-white text-sm">{{ $item->name }}</span>
                                    <span class="rounded bg-slate-800 px-1.5 py-0.5 text-[10px] font-mono text-slate-400">~{{ $item->preparation_minutes }} min</span>
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $item->is_available ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                        {{ $item->is_available ? 'Serving' : 'Sold Out' }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-slate-400 leading-relaxed max-w-xl">{{ $item->description }}</p>
                            </div>

                            <div class="flex items-center gap-4 flex-shrink-0">
                                <span class="font-mono text-base font-bold text-white">₹{{ number_format((float)$item->price, 2) }}</span>
                                
                                <form method="post" action="{{ route('admin.restaurant.items.toggle', $item) }}">
                                    @csrf
                                    <button class="rounded-xl border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-300 hover:bg-slate-700 transition">
                                        {{ $item->is_available ? 'Mark Sold Out' : 'Mark Available' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="py-8 text-center text-xs text-slate-500">No dishes created in this restaurant yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
            @empty
            <div class="rounded-3xl border border-dashed border-slate-800 p-12 text-center text-slate-500">
                No restaurant outlets created. Use the form on the right to establish the primary hotel restaurant.
            </div>
            @endforelse
        </div>

        <!-- Right: Management Actions -->
        <aside class="space-y-6">
            <!-- Add Menu Item -->
            @if($restaurants->isNotEmpty())
            <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
                <h3 class="font-bold text-white text-base">Add Menu Item</h3>
                <p class="text-xs text-slate-400 mt-1">Dishes appear immediately on guest mobile menus.</p>

                <form method="post" action="{{ route('admin.restaurant.items.store') }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Select Outlet</label>
                        <select name="restaurant_id" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-white" required>
                            @foreach($restaurants as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Item Name</label>
                        <input name="name" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-white" placeholder="E.g., Truffle Tagliatelle" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-white" placeholder="Fresh black truffle, aged parmesan emulsion..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Price (₹)</label>
                            <input name="price" type="number" step="0.01" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-white font-mono" placeholder="450.00" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Prep Time (Min)</label>
                            <input name="preparation_minutes" type="number" value="20" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-white font-mono" required>
                        </div>
                    </div>

                    <button class="w-full rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 py-2.5 text-xs font-bold text-slate-950 hover:brightness-110 transition shadow">
                        Add to Menu
                    </button>
                </form>
            </div>

            <!-- Add Category -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
                <h3 class="font-bold text-white text-base">New Menu Category</h3>
                <p class="text-xs text-slate-400 mt-1">Organize dishes into sections (e.g. Starters, Desserts).</p>

                <form method="post" action="{{ route('admin.restaurant.categories.store') }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Select Outlet</label>
                        <select name="restaurant_id" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-white" required>
                            @foreach($restaurants as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Category Name</label>
                        <input name="name" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-white" placeholder="E.g., Artisan Cocktails" required>
                    </div>

                    <button class="w-full rounded-xl border border-slate-700 bg-slate-800 py-2.5 text-xs font-semibold text-white hover:bg-slate-700 transition">
                        Create Category
                    </button>
                </form>
            </div>
            @endif

            <!-- Create Outlet -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
                <h3 class="font-bold text-white text-base">New Dining Outlet</h3>
                <p class="text-xs text-slate-400 mt-1">Add a restaurant, rooftop lounge, or cafe.</p>

                <form method="post" action="{{ route('admin.restaurant.store') }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Outlet Name</label>
                        <input name="name" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-white" placeholder="E.g., Sunset Terrace Bar" required>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Tax Rate (%)</label>
                            <input name="tax_rate" type="number" step="0.01" value="8.50" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-white font-mono" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Currency</label>
                            <input name="currency" value="INR" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-white font-mono" required>
                        </div>
                    </div>

                    <button class="w-full rounded-xl border border-slate-700 bg-slate-800 py-2.5 text-xs font-semibold text-white hover:bg-slate-700 transition">
                        Create Outlet
                    </button>
                </form>
            </div>
        </aside>
    </div>
</div>
@endsection
