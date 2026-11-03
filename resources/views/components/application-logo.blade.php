@props([
    'variant' => 'default', // 'default' (navy text), 'light' (white text), 'icon-only'
    'size' => 'md', // 'sm', 'md', 'lg'
])

@php
$sizeClasses = [
    'sm' => ['icon' => 'w-7 h-7', 'text' => 'text-lg'],
    'md' => ['icon' => 'w-9 h-9', 'text' => 'text-xl'],
    'lg' => ['icon' => 'w-11 h-11', 'text' => 'text-2xl'],
];
$currentSize = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 tracking-tight font-bold']) }}>
    {{-- Open book with checkmark icon matching client mockup --}}
    <div class="relative flex-shrink-0 {{ $currentSize['icon'] }} flex items-center justify-center">
        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full drop-shadow-sm">
            {{-- Open Book Pages in Primary Navy --}}
            <path d="M6 14C11 12 18 12 24 16C30 12 37 12 42 14V36C37 34 30 34 24 38C18 34 11 34 6 36V14Z" 
                  fill="{{ $variant === 'light' ? '#ffffff' : '#153B70' }}" 
                  stroke="{{ $variant === 'light' ? '#ffffff' : '#153B70' }}" 
                  stroke-width="2" 
                  stroke-linecap="round" 
                  stroke-linejoin="round"/>
            {{-- Book Spine / Center Divider --}}
            <path d="M24 16V38" stroke="{{ $variant === 'light' ? '#153B70' : '#ffffff' }}" stroke-width="2" stroke-linecap="round"/>
            {{-- Vibrant Teal Checkmark overlay --}}
            <path d="M17 24L22 29L32 18" 
                  stroke="#087F82" 
                  stroke-width="3.5" 
                  stroke-linecap="round" 
                  stroke-linejoin="round"/>
        </svg>
    </div>

    @if($variant !== 'icon-only')
        <span class="{{ $currentSize['text'] }} font-extrabold {{ $variant === 'light' ? 'text-white' : 'text-[#153B70]' }} leading-none">
            Apti<span class="text-[#087F82]">Medha</span><span class="text-xs font-semibold opacity-75">.com</span>
        </span>
    @endif
</div>

