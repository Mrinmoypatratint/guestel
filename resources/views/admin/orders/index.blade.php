@extends('layouts.app', ['title' => 'Kitchen Operations Board'])

@section('content')
<div class="space-y-6" x-data="{
    kitchenMode: false,
    restaurantStatus: 'OPEN',
    prepDelay: 0,
    statusFilter: 'all',
    toggleKitchenMode() {
        this.kitchenMode = !this.kitchenMode;
    }
}" :class="kitchenMode ? 'p-4 bg-black min-h-screen text-white font-sans' : ''">

    <!-- Top Chef Operations Bar -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between border-b border-slate-800/80 pb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-rose-500/10 px-2 py-0.5 text-xs font-mono font-medium text-rose-400 border border-rose-500/20">Kitchen & F&B Command</span>
                <span class="text-xs text-slate-500">· Back-of-House Dispatch</span>
            </div>
            <h1 class="mt-2 text-2xl lg:text-3xl font-black tracking-tight text-white" :class="kitchenMode ? 'text-4xl font-extrabold tracking-normal text-amber-300' : ''">
                Live Kitchen Operations Board
            </h1>
            <p class="mt-1 text-sm text-slate-400" x-show="!kitchenMode">
                Manage incoming dining tickets, food preparation times, dietary allergens, and room deliveries.
            </p>
        </div>

        <!-- Quick Operational Actions & Kitchen Display Mode Toggle -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Restaurant Open/Pause status -->
            <div class="flex items-center rounded-xl border border-slate-800 bg-slate-900/90 p-1 text-xs">
                <button 
                    type="button" 
                    @click="restaurantStatus = 'OPEN'" 
                    :class="restaurantStatus === 'OPEN' ? 'bg-emerald-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white'" 
                    class="px-3 py-1.5 rounded-lg transition cursor-pointer">
                    OPEN
                </button>
                <button 
                    type="button" 
                    @click="restaurantStatus = 'PAUSED'" 
                    :class="restaurantStatus === 'PAUSED' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white'" 
                    class="px-3 py-1.5 rounded-lg transition cursor-pointer">
                    PAUSE ORDERS
                </button>
                <button 
                    type="button" 
                    @click="restaurantStatus = 'CLOSED'" 
                    :class="restaurantStatus === 'CLOSED' ? 'bg-rose-500 text-white font-bold' : 'text-slate-400 hover:text-white'" 
                    class="px-3 py-1.5 rounded-lg transition cursor-pointer">
                    CLOSED
                </button>
            </div>

            <!-- Kitchen Display Mode Toggle Button -->
            <button 
                type="button" 
                @click="toggleKitchenMode()" 
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition border cursor-pointer"
                :class="kitchenMode ? 'bg-amber-500 text-slate-950 border-amber-400 shadow-xl' : 'bg-slate-800 text-amber-400 border-slate-700 hover:bg-slate-700'">
                <x-icon name="dashboard" class="w-4 h-4" />
                <span x-text="kitchenMode ? 'Exit Kitchen Display Mode' : '🖥️ Kitchen Display Mode'"></span>
            </button>
        </div>
    </div>

    <!-- Active Tickets Kanban Board Columns (NEW / ACCEPTED / PREPARING / READY / DELIVERED) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Column 1: PENDING / NEW (Yellow) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b-2 border-amber-500 pb-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping"></span>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-amber-300">NEW ORDERS</h3>
                </div>
                <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-xs font-mono font-bold text-amber-300">
                    {{ $orders->where('status', 'PENDING')->count() }}
                </span>
            </div>

            <div class="space-y-4">
                @forelse($orders->where('status', 'PENDING') as $ord)
                    @include('admin.orders.partials.kitchen-ticket', ['ord' => $ord, 'accent' => 'border-amber-500/50 bg-amber-950/10'])
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-800 p-6 text-center text-xs text-slate-400">
                        No pending tickets
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Column 2: ACCEPTED / QUEUED (Blue) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b-2 border-blue-500 pb-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-blue-300">ACCEPTED & QUEUED</h3>
                </div>
                <span class="rounded-full bg-blue-500/20 px-2 py-0.5 text-xs font-mono font-bold text-blue-300">
                    {{ $orders->where('status', 'ACCEPTED')->count() }}
                </span>
            </div>

            <div class="space-y-4">
                @forelse($orders->where('status', 'ACCEPTED') as $ord)
                    @include('admin.orders.partials.kitchen-ticket', ['ord' => $ord, 'accent' => 'border-blue-500/50 bg-blue-950/10'])
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-800 p-6 text-center text-xs text-slate-400">
                        No queued tickets
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Column 3: PREPARING (Orange) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b-2 border-orange-500 pb-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-400 animate-pulse"></span>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-orange-300">IN THE KITCHEN</h3>
                </div>
                <span class="rounded-full bg-orange-500/20 px-2 py-0.5 text-xs font-mono font-bold text-orange-300">
                    {{ $orders->where('status', 'PREPARING')->count() }}
                </span>
            </div>

            <div class="space-y-4">
                @forelse($orders->where('status', 'PREPARING') as $ord)
                    @include('admin.orders.partials.kitchen-ticket', ['ord' => $ord, 'accent' => 'border-orange-500/50 bg-orange-950/10'])
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-800 p-6 text-center text-xs text-slate-400">
                        No active kitchen tickets
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Column 4: READY FOR DELIVERY (Emerald) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b-2 border-emerald-500 pb-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-300">READY TO SERVE</h3>
                </div>
                <span class="rounded-full bg-emerald-500/20 px-2 py-0.5 text-xs font-mono font-bold text-emerald-300">
                    {{ $orders->where('status', 'READY')->count() }}
                </span>
            </div>

            <div class="space-y-4">
                @forelse($orders->where('status', 'READY') as $ord)
                    @include('admin.orders.partials.kitchen-ticket', ['ord' => $ord, 'accent' => 'border-emerald-500/50 bg-emerald-950/10'])
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-800 p-6 text-center text-xs text-slate-400">
                        No orders waiting for runner
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
