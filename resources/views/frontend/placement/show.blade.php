<x-layouts.app>
    <x-slot:title>{{ \App\Services\SeoService::placementTitle($guide) }}</x-slot:title>
    <x-slot:metaDescription>{{ \App\Services\SeoService::placementDescription($guide) }}</x-slot:metaDescription>
    <x-slot:schema>{!! \App\Services\SchemaService::toJsonLd(\App\Services\SchemaService::forPlacementGuide($guide)) !!}</x-slot:schema>

    <div class="bg-[#F7F9FC] py-10 sm:py-14">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Flash Messages --}}
            @if(session('error'))
                <div data-auto-dismiss class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="info" class="w-5 h-5 text-amber-600"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-amber-600 hover:text-amber-800">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            {{-- Breadcrumb Navigation --}}
            <nav class="flex items-center space-x-2 text-xs text-gray-500 overflow-x-auto py-1" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-[#087F82] transition-colors shrink-0">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400 shrink-0"></i>
                <a href="{{ route('placement.index') }}" class="hover:text-[#087F82] transition-colors shrink-0">Placement Preparation</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400 shrink-0"></i>
                <a href="{{ route('placement.index') }}?category={{ $guide->category }}" class="text-[#087F82] font-semibold hover:underline shrink-0">{{ $guide->categoryLabel() }}</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400 shrink-0"></i>
                <span class="text-[#243247] font-medium truncate max-w-[200px] sm:max-w-md">{{ $guide->title }}</span>
            </nav>

            {{-- Guide Header Card --}}
            <header class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs p-6 sm:p-10 space-y-5">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $guide->categoryBadgeClass() }}">
                        {{ $guide->categoryLabel() }}
                    </span>
                    @if($guide->published_at)
                        <span class="text-xs text-gray-400 font-medium">
                            Published {{ $guide->published_at->format('M d, Y') }}
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-[#153B70] tracking-tight leading-tight">
                    {{ $guide->title }}
                </h1>

                @if($guide->excerpt)
                    <p class="text-base sm:text-lg text-[#243247]/85 leading-relaxed font-normal">
                        {{ $guide->excerpt }}
                    </p>
                @endif

                {{-- Downloadable Resource Card (Shown ONLY when genuine asset exists) --}}
                @if($guide->hasDownloadableAsset())
                    <div class="p-5 rounded-2xl bg-teal-50/60 border border-teal-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start sm:items-center space-x-3.5">
                            <span class="p-3 rounded-xl bg-[#087F82] text-white shrink-0 shadow-2xs">
                                <i data-lucide="file-down" class="w-5 h-5"></i>
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-[#153B70]">Downloadable Preparation Asset</h2>
                                <p class="text-xs text-gray-600 mt-0.5">
                                    {{ $guide->downloadable_asset_name ?: 'Official curriculum template & checklist' }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('placement.download', $guide->slug) }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-[#087F82] text-white text-xs font-bold hover:bg-[#066668] transition-all hover:-translate-y-0.5 shadow-2xs shrink-0">
                            <i data-lucide="download" class="w-4 h-4 mr-2"></i> Download Resource
                        </a>
                    </div>
                @endif

                {{-- Featured Image / Diagram Banner --}}
                @if($guide->featured_image)
                    <div class="pt-2 overflow-hidden rounded-2xl">
                        <img src="{{ $guide->featured_image_url }}" alt="{{ $guide->featured_image_alt ?: $guide->title }}" class="w-full max-h-[460px] object-cover rounded-2xl border border-gray-200 shadow-2xs">
                    </div>
                @endif
            </header>

            {{-- Main Guide Body --}}
            <article class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs p-6 sm:p-10 text-[#243247]">
                <div class="prose max-w-none text-sm sm:text-base leading-relaxed text-[#243247] space-y-4
                            prose-headings:text-[#153B70] prose-headings:font-black
                            prose-h2:text-2xl prose-h2:mt-8 prose-h2:mb-3
                            prose-h3:text-xl prose-h3:mt-6 prose-h3:mb-2
                            prose-a:text-[#087F82] prose-a:font-bold hover:prose-a:underline
                            prose-strong:text-[#153B70]
                            prose-ul:list-disc prose-ul:ml-6 prose-ul:space-y-1.5
                            prose-ol:list-decimal prose-ol:ml-6 prose-ol:space-y-1.5
                            prose-blockquote:border-l-4 prose-blockquote:border-[#087F82] prose-blockquote:bg-gray-50 prose-blockquote:p-4 prose-blockquote:rounded-r-xl prose-blockquote:italic
                            prose-code:bg-gray-100 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:text-[#153B70] prose-code:text-xs prose-code:font-mono
                            prose-table:w-full prose-table:border prose-table:border-gray-200 prose-table:rounded-xl prose-table:overflow-hidden prose-table:my-6
                            prose-th:bg-gray-50 prose-th:p-3 prose-th:text-xs prose-th:font-bold prose-th:text-[#153B70] prose-th:border-b prose-th:border-gray-200
                            prose-td:p-3 prose-td:text-xs prose-td:border-b prose-td:border-gray-100
                            prose-img:rounded-xl prose-img:shadow-2xs prose-img:border prose-img:border-gray-200 prose-img:my-6">
                    {!! $guide->content !!}
                </div>
            </article>

            {{-- MCQ Practice Cross-Promotion Card --}}
            @if(isset($mcqCategories) && $mcqCategories->isNotEmpty())
                <div class="bg-gradient-to-r from-blue-50 to-teal-50/60 rounded-2xl border border-teal-100 p-6 sm:p-8 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="text-[11px] font-bold text-[#087F82] uppercase tracking-wider">Reinforce Your Learning</span>
                            <h3 class="text-lg sm:text-xl font-bold text-[#153B70]">Practice Relevant MCQ Topics</h3>
                        </div>
                        <a href="{{ route('categories.index') }}" class="text-xs font-bold text-[#087F82] hover:underline flex items-center">
                            Browse All Categories &rarr;
                        </a>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600">
                        Complement this placement guide with targeted multiple-choice question sets with detailed step-by-step explanations.
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                        @foreach($mcqCategories as $cat)
                            <a href="{{ route('categories.show', $cat->slug) }}" class="p-3.5 rounded-xl bg-white border border-gray-200 hover:border-[#087F82] hover:shadow-2xs transition-all group">
                                <p class="text-xs font-bold text-[#153B70] group-hover:text-[#087F82] transition-colors line-clamp-1">{{ $cat->name }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Practice MCQs &rarr;</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Related Guides --}}
            @if(isset($relatedGuides) && $relatedGuides->isNotEmpty())
                <div class="space-y-4 pt-4">
                    <h3 class="text-xl font-extrabold text-[#153B70]">More Placement Preparation Guides</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        @foreach($relatedGuides as $related)
                            <a href="{{ route('placement.show', $related->slug) }}" class="bg-white rounded-2xl p-5 border border-gray-200 hover:border-[#087F82] hover:shadow-md transition-all group flex flex-col justify-between">
                                <div class="space-y-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $related->categoryBadgeClass() }}">
                                        {{ $related->categoryLabel() }}
                                    </span>
                                    <h4 class="font-bold text-[#153B70] group-hover:text-[#087F82] text-sm leading-snug transition-colors line-clamp-2">
                                        {{ $related->title }}
                                    </h4>
                                    @if($related->excerpt)
                                        <p class="text-xs text-gray-500 line-clamp-2">{{ $related->excerpt }}</p>
                                    @endif
                                </div>
                                <div class="pt-4 flex items-center text-xs font-bold text-[#087F82] group-hover:underline">
                                    Read Guide &rarr;
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Back to Hub --}}
            <div class="text-center pt-4">
                <a href="{{ route('placement.index') }}" class="inline-flex items-center px-6 py-2.5 rounded-xl bg-white border border-gray-300 text-xs font-bold text-[#153B70] hover:bg-gray-50 transition-colors shadow-2xs">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i> Back to Placement Hub
                </a>
            </div>

        </div>
    </div>
</x-layouts.app>

