<x-layouts.user>
    <x-slot:title>My Discussions - AptiMedha</x-slot:title>

    <div class="space-y-6" x-data="{
        editingId: null,
        editingComment: '',
        openEdit(id, text) {
            this.editingId = id;
            this.editingComment = text;
        },
        closeEdit() {
            this.editingId = null;
            this.editingComment = '';
        }
    }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#243247]">Discussion Posts ({{ $discussions->total() }})</h1>
                <p class="text-sm text-gray-500 mt-1">Review your queries, solution tips, and discussion threads.</p>
            </div>
            <a href="{{ route('mcq.index') }}" class="px-4 py-2 bg-[#087F82] text-white text-xs font-bold rounded-xl hover:bg-[#066668] transition-colors self-start sm:self-auto shadow-xs">
                Join Community Discussions
            </a>
        </div>

        @if ($discussions->isNotEmpty())
            <div class="space-y-4">
                @foreach ($discussions as $disc)
                    @php $q = $disc->question; @endphp
                    <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-md font-bold bg-[#153B70]/10 text-[#153B70]">
                                    {{ $q?->categoryName ?: 'General Aptitude' }}
                                </span>
                                @if ($q?->topicName)
                                    <span class="text-gray-400">&bull;</span>
                                    <span class="font-semibold text-gray-600">Q#{{ $q->id }} ({{ $q->topicName }})</span>
                                @endif
                            </div>
                            <span class="text-gray-400">{{ $disc->created_at->diffForHumans() }}</span>
                        </div>

                        @if ($q)
                            <h3 class="text-base font-bold text-[#243247]">
                                <a href="{{ route('mcq.show', $q->id) }}" class="hover:text-[#087F82] transition-colors">
                                    "{{ $q->question_text }}"
                                </a>
                            </h3>
                        @endif

                        {{-- Comment view or edit state --}}
                        <div x-show="editingId !== {{ $disc->id }}" class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 text-xs sm:text-sm text-gray-700">
                            <p class="font-bold text-[#153B70] text-xs mb-1">Your Comment:</p>
                            <p class="whitespace-pre-line">{{ $disc->comment }}</p>
                        </div>

                        {{-- Inline Edit Form --}}
                        <div x-show="editingId === {{ $disc->id }}" x-cloak class="p-3.5 bg-teal-50/40 rounded-xl border border-[#087F82]/30 space-y-3">
                            <form method="POST" action="{{ route('user.discussions.update', $disc->id) }}">
                                @csrf
                                @method('PUT')
                                <label class="block text-xs font-bold text-[#153B70] mb-1">Edit Your Comment</label>
                                <textarea name="comment" x-model="editingComment" rows="3" required minlength="3" maxlength="2000"
                                    class="w-full text-xs sm:text-sm border border-gray-300 rounded-lg p-2.5 bg-white focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none"></textarea>
                                <div class="flex items-center gap-2 mt-2 justify-end">
                                    <button type="button" @click="closeEdit()" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-[#087F82] text-white hover:bg-[#066668]">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
                            <div class="flex items-center gap-3">
                                @if ($disc->status !== 'approved')
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">
                                        {{ ucfirst($disc->status) }}
                                    </span>
                                @endif
                                @if ($q)
                                    <a href="{{ route('mcq.show', $q->id) }}#discussions" class="font-bold text-[#087F82] hover:underline flex items-center gap-1">
                                        View in Thread &rarr;
                                    </a>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" @click="openEdit({{ $disc->id }}, @js($disc->comment))"
                                    class="px-2.5 py-1 text-xs font-semibold text-gray-600 hover:text-[#087F82] hover:bg-teal-50 rounded-lg transition-colors min-h-[36px] flex items-center">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5 inline mr-1"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('user.discussions.destroy', $disc->id) }}" onsubmit="return confirm('Delete this comment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-lg transition-colors min-h-[36px] flex items-center">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5 inline mr-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $discussions->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-12 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="message-square" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-[#153B70]">No Discussion Posts Yet</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">
                    Ask questions, share alternate solving shortcuts, or collaborate with peers on any question page.
                </p>
                <a href="{{ route('mcq.index') }}" class="mt-4 inline-block px-5 py-2.5 bg-[#087F82] text-white text-xs font-bold rounded-xl hover:bg-[#066668]">
                    Explore Practice Questions
                </a>
            </div>
        @endif
    </div>
</x-layouts.user>
