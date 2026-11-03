<x-layouts.app>
    <x-slot:title>Q{{ $mcq->number }}: {{ $mcq->topic_name }} - AptiMedha Practice</x-slot:title>

    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-10"
         x-data="{ 
             selected: null, 
             revealed: false,
             bookmarked: false,
             activeReportModal: false,
             reportSubmitted: false,
             commentText: '',
             discussions: [
                 { name: 'Pooja Nair', date: 'Yesterday', text: 'Important question for campus aptitude tests. Remember the formula: (Change / Initial) * 100.' },
                 { name: 'Kunal Joshi', date: '3 days ago', text: 'Very clear explanation! Thanks AptiMedha.' }
             ],
             isCorrect(opt) { return opt === '{{ $mcq->correct_option }}'; },
             selectOption(opt) {
                 if (!this.revealed) {
                     this.selected = opt;
                     this.revealed = true;
                 }
             }
         }">
        
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-xs sm:text-sm text-gray-500 mb-6">
            <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
            <a href="{{ route('categories.show', $mcq->category_slug ?? 'arithmetic-aptitude') }}" class="hover:text-[#153B70] transition-colors">{{ $mcq->category_name ?? 'Aptitude' }}</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
            <a href="{{ route('mcq.index', ['topic' => $mcq->topic_slug ?? 'percentages']) }}" class="hover:text-[#153B70] transition-colors">{{ $mcq->topic_name ?? 'Percentages' }}</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
            <span class="text-[#243247] font-semibold">Question {{ $mcq->number }}</span>
        </nav>

        {{-- Main Question Card --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-sm overflow-hidden">
            
            {{-- Header Badge & Metadata --}}
            <div class="px-6 sm:px-8 py-5 border-b border-gray-200/80 bg-gray-50/70 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-[#153B70] text-white">
                        Question #{{ $mcq->number }}
                    </span>
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-[#087F82]/10 text-[#087F82]">
                        {{ $mcq->topic_name ?? 'Aptitude' }}
                    </span>
                </div>

                <div class="flex items-center space-x-2">
                    <button @click="bookmarked = !bookmarked" 
                            class="p-2 rounded-lg text-gray-400 hover:text-[#087F82] hover:bg-white transition-colors"
                            :class="{ 'text-amber-500': bookmarked }"
                            title="Bookmark Question">
                        <i data-lucide="bookmark" class="w-5 h-5" :class="{ 'fill-amber-400 text-amber-500': bookmarked }"></i>
                    </button>
                    <button @click="activeReportModal = true" 
                            class="p-2 rounded-lg text-gray-400 hover:text-rose-500 hover:bg-white transition-colors"
                            title="Report Question">
                        <i data-lucide="flag" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            {{-- Question Text --}}
            <div class="p-6 sm:p-8">
                <h1 class="text-lg sm:text-xl font-bold text-[#243247] leading-relaxed mb-6">
                    {{ $mcq->question }}
                </h1>

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
                        Correct answer: {{ $mcq->correct_option }}. {{ $mcq->options[$mcq->correct_option] }}
                    </p>
                </div>

                {{-- Step-by-Step Explanation --}}
                <div x-show="revealed" x-transition class="mt-4 p-5 rounded-xl bg-[#F7F9FC] border border-gray-200 text-sm text-[#243247] leading-relaxed" x-cloak>
                    <div class="flex items-center gap-2 font-bold text-[#153B70] mb-2">
                        <i data-lucide="lightbulb" class="w-5 h-5 text-amber-500"></i>
                        <span>Detailed Explanation:</span>
                    </div>
                    <p class="text-gray-700 font-medium pl-7">
                        {{ $mcq->explanation }}
                    </p>
                </div>

                {{-- Bottom Action Row --}}
                <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-between">
                    <button type="button"
                            @click="revealed = !revealed"
                            class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-xs font-bold text-[#153B70] flex items-center gap-2 transition-colors">
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
        <div class="mt-10 bg-white rounded-2xl border border-gray-200/90 shadow-sm p-6 sm:p-8">
            <h3 class="text-lg font-bold text-[#153B70] flex items-center gap-2 mb-6">
                <i data-lucide="message-square" class="w-5 h-5 text-[#087F82]"></i>
                Discussion & Queries (<span x-text="discussions.length"></span>)
            </h3>

            {{-- Post Comment Form --}}
            <div class="mb-8 p-4 bg-gray-50 rounded-xl border border-gray-200/80">
                <label class="block text-xs font-bold text-gray-700 mb-2">Have a doubt or alternative method? Share here:</label>
                <textarea x-model="commentText" rows="3" placeholder="Write your discussion comment or question..." class="w-full text-sm border border-gray-200 rounded-lg p-3 bg-white focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none"></textarea>
                <div class="mt-2 flex justify-end">
                    <button @click="if(commentText.trim()){ discussions.unshift({name: 'You', date: 'Just now', text: commentText}); commentText = ''; }"
                            type="button" 
                            class="px-5 py-2 bg-[#087F82] text-white text-xs font-bold rounded-lg hover:bg-[#066668] transition-colors shadow-xs">
                        Post Comment
                    </button>
                </div>
            </div>

            {{-- Discussion List --}}
            <div class="space-y-4">
                <template x-for="item in discussions">
                    <div class="p-4 rounded-xl border border-gray-100 bg-white hover:border-gray-200 transition-all">
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-bold text-[#153B70]" x-text="item.name"></span>
                            <span class="text-gray-400" x-text="item.date"></span>
                        </div>
                        <p class="text-sm text-gray-700" x-text="item.text"></p>
                    </div>
                </template>
            </div>
        </div>

    </div>
</x-layouts.app>
