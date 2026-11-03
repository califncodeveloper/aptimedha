<x-layouts.admin>
    <x-slot:title>Admin Dashboard</x-slot:title>
    <x-slot:header>Platform Overview & Analytics</x-slot:header>

    <div class="space-y-8">
        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-stat-card title="Total MCQ Questions" value="10,450" icon="help-circle" color="primary" trend="+320 this month" />
            <x-stat-card title="Registered Students" value="1,280" icon="users" color="secondary" trend="+45 this week" />
            <x-stat-card title="Active Categories" value="8" icon="folder-tree" color="success" trend="56 leaf topics" />
            <x-stat-card title="Pending Reports" value="4" icon="flag" color="warning" trend="2 under review" />
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
                    <p class="text-xs text-gray-400">User flagged items</p>
                </div>
            </a>
        </div>

        {{-- Tables Grid: Recent Questions & Pending Moderation Reports --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- Recent MCQs Table --}}
            <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-[#153B70]">Recent Questions in Bank</h3>
                        <p class="text-xs text-gray-400">Latest questions published across topics</p>
                    </div>
                    <a href="{{ route('admin.mcqs.index') }}" class="text-xs font-bold text-[#087F82] hover:underline">
                        View All MCQs &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
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
                            <tr>
                                <td class="px-6 py-3.5 font-bold text-gray-400">10450</td>
                                <td class="px-6 py-3.5">
                                    <span class="font-bold text-[#153B70]">Percentages</span>
                                    <p class="text-[11px] text-gray-400">Arithmetic</p>
                                </td>
                                <td class="px-6 py-3.5 font-medium text-[#243247] max-w-xs truncate">
                                    What is 25% of 240?
                                </td>
                                <td class="px-6 py-3.5"><span class="px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800">C. 60</span></td>
                                <td class="px-6 py-3.5"><span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Published</span></td>
                                <td class="px-6 py-3.5 text-right"><a href="{{ route('admin.mcqs.edit', 1) }}" class="text-xs font-bold text-[#087F82] hover:underline">Edit</a></td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3.5 font-bold text-gray-400">10449</td>
                                <td class="px-6 py-3.5">
                                    <span class="font-bold text-[#153B70]">Percentages</span>
                                    <p class="text-[11px] text-gray-400">Arithmetic</p>
                                </td>
                                <td class="px-6 py-3.5 font-medium text-[#243247] max-w-xs truncate">
                                    A value increases from 80 to 100. What is the percentage increase?
                                </td>
                                <td class="px-6 py-3.5"><span class="px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800">C. 25%</span></td>
                                <td class="px-6 py-3.5"><span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Published</span></td>
                                <td class="px-6 py-3.5 text-right"><a href="{{ route('admin.mcqs.edit', 2) }}" class="text-xs font-bold text-[#087F82] hover:underline">Edit</a></td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3.5 font-bold text-gray-400">10448</td>
                                <td class="px-6 py-3.5">
                                    <span class="font-bold text-[#153B70]">Python Basics</span>
                                    <p class="text-[11px] text-gray-400">Programming</p>
                                </td>
                                <td class="px-6 py-3.5 font-medium text-[#243247] max-w-xs truncate">
                                    What is the output of print(type([]) is list)?
                                </td>
                                <td class="px-6 py-3.5"><span class="px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800">A. True</span></td>
                                <td class="px-6 py-3.5"><span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Published</span></td>
                                <td class="px-6 py-3.5 text-right"><a href="{{ route('admin.mcqs.edit', 3) }}" class="text-xs font-bold text-[#087F82] hover:underline">Edit</a></td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3.5 font-bold text-gray-400">10447</td>
                                <td class="px-6 py-3.5">
                                    <span class="font-bold text-[#153B70]">Number Series</span>
                                    <p class="text-[11px] text-gray-400">Reasoning</p>
                                </td>
                                <td class="px-6 py-3.5 font-medium text-[#243247] max-w-xs truncate">
                                    Find the next term: 2, 6, 12, 20, 30, ?
                                </td>
                                <td class="px-6 py-3.5"><span class="px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800">D. 42</span></td>
                                <td class="px-6 py-3.5"><span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Published</span></td>
                                <td class="px-6 py-3.5 text-right"><a href="{{ route('admin.mcqs.edit', 4) }}" class="text-xs font-bold text-[#087F82] hover:underline">Edit</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pending User Reports Queue --}}
            <div class="lg:col-span-4 bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-bold text-[#153B70]">Moderation Queue</h3>
                        <p class="text-xs text-gray-400">User error reports</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700">4 New</span>
                </div>

                <div class="space-y-3">
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-[#153B70]">Q#4 (Percentages)</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Under Review</span>
                        </div>
                        <p class="text-xs text-gray-600">"Explanation step 2 formula denominator clarification."</p>
                        <div class="flex items-center justify-between text-[11px] text-gray-400 pt-1">
                            <span>Reported by @student_rahul</span>
                            <a href="{{ route('admin.reports.show', 1) }}" class="font-bold text-[#087F82] hover:underline">Review &rarr;</a>
                        </div>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-[#153B70]">Q#18 (Python OOP)</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">New</span>
                        </div>
                        <p class="text-xs text-gray-600">"Typo in class __init__ indentation snippet."</p>
                        <div class="flex items-center justify-between text-[11px] text-gray-400 pt-1">
                            <span>Reported by @priya_m</span>
                            <a href="{{ route('admin.reports.show', 2) }}" class="font-bold text-[#087F82] hover:underline">Review &rarr;</a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.reports.index') }}" class="block text-center py-2 px-3 bg-gray-100 text-[#153B70] rounded-xl text-xs font-bold hover:bg-gray-200 transition-colors">
                    Open Full Reports Queue &rarr;
                </a>
            </div>

        </div>
    </div>
</x-layouts.admin>
