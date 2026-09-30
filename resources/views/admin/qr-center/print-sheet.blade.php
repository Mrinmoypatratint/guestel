<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Printable Guestroom Tent Cards · {{ $hotel->name }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; color: #000 !important; }
            .tent-card { page-break-inside: avoid; break-inside: avoid; margin-bottom: 2cm; border: 1px dashed #ccc !important; }
        }
        .luxury-serif { font-family: 'Cinzel', serif; }
        .body-sans { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 body-sans text-slate-900 p-8">

<!-- Floating Print Controls (Hidden upon printing) -->
<div class="no-print fixed top-5 right-5 z-50 flex items-center gap-3 rounded-2xl border border-slate-300 bg-white/95 p-3 shadow-2xl backdrop-blur">
    <a href="{{ route('admin.qr-center.index') }}" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
        ← Back to QR Center
    </a>
    <button onclick="window.print()" class="flex items-center gap-2 rounded-xl bg-slate-950 px-5 py-2 text-xs font-semibold text-white shadow hover:bg-slate-800 transition">
        <span>Print Tent Cards</span>
    </button>
</div>

<div class="no-print mx-auto max-w-4xl mb-8">
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-900">
        <h2 class="font-semibold text-sm">Guestroom Desk Tent Cards Print Preview</h2>
        <p class="text-xs mt-1 text-amber-800">These cards are styled with precision dimensions for folding on guest nightstands or writing desks. Simply click "Print Tent Cards" and use standard Letter/A4 paper or heavy cardstock.</p>
    </div>
</div>

<div class="mx-auto max-w-4xl space-y-12">
    @foreach($cards as $card)
    <div class="tent-card mx-auto max-w-xl rounded-3xl border border-slate-200 bg-white shadow-xl overflow-hidden">
        <!-- Top Half: Decorative Header & Hotel Name -->
        <div class="relative bg-slate-950 px-8 py-10 text-center text-white overflow-hidden">
            <div class="absolute inset-0 opacity-20" style="background: radial-gradient(circle at center, #C8A96A, transparent 70%);"></div>
            
            <p class="text-[10px] uppercase tracking-[0.25em] text-amber-300/80 font-semibold">Hospitality Experience</p>
            <h1 class="luxury-serif mt-2 text-3xl font-bold tracking-tight text-white">{{ $hotel->name }}</h1>
            
            <div class="mt-4 flex items-center justify-center gap-2">
                <div class="h-px w-10 bg-amber-400/60"></div>
                <div class="h-1.5 w-1.5 rounded-full bg-amber-400"></div>
                <div class="h-px w-10 bg-amber-400/60"></div>
            </div>

            @if($card['room'])
            <div class="mt-4 inline-block rounded-full border border-amber-400/30 bg-white/5 px-4 py-1 text-xs font-semibold tracking-wide text-amber-200">
                Room {{ $card['room']->number }} · {{ $card['room']->roomType?->name ?? 'Standard' }}
            </div>
            @else
            <div class="mt-4 inline-block rounded-full border border-amber-400/30 bg-white/5 px-4 py-1 text-xs font-semibold tracking-wide text-amber-200">
                Welcome to {{ $hotel->name }}
            </div>
            @endif
        </div>

        <!-- Middle: Fold Guide Line -->
        <div class="relative flex items-center justify-center py-2 bg-slate-50 border-y border-dashed border-slate-300">
            <span class="text-[9px] font-mono uppercase tracking-widest text-slate-400">Fold Here for Desk Display</span>
        </div>

        <!-- Bottom Half: The QR Code & Guest Instructions -->
        <div class="px-8 py-8 text-center bg-white">
            <div class="mx-auto flex h-52 w-52 items-center justify-center rounded-2xl border-2 border-slate-900 bg-white p-3 shadow-md">
                <div class="w-full h-full">
                    {!! $card['svg'] !!}
                </div>
            </div>

            <p class="mt-5 text-sm font-bold text-slate-900">Scan with your smartphone camera</p>
            <p class="mt-1 text-xs text-slate-500 max-w-xs mx-auto">No app or registration required. Instantly order in-room dining, request fresh amenities, or message reception.</p>

            <div class="mt-6 grid grid-cols-3 gap-3 border-t border-slate-100 pt-5 text-center text-xs">
                <div>
                    <span class="block font-semibold text-slate-900">Dining</span>
                    <span class="text-[10px] text-slate-500">Live Menu & Ordering</span>
                </div>
                <div>
                    <span class="block font-semibold text-slate-900">Concierge</span>
                    <span class="text-[10px] text-slate-500">Services & Wi-Fi</span>
                </div>
                <div>
                    <span class="block font-semibold text-slate-900">Direct Chat</span>
                    <span class="text-[10px] text-slate-500">Front Desk Assistance</span>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

</body>
</html>
