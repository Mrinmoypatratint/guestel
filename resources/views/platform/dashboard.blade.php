@extends('layouts.platform')

@section('content')
<div class="space-y-8">
    <!-- Hero / Platform Title (Neumorphic) -->
    <div class="neu-flat-lg rounded-3xl p-6 sm:p-8 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
        <div class="space-y-1.5">
            <div class="flex items-center gap-2">
                <span class="neu-pill-inset px-3 py-1 text-xs font-mono font-bold text-[#00214D]">
                    SaaS Provider Master Hub
                </span>
                <span class="text-xs text-slate-500">· Multi-Tenant Platform Cloud</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Platform Company Administration
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 max-w-2xl leading-relaxed">
                Oversee hotel tenants, monitor SaaS subscription revenue, issue service bills, and broadcast updates to hospitality outlets.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('platform.hotels.index') }}" class="neu-btn-blue px-4 py-2.5 rounded-xl text-xs font-bold shadow-md hover:scale-[1.02] transition inline-flex items-center gap-1.5 cursor-pointer">
                <span>+ Onboard New Hotel</span>
            </a>
            <a href="{{ route('platform.billing.index') }}" class="neu-button px-4 py-2.5 rounded-xl text-xs font-bold text-slate-800 hover:text-slate-950 transition inline-flex items-center gap-1.5 cursor-pointer">
                <span>+ Generate Service Bill</span>
            </a>
            <a href="{{ route('platform.communications.index') }}" class="neu-button px-4 py-2.5 rounded-xl text-xs font-bold text-slate-800 hover:text-slate-950 transition inline-flex items-center gap-1.5 cursor-pointer">
                <span>✉️ Broadcast Notice</span>
            </a>
        </div>
    </div>

    <!-- 4 High-Level SaaS Provider KPIs (Tactile Neumorphic Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Total Tenants -->
        <div class="neu-flat rounded-3xl p-6 hover:scale-[1.01] transition">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                <span>Onboarded Properties</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-xl neu-button text-[#0073E6]">
                    <x-icon name="bed" class="w-4 h-4" />
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2.5">
                <span class="text-3xl font-black text-slate-900">{{ $totalHotels }}</span>
                <span class="neu-pill-inset px-2 py-0.5 text-xs text-emerald-700 font-bold rounded-lg">{{ $activeHotels }} Active</span>
            </div>
            <p class="mt-2 text-xs text-slate-500 font-medium">Multi-tenant client base</p>
        </div>

        <!-- 2. SaaS Revenue Collected -->
        <div class="neu-flat rounded-3xl p-6 hover:scale-[1.01] transition">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                <span>Total SaaS Revenue</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-xl neu-button text-emerald-600">
                    <x-icon name="orders" class="w-4 h-4" />
                </div>
            </div>
            <p class="mt-4 text-2xl lg:text-3xl font-black text-emerald-700">₹{{ number_format($totalRevenue, 2) }}</p>
            <p class="mt-2 text-xs text-slate-500 font-medium">Collected software service fees</p>
        </div>

        <!-- 3. Pending Receivables -->
        <div class="neu-flat rounded-3xl p-6 hover:scale-[1.01] transition">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                <span>Unpaid Receivables</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-xl neu-button text-amber-600">
                    <x-icon name="alert" class="w-4 h-4" />
                </div>
            </div>
            <p class="mt-4 text-2xl lg:text-3xl font-black text-amber-700">₹{{ number_format($pendingRevenue, 2) }}</p>
            <p class="mt-2 text-xs text-slate-500 font-medium">Outstanding hotel invoices</p>
        </div>

        <!-- 4. Active Subscriptions -->
        <div class="neu-flat rounded-3xl p-6 hover:scale-[1.01] transition">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                <span>Active Subscriptions</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-xl neu-button text-[#00214D]">
                    <x-icon name="check-circle" class="w-4 h-4" />
                </div>
            </div>
            <p class="mt-4 text-3xl font-black text-slate-900">{{ $activeSubscriptions }}</p>
            <p class="mt-2 text-xs text-slate-500 font-medium">Software licensing contracts</p>
        </div>
    </div>

    <!-- Two Column Section: Recent Invoices & Recent Onboardings -->
    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Left: Recent SaaS Bills / Invoices -->
        <div class="neu-flat rounded-3xl p-6 sm:p-7 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-300/60 pb-4">
                <div>
                    <h2 class="font-extrabold text-slate-900 text-base">Recent Service Invoices</h2>
                    <p class="text-xs text-slate-500 font-medium">Billed to hotel properties for platform services</p>
                </div>
                <a href="{{ route('platform.billing.index') }}" class="text-xs font-bold text-[#0073E6] hover:underline">
                    View All Bills →
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentInvoices as $inv)
                <div class="neu-inset rounded-2xl p-4 flex items-center justify-between gap-3 text-xs">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 text-sm">{{ $inv->hotel?->name ?? 'Hotel Property' }}</span>
                            <span class="neu-pill-inset px-2 py-0.5 font-mono text-slate-600 text-[10px]">{{ $inv->invoice_number }}</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-1 font-medium">{{ $inv->title }} · Due: {{ $inv->due_date->format('M d, Y') }}</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="font-mono font-extrabold text-sm text-[#00214D]">₹{{ number_format((float)$inv->total_amount, 2) }}</span>
                        <span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $inv->status === 'paid' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                            {{ strtoupper($inv->status) }}
                        </span>
                        <a href="{{ route('platform.billing.invoices.show', $inv) }}" class="neu-button rounded-xl p-2 text-slate-600 hover:text-slate-900 transition" title="Print/View Tax Bill">
                            📄
                        </a>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-xs text-slate-500 font-medium">No invoices issued yet. Click "Generate Service Bill" to bill a property.</div>
                @endforelse
            </div>
        </div>

        <!-- Right: Recent Tenant Onboardings -->
        <div class="neu-flat rounded-3xl p-6 sm:p-7 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-300/60 pb-4">
                <div>
                    <h2 class="font-extrabold text-slate-900 text-base">Onboarded Properties</h2>
                    <p class="text-xs text-slate-500 font-medium">Hospitality brands powered by this SaaS platform</p>
                </div>
                <a href="{{ route('platform.hotels.index') }}" class="text-xs font-bold text-[#0073E6] hover:underline">
                    Manage Properties →
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentHotels as $h)
                <div class="neu-inset rounded-2xl p-4 flex items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-3.5">
                        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-2xl neu-button text-[#00214D] font-extrabold text-sm">
                            {{ strtoupper(substr($h->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 text-sm">{{ $h->name }}</span>
                                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $h->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-100 text-rose-800' }}">
                                    {{ ucfirst($h->status) }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-600 mt-0.5 font-medium">
                                {{ $h->city ? $h->city . ', ' : '' }}{{ $h->country ?? 'India' }} · Plan: {{ $h->currentSubscription?->plan_name ?? 'Professional Cloud' }}
                            </p>
                        </div>
                    </div>

                    <div class="text-right font-mono text-xs">
                        <span class="text-[#00214D] font-extrabold">₹{{ number_format((float)($h->currentSubscription?->fee ?? 9999.00), 2) }}</span>
                        <span class="text-[10px] text-slate-500 block font-sans">/month</span>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-xs text-slate-500 font-medium">No hotels onboarded yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Bottom: Recent Communication Dispatches -->
    <div class="neu-flat rounded-3xl p-6 sm:p-7 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-300/60 pb-4">
            <div>
                <h2 class="font-extrabold text-slate-900 text-base">Platform Notices & Email Communications</h2>
                <p class="text-xs text-slate-500 font-medium">Broadcasts, maintenance alerts, and billing notifications dispatched to clients</p>
            </div>
            <a href="{{ route('platform.communications.index') }}" class="text-xs font-bold text-[#0073E6] hover:underline">
                Dispatch New Notice →
            </a>
        </div>

        <div class="space-y-3">
            @forelse($recentComms as $c)
            <div class="neu-inset rounded-2xl p-4 text-xs space-y-2">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="neu-pill-inset px-2.5 py-0.5 text-[10px] font-mono font-bold text-purple-800">
                            {{ strtoupper(str_replace('_', ' ', $c->category)) }}
                        </span>
                        <span class="font-bold text-slate-900">{{ $c->subject }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] text-slate-500 font-medium">
                        <span>Target: <strong class="text-slate-800">{{ $c->hotel?->name ?? 'All Hotels' }} ({{ $c->target_audience }})</strong></span>
                        <span>·</span>
                        <span>{{ $c->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <p class="text-slate-600 text-xs leading-relaxed font-sans">{{ Str::limit($c->message, 240) }}</p>
            </div>
            @empty
            <div class="py-8 text-center text-xs text-slate-500 font-medium">No notices broadcasted yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
