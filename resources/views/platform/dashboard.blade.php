@extends('layouts.platform')

@section('content')
<div class="space-y-8">
    <!-- Hero / Platform Title -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800/80 pb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-amber-500/10 px-2 py-0.5 text-xs font-mono font-bold text-amber-400 border border-amber-500/20">SaaS Provider Master Dashboard</span>
                <span class="text-xs text-slate-500">· Multi-Tenant Platform Cloud</span>
            </div>
            <h1 class="mt-2 text-2xl lg:text-3xl font-black tracking-tight text-white">Platform Company Administration</h1>
            <p class="mt-1 text-sm text-slate-400">Oversee hotel tenants, monitor SaaS subscription revenue, issue service bills, and broadcast updates to hospitality outlets.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('platform.hotels.index') }}" class="rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2.5 text-xs font-bold text-slate-950 hover:brightness-110 shadow-lg shadow-amber-950/40 transition">
                + Onboard New Hotel
            </a>
            <a href="{{ route('platform.billing.index') }}" class="rounded-xl border border-slate-700 bg-slate-800/80 px-4 py-2.5 text-xs font-semibold text-slate-200 hover:bg-slate-700 transition">
                + Generate Service Bill
            </a>
            <a href="{{ route('platform.communications.index') }}" class="rounded-xl border border-purple-500/30 bg-purple-500/10 px-4 py-2.5 text-xs font-semibold text-purple-300 hover:bg-purple-500/20 transition">
                ✉️ Send Announcement
            </a>
        </div>
    </div>

    <!-- 4 High-Level SaaS Provider KPIs -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <!-- 1. Total Tenants -->
        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Onboarded Properties</span>
                <x-icon name="bed" class="w-4 h-4 text-blue-400" />
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-black text-white">{{ $totalHotels }}</span>
                <span class="text-xs text-emerald-400 font-semibold">{{ $activeHotels }} Active</span>
            </div>
            <p class="mt-1.5 text-xs text-slate-500">Multi-tenant client base</p>
        </div>

        <!-- 2. SaaS Revenue Collected -->
        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Total SaaS Revenue</span>
                <x-icon name="orders" class="w-4 h-4 text-emerald-400" />
            </div>
            <p class="mt-3 text-2xl lg:text-3xl font-black text-emerald-400">₹{{ number_format($totalRevenue, 2) }}</p>
            <p class="mt-1.5 text-xs text-slate-500">Collected software service fees</p>
        </div>

        <!-- 3. Pending Receivables -->
        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Unpaid Receivables</span>
                <x-icon name="alert" class="w-4 h-4 text-amber-400" />
            </div>
            <p class="mt-3 text-2xl lg:text-3xl font-black text-amber-400">₹{{ number_format($pendingRevenue, 2) }}</p>
            <p class="mt-1.5 text-xs text-slate-500">Outstanding hotel invoices</p>
        </div>

        <!-- 4. Active Subscriptions -->
        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Active Subscriptions</span>
                <x-icon name="check-circle" class="w-4 h-4 text-purple-400" />
            </div>
            <p class="mt-3 text-3xl font-black text-white">{{ $activeSubscriptions }}</p>
            <p class="mt-1.5 text-xs text-slate-500">Software licensing contracts</p>
        </div>
    </div>

    <!-- Two Column Section: Recent Invoices & Recent Onboardings -->
    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Left: Recent SaaS Bills / Invoices -->
        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-4 mb-4">
                <div>
                    <h2 class="font-bold text-white text-base">Recent Service Invoices</h2>
                    <p class="text-xs text-slate-400">Billed to hotel properties for platform services</p>
                </div>
                <a href="{{ route('platform.billing.index') }}" class="text-xs font-semibold text-amber-400 hover:text-amber-300">
                    View All Bills →
                </a>
            </div>

            <div class="divide-y divide-slate-800/60">
                @forelse($recentInvoices as $inv)
                <div class="py-3.5 flex items-center justify-between gap-3 text-xs">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-white">{{ $inv->hotel?->name ?? 'Hotel Property' }}</span>
                            <span class="font-mono text-slate-400 text-[11px]">{{ $inv->invoice_number }}</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $inv->title }} · Due: {{ $inv->due_date->format('M d, Y') }}</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-sm text-white">₹{{ number_format((float)$inv->total_amount, 2) }}</span>
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $inv->status === 'paid' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                            {{ strtoupper($inv->status) }}
                        </span>
                        <a href="{{ route('platform.billing.invoices.show', $inv) }}" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-800 hover:text-white transition" title="Print/View Tax Bill">
                            📄
                        </a>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-xs text-slate-500">No invoices issued yet. Click "Generate Service Bill" to bill a property.</div>
                @endforelse
            </div>
        </div>

        <!-- Right: Recent Tenant Onboardings -->
        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-4 mb-4">
                <div>
                    <h2 class="font-bold text-white text-base">Onboarded Properties</h2>
                    <p class="text-xs text-slate-400">Hospitality brands powered by this SaaS platform</p>
                </div>
                <a href="{{ route('platform.hotels.index') }}" class="text-xs font-semibold text-amber-400 hover:text-amber-300">
                    Manage Properties →
                </a>
            </div>

            <div class="divide-y divide-slate-800/60">
                @forelse($recentHotels as $h)
                <div class="py-3.5 flex items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300 font-bold">
                            {{ strtoupper(substr($h->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-white">{{ $h->name }}</span>
                                <span class="rounded-full px-2 py-0.2 text-[10px] font-semibold {{ $h->status === 'active' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300' }}">
                                    {{ ucfirst($h->status) }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $h->city ? $h->city . ', ' : '' }}{{ $h->country ?? 'India' }} · Plan: {{ $h->currentSubscription?->plan_name ?? 'Professional Cloud' }}
                            </p>
                        </div>
                    </div>

                    <div class="text-right font-mono text-xs">
                        <span class="text-slate-400">₹{{ number_format((float)($h->currentSubscription?->fee ?? 9999.00), 2) }}</span>
                        <span class="text-[10px] text-slate-500 block">/month</span>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-xs text-slate-500">No hotels onboarded yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Bottom: Recent Communication Dispatches -->
    <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-4 mb-4">
            <div>
                <h2 class="font-bold text-white text-base">Platform Notices & Email Communications</h2>
                <p class="text-xs text-slate-400">Broadcasts, maintenance alerts, and billing notifications dispatched to clients</p>
            </div>
            <a href="{{ route('platform.communications.index') }}" class="text-xs font-semibold text-amber-400 hover:text-amber-300">
                Dispatch New Notice →
            </a>
        </div>

        <div class="space-y-3">
            @forelse($recentComms as $c)
            <div class="rounded-2xl border border-slate-800/80 bg-slate-950/60 p-4 text-xs">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="rounded bg-purple-500/10 px-2 py-0.5 text-[10px] font-mono font-semibold text-purple-300 border border-purple-500/20">
                            {{ strtoupper(str_replace('_', ' ', $c->category)) }}
                        </span>
                        <span class="font-bold text-white">{{ $c->subject }}</span>
                    </div>
                    <span class="text-[11px] text-slate-500">Target: <strong class="text-slate-300">{{ $c->hotel?->name ?? 'All Client Hotels' }} ({{ $c->target_audience }})</strong> · {{ $c->sent_at?->diffForHumans() }}</span>
                </div>
                <p class="mt-2 text-slate-400 text-xs leading-relaxed line-clamp-2">{{ $c->message }}</p>
            </div>
            @empty
            <div class="py-6 text-center text-xs text-slate-500">No broadcast notices dispatched yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
