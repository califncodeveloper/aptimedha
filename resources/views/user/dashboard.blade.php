<x-layouts.user>
    <x-slot:title>Learner Dashboard - AptiMedha</x-slot:title>

    <div class="space-y-8">
        {{-- Welcome Header --}}
        <div class="bg-gradient-to-r from-[#153B70] to-[#087F82] rounded-2xl p-6 sm:p-8 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-teal-100 mb-2">
                    Placement Batch 2026
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Welcome back, {{ Auth::user()->name ?? 'Learner' }}! 👋
                </h1>
                <p class="text-xs sm:text-sm text-white/80 mt-1 max-w-xl">
                    You have solved 184 aptitude and reasoning questions so far with a 78% accuracy rate. Keep up the momentum!
                </p>
            </div>
            <a href="{{ route('mcq.index') }}" class="px-5 py-2.5 rounded-xl bg-white text-[#153B70] font-bold text-xs hover:bg-gray-100 transition-colors shadow-xs whitespace-nowrap">
                Continue Practice &rarr;
            </a>
        </div>

        {{-- Metrics Overview --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <x-stat-card title="MCQs Practiced" value="184" icon="activity" color="primary" trend="+24 this week" />
            <x-stat-card title="Saved Bookmarks" value="12" icon="bookmark" color="secondary" />
            <x-stat-card title="Discussion Queries" value="5" icon="message-square" color="success" />
            <x-stat-card title="Overall Accuracy" value="78%" icon="check-circle" color="primary" />
        </div>

        {{-- Quick Practice & Recommended Tracks --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Recommended Practice Topics --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-[#153B70]">Recommended Practice Topics</h2>
                    <a href="{{ route('categories.index') }}" class="text-xs font-bold text-[#087F82] hover:underline">Explore All</a>
                </div>

                <div class="space-y-3">
                    <div class="p-3.5 rounded-xl border border-gray-200/80 hover:border-[#087F82] hover:bg-teal-50/20 transition-all flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-teal-50 text-[#087F82] flex items-center justify-center font-bold">
                                <i data-lucide="percent" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#243247]">Percentages & Profit Loss</h3>
                                <p class="text-xs text-gray-500">Arithmetic Aptitude &bull; 150 questions</p>
                            </div>
                        </div>
                        <a href="{{ route('mcq.index', ['topic' => 'percentages']) }}" class="px-3.5 py-1.5 rounded-lg bg-[#087F82] text-white text-xs font-bold hover:bg-[#066668]">
                            Practice
                        </a>
                    </div>

                    <div class="p-3.5 rounded-xl border border-gray-200/80 hover:border-[#087F82] hover:bg-teal-50/20 transition-all flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 text-[#153B70] flex items-center justify-center font-bold">
                                <i data-lucide="git-merge" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#243247]">Syllogisms & Logical Deductions</h3>
                                <p class="text-xs text-gray-500">Logical Reasoning &bull; 90 questions</p>
                            </div>
                        </div>
                        <a href="{{ route('mcq.index', ['topic' => 'syllogisms']) }}" class="px-3.5 py-1.5 rounded-lg bg-[#153B70] text-white text-xs font-bold hover:bg-[#0e2a52]">
                            Practice
                        </a>
                    </div>

                    <div class="p-3.5 rounded-xl border border-gray-200/80 hover:border-[#087F82] hover:bg-teal-50/20 transition-all flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                                <i data-lucide="code" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#243247]">Python Basics & OOP</h3>
                                <p class="text-xs text-gray-500">Programming &bull; 120 questions</p>
                            </div>
                        </div>
                        <a href="{{ route('mcq.index', ['topic' => 'python']) }}" class="px-3.5 py-1.5 rounded-lg bg-amber-600 text-white text-xs font-bold hover:bg-amber-700">
                            Practice
                        </a>
                    </div>
                </div>
            </div>

            {{-- Daily Learning Streak & Placement Checklist --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 space-y-5">
                <div>
                    <h2 class="text-base font-bold text-[#153B70]">Preparation Checklist</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Key milestones for upcoming placement season.</p>
                </div>

                <div class="space-y-3 text-xs">
                    <label class="flex items-center gap-2.5 p-2.5 bg-gray-50 rounded-lg cursor-pointer">
                        <input type="checkbox" checked class="h-4 w-4 rounded text-[#087F82] border-gray-300">
                        <span class="line-through text-gray-400">Complete Arithmetic Foundations</span>
                    </label>
                    <label class="flex items-center gap-2.5 p-2.5 bg-gray-50 rounded-lg cursor-pointer">
                        <input type="checkbox" checked class="h-4 w-4 rounded text-[#087F82] border-gray-300">
                        <span class="line-through text-gray-400">Build One-Page Technical Resume</span>
                    </label>
                    <label class="flex items-center gap-2.5 p-2.5 bg-gray-50 rounded-lg cursor-pointer">
                        <input type="checkbox" class="h-4 w-4 rounded text-[#087F82] border-gray-300">
                        <span class="font-medium text-[#243247]">Solve 50 Data Interpretation Sets</span>
                    </label>
                    <label class="flex items-center gap-2.5 p-2.5 bg-gray-50 rounded-lg cursor-pointer">
                        <input type="checkbox" class="h-4 w-4 rounded text-[#087F82] border-gray-300">
                        <span class="font-medium text-[#243247]">Review Top 25 HR Interview Q&As</span>
                    </label>
                </div>

                <a href="{{ route('placement.index') }}" class="block text-center py-2 px-3 bg-teal-50 text-[#087F82] rounded-xl text-xs font-bold hover:bg-teal-100 transition-colors">
                    View Placement Roadmap &rarr;
                </a>
            </div>

        </div>

        {{-- Recent Practice Activity Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-[#153B70]">Recent Practice Sessions</h2>
                <span class="text-xs text-gray-400">Last 7 Days</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3">Topic</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Questions Solved</th>
                            <th class="px-6 py-3">Accuracy</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="px-6 py-3.5 font-bold text-[#243247]">Percentages</td>
                            <td class="px-6 py-3.5 text-gray-500">Arithmetic Aptitude</td>
                            <td class="px-6 py-3.5 font-semibold text-[#153B70]">25 MCQs</td>
                            <td class="px-6 py-3.5"><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">84%</span></td>
                            <td class="px-6 py-3.5 text-gray-400">Today</td>
                            <td class="px-6 py-3.5 text-right"><a href="{{ route('mcq.index', ['topic' => 'percentages']) }}" class="text-xs font-bold text-[#087F82] hover:underline">Continue &rarr;</a></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3.5 font-bold text-[#243247]">Coding & Decoding</td>
                            <td class="px-6 py-3.5 text-gray-500">Logical Reasoning</td>
                            <td class="px-6 py-3.5 font-semibold text-[#153B70]">20 MCQs</td>
                            <td class="px-6 py-3.5"><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">80%</span></td>
                            <td class="px-6 py-3.5 text-gray-400">Yesterday</td>
                            <td class="px-6 py-3.5 text-right"><a href="{{ route('mcq.index', ['topic' => 'coding-and-decoding']) }}" class="text-xs font-bold text-[#087F82] hover:underline">Continue &rarr;</a></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3.5 font-bold text-[#243247]">Time, Speed & Distance</td>
                            <td class="px-6 py-3.5 text-gray-500">Arithmetic Aptitude</td>
                            <td class="px-6 py-3.5 font-semibold text-[#153B70]">15 MCQs</td>
                            <td class="px-6 py-3.5"><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">67%</span></td>
                            <td class="px-6 py-3.5 text-gray-400">3 days ago</td>
                            <td class="px-6 py-3.5 text-right"><a href="{{ route('mcq.index', ['topic' => 'time-speed-distance']) }}" class="text-xs font-bold text-[#087F82] hover:underline">Continue &rarr;</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.user>
