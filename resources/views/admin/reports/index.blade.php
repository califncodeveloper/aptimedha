<x-layouts.admin>
    <x-slot:title>Reports & Moderation - AptiMedha Admin</x-slot:title>
    <x-slot:header>Question Reports & Corrections Queue</x-slot:header>

    <div class="space-y-6" x-data="{
        statusFilter: '',
        reports: [
            { id: 1, questionId: 4, topic: 'Percentages', category: 'Arithmetic', user: 'rahul_s', issue: 'Formula Clarification', notes: 'Explanation step 2 formula denominator clarification.', status: 'Under Review', date: 'Nov 01, 2026' },
            { id: 2, questionId: 18, topic: 'Python OOP', category: 'Programming', user: 'priya_m', issue: 'Typo / Grammar', notes: 'Typo in class __init__ indentation snippet.', status: 'New', date: 'Nov 02, 2026' },
            { id: 3, questionId: 1, topic: 'Percentages', category: 'Arithmetic', user: 'rahul_s', issue: 'Formatting Error', notes: 'Subscript rendering in explanation was missing.', status: 'Resolved', date: 'Oct 29, 2026' },
            { id: 4, questionId: 77, topic: 'Data Interpretation', category: 'DI', user: 'kunal_j', issue: 'Wrong Answer Marked', notes: 'Option B is calculated instead of Option C.', status: 'New', date: 'Nov 02, 2026' }
        ]
    }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#153B70]">Question Reports Queue (<span x-text="reports.length"></span>)</h2>
                <p class="text-xs text-gray-500">Review feedback submitted by users to maintain accuracy across question banks.</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white p-4 rounded-2xl border border-gray-200/90 shadow-xs flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <button @click="statusFilter = ''" :class="!statusFilter ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                    All Reports (4)
                </button>
                <button @click="statusFilter = 'New'" :class="statusFilter === 'New' ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                    New (2)
                </button>
                <button @click="statusFilter = 'Under Review'" :class="statusFilter === 'Under Review' ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                    Under Review (1)
                </button>
                <button @click="statusFilter = 'Resolved'" :class="statusFilter === 'Resolved' ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                    Resolved (1)
                </button>
            </div>
        </div>

        {{-- Reports Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
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
                        <template x-for="item in reports.filter(r => !statusFilter || r.status === statusFilter)" :key="item.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-mono text-gray-400" x-text="'REP-' + item.id"></td>
                                <td class="px-6 py-4">
                                    <a :href="'{{ url('admin/mcqs') }}/' + item.questionId + '/edit'" class="font-bold text-[#153B70] hover:text-[#087F82]" x-text="'Q#' + item.questionId + ' (' + item.topic + ')'"></a>
                                    <p class="text-[11px] text-gray-400" x-text="item.category"></p>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-600" x-text="'@' + item.user"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-800" x-text="item.issue"></span>
                                </td>
                                <td class="px-6 py-4 max-w-xs truncate text-xs text-gray-600" x-text="item.notes"></td>
                                <td class="px-6 py-4">
                                    <span :class="{
                                        'bg-blue-100 text-blue-800': item.status === 'New',
                                        'bg-amber-100 text-amber-800': item.status === 'Under Review',
                                        'bg-emerald-100 text-emerald-800': item.status === 'Resolved'
                                    }" class="px-2.5 py-0.5 rounded-full text-xs font-semibold" x-text="item.status"></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a :href="'{{ url('admin/reports') }}/' + item.id" class="px-3 py-1 bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold rounded-lg transition-colors">
                                        Review &rarr;
                                    </a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
