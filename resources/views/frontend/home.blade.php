<x-layouts.app>
    <x-slot:title>AptiMedha - Practise Smart. Get Placement Ready.</x-slot:title>

    {{-- Hero Section (Matches Mockup) --}}
    <section class="relative bg-[#F7F9FC] overflow-hidden pt-12 pb-16 lg:pt-20 lg:pb-24 border-b border-gray-200/60">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                {{-- Left Text Column --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-200/60 text-xs font-semibold text-[#087F82]">
                        <span class="w-2 h-2 rounded-full bg-[#087F82] animate-pulse"></span>
                        Placement 2026 Ready Practice Sets
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-[3.5rem] font-black text-[#153B70] tracking-tight leading-[1.15]">
                        Practise Smart.<br />
                        <span class="text-[#153B70]">Get Placement Ready.</span>
                    </h1>

                    <p class="text-lg text-[#243247]/80 leading-relaxed max-w-xl">
                        Sharpen your aptitude, reasoning and subject skills to crack placement exams and interviews.
                    </p>

                    <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                        <a href="{{ route('mcq.index') }}"
                           class="inline-flex items-center justify-center px-7 py-3.5 text-base font-semibold text-white bg-[#087F82] hover:bg-[#066668] rounded-xl shadow-md shadow-[#087F82]/20 transition-all hover:translate-y-[-1px] active:translate-y-0">
                            Start Practising &rarr;
                        </a>
                        <a href="{{ route('categories.index') }}"
                           class="inline-flex items-center justify-center px-7 py-3.5 text-base font-semibold text-[#153B70] bg-white border-2 border-[#153B70]/80 hover:bg-gray-50 rounded-xl transition-all">
                            Explore Topics
                        </a>
                    </div>

                    {{-- Quick Highlights --}}
                    <div class="pt-6 border-t border-gray-200/80 flex flex-wrap items-center gap-6 text-xs text-[#243247]/70 font-medium">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="check-circle" class="w-4 h-4 text-[#087F82]"></i> 10,000+ Curated MCQs
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="check-circle" class="w-4 h-4 text-[#087F82]"></i> Step-by-Step Solutions
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="check-circle" class="w-4 h-4 text-[#087F82]"></i> 100% Free Public Access
                        </span>
                    </div>
                </div>

                {{-- Right Illustration Column (Vector representation of client mockup) --}}
                <div class="lg:col-span-5 flex items-center justify-center">
                    <div class="relative w-full max-w-md aspect-square flex items-center justify-center">
                        
                        {{-- Background decorative blurs & circles --}}
                        <div class="absolute inset-0 bg-gradient-to-tr from-[#087F82]/10 to-[#153B70]/10 rounded-full blur-2xl transform scale-90"></div>
                        <div class="absolute w-72 h-72 border-2 border-dashed border-[#087F82]/30 rounded-full animate-spin-slow"></div>

                        {{-- Floating Card: Bar Chart --}}
                        <div class="absolute top-6 left-2 bg-white rounded-2xl p-3.5 shadow-lg border border-gray-100 flex items-center gap-3 animate-bounce-gentle">
                            <div class="w-10 h-10 rounded-xl bg-teal-50 text-[#087F82] flex items-center justify-center">
                                <i data-lucide="bar-chart-2" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#153B70]">Data Interpretation</p>
                                <p class="text-[10px] text-gray-400">Charts & Tables</p>
                            </div>
                        </div>

                        {{-- Floating Card: Lightbulb --}}
                        <div class="absolute top-12 right-2 bg-white rounded-2xl p-3.5 shadow-lg border border-gray-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i data-lucide="lightbulb" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#153B70]">Logical Thinking</p>
                                <p class="text-[10px] text-gray-400">Puzzles & Series</p>
                            </div>
                        </div>

                        {{-- Floating Card: Document --}}
                        <div class="absolute bottom-8 left-6 bg-white rounded-2xl p-3.5 shadow-lg border border-gray-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#153B70] flex items-center justify-center">
                                <i data-lucide="file-check-2" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#153B70]">HR Questions</p>
                                <p class="text-[10px] text-gray-400">Interview Ready</p>
                            </div>
                        </div>

                        {{-- Main Central Graphic: Open Book with Checkmark matching mockup --}}
                        <div class="relative z-10 w-56 h-56 bg-white rounded-3xl shadow-xl border border-gray-100 p-8 flex flex-col items-center justify-center group hover:scale-105 transition-transform duration-300">
                            {{-- Circular Teal Badge with Checkmark --}}
                            <div class="w-18 h-18 rounded-full bg-[#087F82] text-white flex items-center justify-center shadow-lg shadow-[#087F82]/30 mb-4">
                                <i data-lucide="check" class="w-9 h-9 stroke-[3]"></i>
                            </div>
                            {{-- Stylized open book icon --}}
                            <div class="text-[#153B70] flex items-center gap-2">
                                <i data-lucide="book-open" class="w-8 h-8"></i>
                                <span class="text-lg font-black tracking-tight">Apti<span class="text-[#087F82]">Medha</span></span>
                            </div>
                            <span class="mt-1 text-[11px] font-semibold text-[#087F82] bg-teal-50 px-2.5 py-0.5 rounded-full">
                                Verified Solutions
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- "Choose Your Subject" Section (Matches Mockup 9-Grid) --}}
    <section class="py-16 sm:py-20 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-12">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#153B70] tracking-tight">Choose Your Subject</h2>
                <p class="mt-2 text-sm sm:text-base text-[#243247]/70">
                    Pick a subject to start practising topic-wise MCQs and build your skills.
                </p>
            </div>

            {{-- 3x3 Cards Grid matching mockup --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                {{-- 1. Arithmetic Aptitude --}}
                <a href="{{ route('categories.show', 'arithmetic-aptitude') }}"
                   class="flex items-center justify-between p-6 bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#087F82] hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-50 text-[#153B70] flex items-center justify-center group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                            <i data-lucide="calculator" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#243247] group-hover:text-[#153B70] transition-colors">Arithmetic Aptitude</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Numbers, percentages, averages and more.</p>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300 group-hover:text-[#087F82] group-hover:translate-x-1 transition-all"></i>
                </a>

                {{-- 2. Data Interpretation --}}
                <a href="{{ route('categories.show', 'data-interpretation') }}"
                   class="flex items-center justify-between p-6 bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#087F82] hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-50 text-[#153B70] flex items-center justify-center group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                            <i data-lucide="bar-chart-2" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#243247] group-hover:text-[#153B70] transition-colors">Data Interpretation</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Tables, graphs, charts and caselets.</p>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300 group-hover:text-[#087F82] group-hover:translate-x-1 transition-all"></i>
                </a>

                {{-- 3. Verbal Ability --}}
                <a href="{{ route('categories.show', 'verbal-ability') }}"
                   class="flex items-center justify-between p-6 bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#087F82] hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-50 text-[#153B70] flex items-center justify-center group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                            <i data-lucide="message-square" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#243247] group-hover:text-[#153B70] transition-colors">Verbal Ability</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Reading, vocabulary, comprehension.</p>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300 group-hover:text-[#087F82] group-hover:translate-x-1 transition-all"></i>
                </a>

                {{-- 4. Logical Reasoning --}}
                <a href="{{ route('categories.show', 'logical-reasoning') }}"
                   class="flex items-center justify-between p-6 bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#087F82] hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-50 text-[#153B70] flex items-center justify-center group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                            <i data-lucide="puzzle" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#243247] group-hover:text-[#153B70] transition-colors">Logical Reasoning</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Puzzles, series, coding decoding and more.</p>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300 group-hover:text-[#087F82] group-hover:translate-x-1 transition-all"></i>
                </a>

                {{-- 5. English --}}
                <a href="{{ route('categories.show', 'english') }}"
                   class="flex items-center justify-between p-6 bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#087F82] hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-50 text-[#153B70] flex items-center justify-center group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#243247] group-hover:text-[#153B70] transition-colors">English</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Grammar, error spotting, para jumbles.</p>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300 group-hover:text-[#087F82] group-hover:translate-x-1 transition-all"></i>
                </a>

                {{-- 6. General Knowledge --}}
                <a href="{{ route('categories.show', 'general-knowledge') }}"
                   class="flex items-center justify-between p-6 bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#087F82] hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-50 text-[#153B70] flex items-center justify-center group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                            <i data-lucide="globe" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#243247] group-hover:text-[#153B70] transition-colors">General Knowledge</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Static and current affairs for placements.</p>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300 group-hover:text-[#087F82] group-hover:translate-x-1 transition-all"></i>
                </a>

                {{-- 7. Programming --}}
                <a href="{{ route('categories.show', 'programming') }}"
                   class="flex items-center justify-between p-6 bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#087F82] hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-50 text-[#153B70] flex items-center justify-center group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                            <i data-lucide="code" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#243247] group-hover:text-[#153B70] transition-colors">Programming</h3>
                            <p class="text-xs text-gray-500 mt-0.5">C, C++, Java, Python and more.</p>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300 group-hover:text-[#087F82] group-hover:translate-x-1 transition-all"></i>
                </a>

                {{-- 8. Engineering --}}
                <a href="{{ route('categories.show', 'engineering') }}"
                   class="flex items-center justify-between p-6 bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#087F82] hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-50 text-[#153B70] flex items-center justify-center group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                            <i data-lucide="cpu" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#243247] group-hover:text-[#153B70] transition-colors">Engineering</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Core concepts and fundamentals.</p>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300 group-hover:text-[#087F82] group-hover:translate-x-1 transition-all"></i>
                </a>

                {{-- 9. Interview Preparation --}}
                <a href="{{ route('placement.index') }}"
                   class="flex items-center justify-between p-6 bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#087F82] hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-50 text-[#153B70] flex items-center justify-center group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                            <i data-lucide="users" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#243247] group-hover:text-[#153B70] transition-colors">Interview Preparation</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Technical and HR interview questions.</p>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300 group-hover:text-[#087F82] group-hover:translate-x-1 transition-all"></i>
                </a>

            </div>
        </div>
    </section>

    {{-- "Latest Articles" Section (Matches Mockup) --}}
    <section class="py-16 sm:py-20 bg-[#F7F9FC] border-t border-gray-200/60">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#153B70] tracking-tight">Latest Articles</h2>
                    <p class="mt-2 text-sm sm:text-base text-[#243247]/70">
                        Helpful guides, tips and resources for your placement preparation.
                    </p>
                </div>
                <a href="{{ route('blog.index') }}" class="inline-flex items-center text-sm font-bold text-[#087F82] hover:text-[#066668] transition-colors">
                    View All &rarr;
                </a>
            </div>

            {{-- 3 Article Cards matching mockup --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- Article 1 --}}
                <a href="{{ route('blog.show', 'how-to-prepare-for-aptitude-tests-in-placements') }}"
                   class="p-6 bg-white rounded-2xl border border-gray-200/80 shadow-xs hover:border-[#087F82] hover:shadow-md transition-all flex items-start gap-4 group">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#153B70] flex items-center justify-center flex-shrink-0 group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                        <i data-lucide="file-text" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-[#243247] group-hover:text-[#153B70] transition-colors line-clamp-2">
                            How to Prepare for Aptitude Tests in Placements
                        </h3>
                        <p class="text-xs text-gray-400 mt-2 font-medium">Mar 12, 2026</p>
                    </div>
                </a>

                {{-- Article 2 --}}
                <a href="{{ route('blog.show', 'data-interpretation-tips-and-tricks') }}"
                   class="p-6 bg-white rounded-2xl border border-gray-200/80 shadow-xs hover:border-[#087F82] hover:shadow-md transition-all flex items-start gap-4 group">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 text-[#087F82] flex items-center justify-center flex-shrink-0 group-hover:bg-[#087F82] group-hover:text-white transition-colors">
                        <i data-lucide="bar-chart-2" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-[#243247] group-hover:text-[#153B70] transition-colors line-clamp-2">
                            Data Interpretation Tips and Tricks
                        </h3>
                        <p class="text-xs text-gray-400 mt-2 font-medium">Mar 8, 2026</p>
                    </div>
                </a>

                {{-- Article 3 --}}
                <a href="{{ route('blog.show', 'common-interview-questions-and-answers') }}"
                   class="p-6 bg-white rounded-2xl border border-gray-200/80 shadow-xs hover:border-[#087F82] hover:shadow-md transition-all flex items-start gap-4 group">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <i data-lucide="lightbulb" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-[#243247] group-hover:text-[#153B70] transition-colors line-clamp-2">
                            Common Interview Questions and Answers
                        </h3>
                        <p class="text-xs text-gray-400 mt-2 font-medium">Mar 5, 2026</p>
                    </div>
                </a>

            </div>
        </div>
    </section>

    {{-- Interactive Call-to-Action Banner --}}
    <section class="py-14 bg-gradient-to-r from-[#153B70] to-[#087F82] text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8 text-center lg:text-left">
                <div class="max-w-2xl">
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Ready to Master Your Aptitude Skills?</h2>
                    <p class="mt-2 text-white/80 text-sm sm:text-base">
                        Start practising today with comprehensive topic-wise MCQs, instant explanations, and verified placement papers.
                    </p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('mcq.index') }}"
                       class="px-8 py-3.5 bg-white text-[#153B70] font-bold rounded-xl shadow-lg hover:bg-gray-100 transition-colors">
                        Start Practicing Now
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-8 py-3.5 bg-[#087F82] text-white font-bold rounded-xl border border-white/20 hover:bg-[#066668] transition-colors">
                        Create Free Account
                    </a>
                </div>
            </div>
        </div>
    </section>

</x-layouts.app>
