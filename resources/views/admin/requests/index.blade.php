@extends('layouts.app', ['title' => 'Housekeeping & Service Dispatch'])

@section('content')
<div class="space-y-6" x-data="{ priorityFilter: 'all', statusFilter: 'active' }">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800/80 pb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-purple-500/10 px-2 py-0.5 text-xs font-mono font-medium text-purple-400 border border-purple-500/20">Housekeeping & Operations</span>
                <span class="text-xs text-slate-500">· Real-Time SLA Dispatch</span>
            </div>
            <h1 class="mt-2 text-2xl lg:text-3xl font-black tracking-tight text-white">Housekeeping & Service Queue</h1>
            <p class="mt-1 text-sm text-slate-400">Track room cleaning, extra towels, bathroom amenities, and guest maintenance tasks with live SLA timers.</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-400">SLA Response Standard: <strong class="text-emerald-400">10 min</strong></span>
        </div>
    </div>

    <!-- Priority Metrics & Filters -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <button 
            type="button" 
            @click="priorityFilter = 'urgent'"
            :class="priorityFilter === 'urgent' ? 'border-rose-500 bg-rose-500/10' : 'border-slate-800 bg-slate-900/60 hover:border-slate-700'"
            class="text-left p-4 rounded-2xl border transition duration-200 cursor-pointer">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-400">Urgent</span>
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
            </div>
            <p class="mt-2 text-2xl font-black text-white">
                {{ $requests->where('priority', 'urgent')->whereNotIn('status', ['COMPLETED', 'CANCELLED'])->count() }}
            </p>
            <p class="text-[11px] text-slate-400">Immediate attention</p>
        </button>

        <button 
            type="button" 
            @click="priorityFilter = 'high'"
            :class="priorityFilter === 'high' ? 'border-amber-500 bg-amber-500/10' : 'border-slate-800 bg-slate-900/60 hover:border-slate-700'"
            class="text-left p-4 rounded-2xl border transition duration-200 cursor-pointer">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-400">High</span>
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
            </div>
            <p class="mt-2 text-2xl font-black text-white">
                {{ $requests->where('priority', 'high')->whereNotIn('status', ['COMPLETED', 'CANCELLED'])->count() }}
            </p>
            <p class="text-[11px] text-slate-400">Towels, linens, water</p>
        </button>

        <button 
            type="button" 
            @click="priorityFilter = 'normal'"
            :class="priorityFilter === 'normal' ? 'border-blue-500 bg-blue-500/10' : 'border-slate-800 bg-slate-900/60 hover:border-slate-700'"
            class="text-left p-4 rounded-2xl border transition duration-200 cursor-pointer">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-400">Normal</span>
                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
            </div>
            <p class="mt-2 text-2xl font-black text-white">
                {{ $requests->where('priority', 'normal')->whereNotIn('status', ['COMPLETED', 'CANCELLED'])->count() }}
            </p>
            <p class="text-[11px] text-slate-400">Standard turn-down</p>
        </button>

        <button 
            type="button" 
            @click="priorityFilter = 'all'"
            :class="priorityFilter === 'all' ? 'border-purple-500 bg-purple-500/10' : 'border-slate-800 bg-slate-900/60 hover:border-slate-700'"
            class="text-left p-4 rounded-2xl border transition duration-200 cursor-pointer">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-purple-400">All Active</span>
                <span class="w-2 h-2 rounded-full bg-purple-400"></span>
            </div>
            <p class="mt-2 text-2xl font-black text-white">
                {{ $requests->whereNotIn('status', ['COMPLETED', 'CANCELLED'])->count() }}
            </p>
            <p class="text-[11px] text-slate-400">Full operational queue</p>
        </button>
    </div>

    <!-- Requests Table / Card Stream -->
    <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 backdrop-blur-xl overflow-hidden shadow-xl">
        <div class="flex flex-col sm:flex-row items-center justify-between border-b border-slate-800/80 px-6 py-4 gap-4">
            <div class="flex items-center gap-3">
                <h2 class="font-bold text-white text-base">Housekeeping & Amenities Queue</h2>
                <span class="text-xs text-slate-400">Showing {{ $requests->total() }} total requests</span>
            </div>

            <div class="flex items-center gap-2">
                <button @click="priorityFilter = 'all'" :class="priorityFilter === 'all' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white'" class="px-3 py-1 rounded-xl text-xs transition">All</button>
                <button @click="priorityFilter = 'urgent'" :class="priorityFilter === 'urgent' ? 'bg-rose-500 text-white font-bold' : 'text-slate-400 hover:text-white'" class="px-3 py-1 rounded-xl text-xs transition">Urgent</button>
                <button @click="priorityFilter = 'high'" :class="priorityFilter === 'high' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white'" class="px-3 py-1 rounded-xl text-xs transition">High</button>
                <button @click="priorityFilter = 'normal'" :class="priorityFilter === 'normal' ? 'bg-blue-500 text-white font-bold' : 'text-slate-400 hover:text-white'" class="px-3 py-1 rounded-xl text-xs transition">Normal</button>
            </div>
        </div>

        <div class="divide-y divide-slate-800/60">
            @forelse($requests as $r)
            @php
                $isOverdue = $r->completion_due_at && $r->completion_due_at->isPast() && $r->status !== 'COMPLETED';
            @endphp
            <div 
                x-show="priorityFilter === 'all' || priorityFilter === '{{ $r->priority }}'"
                class="p-6 transition hover:bg-slate-800/30 {{ $isOverdue ? 'bg-rose-950/15 border-l-4 border-rose-500' : '' }}">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <!-- Left: Request info -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-sm font-bold text-amber-400">{{ $r->request_number }}</span>
                            <span class="text-slate-500">·</span>
                            <span class="text-base font-bold text-white">Room {{ $r->room?->number ?? 'General Property' }}</span>
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $r->priority === 'urgent' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : ($r->priority === 'high' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-slate-800 text-slate-300') }}">
                                {{ ucfirst($r->priority) }} Priority
                            </span>
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $r->status === 'COMPLETED' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : ($r->status === 'PENDING' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-blue-500/20 text-blue-300 border border-blue-500/30') }}">
                                {{ str_replace('_', ' ', $r->status) }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-slate-200 text-sm">{{ $r->service?->name ?? 'Guest Service' }}</span>
                            <span class="text-xs text-slate-400">· Department: <strong class="text-slate-300">{{ $r->department?->name ?? 'Housekeeping' }}</strong></span>
                        </div>

                        @if($r->guest_note)
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3 text-xs italic text-amber-200/90 max-w-xl">
                            "{{ $r->guest_note }}"
                        </div>
                        @elseif($r->note)
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3 text-xs italic text-amber-200/90 max-w-xl">
                            "{{ $r->note }}"
                        </div>
                        @endif

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400">
                            <span>Created: {{ $r->created_at->diffForHumans() }}</span>
                            @if($isOverdue)
                            <span class="flex items-center gap-1 font-semibold text-rose-400">
                                <x-icon name="alert" class="w-3.5 h-3.5" />
                                <span>SLA Target Breached</span>
                            </span>
                            @elseif($r->completion_due_at)
                            <span class="text-slate-400 font-mono text-[11px]">Due: {{ $r->completion_due_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Quick Operational Status Updates -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if($r->status === 'PENDING')
                        <form method="post" action="{{ route('admin.requests.update', $r) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="ACCEPTED">
                            <button class="rounded-xl bg-blue-500/20 border border-blue-500/30 px-3.5 py-2 text-xs font-semibold text-blue-300 hover:bg-blue-500 hover:text-slate-950 transition cursor-pointer">
                                Accept Ticket
                            </button>
                        </form>
                        @endif

                        @if(in_array($r->status, ['PENDING', 'ACCEPTED']))
                        <form method="post" action="{{ route('admin.requests.update', $r) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="IN_PROGRESS">
                            <button class="rounded-xl bg-amber-500/20 border border-amber-500/30 px-3.5 py-2 text-xs font-semibold text-amber-300 hover:bg-amber-500 hover:text-slate-950 transition cursor-pointer">
                                Start Cleaning / Task
                            </button>
                        </form>
                        @endif

                        @if($r->status !== 'COMPLETED')
                        <form method="post" action="{{ route('admin.requests.update', $r) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="COMPLETED">
                            <button class="rounded-xl bg-emerald-500/20 border border-emerald-500/30 px-3.5 py-2 text-xs font-bold text-emerald-300 hover:bg-emerald-500 hover:text-slate-950 transition cursor-pointer">
                                Complete
                            </button>
                        </form>

                        <form method="post" action="{{ route('admin.requests.update', $r) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="ESCALATED">
                            <button class="rounded-xl bg-rose-500/10 border border-rose-500/20 px-3 py-2 text-xs font-medium text-rose-400 hover:bg-rose-500 hover:text-white transition cursor-pointer" title="Escalate to Front Desk">
                                Escalate
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-sm text-slate-400">
                No active service requests in the housekeeping queue. All rooms are serviced.
            </div>
            @endforelse
        </div>

        @if($requests->hasPages())
        <div class="border-t border-slate-800 p-4">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
