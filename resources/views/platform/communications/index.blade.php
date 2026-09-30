@extends('layouts.platform', ['title' => 'Send Mail to Hotels & Outlets'])

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-white sm:text-3xl">Tenant & Outlet Communications</h1>
            <p class="mt-1 text-sm text-slate-400">Dispatch official service advisories, billing notices, maintenance bulletins, and onboarding emails to partner hotels and restaurants.</p>
        </div>
    </div>

    <!-- 2 Column Layout: Compose Mail Form + Dispatch Log -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Compose Communication Form -->
        <div class="lg:col-span-5 rounded-2xl border border-slate-800/80 bg-slate-900/60 p-6 shadow-xl backdrop-blur-xl space-y-6">
            <div class="border-b border-slate-800 pb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <x-icon name="chat" class="w-5 h-5 text-purple-400" />
                    <span>Compose Official Notice</span>
                </h3>
                <p class="text-xs text-slate-400 mt-1">Super Admin direct channel to tenant management & F&B teams</p>
            </div>

            <form method="post" action="{{ route('platform.communications.send') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Target Hotel / Tenant *</label>
                    <select name="hotel_id" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                        <option value="all">Broadcast to All Partner Hotels</option>
                        @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}">{{ $hotel->name }} ({{ $hotel->city ?? 'India' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Target Audience *</label>
                    <select name="target_audience" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                        <option value="all_hotels">All Hotel Staff & Outlet Managers</option>
                        <option value="hotel_admin">Hotel General Managers / Tenant Admins</option>
                        <option value="restaurant_manager">Restaurant & F&B Outlet Managers</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Notice Category *</label>
                    <select name="category" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                        <option value="billing_notice">SaaS Billing Notice & Invoices</option>
                        <option value="system_alert">Cloud Platform Maintenance & Upgrades</option>
                        <option value="onboarding">Onboarding Welcome & Setup Guide</option>
                        <option value="general">General Platform Advisory</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Subject Line *</label>
                    <input type="text" name="subject" required placeholder="e.g. Action Required: Service Bill Settlement & System Maintenance" value="Monthly SaaS Service Notice & Performance Report" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Message Content *</label>
                    <textarea name="message" rows="5" required placeholder="Write the communication notice for the restaurant or hotel team..." class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">Dear Partner Team,

Please review your monthly SaaS subscription invoice available in your portal. Contact SaaS platform support for any billing or technical queries.

Warm regards,
SaaS Platform Company Management</textarea>
                </div>

                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-5 py-3 text-sm font-bold text-slate-950 hover:from-amber-400 hover:to-amber-500 transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2 cursor-pointer">
                    <x-icon name="chat" class="w-4 h-4" />
                    <span>Dispatch Communication Notice</span>
                </button>
            </form>
        </div>

        <!-- Dispatched Communications History -->
        <div class="lg:col-span-7 rounded-2xl border border-slate-800/80 bg-slate-900/60 overflow-hidden shadow-xl backdrop-blur-xl">
            <div class="border-b border-slate-800 px-6 py-4">
                <h3 class="text-base font-bold text-white">Dispatched Notices Log</h3>
                <p class="text-xs text-slate-400">Permanent audit log of all communications sent to hotel and restaurant partners</p>
            </div>

            <div class="divide-y divide-slate-800/60">
                @forelse($communications as $comm)
                    <div class="p-6 hover:bg-slate-800/30 transition space-y-3">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="rounded bg-slate-800 px-2 py-0.5 text-[10px] font-mono text-slate-300">
                                        {{ $comm->hotel ? $comm->hotel->name : 'All Hotels (Broadcast)' }}
                                    </span>

                                    @if($comm->category === 'billing_notice')
                                        <span class="rounded bg-amber-500/10 px-2 py-0.5 text-[10px] font-semibold text-amber-400 border border-amber-500/20">Billing Notice</span>
                                    @elseif($comm->category === 'system_alert')
                                        <span class="rounded bg-rose-500/10 px-2 py-0.5 text-[10px] font-semibold text-rose-400 border border-rose-500/20">System Alert</span>
                                    @elseif($comm->category === 'onboarding')
                                        <span class="rounded bg-emerald-500/10 px-2 py-0.5 text-[10px] font-semibold text-emerald-400 border border-emerald-500/20">Onboarding</span>
                                    @else
                                        <span class="rounded bg-blue-500/10 px-2 py-0.5 text-[10px] font-semibold text-blue-400 border border-blue-500/20">General Advisory</span>
                                    @endif

                                    <span class="rounded bg-purple-500/10 px-2 py-0.5 text-[10px] font-semibold text-purple-400 border border-purple-500/20">
                                        Target: {{ ucwords(str_replace('_', ' ', $comm->target_audience)) }}
                                    </span>
                                </div>
                                <h4 class="mt-2 text-sm font-bold text-white">{{ $comm->subject }}</h4>
                            </div>

                            <span class="text-[11px] text-slate-400 whitespace-nowrap">
                                {{ $comm->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <p class="text-xs text-slate-300 bg-slate-950/60 p-3 rounded-xl border border-slate-800/80 leading-relaxed font-sans whitespace-pre-line">{{ $comm->message }}</p>

                        <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1">
                            <span>Recipient: <span class="text-slate-300 font-mono">{{ $comm->recipient_email ?? 'Platform Tenants' }}</span></span>
                            <span class="inline-flex items-center gap-1 text-emerald-400">
                                <x-icon name="check" class="w-3.5 h-3.5" />
                                <span>Sent by {{ $comm->sender?->name ?? 'Company Super Admin' }}</span>
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-sm text-slate-400">
                        No communications dispatched yet. Use the form on the left to send messages to hotels or restaurants.
                    </div>
                @endforelse
            </div>

            @if($communications->hasPages())
                <div class="border-t border-slate-800 p-4">
                    {{ $communications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
