<x-layouts.app>
    <x-slot:title>Categories & Subject Tracks - AptiMedha</x-slot:title>

    <div class="bg-white border-b border-gray-200/80 py-10 sm:py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">
                <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-[#087F82]">Categories</span>
            </nav>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-[#153B70] tracking-tight">Explore Question Categories</h1>
            <p class="mt-2 text-base text-[#243247]/70 max-w-3xl">
                Browse our structured categories covering aptitude, logical reasoning, verbal ability, general knowledge, programming, and engineering disciplines.
            </p>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @foreach($categories as $category)
                <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#087F82]/50 hover:shadow-lg transition-all flex flex-col justify-between overflow-hidden group">
                    <div class="p-6 sm:p-7">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-teal-50 text-[#087F82] flex items-center justify-center group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                                <i data-lucide="{{ $category->icon ?? 'folder' }}" class="w-6 h-6"></i>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-[#153B70]">
                                {{ $category->badge ?? 'Category' }}
                            </span>
                        </div>

                        <h2 class="text-xl font-bold text-[#153B70] group-hover:text-[#087F82] transition-colors">
                            <a href="{{ route('categories.show', $category->slug) }}">
                                {{ $category->name }}
                            </a>
                        </h2>

                        <p class="mt-2 text-sm text-[#243247]/70 leading-relaxed line-clamp-2">
                            {{ $category->description }}
                        </p>

                        {{-- Subjects / Subtopics Snippet --}}
                        @if(isset($category->subjects))
                            <div class="mt-4 pt-4 border-t border-gray-100 space-y-1.5">
                                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Key Modules</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($category->subjects as $subject)
                                        <span class="inline-block text-xs font-medium text-[#243247]/80 bg-[#F7F9FC] border border-gray-200/80 px-2 py-0.5 rounded-md">
                                            {{ $subject->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="px-6 sm:px-7 py-4 bg-[#F7F9FC] border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-[#087F82] flex items-center gap-1.5">
                            <i data-lucide="help-circle" class="w-4 h-4"></i>
                            {{ number_format($category->mcqs_count ?? 100) }} MCQs
                        </span>
                        <a href="{{ route('categories.show', $category->slug) }}"
                           class="inline-flex items-center text-xs font-bold text-[#153B70] group-hover:text-[#087F82] transition-colors">
                            View Topics &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
