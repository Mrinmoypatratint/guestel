@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Operations Hub Hero Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800/80 pb-6">
        <div>
            @php
                $hour = (int) date('H');
                $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
            @endphp
            <div class="flex items-center gap-2">
                <span class="rounded bg-amber-500/10 px-2 py-0.5 text-xs font-mono font-medium text-amber-400 border border-amber-500/20">Operations Command Center</span>
                <span class="text-xs text-slate-500">· Live Property Dispatch</span>
            </div>
            <h1 class="mt-2 text-2xl lg:text-3xl font-bold tracking-tight text-white">{{ $greeting }}, {{ auth()->user()->name }}</h1>
            <p class="mt-1 text-sm text-slate-400">Managing <span class="text-amber-300 font-semibold">{{ $hotel->name }}</span> · {{ $roomCount }} Total Guestrooms · Real-time operational dispatch active.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.qr-center.index') }}" class="flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-800/90 px-4 py-2.5 text-xs font-semibold text-slate-200 hover:bg-slate-700 transition">
                <x-icon name="qr" class="w-4 h-4 text-emerald-400" />
                <span>QR Print Center</span>
            </a>
            <a href="{{ route('admin.requests.index') }}" class="flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2.5 text-xs font-semibold text-slate-950 shadow-lg shadow-amber-950/50 hover:brightness-110 transition">
                <x-icon name="concierge" class="w-4 h-4" />
                <span>Manage Dispatch</span>
            </a>
        </div>
    </div>

    <!-- Command Metrics Grid (6 Primary Operations KPIs) -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-6">
        <!-- 1. Revenue -->
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-xl">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium uppercase tracking-wider">
                <span>Today's F&B Revenue</span>
                <x-icon name="orders" class="w-4 h-4 text-amber-400" />
            </div>
            <p class="mt-2 text-2xl lg:text-3xl font-bold text-white">₹{{ number_format($todayRevenue, 2) }}</p>
            <p class="mt-1 text-xs text-slate-500">Live dining receipts</p>
        </div>

        <!-- 2. Occupancy -->
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-xl">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium uppercase tracking-wider">
                <span>Occupied Rooms</span>
                <x-icon name="bed" class="w-4 h-4 text-purple-400" />
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl lg:text-3xl font-bold text-white">{{ $occupiedCount }}</span>
                <span class="text-xs text-slate-400">/ {{ $roomCount }}</span>
            </div>
            <p class="mt-1 text-xs text-slate-500">{{ $availableCount }} Available · {{ $maintenanceCount }} Maint.</p>
        </div>

        <!-- 3. Active Requests -->
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-xl">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium uppercase tracking-wider">
                <span>Active Requests</span>
                <x-icon name="requests" class="w-4 h-4 text-blue-400" />
            </div>
            <p class="mt-2 text-2xl lg:text-3xl font-bold text-white">{{ $activeRequests->count() }}</p>
            <p class="mt-1 text-xs {{ $pendingRequestsCount > 0 ? 'text-amber-400 font-semibold' : 'text-slate-500' }}">
                {{ $pendingRequestsCount }} Pending Dispatch
            </p>
        </div>

        <!-- 4. Kitchen Orders -->
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-xl">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium uppercase tracking-wider">
                <span>Kitchen Tickets</span>
                <x-icon name="restaurant" class="w-4 h-4 text-rose-400" />
            </div>
            <p class="mt-2 text-2xl lg:text-3xl font-bold text-white">{{ $activeOrders->count() }}</p>
            <p class="mt-1 text-xs {{ $pendingOrdersCount > 0 ? 'text-rose-400 font-semibold' : 'text-slate-500' }}">
                {{ $pendingOrdersCount }} New Orders
            </p>
        </div>

        <!-- 5. Guest Satisfaction -->
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-xl">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium uppercase tracking-wider">
                <span>Satisfaction</span>
                <x-icon name="sparkles" class="w-4 h-4 text-yellow-400" />
            </div>
            <p class="mt-2 text-2xl lg:text-3xl font-bold text-emerald-400">{{ $satisfactionScore }}%</p>
            <p class="mt-1 text-xs text-slate-500">Positive stay sentiment</p>
        </div>

        <!-- 6. SLA Health -->
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-xl">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium uppercase tracking-wider">
                <span>SLA Breaches</span>
                <x-icon name="alert" class="w-4 h-4 text-rose-400" />
            </div>
            <p class="mt-2 text-2xl lg:text-3xl font-bold {{ $slaBreachesCount > 0 ? 'text-rose-400' : 'text-white' }}">{{ $slaBreachesCount }}</p>
            <p class="mt-1 text-xs {{ $slaBreachesCount > 0 ? 'text-rose-400 font-semibold' : 'text-emerald-400' }}">
                {{ $slaBreachesCount > 0 ? 'Attention Required' : '100% On-time resolution' }}
            </p>
        </div>
    </div>

    <!-- Live Operations Real-Time Stream (Two-Column Flow) -->
    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Left: Live Guest Service Requests -->
        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                <div class="flex items-center gap-2">
                    <span class="inline-block h-2 w-2 rounded-full bg-blue-400 animate-pulse"></span>
                    <h2 class="font-bold text-white text-base">Live Service Requests</h2>
                    <span class="rounded-full bg-slate-800 px-2 py-0.5 text-xs text-slate-400 font-mono">{{ $activeRequests->count() }} active</span>
                </div>
                <a href="{{ route('admin.requests.index') }}" class="text-xs text-amber-400 hover:text-amber-300 font-medium">View all →</a>
            </div>

            <div class="mt-4 space-y-3 max-h-[520px] overflow-y-auto pr-1">
                @forelse($activeRequests as $req)
                @php
                    $isOverdue = $req->completion_due_at && $req->completion_due_at->isPast() && $req->status !== 'COMPLETED';
                @endphp
                <div class="rounded-2xl border {{ $isOverdue ? 'border-rose-500/40 bg-rose-950/20' : ($req->status === 'PENDING' ? 'border-amber-500/30 bg-amber-950/20' : 'border-slate-800 bg-slate-800/40') }} p-4 transition">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-white text-sm">Room {{ $req->room?->number ?? 'General' }}</span>
                                <span class="text-slate-400">·</span>
                                <span class="font-semibold text-slate-200 text-sm">{{ $req->service?->name ?? 'Guest Request' }}</span>
                                @if($req->priority === 'urgent' || $req->priority === 'high')
                                <span class="rounded bg-rose-500/20 text-rose-300 border border-rose-500/30 px-1.5 py-0.2 text-[10px] font-bold uppercase">{{ $req->priority }}</span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-slate-400">
                                Dept: <span class="text-slate-300 font-medium">{{ $req->department?->name ?? 'Housekeeping' }}</span> · 
                                Created {{ $req->created_at->diffForHumans() }}
                            </p>
                            @if($req->note)
                            <p class="mt-2 text-xs italic text-amber-200/90 rounded-lg bg-black/20 p-2 border border-white/5">"{{ $req->note }}"</p>
                            @endif

                            @if($isOverdue)
                            <div class="mt-2 flex items-center gap-1.5 text-xs text-rose-400 font-semibold">
                                <x-icon name="alert" class="w-3.5 h-3.5" />
                                <span>SLA Target Passed · Escalate Immediately</span>
                            </div>
                            @elseif($req->completion_due_at)
                            <p class="mt-1 text-[11px] text-slate-500">Target Resolution: {{ $req->completion_due_at->diffForHumans() }}</p>
                            @endif
                        </div>

                        <!-- Status Badge & Immediate Action -->
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $req->status === 'PENDING' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : ($req->status === 'IN_PROGRESS' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : 'bg-slate-700 text-slate-300') }}">
                                {{ str_replace('_', ' ', $req->status) }}
                            </span>

                            <form method="post" action="{{ route('admin.requests.update', $req) }}" class="flex items-center gap-1">
                                @csrf
                                @method('PATCH')
                                @if($req->status === 'PENDING')
                                <input type="hidden" name="status" value="ACCEPTED">
                                <button class="rounded-lg bg-amber-500 px-2.5 py-1 text-xs font-bold text-slate-950 hover:bg-amber-400 transition">Accept</button>
                                @elseif($req->status === 'ACCEPTED')
                                <input type="hidden" name="status" value="IN_PROGRESS">
                                <button class="rounded-lg bg-blue-500 px-2.5 py-1 text-xs font-bold text-white hover:bg-blue-400 transition">Start</button>
                                @elseif($req->status === 'IN_PROGRESS')
                                <input type="hidden" name="status" value="COMPLETED">
                                <button class="rounded-lg bg-emerald-500 px-2.5 py-1 text-xs font-bold text-slate-950 hover:bg-emerald-400 transition">Complete</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center text-slate-500 text-xs">
                    <x-icon name="check-circle" class="w-8 h-8 mx-auto text-emerald-500/60 mb-2" />
                    All guest requests handled. Zero active pending tasks.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Kitchen / In-Room Dining Dispatch -->
        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                <div class="flex items-center gap-2">
                    <span class="inline-block h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <h2 class="font-bold text-white text-base">In-Room Dining Kitchen Orders</h2>
                    <span class="rounded-full bg-slate-800 px-2 py-0.5 text-xs text-slate-400 font-mono">{{ $activeOrders->count() }} active</span>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-amber-400 hover:text-amber-300 font-medium">View all →</a>
            </div>

            <div class="mt-4 space-y-3 max-h-[520px] overflow-y-auto pr-1">
                @forelse($activeOrders as $ord)
                <div class="rounded-2xl border {{ $ord->status === 'PENDING' ? 'border-amber-500/30 bg-amber-950/20' : 'border-slate-800 bg-slate-800/40' }} p-4 transition">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-white text-sm">Room {{ $ord->room?->number ?? 'F&B' }}</span>
                                <span class="text-slate-400">·</span>
                                <span class="font-mono text-xs text-amber-300 font-semibold">{{ $ord->order_number }}</span>
                                <span class="text-slate-400">·</span>
                                <span class="font-bold text-white text-sm">₹{{ number_format((float)$ord->total, 2) }}</span>
                            </div>

                            <p class="mt-1 text-xs text-slate-400">
                                Placed {{ $ord->created_at->diffForHumans() }} · {{ $ord->items->count() }} items ordered
                            </p>

                            <!-- Items Summary Pill -->
                            <div class="mt-2 space-y-1">
                                @foreach($ord->items as $it)
                                <div class="text-xs text-slate-300 flex items-center gap-2">
                                    <span class="rounded bg-slate-800 px-1.5 py-0.5 font-bold font-mono text-[10px] text-amber-300">{{ $it->quantity }}x</span>
                                    <span>{{ $it->item_name }}</span>
                                </div>
                                @endforeach
                            </div>

                            @if($ord->special_instructions)
                            <p class="mt-2 text-xs italic text-amber-200/90 rounded-lg bg-black/20 p-2 border border-white/5">"{{ $ord->special_instructions }}"</p>
                            @endif
                        </div>

                        <!-- Status & Kitchen Actions -->
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $ord->status === 'PENDING' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : ($ord->status === 'PREPARING' ? 'bg-orange-500/20 text-orange-300 border border-orange-500/30' : 'bg-emerald-500/20 text-emerald-300') }}">
                                {{ $ord->status }}
                            </span>

                            <form method="post" action="{{ route('admin.orders.update', $ord) }}" class="flex items-center gap-1">
                                @csrf
                                @method('PATCH')
                                @if($ord->status === 'PENDING')
                                <input type="hidden" name="status" value="ACCEPTED">
                                <button class="rounded-lg bg-amber-500 px-2.5 py-1 text-xs font-bold text-slate-950 hover:bg-amber-400 transition">Accept</button>
                                @elseif($ord->status === 'ACCEPTED')
                                <input type="hidden" name="status" value="PREPARING">
                                <button class="rounded-lg bg-orange-500 px-2.5 py-1 text-xs font-bold text-white hover:bg-orange-400 transition">Kitchen Prep</button>
                                @elseif($ord->status === 'PREPARING')
                                <input type="hidden" name="status" value="READY">
                                <button class="rounded-lg bg-blue-500 px-2.5 py-1 text-xs font-bold text-white hover:bg-blue-400 transition">Ready</button>
                                @elseif($ord->status === 'READY')
                                <input type="hidden" name="status" value="DELIVERED">
                                <button class="rounded-lg bg-emerald-500 px-2.5 py-1 text-xs font-bold text-slate-950 hover:bg-emerald-400 transition">Delivered</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center text-slate-500 text-xs">
                    <x-icon name="check-circle" class="w-8 h-8 mx-auto text-emerald-500/60 mb-2" />
                    Kitchen line is clear. No active dining tickets.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
