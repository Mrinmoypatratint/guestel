@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between border-b border-slate-800/80 pb-5">
        <a class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition" href="{{ route('admin.rooms.index') }}">
            <span>← Back to Rooms Directory</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $room->status === 'available' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : ($room->status === 'occupied' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30') }}">
                Status: {{ ucfirst($room->status) }}
            </span>
        </div>
    </div>

    <!-- Main Room Detail Grid -->
    <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
        <!-- Left: Room Specs & History -->
        <div class="space-y-6">
            <!-- Room Overview Card -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 lg:p-8 backdrop-blur-xl">
                @if(file_exists(public_path('storage/rooms/' . $room->number . '/cover.jpg')))
                <div class="relative h-48 w-full overflow-hidden rounded-2xl mb-6 shadow-lg border border-slate-800">
                    <img src="{{ asset('storage/rooms/' . $room->number . '/cover.jpg') }}" alt="Room {{ $room->number }}" class="h-full w-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
                    <div class="absolute bottom-3 left-4">
                        <span class="rounded-md bg-black/60 px-2.5 py-1 text-[11px] font-mono text-amber-300 backdrop-blur-md">Room {{ $room->number }} Ambiance</span>
                    </div>
                </div>
                @endif

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <span class="rounded bg-amber-500/10 px-2 py-0.5 text-xs font-mono font-medium text-amber-400 border border-amber-500/20">Room Profile</span>
                        <h1 class="mt-2 text-3xl lg:text-4xl font-bold tracking-tight text-white">Room {{ $room->number }}</h1>
                        <p class="mt-1 text-sm text-slate-400">{{ $room->name ? $room->name . ' · ' : '' }}{{ $room->roomType?->name ?? 'Standard Room' }}</p>
                    </div>

                    <form method="post" action="{{ route('admin.rooms.status', $room) }}" class="flex items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-700 bg-slate-800 px-3.5 py-2 text-xs font-semibold text-slate-200 focus:outline-none">
                            <option value="available" {{ $room->status === 'available' ? 'selected' : '' }}>Set Available</option>
                            <option value="occupied" {{ $room->status === 'occupied' ? 'selected' : '' }}>Set Occupied</option>
                            <option value="maintenance" {{ $room->status === 'maintenance' ? 'selected' : '' }}>Set Maintenance</option>
                        </select>
                    </form>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-4 border-t border-slate-800/80 pt-6">
                    <div>
                        <span class="text-xs text-slate-500">Floor Level</span>
                        <p class="mt-1 text-sm font-semibold text-white">{{ $room->floor?->name ?? 'Level 1' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500">Nightly Rate</span>
                        <p class="mt-1 text-sm font-semibold text-white">₹{{ number_format((float)($room->roomType?->base_rate ?? 0), 2) }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500">QR Scan Total</span>
                        <p class="mt-1 text-sm font-semibold text-amber-300">{{ $scansCount }} Scans</p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500">Active Guest Sessions</span>
                        <p class="mt-1 text-sm font-semibold text-emerald-400">{{ $sessions->where('status', 'active')->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Service Requests for this Room -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                    <h2 class="font-bold text-white text-base">Service Requests History</h2>
                    <span class="text-xs text-slate-400 font-mono">{{ $requests->count() }} records</span>
                </div>

                <div class="mt-4 divide-y divide-slate-800/60">
                    @forelse($requests as $r)
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-white">{{ $r->request_number }} · {{ $r->service?->name ?? 'Service' }}</span>
                            <p class="text-[11px] text-slate-400 mt-0.5">Dept: {{ $r->department?->name ?? 'Staff' }} · {{ $r->created_at->diffForHumans() }}</p>
                            @if($r->note)
                            <p class="text-[11px] italic text-amber-200/80 mt-1">"{{ $r->note }}"</p>
                            @endif
                        </div>
                        <span class="rounded-full px-2 py-0.5 font-semibold {{ $r->status === 'COMPLETED' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }}">
                            {{ $r->status }}
                        </span>
                    </div>
                    @empty
                    <div class="py-6 text-center text-xs text-slate-500">No service requests logged for this room.</div>
                    @endforelse
                </div>
            </div>

            <!-- Recent In-Room Dining Orders for this Room -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                    <h2 class="font-bold text-white text-base">Dining Orders History</h2>
                    <span class="text-xs text-slate-400 font-mono">{{ $orders->count() }} records</span>
                </div>

                <div class="mt-4 divide-y divide-slate-800/60">
                    @forelse($orders as $ord)
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-white">{{ $ord->order_number }} · ₹{{ number_format((float)$ord->total, 2) }}</span>
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $ord->items->count() }} items · {{ $ord->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="rounded-full px-2 py-0.5 font-semibold {{ $ord->status === 'DELIVERED' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-orange-500/20 text-orange-300' }}">
                            {{ $ord->status }}
                        </span>
                    </div>
                    @empty
                    <div class="py-6 text-center text-xs text-slate-500">No dining orders placed from this room.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Permanent QR Code & Physical Tent Card -->
        <aside class="space-y-6">
            <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl text-center">
                <span class="rounded bg-amber-500/10 px-2 py-0.5 text-xs font-mono font-medium text-amber-400 border border-amber-500/20">Permanent Identity</span>
                <h3 class="mt-2 font-bold text-white text-lg">Room {{ $room->number }} QR Code</h3>
                <p class="mt-1 text-xs text-slate-400">Fixed token linked permanently to this guestroom.</p>

                @if($room->qrCode)
                <div class="mt-5 rounded-2xl bg-white p-6 shadow-inner mx-auto w-56 h-56 flex items-center justify-center">
                    <img src="{{ route('admin.qr.svg', $room->qrCode) }}" class="w-full h-full" alt="Room {{ $room->number }} QR">
                </div>

                <div class="mt-4 p-3 rounded-xl bg-slate-950/60 border border-slate-800 text-[11px] text-slate-400 font-mono break-all">
                    {{ url('/' . config('hospitality.qr_public_prefix', 'g') . '/' . $room->qrCode->public_token) }}
                </div>

                <div class="mt-5 flex gap-2">
                    <a href="{{ route('admin.qr.svg', $room->qrCode) }}" download="qr-room-{{ $room->number }}.svg" class="flex-1 rounded-xl bg-slate-800 px-4 py-2.5 text-xs font-semibold text-white hover:bg-slate-700 transition">
                        Download SVG
                    </a>
                    <a href="{{ route('admin.qr-center.print-sheet', ['room_id' => $room->id]) }}" target="_blank" class="flex-1 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2.5 text-xs font-bold text-slate-950 hover:brightness-110 transition">
                        Print Tent Card
                    </a>
                </div>
                @else
                <p class="mt-4 text-xs text-rose-400">QR code missing. Please re-save room.</p>
                @endif
            </div>

            <!-- Guest Sessions Audit -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
                <h3 class="font-bold text-white text-sm">Recent Guest Sessions</h3>
                <div class="mt-3 divide-y divide-slate-800/60">
                    @forelse($sessions as $sess)
                    <div class="py-2.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-slate-300 text-[11px]">{{ substr($sess->public_id, 0, 16) }}...</span>
                            <span class="rounded px-1.5 py-0.2 text-[10px] {{ $sess->status === 'active' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-slate-800 text-slate-400' }}">{{ $sess->status }}</span>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-0.5">Expires: {{ $sess->expires_at->diffForHumans() }}</p>
                    </div>
                    @empty
                    <p class="text-xs text-slate-500 py-3">No active guest sessions recorded.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
