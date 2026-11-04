<x-layouts.admin>
    <x-slot:title>Review Report #REP-{{ $report->id }} - AptiMedha Admin</x-slot:title>
    <x-slot:header>Report Moderation: REP-{{ $report->id }}</x-slot:header>

    <div class="space-y-6 max-w-3xl">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Reports Queue
            </a>
            <div>
                @if ($report->status === 'new')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">New</span>
                @elseif ($report->status === 'under_review')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Under Review</span>
                @elseif ($report->status === 'resolved')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Resolved</span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">Closed</span>
                @endif
            </div>
        </div>

        {{-- Question Under Review Card --}}
        @php $q = $report->question; @endphp
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-7 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <span class="text-xs font-bold text-[#153B70]">
                    Target Question (Q#{{ $q?->id ?? $report->question_id }} {{ $q?->topicName ? '- ' . $q->topicName : '' }})
                </span>
                @if ($q)
                    <a href="{{ route('admin.mcqs.edit', $q->id) }}" class="text-xs font-bold text-[#087F82] hover:underline">
                        Edit Question &rarr;
                    </a>
                @endif
            </div>

            @if ($q)
                <h3 class="text-base font-bold text-[#243247]">
                    "{{ $q->question_text }}"
                </h3>

                <div class="p-3 bg-gray-50 rounded-xl text-xs space-y-1">
                    <p class="{{ $q->correct_option === 'A' ? 'font-bold text-emerald-700' : '' }}">
                        <strong>Option A:</strong> {{ $q->option_a }} {{ $q->correct_option === 'A' ? ' (Marked Correct)' : '' }}
                    </p>
                    <p class="{{ $q->correct_option === 'B' ? 'font-bold text-emerald-700' : '' }}">
                        <strong>Option B:</strong> {{ $q->option_b }} {{ $q->correct_option === 'B' ? ' (Marked Correct)' : '' }}
                    </p>
                    <p class="{{ $q->correct_option === 'C' ? 'font-bold text-emerald-700' : '' }}">
                        <strong>Option C:</strong> {{ $q->option_c }} {{ $q->correct_option === 'C' ? ' (Marked Correct)' : '' }}
                    </p>
                    <p class="{{ $q->correct_option === 'D' ? 'font-bold text-emerald-700' : '' }}">
                        <strong>Option D:</strong> {{ $q->option_d }} {{ $q->correct_option === 'D' ? ' (Marked Correct)' : '' }}
                    </p>
                </div>

                @if ($q->explanation)
                    <div class="p-3 bg-teal-50/40 rounded-xl text-xs text-gray-700 border border-teal-100">
                        <strong class="text-[#153B70]">Explanation:</strong>
                        <p class="mt-1 whitespace-pre-line">{{ $q->explanation }}</p>
                    </div>
                @endif
            @else
                <p class="text-xs text-gray-400">The referenced question has been deleted from the database.</p>
            @endif
        </div>

        {{-- User Feedback & Resolution Card --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8">
            <h4 class="text-base font-bold text-[#153B70] mb-4">User Feedback & Reporter Information</h4>

            <div class="grid grid-cols-2 gap-4 pb-4 border-b border-gray-100 text-xs text-gray-600 mb-5">
                <div>
                    <span class="font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Reported By</span>
                    <span class="font-bold text-[#243247]">
                        {{ $report->user?->name }} ({{ $report->user?->email }})
                    </span>
                </div>
                <div>
                    <span class="font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Issue Category</span>
                    <span class="font-bold text-rose-600">{{ $report->issue_label }}</span>
                </div>
                <div>
                    <span class="font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Reported At</span>
                    <span class="font-medium text-gray-700">{{ $report->created_at->format('M d, Y h:i A') }}</span>
                </div>
                @if ($report->resolver)
                    <div>
                        <span class="font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Resolved By</span>
                        <span class="font-medium text-gray-700">
                            {{ $report->resolver->name }} on {{ $report->resolved_at?->format('M d, Y') }}
                        </span>
                    </div>
                @endif
                <div class="col-span-2">
                    <span class="font-bold text-gray-400 uppercase tracking-wider block mb-0.5">User Description</span>
                    <p class="text-sm text-gray-800 bg-gray-50 p-3 rounded-xl border border-gray-200 whitespace-pre-line">
                        {{ $report->description }}
                    </p>
                </div>
            </div>

            <form action="{{ route('admin.reports.update', $report->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Update Report Status <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                        <option value="new" {{ $report->status === 'new' ? 'selected' : '' }}>New (Awaiting Review)</option>
                        <option value="under_review" {{ $report->status === 'under_review' ? 'selected' : '' }}>Under Review (Investigation in Progress)</option>
                        <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Resolved (Correction Applied)</option>
                        <option value="closed" {{ $report->status === 'closed' ? 'selected' : '' }}>Closed (Invalid or Non-actionable)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Editorial Resolution Notes (Visible to Reporter)
                    </label>
                    <textarea name="admin_notes" rows="3" placeholder="Provide notes on what changes were made or reason for resolution..."
                        class="w-full p-3.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">{{ old('admin_notes', $report->admin_notes) }}</textarea>
                </div>

                <div class="pt-3 flex justify-end gap-3">
                    <a href="{{ route('admin.reports.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold shadow-xs transition-colors">
                        Save Resolution
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
