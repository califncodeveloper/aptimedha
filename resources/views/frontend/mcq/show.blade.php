<x-layouts.app>
    <x-slot:title>Q{{ $mcq->question_number }}: {{ $mcq->topic?->name }} - AptiMedha Practice</x-slot:title>

    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-10"
         x-data="{ 
             selected: null, 
             revealed: false,
             bookmarked: {{ $isBookmarked ? 'true' : 'false' }},
             activeReportModal: false,
             reportSubmitted: {{ $hasPendingReport ? 'true' : 'false' }},
             reportErrorMessage: '',
             isSubmittingReport: false,
             reportIssueType: 'incorrect_answer',
             reportNotes: '',
             editingDiscussionId: null,
             editingDiscussionText: '',
             replyingToId: null,
             replyText: '',
             isCorrect(opt) { return opt === '{{ $mcq->correct_option }}'; },
             selectOption(opt) {
                 if (!this.revealed) {
                     this.selected = opt;
                     this.revealed = true;
                 }
             },
             toggleBookmark() {
                 @guest
                     window.location.href = '{{ route('login') }}';
                     return;
                 @else
                     fetch('{{ route('user.bookmarks.toggle', $mcq->id) }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                             'Accept': 'application/json'
                         }
                     })
                     .then(res => res.json())
                     .then(data => {
                         this.bookmarked = data.bookmarked;
                     })
                     .catch(err => console.error(err));
                 @endguest
             },
             openReportModal() {
                 this.activeReportModal = true;
                 this.reportErrorMessage = '';
             },
             submitReport() {
                 @guest
                     window.location.href = '{{ route('login') }}';
                     return;
                 @else
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
                             question_id: {{ $mcq->id }},
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
        
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if (session('warning'))
            <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs sm:text-sm flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            </div>
        @endif

        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-xs sm:text-sm text-gray-500 mb-6">
            <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
            @if($mcq->category)
                <a href="{{ route('categories.show', $mcq->category->slug) }}" class="hover:text-[#153B70] transition-colors">{{ $mcq->category->name }}</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
            @endif
            @if($mcq->topic)
                <a href="{{ route('mcq.index', ['category' => $mcq->category?->slug, 'topic' => $mcq->topic->slug]) }}" class="hover:text-[#153B70] transition-colors">{{ $mcq->topic->name }}</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
            @endif
            <span class="text-[#243247] font-semibold">Question {{ $mcq->question_number }}</span>
        </nav>

        {{-- Main Question Card --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-sm overflow-hidden">
            
            {{-- Header Badge & Metadata --}}
            <div class="px-6 sm:px-8 py-5 border-b border-gray-200/80 bg-gray-50/70 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-[#153B70] text-white">
                        Question #{{ $mcq->question_number }}
                    </span>
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-[#087F82]/10 text-[#087F82]">
                        {{ $mcq->topic?->name ?? 'Aptitude' }}
                    </span>
                    @if($mcq->section_number > 1)
                        <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-gray-200 text-gray-700">
                            Section {{ $mcq->section_number }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center text-xs text-gray-500 font-medium px-2.5 py-1 bg-white rounded-lg border border-gray-200/80 shadow-2xs" title="Total Views">
                        <i data-lucide="eye" class="w-3.5 h-3.5 mr-1 text-[#087F82]"></i>
                        {{ number_format($mcq->views_count) }}
                    </span>
                    <button @click="toggleBookmark()" 
                            class="p-2 rounded-lg text-gray-400 hover:text-[#087F82] hover:bg-white transition-colors"
                            :class="{ 'text-amber-500': bookmarked }"
                            title="Bookmark Question">
                        <i data-lucide="bookmark" class="w-5 h-5" :class="{ 'fill-amber-400 text-amber-500': bookmarked }"></i>
                    </button>
                    <button @click="openReportModal()" 
                            class="p-2 rounded-lg text-gray-400 hover:text-rose-500 hover:bg-white transition-colors"
                            title="Report Question">
                        <i data-lucide="flag" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            {{-- Question Text --}}
            <div class="p-6 sm:p-8">
                <h1 class="text-lg sm:text-xl font-bold text-[#243247] leading-relaxed mb-6">
                    {{ $mcq->question_text }}
                </h1>

                {{-- Optional Diagram / Image --}}
                @if($mcq->has_image && $mcq->image_url)
                    <div class="mb-6 rounded-xl border border-gray-200 overflow-hidden max-w-lg bg-gray-50 p-2">
                        <img src="{{ $mcq->image_url }}" alt="Question {{ $mcq->question_number }} Diagram" class="w-full h-auto rounded-lg">
                    </div>
                @endif

                {{-- Selectable Options --}}
                <div class="space-y-3.5">
                    @foreach($mcq->options as $optKey => $optVal)
                        <button type="button"
                                @click="selectOption('{{ $optKey }}')"
                                :disabled="revealed"
                                class="w-full flex items-center justify-between px-5 py-4 rounded-xl border-2 text-left transition-all duration-200 cursor-pointer disabled:cursor-default"
                                :class="{
                                    'border-emerald-500 bg-emerald-50/70 ring-2 ring-emerald-500/20': revealed && isCorrect('{{ $optKey }}'),
                                    'border-rose-400 bg-rose-50/70 ring-2 ring-rose-400/20': revealed && selected === '{{ $optKey }}' && !isCorrect('{{ $optKey }}'),
                                    'border-gray-200 hover:border-gray-300 hover:bg-gray-50/80': !revealed
                                }">
                            
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center flex-shrink-0 text-xs font-bold transition-colors"
                                     :class="{
                                         'border-emerald-500 bg-emerald-500 text-white': revealed && isCorrect('{{ $optKey }}'),
                                         'border-rose-500 bg-rose-500 text-white': revealed && selected === '{{ $optKey }}' && !isCorrect('{{ $optKey }}'),
                                         'border-gray-300 text-gray-500': !revealed
                                     }">
                                    <span x-show="!revealed || (!isCorrect('{{ $optKey }}') && selected !== '{{ $optKey }}')">{{ $optKey }}</span>
                                    <i x-show="revealed && isCorrect('{{ $optKey }}')" data-lucide="check" class="w-4 h-4 stroke-[3]"></i>
                                    <i x-show="revealed && selected === '{{ $optKey }}' && !isCorrect('{{ $optKey }}')" data-lucide="x" class="w-4 h-4 stroke-[3]"></i>
                                </div>
                                <span class="text-base font-medium text-[#243247]">
                                    <span class="font-bold text-gray-400 mr-2">{{ $optKey }}.</span> {{ $optVal }}
                                </span>
                            </div>

                            <div x-show="revealed && isCorrect('{{ $optKey }}')" class="text-emerald-600 flex-shrink-0 ml-2" x-cloak>
                                <i data-lucide="check" class="w-6 h-6 stroke-[2.5]"></i>
                            </div>
                        </button>
                    @endforeach
                </div>

                {{-- Correct Answer Banner --}}
                <div x-show="revealed" x-transition class="mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-emerald-800" x-cloak>
                    <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center flex-shrink-0">
                        <i data-lucide="check" class="w-3.5 h-3.5 stroke-[3]"></i>
                    </div>
                    <p class="text-sm font-bold">
                        Correct answer: {{ $mcq->correct_option }}. {{ $mcq->options[$mcq->correct_option] ?? '' }}
                    </p>
                </div>

                {{-- Step-by-Step Explanation --}}
                @if($mcq->explanation)
                    <div x-show="revealed" x-transition class="mt-4 p-5 rounded-xl bg-[#F7F9FC] border border-gray-200 text-sm text-[#243247] leading-relaxed" x-cloak>
                        <div class="flex items-center gap-2 font-bold text-[#153B70] mb-2">
                            <i data-lucide="lightbulb" class="w-5 h-5 text-amber-500"></i>
                            <span>Detailed Explanation:</span>
                        </div>
                        <p class="text-gray-700 font-medium pl-7 whitespace-pre-line">
                            {{ $mcq->explanation }}
                        </p>
                    </div>
                @endif

                {{-- Bottom Action Row --}}
                <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-between">
                    <button type="button"
                            @click="revealed = !revealed"
                            class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-xs font-bold text-[#153B70] flex items-center gap-2 transition-colors cursor-pointer">
                        <i data-lucide="eye" class="w-4 h-4 text-[#087F82]"></i>
                        <span x-text="revealed ? 'Hide Answer' : 'Show Answer'"></span>
                    </button>

                    <div class="flex items-center space-x-3">
                        @if(isset($previousMcq))
                            <a href="{{ route('mcq.show', $previousMcq->id) }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 text-xs font-bold text-[#153B70] hover:bg-gray-200 transition-colors">
                                &larr; Previous
                            </a>
                        @endif
                        @if(isset($nextMcq))
                            <a href="{{ route('mcq.show', $nextMcq->id) }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#087F82] text-xs font-bold text-white hover:bg-[#066668] transition-colors">
                                Next Question &rarr;
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- Community Discussion Thread --}}
        <div id="discussions" class="mt-10 bg-white rounded-2xl border border-gray-200/90 shadow-sm p-6 sm:p-8">
            <h3 class="text-lg font-bold text-[#153B70] flex items-center gap-2 mb-6">
                <i data-lucide="message-square" class="w-5 h-5 text-[#087F82]"></i>
                Discussion & Queries ({{ $discussions->count() }})
            </h3>

            {{-- Post Comment Form --}}
            @auth
                <div class="mb-8 p-5 bg-gray-50 rounded-xl border border-gray-200/80">
                    <form method="POST" action="{{ route('user.discussions.store') }}">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $mcq->id }}">
                        <label for="comment" class="block text-xs font-bold text-gray-700 mb-2">
                            Have a doubt, alternate method, or shortcut? Share with peers:
                        </label>
                        <textarea id="comment" name="comment" rows="3" required minlength="3" maxlength="2000"
                            placeholder="Write your explanation or question..."
                            class="w-full text-sm border border-gray-200 rounded-lg p-3 bg-white focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none"></textarea>
                        <div class="mt-2.5 flex items-center justify-between">
                            <span class="text-[11px] text-gray-400">Be respectful and focus on constructive learning discussions.</span>
                            <button type="submit" 
                                    class="px-5 py-2 bg-[#087F82] text-white text-xs font-bold rounded-lg hover:bg-[#066668] transition-colors shadow-xs">
                                Post Comment
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="mb-8 p-5 bg-teal-50/50 rounded-xl border border-teal-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h4 class="text-sm font-bold text-[#153B70]">Join the Discussion</h4>
                        <p class="text-xs text-gray-600 mt-0.5">Please sign in to post questions, answer doubts, or suggest formulas.</p>
                    </div>
                    <a href="{{ route('login') }}" class="px-5 py-2 bg-[#153B70] text-white text-xs font-bold rounded-lg hover:bg-[#0e2a52] transition-colors whitespace-nowrap self-start sm:self-auto">
                        Sign In to Discuss
                    </a>
                </div>
            @endauth

            {{-- Discussion List --}}
            <div class="space-y-4">
                @forelse ($discussions as $disc)
                    <div class="p-4 rounded-xl border border-gray-100 bg-white hover:border-gray-200 transition-all space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-[#153B70]">{{ $disc->user?->name ?? 'Learner' }}</span>
                                @if ($disc->user?->isAdmin())
                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-[#153B70] text-white">Admin</span>
                                @endif
                                <span class="text-gray-400">&bull;</span>
                                <span class="text-gray-400">{{ $disc->created_at->diffForHumans() }}</span>
                            </div>

                            @if (Auth::id() === $disc->user_id)
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="editingDiscussionId = {{ $disc->id }}; editingDiscussionText = '{{ addslashes($disc->comment) }}';"
                                        class="text-xs text-gray-500 hover:text-[#087F82]">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('user.discussions.destroy', $disc->id) }}" onsubmit="return confirm('Delete this comment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-500 hover:text-rose-700">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        {{-- Comment view or edit state --}}
                        <div x-show="editingDiscussionId !== {{ $disc->id }}">
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $disc->comment }}</p>
                        </div>

                        {{-- Inline Edit Form --}}
                        <div x-show="editingDiscussionId === {{ $disc->id }}" x-cloak class="p-3 bg-teal-50/40 rounded-lg border border-[#087F82]/30 space-y-2">
                            <form method="POST" action="{{ route('user.discussions.update', $disc->id) }}">
                                @csrf
                                @method('PUT')
                                <textarea name="comment" x-model="editingDiscussionText" rows="2" required minlength="3" maxlength="2000"
                                    class="w-full text-xs sm:text-sm border border-gray-300 rounded-lg p-2.5 bg-white focus:border-[#087F82] focus:outline-none"></textarea>
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" @click="editingDiscussionId = null" class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-3 py-1 text-xs font-bold bg-[#087F82] text-white rounded hover:bg-[#066668]">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Replies if any --}}
                        @if ($disc->replies && $disc->replies->isNotEmpty())
                            <div class="mt-3 pl-4 border-l-2 border-teal-200 space-y-2">
                                @foreach ($disc->replies as $reply)
                                    <div class="p-2.5 bg-gray-50 rounded-lg text-xs space-y-1">
                                        <div class="flex items-center justify-between text-gray-500">
                                            <span class="font-bold text-[#153B70]">{{ $reply->user?->name ?? 'Learner' }}</span>
                                            <span>{{ $reply->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-700 whitespace-pre-line">{{ $reply->comment }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-6">No discussions yet. Be the first to share your approach!</p>
                @endforelse
            </div>
        </div>

        {{-- Interactive Report Modal --}}
        <div x-show="activeReportModal" 
             class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 flex items-center justify-center"
             x-cloak>
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="activeReportModal = false"></div>

            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden z-10">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h4 class="text-base font-bold text-[#153B70] flex items-center gap-2">
                        <i data-lucide="flag" class="w-4 h-4 text-rose-500"></i>
                        Report Question (Q#{{ $mcq->question_number }})
                    </h4>
                    <button @click="activeReportModal = false" class="text-gray-400 hover:text-gray-600">
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
                                Please sign in to submit discrepancy reports and help maintain question accuracy.
                            </p>
                            <div class="pt-2 flex items-center justify-center gap-3">
                                <button @click="activeReportModal = false" type="button" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
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
                                <button @click="activeReportModal = false" type="button" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
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
                            <button @click="activeReportModal = false" type="button" class="mt-2 px-5 py-2 text-xs font-bold bg-[#153B70] text-white rounded-lg">
                                Close
                            </button>
                        </div>
                    @endguest
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
