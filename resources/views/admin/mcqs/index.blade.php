<x-layouts.admin>
    <x-slot:title>MCQ Management - AptiMedha Admin</x-slot:title>
    <x-slot:header>MCQ Question Bank</x-slot:header>

    <div class="space-y-6" x-data="{
        searchQuery: '',
        selectedCategory: '',
        deleteModal: null,
        previewModal: null,
        mcqs: [
            { id: 1, number: 1, category: 'Arithmetic Aptitude', subject: 'Commercial Arithmetic', topic: 'Percentages', question: 'What is 25% of 240?', answer: 'C', status: 'Published', date: 'Oct 28, 2026' },
            { id: 2, number: 2, category: 'Arithmetic Aptitude', subject: 'Commercial Arithmetic', topic: 'Percentages', question: 'A value increases from 80 to 100. What is the percentage increase?', answer: 'C', status: 'Published', date: 'Oct 28, 2026' },
            { id: 3, number: 3, category: 'Arithmetic Aptitude', subject: 'Commercial Arithmetic', topic: 'Percentages', question: 'If 30% of a number is 120, what is 45% of that number?', answer: 'B', status: 'Published', date: 'Oct 29, 2026' },
            { id: 4, number: 4, category: 'Arithmetic Aptitude', subject: 'Commercial Arithmetic', topic: 'Percentages', question: 'The price of sugar increases by 20%. By what percentage must a household reduce consumption?', answer: 'A', status: 'Published', date: 'Oct 29, 2026' },
            { id: 5, number: 5, category: 'Arithmetic Aptitude', subject: 'Commercial Arithmetic', topic: 'Percentages', question: 'In an examination, a candidate must secure 40% marks to pass...', answer: 'B', status: 'Published', date: 'Oct 30, 2026' },
            { id: 6, number: 1, category: 'Programming', subject: 'Languages', topic: 'Python', question: 'What is the output of print(type([]) is list)?', answer: 'A', status: 'Published', date: 'Nov 01, 2026' },
            { id: 7, number: 2, category: 'Programming', subject: 'Languages', topic: 'Python', question: 'Which keyword is used to define a generator function in Python?', answer: 'D', status: 'Published', date: 'Nov 01, 2026' },
            { id: 8, number: 1, category: 'Logical Reasoning', subject: 'Analytical', topic: 'Coding Decoding', question: 'In a certain code, COMPUTER is written as RFUVQNPC. How is MEDICINE written?', answer: 'J', status: 'Draft', date: 'Nov 02, 2026' }
        ],
        deleteItem(id) {
            this.mcqs = this.mcqs.filter(m => m.id !== id);
            this.deleteModal = null;
        }
    }">
        {{-- Top Toolbar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#153B70]">All Questions (<span x-text="mcqs.length"></span>)</h2>
                <p class="text-xs text-gray-500">Manage individual questions, correct answer keys, and editorial solutions.</p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.mcqs.import.form') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold rounded-xl text-[#087F82] bg-teal-50 hover:bg-teal-100 transition-colors border border-teal-200/80 shadow-2xs">
                    <i data-lucide="upload" class="w-4 h-4 mr-1.5"></i> Bulk Import
                </a>
                <a href="{{ route('admin.mcqs.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold rounded-xl text-white bg-[#153B70] hover:bg-[#0e2a52] transition-colors shadow-xs">
                    <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Add New MCQ
                </a>
            </div>
        </div>

        {{-- Filters & Search Bar --}}
        <div class="bg-white p-4 rounded-2xl border border-gray-200/90 shadow-xs flex flex-col md:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Search question text or keywords..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-xs sm:text-sm focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none">
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <select x-model="selectedCategory" class="px-3 py-2 border border-gray-200 rounded-xl text-xs focus:border-[#087F82] focus:outline-none w-full md:w-48">
                    <option value="">All Categories</option>
                    <option value="Arithmetic Aptitude">Arithmetic Aptitude</option>
                    <option value="Logical Reasoning">Logical Reasoning</option>
                    <option value="Programming">Programming</option>
                </select>
            </div>
        </div>

        {{-- MCQs Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5">ID</th>
                            <th class="px-6 py-3.5">Q#</th>
                            <th class="px-6 py-3.5">Topic Taxonomy</th>
                            <th class="px-6 py-3.5">Question Text</th>
                            <th class="px-6 py-3.5 text-center">Correct</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="item in mcqs.filter(m => (!selectedCategory || m.category === selectedCategory) && (!searchQuery || m.question.toLowerCase().includes(searchQuery.toLowerCase())))" :key="item.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-mono text-gray-400" x-text="item.id"></td>
                                <td class="px-6 py-4 font-bold text-[#153B70]" x-text="'Q' + item.number"></td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-[#243247]" x-text="item.topic"></p>
                                    <p class="text-[11px] text-gray-400" x-text="item.category"></p>
                                </td>
                                <td class="px-6 py-4 max-w-md">
                                    <p class="font-medium text-[#243247] truncate" x-text="item.question"></p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-800 font-bold text-xs inline-flex items-center justify-center" x-text="item.answer"></span>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="item.status === 'Published' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700'" class="px-2.5 py-0.5 rounded-full text-xs font-semibold" x-text="item.status"></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a :href="'{{ url('admin/mcqs') }}/' + item.id + '/edit'" class="p-1.5 text-gray-500 hover:text-[#087F82] hover:bg-teal-50 rounded-lg transition-colors" title="Edit Question">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        <button @click="deleteModal = item" type="button" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Question">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Table Pagination Footer --}}
            <div class="px-6 py-3.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Showing 1 to 8 of 10,450 questions</span>
                <div class="flex items-center gap-1">
                    <button class="px-3 py-1 bg-white border border-gray-200 rounded-md font-bold text-[#153B70]">&larr; Prev</button>
                    <button class="px-3 py-1 bg-[#153B70] text-white rounded-md font-bold">1</button>
                    <button class="px-3 py-1 bg-white border border-gray-200 rounded-md font-bold text-[#153B70]">2</button>
                    <button class="px-3 py-1 bg-white border border-gray-200 rounded-md font-bold text-[#153B70]">Next &rarr;</button>
                </div>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="deleteModal !== null" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs" @click="deleteModal = null"></div>
            <div class="relative bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center z-10">
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-[#153B70]">Delete MCQ Question?</h3>
                <p class="text-xs text-gray-500 mt-1">This will permanently remove question #<span x-text="deleteModal?.number"></span> from the published practice bank.</p>
                <div class="mt-5 flex items-center justify-center gap-2">
                    <button @click="deleteModal = null" type="button" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200">Cancel</button>
                    <button @click="deleteItem(deleteModal.id)" type="button" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 rounded-xl hover:bg-rose-700">Delete</button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
