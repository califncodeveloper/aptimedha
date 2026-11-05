<x-layouts.user>
    <x-slot:title>My Question Reports - AptiMedha</x-slot:title>

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-[#243247]">Question Reports & Corrections</h1>
            <p class="text-sm text-gray-500 mt-1">
                Track status and editorial responses for errors, typos, or formula clarifications you reported.
            </p>
        </div>

        @if ($reports->isNotEmpty())
            {{-- Reports Table --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#153B70]">Submitted Feedback ({{ $reports->total() }})</span>
                    <span class="text-xs text-gray-400">Statuses: New, Under Review, Resolved, Closed</span>
                </div>

                <div class="overflow-x-auto scrollbar-thin">
                    <table class="w-full min-w-[640px] text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3">Question</th>
                                <th class="px-6 py-3">Issue Category</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Date Submitted</th>
                                <th class="px-6 py-3">Editorial Resolution</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($reports as $report)
                                @php $q = $report->question; @endphp
                                <tr>
                                    <td class="px-6 py-4">
                                        @if ($q)
                                            <a href="{{ route('mcq.show', $q->id) }}" class="font-bold text-[#153B70] hover:text-[#087F82]">
                                                Q#{{ $q->id }} ({{ $q->topicName ?: 'MCQ' }})
                                            </a>
                                            <p class="text-xs text-gray-500 truncate max-w-xs mt-0.5">
                                                "{{ $q->question_text }}"
                                            </p>
                                        @else
                                            <span class="text-gray-400">Question removed</span>
                                        @endif
                                        <p class="text-[11px] text-gray-500 mt-1 italic">
                                            Notes: {{ Str::limit($report->description, 80) }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-[#243247]">
                                            {{ $report->issue_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($report->status === 'resolved')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1 w-fit">
                                                <i data-lucide="check" class="w-3 h-3"></i> Resolved
                                            </span>
                                        @elseif ($report->status === 'under_review')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 flex items-center gap-1 w-fit">
                                                <i data-lucide="clock" class="w-3 h-3"></i> Under Review
                                            </span>
                                        @elseif ($report->status === 'closed')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700 flex items-center gap-1 w-fit">
                                                <i data-lucide="x" class="w-3 h-3"></i> Closed
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 flex items-center gap-1 w-fit">
                                                <i data-lucide="inbox" class="w-3 h-3"></i> New
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-400 whitespace-nowrap">
                                        {{ $report->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-600 max-w-xs">
                                        @if ($report->admin_notes)
                                            <span class="text-[#153B70] font-medium"><em>"{{ $report->admin_notes }}"</em></span>
                                        @else
                                            <span class="text-gray-400 italic">Pending editorial review.</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $reports->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-12 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="flag" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-[#153B70]">No Question Reports</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">
                    If you discover an error or typo while practicing, click "Report" on the question to flag it for our editorial team.
                </p>
                <a href="{{ route('mcq.index') }}" class="mt-4 inline-block px-5 py-2.5 bg-[#087F82] text-white text-xs font-bold rounded-xl hover:bg-[#066668]">
                    Explore Practice Questions
                </a>
            </div>
        @endif
    </div>
</x-layouts.user>
