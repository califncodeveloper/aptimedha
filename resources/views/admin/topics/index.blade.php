<x-layouts.admin>
    <x-slot:title>Topic Management - AptiMedha Admin</x-slot:title>
    <x-slot:header>Leaf Topic Taxonomy</x-slot:header>

    <div class="space-y-6" x-data="{
        searchQuery: '',
        topics: [
            { id: 1, name: 'Percentages', slug: 'percentages', subject: 'Commercial Arithmetic', category: 'Arithmetic Aptitude', questionsCount: 150, sections: 2, status: 'Published' },
            { id: 2, name: 'Profit and Loss', slug: 'profit-and-loss', subject: 'Commercial Arithmetic', category: 'Arithmetic Aptitude', questionsCount: 140, sections: 2, status: 'Published' },
            { id: 3, name: 'Averages', slug: 'averages', subject: 'Commercial Arithmetic', category: 'Arithmetic Aptitude', questionsCount: 120, sections: 2, status: 'Published' },
            { id: 4, name: 'Ratio and Proportion', slug: 'ratio-and-proportion', subject: 'Commercial Arithmetic', category: 'Arithmetic Aptitude', questionsCount: 110, sections: 2, status: 'Published' },
            { id: 5, name: 'Time and Work', slug: 'time-and-work', subject: 'Time, Speed & Work', category: 'Arithmetic Aptitude', questionsCount: 130, sections: 2, status: 'Published' },
            { id: 6, name: 'Python Programming', slug: 'python', subject: 'Programming Languages', category: 'Programming', questionsCount: 160, sections: 2, status: 'Published' },
            { id: 7, name: 'Coding & Decoding', slug: 'coding-and-decoding', subject: 'Analytical Reasoning', category: 'Logical Reasoning', questionsCount: 115, sections: 2, status: 'Published' },
            { id: 8, name: 'Syllogisms', slug: 'syllogisms', subject: 'Critical Deduction', category: 'Logical Reasoning', questionsCount: 95, sections: 1, status: 'Published' }
        ]
    }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#153B70]">Leaf Topics (<span x-text="topics.length"></span>)</h2>
                <p class="text-xs text-gray-500">Target topics that attach directly to published MCQ sets and 100-question sections.</p>
            </div>

            <a href="{{ route('admin.topics.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold rounded-xl text-white bg-[#153B70] hover:bg-[#0e2a52] transition-colors shadow-xs">
                <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Add New Topic
            </a>
        </div>

        {{-- Search bar --}}
        <div class="bg-white p-4 rounded-2xl border border-gray-200/90 shadow-xs">
            <div class="relative w-full max-w-md">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Filter topics by name or subject..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
            </div>
        </div>

        {{-- Topics Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5">ID</th>
                            <th class="px-6 py-3.5">Topic Name</th>
                            <th class="px-6 py-3.5">Parent Hierarchy</th>
                            <th class="px-6 py-3.5">Questions</th>
                            <th class="px-6 py-3.5">Sections</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="item in topics.filter(t => !searchQuery || t.name.toLowerCase().includes(searchQuery.toLowerCase()) || t.subject.toLowerCase().includes(searchQuery.toLowerCase()))" :key="item.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-mono text-gray-400" x-text="item.id"></td>
                                <td class="px-6 py-4 font-bold text-[#243247]" x-text="item.name"></td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-gray-700" x-text="item.subject"></span>
                                    <p class="text-[11px] text-gray-400" x-text="item.category"></p>
                                </td>
                                <td class="px-6 py-4 font-bold text-[#087F82]" x-text="item.questionsCount + ' MCQs'"></td>
                                <td class="px-6 py-4 text-xs text-gray-600" x-text="item.sections + ' Section(s)'"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800" x-text="item.status"></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a :href="'{{ url('admin/topics') }}/' + item.id + '/edit'" class="p-1.5 text-gray-500 hover:text-[#087F82] rounded-lg hover:bg-teal-50 transition-colors" title="Edit Topic">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        <a :href="'{{ url('mcq') }}?topic=' + item.slug" target="_blank" class="p-1.5 text-gray-500 hover:text-[#153B70] rounded-lg hover:bg-blue-50 transition-colors" title="Practice Topic MCQs">
                                            <i data-lucide="external-link" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
