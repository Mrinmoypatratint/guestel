<!doctype html>
<html lang="en" class="h-full bg-[#e8edf5]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>In-Room Dining · Culinary Experience</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/guestel-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#0f172a">

    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
        .luxury-heading { font-family: 'Cinzel', serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-full bg-[#f8f7f4] text-slate-900 antialiased pb-28" x-data="diningApp()">

<!-- Top Bar -->
<header class="sticky top-0 z-40 border-b border-black/5 bg-white/90 px-5 py-4 backdrop-blur-md">
    <div class="mx-auto flex max-w-lg items-center justify-between">
        <a href="{{ route('guest.stay', $g->public_id) }}" class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-950">
            <span>← Return to Concierge</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="inline-block h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-medium text-slate-600">Kitchen Open</span>
        </div>
    </div>
</header>

<main class="mx-auto max-w-lg px-5 pt-6 space-y-6">

    @foreach($restaurants as $restaurant)
    <!-- Restaurant Header -->
    <div class="rounded-3xl bg-slate-950 p-6 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-8 -top-8 h-36 w-36 rounded-full opacity-20 blur-2xl bg-amber-400"></div>
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-amber-300">In-Room Dining</p>
        <h1 class="luxury-heading mt-1 text-2xl font-bold text-white">{{ $restaurant->name }}</h1>
        <p class="mt-1.5 text-xs text-slate-300/80 leading-relaxed">{{ $restaurant->description }}</p>
        
        <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-slate-400 border-t border-white/10 pt-3">
            <span>Hours: <strong class="text-white">{{ substr($restaurant->opens_at ?? '06:00', 0, 5) }} – {{ substr($restaurant->closes_at ?? '23:30', 0, 5) }}</strong></span>
            <span>Tax Rate: <strong class="text-white">{{ $restaurant->tax_rate }}%</strong></span>
            <span>Currency: <strong class="text-amber-300">{{ $restaurant->currency }}</strong></span>
        </div>
    </div>

    <!-- Category Filter Chips -->
    <div class="flex gap-2 overflow-x-auto pb-1 no-scrollbar">
        <button @click="selectedCategory = 'all'" :class="selectedCategory === 'all' ? 'bg-slate-950 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200'" class="whitespace-nowrap rounded-2xl px-4 py-2 text-xs font-bold transition">
            Full Menu
        </button>
        @foreach($restaurant->categories as $cat)
        <button @click="selectedCategory = '{{ $cat->id }}'" :class="selectedCategory === '{{ $cat->id }}' ? 'bg-slate-950 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200'" class="whitespace-nowrap rounded-2xl px-4 py-2 text-xs font-bold transition">
            {{ $cat->name }}
        </button>
        @endforeach
    </div>

    <!-- Menu Items List -->
    <div class="space-y-4">
        @foreach($restaurant->menuItems as $item)
        <div x-show="selectedCategory === 'all' || selectedCategory === '{{ $item->menu_category_id }}'" class="rounded-3xl border border-black/5 bg-white p-5 shadow-sm transition hover:shadow-md">
            <div class="flex items-start gap-4">
                @if($item->image_path)
                <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-2xl bg-slate-900 shadow-xs border border-slate-200/60">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="h-full w-full object-cover object-center">
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-slate-900 text-sm truncate">{{ $item->name }}</h3>
                        @if($item->preparation_minutes)
                        <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-mono text-slate-500">~{{ $item->preparation_minutes }}m</span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">{{ $item->description }}</p>

                    <!-- Dietary & Allergens Badges -->
                    <div class="mt-2.5 flex flex-wrap gap-1">
                        @if($item->dietary_info)
                            @foreach($item->dietary_info as $diet)
                            <span class="rounded-full bg-emerald-50 text-emerald-800 px-2 py-0.5 text-[10px] font-semibold">{{ $diet }}</span>
                            @endforeach
                        @endif
                        @if($item->allergens)
                            @foreach($item->allergens as $allg)
                            <span class="rounded-full bg-rose-50 text-rose-700 px-2 py-0.5 text-[10px] font-semibold">Contains: {{ $allg }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Price & Add Button -->
                <div class="flex flex-col items-end gap-3 flex-shrink-0">
                    <span class="font-bold text-slate-900 text-base">₹{{ number_format((float)$item->price, 2) }}</span>

                    <!-- Quantity Adjuster -->
                    <template x-if="getItemQty({{ $item->id }}) === 0">
                        <button @click="addItem({{ $item->id }}, '{{ addslashes($item->name) }}', {{ (float)$item->price }}, {{ $restaurant->id }})" class="rounded-2xl bg-slate-950 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-slate-800 transition">
                            + Add
                        </button>
                    </template>

                    <template x-if="getItemQty({{ $item->id }}) > 0">
                        <div class="flex items-center gap-2 rounded-2xl border border-slate-300 bg-slate-100 p-1">
                            <button @click="decrementItem({{ $item->id }})" class="flex h-7 w-7 items-center justify-center rounded-xl bg-white text-slate-900 shadow-xs font-bold text-xs">-</button>
                            <span class="font-mono text-xs font-bold px-1" x-text="getItemQty({{ $item->id }})"></span>
                            <button @click="incrementItem({{ $item->id }})" class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-950 text-white shadow-xs font-bold text-xs">+</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach

    <!-- Footer Security & Brand Badge -->
    <footer class="pt-8 pb-10 text-center text-[10px] text-slate-400 space-y-2 flex flex-col items-center">
        <div class="flex items-center justify-center gap-2">
            <span class="text-[10px] text-slate-400 font-medium">Powered by</span>
            <x-brand-logo size="xs" :tagline="null" :href="route('landing')" />
        </div>
        <p>© {{ date('Y') }} Guestel · Talisha Software</p>
    </footer>

</main>

<!-- Sticky Bottom Cart Pill -->
<div x-show="totalCount > 0" x-cloak class="fixed bottom-6 inset-x-0 z-40 px-5">
    <div class="mx-auto max-w-lg">
        <button @click="cartDrawer = true" class="flex w-full items-center justify-between rounded-3xl bg-slate-950 px-6 py-4 text-white shadow-2xl transition hover:brightness-110">
            <div class="flex items-center gap-3">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-amber-400 text-xs font-bold text-slate-950 font-mono" x-text="totalCount"></span>
                <span class="font-bold text-sm">Review Dining Order</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-bold text-base text-amber-300" x-text="'₹' + cartTotal.toFixed(2)"></span>
                <x-icon name="chevron-right" class="w-4 h-4 text-slate-400" />
            </div>
        </button>
    </div>
</div>

<!-- Slide-over Cart Review Drawer -->
<div x-show="cartDrawer" x-cloak class="fixed inset-0 z-50 flex items-end justify-center bg-black/80 backdrop-blur-sm">
    <div @click.away="cartDrawer = false" class="w-full max-w-lg rounded-t-3xl border-t border-slate-200 bg-white p-6 shadow-2xl max-h-[85vh] flex flex-col">
        <!-- Drawer Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-bold text-lg text-slate-900">Your Dining Order</h3>
                <p class="text-xs text-slate-500">Delivered directly to your room</p>
            </div>
            <button @click="cartDrawer = false" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-900">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        <!-- Selected Items List -->
        <div class="flex-1 overflow-y-auto py-4 space-y-3">
            <template x-for="item in cartItems" :key="item.id">
                <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-3.5 border border-slate-100">
                    <div class="min-w-0 pr-2">
                        <p class="font-bold text-xs text-slate-900 truncate" x-text="item.name"></p>
                        <p class="text-[11px] font-mono text-slate-500 mt-0.5" x-text="'₹' + (item.price * item.quantity).toFixed(2) + ' (₹' + item.price.toFixed(2) + ' each)'"></p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button @click="decrementItem(item.id)" class="flex h-7 w-7 items-center justify-center rounded-xl bg-white border border-slate-200 font-bold text-xs">-</button>
                        <span class="font-mono text-xs font-bold px-1" x-text="item.quantity"></span>
                        <button @click="incrementItem(item.id)" class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-950 text-white font-bold text-xs">+</button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Checkout Form Submission -->
        <form method="post" action="{{ route('guest.order', $g->public_id) }}" class="border-t border-slate-100 pt-4 space-y-3">
            @csrf
            <input type="hidden" name="restaurant_id" :value="cartRestaurantId">
            <input type="hidden" name="idempotency_key" :value="idempotencyKey">

            <!-- Hidden input bindings for Laravel validation -->
            <template x-for="(item, index) in cartItems" :key="'input_' + item.id">
                <div>
                    <input type="hidden" :name="'items[' + index + '][menu_item_id]'" :value="item.id">
                    <input type="hidden" :name="'items[' + index + '][quantity]'" :value="item.quantity">
                </div>
            </template>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Chef & Delivery Instructions (Optional)</label>
                <textarea name="special_instructions" rows="2" placeholder="E.g., Please provide cutlery for two, dressing on the side..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-900 focus:border-amber-500 focus:bg-white focus:outline-none transition"></textarea>
            </div>

            <!-- Financial Recalculation Notice -->
            <div class="rounded-xl bg-amber-50/70 p-3 text-[11px] text-amber-900 flex justify-between items-center">
                <span>Estimated Subtotal</span>
                <span class="font-bold text-sm" x-text="'₹' + cartTotal.toFixed(2)"></span>
            </div>
            <p class="text-[10px] text-slate-400">All prices and applicable resort taxes are authoritatively recalculated from server inventory upon submission.</p>

            <button type="submit" :disabled="submitting || totalCount === 0" @click="submitting = true" class="w-full rounded-2xl bg-slate-950 py-4 text-xs font-bold text-white shadow-xl hover:bg-slate-900 transition disabled:opacity-50">
                <span x-text="submitting ? 'Transmitting to Kitchen...' : 'Confirm In-Room Dining Order'"></span>
            </button>
        </form>
    </div>
</div>

<script>
function diningApp() {
    return {
        selectedCategory: 'all',
        cartDrawer: false,
        cartItems: [],
        cartRestaurantId: null,
        submitting: false,
        idempotencyKey: 'ord_' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36),

        get totalCount() {
            return this.cartItems.reduce((acc, it) => acc + it.quantity, 0);
        },

        get cartTotal() {
            return this.cartItems.reduce((acc, it) => acc + (it.price * it.quantity), 0);
        },

        getItemQty(itemId) {
            const found = this.cartItems.find(it => it.id === itemId);
            return found ? found.quantity : 0;
        },

        addItem(id, name, price, restId) {
            this.cartRestaurantId = restId;
            const existing = this.cartItems.find(it => it.id === id);
            if (existing) {
                existing.quantity++;
            } else {
                this.cartItems.push({ id, name, price, quantity: 1 });
            }
        },

        incrementItem(id) {
            const existing = this.cartItems.find(it => it.id === id);
            if (existing) {
                existing.quantity++;
            }
        },

        decrementItem(id) {
            const idx = this.cartItems.findIndex(it => it.id === id);
            if (idx > -1) {
                if (this.cartItems[idx].quantity > 1) {
                    this.cartItems[idx].quantity--;
                } else {
                    this.cartItems.splice(idx, 1);
                }
            }
        }
    };
}
</script>

</body>
</html>
