@props(['title' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200']) }}>
    @if($title)
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-[#243247]">{{ $title }}</h3>
        </div>
    @endif
    <div @class(['px-6 py-4' => $padding])>
        {{ $slot }}
    </div>
</div>
