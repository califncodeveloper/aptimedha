<x-layouts.app>
    <x-slot:title>{{ \App\Services\SeoService::blogTitle($post) }}</x-slot:title>
    <x-slot:metaDescription>{{ \App\Services\SeoService::blogDescription($post) }}</x-slot:metaDescription>
    <x-slot:schema>{!! \App\Services\SchemaService::toJsonLd(\App\Services\SchemaService::forBlog($post)) !!}</x-slot:schema>
    <x-slot:meta>
        <meta property="og:title" content="{{ $post->meta_title ?: $post->title }}">
        <meta property="og:description" content="{{ $post->meta_description ?: $post->excerpt }}">
        <meta property="og:type" content="article">
        <meta property="og:url" content="{{ route('blog.show', $post->slug) }}">
        @if($post->featured_image_url)
            <meta property="og:image" content="{{ $post->featured_image_url }}">
            <meta name="twitter:image" content="{{ $post->featured_image_url }}">
        @endif
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $post->meta_title ?: $post->title }}">
        <meta name="twitter:description" content="{{ $post->meta_description ?: $post->excerpt }}">
        @if($post->published_at)
            <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
        @endif
        @if($post->category)
            <meta property="article:section" content="{{ $post->category->name }}">
        @endif
    </x-slot:meta>

    {{-- Admin Preview Banner for Draft Posts --}}
    @if(isset($isAdmin) && $isAdmin && !$post->isPublished())
        <div class="bg-amber-500 text-amber-950 px-4 py-2.5 text-xs font-bold text-center">
            ⚠️ Admin Preview Mode: This post has status "{{ $post->status }}" and is hidden from public visitors.
            <a href="{{ route('admin.blogs.edit', $post) }}" class="underline ml-2">Edit in Admin</a>
        </div>
    @endif

    {{-- Header Section --}}
    <div class="bg-white border-b border-gray-200/80 py-10 sm:py-14">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <nav class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">
                <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <a href="{{ route('blog.index') }}" class="hover:text-[#153B70] transition-colors">Blog</a>
                @if($post->category)
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="hover:text-[#087F82] transition-colors">
                        {{ $post->category->name }}
                    </a>
                @endif
            </nav>

            @if($post->category)
                <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-[#087F82]/10 text-[#087F82] mb-3 hover:bg-[#087F82]/20 transition-colors">
                    {{ $post->category->name }}
                </a>
            @endif

            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#153B70] tracking-tight leading-tight break-words">
                {{ $post->title }}
            </h1>

            @if($post->excerpt)
                <p class="mt-3 text-sm sm:text-base text-gray-600 leading-relaxed font-medium">
                    {{ $post->excerpt }}
                </p>
            @endif

            <div class="mt-6 flex flex-wrap items-center gap-3 sm:gap-4 text-xs sm:text-sm text-gray-500 font-medium pt-4 border-t border-gray-100">
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded-full bg-[#153B70] text-white flex items-center justify-center font-bold text-xs">
                        {{ substr($post->author_display_name, 0, 1) }}
                    </div>
                    <span class="font-bold text-gray-700">{{ $post->author_display_name }}</span>
                </div>
                <span>&bull;</span>
                <span class="flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-4 h-4 text-[#087F82]"></i>
                    {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Draft' }}
                </span>
                <span>&bull;</span>
                <span class="flex items-center gap-1.5">
                    <i data-lucide="clock" class="w-4 h-4 text-[#087F82]"></i>
                    {{ $post->read_time }}
                </span>
                <span>&bull;</span>
                <span class="flex items-center gap-1.5">
                    <i data-lucide="eye" class="w-4 h-4 text-[#087F82]"></i>
                    {{ number_format($post->views_count) }} views
                </span>
            </div>
        </div>
    </div>

    {{-- Main Article Content Container --}}
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        @if($post->featured_image_url)
            <div class="mb-10 rounded-2xl overflow-hidden border border-gray-200 shadow-xs">
                <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?: $post->title }}" class="w-full max-h-[460px] object-cover">
            </div>
        @endif

        {{-- Rich-Text Content --}}
        <article class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs p-4 sm:p-10 text-[#243247] leading-relaxed space-y-5 text-sm sm:text-base overflow-hidden break-words">
            <div class="prose max-w-none text-[#243247] prose-headings:text-[#153B70] prose-headings:font-black prose-h2:text-2xl prose-h3:text-xl prose-a:text-[#087F82] prose-a:font-semibold prose-strong:text-[#153B70] prose-code:bg-gray-100 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:text-[#153B70] prose-blockquote:border-l-4 prose-blockquote:border-[#087F82] prose-blockquote:bg-gray-50 prose-blockquote:p-4 prose-blockquote:rounded-r-lg">
                {!! $post->body !!}
            </div>
        </article>

        {{-- Social Sharing & Navigation --}}
        <div class="mt-8 bg-white rounded-2xl border border-gray-200/90 shadow-2xs p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-2 text-xs font-bold text-gray-500 uppercase tracking-wider">
                <i data-lucide="share-2" class="w-4 h-4 text-[#087F82]"></i>
                <span>Share this article:</span>
            </div>

            @php
                $shareUrl = urlencode(route('blog.show', $post->slug));
                $shareTitle = urlencode($post->title);
            @endphp

            <div class="flex items-center space-x-2" x-data="{ copied: false }">
                <a href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ $shareUrl }}" target="_blank" rel="noopener noreferrer"
                   class="p-2 rounded-xl border border-gray-200 hover:border-[#153B70] hover:text-[#153B70] transition-colors" title="Share on Twitter / X">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener noreferrer"
                   class="p-2 rounded-xl border border-gray-200 hover:border-[#087F82] hover:text-[#087F82] transition-colors" title="Share on LinkedIn">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                </a>
                <a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener noreferrer"
                   class="p-2 rounded-xl border border-gray-200 hover:border-emerald-600 hover:text-emerald-600 transition-colors" title="Share on WhatsApp">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                </a>
                <button type="button"
                        @click="navigator.clipboard.writeText('{{ route('blog.show', $post->slug) }}'); copied = true; setTimeout(() => copied = false, 2500)"
                        class="px-3 py-1.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:border-[#153B70] transition-colors flex items-center space-x-1.5">
                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                    <span x-text="copied ? 'Copied!' : 'Copy Link'"></span>
                </button>
            </div>
        </div>

        {{-- Practice MCQs Callout Banner --}}
        <div class="mt-8 p-6 sm:p-8 bg-gradient-to-r from-[#153B70] to-[#087F82] rounded-2xl text-white shadow-xs flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="space-y-1 text-center sm:text-left">
                <h4 class="text-base sm:text-lg font-black text-white">Practise What You Learned</h4>
                <p class="text-xs sm:text-sm text-white/85 max-w-md">Solve real campus placement questions across Arithmetic, Reasoning, Verbal, and Programming topics.</p>
            </div>
            <a href="{{ route('mcq.index') }}" class="px-6 py-3 bg-white hover:bg-gray-100 text-[#153B70] text-xs font-bold rounded-xl transition-all shadow-xs whitespace-nowrap">
                Start MCQ Practice &rarr;
            </a>
        </div>

        {{-- Related Articles Section --}}
        @if($relatedPosts->count() > 0)
            <div class="mt-14 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-200 pb-3">
                    <h3 class="text-lg sm:text-xl font-bold text-[#153B70]">Related Articles & Guides</h3>
                    <a href="{{ route('blog.index') }}" class="text-xs font-bold text-[#087F82] hover:underline">
                        View All Articles &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedPosts as $rel)
                        <a href="{{ route('blog.show', $rel->slug) }}" class="p-5 bg-white rounded-2xl border border-gray-200/90 shadow-2xs hover:border-[#087F82] transition-all group flex flex-col justify-between">
                            <div>
                                @if($rel->category)
                                    <span class="text-[11px] font-bold text-[#087F82] uppercase">{{ $rel->category->name }}</span>
                                @endif
                                <h4 class="text-sm font-bold text-[#243247] group-hover:text-[#153B70] transition-colors mt-1.5 line-clamp-2">
                                    {{ $rel->title }}
                                </h4>
                                @if($rel->excerpt)
                                    <p class="text-xs text-gray-500 mt-2 line-clamp-2">
                                        {{ $rel->excerpt }}
                                    </p>
                                @endif
                            </div>
                            <div class="pt-4 mt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                                <span>{{ $rel->published_at ? $rel->published_at->format('M d, Y') : '' }}</span>
                                <span class="font-bold text-[#087F82] group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Back to Blog Navigation --}}
        <div class="mt-12 text-center">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center text-xs font-bold text-[#153B70] hover:text-[#087F82] transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i> Back to All Articles
            </a>
        </div>
    </div>
</x-layouts.app>
