<x-layouts.app>
    <x-slot:title>Blog & Career Preparation Articles - AptiMedha</x-slot:title>
    <x-slot:metaDescription>Explore placement preparation roadmaps, speed math techniques, interview frameworks, and aptitude exam patterns on AptiMedha.</x-slot:metaDescription>
    <x-slot:meta>
        <meta property="og:title" content="AptiMedha Blog - Career Insights & Placement Guides">
        <meta property="og:description" content="Master campus placements with structured roadmaps, aptitude shortcuts, and interview strategies.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('blog.index') }}">
    </x-slot:meta>

    {{-- Blog Header with Subtle Grid Pattern --}}
    <div class="bg-gradient-to-b from-white to-[#F7F9FC] border-b border-gray-200/80 py-12 sm:py-16 bg-grid-pattern relative overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <nav class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">
                <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-300"></i>
                <span class="text-[#087F82]">Blog & Placement Guides</span>
            </nav>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-200/70 text-xs font-bold text-[#087F82] mb-3">
                <i data-lucide="book-open" class="w-3.5 h-3.5 shrink-0"></i>
                <span>Aptitude & Placement Insights</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#153B70] tracking-tight">Placement Insights & Aptitude Guides</h1>
            <p class="mt-3 text-base sm:text-lg text-[#243247]/75 max-w-2xl leading-relaxed">
                Practical advice, assessment test patterns, speed math shortcuts, and interview frameworks written for placement aspirants.
            </p>

            {{-- Search & Filter Bar --}}
            <div class="mt-8 max-w-xl">
                <form action="{{ route('blog.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="relative flex-1">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3.5 text-gray-400"></i>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search articles, roadmaps, frameworks..."
                               class="w-full pl-10 pr-4 py-2.5 bg-white text-xs sm:text-sm rounded-xl border border-gray-300 shadow-2xs focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none min-h-[44px]">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 sm:flex-none px-5 py-2.5 bg-[#153B70] hover:bg-[#0e2a52] text-white text-xs sm:text-sm font-bold rounded-xl transition-colors shadow-2xs min-h-[44px]">
                            Search
                        </button>
                        @if(request()->hasAny(['q', 'category']))
                            <a href="{{ route('blog.index') }}" class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center" title="Reset Filters">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Category Filter Pills --}}
            @if($categories->count() > 0)
                <div class="mt-6 flex flex-wrap items-center gap-2">
                    <a href="{{ route('blog.index', array_filter(['q' => request('q')])) }}"
                       @class([
                           'px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors',
                           'bg-[#153B70] text-white shadow-2xs' => !request('category'),
                           'bg-white text-gray-600 border border-gray-200 hover:border-[#153B70]' => request('category'),
                       ])>
                        All Categories
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('blog.index', array_filter(['category' => $cat->slug, 'q' => request('q')])) }}"
                           @class([
                               'px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors',
                               'bg-[#087F82] text-white shadow-2xs' => request('category') === $cat->slug,
                               'bg-white text-gray-600 border border-gray-200 hover:border-[#087F82] hover:text-[#087F82]' => request('category') !== $cat->slug,
                           ])>
                            {{ $cat->name }} ({{ $cat->blogs_count }})
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Blog Listing --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        @if($posts->count() > 0)
            <div data-motion-stagger data-motion-stagger-delay="0.08" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                    <article data-motion-item
                             data-motion-zoom
                             class="image-zoom-card bg-white rounded-2xl border border-gray-200/90 shadow-2xs hover:border-[#087F82] card-hover-effect flex flex-col justify-between overflow-hidden group">
                        <div>
                            {{-- Featured Image with 16:10 Ratio and Clip-Reveal --}}
                            @if($post->featured_image_url)
                                <a href="{{ route('blog.show', $post->slug) }}" data-motion="clip-reveal" class="block aspect-[16/10] overflow-hidden relative group-hover:opacity-95 transition-opacity">
                                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?: $post->title }}" class="w-full h-full object-cover">
                                    @if($post->category)
                                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#153B70]/90 text-white shadow-xs backdrop-blur-xs">
                                            {{ $post->category->name }}
                                        </span>
                                    @endif
                                </a>
                            @else
                                <div data-motion="clip-reveal" class="aspect-[16/10] bg-gradient-to-tr from-[#153B70] via-[#1a4a8a] to-[#087F82] p-5 flex items-end justify-between relative overflow-hidden">
                                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors"></div>
                                    <div class="absolute inset-0 bg-grid-white opacity-20 pointer-events-none"></div>

                                    @if($post->category)
                                        <span class="relative z-10 px-3 py-1 rounded-lg text-xs font-bold bg-white text-[#153B70] shadow-xs">
                                            {{ $post->category->name }}
                                        </span>
                                    @endif
                                    <span class="relative z-10 text-xs font-semibold text-white/95 bg-black/30 backdrop-blur-xs px-2.5 py-0.5 rounded-md">
                                        {{ $post->read_time }}
                                    </span>
                                </div>
                            @endif

                            <div class="p-6">
                                <div class="flex items-center gap-2 text-xs text-gray-400 mb-2.5 font-medium">
                                    <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : '' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $post->author_display_name }}</span>
                                </div>

                                <h2 class="text-lg font-bold text-[#243247] group-hover:text-[#153B70] transition-colors leading-snug">
                                    <a href="{{ route('blog.show', $post->slug) }}">
                                        {{ $post->title }}
                                    </a>
                                </h2>

                                @if($post->excerpt)
                                    <p class="mt-3 text-xs sm:text-sm text-gray-500 leading-relaxed line-clamp-3">
                                        {{ $post->excerpt }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="p-6 pt-0 border-t border-gray-50 flex items-center justify-between mt-auto">
                            <a href="{{ route('blog.show', $post->slug) }}"
                               class="inline-flex items-center text-xs font-bold text-[#087F82] group-hover:text-[#066668] transition-colors">
                                Read Full Article &rarr;
                            </a>
                            <span class="text-[11px] text-gray-400 flex items-center">
                                <i data-lucide="eye" class="w-3.5 h-3.5 mr-1"></i> {{ number_format($post->views_count) }}
                            </span>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($posts->hasPages())
                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs p-12 text-center max-w-lg mx-auto space-y-4">
                <div class="w-16 h-16 rounded-full bg-teal-50 text-[#087F82] flex items-center justify-center mx-auto">
                    <i data-lucide="book-open" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-bold text-[#153B70]">No Articles Found</h3>
                <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">
                    @if(request('q') || request('category'))
                        We couldn't find any articles matching your search criteria. Try different keywords or clear filters.
                    @else
                        Educational articles and career roadmaps will be published soon.
                    @endif
                </p>
                <div class="pt-2">
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold text-white bg-[#087F82] hover:bg-[#066668] rounded-xl transition-colors">
                        View All Articles
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
