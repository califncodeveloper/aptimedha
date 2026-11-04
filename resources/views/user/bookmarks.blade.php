<x-layouts.user>
    <x-slot:title>My Saved Bookmarks - AptiMedha</x-slot:title>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#243247]">Saved Bookmarks ({{ $bookmarks->total() }})</h1>
                <p class="text-sm text-gray-500 mt-1">Review complex or high-yield aptitude questions you saved for revision.</p>
            </div>
            <a href="{{ route('mcq.index') }}" class="px-4 py-2 bg-[#087F82] text-white text-xs font-bold rounded-xl hover:bg-[#066668] transition-colors self-start sm:self-auto shadow-xs">
                Browse More MCQs
            </a>
        </div>

        {{-- Bookmarks List --}}
        @if ($bookmarks->isNotEmpty())
            <div class="space-y-4">
                @foreach ($bookmarks as $bookmark)
                    @php $q = $bookmark->question; @endphp
                    @if ($q)
                        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 hover:border-gray-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-2 max-w-2xl">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-[#153B70]/10 text-[#153B70]">
                                        {{ $q->categoryName ?: 'General Aptitude' }}
                                    </span>
                                    @if ($q->topicName)
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-teal-50 text-[#087F82]">
                                            {{ $q->topicName }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-400">
                                        Saved {{ $bookmark->created_at ? $bookmark->created_at->diffForHumans() : 'recently' }}
                                    </span>
                                </div>
                                <h3 class="text-base font-bold text-[#243247]">
                                    <a href="{{ route('mcq.show', $q->id) }}" class="hover:text-[#087F82] transition-colors">
                                        {{ $q->question_text }}
                                    </a>
                                </h3>
                                @if ($q->correct_option)
                                    <p class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md inline-block">
                                        Correct Option: Option {{ $q->correct_option }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="{{ route('mcq.show', $q->id) }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-[#087F82] hover:text-white text-[#153B70] text-xs font-bold transition-colors">
                                    Solve Again
                                </a>
                                <form method="POST" action="{{ route('user.bookmarks.destroy', $bookmark->id) }}" onsubmit="return confirm('Remove this question from your saved bookmarks?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Remove Bookmark">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $bookmarks->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-12 text-center">
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
        @endif
    </div>
</x-layouts.user>
