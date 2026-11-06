<x-layouts.app>
    <x-slot:title>{{ \App\Services\SeoService::topicTitle($topic ?? $topicName, $category) }}</x-slot:title>
    <x-slot:metaDescription>{{ \App\Services\SeoService::topicDescription($topic ?? $topicName, $category) }}</x-slot:metaDescription>
    <x-slot:canonicalUrl>{{ $canonicalUrl ?? ($topic && $category ? route('categories.topics.show', ['category' => $category->slug, 'topic' => $topic->slug]) : ($category ? route('categories.show', $category->slug) : route('mcq.index'))) }}</x-slot:canonicalUrl>
    @if(!empty($topic))
        <x-slot:schema>{!! \App\Services\SchemaService::toJsonLd(\App\Services\SchemaService::forTopic($topic, $category)) !!}</x-slot:schema>
    @endif

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8"
         x-data="{ 
             activeDiscussionModal: null, 
             activeReportModal: null,
             reportSubmitted: false,
             reportErrorMessage: '',
             isSubmittingReport: false,
             reportIssueType: 'incorrect_answer',
             reportNotes: '',
             bookmarkedIds: {{ json_encode($bookmarkedIds ?? []) }},
             isBookmarked(id) {
                 return this.bookmarkedIds.includes(id);
             },
             toggleBookmark(id) {
                 @guest
                     window.location.href = '{{ route('login') }}';
                     return;
                 @else
                     fetch('{{ route('user.bookmarks.toggle') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                             'Accept': 'application/json'
                         },
                         body: JSON.stringify({ question_id: id })
                     })
                     .then(res => res.json())
                     .then(data => {
                         if (data.bookmarked) {
                             if (!this.bookmarkedIds.includes(id)) this.bookmarkedIds.push(id);
                         } else {
                             this.bookmarkedIds = this.bookmarkedIds.filter(i => i !== id);
                         }
                     })
                     .catch(err => console.error(err));
                 @endguest
             },
             openReportModal(id) {
                 this.activeReportModal = id;
                 this.reportSubmitted = false;
                 this.reportErrorMessage = '';
                 this.reportNotes = '';
             },
             submitReport() {
                 @guest
                     window.location.href = '{{ route('login') }}';
                     return;
                 @else
                     if (!this.activeReportModal) return;
                     this.isSubmittingReport = true;
                     this.reportErrorMessage = '';
                     fetch('{{ route('user.reports.store') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                             'Accept': 'application/json'
                         },
                         body: JSON.stringify({
                             question_id: this.activeReportModal,
                             issue_type: this.reportIssueType,
                             description: this.reportNotes
                         })
                     })
                     .then(async res => {
                         const data = await res.json();
                         if (!res.ok) {
                             throw new Error(data.message || 'Failed to submit report.');
                         }
                         return data;
                     })
                     .then(data => {
                         this.reportSubmitted = true;
                         this.reportNotes = '';
                     })
                     .catch(err => {
                         this.reportErrorMessage = err.message;
                     })
                     .finally(() => {
                         this.isSubmittingReport = false;
                     });
                 @endguest
             }
         }">

        {{-- Breadcrumb matching mockup: Home / Category / Topic --}}
        <nav class="flex items-center space-x-2 text-xs sm:text-sm text-gray-500 mb-6">
            <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
            @if($categorySlug)
                <a href="{{ route('categories.show', $categorySlug) }}" class="hover:text-[#153B70] transition-colors">{{ $categoryName }}</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
            @endif
            <span class="text-[#243247] font-semibold">{{ $topicName }}</span>
        </nav>

        {{-- 2-Column Practice Layout matching Mockup --}}
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            {{-- Left Sidebar matching mockup --}}
            <aside class="w-full lg:w-72 flex-shrink-0" x-data="{ mobileTopicMenu: false }">
                <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden sticky top-24">
                    <div class="px-5 py-4 border-b border-gray-200/80 bg-gray-50/70 flex items-center justify-between cursor-pointer lg:cursor-default"
                         @click="mobileTopicMenu = !mobileTopicMenu">
                        <div class="flex-1">
                            <h2 class="text-base font-bold text-[#153B70] flex items-center justify-between">
                                <span>{{ $categoryName }}</span>
                                <span class="text-xs font-normal text-gray-400">{{ $sidebarTopics->count() }} Topics</span>
                            </h2>
                            <p class="text-[11px] text-[#087F82] font-semibold mt-0.5 lg:hidden">
                                Current: {{ $topicName }} (Tap to change)
                            </p>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500 lg:hidden ml-2 transition-transform duration-200"
                           :class="{ 'rotate-180': mobileTopicMenu }"></i>
                    </div>

                    <div class="p-2 space-y-1 max-h-[calc(100vh-220px)] overflow-y-auto" :class="{ 'hidden lg:block': !mobileTopicMenu }">
                        @forelse($sidebarTopics as $sTopic)
                            <a href="{{ $categorySlug ? route('categories.topics.show', ['category' => $categorySlug, 'topic' => $sTopic->slug]) : route('mcq.index', ['topic' => $sTopic->slug]) }}"
                               @class([
                                   'flex items-center justify-between px-4 py-3 rounded-xl text-sm font-semibold transition-all',
                                   'bg-[#087F82] text-white shadow-xs' => $sTopic->slug === $topicSlug,
                                   'text-[#243247] hover:bg-gray-100/80 hover:text-[#087F82]' => $sTopic->slug !== $topicSlug,
                               ])>
                                <span class="truncate mr-2">{{ $sTopic->name }}</span>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <span class="text-xs px-2 py-0.5 rounded-md {{ $sTopic->slug === $topicSlug ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $sTopic->questions_count }}
                                    </span>
                                    <i data-lucide="chevron-right" class="w-4 h-4 {{ $sTopic->slug === $topicSlug ? 'text-white' : 'text-gray-400' }}"></i>
                                </div>
                            </a>
                        @empty
                            <div class="p-4 text-center text-xs text-gray-400">
                                No topics in this category yet.
                            </div>
                        @endforelse
                    </div>

                    @if($categorySlug)
                        <div class="p-4 border-t border-gray-100 bg-[#F7F9FC]" :class="{ 'hidden lg:block': !mobileTopicMenu }">
                            <a href="{{ route('categories.show', $categorySlug) }}"
                               class="inline-flex items-center text-xs font-bold text-[#153B70] hover:text-[#087F82] transition-colors">
                                <i data-lucide="grid" class="w-3.5 h-3.5 mr-1.5"></i>
                                View All Categories
                            </a>
                        </div>
                    @endif
                </div>
            </aside>

            {{-- Main Content Area matching mockup --}}
            <main class="flex-1 min-w-0 w-full space-y-6">
                
                {{-- Header & Section Selector --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-gray-200/70">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#153B70] tracking-tight">
                            {{ $topicName }} MCQ
                        </h1>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                            Step-by-step verified aptitude solutions for placement assessments.
                        </p>
                    </div>

                    @if($mcqs->isNotEmpty())
                        <div class="flex items-center gap-3">
                            {{-- Section Pill matching mockup --}}
                            <div class="inline-flex items-center gap-2">
                                <span class="px-3.5 py-1.5 rounded-lg bg-[#087F82] text-white text-xs font-bold shadow-xs">
                                    Section {{ $currentSection }}
                                </span>
                                <span class="text-xs font-semibold text-gray-500">
                                    Questions {{ $mcqs->firstItem() }} – {{ $mcqs->lastItem() }} of {{ $totalQuestions }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Empty State when no questions exist in topic --}}
                @if($mcqs->isEmpty())
                    <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-12 text-center space-y-3">
                        <div class="w-14 h-14 rounded-full bg-slate-100 text-[#153B70] flex items-center justify-center mx-auto">
                            <i data-lucide="book-open" class="w-7 h-7"></i>
                        </div>
                        <h3 class="text-lg font-bold text-[#153B70]">No Questions in This Topic Yet</h3>
                        <p class="text-xs sm:text-sm text-gray-500 max-w-md mx-auto">
                            Questions for <span class="font-semibold text-[#153B70]">{{ $topicName }}</span> are currently being authored and verified. Please select another topic from the sidebar.
                        </p>
                    </div>
                @else
                    {{-- Questions List (10 questions per page) --}}
                    <div data-motion-stagger data-motion-stagger-delay="0.05" class="space-y-6">
                        @foreach($mcqs as $mcq)
                            <div data-motion-item
                                 class="bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-gray-300 transition-all overflow-hidden"
                                 x-data="{ 
                                     selected: null, 
                                     revealed: false,
                                     get isAnswered() { return this.selected !== null || this.revealed; },
                                     isCorrect(opt) { return opt === '{{ $mcq->correct_option }}'; },
                                     selectOption(opt) {
                                         if (!this.revealed) {
                                             this.selected = opt;
                                             this.revealed = true;
                                         }
                                     },
                                     toggleShowAnswer() {
                                         this.revealed = !this.revealed;
                                     }
                                 }">
                                
                                {{-- Question Body --}}
                                <div class="p-4 sm:p-7">
                                    
                                    {{-- Question Header with Number and Actions --}}
                                    <div class="flex items-start justify-between gap-3 sm:gap-4 mb-4 sm:mb-5">
                                        <h3 class="text-base sm:text-lg font-bold text-[#243247] leading-relaxed break-words">
                                            <span class="text-[#153B70] mr-1">{{ $mcq->question_number }}.</span> {{ $mcq->question_text }}
                                        </h3>

                                        {{-- Quick Actions: Bookmark --}}
                                        <div class="flex items-center space-x-1 flex-shrink-0">
                                            <button @click="toggleBookmark({{ $mcq->id }})" 
                                                    type="button"
                                                    class="p-2 rounded-lg text-gray-400 hover:text-[#087F82] hover:bg-gray-50 transition-colors min-h-[40px] min-w-[40px] flex items-center justify-center"
                                                    :class="{ 'text-amber-500': isBookmarked({{ $mcq->id }}) }"
                                                    aria-label="Bookmark Question"
                                                    title="Bookmark Question">
                                                <i data-lucide="bookmark" class="w-5 h-5" :class="{ 'fill-amber-400 text-amber-500': isBookmarked({{ $mcq->id }}) }"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Optional Question Image --}}
                                    @if($mcq->has_image && $mcq->image_url)
                                        <div class="mb-5 rounded-xl border border-gray-200 overflow-hidden max-w-full sm:max-w-md bg-gray-50 p-2">
                                            <img src="{{ $mcq->image_url }}"
                                                 alt="{{ $mcq->question_image_alt ?: ('Question ' . $mcq->question_number . ' Diagram') }}"
                                                 loading="lazy"
                                                 onerror="this.parentElement.style.display='none'"
                                                 class="w-full h-auto rounded-lg object-contain">
                                        </div>
                                    @endif

                                    {{-- Four Selectable Options --}}
                                    <div class="space-y-3">
                                        @foreach($mcq->options as $optKey => $optVal)
                                            <button type="button"
                                                    @click="selectOption('{{ $optKey }}')"
                                                    :disabled="revealed"
                                                    class="interactive-mcq-option w-full min-h-[48px] flex items-center justify-between px-3.5 sm:px-5 py-3 sm:py-3.5 rounded-xl border-2 text-left transition-all duration-200 cursor-pointer disabled:cursor-default"
                                                    :class="{
                                                        'border-emerald-500 bg-emerald-50/70 ring-2 ring-emerald-500/20': revealed && isCorrect('{{ $optKey }}'),
                                                        'border-rose-400 bg-rose-50/70 ring-2 ring-rose-400/20': revealed && selected === '{{ $optKey }}' && !isCorrect('{{ $optKey }}'),
                                                        'border-gray-200 hover:border-gray-300 hover:bg-gray-50/80': !revealed
                                                    }">
                                                
                                                <div class="flex items-start sm:items-center gap-3 sm:gap-3.5 min-w-0 flex-1">
                                                    {{-- Radio circle indicator matching mockup --}}
                                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 text-xs font-bold transition-colors mt-0.5 sm:mt-0"
                                                         :class="{
                                                             'border-emerald-500 bg-emerald-500 text-white': revealed && isCorrect('{{ $optKey }}'),
                                                             'border-rose-500 bg-rose-500 text-white': revealed && selected === '{{ $optKey }}' && !isCorrect('{{ $optKey }}'),
                                                             'border-gray-300 text-gray-500': !revealed
                                                         }">
                                                        <span x-show="!revealed || (!isCorrect('{{ $optKey }}') && selected !== '{{ $optKey }}')">{{ $optKey }}</span>
                                                        <i x-show="revealed && isCorrect('{{ $optKey }}')" data-lucide="check" class="w-3.5 h-3.5 stroke-[3]"></i>
                                                        <i x-show="revealed && selected === '{{ $optKey }}' && !isCorrect('{{ $optKey }}')" data-lucide="x" class="w-3.5 h-3.5 stroke-[3]"></i>
                                                    </div>

                                                    <div class="space-y-1.5 min-w-0">
                                                        <span class="text-sm sm:text-base font-medium text-[#243247] break-words block">
                                                            <span class="font-bold text-gray-400 mr-1.5">{{ $optKey }}.</span> {{ $optVal }}
                                                        </span>
                                                        @if($mcq->hasOptionImage($optKey))
                                                            <div class="rounded-lg border border-gray-200/90 bg-white p-1 max-w-[180px] overflow-hidden">
                                                                <img src="{{ $mcq->getOptionImageUrl($optKey) }}"
                                                                     alt="Option {{ $optKey }} Diagram"
                                                                     loading="lazy"
                                                                     onerror="this.parentElement.style.display='none'"
                                                                     class="max-h-20 w-auto object-contain">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Correct Checkmark Badge on far right matching mockup --}}
                                                <div x-show="revealed && isCorrect('{{ $optKey }}')" class="text-emerald-600 flex-shrink-0 ml-2" x-cloak>
                                                    <i data-lucide="check" class="w-5 h-5 stroke-[2.5]"></i>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>

                                    {{-- Correct Answer Banner matching mockup --}}
                                    <div x-show="revealed" x-transition class="mt-4 p-3.5 sm:p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-2.5 sm:gap-3 text-emerald-800" x-cloak>
                                        <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center flex-shrink-0">
                                            <i data-lucide="check" class="w-3.5 h-3.5 stroke-[3]"></i>
                                        </div>
                                        <p class="text-xs sm:text-sm font-bold break-words">
                                            Correct answer: {{ $mcq->correct_option }}. {{ $mcq->options[$mcq->correct_option] ?? '' }}
                                        </p>
                                    </div>

                                    {{-- Step-by-Step Explanation & Explanation Diagram --}}
                                    @if($mcq->explanation || $mcq->has_explanation_image)
                                        <div x-show="revealed" x-transition class="mt-3 p-3.5 sm:p-5 rounded-xl bg-[#F7F9FC] border border-gray-200/90 text-xs sm:text-sm text-[#243247] leading-relaxed break-words space-y-2.5" x-cloak>
                                            <div class="flex items-center gap-2 font-bold text-[#153B70]">
                                                <i data-lucide="lightbulb" class="w-4 h-4 text-amber-500"></i>
                                                <span>Explanation:</span>
                                            </div>
                                            @if($mcq->explanation)
                                                <p class="text-gray-700 font-medium pl-2 sm:pl-6 whitespace-pre-line break-words">
                                                    {{ $mcq->explanation }}
                                                </p>
                                            @endif
                                            @if($mcq->has_explanation_image && $mcq->explanation_image_url)
                                                <div class="pl-2 sm:pl-6">
                                                    <div class="rounded-xl border border-gray-200 overflow-hidden bg-white p-2 max-w-full sm:max-w-sm">
                                                        <img src="{{ $mcq->explanation_image_url }}"
                                                             alt="Explanation Diagram for Question {{ $mcq->question_number }}"
                                                             loading="lazy"
                                                             onerror="this.parentElement.style.display='none'"
                                                             class="w-full h-auto rounded-lg object-contain">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                </div>

                                {{-- Question Footer Bar (Actions: Show Answer, Bookmark, Discuss, Report) --}}
                                <div class="px-4 sm:px-7 py-3 bg-gray-50/70 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2.5 text-xs">
                                    
                                    {{-- Show Answer toggle button --}}
                                    <button type="button"
                                            @click="toggleShowAnswer()"
                                            class="inline-flex items-center font-bold px-3 py-2 rounded-lg border border-gray-300 hover:bg-white text-[#153B70] transition-colors cursor-pointer min-h-[38px]">
                                        <i data-lucide="eye" class="w-4 h-4 mr-1.5 text-[#087F82]"></i>
                                        <span x-text="revealed ? 'Hide Answer' : 'Show Answer'"></span>
                                    </button>

                                    {{-- Secondary Action Buttons: Discuss Here & Report --}}
                                    <div class="flex items-center gap-1.5 sm:gap-2">
                                        <a href="{{ route('mcq.show', $mcq->id) }}"
                                           class="inline-flex items-center px-2.5 sm:px-3 py-2 rounded-lg text-gray-600 hover:text-[#087F82] hover:bg-white transition-colors font-medium min-h-[38px]">
                                            <i data-lucide="message-square" class="w-4 h-4 mr-1.5"></i>
                                            <span>Discuss <span class="hidden xs:inline">Here</span> ({{ $mcq->discussions_count }})</span>
                                        </a>

                                        <button type="button"
                                                @click="openReportModal({{ $mcq->id }})"
                                                class="inline-flex items-center px-2.5 py-2 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors font-medium min-h-[38px]"
                                                title="Report Error in Question">
                                            <i data-lucide="flag" class="w-4 h-4 mr-1"></i>
                                            <span>Report</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination matching client mockup --}}
                    @if($mcqs->hasPages())
                        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <p class="text-xs text-gray-500 font-medium text-center sm:text-left">
                                Showing questions <span class="font-bold text-[#153B70]">{{ $mcqs->firstItem() }} – {{ $mcqs->lastItem() }}</span> of <span class="font-bold text-[#153B70]">{{ $mcqs->total() }}</span>
                            </p>

                            <nav class="inline-flex items-center gap-1 sm:gap-1.5 flex-wrap justify-center" aria-label="Pagination">
                                @if ($mcqs->onFirstPage())
                                    <span class="inline-flex items-center px-2.5 sm:px-3 h-9 rounded-lg bg-gray-100 text-gray-400 font-bold text-xs cursor-not-allowed">
                                        &larr; Prev
                                    </span>
                                @else
                                    <a href="{{ $mcqs->previousPageUrl() }}" class="inline-flex items-center px-2.5 sm:px-3 h-9 rounded-lg bg-white border border-gray-200 text-[#243247] hover:border-[#087F82] font-bold text-xs transition-colors">
                                        &larr; Prev
                                    </a>
                                @endif

                                {{-- Compact mobile indicator when on small screens --}}
                                <span class="sm:hidden px-3 py-1.5 text-xs font-bold text-[#153B70] bg-gray-100 rounded-lg">
                                    {{ $mcqs->currentPage() }} / {{ $mcqs->lastPage() }}
                                </span>

                                {{-- Desktop page buttons --}}
                                <div class="hidden sm:inline-flex items-center gap-1.5">
                                    @foreach ($mcqs->getUrlRange(max(1, $mcqs->currentPage() - 2), min($mcqs->lastPage(), $mcqs->currentPage() + 2)) as $page => $url)
                                        @if ($page == $mcqs->currentPage())
                                            <span class="w-9 h-9 rounded-lg bg-[#087F82] text-white font-bold text-sm flex items-center justify-center shadow-xs">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $url }}" class="w-9 h-9 rounded-lg bg-white border border-gray-200 text-[#243247] hover:border-[#087F82] font-semibold text-sm flex items-center justify-center transition-colors">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>

                                @if ($mcqs->hasMorePages())
                                    <a href="{{ $mcqs->nextPageUrl() }}" class="inline-flex items-center px-2.5 sm:px-3 h-9 rounded-lg bg-white border border-gray-200 text-[#243247] hover:border-[#087F82] font-bold text-xs transition-colors">
                                        Next &rarr;
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-2.5 sm:px-3 h-9 rounded-lg bg-gray-100 text-gray-400 font-bold text-xs cursor-not-allowed">
                                        Next &rarr;
                                    </span>
                                @endif
                            </nav>
                        </div>
                    @endif
                @endif
            </main>
        </div>

        {{-- Interactive Report Modal --}}
        <div x-show="activeReportModal !== null" 
             class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 flex items-center justify-center"
             x-cloak>
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="activeReportModal = null"></div>

            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden z-10">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h4 class="text-base font-bold text-[#153B70] flex items-center gap-2">
                        <i data-lucide="flag" class="w-4 h-4 text-rose-500"></i>
                        Report Question Issue (Q<span x-text="activeReportModal"></span>)
                    </h4>
                    <button @click="activeReportModal = null" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="p-6">
                    @guest
                        <div class="text-center py-4 space-y-4">
                            <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
                                <i data-lucide="lock" class="w-6 h-6"></i>
                            </div>
                            <h5 class="text-base font-bold text-[#153B70]">Authentication Required</h5>
                            <p class="text-xs text-gray-500">
                                Please sign in to submit discrepancy reports and help keep our practice questions accurate.
                            </p>
                            <div class="pt-2 flex items-center justify-center gap-3">
                                <button @click="activeReportModal = null" type="button" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                                <a href="{{ route('login') }}" class="px-5 py-2 text-xs font-bold bg-[#153B70] text-white rounded-lg hover:bg-[#0e2a52]">
                                    Sign In
                                </a>
                            </div>
                        </div>
                    @else
                        <div x-show="!reportSubmitted" class="space-y-4">
                            <div x-show="reportErrorMessage" class="p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs" x-text="reportErrorMessage" x-cloak></div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Issue Category <span class="text-rose-500">*</span></label>
                                <select x-model="reportIssueType" class="w-full text-sm border border-gray-200 rounded-xl p-2.5 focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82]">
                                    <option value="incorrect_answer">Incorrect Answer Marked</option>
                                    <option value="incorrect_explanation">Incorrect Explanation / Steps</option>
                                    <option value="wrong_question">Incorrect Question</option>
                                    <option value="broken_image">Missing Image or Figure</option>
                                    <option value="duplicate_question">Duplicate Question</option>
                                    <option value="typo_formatting">Typo or Formatting Issue</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Description (Optional)</label>
                                <textarea x-model="reportNotes" rows="3" placeholder="Explain what is incorrect and your suggested correction..." class="w-full text-sm border border-gray-200 rounded-xl p-3 focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none"></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2">
                                <button @click="activeReportModal = null" type="button" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                                <button @click="submitReport()" :disabled="isSubmittingReport" type="button" class="px-4 py-2 text-xs font-bold bg-rose-600 text-white hover:bg-rose-700 rounded-lg shadow-xs disabled:opacity-50">
                                    <span x-show="!isSubmittingReport">Submit Report</span>
                                    <span x-show="isSubmittingReport" x-cloak>Submitting...</span>
                                </button>
                            </div>
                        </div>

                        <div x-show="reportSubmitted" class="text-center py-6 space-y-3" x-cloak>
                            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
                                <i data-lucide="check-circle" class="w-6 h-6"></i>
                            </div>
                            <h5 class="text-base font-bold text-[#153B70]">Report Submitted</h5>
                            <p class="text-xs text-gray-500 max-w-xs mx-auto">
                                Thank you! Our editorial team reviews every report and will verify the calculation.
                            </p>
                            <button @click="activeReportModal = null" type="button" class="mt-2 px-5 py-2 text-xs font-bold bg-[#153B70] text-white rounded-lg">
                                Close
                            </button>
                        </div>
                    @endguest
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
