@props([
    'variant' => 'default', // 'default' (on light bg), 'light' (on dark navy bg), 'on-dark'
    'size' => 'md', // 'sm', 'md', 'lg', 'xl'
])

@php
$sizeClasses = [
    'sm' => 'h-7',
    'md' => 'h-9',
    'lg' => 'h-11',
    'xl' => 'h-14',
];
$heightClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

@if($variant === 'light' || $variant === 'on-dark')
    <div {{ $attributes->merge(['class' => 'inline-flex items-center bg-white px-2.5 py-1 rounded-xl shadow-xs transition-opacity hover:opacity-95']) }}>
        <img src="{{ asset('images/aptimedha-logo.png') }}"
             alt="AptiMedha.com"
             width="1024"
             height="341"
             class="{{ $heightClass }} w-auto object-contain select-none"
             loading="eager" />
    </div>
@else
    <div {{ $attributes->merge(['class' => 'inline-flex items-center']) }}>
        <img src="{{ asset('images/aptimedha-logo.png') }}"
             alt="AptiMedha.com"
             width="1024"
             height="341"
             class="{{ $heightClass }} w-auto object-contain select-none"
             loading="eager" />
    </div>
@endif
