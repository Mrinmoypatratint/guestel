<!doctype html>
<html lang="en" class="h-full bg-[#0a0f1d]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $hotel->name }} · Guest Concierge</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/guestel-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="{{ $hotel->branding?->primary_color ?? '#0f172a' }}">

    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
        .luxury-heading { font-family: 'Cinzel', serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-full bg-[#f8f7f4] text-slate-900 antialiased selection:bg-amber-200" x-data="guestPortal()">

<!-- Hero Section with Luxury Backdrop -->
<section class="relative overflow-hidden bg-slate-950 px-6 pt-12 pb-16 text-white shadow-2xl">
    @if($coverImage)
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('storage/' . $coverImage) }}" alt="{{ $hotel->name }}" class="h-full w-full object-cover object-center opacity-30 brightness-75 scale-105 transition duration-1000">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-slate-950/40"></div>
    </div>
    @endif

    <!-- Ambient Gold Glow -->
    <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full opacity-25 blur-3xl z-0" style="background: radial-gradient(circle, {{ $hotel->branding?->accent_color ?? '#C8A96A' }}, transparent 70%);"></div>
    <div class="absolute -bottom-24 -left-24 h-80 w-80 rounded-full opacity-15 blur-3xl bg-blue-600 z-0"></div>

    <div class="relative mx-auto max-w-lg z-10">
        <!-- Top Status Bar: Monogram & Room Badge -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 via-amber-600 to-amber-700 shadow-md text-slate-950 font-bold text-lg">
                    {{ strtoupper(substr($hotel->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-amber-300/90">Hospitality Concierge</p>
                    <p class="text-xs font-medium text-slate-300">{{ $hotel->city ?? 'Private Resort' }}</p>
                </div>
            </div>

            @if($room)
            <div class="rounded-full border border-amber-400/40 bg-amber-400/10 px-3.5 py-1 text-xs font-semibold text-amber-200 backdrop-blur-md">
                Room {{ $room->number }}
            </div>
            @endif
        </div>

        <!-- Headline & Welcome -->
        <div class="mt-8">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Welcome to</p>
            <h1 class="luxury-heading mt-1 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $hotel->name }}</h1>
            
            @if($room)
            <p class="mt-2 text-sm text-amber-200/90 font-medium">
                {{ $room->name ? $room->name . ' · ' : '' }}{{ $room->roomType?->name ?? 'Standard Room' }}
            </p>
            @endif

            <p class="mt-4 text-xs leading-relaxed text-slate-300/80 max-w-sm">
                {{ $hotel->branding?->welcome_message ?? 'Everything you need for an unforgettable stay. Browse in-room dining, request amenities, or message reception directly.' }}
            </p>
        </div>
    </div>
</section>

<!-- Main Concierge Container -->
<main class="mx-auto max-w-lg px-5 py-6 space-y-6">

    <!-- Flash Status Notification -->
    @if(session('status'))
    <div class="rounded-2xl border border-emerald-500/30 bg-emerald-50 p-4 text-xs font-semibold text-emerald-900 shadow-sm flex items-center gap-3">
        <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0" />
        <span>{{ session('status') }}</span>
    </div>
    @endif

    <!-- Live Order / Request Tracking Pill (Real-Time Service Status) -->
    @if($activeGuestRequests->isNotEmpty() || $activeGuestOrders->isNotEmpty())
    <section class="rounded-3xl border border-amber-500/30 bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent p-5 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
            <span class="inline-block h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
            <h2 class="text-xs font-bold uppercase tracking-wider text-amber-900">Your Active Requests & Orders</h2>
        </div>

        <div class="space-y-2">
            @foreach($activeGuestRequests as $req)
            <div class="flex items-center justify-between rounded-xl bg-white p-3 shadow-xs border border-black/5 text-xs">
                <div>
                    <span class="font-bold text-slate-900">{{ $req->service?->name ?? 'Service Request' }}</span>
                    <p class="text-[11px] text-slate-500">Dept: {{ $req->department?->name ?? 'Hotel Team' }} · {{ $req->created_at->diffForHumans() }}</p>
                </div>
                <span class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $req->status === 'COMPLETED' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ str_replace('_', ' ', $req->status) }}
                </span>
            </div>
            @endforeach

            @foreach($activeGuestOrders as $ord)
            <div class="flex items-center justify-between rounded-xl bg-white p-3 shadow-xs border border-black/5 text-xs">
                <div>
                    <span class="font-bold text-slate-900">Dining Ticket {{ $ord->order_number }}</span>
                    <p class="text-[11px] text-slate-500">₹{{ number_format((float)$ord->total, 2) }} · {{ $ord->items->count() }} items · {{ $ord->created_at->diffForHumans() }}</p>
                </div>
                <span class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $ord->status === 'DELIVERED' ? 'bg-emerald-100 text-emerald-800' : 'bg-orange-100 text-orange-800' }}">
                    {{ $ord->status }}
                </span>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- High Priority Announcements -->
    @if($announcements->isNotEmpty())
    <section class="space-y-2">
        @foreach($announcements as $ann)
        <div class="rounded-2xl border border-amber-200/80 bg-gradient-to-r from-amber-50 to-orange-50/40 p-4 text-xs">
            <div class="flex items-center gap-2 font-bold text-amber-950">
                <x-icon name="sparkles" class="w-4 h-4 text-amber-600" />
                <span>{{ $ann->title }}</span>
            </div>
            <p class="mt-1 text-slate-700 leading-relaxed">{{ $ann->body }}</p>
        </div>
        @endforeach
    </section>
    @endif

    <!-- Quick Concierge Action Grid -->
    <div class="grid grid-cols-2 gap-3">
        <!-- Room Dining CTA -->
        <a href="{{ route('guest.restaurant', $g->public_id) }}" class="group relative col-span-2 overflow-hidden rounded-3xl bg-slate-950 p-6 text-white shadow-xl transition hover:brightness-110">
            <div class="absolute -right-6 -bottom-6 h-32 w-32 rounded-full opacity-20 blur-xl bg-amber-400"></div>
            <div class="flex items-center justify-between">
                <div>
                    <span class="rounded-full bg-amber-400/20 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-amber-300">Culinary Experience</span>
                    <h3 class="mt-2 text-xl font-bold tracking-tight text-white">In-Room Dining & Bar</h3>
                    <p class="mt-1 text-xs text-slate-300/80">Browse handcrafted menus & order directly to your door.</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 text-amber-300 transition group-hover:scale-110">
                    <x-icon name="restaurant" class="w-6 h-6" />
                </div>
            </div>
        </a>

        <!-- Wi-Fi Quick Card -->
        <div @click="copyWifi()" class="cursor-pointer rounded-3xl border border-black/5 bg-white p-5 shadow-sm transition hover:border-amber-400/40">
            <div class="flex items-center justify-between text-amber-700">
                <x-icon name="wifi" class="w-5 h-5 text-amber-600" />
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400" x-text="wifiCopied ? 'Copied!' : 'Tap to copy'">Tap to copy</span>
            </div>
            <h4 class="mt-3 font-bold text-sm text-slate-900">Resort Wi-Fi</h4>
            <p class="text-[11px] text-slate-500 font-mono mt-0.5">StayComfortable</p>
        </div>

        <!-- Reception Chat CTA -->
        <div @click="activeTab = 'chat'" class="cursor-pointer rounded-3xl border border-black/5 bg-white p-5 shadow-sm transition hover:border-amber-400/40">
            <div class="flex items-center justify-between text-blue-600">
                <x-icon name="chat" class="w-5 h-5" />
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Active</span>
            </div>
            <h4 class="mt-3 font-bold text-sm text-slate-900">Front Desk Chat</h4>
            <p class="text-[11px] text-slate-500 mt-0.5">Direct messaging</p>
        </div>
    </div>

    <!-- Interactive Navigation Tabs -->
    <div class="flex rounded-2xl bg-slate-200/70 p-1 text-xs font-semibold text-slate-600">
        <button @click="activeTab = 'services'" :class="activeTab === 'services' ? 'bg-white text-slate-950 shadow-sm' : 'hover:text-slate-900'" class="flex-1 rounded-xl py-2.5 transition">Services</button>
        <button @click="activeTab = 'compendium'" :class="activeTab === 'compendium' ? 'bg-white text-slate-950 shadow-sm' : 'hover:text-slate-900'" class="flex-1 rounded-xl py-2.5 transition">Compendium</button>
        <button @click="activeTab = 'chat'" :class="activeTab === 'chat' ? 'bg-white text-slate-950 shadow-sm' : 'hover:text-slate-900'" class="flex-1 rounded-xl py-2.5 transition">Reception</button>
    </div>

    <!-- TAB 1: Smart Service Requests -->
    <div x-show="activeTab === 'services'" class="space-y-4">
        <div class="rounded-3xl border border-black/5 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Request Guest Services</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Dispatched directly to the appropriate department.</p>
                </div>
                <div class="h-9 w-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-700">
                    <x-icon name="concierge" class="w-5 h-5" />
                </div>
            </div>

            <form method="post" action="{{ route('guest.service', $g->public_id) }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Select Service</label>
                    <select name="service_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-medium text-slate-900 focus:border-amber-500 focus:bg-white focus:outline-none transition" required>
                        <option value="">Choose an amenity or request...</option>
                        @foreach($services as $svc)
                        <option value="{{ $svc->id }}">
                            {{ $svc->name }} · {{ $svc->department?->name ?? 'Housekeeping' }} (Typ. {{ $svc->target_response_minutes ?? 15 }}m)
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Priority</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex cursor-pointer items-center justify-center rounded-xl border border-slate-200 p-2 text-center text-xs font-medium has-[:checked]:border-slate-950 has-[:checked]:bg-slate-950 has-[:checked]:text-white transition">
                            <input type="radio" name="priority" value="normal" checked class="sr-only">
                            <span>Standard</span>
                        </label>
                        <label class="flex cursor-pointer items-center justify-center rounded-xl border border-slate-200 p-2 text-center text-xs font-medium has-[:checked]:border-amber-500 has-[:checked]:bg-amber-500 has-[:checked]:text-slate-950 transition">
                            <input type="radio" name="priority" value="high" class="sr-only">
                            <span>High</span>
                        </label>
                        <label class="flex cursor-pointer items-center justify-center rounded-xl border border-slate-200 p-2 text-center text-xs font-medium has-[:checked]:border-rose-600 has-[:checked]:bg-rose-600 has-[:checked]:text-white transition">
                            <input type="radio" name="priority" value="urgent" class="sr-only">
                            <span>Urgent</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Special Instructions (Optional)</label>
                    <textarea name="note" rows="2" placeholder="E.g., Please leave extra bath sheets by the luggage rack..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-900 focus:border-amber-500 focus:bg-white focus:outline-none transition"></textarea>
                </div>

                <button class="w-full rounded-2xl bg-slate-950 py-3.5 text-xs font-bold text-white shadow-lg shadow-black/10 hover:bg-slate-900 transition">
                    Send Service Request
                </button>
            </form>
        </div>
    </div>

    <!-- TAB 2: Hotel Compendium & Experiences -->
    <div x-show="activeTab === 'compendium'" x-cloak class="space-y-4">
        <!-- Facilities & Amenities -->
        @if($facilities->isNotEmpty())
        <div class="rounded-3xl border border-black/5 bg-white p-5 shadow-sm">
            <h3 class="font-bold text-slate-900 text-sm">Resort Facilities & Hours</h3>
            <div class="mt-3 divide-y divide-slate-100">
                @foreach($facilities as $fac)
                <div class="py-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-900">{{ $fac->name }}</span>
                        <span class="text-[11px] font-mono text-amber-700 font-medium">{{ $fac->hours }}</span>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500">{{ $fac->description }}</p>
                    @if($fac->location)
                    <p class="text-[10px] text-slate-400 mt-0.5">📍 {{ $fac->location }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Resort Spaces & Ambiance Gallery -->
        @if($gallery->isNotEmpty())
        <div class="rounded-3xl border border-black/5 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between pb-3">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Resort Spaces & Grounds</h3>
                    <p class="text-[11px] text-slate-500">Discover our private grounds, wellness spa, and dining terraces.</p>
                </div>
                <span class="rounded-full bg-amber-500/10 px-2.5 py-1 text-[10px] font-bold text-amber-600 border border-amber-500/20">5-Star Luxury</span>
            </div>
            <div class="mt-2 grid grid-cols-2 gap-2.5 sm:grid-cols-3">
                @foreach($gallery as $photo)
                <div class="group relative overflow-hidden rounded-2xl bg-slate-100 aspect-4/3 shadow-xs">
                    <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->alt_text }}" class="h-full w-full object-cover object-center transition duration-500 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent"></div>
                    <span class="absolute bottom-2 left-2 right-2 text-[10px] font-semibold text-white truncate drop-shadow-sm">{{ $photo->alt_text }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Hotel Policies & Guidelines -->
        @if($policies->isNotEmpty())
        <div class="rounded-3xl border border-black/5 bg-white p-5 shadow-sm">
            <h3 class="font-bold text-slate-900 text-sm">Stay Guidelines & Policies</h3>
            <div class="mt-3 space-y-2">
                @foreach($policies as $pol)
                <div class="rounded-2xl bg-slate-50 p-3.5 text-xs">
                    <h4 class="font-bold text-slate-900">{{ $pol->title }}</h4>
                    <p class="mt-1 whitespace-pre-line text-[11px] text-slate-600 leading-relaxed">{{ $pol->content }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Exclusive Offers -->
        @if($offers->isNotEmpty())
        <div class="rounded-3xl border border-black/5 bg-white p-5 shadow-sm">
            <h3 class="font-bold text-slate-900 text-sm">Curated Experiences & Packages</h3>
            <div class="mt-3 space-y-2">
                @foreach($offers as $off)
                <div class="flex items-center justify-between rounded-2xl border border-amber-200/60 bg-amber-50/40 p-3.5 text-xs">
                    <div>
                        <span class="font-bold text-amber-950">{{ $off->title }}</span>
                        <p class="text-[11px] text-slate-600 mt-0.5">{{ $off->description }}</p>
                    </div>
                    @if($off->price)
                    <span class="font-bold text-amber-900 text-sm whitespace-nowrap ml-3">₹{{ number_format((float)$off->price, 2) }}</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Local Sightseeing & Dining -->
        @if($recommendations->isNotEmpty())
        <div class="rounded-3xl border border-black/5 bg-white p-5 shadow-sm">
            <h3 class="font-bold text-slate-900 text-sm">Local Recommendations</h3>
            <div class="mt-3 divide-y divide-slate-100">
                @foreach($recommendations as $rec)
                <div class="py-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[9px] font-bold uppercase text-slate-600">{{ $rec->category }}</span>
                        <span class="font-semibold text-slate-900">{{ $rec->name }}</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">{{ $rec->description }}</p>
                    @if($rec->address)
                    <p class="text-[10px] text-slate-400 mt-0.5">📍 {{ $rec->address }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- TAB 3: Front Desk Chat -->
    <div x-show="activeTab === 'chat'" x-cloak class="space-y-4">
        <div class="rounded-3xl border border-black/5 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Front Desk Concierge Chat</h3>
                    <p class="text-[11px] text-slate-500">Instant direct assistance from the on-duty reception team.</p>
                </div>
                <span class="flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Online
                </span>
            </div>

            <!-- Messages Thread -->
            <div class="my-4 max-h-72 space-y-2.5 overflow-y-auto rounded-2xl bg-slate-50 p-4">
                @if($conversation && $conversation->messages->isNotEmpty())
                    @foreach($conversation->messages as $msg)
                    <div class="flex flex-col {{ $msg->sender_type === 'guest' ? 'items-end' : 'items-start' }}">
                        <div class="max-w-[85%] rounded-2xl px-3.5 py-2.5 text-xs shadow-xs {{ $msg->sender_type === 'guest' ? 'bg-slate-950 text-white rounded-br-xs' : 'bg-white text-slate-900 border border-slate-200 rounded-bl-xs' }}">
                            {{ $msg->body }}
                        </div>
                        <span class="mt-1 text-[9px] text-slate-400">{{ $msg->created_at->format('H:i') }}</span>
                    </div>
                    @endforeach
                @else
                    <div class="py-8 text-center text-xs text-slate-400">
                        No previous messages. Type below to reach reception anytime.
                    </div>
                @endif
            </div>

            <!-- Send Input -->
            <form method="post" action="{{ route('guest.message', $g->public_id) }}" class="flex gap-2">
                @csrf
                <input type="text" name="body" placeholder="Ask reception a question..." class="flex-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-900 focus:border-amber-500 focus:bg-white focus:outline-none transition" required maxlength="2000">
                <button class="rounded-2xl bg-slate-950 px-5 py-3 text-xs font-bold text-white shadow-sm hover:bg-slate-800 transition">
                    Send
                </button>
            </form>
        </div>
    </div>

    <!-- Guest Feedback Widget (3-Tap Service Sentiment) -->
    <section class="rounded-3xl border border-black/5 bg-white p-5 shadow-sm">
        <h3 class="font-bold text-slate-900 text-sm">How is your stay today?</h3>
        <p class="text-[11px] text-slate-500 mt-0.5">Your immediate feedback helps our team ensure a flawless experience.</p>

        <form method="post" action="{{ route('guest.feedback', $g->public_id) }}" class="mt-4 space-y-3">
            @csrf
            <div class="grid grid-cols-3 gap-2">
                <label class="cursor-pointer rounded-2xl border border-slate-200 p-3 text-center transition hover:border-slate-300 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50">
                    <input type="radio" name="rating" value="great" required class="sr-only">
                    <span class="block text-lg">✨</span>
                    <span class="mt-1 block text-xs font-bold text-slate-800">Delightful</span>
                </label>
                <label class="cursor-pointer rounded-2xl border border-slate-200 p-3 text-center transition hover:border-slate-300 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50">
                    <input type="radio" name="rating" value="okay" required class="sr-only">
                    <span class="block text-lg">👌</span>
                    <span class="mt-1 block text-xs font-bold text-slate-800">Satisfactory</span>
                </label>
                <label class="cursor-pointer rounded-2xl border border-slate-200 p-3 text-center transition hover:border-slate-300 has-[:checked]:border-rose-600 has-[:checked]:bg-rose-50">
                    <input type="radio" name="rating" value="needs_attention" required class="sr-only">
                    <span class="block text-lg">🛎️</span>
                    <span class="mt-1 block text-xs font-bold text-slate-800">Needs Care</span>
                </label>
            </div>

            <textarea name="comment" rows="2" placeholder="Tell us anything we can do to make your stay better..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-900 focus:border-amber-500 focus:bg-white focus:outline-none transition"></textarea>

            <button class="w-full rounded-2xl border border-slate-300 bg-slate-100 py-2.5 text-xs font-bold text-slate-800 hover:bg-slate-200 transition">
                Share Guest Feedback
            </button>
        </form>
    </section>

    <!-- Footer Security & Privacy Badge -->
    <footer class="pt-6 pb-12 text-center text-[10px] text-slate-400 space-y-2 flex flex-col items-center">
        <div class="flex items-center justify-center gap-2">
            <span class="text-[10px] text-slate-400 font-medium">Powered by</span>
            <x-brand-logo size="xs" :tagline="null" :href="route('landing')" />
        </div>
        <p class="font-medium text-slate-500">{{ $hotel->name }} · Digital Guest Experience Engine</p>
        <p>Private & secure connection · No personal profiling or third-party cookies.</p>
    </footer>

</main>

<script>
function guestPortal() {
    return {
        activeTab: 'services',
        wifiCopied: false,

        copyWifi() {
            navigator.clipboard.writeText('StayComfortable');
            this.wifiCopied = true;
            setTimeout(() => this.wifiCopied = false, 2500);
        }
    };
}
if ("serviceWorker" in navigator) {
    window.addEventListener("load", () => navigator.serviceWorker.register("/sw.js"));
}
</script>

</body>
</html>
