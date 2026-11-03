<x-layouts.app>
    <x-slot:title>{{ $post->title }} - AptiMedha</x-slot:title>

    <div class="bg-white border-b border-gray-200/80 py-10 sm:py-14">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">
                <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <a href="{{ route('blog.index') }}" class="hover:text-[#153B70] transition-colors">Blog</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-[#087F82]">{{ $post->category ?? 'Article' }}</span>
            </nav>

            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-[#087F82] mb-3">
                {{ $post->category ?? 'Guidance' }}
            </span>

            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#153B70] tracking-tight leading-tight">
                {{ $post->title }}
            </h1>

            <div class="mt-4 flex flex-wrap items-center gap-4 text-xs sm:text-sm text-gray-500 font-medium">
                <span class="flex items-center gap-1.5"><i data-lucide="user" class="w-4 h-4 text-[#087F82]"></i> {{ $post->author }}</span>
                <span>&bull;</span>
                <span class="flex items-center gap-1.5"><i data-lucide="calendar" class="w-4 h-4 text-[#087F82]"></i> {{ $post->date }}</span>
                <span>&bull;</span>
                <span class="flex items-center gap-1.5"><i data-lucide="clock" class="w-4 h-4 text-[#087F82]"></i> {{ $post->read_time }}</span>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-12">
        <article class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-10 prose prose-sm sm:prose-base max-w-none text-[#243247] prose-headings:text-[#153B70] prose-a:text-[#087F82]">
            {!! $post->body !!}
        </article>

        {{-- Author Bio & Callout --}}
        <div class="mt-10 p-6 bg-[#F7F9FC] rounded-2xl border border-gray-200 flex items-center justify-between gap-6">
            <div>
                <h4 class="text-sm font-bold text-[#153B70]">Found this guide helpful?</h4>
                <p class="text-xs text-gray-500 mt-1">Start practising topic-wise MCQs right now with verified step-by-step solutions.</p>
            </div>
            <a href="{{ route('mcq.index') }}" class="px-5 py-2.5 bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold rounded-xl transition-colors shadow-xs whitespace-nowrap">
                Practice MCQs Now &rarr;
            </a>
        </div>

        {{-- Related Articles --}}
        @if(!empty($related))
            <div class="mt-14">
                <h3 class="text-xl font-bold text-[#153B70] mb-6">Related Preparation Guides</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($related as $rel)
                        <a href="{{ route('blog.show', $rel->slug) }}" class="p-5 bg-white rounded-xl border border-gray-200 hover:border-[#087F82] hover:shadow-md transition-all group">
                            <span class="text-[11px] font-bold text-[#087F82] uppercase">{{ $rel->category }}</span>
                            <h4 class="text-sm font-bold text-[#243247] group-hover:text-[#153B70] transition-colors mt-1 line-clamp-2">{{ $rel->title }}</h4>
                            <p class="text-xs text-gray-400 mt-2">{{ $rel->date }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
