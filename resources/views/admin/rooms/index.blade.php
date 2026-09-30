@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800/80 pb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-purple-500/10 px-2 py-0.5 text-xs font-mono font-medium text-purple-400 border border-purple-500/20">Room Inventory</span>
                <span class="text-xs text-slate-500">· Digital Key & Physical Mapping</span>
            </div>
            <h1 class="mt-2 text-2xl lg:text-3xl font-bold tracking-tight text-white">Guestrooms & Suites</h1>
            <p class="mt-1 text-sm text-slate-400">Manage room occupancy status, inspect active guest sessions, and view permanent QR identities.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.qr-center.print-sheet') }}" target="_blank" class="flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-800 px-4 py-2.5 text-xs font-semibold text-slate-200 hover:bg-slate-700 transition">
                <x-icon name="printer" class="w-4 h-4 text-emerald-400" />
                <span>Print All Tent Cards</span>
            </a>
        </div>
    </div>

    <!-- Main Grid: Rooms Catalog & Create Room Form -->
    <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
        <!-- Left: Rooms Grid -->
        <div class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                @forelse($rooms as $room)
                <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-xl transition hover:border-slate-700/80">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xl font-bold text-white tracking-tight">Room {{ $room->number }}</span>
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $room->status === 'available' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : ($room->status === 'occupied' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30') }}">
                                    {{ ucfirst($room->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1 font-medium">{{ $room->name ? $room->name . ' · ' : '' }}{{ $room->roomType?->name ?? 'Standard Room' }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">{{ $room->floor?->name ?? 'Level 1' }} · Rate: ₹{{ number_format((float)($room->roomType?->base_rate ?? 0), 2) }}/night</p>
                        </div>

                        <!-- Mini QR preview badge -->
                        @if($room->qrCode)
                        <a href="{{ route('admin.qr.svg', $room->qrCode) }}" target="_blank" class="rounded-xl border border-slate-700 bg-white p-1 hover:scale-105 transition" title="View Vector QR">
                            <img src="{{ route('admin.qr.svg', $room->qrCode) }}" class="h-10 w-10" alt="QR">
                        </a>
                        @endif
                    </div>

                    <!-- Room Status Switcher & Link -->
                    <div class="mt-5 flex items-center justify-between border-t border-slate-800/80 pt-4 text-xs">
                        <form method="post" action="{{ route('admin.rooms.status', $room) }}" class="flex items-center gap-1.5">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-800 bg-slate-950 px-2.5 py-1 text-[11px] font-medium text-slate-300 focus:outline-none">
                                <option value="available" {{ $room->status === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="occupied" {{ $room->status === 'occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="maintenance" {{ $room->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </form>

                        <a href="{{ route('admin.rooms.show', $room) }}" class="font-semibold text-amber-400 hover:text-amber-300 transition">
                            Inspect Room →
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-2 rounded-3xl border border-dashed border-slate-800 p-12 text-center text-slate-500">
                    No guestrooms configured yet.
                </div>
                @endforelse
            </div>

            <div>{{ $rooms->links() }}</div>
        </div>

        <!-- Right: Create Room Card -->
        <aside class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl h-fit">
            <h2 class="font-bold text-white text-base">Add New Room</h2>
            <p class="text-xs text-slate-400 mt-1">Automatically generates a permanent vector QR token upon creation.</p>

            <form class="mt-5 space-y-4" method="post" action="{{ route('admin.rooms.store') }}">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Room Number</label>
                    <input class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none" name="number" placeholder="E.g., 301" required>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Display Name (Optional)</label>
                    <input class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none" name="name" placeholder="E.g., Sunrise Ocean Suite">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Room Category</label>
                    <select class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-2.5 text-xs text-white focus:border-amber-500 focus:outline-none" name="room_type_id" required>
                        <option value="">Select category...</option>
                        @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }} (₹{{ number_format((float)$type->base_rate, 2) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Floor Level</label>
                    <select class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-2.5 text-xs text-white focus:border-amber-500 focus:outline-none" name="floor_id">
                        <option value="">No floor assigned</option>
                        @foreach($floors as $floor)
                        <option value="{{ $floor->id }}">{{ $floor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button class="w-full rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 py-3 text-xs font-bold text-slate-950 shadow-lg shadow-amber-950/40 hover:brightness-110 transition">
                    Create Room & Generate QR
                </button>
            </form>
        </aside>
    </div>
</div>
@endsection
