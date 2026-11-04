<x-layouts.app>
    <x-slot:title>Categories & Subject Tracks - AptiMedha</x-slot:title>

    {{-- Category Hero with Subtle Background Pattern --}}
    <div class="bg-gradient-to-b from-white to-[#F7F9FC] border-b border-gray-200/80 py-12 sm:py-16 bg-grid-pattern relative overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <nav class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">
                <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-300"></i>
                <span class="text-[#087F82]">Categories</span>
            </nav>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-200/70 text-xs font-bold text-[#087F82] mb-3">
                <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                <span>Structured Learning Taxonomy</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#153B70] tracking-tight">Explore Question Categories</h1>
            <p class="mt-3 text-base sm:text-lg text-[#243247]/75 max-w-3xl leading-relaxed">
                Browse our structured categories covering aptitude, logical reasoning, verbal ability, general knowledge, programming, and core engineering disciplines.
            </p>
        </div>
    </div>

    {{-- Category Cards Grid --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div data-motion-stagger data-motion-stagger-delay="0.06" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @forelse($categories as $category)
                <div data-motion-item class="bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#087F82] card-hover-effect flex flex-col justify-between overflow-hidden group">
                    <div class="p-6 sm:p-7">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-teal-50 text-[#087F82] flex items-center justify-center group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                                <i data-lucide="{{ $category->icon ?: 'folder' }}" class="w-6 h-6"></i>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-teal-50 text-[#087F82] border border-teal-100">
                                {{ $category->subjects->count() > 0 ? $category->subjects->count() . ' Subjects' : 'Direct Track' }}
                            </span>
                        </div>

                        <h2 class="text-xl font-bold text-[#153B70] group-hover:text-[#087F82] transition-colors">
                            <a href="{{ route('categories.show', $category->slug) }}">
                                {{ $category->name }}
                            </a>
                        </h2>

                        <p class="mt-2 text-sm text-[#243247]/70 leading-relaxed line-clamp-2">
                            {{ $category->description ?? 'Comprehensive MCQ practice sets and topic guides.' }}
                        </p>

                        {{-- Key Modules / Subjects Pill List --}}
                        @if($category->subjects && $category->subjects->isNotEmpty())
                            <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Featured Subjects</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($category->subjects->take(4) as $subject)
                                        <span class="inline-block text-xs font-medium text-[#243247]/80 bg-[#F7F9FC] border border-gray-200/80 px-2 py-0.5 rounded-md">
                                            {{ $subject->name }}
                                        </span>
                                    @endforeach
                                    @if($category->subjects->count() > 4)
                                        <span class="inline-block text-xs font-medium text-gray-400 bg-gray-50 border border-gray-200/60 px-2 py-0.5 rounded-md">
                                            +{{ $category->subjects->count() - 4 }} more
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="px-6 sm:px-7 py-4 bg-[#F7F9FC] border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-[#087F82] flex items-center gap-1.5">
                            <i data-lucide="help-circle" class="w-4 h-4"></i>
                            @if($category->questions_count > 0)
                                {{ number_format($category->questions_count) }} MCQs
                            @else
                                {{ $category->topics->count() }} Topics
                            @endif
                        </span>
                        <a href="{{ route('categories.show', $category->slug) }}"
                           class="inline-flex items-center text-xs font-bold text-[#153B70] group-hover:text-[#087F82] group-hover:translate-x-0.5 transition-all">
                            View Topics &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-gray-400">
                    No active categories available at this moment.
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
