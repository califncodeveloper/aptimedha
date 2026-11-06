<x-layouts.app>
    <x-slot:title>AptiMedha – Practice MCQs, Aptitude & Placement Preparation</x-slot:title>
    <x-slot:metaDescription>Practice aptitude MCQs, reasoning puzzles, programming questions, and placement interview preparation guides on AptiMedha.</x-slot:metaDescription>
    <x-slot:schema>{!! \App\Services\SchemaService::toJsonLd(\App\Services\SchemaService::forHome()) !!}</x-slot:schema>

    {{-- Hero Section (Premium, Modern EdTech Landing Design) --}}
    <section class="relative bg-gradient-to-b from-white via-[#F7F9FC] to-[#F7F9FC] overflow-hidden pt-12 pb-16 lg:pt-16 lg:pb-24 border-b border-gray-200/60 bg-grid-pattern">
        {{-- Ambient decorative background glow with subtle Framer Motion parallax --}}
        <div data-motion-parallax data-motion-parallax-speed="0.12" class="absolute top-0 right-1/4 w-96 h-96 bg-[#087F82]/10 rounded-full blur-3xl pointer-events-none -z-10"></div>
        <div data-motion-parallax data-motion-parallax-speed="0.18" data-motion-parallax-dir="up" class="absolute top-1/3 left-10 w-80 h-80 bg-[#153B70]/8 rounded-full blur-3xl pointer-events-none -z-10"></div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                {{-- Left Content Section --}}
                <div class="lg:col-span-7 space-y-6">
                    
                    {{-- 1. Trust Badge --}}
                    <div data-motion="fade-down" class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white/95 border border-teal-200/90 shadow-2xs text-xs font-semibold text-[#087F82] backdrop-blur-xs">
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#087F82] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#087F82]"></span>
                        </span>
                        <span>Placement Preparation Hub</span>
                        <span class="text-gray-300">&bull;</span>
                        <span class="text-gray-500 font-medium">Verified Solutions</span>
                    </div>

                    {{-- 2. Powerful Headline with Gradient Highlight --}}
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-[3.5rem] xl:text-[3.85rem] font-black text-[#153B70] tracking-tight leading-[1.15] sm:leading-[1.12]">
                        <span data-motion="fade-up" class="block">Practise Smart.</span>
                        <span data-motion="fade-up" data-motion-delay="0.12" class="block text-transparent bg-clip-text bg-gradient-to-r from-[#153B70] via-[#087F82] to-[#087F82]">
                            Get Placement Ready.
                        </span>
                    </h1>

                    {{-- Supporting Description --}}
                    <p data-motion="fade-up" data-motion-delay="0.2" class="text-sm sm:text-base md:text-lg text-[#243247]/80 leading-relaxed max-w-xl">
                        Master quantitative aptitude, logical reasoning, verbal ability, and programming with verified step-by-step solutions designed for campus placements and competitive exams.
                    </p>

                    {{-- 3. Smart Search Bar & Trending Topics --}}
                    <div data-motion="fade-up" data-motion-delay="0.26" class="pt-1 max-w-xl" x-data="{ heroSearch: '' }">
                        <form action="{{ route('mcq.index') }}" method="GET" class="relative flex items-center group">
                            <div class="absolute left-3.5 sm:left-4 pointer-events-none text-gray-400 group-focus-within:text-[#087F82] transition-colors">
                                <i data-lucide="search" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                            </div>
                            <input type="text"
                                   name="topic"
                                   x-model="heroSearch"
                                   placeholder="Search Percentages, Python, Number Series..."
                                   class="w-full pl-10 sm:pl-12 pr-24 sm:pr-32 py-3 sm:py-4 bg-white rounded-2xl border border-gray-200/90 shadow-xs text-xs sm:text-base text-[#243247] placeholder-gray-400 focus:outline-none focus:border-[#087F82] focus:ring-4 focus:ring-[#087F82]/15 transition-all">
                            <button type="submit"
                                    data-motion-spring
                                    class="absolute right-1.5 sm:right-2 px-3 sm:px-5 py-2 sm:py-2.5 bg-[#087F82] hover:bg-[#066668] text-white font-semibold text-xs sm:text-sm rounded-xl shadow-xs hover:shadow-sm transition-all cursor-pointer flex items-center gap-1.5">
                                <span>Practice</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 hidden sm:inline"></i>
                            </button>
                        </form>

                        {{-- 4. Trending Topics --}}
                        <div class="mt-3 flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs">
                            <span class="text-gray-400 font-medium flex items-center gap-1 mr-0.5 text-[11px] sm:text-xs">
                                <i data-lucide="trending-up" class="w-3.5 h-3.5 text-[#087F82]"></i>
                                <span>Trending:</span>
                            </span>
                            <a href="{{ route('mcq.index', ['topic' => 'percentages']) }}"
                               class="px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg bg-white border border-gray-200/80 text-gray-600 hover:text-[#087F82] hover:border-[#087F82] hover:bg-teal-50/50 hover:shadow-2xs transition-all font-medium text-[11px] sm:text-xs">
                                Percentages
                            </a>
                            <a href="{{ route('mcq.index', ['topic' => 'profit-and-loss']) }}"
                               class="px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg bg-white border border-gray-200/80 text-gray-600 hover:text-[#087F82] hover:border-[#087F82] hover:bg-teal-50/50 hover:shadow-2xs transition-all font-medium text-[11px] sm:text-xs">
                                Profit &amp; Loss
                            </a>
                            <a href="{{ route('mcq.index', ['topic' => 'number-series']) }}"
                               class="px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg bg-white border border-gray-200/80 text-gray-600 hover:text-[#087F82] hover:border-[#087F82] hover:bg-teal-50/50 hover:shadow-2xs transition-all font-medium text-[11px] sm:text-xs">
                                Number Series
                            </a>
                            <a href="{{ route('mcq.index', ['topic' => 'python']) }}"
                               class="px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg bg-white border border-gray-200/80 text-gray-600 hover:text-[#087F82] hover:border-[#087F82] hover:bg-teal-50/50 hover:shadow-2xs transition-all font-medium text-[11px] sm:text-xs">
                                Python
                            </a>
                        </div>
                    </div>

                    {{-- 5. Call-to-Action Buttons --}}
                    <div data-motion="fade-up" data-motion-delay="0.32" class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                        <a href="{{ route('mcq.index') }}"
                           data-motion-spring
                           class="inline-flex items-center justify-center px-6 sm:px-7 py-3.5 text-sm sm:text-base font-bold text-white bg-[#087F82] hover:bg-[#066668] rounded-xl shadow-md shadow-[#087F82]/20 hover:-translate-y-0.5 active:translate-y-0 transition-all cursor-pointer group w-full sm:w-auto">
                            <i data-lucide="play-circle" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform"></i>
                            Start Practicing
                        </a>
                        <a href="{{ route('categories.index') }}"
                           data-motion-spring
                           class="inline-flex items-center justify-center px-6 sm:px-7 py-3.5 text-sm sm:text-base font-semibold text-[#153B70] bg-white border-2 border-gray-200 hover:border-[#153B70] hover:bg-gray-50 rounded-xl transition-all cursor-pointer group w-full sm:w-auto">
                            <i data-lucide="compass" class="w-5 h-5 mr-2 text-[#153B70] group-hover:rotate-45 transition-transform"></i>
                            Explore Topics
                        </a>
                    </div>

                    {{-- 8. Trust Indicators --}}
                    <div data-motion="fade-up" data-motion-delay="0.38" class="pt-6 border-t border-gray-200/80 flex flex-wrap items-center gap-5 sm:gap-7 text-xs sm:text-sm text-[#243247]/85 font-semibold">
                        <span class="flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-4.5 h-4.5 text-[#087F82] flex-shrink-0"></i>
                            <span>{{ number_format(max(20, $totalActiveQuestions ?? 0)) }}+ Curated MCQs</span>
                        </span>
                        <span class="flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-4.5 h-4.5 text-[#087F82] flex-shrink-0"></i>
                            <span>Step-by-Step Solutions</span>
                        </span>
                        <span class="flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-4.5 h-4.5 text-[#087F82] flex-shrink-0"></i>
                            <span>100% Free Public Access</span>
                        </span>
                    </div>
                </div>

                {{-- Right Visual Showcase: Interactive Aptitude Question Preview Interface --}}
                <div class="lg:col-span-5 flex items-center justify-center">
                    <div class="relative w-full max-w-lg lg:max-w-none xl:max-w-lg">
                        
                        {{-- Soft teal radial gradient accents behind preview card --}}
                        <div data-motion-parallax data-motion-parallax-speed="0.1" class="absolute -top-10 -right-6 w-80 h-80 bg-[#087F82]/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
                        <div data-motion-parallax data-motion-parallax-speed="0.14" data-motion-parallax-dir="up" class="absolute -bottom-10 -left-6 w-72 h-72 bg-[#153B70]/12 rounded-full blur-3xl pointer-events-none -z-10"></div>

                        {{-- 7. Floating Information Element 1: Top-Right (Campus Assessment Ready) --}}
                        <div data-motion="fade-left"
                             data-motion-delay="0.3"
                             class="absolute -top-4 -right-1 xs:-top-5 xs:-right-2 sm:-right-4 bg-white/95 backdrop-blur-md rounded-2xl px-3 py-1.5 sm:px-4 sm:py-2.5 shadow-xl border border-gray-100 flex items-center gap-2 sm:gap-3 z-20 animate-float scale-90 sm:scale-100 origin-top-right">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-teal-50 text-[#087F82] flex items-center justify-center font-bold flex-shrink-0 shadow-2xs">
                                <i data-lucide="briefcase" class="w-4 h-4 sm:w-4.5 sm:h-4.5"></i>
                            </div>
                            <div>
                                <p class="text-[11px] sm:text-xs font-bold text-[#153B70]">Campus Assessment Ready</p>
                                <p class="text-[10px] sm:text-[11px] text-gray-500">TCS, Infosys &amp; Wipro Patterns</p>
                            </div>
                        </div>

                        {{-- 7. Floating Information Element 2: Bottom-Left (Instant Verification) --}}
                        <div data-motion="fade-right"
                             data-motion-delay="0.4"
                             class="absolute -bottom-4 -left-1 xs:-bottom-5 xs:-left-2 sm:-left-4 bg-white/95 backdrop-blur-md rounded-2xl px-3 py-1.5 sm:px-4 sm:py-2.5 shadow-xl border border-gray-100 flex items-center gap-2 sm:gap-3 z-20 animate-float-delayed scale-90 sm:scale-100 origin-bottom-left">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold flex-shrink-0 shadow-2xs">
                                <i data-lucide="trending-up" class="w-4 h-4 sm:w-4.5 sm:h-4.5"></i>
                            </div>
                            <div>
                                <p class="text-[11px] sm:text-xs font-bold text-[#153B70]">Instant Verification</p>
                                <p class="text-[10px] sm:text-[11px] text-gray-500">Formulas &amp; Speed Math</p>
                            </div>
                        </div>

                        {{-- 6. Central Interactive Question Preview Card --}}
                        <div data-motion="scale-up"
                             data-motion-delay="0.18"
                             class="bg-white rounded-3xl shadow-xl shadow-[#153B70]/8 border border-gray-200/90 overflow-hidden relative z-10 transition-all duration-300 hover:shadow-2xl hover:shadow-[#153B70]/12"
                             x-data="{ 
                                 selected: 'B', 
                                 revealed: true,
                                 selectOption(opt) {
                                     this.selected = opt;
                                     this.revealed = true;
                                 }
                             }">
                            
                            {{-- Card Top Header Bar --}}
                            <div class="bg-[#153B70] px-5 sm:px-6 py-3.5 flex items-center justify-between text-white">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#087F82] ring-2 ring-teal-300/40 animate-pulse"></span>
                                    <span class="text-xs font-bold uppercase tracking-wider text-teal-200">Quantitative Aptitude</span>
                                </div>
                                <span class="text-[11px] font-semibold bg-white/15 px-2.5 py-0.5 rounded-full text-white">
                                    Question 1 of 10
                                </span>
                            </div>

                            {{-- Question Body --}}
                            <div class="p-5 sm:p-6 space-y-4">
                                <p class="text-sm sm:text-base font-bold text-[#243247] leading-snug">
                                    A train 150 metres long passes an electric pole in 9 seconds. What is the speed of the train in km/hr?
                                </p>

                                {{-- Four Multiple-Choice Options --}}
                                <div class="space-y-2.5 text-xs sm:text-sm">
                                    {{-- Option A --}}
                                    <div @click="selectOption('A')"
                                         data-interactive-option
                                         class="interactive-mcq-option flex items-center justify-between p-3 rounded-xl border transition-all cursor-pointer"
                                         :class="selected === 'A' ? 'border-rose-300 bg-rose-50/70 text-rose-900 ring-1 ring-rose-200' : 'border-gray-200 bg-gray-50/60 text-gray-700 hover:border-gray-300 hover:bg-gray-100/60'">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-full border border-gray-300 flex items-center justify-center text-xs font-bold text-gray-600 bg-white">A</span>
                                            <span class="font-medium">50 km/hr</span>
                                        </div>
                                        <span x-show="selected === 'A'" class="text-[11px] font-semibold text-rose-600 bg-white px-2 py-0.5 rounded border border-rose-200" x-cloak>Incorrect</span>
                                    </div>

                                    {{-- Option B (Correct Answer Highlighted in Green) --}}
                                    <div @click="selectOption('B')"
                                         data-interactive-option
                                         class="interactive-mcq-option flex items-center justify-between p-3 rounded-xl border-2 border-emerald-500 bg-emerald-50/90 text-emerald-950 font-semibold shadow-xs cursor-pointer ring-2 ring-emerald-500/20">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">
                                                <i data-lucide="check" class="w-3.5 h-3.5 stroke-[3]"></i>
                                            </span>
                                            <span>60 km/hr</span>
                                        </div>
                                        <span class="text-[11px] font-bold text-emerald-700 bg-white px-2.5 py-0.5 rounded-md border border-emerald-200 shadow-2xs">
                                            Correct Answer
                                        </span>
                                    </div>

                                    {{-- Option C --}}
                                    <div @click="selectOption('C')"
                                         data-interactive-option
                                         class="interactive-mcq-option flex items-center justify-between p-3 rounded-xl border transition-all cursor-pointer"
                                         :class="selected === 'C' ? 'border-rose-300 bg-rose-50/70 text-rose-900 ring-1 ring-rose-200' : 'border-gray-200 bg-gray-50/60 text-gray-700 hover:border-gray-300 hover:bg-gray-100/60'">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-full border border-gray-300 flex items-center justify-center text-xs font-bold text-gray-600 bg-white">C</span>
                                            <span class="font-medium">65 km/hr</span>
                                        </div>
                                        <span x-show="selected === 'C'" class="text-[11px] font-semibold text-rose-600 bg-white px-2 py-0.5 rounded border border-rose-200" x-cloak>Incorrect</span>
                                    </div>

                                    {{-- Option D --}}
                                    <div @click="selectOption('D')"
                                         data-interactive-option
                                         class="interactive-mcq-option flex items-center justify-between p-3 rounded-xl border transition-all cursor-pointer"
                                         :class="selected === 'D' ? 'border-rose-300 bg-rose-50/70 text-rose-900 ring-1 ring-rose-200' : 'border-gray-200 bg-gray-50/60 text-gray-700 hover:border-gray-300 hover:bg-gray-100/60'">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-full border border-gray-300 flex items-center justify-center text-xs font-bold text-gray-600 bg-white">D</span>
                                            <span class="font-medium">72 km/hr</span>
                                        </div>
                                        <span x-show="selected === 'D'" class="text-[11px] font-semibold text-rose-600 bg-white px-2 py-0.5 rounded border border-rose-200" x-cloak>Incorrect</span>
                                    </div>
                                </div>

                                {{-- Verified Step-by-Step Solution Panel --}}
                                <div x-show="revealed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="p-3.5 rounded-xl bg-teal-50/80 border border-teal-100 text-xs text-[#243247] leading-relaxed">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <div class="flex items-center gap-1.5 font-bold text-[#087F82]">
                                            <i data-lucide="lightbulb" class="w-4 h-4 text-amber-500"></i>
                                            <span>Verified Step-by-Step Solution:</span>
                                        </div>
                                    </div>
                                    <p class="text-gray-600 text-xs leading-relaxed">
                                        Speed = Distance &divide; Time = 150 / 9 m/s.<br />
                                        To convert to km/hr, multiply by 18/5: <span class="font-bold text-[#153B70]">(150/9) &times; (18/5) = 60 km/hr.</span>
                                    </p>
                                </div>
                            </div>

                            {{-- Chapter Label & Footer Bar --}}
                            <div class="px-5 sm:px-6 py-3 bg-[#F7F9FC] border-t border-gray-100 flex items-center justify-between text-xs text-gray-500 font-medium">
                                <span class="flex items-center gap-1.5 text-[#087F82] font-semibold">
                                    <i data-lucide="shield-check" class="w-4 h-4"></i> AptiMedha Verified
                                </span>
                                <span class="text-gray-500">Time and Distance Chapter</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Platform Statistics Section (100% Genuine Database-Driven Stats) --}}
    <section class="py-8 sm:py-10 bg-white border-b border-gray-200/70">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div data-motion-stagger data-motion-stagger-delay="0.08" class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-6">
                {{-- Stat 1: MCQs --}}
                <div data-motion-item class="bg-[#F7F9FC] p-4 sm:p-6 rounded-2xl border border-gray-200/80 flex flex-col xs:flex-row items-start xs:items-center gap-2.5 sm:gap-4 transition-all hover:border-[#087F82] hover:bg-white hover:shadow-xs group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-teal-50 text-[#087F82] flex items-center justify-center flex-shrink-0 group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                        <i data-lucide="help-circle" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xl sm:text-3xl font-extrabold text-[#153B70] tracking-tight truncate">
                            {{ number_format($totalActiveQuestions ?? 0) }}+
                        </div>
                        <p class="text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider mt-0.5 truncate">Verified MCQs</p>
                    </div>
                </div>

                {{-- Stat 2: Categories --}}
                <div data-motion-item class="bg-[#F7F9FC] p-4 sm:p-6 rounded-2xl border border-gray-200/80 flex flex-col xs:flex-row items-start xs:items-center gap-2.5 sm:gap-4 transition-all hover:border-[#087F82] hover:bg-white hover:shadow-xs group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 text-[#153B70] flex items-center justify-center flex-shrink-0 group-hover:bg-[#087F82] group-hover:text-white transition-colors">
                        <i data-lucide="layers" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xl sm:text-3xl font-extrabold text-[#153B70] tracking-tight truncate">
                            {{ number_format($totalCategories ?? 0) }}
                        </div>
                        <p class="text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider mt-0.5 truncate">Learning Domains</p>
                    </div>
                </div>

                {{-- Stat 3: Subjects --}}
                <div data-motion-item class="bg-[#F7F9FC] p-4 sm:p-6 rounded-2xl border border-gray-200/80 flex flex-col xs:flex-row items-start xs:items-center gap-2.5 sm:gap-4 transition-all hover:border-[#087F82] hover:bg-white hover:shadow-xs group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center flex-shrink-0 group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                        <i data-lucide="book-open" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xl sm:text-3xl font-extrabold text-[#153B70] tracking-tight truncate">
                            {{ number_format($totalSubjects ?? 0) }}
                        </div>
                        <p class="text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider mt-0.5 truncate">Core Subjects</p>
                    </div>
                </div>

                {{-- Stat 4: Topics --}}
                <div data-motion-item class="bg-[#F7F9FC] p-4 sm:p-6 rounded-2xl border border-gray-200/80 flex flex-col xs:flex-row items-start xs:items-center gap-2.5 sm:gap-4 transition-all hover:border-[#087F82] hover:bg-white hover:shadow-xs group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-teal-50 text-[#087F82] flex items-center justify-center flex-shrink-0 group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                        <i data-lucide="tag" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xl sm:text-3xl font-extrabold text-[#153B70] tracking-tight truncate">
                            {{ number_format($totalTopics ?? 0) }}+
                        </div>
                        <p class="text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wider mt-0.5 truncate">Practice Chapters</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- "Choose Your Preparation Domain" Section (Dynamic Active Categories) --}}
    <section class="py-16 sm:py-20 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div data-motion="fade-up" class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#087F82] mb-2">
                        <i data-lucide="compass" class="w-4 h-4"></i>
                        <span>Targeted Practice Tracks</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#153B70] tracking-tight">Choose Your Preparation Domain</h2>
                    <p class="mt-2 text-sm sm:text-base text-[#243247]/70">
                        Structured chapters and topic-wise MCQ sets built for campus assessment and recruitment screening rounds.
                    </p>
                </div>
                <a href="{{ route('categories.index') }}" class="inline-flex items-center text-sm font-bold text-[#087F82] hover:text-[#066668] transition-colors group">
                    <span>Browse All Categories</span>
                    <span class="inline-block transition-transform duration-200 group-hover:translate-x-1 ml-1">&rarr;</span>
                </a>
            </div>

            {{-- 3x3 Domain Cards Grid with Framer Motion Staggered Fade & Slide --}}
            <div data-motion-stagger data-motion-stagger-delay="0.06" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($categories as $cat)
                    @if(is_object($cat) && !empty($cat->slug))
                    <a href="{{ route('categories.show', $cat->slug) }}"
                       data-motion-item
                       class="card-hover-effect flex flex-col justify-between p-6 bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#087F82] group">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-teal-50 text-[#087F82] flex items-center justify-center group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                                    <i data-lucide="{{ $cat->icon ?? 'book-open' }}" class="w-6 h-6"></i>
                                </div>
                                <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-teal-50 text-[#087F82]">
                                    Active Track
                                </span>
                            </div>
                            <h3 class="text-base font-bold text-[#243247] group-hover:text-[#153B70] transition-colors">{{ $cat->name }}</h3>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed line-clamp-2">
                                {{ $cat->description ?? 'Practice topic-wise curated MCQs for campus recruitment tests.' }}
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between text-xs font-semibold text-gray-500">
                            <span class="text-[#087F82]">{{ number_format($cat->questions_count ?? 0) }} {{ Str::plural('Question', $cat->questions_count ?? 0) }}</span>
                            <span class="inline-flex items-center text-[#153B70] group-hover:text-[#087F82] group-hover:translate-x-1 transition-all">
                                Start &rarr;
                            </span>
                        </div>
                    </a>
                    @endif
                @empty
                    <div class="col-span-3 py-12 text-center text-gray-400 bg-white rounded-2xl border border-gray-200">
                        <i data-lucide="folder-tree" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                        <p class="text-sm font-bold text-gray-600">Categories are being organized</p>
                        <p class="text-xs text-gray-400 mt-0.5">Please check back shortly.</p>
                    </div>
                @endforelse

                {{-- Placement Guide Card --}}
                <a href="{{ route('placement.index') }}"
                   data-motion-item
                   class="card-hover-effect flex flex-col justify-between p-6 bg-gradient-to-br from-[#153B70] to-[#087F82] rounded-2xl text-white shadow-md group">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-white/15 text-white flex items-center justify-center">
                                <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                            </div>
                            <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-white/20 text-white">
                                Full Roadmap
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-white">Placement Preparation</h3>
                        <p class="text-xs text-white/80 mt-1 leading-relaxed">
                            HR questions, STAR method responses, technical interview tips, and resume guides.
                        </p>
                    </div>
                    <div class="mt-5 pt-4 border-t border-white/20 flex items-center justify-between text-xs font-semibold">
                        <span class="text-teal-200">Campus to Offer Letter</span>
                        <span class="inline-flex items-center text-white group-hover:translate-x-1 transition-all">
                            Explore Guide &rarr;
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- Industry Advantage / "Why AptiMedha" (4 Core Pillars) --}}
    <section class="py-16 sm:py-20 bg-[#F7F9FC] border-y border-gray-200/70">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div data-motion="fade-up" class="max-w-3xl mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-[#087F82]">Purpose-Built Platform</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#153B70] mt-1">Why Students & Job Aspirants Choose AptiMedha</h2>
                <p class="mt-2 text-sm sm:text-base text-[#243247]/70">
                    Engineered specifically for aptitude mastery with high-clarity solutions and zero distractions.
                </p>
            </div>

            <div data-motion-stagger data-motion-stagger-delay="0.08" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Pillar 1 --}}
                <div data-motion-item class="bg-white p-6 rounded-2xl border border-gray-200/90 shadow-xs card-hover-effect">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 text-[#087F82] flex items-center justify-center mb-4">
                        <i data-lucide="check-check" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-[#153B70] mb-2">Step-by-Step Solutions</h3>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                        Never guess why an answer is right. Every question includes detailed calculations, formulas, and shortcut methods.
                    </p>
                </div>

                {{-- Pillar 2 --}}
                <div data-motion-item class="bg-white p-6 rounded-2xl border border-gray-200/90 shadow-xs card-hover-effect">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#153B70] flex items-center justify-center mb-4">
                        <i data-lucide="target" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-[#153B70] mb-2">Placement-Tested Patterns</h3>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                        Practice questions modeled on actual assessment patterns from TCS NQT, Infosys, Wipro, Accenture, and Cognizant.
                    </p>
                </div>

                {{-- Pillar 3 --}}
                <div data-motion-item class="bg-white p-6 rounded-2xl border border-gray-200/90 shadow-xs card-hover-effect">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 text-[#087F82] flex items-center justify-center mb-4">
                        <i data-lucide="zap" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-[#153B70] mb-2">Distraction-Free UI</h3>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                        Clean typography, rapid option selection, instant solution reveals, and bookmarking designed for focused study.
                    </p>
                </div>

                {{-- Pillar 4 --}}
                <div data-motion-item class="bg-white p-6 rounded-2xl border border-gray-200/90 shadow-xs card-hover-effect">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#153B70] flex items-center justify-center mb-4">
                        <i data-lucide="unlock" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-[#153B70] mb-2">100% Free Public Access</h3>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                        Every topic, subject, and MCQ is immediately accessible to learners worldwide without paywalls or subscriptions.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Animated Marquee: Placement Company Patterns & Student Success Testimonials --}}
    <section class="py-16 bg-white overflow-hidden border-b border-gray-200/70">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mb-8 text-center">
            <span data-motion="fade-up" class="text-xs font-bold uppercase tracking-wider text-[#087F82]">Proven Placement Preparation</span>
            <h2 data-motion="fade-up" data-motion-delay="0.1" class="text-2xl sm:text-3xl font-extrabold text-[#153B70] mt-1">Company Patterns & Aspirant Feedback</h2>
            <p data-motion="fade-up" data-motion-delay="0.15" class="mt-2 text-sm sm:text-base text-[#243247]/70 max-w-2xl mx-auto">
                Practice topic sets calibrated for campus recruitment assessments at leading technology and consulting employers.
            </p>
        </div>

        {{-- Marquee Track 1: Company Assessment Patterns Ticker (Flows Left) --}}
        <div data-motion-marquee data-motion-duration="28" class="motion-marquee-container mb-6 py-2">
            <div class="motion-marquee-track flex items-center gap-4 sm:gap-6 pr-6">
                <div class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-[#F7F9FC] border border-gray-200 text-xs font-bold text-[#153B70] whitespace-nowrap shadow-2xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#087F82]"></span>
                    <span>TCS NQT Foundation & Advanced</span>
                </div>
                <div class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-[#F7F9FC] border border-gray-200 text-xs font-bold text-[#153B70] whitespace-nowrap shadow-2xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#153B70]"></span>
                    <span>Infosys Specialist & DSE Rounds</span>
                </div>
                <div class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-[#F7F9FC] border border-gray-200 text-xs font-bold text-[#153B70] whitespace-nowrap shadow-2xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    <span>Wipro Elite National Talent Hunt</span>
                </div>
                <div class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-[#F7F9FC] border border-gray-200 text-xs font-bold text-[#153B70] whitespace-nowrap shadow-2xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#087F82]"></span>
                    <span>Accenture Cognitive & Technical Assessment</span>
                </div>
                <div class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-[#F7F9FC] border border-gray-200 text-xs font-bold text-[#153B70] whitespace-nowrap shadow-2xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#153B70]"></span>
                    <span>Cognizant GenC & GenC Next</span>
                </div>
                <div class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-[#F7F9FC] border border-gray-200 text-xs font-bold text-[#153B70] whitespace-nowrap shadow-2xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span>Amazon Online Assessment Aptitude</span>
                </div>
                <div class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-[#F7F9FC] border border-gray-200 text-xs font-bold text-[#153B70] whitespace-nowrap shadow-2xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#087F82]"></span>
                    <span>Capgemini Game-Based & Quant</span>
                </div>
                <div class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-[#F7F9FC] border border-gray-200 text-xs font-bold text-[#153B70] whitespace-nowrap shadow-2xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                    <span>Deloitte Campus Aptitude & Verbal</span>
                </div>
            </div>
        </div>

        {{-- Marquee Track 2: Student Testimonials (Flows Right with Pause on Hover) --}}
        <div data-motion-marquee data-motion-direction="reverse" data-motion-duration="44" class="motion-marquee-container py-2">
            <div class="motion-marquee-track flex items-center gap-6 pr-6">
                {{-- Testimonial 1 --}}
                <div class="w-[280px] xs:w-80 sm:w-96 p-4 sm:p-5 rounded-2xl bg-[#F7F9FC] border border-gray-200 shadow-xs flex flex-col justify-between whitespace-normal flex-shrink-0">
                    <div class="flex items-center gap-1 text-amber-400 mb-2.5">
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                    </div>
                    <p class="text-xs sm:text-sm text-[#243247] leading-relaxed mb-4">
                        "The Arithmetic Aptitude solutions with speed math formulas helped me clear TCS NQT in my very first attempt."
                    </p>
                    <div class="border-t border-gray-200/80 pt-3 flex items-center justify-between text-xs">
                        <span class="font-bold text-[#153B70]">Priya Sharma</span>
                        <span class="text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md">Placed at TCS Digital</span>
                    </div>
                </div>

                {{-- Testimonial 2 --}}
                <div class="w-[280px] xs:w-80 sm:w-96 p-4 sm:p-5 rounded-2xl bg-[#F7F9FC] border border-gray-200 shadow-xs flex flex-col justify-between whitespace-normal flex-shrink-0">
                    <div class="flex items-center gap-1 text-amber-400 mb-2.5">
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                    </div>
                    <p class="text-xs sm:text-sm text-[#243247] leading-relaxed mb-4">
                        "Step-by-step explanations for Logical Reasoning and Number Series are so clear. No other platform comes close."
                    </p>
                    <div class="border-t border-gray-200/80 pt-3 flex items-center justify-between text-xs">
                        <span class="font-bold text-[#153B70]">Rahul Verma</span>
                        <span class="text-blue-700 font-semibold bg-blue-50 px-2 py-0.5 rounded-md">Placed at Infosys</span>
                    </div>
                </div>

                {{-- Testimonial 3 --}}
                <div class="w-[280px] xs:w-80 sm:w-96 p-4 sm:p-5 rounded-2xl bg-[#F7F9FC] border border-gray-200 shadow-xs flex flex-col justify-between whitespace-normal flex-shrink-0">
                    <div class="flex items-center gap-1 text-amber-400 mb-2.5">
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                    </div>
                    <p class="text-xs sm:text-sm text-[#243247] leading-relaxed mb-4">
                        "Clean, distraction-free interface without ads or paywalls. Practiced daily for 3 weeks before my campus drive."
                    </p>
                    <div class="border-t border-gray-200/80 pt-3 flex items-center justify-between text-xs">
                        <span class="font-bold text-[#153B70]">Sneha Patel</span>
                        <span class="text-teal-700 font-semibold bg-teal-50 px-2 py-0.5 rounded-md">Placed at Cognizant</span>
                    </div>
                </div>

                {{-- Testimonial 4 --}}
                <div class="w-[280px] xs:w-80 sm:w-96 p-4 sm:p-5 rounded-2xl bg-[#F7F9FC] border border-gray-200 shadow-xs flex flex-col justify-between whitespace-normal flex-shrink-0">
                    <div class="flex items-center gap-1 text-amber-400 mb-2.5">
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                    </div>
                    <p class="text-xs sm:text-sm text-[#243247] leading-relaxed mb-4">
                        "HR interview frameworks and STAR method guides in the Placement Prep section gave me total confidence."
                    </p>
                    <div class="border-t border-gray-200/80 pt-3 flex items-center justify-between text-xs">
                        <span class="font-bold text-[#153B70]">Ankit Deshmukh</span>
                        <span class="text-indigo-700 font-semibold bg-indigo-50 px-2 py-0.5 rounded-md">Placed at Wipro</span>
                    </div>
                </div>

                {{-- Testimonial 5 --}}
                <div class="w-[280px] xs:w-80 sm:w-96 p-4 sm:p-5 rounded-2xl bg-[#F7F9FC] border border-gray-200 shadow-xs flex flex-col justify-between whitespace-normal flex-shrink-0">
                    <div class="flex items-center gap-1 text-amber-400 mb-2.5">
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                    </div>
                    <p class="text-xs sm:text-sm text-[#243247] leading-relaxed mb-4">
                        "Practicing topic-wise programming MCQs in Python and C strengthened my fundamental technical rounds."
                    </p>
                    <div class="border-t border-gray-200/80 pt-3 flex items-center justify-between text-xs">
                        <span class="font-bold text-[#153B70]">Divya Nair</span>
                        <span class="text-[#087F82] font-semibold bg-teal-50 px-2 py-0.5 rounded-md">Placed at Accenture</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- "Latest Placement Guides & Articles" Section --}}
    <section class="py-16 sm:py-20 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div data-motion="fade-up" class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#087F82]">Placement Knowledge</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#153B70] tracking-tight mt-1">Latest Articles & Prep Guides</h2>
                    <p class="mt-2 text-sm sm:text-base text-[#243247]/70">
                        Helpful strategies, exam shortcuts, and interview frameworks for placement readiness.
                    </p>
                </div>
                <a href="{{ route('blog.index') }}" class="inline-flex items-center text-sm font-bold text-[#087F82] hover:text-[#066668] transition-colors group">
                    <span>View All Articles</span>
                    <span class="inline-block transition-transform duration-200 group-hover:translate-x-1 ml-1">&rarr;</span>
                </a>
            </div>

            {{-- Article Cards with Staggered Entrance & Subtle Image Zoom --}}
            <div data-motion-stagger data-motion-stagger-delay="0.1" class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                @forelse($latestBlogs as $blog)
                    @if(is_object($blog) && !empty($blog->slug))
                    <article data-motion-item
                             data-motion-zoom
                             class="image-zoom-card bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#087F82] card-hover-effect flex flex-col justify-between overflow-hidden group">
                        <div>
                            {{-- Image Overlay Container with Clip-Reveal --}}
                            <div data-motion="clip-reveal" class="aspect-[16/10] bg-gradient-to-tr from-[#153B70] to-[#087F82] relative overflow-hidden flex items-end p-5">
                                @if($blog->featured_image_url)
                                    <img src="{{ $blog->featured_image_url }}" alt="{{ $blog->featured_image_alt ?? $blog->title }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover">
                                @endif
                                <div class="absolute inset-0 bg-black/25 group-hover:bg-black/15 transition-colors"></div>
                                <div class="relative z-10 flex items-center justify-between w-full">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white text-[#153B70] shadow-xs">
                                        {{ $blog->category?->name ?? 'Career & Aptitude' }}
                                    </span>
                                    <span class="text-xs font-semibold text-white/90">
                                        {{ $blog->reading_time ?? '5' }} min read
                                    </span>
                                </div>
                            </div>

                            <div class="p-6">
                                <p class="text-xs text-gray-400 font-medium mb-2">
                                    {{ $blog->published_at?->format('F d, Y') ?? 'Recent' }} &bull; {{ $blog->author?->name ?? 'Editorial Team' }}
                                </p>
                                <h3 class="text-base font-bold text-[#243247] group-hover:text-[#153B70] transition-colors line-clamp-2 leading-snug">
                                    <a href="{{ route('blog.show', $blog->slug) }}">
                                        {{ $blog->title }}
                                    </a>
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-500 mt-2.5 line-clamp-2 leading-relaxed">
                                    {{ $blog->excerpt ?? Str::limit(strip_tags($blog->body), 120) }}
                                </p>
                            </div>
                        </div>
                        <div class="p-6 pt-0 border-t border-gray-50">
                            <a href="{{ route('blog.show', $blog->slug) }}"
                               class="inline-flex items-center text-xs font-bold text-[#087F82] group-hover:text-[#066668]">
                                <span>Read Guide</span>
                                <span class="inline-block transition-transform duration-200 group-hover:translate-x-1 ml-1">&rarr;</span>
                            </a>
                        </div>
                    </article>
                    @endif
                @empty
                    <div class="col-span-3 py-12 text-center text-gray-400 bg-white rounded-2xl border border-gray-200">
                        <i data-lucide="file-text" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                        <p class="text-sm font-bold text-gray-600">Articles being drafted</p>
                        <p class="text-xs text-gray-400 mt-0.5">Career guidance articles will appear here once published.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Interactive Call-to-Action Banner with Framer Motion Scale-in --}}
    <section data-motion="scale-up" class="py-16 bg-gradient-to-r from-[#153B70] via-[#18447e] to-[#087F82] text-white relative overflow-hidden">
        {{-- Background grid decoration --}}
        <div class="absolute inset-0 bg-grid-white opacity-20 pointer-events-none"></div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8 text-center lg:text-left">
                <div class="max-w-2xl">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-white/10 text-teal-200 border border-white/20 mb-3">
                        Placement Preparation
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                        Ready to Master Your Aptitude Skills?
                    </h2>
                    <p class="mt-3 text-white/85 text-sm sm:text-base leading-relaxed">
                        Start practising today with comprehensive topic-wise MCQs, instant step-by-step explanations, and verified placement test sets.
                    </p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('mcq.index') }}"
                       data-motion-spring
                       class="px-8 py-3.5 bg-white text-[#153B70] font-bold text-sm rounded-xl shadow-lg hover:bg-gray-100 transition-colors">
                        Start Practicing Free &rarr;
                    </a>
                    <a href="{{ route('register') }}"
                       data-motion-spring
                       class="px-8 py-3.5 bg-[#087F82] text-white font-bold text-sm rounded-xl border border-white/20 hover:bg-[#066668] transition-colors">
                        Create Free Account
                    </a>
                </div>
            </div>
        </div>
    </section>

</x-layouts.app>
