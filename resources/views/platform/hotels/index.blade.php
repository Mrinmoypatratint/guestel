@extends('layouts.platform')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800/80 pb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-amber-500/10 px-2 py-0.5 text-xs font-mono font-bold text-amber-400 border border-amber-500/20">Client Tenant Portfolio</span>
                <span class="text-xs text-slate-500">· Multi-Tenant SaaS Engine</span>
            </div>
            <h1 class="mt-2 text-2xl lg:text-3xl font-black tracking-tight text-white">Hotel Properties & Onboarding</h1>
            <p class="mt-1 text-sm text-slate-400">Onboard new hotel properties into the platform, establish software subscription plans, and manage tenant access status.</p>
        </div>

        <div class="flex items-center gap-2 text-xs font-mono text-slate-400 bg-slate-900 border border-slate-800 px-3.5 py-2 rounded-xl">
            <span>Total Properties: <strong class="text-white">{{ $totalHotels }}</strong></span>
            <span>·</span>
            <span>Active: <strong class="text-emerald-400">{{ $activeHotels }}</strong></span>
        </div>
    </div>

    <!-- Main Grid: Hotels List & Provisioning Form -->
    <div class="grid gap-8 lg:grid-cols-[1fr_440px]">
        <!-- Left: Tenant Properties List -->
        <div class="space-y-4">
            <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-4 mb-4">
                    <h2 class="font-bold text-white text-base">Onboarded Client Tenants</h2>
                    <span class="text-xs text-slate-400 font-mono">{{ $hotels->total() }} hotels</span>
                </div>

                <div class="divide-y divide-slate-800/60">
                    @forelse($hotels as $h)
                    <div class="py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-300 font-black text-lg">
                                {{ strtoupper(substr($h->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-white text-base">{{ $h->name }}</h3>
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $h->status === 'active' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                                        {{ ucfirst($h->status) }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 mt-1">
                                    {{ $h->city ? $h->city . ', ' : '' }}{{ $h->country ?? 'India' }} · Timezone: {{ $h->timezone }}
                                </p>
                                <div class="flex flex-wrap items-center gap-3 text-[11px] text-slate-400 mt-2">
                                    <span class="rounded bg-slate-800 px-2 py-0.5 text-amber-300 font-semibold">
                                        Plan: {{ $h->currentSubscription?->plan_name ?? 'Professional Cloud' }} (₹{{ number_format((float)($h->currentSubscription?->fee ?? 9999.00), 2) }}/mo)
                                    </span>
                                    <span>Slug: <code class="font-mono text-slate-300">{{ $h->slug }}</code></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <!-- Toggle Status Form -->
                            <form method="post" action="{{ route('platform.hotels.toggle-status', $h) }}">
                                @csrf
                                <button type="submit" class="rounded-xl border {{ $h->status === 'active' ? 'border-rose-500/30 bg-rose-500/10 text-rose-300 hover:bg-rose-500/20' : 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20' }} px-3 py-2 text-xs font-semibold transition" title="Toggle tenant service availability">
                                    {{ $h->status === 'active' ? 'Suspend Tenant' : 'Activate Tenant' }}
                                </button>
                            </form>

                            <!-- Direct Billing Link -->
                            <a href="{{ route('platform.billing.index') }}" class="rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-700 transition" title="Generate or view invoices for this hotel">
                                + Issue Bill
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-xs text-slate-500">No hotels onboarded yet. Use the onboarding form to register your first client property.</div>
                    @endforelse
                </div>
            </div>

            <div>{{ $hotels->links() }}</div>
        </div>

        <!-- Right: Onboard New Property Aside -->
        <aside id="onboard" class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 lg:p-8 backdrop-blur-xl h-fit">
            <div class="flex items-center gap-2">
                <span class="rounded bg-amber-500/10 px-2 py-0.5 text-xs font-mono font-bold text-amber-400 border border-amber-500/20">Client Provisioning</span>
            </div>
            <h2 class="mt-2 font-bold text-white text-base">Onboard New Hotel Property</h2>
            <p class="text-xs text-slate-400 mt-1">Deploys an isolated multi-tenant environment, initial SaaS subscription, and administrative onboarding credentials.</p>

            <form method="post" action="{{ route('platform.hotels.store') }}" class="mt-5 space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Hotel Property Name</label>
                    <input class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none" name="name" placeholder="E.g., The Heritage Palace Resort" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Hotel Admin Name</label>
                        <input class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none" name="admin_name" placeholder="E.g., Rajesh Sharma" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Admin Email</label>
                        <input class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none" type="email" name="admin_email" placeholder="rajesh@hotel.com" required>
                    </div>
                </div>

                <!-- SaaS Plan Selection -->
                <div class="rounded-2xl border border-slate-800 bg-slate-950/80 p-3.5 space-y-2">
                    <label class="block text-xs font-bold text-amber-400 uppercase tracking-wider">Select SaaS Subscription Tier</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="plan_name" value="Boutique Starter" onchange="document.getElementById('plan_fee').value = 4999" class="sr-only peer">
                            <div class="rounded-xl border border-slate-800 p-2.5 text-center peer-checked:border-amber-500 peer-checked:bg-amber-500/10 peer-checked:text-amber-300 text-slate-400 transition">
                                <div class="text-[11px] font-bold">Starter</div>
                                <div class="text-[10px] font-mono mt-0.5">₹4,999/mo</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="plan_name" value="Professional Cloud" checked onchange="document.getElementById('plan_fee').value = 9999" class="sr-only peer">
                            <div class="rounded-xl border border-slate-800 p-2.5 text-center peer-checked:border-amber-500 peer-checked:bg-amber-500/10 peer-checked:text-amber-300 text-slate-400 transition">
                                <div class="text-[11px] font-bold">Pro Cloud</div>
                                <div class="text-[10px] font-mono mt-0.5">₹9,999/mo</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="plan_name" value="Enterprise Resort" onchange="document.getElementById('plan_fee').value = 19999" class="sr-only peer">
                            <div class="rounded-xl border border-slate-800 p-2.5 text-center peer-checked:border-amber-500 peer-checked:bg-amber-500/10 peer-checked:text-amber-300 text-slate-400 transition">
                                <div class="text-[11px] font-bold">Enterprise</div>
                                <div class="text-[10px] font-mono mt-0.5">₹19,999/mo</div>
                            </div>
                        </label>
                    </div>

                    <div class="pt-1 flex items-center justify-between text-xs">
                        <span class="text-slate-400">Monthly License Fee (₹):</span>
                        <input id="plan_fee" name="plan_fee" type="number" step="0.01" value="9999" class="w-32 rounded-lg border border-slate-800 bg-slate-900 px-2.5 py-1 text-right text-xs font-mono font-bold text-white focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">City</label>
                        <input class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none" name="city" placeholder="Goa" value="Goa">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Country</label>
                        <input class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none" name="country" value="India">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Timezone</label>
                    <input class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3.5 py-2.5 text-xs text-white font-mono placeholder-slate-500 focus:border-amber-500 focus:outline-none" name="timezone" value="Asia/Kolkata" required>
                </div>

                <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-3 text-[11px] text-amber-200/90 leading-relaxed">
                    🛡️ Onboarding automatically establishes database isolation, provisions initial billing invoice, and generates activation links.
                </div>

                <button class="w-full rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 py-3 text-xs font-bold text-slate-950 hover:brightness-110 transition shadow-lg shadow-amber-950/40">
                    Deploy & Onboard Hotel Property
                </button>
            </form>
        </aside>
    </div>
</div>
@endsection
