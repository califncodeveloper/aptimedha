<x-layouts.admin>
    <x-slot:title>Reports & Moderation - AptiMedha Admin</x-slot:title>
    <x-slot:header>Question Reports & Corrections Queue</x-slot:header>

    <div class="space-y-6">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#153B70]">Question Reports Queue ({{ $reports->total() }})</h2>
                <p class="text-xs text-gray-500">Review feedback submitted by users to maintain accuracy across question banks.</p>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white p-4 rounded-2xl border border-gray-200/90 shadow-xs flex flex-col lg:flex-row items-center justify-between gap-4">
            {{-- Status Tabs --}}
            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                <a href="{{ route('admin.reports.index', array_filter(['issue_type' => $issueType, 'search' => $search])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ empty($status) ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    All ({{ $counts['all'] }})
                </a>
                <a href="{{ route('admin.reports.index', array_filter(['status' => 'new', 'issue_type' => $issueType, 'search' => $search])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $status === 'new' ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    New ({{ $counts['new'] }})
                </a>
                <a href="{{ route('admin.reports.index', array_filter(['status' => 'under_review', 'issue_type' => $issueType, 'search' => $search])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $status === 'under_review' ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Under Review ({{ $counts['under_review'] }})
                </a>
                <a href="{{ route('admin.reports.index', array_filter(['status' => 'resolved', 'issue_type' => $issueType, 'search' => $search])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $status === 'resolved' ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Resolved ({{ $counts['resolved'] }})
                </a>
                <a href="{{ route('admin.reports.index', array_filter(['status' => 'closed', 'issue_type' => $issueType, 'search' => $search])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $status === 'closed' ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Closed ({{ $counts['closed'] }})
                </a>
            </div>

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                @if ($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <select name="issue_type" class="px-2.5 py-1.5 text-xs rounded-xl border border-gray-300 focus:border-[#087F82] focus:outline-none">
                    <option value="">All Issue Types</option>
                    <option value="incorrect_answer" {{ $issueType === 'incorrect_answer' ? 'selected' : '' }}>Incorrect Answer</option>
                    <option value="incorrect_explanation" {{ $issueType === 'incorrect_explanation' ? 'selected' : '' }}>Incorrect Explanation</option>
                    <option value="wrong_question" {{ $issueType === 'wrong_question' ? 'selected' : '' }}>Incorrect Question</option>
                    <option value="broken_image" {{ $issueType === 'broken_image' ? 'selected' : '' }}>Missing Image / Figure</option>
                    <option value="duplicate_question" {{ $issueType === 'duplicate_question' ? 'selected' : '' }}>Duplicate Question</option>
                    <option value="typo_formatting" {{ $issueType === 'typo_formatting' ? 'selected' : '' }}>Typo / Formatting</option>
                    <option value="other" {{ $issueType === 'other' ? 'selected' : '' }}>Other</option>
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search description, question..."
                           class="pl-8 pr-3 py-1.5 text-xs rounded-xl border border-gray-300 focus:border-[#087F82] focus:outline-none">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-2.5 top-2"></i>
                </div>

                <button type="submit" class="px-3 py-1.5 bg-[#087F82] text-white text-xs font-bold rounded-xl hover:bg-[#066668]">
                    Filter
                </button>
                @if ($search || $status || $issueType)
                    <a href="{{ route('admin.reports.index') }}" class="px-2 py-1.5 text-xs text-gray-500 hover:text-gray-700">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Reports Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            @if ($reports->isNotEmpty())
                <div class="overflow-x-auto scrollbar-thin">
                    <table class="w-full text-left text-xs sm:text-sm min-w-[750px]">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3.5">Report #</th>
                                <th class="px-6 py-3.5">Question Reference</th>
                                <th class="px-6 py-3.5">Reported By</th>
                                <th class="px-6 py-3.5">Issue Type</th>
                                <th class="px-6 py-3.5">Description</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($reports as $item)
                                @php $q = $item->question; @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-mono text-gray-400 whitespace-nowrap">
                                        REP-{{ $item->id }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($q)
                                            <a href="{{ route('admin.mcqs.edit', $q->id) }}" class="font-bold text-[#153B70] hover:text-[#087F82]">
                                                Q#{{ $q->id }} ({{ $q->topicName ?: 'MCQ' }})
                                            </a>
                                            <p class="text-[11px] text-gray-400">{{ $q->categoryName }}</p>
                                        @else
                                            <span class="text-gray-400">Question removed</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs text-gray-600 whitespace-nowrap">
                                        {{ $item->user?->username ? '@' . $item->user->username : ($item->user?->name ?? 'User #' . $item->user_id) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-800">
                                            {{ $item->issue_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 max-w-xs truncate text-xs text-gray-600" title="{{ $item->description }}">
                                        {{ $item->description }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($item->status === 'new')
                                            <span class="bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-full text-xs font-semibold">New</span>
                                        @elseif ($item->status === 'under_review')
                                            <span class="bg-amber-100 text-amber-800 px-2.5 py-0.5 rounded-full text-xs font-semibold">Under Review</span>
                                        @elseif ($item->status === 'resolved')
                                            <span class="bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full text-xs font-semibold">Resolved</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-700 px-2.5 py-0.5 rounded-full text-xs font-semibold">Closed</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.reports.show', $item->id) }}" class="px-3 py-1 bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold rounded-lg transition-colors">
                                            Review &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-4 border-t border-gray-100">
                    {{ $reports->links() }}
                </div>
            @else
                <div class="text-center py-12 text-gray-400">
                    <i data-lucide="flag" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                    <p class="text-sm">No question reports found in this queue.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
