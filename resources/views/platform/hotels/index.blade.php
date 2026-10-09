@extends('layouts.platform')

@section('content')
<div class="space-y-6">
    <!-- Header (Neumorphic) -->
    <div class="neu-flat-lg rounded-3xl p-6 sm:p-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="neu-pill-inset px-3 py-1 text-xs font-mono font-bold text-[#00214D]">Client Tenant Portfolio</span>
                <span class="text-xs text-slate-500">· Multi-Tenant SaaS Engine</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">Hotel Properties & Onboarding</h1>
            <p class="text-xs sm:text-sm text-slate-600 max-w-2xl">Onboard new hotel properties into the platform, establish software subscription plans, and manage tenant access status.</p>
        </div>

        <div class="flex items-center gap-2.5 text-xs font-mono neu-inset px-4 py-2.5 rounded-2xl">
            <span class="text-slate-600">Total Properties: <strong class="text-[#00214D] font-bold">{{ $totalHotels }}</strong></span>
            <span class="text-slate-300">|</span>
            <span class="text-slate-600">Active: <strong class="text-emerald-700 font-bold">{{ $activeHotels }}</strong></span>
        </div>
    </div>

    <!-- Main Grid: Hotels List & Provisioning Form -->
    <div class="grid gap-8 lg:grid-cols-[1fr_440px]">
        <!-- Left: Tenant Properties List -->
        <div class="space-y-4">
            <div class="neu-flat rounded-3xl p-6 sm:p-7 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-300/60 pb-4">
                    <h2 class="font-extrabold text-slate-900 text-base">Onboarded Client Tenants</h2>
                    <span class="neu-pill-inset px-3 py-1 text-xs text-slate-600 font-mono font-semibold">{{ $hotels->total() }} hotels</span>
                </div>

                <div class="space-y-3.5">
                    @forelse($hotels as $h)
                    <div class="neu-inset rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl neu-button text-[#00214D] font-black text-lg">
                                {{ strtoupper(substr($h->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-slate-900 text-base">{{ $h->name }}</h3>
                                    <span class="rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $h->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-100 text-rose-800 border border-rose-300' }}">
                                        {{ ucfirst($h->status) }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 font-medium">
                                    {{ $h->city ? $h->city . ', ' : '' }}{{ $h->country ?? 'India' }} · Timezone: {{ $h->timezone }}
                                </p>
                                <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-600 mt-2">
                                    <span class="neu-pill px-2.5 py-0.5 text-[#00214D] font-bold">
                                        Plan: {{ $h->currentSubscription?->plan_name ?? 'Professional Cloud' }} (₹{{ number_format((float)($h->currentSubscription?->fee ?? 9999.00), 2) }}/mo)
                                    </span>
                                    <span class="text-slate-400 font-mono text-[10px]">slug: {{ $h->slug }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <!-- Toggle Status Form -->
                            <form method="post" action="{{ route('platform.hotels.toggle-status', $h) }}">
                                @csrf
                                <button type="submit" class="neu-button rounded-xl px-3.5 py-2 text-xs font-bold transition {{ $h->status === 'active' ? 'text-rose-700 hover:text-rose-900' : 'text-emerald-700 hover:text-emerald-900' }}" title="Toggle tenant service availability">
                                    {{ $h->status === 'active' ? 'Suspend Tenant' : 'Activate Tenant' }}
                                </button>
                            </form>

                            <!-- Direct Billing Link -->
                            <a href="{{ route('platform.billing.index') }}" class="neu-button rounded-xl px-3.5 py-2 text-xs font-bold text-slate-800 hover:text-slate-950 transition" title="Generate or view invoices for this hotel">
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

        <!-- Right: Onboard New Property Aside (Neumorphic Card) -->
        <aside id="onboard" class="neu-flat rounded-3xl p-6 sm:p-7 space-y-4 h-fit">
            <div class="flex items-center gap-2">
                <span class="neu-pill-inset px-2.5 py-0.5 text-xs font-mono font-bold text-[#00214D]">Client Provisioning</span>
            </div>
            <h2 class="font-extrabold text-slate-900 text-base">Onboard New Hotel Property</h2>
            <p class="text-xs text-slate-500 leading-relaxed">Deploys an isolated multi-tenant environment, initial SaaS subscription, and administrative onboarding credentials.</p>

            <form method="post" action="{{ route('platform.hotels.store') }}" class="mt-4 space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Hotel Property Name</label>
                    <input class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none" name="name" placeholder="E.g., The Heritage Palace Resort" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Hotel Admin Name</label>
                        <input class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none" name="admin_name" placeholder="E.g., Rajesh Sharma" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Admin Email</label>
                        <input class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none" type="email" name="admin_email" placeholder="rajesh@hotel.com" required>
                    </div>
                </div>

                <!-- SaaS Plan Selection (Neumorphic Options) -->
                <div class="neu-inset rounded-2xl p-4 space-y-2.5">
                    <label class="block text-[11px] font-extrabold text-[#00214D] uppercase tracking-wider">Select SaaS Subscription Tier</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="plan_name" value="Boutique Starter" onchange="document.getElementById('plan_fee').value = 4999" class="sr-only peer">
                            <div class="rounded-xl neu-button p-2.5 text-center peer-checked:neu-inset peer-checked:text-[#00214D] text-slate-600 transition">
                                <div class="text-[11px] font-bold">Starter</div>
                                <div class="text-[10px] font-mono mt-0.5">₹4,999/mo</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="plan_name" value="Professional Cloud" checked onchange="document.getElementById('plan_fee').value = 9999" class="sr-only peer">
                            <div class="rounded-xl neu-button p-2.5 text-center peer-checked:neu-inset peer-checked:text-[#00214D] text-slate-600 transition">
                                <div class="text-[11px] font-bold">Pro Cloud</div>
                                <div class="text-[10px] font-mono mt-0.5">₹9,999/mo</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="plan_name" value="Enterprise Resort" onchange="document.getElementById('plan_fee').value = 19999" class="sr-only peer">
                            <div class="rounded-xl neu-button p-2.5 text-center peer-checked:neu-inset peer-checked:text-[#00214D] text-slate-600 transition">
                                <div class="text-[11px] font-bold">Enterprise</div>
                                <div class="text-[10px] font-mono mt-0.5">₹19,999/mo</div>
                            </div>
                        </label>
                    </div>

                    <div class="pt-1 flex items-center justify-between text-xs">
                        <span class="text-slate-600 font-semibold">Monthly License Fee (₹):</span>
                        <input id="plan_fee" name="plan_fee" type="number" step="0.01" value="9999" class="w-32 rounded-lg neu-input px-2.5 py-1 text-right text-xs font-mono font-bold text-slate-900 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">City</label>
                        <input class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none" name="city" placeholder="Goa" value="Goa">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Country</label>
                        <input class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none" name="country" value="India">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Timezone</label>
                    <input class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 font-mono placeholder-slate-400 focus:outline-none" name="timezone" value="Asia/Kolkata" required>
                </div>

                <div class="neu-pill-inset p-3 text-[11px] text-slate-600 leading-relaxed rounded-xl font-medium">
                    🛡️ Onboarding automatically establishes database isolation, provisions initial billing invoice, and generates activation links.
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-xs text-white neu-btn-primary hover:brightness-110 transition cursor-pointer">
                    Deploy & Onboard Hotel Property
                </button>
            </form>
        </aside>
    </div>
</div>
@endsection
