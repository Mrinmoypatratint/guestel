@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800/80 pb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-indigo-500/10 px-2 py-0.5 text-xs font-mono font-medium text-indigo-400 border border-indigo-500/20">Media Storage</span>
                <span class="text-xs text-slate-500">· Secure Tenant Isolation</span>
            </div>
            <h1 class="mt-2 text-2xl lg:text-3xl font-bold tracking-tight text-white">Hotel Gallery & Visual Assets</h1>
            <p class="mt-1 text-sm text-slate-400">Upload high-resolution photography for guest mobile hero banners, room views, and facilities.</p>
        </div>
    </div>

    <!-- Main Grid: Gallery & Upload Form -->
    <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
        <!-- Left: Image Cards Grid -->
        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 lg:p-8 backdrop-blur-xl">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-4 mb-6">
                <h2 class="font-bold text-white text-base">Published Visuals</h2>
                <span class="text-xs text-slate-400 font-mono">{{ $images->count() }} assets</span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($images as $image)
                <article class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-950/70 transition hover:border-slate-700">
                    <div class="relative aspect-[4/3] w-full bg-slate-900">
                        <img class="h-full w-full object-cover" src="{{ Storage::disk('public')->url($image->path) }}" alt="{{ $image->alt_text ?: 'Hotel image' }}">
                        @if($image->is_cover)
                        <span class="absolute top-2 left-2 rounded-full bg-amber-500 px-2.5 py-0.5 text-[10px] font-bold text-slate-950 shadow-md">
                            Cover Image
                        </span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between p-4 text-xs">
                        <div>
                            <span class="font-bold text-white">{{ $image->category ?: 'General Gallery' }}</span>
                            <p class="text-[11px] text-slate-500 truncate max-w-[140px]">{{ $image->alt_text ?: 'No description' }}</p>
                        </div>
                        <form method="post" action="{{ route('admin.gallery.destroy', $image) }}">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-800 hover:text-rose-400 transition" title="Delete image">
                                <x-icon name="x" class="w-4 h-4" />
                            </button>
                        </form>
                    </div>
                </article>
                @empty
                <div class="col-span-3 py-16 text-center text-xs text-slate-500">
                    <x-icon name="gallery" class="w-8 h-8 mx-auto text-slate-600 mb-2" />
                    No hotel photography uploaded yet. Use the upload panel on the right.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Upload Panel -->
        <aside class="rounded-3xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl h-fit">
            <h3 class="font-bold text-white text-base">Upload Secure Asset</h3>
            <p class="text-xs text-slate-400 mt-1">Images are validated by MIME type, renamed with UUIDs, and isolated per hotel.</p>

            <form method="post" enctype="multipart/form-data" action="{{ route('admin.gallery.store') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Image File (JPEG, PNG, WebP)</label>
                    <input class="w-full text-xs text-slate-400 file:mr-3 file:rounded-xl file:border-0 file:bg-slate-800 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-slate-200 hover:file:bg-slate-700" type="file" name="image" accept="image/jpeg,image/png,image/webp" required>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Category</label>
                    <input class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none" name="category" placeholder="E.g., Exterior, Pool, Suite...">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Accessible Alt Description</label>
                    <input class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none" name="alt_text" placeholder="E.g., Panoramic sunset view over infinity pool...">
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <label class="flex items-center gap-2.5 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_cover" value="1" class="rounded border-slate-700 bg-slate-900 text-amber-500 focus:ring-0">
                        <span>Set as primary guest portal cover image</span>
                    </label>
                </div>

                <button class="w-full rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 py-3 text-xs font-bold text-slate-950 hover:brightness-110 transition shadow">
                    Upload Image Securely
                </button>
            </form>
        </aside>
    </div>
</div>
@endsection
