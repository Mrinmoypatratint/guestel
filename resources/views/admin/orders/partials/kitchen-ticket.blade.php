<div class="rounded-2xl border {{ $accent ?? 'border-slate-800' }} bg-slate-900/90 p-4 shadow-xl space-y-3 transition duration-200 hover:border-amber-400/50">
    <!-- Card Header -->
    <div class="flex items-start justify-between gap-2 border-b border-slate-800/80 pb-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="font-mono text-sm font-black text-amber-300">{{ $ord->order_number }}</span>
                <span class="rounded bg-slate-800 px-1.5 py-0.5 text-[10px] font-bold text-white">
                    Room {{ $ord->room?->number ?? 'F&B' }}
                </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">
                Total: <span class="font-bold text-emerald-400">₹{{ number_format((float)$ord->total, 2) }}</span>
            </p>
        </div>

        <div class="text-right">
            <span class="inline-flex items-center gap-1 font-mono text-xs font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-lg border border-amber-500/20">
                ⏱ {{ $ord->created_at->diffForHumans(null, true) }}
            </span>
        </div>
    </div>

    <!-- Items List -->
    <div class="space-y-1.5 text-xs">
        @foreach($ord->items as $it)
        <div class="flex items-start justify-between gap-2 text-slate-200">
            <div class="flex items-start gap-1.5">
                <span class="font-mono font-bold text-amber-300 text-[11px] bg-slate-800 px-1 rounded">{{ $it->quantity }}x</span>
                <span class="font-medium text-white">{{ $it->item_name }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Special Chef Instructions -->
    @if($ord->special_instructions)
    <div class="rounded-xl border border-amber-500/30 bg-amber-950/20 p-2.5 text-xs text-amber-200">
        <span class="font-bold uppercase text-[10px] text-amber-400 block tracking-wider">Chef Note:</span>
        "{{ $ord->special_instructions }}"
    </div>
    @endif

    <!-- Workflow Progression Actions -->
    <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between gap-2">
        @if($ord->status === 'PENDING')
        <form method="post" action="{{ route('admin.orders.update', $ord) }}" class="w-full">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="ACCEPTED">
            <button type="submit" class="w-full rounded-xl bg-blue-500 py-2 text-xs font-bold text-white hover:bg-blue-400 transition cursor-pointer">
                Accept Ticket
            </button>
        </form>
        @elseif($ord->status === 'ACCEPTED')
        <form method="post" action="{{ route('admin.orders.update', $ord) }}" class="w-full">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="PREPARING">
            <button type="submit" class="w-full rounded-xl bg-orange-500 py-2 text-xs font-bold text-white hover:bg-orange-400 transition cursor-pointer">
                Start Preparing
            </button>
        </form>
        @elseif($ord->status === 'PREPARING')
        <form method="post" action="{{ route('admin.orders.update', $ord) }}" class="w-full">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="READY">
            <button type="submit" class="w-full rounded-xl bg-emerald-500 py-2 text-xs font-bold text-slate-950 hover:bg-emerald-400 transition cursor-pointer">
                Order Ready!
            </button>
        </form>
        @elseif($ord->status === 'READY')
        <form method="post" action="{{ route('admin.orders.update', $ord) }}" class="w-full">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="DELIVERED">
            <button type="submit" class="w-full rounded-xl bg-slate-700 py-2 text-xs font-bold text-white hover:bg-slate-600 transition cursor-pointer">
                Delivered / Out
            </button>
        </form>
        @endif

        @if(!in_array($ord->status, ['DELIVERED', 'CANCELLED']))
        <form method="post" action="{{ route('admin.orders.update', $ord) }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="CANCELLED">
            <button type="submit" class="rounded-xl p-2 text-slate-400 hover:bg-slate-800 hover:text-rose-400 transition text-xs" title="Cancel Ticket">
                &times;
            </button>
        </form>
        @endif
    </div>
</div>
