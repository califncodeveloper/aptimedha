<x-layouts.app>
    <x-slot:title>{{ \App\Services\SeoService::categoryTitle($category) }}</x-slot:title>
    <x-slot:metaDescription>{{ \App\Services\SeoService::categoryDescription($category) }}</x-slot:metaDescription>
    <x-slot:schema>{!! \App\Services\SchemaService::toJsonLd(\App\Services\SchemaService::forCategory($category)) !!}</x-slot:schema>

    {{-- Breadcrumb & Category Hero with Modern Background Pattern --}}
    <div class="bg-gradient-to-b from-white to-[#F7F9FC] border-b border-gray-200/80 py-10 sm:py-14 bg-grid-pattern relative overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <nav class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">
                <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-300"></i>
                <a href="{{ route('categories.index') }}" class="hover:text-[#153B70] transition-colors">Categories</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-300"></i>
                <span class="text-[#087F82]">{{ $category->name }}</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start gap-3 sm:gap-4">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-teal-50 text-[#087F82] flex items-center justify-center flex-shrink-0 shadow-xs border border-teal-100">
                        <i data-lucide="{{ $category->icon ?: 'folder' }}" class="w-6 h-6 sm:w-7 sm:h-7"></i>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                            <h1 class="text-xl sm:text-3xl font-extrabold text-[#153B70] tracking-tight">{{ $category->name }}</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#087F82]/10 text-[#087F82] border border-[#087F82]/20">
                                {{ $category->subjects->count() > 0 ? $category->subjects->count() . ' Subjects' : 'Direct Track' }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs sm:text-sm text-[#243247]/70 max-w-2xl leading-relaxed">
                            {{ $category->description ?? 'Browse topics and start practicing multiple choice questions.' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('mcq.index', ['category' => $category->slug]) }}"
                       class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 rounded-xl bg-[#087F82] text-white font-bold text-sm hover:bg-[#066668] transition-all shadow-md shadow-[#087F82]/20 hover:-translate-y-0.5">
                        <i data-lucide="play" class="w-4 h-4 mr-2"></i>
                        Start Full Track Practice
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Topic Taxonomy Section --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 sm:py-12" x-data="{ searchQuery: '' }">

        {{-- Search & Stats Toolbar --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-xs">
            <div class="relative flex-1 max-w-md">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5"></i>
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Filter topics (e.g. Percentages, Divisibility)..."
                       class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none transition-all">
            </div>
            <div class="text-xs text-gray-500 font-semibold flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#087F82]"></span>
                <span>Structured syllabus for placement screening rounds</span>
            </div>
        </div>

        {{-- Subjects & Topics List --}}
        <div class="space-y-8">
            @foreach($category->subjects as $subject)
                <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#087F82]"></span>
                            <h2 class="text-lg font-bold text-[#153B70]">{{ $subject->name }}</h2>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full">
                            {{ $subject->topics->count() }} Chapters
                        </span>
                    </div>

                    <div class="p-6 sm:p-7">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            @forelse($subject->topics as $topic)
                                <a href="{{ route('mcq.index', ['category' => $category->slug, 'topic' => $topic->slug]) }}"
                                   x-show="!searchQuery || (@js(strtolower($topic->name))).includes(searchQuery.toLowerCase())"
                                   class="flex items-center justify-between p-3.5 rounded-xl border border-gray-200/70 hover:border-[#087F82] hover:bg-teal-50/30 group transition-all card-hover-effect">
                                    <span class="text-sm font-semibold text-[#243247] group-hover:text-[#087F82] transition-colors truncate">
                                        {{ $topic->name }}
                                    </span>
                                    <i data-lucide="arrow-right" class="w-4 h-4 text-gray-300 group-hover:text-[#087F82] group-hover:translate-x-1 transition-all flex-shrink-0 ml-2"></i>
                                </a>
                            @empty
                                <div class="col-span-full py-4 text-center text-xs text-gray-400">
                                    No chapters configured under this subject.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Direct category topics (if any) --}}
            @if($category->topics && $category->topics->isNotEmpty())
                <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#153B70]"></span>
                            <h2 class="text-lg font-bold text-[#153B70]">Direct Topics</h2>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full">
                            {{ $category->topics->count() }} Topics
                        </span>
                    </div>

                    <div class="p-6 sm:p-7">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach($category->topics as $topic)
                                <a href="{{ route('mcq.index', ['category' => $category->slug, 'topic' => $topic->slug]) }}"
                                   x-show="!searchQuery || (@js(strtolower($topic->name))).includes(searchQuery.toLowerCase())"
                                   class="flex items-center justify-between p-3.5 rounded-xl border border-gray-200/70 hover:border-[#087F82] hover:bg-teal-50/30 group transition-all card-hover-effect">
                                    <span class="text-sm font-semibold text-[#243247] group-hover:text-[#087F82] transition-colors truncate">
                                        {{ $topic->name }}
                                    </span>
                                    <i data-lucide="arrow-right" class="w-4 h-4 text-gray-300 group-hover:text-[#087F82] group-hover:translate-x-1 transition-all flex-shrink-0 ml-2"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
