@extends('layouts.platform', ['title' => 'Send Mail to Hotels & Outlets'])

@section('content')
<div class="space-y-8">
    <!-- Header (Neumorphic) -->
    <div class="neu-flat-lg rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="neu-pill-inset px-2.5 py-0.5 text-xs font-mono font-bold text-[#00214D]">Advisories & Partner Dispatch</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">Tenant & Outlet Communications</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-600 max-w-2xl">Dispatch official service advisories, billing notices, maintenance bulletins, and onboarding emails to partner hotels and restaurants.</p>
        </div>
    </div>

    <!-- 2 Column Layout: Compose Mail Form + Dispatch Log -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Compose Communication Form (Neumorphic) -->
        <div class="lg:col-span-5 neu-flat rounded-3xl p-6 sm:p-7 space-y-5">
            <div class="border-b border-slate-300/60 pb-4">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg neu-button text-purple-700">
                        <x-icon name="chat" class="w-4 h-4" />
                    </div>
                    <span>Compose Official Notice</span>
                </h3>
                <p class="text-xs text-slate-500 mt-1">Super Admin direct channel to tenant management & F&B teams</p>
            </div>

            <form method="post" action="{{ route('platform.communications.send') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Target Hotel / Tenant *</label>
                    <select name="hotel_id" class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none">
                        <option value="all">Broadcast to All Partner Hotels</option>
                        @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}">{{ $hotel->name }} ({{ $hotel->city ?? 'India' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Target Audience *</label>
                    <select name="target_audience" required class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none">
                        <option value="all_hotels">All Hotel Staff & Outlet Managers</option>
                        <option value="hotel_admin">Hotel General Managers / Tenant Admins</option>
                        <option value="restaurant_manager">Restaurant & F&B Outlet Managers</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Notice Category *</label>
                    <select name="category" required class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none">
                        <option value="billing_notice">SaaS Billing Notice & Invoices</option>
                        <option value="system_alert">Cloud Platform Maintenance & Upgrades</option>
                        <option value="onboarding">Onboarding Welcome & Setup Guide</option>
                        <option value="general">General Platform Advisory</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Subject Line *</label>
                    <input type="text" name="subject" required placeholder="e.g. Action Required: Service Bill Settlement & System Maintenance" value="Monthly SaaS Service Notice & Performance Report" class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Message Content *</label>
                    <textarea name="message" rows="5" required placeholder="Write the communication notice for the restaurant or hotel team..." class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none">Dear Partner Team,

Please review your monthly SaaS subscription invoice available in your portal. Contact SaaS platform support for any billing or technical queries.

Warm regards,
SaaS Platform Company Management</textarea>
                </div>

                <button type="submit" class="w-full py-3 px-5 rounded-xl font-bold text-xs text-white neu-btn-primary hover:brightness-110 transition shadow-md flex items-center justify-center gap-2 cursor-pointer">
                    <x-icon name="chat" class="w-4 h-4 text-white" />
                    <span>Dispatch Communication Notice</span>
                </button>
            </form>
        </div>

        <!-- Dispatched Communications History (Neumorphic) -->
        <div class="lg:col-span-7 neu-flat rounded-3xl p-6 sm:p-7 space-y-5">
            <div class="border-b border-slate-300/60 pb-4">
                <h3 class="text-base font-extrabold text-slate-900">Dispatched Notices Log</h3>
                <p class="text-xs text-slate-500">Permanent audit log of all communications sent to hotel and restaurant partners</p>
            </div>

            <div class="space-y-4">
                @forelse($communications as $comm)
                    <div class="neu-inset rounded-2xl p-5 space-y-3">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="neu-pill px-2.5 py-0.5 text-[10px] font-mono font-bold text-[#00214D]">
                                        {{ $comm->hotel ? $comm->hotel->name : 'All Hotels (Broadcast)' }}
                                    </span>

                                    @if($comm->category === 'billing_notice')
                                        <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-bold text-amber-800 border border-amber-300">Billing Notice</span>
                                    @elseif($comm->category === 'system_alert')
                                        <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-[10px] font-bold text-rose-800 border border-rose-300">System Alert</span>
                                    @elseif($comm->category === 'onboarding')
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800 border border-emerald-300">Onboarding</span>
                                    @else
                                        <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-[10px] font-bold text-blue-800 border border-blue-300">General Advisory</span>
                                    @endif

                                    <span class="rounded-full bg-purple-100 px-2.5 py-0.5 text-[10px] font-bold text-purple-800 border border-purple-300">
                                        Target: {{ ucwords(str_replace('_', ' ', $comm->target_audience)) }}
                                    </span>
                                </div>
                                <h4 class="mt-2 text-sm font-extrabold text-slate-900">{{ $comm->subject }}</h4>
                            </div>

                            <span class="text-[11px] text-slate-500 font-medium whitespace-nowrap">
                                {{ $comm->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <p class="text-xs text-slate-700 bg-white/60 p-3.5 rounded-xl border border-slate-200/80 leading-relaxed font-sans whitespace-pre-line">{{ $comm->message }}</p>

                        <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1 font-medium">
                            <span>Recipient: <span class="text-slate-800 font-mono">{{ $comm->recipient_email ?? 'Platform Tenants' }}</span></span>
                            <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                                <x-icon name="check" class="w-3.5 h-3.5" />
                                <span>Sent by {{ $comm->sender?->name ?? 'Company Super Admin' }}</span>
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-sm text-slate-500 font-medium">
                        No communications dispatched yet. Use the form on the left to send messages to hotels or restaurants.
                    </div>
                @endforelse
            </div>

            @if($communications->hasPages())
                <div class="pt-2">
                    {{ $communications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
