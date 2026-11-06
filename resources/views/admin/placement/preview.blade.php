<x-layouts.app>
    <x-slot:title>Preview: {{ $guide->title }} - AptiMedha Admin</x-slot:title>
    <x-slot:noindex>true</x-slot:noindex>

    {{-- Admin Preview Banner --}}
    <div class="bg-amber-500 text-amber-950 px-4 py-3 text-xs sm:text-sm font-bold shadow-md sticky top-0 z-50 flex flex-col sm:flex-row items-center justify-between gap-2">
        <div class="flex items-center space-x-2">
            <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 text-amber-900"></i>
            <span>
                ADMIN PREVIEW MODE — This guide is currently
                <strong class="uppercase underline">{{ $guide->is_published ? 'Published' : 'Draft / Unpublished' }}</strong>.
            </span>
        </div>
        <div class="flex items-center space-x-3 shrink-0">
            <a href="{{ route('admin.placement.edit', $guide) }}" class="px-3 py-1 bg-amber-900 text-amber-100 rounded-lg hover:bg-amber-950 transition-colors text-xs">
                Edit Guide
            </a>
            <a href="{{ route('admin.placement.index') }}" class="px-3 py-1 bg-white/40 text-amber-950 rounded-lg hover:bg-white/60 transition-colors text-xs">
                Exit Preview
            </a>
        </div>
    </div>

    {{-- Guide Detail Rendering --}}
    <div class="bg-[#F7F9FC] py-10 sm:py-14">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Breadcrumbs --}}
            <nav class="flex items-center space-x-2 text-xs text-gray-500">
                <a href="{{ route('placement.index') }}" class="hover:text-[#087F82] transition-colors">Placement Preparation</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
                <span class="text-[#087F82] font-semibold">{{ $guide->categoryLabel() }}</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
                <span class="text-[#243247] truncate max-w-xs">{{ $guide->title }}</span>
            </nav>

            {{-- Header Card --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs p-6 sm:p-10 space-y-5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $guide->categoryBadgeClass() }}">
                        {{ $guide->categoryLabel() }}
                    </span>
                    @if($guide->published_at)
                        <span class="text-xs text-gray-400 font-medium">
                            Updated {{ $guide->published_at->format('M d, Y') }}
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#153B70] tracking-tight leading-tight">
                    {{ $guide->title }}
                </h1>

                @if($guide->excerpt)
                    <p class="text-base sm:text-lg text-[#243247]/80 leading-relaxed font-normal">
                        {{ $guide->excerpt }}
                    </p>
                @endif

                {{-- Action / Download row --}}
                @if($guide->hasDownloadableAsset())
                    <div class="p-4 rounded-xl bg-teal-50/60 border border-teal-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center space-x-3">
                            <span class="p-2.5 rounded-lg bg-[#087F82] text-white">
                                <i data-lucide="file-down" class="w-5 h-5"></i>
                            </span>
                            <div>
                                <p class="text-xs font-bold text-[#153B70]">Curriculum Resource Included</p>
                                <p class="text-[11px] text-gray-500">{{ $guide->downloadable_asset_name ?: 'Downloadable attachment' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('placement.download', $guide->slug) }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-[#087F82] text-white text-xs font-bold hover:bg-[#066668] transition-colors shadow-2xs">
                            <i data-lucide="download" class="w-4 h-4 mr-1.5"></i> Download Resource
                        </a>
                    </div>
                @endif

                @if($guide->featured_image)
                    <div class="pt-4 overflow-hidden rounded-xl">
                        <img src="{{ $guide->featured_image_url }}" alt="{{ $guide->featured_image_alt ?: $guide->title }}" class="w-full max-h-[420px] object-cover rounded-xl border border-gray-200">
                    </div>
                @endif
            </div>

            {{-- Guide Content Body --}}
            <article class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs p-6 sm:p-10 text-[#243247]">
                <div class="prose max-w-none text-sm sm:text-base leading-relaxed text-[#243247] space-y-4 prose-headings:text-[#153B70] prose-a:text-[#087F82] prose-strong:text-[#153B70]">
                    {!! $guide->content !!}
                </div>
            </article>

            {{-- Related Guides --}}
            @if(isset($relatedGuides) && $relatedGuides->isNotEmpty())
                <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs p-6 sm:p-8 space-y-4">
                    <h3 class="text-base font-bold text-[#153B70]">Related Placement Guides</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($relatedGuides as $related)
                            <div class="p-4 rounded-xl border border-gray-200 hover:border-[#087F82] transition-colors">
                                <span class="text-[11px] font-bold text-[#087F82] uppercase">{{ $related->categoryLabel() }}</span>
                                <h4 class="font-bold text-[#153B70] text-sm mt-1 line-clamp-2">{{ $related->title }}</h4>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $related->excerpt }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-layouts.app>

