<x-layouts.app>
    <x-slot:title>{{ $topicName }} MCQ Practice - AptiMedha</x-slot:title>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8"
         x-data="{ 
             activeDiscussionModal: null, 
             activeReportModal: null,
             reportSubmitted: false,
             discussionComment: '',
             sampleDiscussions: {
                 1: [
                     { user: 'Rohit Sharma', date: '2 days ago', comment: 'Very straightforward! Remember 25% is just dividing by 4.' },
                     { user: 'Sneha Patel', date: 'Yesterday', comment: 'Helped me in TCS NQT quantitative section.' }
                 ],
                 2: [
                     { user: 'Amit Verma', date: '3 days ago', comment: 'Always remember to divide by the INITIAL value (80), not the final value (100).' }
                 ]
             }
         }">

        {{-- Breadcrumb matching mockup: Home / Arithmetic Aptitude / Percentages --}}
        <nav class="flex items-center space-x-2 text-xs sm:text-sm text-gray-500 mb-6">
            <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
            <a href="{{ route('categories.show', $categorySlug) }}" class="hover:text-[#153B70] transition-colors">{{ $categoryName }}</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
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
                                <span class="text-xs font-normal text-gray-400">10 Topics</span>
                            </h2>
                            <p class="text-[11px] text-[#087F82] font-semibold mt-0.5 lg:hidden">
                                Current: {{ $topicName }} (Tap to change)
                            </p>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500 lg:hidden ml-2 transition-transform duration-200"
                           :class="{ 'rotate-180': mobileTopicMenu }"></i>
                    </div>

                    <div class="p-2 space-y-1" :class="{ 'hidden lg:block': !mobileTopicMenu }">
                        @foreach($sidebarTopics as $topic)
                            <a href="{{ route('mcq.index', ['category' => $categorySlug, 'topic' => $topic->slug]) }}"
                               @class([
                                   'flex items-center justify-between px-4 py-3 rounded-xl text-sm font-semibold transition-all',
                                   'bg-[#087F82] text-white shadow-xs' => $topic->slug === $topicSlug,
                                   'text-[#243247] hover:bg-gray-100/80 hover:text-[#087F82]' => $topic->slug !== $topicSlug,
                               ])>
                                <span>{{ $topic->name }}</span>
                                <i data-lucide="chevron-right" class="w-4 h-4 {{ $topic->slug === $topicSlug ? 'text-white' : 'text-gray-400' }}"></i>
                            </a>
                        @endforeach
                    </div>

                    <div class="p-4 border-t border-gray-100 bg-[#F7F9FC]" :class="{ 'hidden lg:block': !mobileTopicMenu }">
                        <a href="{{ route('categories.show', $categorySlug) }}"
                           class="inline-flex items-center text-xs font-bold text-[#153B70] hover:text-[#087F82] transition-colors">
                            <i data-lucide="grid" class="w-3.5 h-3.5 mr-1.5"></i>
                            View All Categories
                        </a>
                    </div>
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

                    <div class="flex items-center gap-3">
                        {{-- Section Pill matching mockup --}}
                        <div class="inline-flex items-center gap-2">
                            <button class="px-3.5 py-1.5 rounded-lg bg-[#087F82] text-white text-xs font-bold shadow-xs">
                                Section 1
                            </button>
                            <span class="text-xs font-semibold text-gray-500">
                                Questions 1 – 10 of {{ $totalQuestions }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Questions List (10 questions per page) --}}
                <div class="space-y-6">
                    @foreach($mcqs as $mcq)
                        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-gray-300 transition-all overflow-hidden"
                             x-data="{ 
                                 selected: null, 
                                 revealed: false,
                                 bookmarked: false,
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
                            <div class="p-6 sm:p-7">
                                
                                {{-- Question Header with Number and Actions --}}
                                <div class="flex items-start justify-between gap-4 mb-5">
                                    <h3 class="text-base sm:text-lg font-bold text-[#243247] leading-relaxed">
                                        <span class="text-[#153B70] mr-1">{{ $mcq->number }}.</span> {{ $mcq->question }}
                                    </h3>

                                    {{-- Quick Actions: Bookmark --}}
                                    <div class="flex items-center space-x-1 flex-shrink-0">
                                        <button @click="bookmarked = !bookmarked" 
                                                type="button"
                                                class="p-2 rounded-lg text-gray-400 hover:text-[#087F82] hover:bg-gray-50 transition-colors"
                                                :class="{ 'text-amber-500': bookmarked }"
                                                title="Bookmark Question">
                                            <i data-lucide="bookmark" class="w-5 h-5" :class="{ 'fill-amber-400 text-amber-500': bookmarked }"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Optional Question Image --}}
                                @if($mcq->has_image && $mcq->image_url)
                                    <div class="mb-5 rounded-xl border border-gray-200 overflow-hidden max-w-md bg-gray-50 p-2">
                                        <img src="{{ $mcq->image_url }}" alt="Question {{ $mcq->number }} Diagram" class="w-full h-auto rounded-lg">
                                    </div>
                                @endif

                                {{-- Four Selectable Options --}}
                                <div class="space-y-3">
                                    @foreach($mcq->options as $optKey => $optVal)
                                        <button type="button"
                                                @click="selectOption('{{ $optKey }}')"
                                                :disabled="revealed"
                                                class="w-full flex items-center justify-between px-4 sm:px-5 py-3.5 rounded-xl border-2 text-left transition-all duration-200 cursor-pointer disabled:cursor-default"
                                                :class="{
                                                    'border-emerald-500 bg-emerald-50/70 ring-2 ring-emerald-500/20': revealed && isCorrect('{{ $optKey }}'),
                                                    'border-rose-400 bg-rose-50/70 ring-2 ring-rose-400/20': revealed && selected === '{{ $optKey }}' && !isCorrect('{{ $optKey }}'),
                                                    'border-gray-200 hover:border-gray-300 hover:bg-gray-50/80': !revealed
                                                }">
                                            
                                            <div class="flex items-center gap-3.5 min-w-0">
                                                {{-- Radio circle indicator matching mockup --}}
                                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 text-xs font-bold transition-colors"
                                                     :class="{
                                                         'border-emerald-500 bg-emerald-500 text-white': revealed && isCorrect('{{ $optKey }}'),
                                                         'border-rose-500 bg-rose-500 text-white': revealed && selected === '{{ $optKey }}' && !isCorrect('{{ $optKey }}'),
                                                         'border-gray-300 text-gray-500': !revealed
                                                     }">
                                                    <span x-show="!revealed || (!isCorrect('{{ $optKey }}') && selected !== '{{ $optKey }}')">{{ $optKey }}</span>
                                                    <i x-show="revealed && isCorrect('{{ $optKey }}')" data-lucide="check" class="w-3.5 h-3.5 stroke-[3]"></i>
                                                    <i x-show="revealed && selected === '{{ $optKey }}' && !isCorrect('{{ $optKey }}')" data-lucide="x" class="w-3.5 h-3.5 stroke-[3]"></i>
                                                </div>

                                                <span class="text-sm sm:text-base font-medium text-[#243247]">
                                                    <span class="font-bold text-gray-400 mr-1.5">{{ $optKey }}.</span> {{ $optVal }}
                                                </span>
                                            </div>

                                            {{-- Correct Checkmark Badge on far right matching mockup --}}
                                            <div x-show="revealed && isCorrect('{{ $optKey }}')" class="text-emerald-600 flex-shrink-0 ml-2" x-cloak>
                                                <i data-lucide="check" class="w-5 h-5 stroke-[2.5]"></i>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>

                                {{-- Correct Answer Banner matching mockup --}}
                                <div x-show="revealed" x-transition class="mt-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-emerald-800" x-cloak>
                                    <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="check" class="w-3.5 h-3.5 stroke-[3]"></i>
                                    </div>
                                    <p class="text-sm font-bold">
                                        Correct answer: {{ $mcq->correct_option }}. {{ $mcq->options[$mcq->correct_option] }}
                                    </p>
                                </div>

                                {{-- Step-by-Step Explanation matching mockup --}}
                                <div x-show="revealed" x-transition class="mt-3 p-4 sm:p-5 rounded-xl bg-[#F7F9FC] border border-gray-200/90 text-sm text-[#243247] leading-relaxed" x-cloak>
                                    <div class="flex items-center gap-2 font-bold text-[#153B70] mb-1.5">
                                        <i data-lucide="lightbulb" class="w-4 h-4 text-amber-500"></i>
                                        <span>Explanation:</span>
                                    </div>
                                    <p class="text-gray-700 font-medium pl-6">
                                        {{ $mcq->explanation }}
                                    </p>
                                </div>

                            </div>

                            {{-- Question Footer Bar (Actions: Show Answer, Bookmark, Discuss, Report) --}}
                            <div class="px-6 sm:px-7 py-3.5 bg-gray-50/70 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                                
                                {{-- Show Answer toggle button --}}
                                <button type="button"
                                        @click="toggleShowAnswer()"
                                        class="inline-flex items-center font-bold px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-white text-[#153B70] transition-colors">
                                    <i data-lucide="eye" class="w-3.5 h-3.5 mr-1.5 text-[#087F82]"></i>
                                    <span x-text="revealed ? 'Hide Answer' : 'Show Answer'"></span>
                                </button>

                                {{-- Secondary Action Buttons: Discuss Here & Report --}}
                                <div class="flex items-center space-x-2">
                                    <button type="button"
                                            @click="activeDiscussionModal = {{ $mcq->id }}"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-gray-600 hover:text-[#087F82] hover:bg-white transition-colors font-medium">
                                        <i data-lucide="message-square" class="w-3.5 h-3.5 mr-1.5"></i>
                                        Discuss Here ({{ $mcq->discussions_count }})
                                    </button>

                                    <button type="button"
                                            @click="activeReportModal = {{ $mcq->id }}; reportSubmitted = false;"
                                            class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors font-medium"
                                            title="Report Error in Question">
                                        <i data-lucide="flag" class="w-3.5 h-3.5 mr-1"></i>
                                        Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination matching client mockup: 1, 2, 3, 4, 5 ... 10 Next -> --}}
                <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-gray-500 font-medium">
                        Showing questions <span class="font-bold text-[#153B70]">1 – 10</span> of <span class="font-bold text-[#153B70]">150</span>
                    </p>

                    <nav class="inline-flex items-center gap-1.5" aria-label="Pagination">
                        <button class="w-9 h-9 rounded-lg bg-[#087F82] text-white font-bold text-sm flex items-center justify-center shadow-xs">
                            1
                        </button>
                        <a href="{{ route('mcq.index', ['page' => 2]) }}" class="w-9 h-9 rounded-lg bg-white border border-gray-200 text-[#243247] hover:border-[#087F82] font-semibold text-sm flex items-center justify-center transition-colors">
                            2
                        </a>
                        <a href="{{ route('mcq.index', ['page' => 3]) }}" class="w-9 h-9 rounded-lg bg-white border border-gray-200 text-[#243247] hover:border-[#087F82] font-semibold text-sm flex items-center justify-center transition-colors">
                            3
                        </a>
                        <a href="{{ route('mcq.index', ['page' => 4]) }}" class="w-9 h-9 rounded-lg bg-white border border-gray-200 text-[#243247] hover:border-[#087F82] font-semibold text-sm flex items-center justify-center transition-colors">
                            4
                        </a>
                        <a href="{{ route('mcq.index', ['page' => 5]) }}" class="w-9 h-9 rounded-lg bg-white border border-gray-200 text-[#243247] hover:border-[#087F82] font-semibold text-sm flex items-center justify-center transition-colors">
                            5
                        </a>
                        <span class="px-2 text-gray-400 font-bold">...</span>
                        <a href="{{ route('mcq.index', ['page' => 10]) }}" class="w-9 h-9 rounded-lg bg-white border border-gray-200 text-[#243247] hover:border-[#087F82] font-semibold text-sm flex items-center justify-center transition-colors">
                            10
                        </a>
                        <a href="{{ route('mcq.index', ['page' => 2]) }}" class="inline-flex items-center px-4 h-9 rounded-lg bg-white border border-gray-200 text-[#243247] hover:border-[#087F82] font-bold text-xs transition-colors ml-1">
                            Next &rarr;
                        </a>
                    </nav>
                </div>
            </main>
        </div>

        {{-- Interactive Discussion Modal --}}
        <div x-show="activeDiscussionModal !== null" 
             class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 flex items-center justify-center"
             x-cloak>
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="activeDiscussionModal = null"></div>

            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden z-10">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h4 class="text-base font-bold text-[#153B70] flex items-center gap-2">
                        <i data-lucide="message-square" class="w-5 h-5 text-[#087F82]"></i>
                        Community Discussion (Question <span x-text="activeDiscussionModal"></span>)
                    </h4>
                    <button @click="activeDiscussionModal = null" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4 max-h-96 overflow-y-auto">
                    <template x-for="item in (sampleDiscussions[activeDiscussionModal] || [{ user: 'Community Member', date: 'Just now', comment: 'Ask your questions or share tips on this MCQ below.' }])">
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-bold text-[#153B70]" x-text="item.user"></span>
                                <span class="text-gray-400" x-text="item.date"></span>
                            </div>
                            <p class="text-sm text-gray-700" x-text="item.comment"></p>
                        </div>
                    </template>

                    <div class="pt-2 border-t border-gray-100">
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Post your comment or doubt:</label>
                        <textarea x-model="discussionComment" rows="3" placeholder="Share alternative solutions, shortcuts, or ask for clarification..." class="w-full text-sm border border-gray-200 rounded-xl p-3 focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none"></textarea>
                        <div class="mt-2 flex justify-end">
                            <button @click="if(discussionComment.trim()) { if(!sampleDiscussions[activeDiscussionModal]) sampleDiscussions[activeDiscussionModal]=[]; sampleDiscussions[activeDiscussionModal].push({user: 'You', date: 'Just now', comment: discussionComment}); discussionComment = ''; }" type="button" class="px-4 py-2 bg-[#087F82] text-white text-xs font-bold rounded-lg hover:bg-[#066668]">
                                Post Comment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Interactive Report Modal --}}
        <div x-show="activeReportModal !== null" 
             class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 flex items-center justify-center"
             x-cloak>
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="activeReportModal = null"></div>

            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden z-10" x-data="{ issueType: 'Wrong Answer', reportNotes: '' }">
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
                    <div x-show="!reportSubmitted" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Issue Category</label>
                            <select x-model="issueType" class="w-full text-sm border border-gray-200 rounded-xl p-2.5 focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82]">
                                <option value="Wrong Answer">Incorrect Answer Marked</option>
                                <option value="Explanation Error">Error in Explanation / Steps</option>
                                <option value="Typo / Grammar">Typographical or Grammar Error</option>
                                <option value="Missing Information">Incomplete Question or Diagram</option>
                                <option value="Duplicate">Duplicate Question</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Describe the Error</label>
                            <textarea x-model="reportNotes" rows="3" placeholder="Explain what is incorrect and your suggested correction..." class="w-full text-sm border border-gray-200 rounded-xl p-3 focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button @click="activeReportModal = null" type="button" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                            <button @click="reportSubmitted = true" type="button" class="px-4 py-2 text-xs font-bold bg-rose-600 text-white hover:bg-rose-700 rounded-lg shadow-xs">
                                Submit Report
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
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
