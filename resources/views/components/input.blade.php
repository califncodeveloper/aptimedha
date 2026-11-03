@props([
    'label' => null,
    'name' => '',
    'type' => 'text',
    'error' => null,
])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-[#243247] mb-1">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge(['class' => 'w-full rounded-lg border border-gray-300 px-4 py-2.5 text-[#243247] placeholder-gray-400 focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none transition-colors' . ($error ? ' border-red-500' : '')]) }}
    >
    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
