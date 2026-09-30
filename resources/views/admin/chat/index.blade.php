@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800/80 pb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-teal-500/10 px-2 py-0.5 text-xs font-mono font-medium text-teal-400 border border-teal-500/20">Front Desk Messaging</span>
                <span class="text-xs text-slate-500">· Real-Time Concierge Inquiries</span>
            </div>
            <h1 class="mt-2 text-2xl lg:text-3xl font-bold tracking-tight text-white">Guest Concierge Messages</h1>
            <p class="mt-1 text-sm text-slate-400">Direct two-way digital communication between checked-in guests and the on-duty reception team.</p>
        </div>
    </div>

    <!-- Conversations List -->
    <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 backdrop-blur-xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-800/80 px-6 py-4">
            <h2 class="font-semibold text-white">Open Inquiries</h2>
            <span class="text-xs text-slate-400">Total: {{ $conversations->total() }}</span>
        </div>

        <div class="divide-y divide-slate-800/60">
            @forelse($conversations as $c)
            @php
                $lastMsg = $c->messages->last();
            @endphp
            <a href="{{ route('admin.chat.show', $c) }}" class="flex items-center justify-between p-5 hover:bg-slate-800/40 transition">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-2xl bg-teal-500/10 border border-teal-500/20 text-teal-300 font-bold text-sm">
                        {{ $c->room ? $c->room->number : 'G' }}
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-white text-sm">Room {{ $c->room ? $c->room->number : 'General Guest' }}</span>
                            <span class="rounded-full bg-emerald-500/20 text-emerald-300 px-2 py-0.2 text-[10px] font-semibold uppercase">Active</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-400 truncate max-w-xl">{{ $lastMsg ? $lastMsg->body : 'No messages' }}</p>
                    </div>
                </div>

                <div class="text-right flex-shrink-0 text-xs text-slate-500">
                    {{ $c->last_message_at ? $c->last_message_at->diffForHumans() : 'Just now' }}
                </div>
            </a>
            @empty
            <div class="p-12 text-center text-slate-500 text-xs">No guest conversations at this time.</div>
            @endforelse
        </div>

        <div class="border-t border-slate-800/80 px-6 py-4">
            {{ $conversations->links() }}
        </div>
    </div>
</div>
@endsection
