@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Operations Hub Hero Header (Neumorphic) -->
    <div class="neu-flat-lg rounded-3xl p-6 sm:p-8 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            @php
                $hour = (int) date('H');
                $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
            @endphp
            <div class="flex items-center gap-2 mb-1.5">
                <span class="neu-pill-inset px-3 py-1 text-xs font-mono font-bold text-[#00214D]">Operations Command Center</span>
                <span class="text-xs text-slate-500">· Live Property Dispatch</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">{{ $greeting }}, {{ auth()->user()->name }}</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-600">Managing <strong class="text-[#00214D]">{{ $hotel->name }}</strong> · {{ $roomCount }} Total Guestrooms · Real-time operational dispatch active.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.qr-center.index') }}" class="neu-button px-4 py-2.5 rounded-xl text-xs font-bold text-slate-800 hover:text-slate-950 transition inline-flex items-center gap-2 cursor-pointer">
                <x-icon name="qr" class="w-4 h-4 text-emerald-600" />
                <span>QR Print Center</span>
            </a>
            <a href="{{ route('admin.requests.index') }}" class="neu-btn-primary px-4 py-2.5 rounded-xl text-xs font-bold text-white shadow-md hover:brightness-110 transition inline-flex items-center gap-2 cursor-pointer">
                <x-icon name="concierge" class="w-4 h-4 text-white" />
                <span>Manage Dispatch</span>
            </a>
        </div>
    </div>

    <!-- Command Metrics Grid (6 Primary Operations KPIs) -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-6">
        <!-- 1. Revenue -->
        <div class="neu-flat rounded-3xl p-5 hover:scale-[1.01] transition">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                <span>Today's F&B</span>
                <div class="flex h-7 w-7 items-center justify-center rounded-lg neu-button text-amber-600">
                    <x-icon name="orders" class="w-4 h-4" />
                </div>
            </div>
            <p class="mt-3 text-xl lg:text-2xl font-black text-slate-900">₹{{ number_format($todayRevenue, 2) }}</p>
            <p class="mt-1 text-[11px] text-slate-500 font-medium">Live dining receipts</p>
        </div>

        <!-- 2. Occupancy -->
        <div class="neu-flat rounded-3xl p-5 hover:scale-[1.01] transition">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                <span>Occupied</span>
                <div class="flex h-7 w-7 items-center justify-center rounded-lg neu-button text-purple-600">
                    <x-icon name="bed" class="w-4 h-4" />
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-1.5">
                <span class="text-xl lg:text-2xl font-black text-slate-900">{{ $occupiedCount }}</span>
                <span class="text-xs text-slate-500 font-semibold">/ {{ $roomCount }}</span>
            </div>
            <p class="mt-1 text-[11px] text-slate-500 font-medium">{{ $availableCount }} Available</p>
        </div>

        <!-- 3. Active Requests -->
        <div class="neu-flat rounded-3xl p-5 hover:scale-[1.01] transition">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                <span>Active Tasks</span>
                <div class="flex h-7 w-7 items-center justify-center rounded-lg neu-button text-blue-600">
                    <x-icon name="requests" class="w-4 h-4" />
                </div>
            </div>
            <p class="mt-3 text-xl lg:text-2xl font-black text-slate-900">{{ $activeRequests->count() }}</p>
            <p class="mt-1 text-[11px] {{ $pendingRequestsCount > 0 ? 'text-amber-700 font-bold' : 'text-slate-500 font-medium' }}">
                {{ $pendingRequestsCount }} Pending Dispatch
            </p>
        </div>

        <!-- 4. Kitchen Orders -->
        <div class="neu-flat rounded-3xl p-5 hover:scale-[1.01] transition">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                <span>Kitchen Tickets</span>
                <div class="flex h-7 w-7 items-center justify-center rounded-lg neu-button text-rose-600">
                    <x-icon name="restaurant" class="w-4 h-4" />
                </div>
            </div>
            <p class="mt-3 text-xl lg:text-2xl font-black text-slate-900">{{ $activeOrders->count() }}</p>
            <p class="mt-1 text-[11px] {{ $pendingOrdersCount > 0 ? 'text-rose-700 font-bold' : 'text-slate-500 font-medium' }}">
                {{ $pendingOrdersCount }} New Orders
            </p>
        </div>

        <!-- 5. Guest Satisfaction -->
        <div class="neu-flat rounded-3xl p-5 hover:scale-[1.01] transition">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                <span>Satisfaction</span>
                <div class="flex h-7 w-7 items-center justify-center rounded-lg neu-button text-emerald-600">
                    <x-icon name="sparkles" class="w-4 h-4" />
                </div>
            </div>
            <p class="mt-3 text-xl lg:text-2xl font-black text-emerald-700">{{ $satisfactionScore }}%</p>
            <p class="mt-1 text-[11px] text-slate-500 font-medium">Positive stay sentiment</p>
        </div>

        <!-- 6. SLA Health -->
        <div class="neu-flat rounded-3xl p-5 hover:scale-[1.01] transition">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                <span>SLA Breaches</span>
                <div class="flex h-7 w-7 items-center justify-center rounded-lg neu-button text-rose-600">
                    <x-icon name="alert" class="w-4 h-4" />
                </div>
            </div>
            <p class="mt-3 text-xl lg:text-2xl font-black {{ $slaBreachesCount > 0 ? 'text-rose-700' : 'text-slate-900' }}">{{ $slaBreachesCount }}</p>
            <p class="mt-1 text-[11px] {{ $slaBreachesCount > 0 ? 'text-rose-700 font-bold' : 'text-emerald-700 font-semibold' }}">
                {{ $slaBreachesCount > 0 ? 'Attention Required' : '100% On-time resolution' }}
            </p>
        </div>
    </div>

    <!-- Live Operations Real-Time Stream (Two-Column Flow) -->
    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Left: Live Guest Service Requests (Neumorphic Card) -->
        <div class="neu-flat rounded-3xl p-6 sm:p-7 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-300/60 pb-4">
                <div class="flex items-center gap-2.5">
                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                    <h2 class="font-extrabold text-slate-900 text-base">Live Service Requests</h2>
                    <span class="neu-pill-inset px-2.5 py-0.5 text-xs text-slate-600 font-mono font-bold">{{ $activeRequests->count() }} active</span>
                </div>
                <a href="{{ route('admin.requests.index') }}" class="text-xs font-bold text-[#0073E6] hover:underline">View all →</a>
            </div>

            <div class="space-y-3.5 max-h-[520px] overflow-y-auto pr-1">
                @forelse($activeRequests as $req)
                @php
                    $isOverdue = $req->completion_due_at && $req->completion_due_at->isPast() && $req->status !== 'COMPLETED';
                @endphp
                <div class="neu-inset rounded-2xl p-4 transition space-y-2">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-slate-900 text-sm">Room {{ $req->room?->number ?? 'General' }}</span>
                                <span class="text-slate-400">·</span>
                                <span class="font-bold text-slate-800 text-sm">{{ $req->service?->name ?? 'Guest Request' }}</span>
                                @if($req->priority === 'urgent' || $req->priority === 'high')
                                <span class="rounded-full bg-rose-100 text-rose-800 border border-rose-300 px-2 py-0.5 text-[10px] font-bold uppercase">{{ $req->priority }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5 font-medium">
                                Dept: <strong class="text-slate-700">{{ $req->department?->name ?? 'Housekeeping' }}</strong> · 
                                Created {{ $req->created_at->diffForHumans() }}
                            </p>
                            @if($req->note)
                            <p class="mt-2 text-xs italic text-slate-700 rounded-xl bg-white/70 p-2.5 border border-slate-200/60">"{{ $req->note }}"</p>
                            @endif

                            @if($isOverdue)
                            <div class="mt-2 flex items-center gap-1.5 text-xs text-rose-700 font-bold">
                                <x-icon name="alert" class="w-3.5 h-3.5 text-rose-600" />
                                <span>SLA Target Passed · Escalate Immediately</span>
                            </div>
                            @elseif($req->completion_due_at)
                            <p class="mt-1 text-[11px] text-slate-500 font-medium">Target Resolution: {{ $req->completion_due_at->diffForHumans() }}</p>
                            @endif
                        </div>

                        <!-- Status Badge & Immediate Action -->
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $req->status === 'PENDING' ? 'bg-amber-100 text-amber-800 border border-amber-300' : ($req->status === 'IN_PROGRESS' ? 'bg-blue-100 text-blue-800 border border-blue-300' : 'bg-slate-200 text-slate-700') }}">
                                {{ str_replace('_', ' ', $req->status) }}
                            </span>

                            <form method="post" action="{{ route('admin.requests.update', $req) }}" class="flex items-center gap-1">
                                @csrf
                                @method('PATCH')
                                @if($req->status === 'PENDING')
                                <input type="hidden" name="status" value="ACCEPTED">
                                <button class="neu-button rounded-xl px-3 py-1 text-xs font-bold text-amber-900 hover:text-black transition">Accept</button>
                                @elseif($req->status === 'ACCEPTED')
                                <input type="hidden" name="status" value="IN_PROGRESS">
                                <button class="neu-btn-blue rounded-xl px-3 py-1 text-xs font-bold text-white shadow-sm transition">Start</button>
                                @elseif($req->status === 'IN_PROGRESS')
                                <input type="hidden" name="status" value="COMPLETED">
                                <button class="neu-btn-primary rounded-xl px-3 py-1 text-xs font-bold text-white shadow-sm transition">Complete</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center text-slate-500 text-xs font-medium">
                    <x-icon name="check-circle" class="w-8 h-8 mx-auto text-emerald-600 mb-2" />
                    All guest requests handled. Zero active pending tasks.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Kitchen / In-Room Dining Dispatch (Neumorphic Card) -->
        <div class="neu-flat rounded-3xl p-6 sm:p-7 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-300/60 pb-4">
                <div class="flex items-center gap-2.5">
                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <h2 class="font-extrabold text-slate-900 text-base">In-Room Dining Kitchen Orders</h2>
                    <span class="neu-pill-inset px-2.5 py-0.5 text-xs text-slate-600 font-mono font-bold">{{ $activeOrders->count() }} active</span>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-[#0073E6] hover:underline">View all →</a>
            </div>

            <div class="space-y-3.5 max-h-[520px] overflow-y-auto pr-1">
                @forelse($activeOrders as $ord)
                <div class="neu-inset rounded-2xl p-4 transition space-y-2">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-slate-900 text-sm">Room {{ $ord->room?->number ?? 'F&B' }}</span>
                                <span class="text-slate-400">·</span>
                                <span class="font-mono text-xs text-[#0073E6] font-bold">{{ $ord->order_number }}</span>
                                <span class="text-slate-400">·</span>
                                <span class="font-bold text-slate-900 text-sm">₹{{ number_format((float)$ord->total, 2) }}</span>
                            </div>

                            <p class="text-xs text-slate-500 mt-0.5 font-medium">
                                Placed {{ $ord->created_at->diffForHumans() }} · {{ $ord->items->count() }} items ordered
                            </p>

                            <!-- Items Summary Pill -->
                            <div class="mt-2 space-y-1">
                                @foreach($ord->items as $it)
                                <div class="text-xs text-slate-700 flex items-center gap-2 font-medium">
                                    <span class="neu-pill px-1.5 py-0.5 font-bold font-mono text-[10px] text-[#00214D]">{{ $it->quantity }}x</span>
                                    <span>{{ $it->item_name }}</span>
                                </div>
                                @endforeach
                            </div>

                            @if($ord->special_instructions)
                            <p class="mt-2 text-xs italic text-slate-700 rounded-xl bg-white/70 p-2.5 border border-slate-200/60">"{{ $ord->special_instructions }}"</p>
                            @endif
                        </div>

                        <!-- Status & Kitchen Actions -->
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $ord->status === 'PENDING' ? 'bg-amber-100 text-amber-800 border border-amber-300' : ($ord->status === 'PREPARING' ? 'bg-orange-100 text-orange-800 border border-orange-300' : 'bg-emerald-100 text-emerald-800 border border-emerald-300') }}">
                                {{ $ord->status }}
                            </span>

                            <form method="post" action="{{ route('admin.orders.update', $ord) }}" class="flex items-center gap-1">
                                @csrf
                                @method('PATCH')
                                @if($ord->status === 'PENDING')
                                <input type="hidden" name="status" value="ACCEPTED">
                                <button class="neu-button rounded-xl px-3 py-1 text-xs font-bold text-amber-900 hover:text-black transition">Accept</button>
                                @elseif($ord->status === 'ACCEPTED')
                                <input type="hidden" name="status" value="PREPARING">
                                <button class="neu-button rounded-xl px-3 py-1 text-xs font-bold text-orange-800 hover:text-orange-950 transition">Kitchen Prep</button>
                                @elseif($ord->status === 'PREPARING')
                                <input type="hidden" name="status" value="READY">
                                <button class="neu-btn-blue rounded-xl px-3 py-1 text-xs font-bold text-white shadow-sm transition">Ready</button>
                                @elseif($ord->status === 'READY')
                                <input type="hidden" name="status" value="DELIVERED">
                                <button class="neu-btn-primary rounded-xl px-3 py-1 text-xs font-bold text-white shadow-sm transition">Delivered</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center text-slate-500 text-xs font-medium">
                    <x-icon name="check-circle" class="w-8 h-8 mx-auto text-emerald-600 mb-2" />
                    Kitchen line is clear. No active dining tickets.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
