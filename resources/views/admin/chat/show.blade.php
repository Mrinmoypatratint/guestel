@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between border-b border-slate-800/80 pb-5">
        <a class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition" href="{{ route('admin.chat.index') }}">
            <span>← Back to Inquiries</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 px-3 py-1 text-xs font-semibold">
                Room {{ $conversation->room ? $conversation->room->number : 'General Guest' }}
            </span>
        </div>
    </div>

    <!-- Chat Card -->
    <div class="mx-auto max-w-3xl rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
            <div>
                <h2 class="font-bold text-white text-base">Conversation Thread</h2>
                <p class="text-xs text-slate-400">All responses are immediately pushed to the guest's mobile portal.</p>
            </div>
            <span class="flex items-center gap-1.5 text-xs font-semibold text-emerald-400">
                <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Connected
            </span>
        </div>

        <!-- Messages Bubble Stream -->
        <div class="my-6 max-h-[55vh] space-y-3 overflow-y-auto pr-2">
            @forelse($conversation->messages as $m)
            <div class="flex flex-col {{ $m->sender_type === 'staff' ? 'items-end' : 'items-start' }}">
                <div class="max-w-[80%] rounded-2xl px-4 py-3 text-xs leading-relaxed shadow-sm {{ $m->sender_type === 'staff' ? 'bg-amber-500 text-slate-950 font-medium rounded-br-xs' : 'bg-slate-800 text-slate-100 rounded-bl-xs border border-slate-700/60' }}">
                    {{ $m->body }}
                </div>
                <div class="mt-1 flex items-center gap-2 text-[10px] text-slate-500">
                    <span>{{ $m->sender_type === 'staff' ? 'Staff' : 'Guest' }}</span>
                    <span>·</span>
                    <span>{{ $m->created_at->format('M d, H:i') }}</span>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-xs text-slate-500">No messages yet.</div>
            @endforelse
        </div>

        <!-- Reply Form -->
        <form class="flex gap-3 border-t border-slate-800/80 pt-4" method="post" action="{{ route('admin.chat.reply', $conversation) }}">
            @csrf
            <input class="flex-1 rounded-2xl border border-slate-800 bg-slate-950 px-4 py-3 text-xs text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none" name="body" maxlength="2000" placeholder="Type official response to guest..." required autofocus>
            <button class="rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-3 text-xs font-bold text-slate-950 hover:brightness-110 transition shadow">
                Send Reply
            </button>
        </form>
    </div>
</div>
@endsection
