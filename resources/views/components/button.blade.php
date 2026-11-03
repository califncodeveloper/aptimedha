@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';

$variants = [
    'primary' => 'bg-[#153B70] text-white hover:bg-[#0e2a52] focus:ring-[#153B70]',
    'secondary' => 'bg-[#087F82] text-white hover:bg-[#066668] focus:ring-[#087F82]',
    'outline' => 'border-2 border-[#153B70] text-[#153B70] hover:bg-[#153B70] hover:text-white focus:ring-[#153B70]',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    'ghost' => 'text-[#243247] hover:bg-gray-100 focus:ring-gray-300',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
];

$classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
