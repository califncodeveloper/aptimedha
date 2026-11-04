<x-layouts.user>
    <x-slot:title>Learner Dashboard - AptiMedha</x-slot:title>

    <div class="space-y-8">
        {{-- Welcome Header with Grid Overlay --}}
        <div class="bg-gradient-to-r from-[#153B70] via-[#1a4a8a] to-[#087F82] rounded-3xl p-6 sm:p-8 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 shadow-md relative overflow-hidden">
            <div class="absolute inset-0 bg-grid-white opacity-15 pointer-events-none"></div>
            <div class="relative z-10">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-teal-100 mb-3 border border-white/20 backdrop-blur-xs">
                    Placement Season 2026
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Welcome back, {{ Auth::user()->name }}! 👋
                </h1>
                <p class="text-xs sm:text-sm text-white/85 mt-1.5 max-w-xl leading-relaxed">
                    Track your saved bookmarks, community discussions, and question correction reports all in one place.
                </p>
            </div>
            <a href="{{ route('mcq.index') }}" class="relative z-10 px-6 py-3 rounded-xl bg-white text-[#153B70] font-bold text-xs hover:bg-gray-100 transition-all shadow-xs hover:-translate-y-0.5 whitespace-nowrap">
                Continue Practice &rarr;
            </a>
        </div>

        {{-- Metrics Overview (Real Database Stats) --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white p-5 rounded-2xl border border-gray-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Saved Bookmarks</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-[#153B70] mt-1">{{ $bookmarksCount }}</p>
                    <a href="{{ route('user.bookmarks') }}" class="text-[11px] font-semibold text-[#087F82] hover:underline mt-1 inline-block">
                        View all bookmarks &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-xl bg-teal-50 text-[#087F82] flex items-center justify-center flex-shrink-0">
                    <i data-lucide="bookmark" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Discussion Posts</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-[#153B70] mt-1">{{ $discussionsCount }}</p>
                    <a href="{{ route('user.discussions') }}" class="text-[11px] font-semibold text-[#087F82] hover:underline mt-1 inline-block">
                        View all discussions &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#153B70] flex items-center justify-center flex-shrink-0">
                    <i data-lucide="message-square" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Submitted Reports</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-[#153B70] mt-1">{{ $reportsCount }}</p>
                    <a href="{{ route('user.reports') }}" class="text-[11px] font-semibold text-[#087F82] hover:underline mt-1 inline-block">
                        View report statuses &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="flag" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        {{-- Real User Activity Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Recent Bookmarks --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="bookmark" class="w-4 h-4 text-[#087F82]"></i>
                        <h2 class="text-base font-bold text-[#153B70]">Recent Bookmarks</h2>
                    </div>
                    <a href="{{ route('user.bookmarks') }}" class="text-xs font-bold text-[#087F82] hover:underline">
                        View All ({{ $bookmarksCount }})
                    </a>
                </div>

                @if ($recentBookmarks->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($recentBookmarks as $bm)
                            @php $q = $bm->question; @endphp
                            @if ($q)
                                <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-teal-50/20 hover:border-[#087F82]/30 transition-all flex items-start justify-between gap-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2 text-[11px]">
                                            <span class="font-bold text-[#153B70]">{{ $q->categoryName ?: 'Aptitude' }}</span>
                                            @if ($q->topicName)
                                                <span class="text-gray-400">&bull;</span>
                                                <span class="text-gray-600">{{ $q->topicName }}</span>
                                            @endif
                                        </div>
                                        <h3 class="text-xs sm:text-sm font-semibold text-[#243247] line-clamp-2">
                                            <a href="{{ route('mcq.show', $q->id) }}" class="hover:text-[#087F82]">
                                                {{ $q->question_text }}
                                            </a>
                                        </h3>
                                    </div>
                                    <a href="{{ route('mcq.show', $q->id) }}" class="px-2.5 py-1 text-xs font-bold text-[#087F82] hover:bg-teal-50 rounded-lg whitespace-nowrap">
                                        Solve &rarr;
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400">
                        <i data-lucide="bookmark" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                        <p class="text-xs">No bookmarks saved yet.</p>
                        <a href="{{ route('mcq.index') }}" class="mt-2 inline-block text-xs font-bold text-[#087F82] hover:underline">
                            Browse questions to bookmark
                        </a>
                    </div>
                @endif
            </div>

            {{-- Recent Discussions --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="message-square" class="w-4 h-4 text-[#153B70]"></i>
                        <h2 class="text-base font-bold text-[#153B70]">Recent Discussions</h2>
                    </div>
                    <a href="{{ route('user.discussions') }}" class="text-xs font-bold text-[#087F82] hover:underline">
                        View All ({{ $discussionsCount }})
                    </a>
                </div>

                @if ($recentDiscussions->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($recentDiscussions as $disc)
                            @php $q = $disc->question; @endphp
                            <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50/50 hover:border-gray-200 transition-all space-y-1.5">
                                <div class="flex items-center justify-between text-[11px] text-gray-400">
                                    <span>{{ $q ? 'Q#' . $q->id : 'Question' }}</span>
                                    <span>{{ $disc->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700 font-medium line-clamp-2">
                                    "{{ $disc->comment }}"
                                </p>
                                @if ($q)
                                    <div class="pt-1 text-right">
                                        <a href="{{ route('mcq.show', $q->id) }}#discussions" class="text-[11px] font-bold text-[#087F82] hover:underline">
                                            Go to Question &rarr;
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400">
                        <i data-lucide="message-square" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                        <p class="text-xs">No discussion posts yet.</p>
                        <a href="{{ route('mcq.index') }}" class="mt-2 inline-block text-xs font-bold text-[#087F82] hover:underline">
                            Join a question discussion
                        </a>
                    </div>
                @endif
            </div>

        </div>

        {{-- Recent Reports & Statuses --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="flag" class="w-4 h-4 text-amber-600"></i>
                    <h2 class="text-base font-bold text-[#153B70]">Recent Question Reports</h2>
                </div>
                <a href="{{ route('user.reports') }}" class="text-xs font-bold text-[#087F82] hover:underline">
                    View All ({{ $reportsCount }})
                </a>
            </div>

            @if ($recentReports->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3">Question</th>
                                <th class="px-6 py-3">Issue Reason</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Reported On</th>
                                <th class="px-6 py-3">Admin Resolution Note</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($recentReports as $rep)
                                @php $q = $rep->question; @endphp
                                <tr>
                                    <td class="px-6 py-3.5 font-bold text-[#243247]">
                                        @if ($q)
                                            <a href="{{ route('mcq.show', $q->id) }}" class="text-[#153B70] hover:text-[#087F82]">
                                                Q#{{ $q->id }} ({{ $q->topicName ?: 'MCQ' }})
                                            </a>
                                        @else
                                            <span class="text-gray-400">MCQ</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $rep->issue_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        @if ($rep->status === 'resolved')
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                Resolved
                                            </span>
                                        @elseif ($rep->status === 'under_review')
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                                Under Review
                                            </span>
                                        @elseif ($rep->status === 'closed')
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                                                Closed
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                                New
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-gray-400">
                                        {{ $rep->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-3.5 text-xs text-gray-600">
                                        {{ $rep->admin_notes ?: 'Pending editorial review' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-400">
                    <p class="text-xs">No reports submitted.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.user>
