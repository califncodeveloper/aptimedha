@props([
    'title' => '',
    'value' => '0',
    'icon' => 'bar-chart',
    'trend' => null,
    'color' => 'primary',
])

@php
$colors = [
    'primary' => 'bg-[#153B70]/10 text-[#153B70]',
    'secondary' => 'bg-[#087F82]/10 text-[#087F82]',
    'success' => 'bg-green-100 text-green-700',
    'warning' => 'bg-yellow-100 text-yellow-700',
];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
            <p class="mt-1 text-2xl font-bold text-[#243247]">{{ $value }}</p>
            @if($trend)
                <p class="mt-1 text-sm text-green-600">{{ $trend }}</p>
            @endif
        </div>
        <div class="flex-shrink-0 rounded-lg p-3 {{ $colors[$color] ?? $colors['primary'] }}">
            <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
        </div>
    </div>
</div>
