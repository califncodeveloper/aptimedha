<x-layouts.app>
    <x-slot:title>{{ $category->name }} - AptiMedha Topics & MCQs</x-slot:title>

    {{-- Breadcrumb & Category Hero --}}
    <div class="bg-white border-b border-gray-200/80 py-8 sm:py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">
                <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <a href="{{ route('categories.index') }}" class="hover:text-[#153B70] transition-colors">Categories</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-[#087F82]">{{ $category->name }}</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-teal-50 text-[#087F82] flex items-center justify-center flex-shrink-0 shadow-xs">
                        <i data-lucide="{{ $category->icon ?? 'folder' }}" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#153B70] tracking-tight">{{ $category->name }}</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#087F82]/10 text-[#087F82]">
                                {{ $category->badge ?? 'Category' }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-[#243247]/70 max-w-2xl leading-relaxed">
                            {{ $category->description ?? 'Browse topics and start practicing questions.' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('mcq.index', ['category' => $category->slug]) }}"
                       class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-[#087F82] text-white font-bold text-sm hover:bg-[#066668] transition-colors shadow-xs">
                        <i data-lucide="play" class="w-4 h-4 mr-2"></i>
                        Practice All {{ $category->name }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Topic Taxonomy Section --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10" x-data="{ searchQuery: '' }">
        
        {{-- Search & Stats Toolbar --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-xl border border-gray-200/80 shadow-xs">
            <div class="relative flex-1 max-w-md">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-3"></i>
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Filter topics (e.g. Percentages, Divisibility)..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none">
            </div>
            <div class="text-xs text-gray-500 font-medium">
                Showing all topics in <span class="font-bold text-[#153B70]">{{ $category->name }}</span>
            </div>
        </div>

        {{-- Subjects & Topics List --}}
        <div class="space-y-8">
            @foreach($category->subjects ?? [] as $subject)
                <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#087F82]"></span>
                            <h2 class="text-lg font-bold text-[#153B70]">{{ $subject->name }}</h2>
                        </div>
                        <span class="text-xs font-semibold text-gray-400">
                            {{ count($subject->topics ?? []) }} Topics
                        </span>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach($subject->topics as $topic)
                                @php
                                    $topicSlug = \Illuminate\Support\Str::slug($topic);
                                @endphp
                                <a href="{{ route('mcq.index', ['category' => $category->slug, 'topic' => $topicSlug]) }}"
                                   x-show="!searchQuery || '{{ strtolower($topic) }}'.includes(searchQuery.toLowerCase())"
                                   class="flex items-center justify-between p-3 rounded-xl border border-gray-200/70 hover:border-[#087F82] hover:bg-teal-50/30 group transition-all">
                                    <span class="text-sm font-semibold text-[#243247] group-hover:text-[#087F82] transition-colors truncate">
                                        {{ $topic }}
                                    </span>
                                    <i data-lucide="arrow-right" class="w-4 h-4 text-gray-300 group-hover:text-[#087F82] group-hover:translate-x-0.5 transition-all flex-shrink-0 ml-2"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
