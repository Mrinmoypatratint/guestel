@props([
    'size' => 'md',
    'showWordmark' => true,
    'tagline' => 'Hospitality Cloud OS',
    'href' => route('landing'),
    'theme' => 'light',
    'iconOnly' => false,
])

@php
    $iconSizes = [
        'xs' => 'h-5 w-5',
        'sm' => 'h-7 w-7',
        'md' => 'h-9 w-9',
        'lg' => 'h-11 w-11',
        'xl' => 'h-14 w-14',
    ];
    $textSizes = [
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-xl',
        'xl' => 'text-2xl',
    ];
    $iconClass = $iconSizes[$size] ?? 'h-9 w-9';
    $textClass = $textSizes[$size] ?? 'text-base';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 group select-none']) }}>
    <div class="flex items-center justify-center p-1 rounded-xl neu-button group-hover:scale-105 transition bg-[#e8edf5]">
        <img src="{{ asset('images/guestel-icon.png') }}" alt="Guestel Logo" class="{{ $iconClass }} object-contain" />
    </div>
    @if($showWordmark && !$iconOnly)
        <div class="leading-none text-left">
            <span class="block {{ $textClass }} font-black tracking-tight">
                <span class="{{ $theme === 'dark' ? 'text-white' : 'text-[#00214D]' }}">Gues</span><span class="text-[#0073E6]">tel</span>
            </span>
            @if($tagline)
                <span class="text-[9px] font-mono uppercase tracking-widest {{ $theme === 'dark' ? 'text-slate-400' : 'text-slate-500' }} font-semibold block mt-0.5">
                    {{ $tagline }}
                </span>
            @endif
        </div>
    @endif
</a>
