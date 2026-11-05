<x-layouts.admin>
    <x-slot:title>Admin Dashboard</x-slot:title>
    <x-slot:header>Platform Overview & Accurate Analytics</x-slot:header>

    <div class="space-y-8">
        {{-- Primary Stat Cards Grid (Actual Database Counts) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total MCQs --}}
            <div class="p-5 bg-white rounded-2xl border border-gray-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total MCQs</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-[#153B70] mt-1">{{ number_format($totalQuestions) }}</p>
                    <p class="text-[11px] text-gray-500 mt-1">
                        <span class="text-emerald-700 font-bold">{{ number_format($activeQuestions) }} active</span>
                        @if($inactiveQuestions > 0)
                            &bull; <span class="text-amber-700 font-medium">{{ number_format($inactiveQuestions) }} inactive</span>
                        @endif
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#153B70] flex items-center justify-center flex-shrink-0">
                    <i data-lucide="help-circle" class="w-6 h-6"></i>
                </div>
            </div>

            {{-- Registered Users --}}
            <div class="p-5 bg-white rounded-2xl border border-gray-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Registered Users</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-[#153B70] mt-1">{{ number_format($totalUsers) }}</p>
                    <p class="text-[11px] text-gray-500 mt-1">
                        <span class="text-[#087F82] font-bold">{{ number_format($activeUsersCount) }} active</span> (activity / 30d)
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-teal-50 text-[#087F82] flex items-center justify-center flex-shrink-0">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
            </div>

            {{-- Taxonomy Structure --}}
            <div class="p-5 bg-white rounded-2xl border border-gray-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Taxonomy</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-[#153B70] mt-1">{{ number_format($activeCategories) }}</p>
                    <p class="text-[11px] text-gray-500 mt-1">
                        {{ number_format($activeSubjects) }} subjects &bull; {{ number_format($activeTopics) }} leaf topics
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="folder-tree" class="w-6 h-6"></i>
                </div>
            </div>

            {{-- Pending Reports Queue --}}
            <div class="p-5 bg-white rounded-2xl border border-gray-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pending Reports</p>
                    <p class="text-2xl sm:text-3xl font-extrabold {{ $pendingReports > 0 ? 'text-amber-600' : 'text-[#153B70]' }} mt-1">
                        {{ number_format($pendingReports) }}
                    </p>
                    <p class="text-[11px] text-gray-500 mt-1">
                        {{ number_format($totalReports) }} total &bull; {{ number_format($resolvedReports) }} resolved
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl {{ $pendingReports > 0 ? 'bg-amber-50 text-amber-600' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center flex-shrink-0">
                    <i data-lucide="flag" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        {{-- Secondary Metric Pills (Database-Driven) --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="p-3.5 bg-white rounded-xl border border-gray-200/90 shadow-2xs">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Articles (Blog)</span>
                <p class="text-lg font-bold text-[#153B70] mt-0.5">{{ number_format($totalBlogs) }}</p>
                <span class="text-[10px] text-emerald-700">{{ number_format($publishedBlogs) }} published</span>
            </div>

            <div class="p-3.5 bg-white rounded-xl border border-gray-200/90 shadow-2xs">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Discussions</span>
                <p class="text-lg font-bold text-[#153B70] mt-0.5">{{ number_format($totalDiscussions) }}</p>
                <span class="text-[10px] {{ $pendingDiscussions > 0 ? 'text-amber-600 font-bold' : 'text-gray-500' }}">{{ number_format($pendingDiscussions) }} pending</span>
            </div>

            <div class="p-3.5 bg-white rounded-xl border border-gray-200/90 shadow-2xs">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">User Bookmarks</span>
                <p class="text-lg font-bold text-[#153B70] mt-0.5">{{ number_format($totalBookmarks) }}</p>
                <span class="text-[10px] text-[#087F82]">Recorded saves</span>
            </div>

            <div class="p-3.5 bg-white rounded-xl border border-gray-200/90 shadow-2xs">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Contact Inquiries</span>
                <p class="text-lg font-bold text-[#153B70] mt-0.5">{{ number_format($totalContacts) }}</p>
                <span class="text-[10px] {{ $unreadContacts > 0 ? 'text-rose-600 font-bold' : 'text-gray-500' }}">{{ number_format($unreadContacts) }} unread</span>
            </div>

            <div class="p-3.5 bg-white rounded-xl border border-gray-200/90 shadow-2xs">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">MCQ Views</span>
                <p class="text-lg font-bold text-[#153B70] mt-0.5">{{ number_format($totalQuestionViews) }}</p>
                <span class="text-[10px] text-gray-500">Total recorded</span>
            </div>

            <div class="p-3.5 bg-white rounded-xl border border-gray-200/90 shadow-2xs">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Blog Reads</span>
                <p class="text-lg font-bold text-[#153B70] mt-0.5">{{ number_format($totalBlogViews) }}</p>
                <span class="text-[10px] text-gray-500">Total recorded</span>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('admin.mcqs.create') }}" class="p-4 bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#153B70] hover:shadow-md transition-all group flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#153B70] flex items-center justify-center group-hover:bg-[#153B70] group-hover:text-white transition-colors">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-[#243247]">Add MCQ</h4>
                    <p class="text-xs text-gray-400">Single question</p>
                </div>
            </a>

            <a href="{{ route('admin.mcqs.import.form') }}" class="p-4 bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#087F82] hover:shadow-md transition-all group flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-[#087F82] flex items-center justify-center group-hover:bg-[#087F82] group-hover:text-white transition-colors">
                    <i data-lucide="upload" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-[#243247]">Bulk Import</h4>
                    <p class="text-xs text-gray-400">Excel (.xlsx) / CSV</p>
                </div>
            </a>

            <a href="{{ route('admin.categories.create') }}" class="p-4 bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-gray-400 hover:shadow-md transition-all group flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gray-50 text-gray-700 flex items-center justify-center group-hover:bg-gray-700 group-hover:text-white transition-colors">
                    <i data-lucide="folder-plus" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-[#243247]">New Category</h4>
                    <p class="text-xs text-gray-400">Taxonomy hierarchy</p>
                </div>
            </a>

            <a href="{{ route('admin.reports.index') }}" class="p-4 bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-amber-400 hover:shadow-md transition-all group flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    <i data-lucide="flag" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-[#243247]">Review Reports</h4>
                    <p class="text-xs text-gray-400">{{ $pendingReports }} pending action</p>
                </div>
            </a>
        </div>

        {{-- Analytics & Real Database Charts Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Questions by Category (Real Database Aggregate) --}}
            <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-bold text-[#153B70]">Questions by Category</h3>
                        <p class="text-xs text-gray-400">Database distribution of active MCQs across top categories</p>
                    </div>
                    <a href="{{ route('admin.categories.index') }}" class="text-xs font-bold text-[#087F82] hover:underline">
                        View Taxonomy &rarr;
                    </a>
                </div>

                @if($categoryBreakdown->isNotEmpty())
                    <div class="space-y-3 pt-2">
                        @php
                            $maxQuestions = max(1, $categoryBreakdown->max('questions_count'));
                        @endphp
                        @foreach($categoryBreakdown as $cat)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-bold text-[#243247]">{{ $cat->name }}</span>
                                    <span class="text-gray-500 font-semibold">{{ number_format($cat->questions_count) }} questions</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-[#153B70] h-2.5 rounded-full transition-all" style="width: {{ round(($cat->questions_count / $maxQuestions) * 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-400 italic py-6 text-center">No categories recorded yet in the database.</p>
                @endif
            </div>

            {{-- 7-Day User Registration Trend (Real Database Values) --}}
            <div class="lg:col-span-5 bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-bold text-[#153B70]">New Registrations (7 Days)</h3>
                        <p class="text-xs text-gray-400">Actual daily user accounts created</p>
                    </div>
                    <span class="text-xs font-bold text-[#153B70] bg-blue-50 px-2 py-0.5 rounded">
                        Past 7 Days
                    </span>
                </div>

                <div class="space-y-2 pt-2">
                    @foreach($userTrend as $day)
                        <div class="flex items-center justify-between text-xs py-1.5 border-b border-gray-50 last:border-0">
                            <span class="text-gray-600 font-medium">{{ $day['day'] }}</span>
                            <span class="font-bold {{ $day['count'] > 0 ? 'text-[#087F82]' : 'text-gray-400' }}">
                                {{ $day['count'] }} {{ Str::plural('user', $day['count']) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tables Grid: Real Recent Questions & Pending Moderation Reports --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Real Recent MCQs Table --}}
            <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-[#153B70]">Recent Questions in Bank</h3>
                        <p class="text-xs text-gray-400">Latest active questions recorded in the database</p>
                    </div>
                    <a href="{{ route('admin.mcqs.index') }}" class="text-xs font-bold text-[#087F82] hover:underline">
                        View All MCQs &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto scrollbar-thin">
                    <table class="w-full min-w-[580px] text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3">#</th>
                                <th class="px-6 py-3">Topic / Category</th>
                                <th class="px-6 py-3">Question</th>
                                <th class="px-6 py-3">Answer</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentQuestions as $mcq)
                                <tr>
                                    <td class="px-6 py-3.5 font-bold text-gray-400">#{{ $mcq->id }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="font-bold text-[#153B70]">{{ $mcq->topic?->name ?? 'N/A' }}</span>
                                        <p class="text-[11px] text-gray-400">{{ $mcq->category?->name ?? 'General' }}</p>
                                    </td>
                                    <td class="px-6 py-3.5 font-medium text-[#243247] max-w-xs truncate">
                                        {{ strip_tags($mcq->question_text) }}
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <span class="px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800 uppercase">
                                            {{ $mcq->correct_option }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        @if($mcq->is_active)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Active</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-right">
                                        <a href="{{ route('admin.mcqs.edit', $mcq->id) }}" class="text-xs font-bold text-[#087F82] hover:underline">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                        No questions exist in database. <a href="{{ route('admin.mcqs.create') }}" class="font-bold text-[#087F82] hover:underline">Add one now</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Real Pending User Reports Queue --}}
            <div class="lg:col-span-4 bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-bold text-[#153B70]">Moderation Queue</h3>
                        <p class="text-xs text-gray-400">User flagged items</p>
                    </div>
                    @if($pendingReports > 0)
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                            {{ $pendingReports }} Pending
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                            All Clear
                        </span>
                    @endif
                </div>

                <div class="space-y-3">
                    @forelse($pendingReportsList as $report)
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-[#153B70]">Q#{{ $report->question_id }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                    {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 line-clamp-2">"{{ $report->description }}"</p>
                            <div class="flex items-center justify-between text-[11px] text-gray-400 pt-1">
                                <span>Reported by {{ $report->user?->name ?? 'User' }}</span>
                                <a href="{{ route('admin.reports.show', $report->id) }}" class="font-bold text-[#087F82] hover:underline">Review &rarr;</a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-400">
                            <i data-lucide="check-circle-2" class="w-8 h-8 mx-auto mb-2 text-emerald-500"></i>
                            <p class="text-xs font-bold text-gray-600">No pending reports</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">All user-submitted reports have been reviewed.</p>
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('admin.reports.index') }}" class="block text-center py-2 px-3 bg-gray-100 text-[#153B70] rounded-xl text-xs font-bold hover:bg-gray-200 transition-colors">
                    Open Full Reports Queue &rarr;
                </a>
            </div>
        </div>
    </div>
</x-layouts.admin>
