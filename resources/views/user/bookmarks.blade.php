<x-layouts.user>
    <x-slot:title>My Saved Bookmarks - AptiMedha</x-slot:title>

    <div class="space-y-6" x-data="{
        bookmarks: [
            {
                id: 1,
                number: 1,
                topic: 'Percentages',
                category: 'Arithmetic Aptitude',
                question: 'What is 25% of 240?',
                answer: 'C. 60',
                date: 'Saved 2 days ago'
            },
            {
                id: 2,
                number: 4,
                topic: 'Percentages',
                category: 'Arithmetic Aptitude',
                question: 'The price of sugar increases by 20%. By what percentage must a household reduce its consumption so as not to increase the expenditure on sugar?',
                answer: 'A. 16.67%',
                date: 'Saved 3 days ago'
            },
            {
                id: 3,
                number: 8,
                topic: 'Percentages',
                category: 'Arithmetic Aptitude',
                question: 'If the radius of a circle is increased by 50%, its area is increased by:',
                answer: 'B. 125%',
                date: 'Saved last week'
            }
        ],
        removeBookmark(id) {
            this.bookmarks = this.bookmarks.filter(b => b.id !== id);
        }
    }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#243247]">Saved Bookmarks (<span x-text="bookmarks.length"></span>)</h1>
                <p class="text-sm text-gray-500 mt-1">Review complex or high-yield aptitude questions you saved for final revision.</p>
            </div>
            <a href="{{ route('mcq.index') }}" class="px-4 py-2 bg-[#087F82] text-white text-xs font-bold rounded-xl hover:bg-[#066668] transition-colors self-start sm:self-auto shadow-xs">
                Browse More MCQs
            </a>
        </div>

        {{-- Bookmarks List --}}
        <div class="space-y-4">
            <template x-for="item in bookmarks" :key="item.id">
                <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 hover:border-gray-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-2 max-w-2xl">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-[#153B70]/10 text-[#153B70]" x-text="item.category"></span>
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-teal-50 text-[#087F82]" x-text="item.topic"></span>
                            <span class="text-xs text-gray-400" x-text="item.date"></span>
                        </div>
                        <h3 class="text-base font-bold text-[#243247]" x-text="item.question"></h3>
                        <p class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md inline-block">
                            Verified Answer: <span x-text="item.answer"></span>
                        </p>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a :href="'{{ url('mcq') }}/' + item.number" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-[#087F82] hover:text-white text-[#153B70] text-xs font-bold transition-colors">
                            Solve Again
                        </a>
                        <button @click="removeBookmark(item.id)" type="button" class="p-2 rounded-xl text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Remove Bookmark">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </template>

            <div x-show="bookmarks.length === 0" class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-12 text-center" x-cloak>
                <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="bookmark" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-[#153B70]">No Saved Bookmarks</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">
                    While solving MCQs, click the bookmark icon on any question to save it here for fast revision.
                </p>
                <a href="{{ route('mcq.index') }}" class="mt-4 inline-block px-5 py-2.5 bg-[#087F82] text-white text-xs font-bold rounded-xl hover:bg-[#066668]">
                    Explore Practice Questions
                </a>
            </div>
        </div>
    </div>
</x-layouts.user>
