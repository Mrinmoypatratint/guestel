@extends('layouts.app')

@section('content')
<div x-data="{ previewModal: false, activeQr: null, copyToast: false }">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800/80 pb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-amber-500/10 px-2 py-0.5 text-xs font-mono font-medium text-amber-400 border border-amber-500/20">Permanent Identity Engine</span>
                <span class="text-xs text-slate-500">· Zero-replacement QR Architecture</span>
            </div>
            <h1 class="mt-2 text-2xl lg:text-3xl font-bold tracking-tight text-white">QR Code Management & Print Center</h1>
            <p class="mt-1 text-sm text-slate-400">All room and hotel QR tokens are immutable. Changes to room names, types, or services update dynamically without reprinting.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.qr-center.print-sheet') }}" target="_blank" class="flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2.5 text-sm font-semibold text-slate-950 shadow-lg shadow-amber-950/50 hover:brightness-110 transition">
                <x-icon name="printer" class="w-4 h-4" />
                <span>Print All Tent Cards</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-xl">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Generated</p>
            <p class="mt-2 text-3xl font-bold text-white">{{ $totalQrs }}</p>
            <p class="mt-1 text-xs text-slate-500">Immutable QR tokens</p>
        </div>
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-xl">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Active & Serving</p>
            <p class="mt-2 text-3xl font-bold text-emerald-400">{{ $activeQrs }}</p>
            <p class="mt-1 text-xs text-slate-500">Live for guest scanning</p>
        </div>
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-xl">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Scans Recorded</p>
            <p class="mt-2 text-3xl font-bold text-amber-300">{{ $qrCodes->sum('scans_count') }}</p>
            <p class="mt-1 text-xs text-slate-500">Privacy-preserving count</p>
        </div>
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-xl">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Output Format</p>
            <p class="mt-2 text-3xl font-bold text-blue-400">Vector SVG</p>
            <p class="mt-1 text-xs text-slate-500">Infinite print resolution</p>
        </div>
    </div>

    <!-- QR Catalog Grid -->
    <div class="mt-8 rounded-3xl border border-slate-800/80 bg-slate-900/60 backdrop-blur-xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-800/80 px-6 py-4">
            <h2 class="font-semibold text-white">Active QR Inventory</h2>
            <span class="text-xs text-slate-400">Showing {{ $qrCodes->count() }} of {{ $qrCodes->total() }}</span>
        </div>

        <div class="divide-y divide-slate-800/60">
            @forelse($qrCodes as $qr)
            @php
                $isRoom = $qr->qrable instanceof \App\Models\Room;
                $publicUrl = url('/' . config('hospitality.qr_public_prefix', 'g') . '/' . $qr->public_token);
                $svgUrl = route('admin.qr.svg', $qr);
            @endphp
            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 gap-4 hover:bg-slate-800/30 transition">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="relative flex-shrink-0 cursor-pointer rounded-xl border border-slate-800 bg-white p-1.5 transition hover:scale-105"
                         @click="activeQr = { label: '{{ $qr->label ?? ($isRoom ? 'Room ' . $qr->qrable->number : 'General QR') }}', svg: '{{ $svgUrl }}', url: '{{ $publicUrl }}', token: '{{ $qr->public_token }}' }; previewModal = true">
                        <img src="{{ $svgUrl }}" class="h-14 w-14" alt="QR Code">
                        <span class="absolute inset-0 flex items-center justify-center bg-black/40 rounded-xl opacity-0 hover:opacity-100 transition text-[10px] text-white font-medium">Zoom</span>
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-white text-base truncate">{{ $qr->label ?? ($isRoom ? 'Room ' . $qr->qrable->number : 'Hotel QR') }}</span>
                            @if($isRoom)
                            <span class="rounded bg-slate-800 px-2 py-0.5 text-xs text-slate-300">{{ $qr->qrable->roomType?->name ?? 'Standard Room' }}</span>
                            @else
                            <span class="rounded bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 text-xs text-amber-300">Property General</span>
                            @endif
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $qr->is_active ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                {{ $qr->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </div>
                        <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400">
                            <span>Token: <code class="font-mono text-slate-300">{{ substr($qr->public_token, 0, 14) }}...</code></span>
                            <span>Total Scans: <strong class="text-white">{{ $qr->scans_count }}</strong></span>
                            <span>Last scanned: {{ $qr->last_scanned_at ? $qr->last_scanned_at->diffForHumans() : 'Never' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <button @click="navigator.clipboard.writeText('{{ $publicUrl }}'); copyToast = true; setTimeout(() => copyToast = false, 2000)" class="rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-xs font-medium text-slate-200 hover:bg-slate-700 transition" title="Copy public scan URL">
                        Copy Link
                    </button>

                    <a href="{{ $svgUrl }}" download="qr-{{ $isRoom ? 'room-' . $qr->qrable->number : 'hotel' }}.svg" class="flex items-center gap-1.5 rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-xs font-medium text-slate-200 hover:bg-slate-700 transition" title="Download SVG">
                        <x-icon name="download" class="w-3.5 h-3.5" />
                        <span>SVG</span>
                    </a>

                    @if($isRoom)
                    <a href="{{ route('admin.qr-center.print-sheet', ['room_id' => $qr->qrable->id]) }}" target="_blank" class="flex items-center gap-1.5 rounded-xl border border-amber-500/40 bg-amber-500/10 px-3 py-2 text-xs font-medium text-amber-300 hover:bg-amber-500/20 transition" title="Print Tent Card">
                        <x-icon name="printer" class="w-3.5 h-3.5" />
                        <span>Print</span>
                    </a>
                    @endif

                    <form method="post" action="{{ route('admin.qr-center.toggle', $qr) }}">
                        @csrf
                        <button type="submit" class="rounded-xl p-2 text-xs font-medium transition {{ $qr->is_active ? 'text-slate-400 hover:text-rose-400' : 'text-emerald-400 hover:text-emerald-300' }}" title="{{ $qr->is_active ? 'Deactivate QR' : 'Activate QR' }}">
                            {{ $qr->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-slate-500">No QR codes generated yet.</div>
            @endforelse
        </div>

        <div class="border-t border-slate-800/80 px-6 py-4">
            {{ $qrCodes->links() }}
        </div>
    </div>

    <!-- Quick Preview Modal -->
    <div x-show="previewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm">
        <div @click.away="previewModal = false" class="w-full max-w-sm rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-2xl text-center">
            <h3 class="font-bold text-lg text-white" x-text="activeQr?.label"></h3>
            <p class="text-xs text-slate-400 mt-1">Permanent Vector QR Code</p>

            <div class="mt-5 rounded-2xl bg-white p-6 shadow-inner mx-auto w-64 h-64 flex items-center justify-center">
                <img :src="activeQr?.svg" class="w-full h-full" alt="QR Code">
            </div>

            <div class="mt-4 p-3 rounded-xl bg-slate-800/60 border border-slate-700/60 text-xs">
                <p class="text-slate-400 font-mono break-all" x-text="activeQr?.url"></p>
            </div>

            <div class="mt-5 flex gap-2">
                <a :href="activeQr?.svg" download="qr-preview.svg" class="flex-1 rounded-xl bg-slate-800 px-4 py-2.5 text-xs font-semibold text-white hover:bg-slate-700 transition">
                    Download SVG
                </a>
                <button @click="previewModal = false" class="flex-1 rounded-xl bg-slate-700 px-4 py-2.5 text-xs font-semibold text-white hover:bg-slate-600 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Copy Link Notification Toast -->
    <div x-show="copyToast" x-cloak class="fixed bottom-6 right-6 z-50 rounded-2xl border border-emerald-500/40 bg-emerald-950 px-4 py-3 text-xs font-semibold text-emerald-300 shadow-2xl">
        Scan URL copied to clipboard!
    </div>
</div>
@endsection
