@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800/80 pb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-yellow-500/10 px-2 py-0.5 text-xs font-mono font-medium text-yellow-400 border border-yellow-500/20">Operational Alerts</span>
                <span class="text-xs text-slate-500">· System & Staff Notifications</span>
            </div>
            <h1 class="mt-2 text-2xl lg:text-3xl font-bold tracking-tight text-white">Notifications & Alerts</h1>
            <p class="mt-1 text-sm text-slate-400">Review critical SLA warnings, kitchen order dispatches, and guest messages.</p>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 backdrop-blur-xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-800/80 px-6 py-4">
            <h2 class="font-semibold text-white">Alerts Feed</h2>
            <span class="text-xs text-slate-400">Total: {{ $notifications->total() }}</span>
        </div>

        <div class="divide-y divide-slate-800/60">
            @forelse($notifications as $n)
            <div class="p-5 flex items-start justify-between gap-4 transition hover:bg-slate-800/30 {{ $n->read_at ? 'opacity-60' : 'bg-slate-800/20' }}">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-xl {{ $n->read_at ? 'bg-slate-800 text-slate-400' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                        <x-icon name="notifications" class="w-4 h-4" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-white text-sm">{{ $n->data['title'] ?? 'Operational Notice' }}</span>
                            @if(!$n->read_at)
                            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-300 mt-1 leading-relaxed">{{ $n->data['message'] ?? (is_array($n->data) ? json_encode($n->data) : $n->data) }}</p>
                        <p class="text-[11px] text-slate-500 mt-1.5">{{ $n->created_at->diffForHumans() }} ({{ $n->created_at->format('M d, H:i') }})</p>
                    </div>
                </div>

                @if(!$n->read_at)
                <form method="post" action="{{ route('admin.notifications.read', $n->id) }}">
                    @csrf
                    <button class="rounded-xl border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-300 hover:bg-slate-700 transition">
                        Mark Read
                    </button>
                </form>
                @endif
            </div>
            @empty
            <div class="p-12 text-center text-slate-500 text-xs">No notifications recorded.</div>
            @endforelse
        </div>

        <div class="border-t border-slate-800/80 px-6 py-4">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection
