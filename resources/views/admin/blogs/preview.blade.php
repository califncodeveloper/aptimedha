<x-layouts.app>
    <x-slot:title>[Admin Preview] {{ $blog->meta_title ?: $blog->title }} - AptiMedha</x-slot:title>
    <x-slot:noindex>true</x-slot:noindex>

    {{-- Admin Preview Banner --}}
    <div class="bg-amber-500 text-amber-950 px-4 py-3 sticky top-0 z-50 shadow-md">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-xs font-bold">
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded bg-black/20 uppercase tracking-wider text-[10px]">Admin Preview Mode</span>
                <span>Status: <span class="capitalize underline">{{ $blog->status }}</span></span>
                @if(!$blog->isPublished())
                    <span class="text-amber-900 font-normal">(Hidden from public visitors)</span>
                @endif
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.blogs.edit', $blog) }}" class="px-3 py-1 bg-white rounded-lg text-[#153B70] hover:bg-gray-100 transition-colors">
                    Return to Editor
                </a>
                <a href="{{ route('admin.blogs.index') }}" class="text-amber-950 hover:underline">
                    All Articles
                </a>
            </div>
        </div>
    </div>

    {{-- Breadcrumb Navigation --}}
    <div class="bg-white border-b border-gray-200/80 py-4">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-gray-400">
                <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <a href="{{ route('blog.index') }}" class="hover:text-[#153B70] transition-colors">Blog</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-[#087F82] truncate max-w-xs">{{ $blog->title }}</span>
            </nav>
        </div>
    </div>

    {{-- Main Article Container --}}
    <article class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <header class="space-y-4 mb-8">
            @if($blog->category)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#087F82]/10 text-[#087F82]">
                    {{ $blog->category->name }}
                </span>
            @endif

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#153B70] tracking-tight leading-tight">
                {{ $blog->title }}
            </h1>

            @if($blog->excerpt)
                <p class="text-base sm:text-lg text-gray-600 leading-relaxed font-medium">
                    {{ $blog->excerpt }}
                </p>
            @endif

            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 pt-3 border-t border-gray-100">
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded-full bg-[#153B70] text-white flex items-center justify-center font-bold text-xs">
                        {{ substr($blog->author_display_name, 0, 1) }}
                    </div>
                    <span class="font-bold text-gray-700">{{ $blog->author_display_name }}</span>
                </div>
                <span>•</span>
                <span>{{ $blog->published_at ? $blog->published_at->format('F d, Y') : 'Unpublished draft' }}</span>
                <span>•</span>
                <span>{{ $blog->read_time }}</span>
                <span>•</span>
                <span>{{ number_format($blog->views_count) }} views</span>
            </div>
        </header>

        @if($blog->featured_image_url)
            <div class="mb-10 rounded-2xl overflow-hidden border border-gray-200 shadow-xs">
                <img src="{{ $blog->featured_image_url }}" alt="{{ $blog->featured_image_alt ?: $blog->title }}" class="w-full max-h-[460px] object-cover">
            </div>
        @endif

        {{-- Article Content --}}
        <div class="prose max-w-none text-[#243247] leading-relaxed space-y-5 bg-white p-6 sm:p-10 rounded-2xl border border-gray-200/90 shadow-2xs">
            {!! $blog->body !!}
        </div>
    </article>
</x-layouts.app>

