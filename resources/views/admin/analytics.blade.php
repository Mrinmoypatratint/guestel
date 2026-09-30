@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800/80 pb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-amber-500/10 px-2 py-0.5 text-xs font-mono font-medium text-amber-400 border border-amber-500/20">Operational Intelligence</span>
                <span class="text-xs text-slate-500">· Real-Time KPI Aggregation</span>
            </div>
            <h1 class="mt-2 text-2xl lg:text-3xl font-bold tracking-tight text-white">Hospitality Performance & Analytics</h1>
            <p class="mt-1 text-sm text-slate-400">Track guest engagement, dining revenue, popular menu items, and SLA adherence.</p>
        </div>
    </div>

    <!-- Executive Metrics Grid -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
            <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Delivered F&B Revenue</span>
            <p class="mt-2 text-3xl font-bold text-white">₹{{ number_format($metrics['revenue'], 2) }}</p>
            <p class="mt-1 text-xs text-slate-500">Avg ticket: ₹{{ number_format($metrics['avg_order'], 2) }}</p>
        </div>

        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
            <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">QR Code Engagement</span>
            <p class="mt-2 text-3xl font-bold text-amber-300">{{ $metrics['qr_scans'] }}</p>
            <p class="mt-1 text-xs text-slate-500">Total room scans recorded</p>
        </div>

        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
            <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Dining Orders Completed</span>
            <p class="mt-2 text-3xl font-bold text-emerald-400">{{ $metrics['orders'] }}</p>
            <p class="mt-1 text-xs text-slate-500">Kitchen fulfillment rate</p>
        </div>

        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
            <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">SLA Incidents</span>
            <p class="mt-2 text-3xl font-bold {{ $metrics['sla_breaches'] > 0 ? 'text-rose-400' : 'text-emerald-400' }}">{{ $metrics['sla_breaches'] }}</p>
            <p class="mt-1 text-xs {{ $metrics['sla_breaches'] > 0 ? 'text-rose-400 font-semibold' : 'text-slate-500' }}">Overdue service tickets</p>
        </div>
    </div>

    <!-- Analytics Breakdown: Popular Food -->
    <div class="grid gap-8 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 lg:p-8 backdrop-blur-xl">
            <h2 class="font-bold text-white text-base">Top 10 In-Demand Menu Items</h2>
            <p class="text-xs text-slate-400 mt-1">Most frequently ordered dishes across all guestrooms.</p>

            <div class="mt-6 space-y-4">
                @php
                    $maxQty = $popular_food->max('qty') ?: 1;
                @endphp
                @forelse($popular_food as $idx => $row)
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-800 text-[10px] font-bold font-mono text-amber-300">#{{ $idx + 1 }}</span>
                            <span class="font-semibold text-white">{{ $row->item_name }}</span>
                        </div>
                        <span class="font-bold font-mono text-slate-300">{{ $row->qty }} sold</span>
                    </div>
                    <!-- Progress Bar -->
                    <div class="h-2 w-full rounded-full bg-slate-800 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-amber-500 to-amber-400" style="width: {{ ($row->qty / $maxQty) * 100 }}%"></div>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-xs text-slate-500">No dining orders recorded yet.</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 lg:p-8 backdrop-blur-xl">
            <h2 class="font-bold text-white text-base">Service Delivery & Sentiment Summary</h2>
            <p class="text-xs text-slate-400 mt-1">Overview of service recovery and guest satisfaction.</p>

            <div class="mt-6 space-y-4">
                <div class="flex items-center justify-between rounded-2xl bg-slate-950/60 p-4 border border-slate-800">
                    <div>
                        <span class="text-xs text-slate-400">Total Service Requests Logged</span>
                        <p class="mt-1 text-2xl font-bold text-white">{{ $metrics['requests'] }}</p>
                    </div>
                    <x-icon name="requests" class="w-8 h-8 text-blue-400/50" />
                </div>

                <div class="flex items-center justify-between rounded-2xl bg-slate-950/60 p-4 border border-slate-800">
                    <div>
                        <span class="text-xs text-slate-400">Negative Sentiment / Needs Attention</span>
                        <p class="mt-1 text-2xl font-bold {{ $metrics['needs_attention'] > 0 ? 'text-rose-400' : 'text-emerald-400' }}">{{ $metrics['needs_attention'] }}</p>
                    </div>
                    <x-icon name="alert" class="w-8 h-8 text-rose-400/50" />
                </div>

                <div class="rounded-2xl border border-amber-500/20 bg-amber-500/5 p-4 text-xs text-amber-200/90 leading-relaxed">
                    💡 <strong>Operational Recommendation:</strong> With zero active SLA breaches, ensure kitchen prep times during peak hours (19:00 - 21:00) remain under 25 minutes to preserve 98%+ guest satisfaction.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
