<x-layouts.admin>
    <x-slot:title>MCQ Management - AptiMedha Admin</x-slot:title>
    <x-slot:header>MCQ Question Bank</x-slot:header>

    <div class="space-y-6" x-data="{
        deleteModal: null,
        deleteAction: '',
        openDelete(id, number) {
            this.deleteModal = { id: id, number: number };
            this.deleteAction = '{{ url('admin/mcqs') }}/' + id;
        }
    }">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Top Toolbar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#153B70]">All Questions ({{ number_format($mcqs->total()) }})</h2>
                <p class="text-xs text-gray-500">Manage individual MCQs, options, answer keys, explanations, and diagram assets.</p>
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
        <div class="bg-white p-4 rounded-2xl border border-gray-200/90 shadow-xs" x-data="{
            selectedCat: '{{ $categoryId }}',
            selectedSubj: '{{ $subjectId }}',
            allSubjects: {{ $subjects->toJson() }},
            allTopics: {{ $topics->toJson() }},
            get filteredSubjects() {
                if (!this.selectedCat) return this.allSubjects;
                return this.allSubjects.filter(s => s.category_id == this.selectedCat);
            },
            get filteredTopics() {
                return this.allTopics.filter(t => {
                    const matchCat = !this.selectedCat || t.category_id == this.selectedCat;
                    const matchSubj = !this.selectedSubj || t.subject_id == this.selectedSubj;
                    return matchCat && matchSubj;
                });
            }
        }">
            <form action="{{ route('admin.mcqs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-3">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Search Keyword / Q#</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Search question text or Q#..."
                               class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-xl text-xs focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Category</label>
                    <select name="category_id" x-model="selectedCat" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:border-[#087F82] focus:outline-none">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected($categoryId == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Subject</label>
                    <select name="subject_id" x-model="selectedSubj" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:border-[#087F82] focus:outline-none">
                        <option value="">All Subjects</option>
                        <template x-for="subj in filteredSubjects" :key="subj.id">
                            <option :value="subj.id" x-text="subj.name" :selected="subj.id == '{{ $subjectId }}'"></option>
                        </template>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Topic</label>
                    <select name="topic_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:border-[#087F82] focus:outline-none">
                        <option value="">All Topics</option>
                        <template x-for="t in filteredTopics" :key="t.id">
                            <option :value="t.id" x-text="t.name" :selected="t.id == '{{ $topicId }}'"></option>
                        </template>
                    </select>
                </div>

                <div class="md:col-span-1">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full px-2.5 py-2 border border-gray-200 rounded-xl text-xs focus:border-[#087F82] focus:outline-none">
                        <option value="">All</option>
                        <option value="1" @selected($status === '1' || $status === 'active' || $status === 'published')>Active</option>
                        <option value="0" @selected($status === '0' || $status === 'inactive' || $status === 'draft')>Draft</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold rounded-xl transition-colors shadow-2xs">
                        Filter
                    </button>
                    @if($search || $categoryId || $subjectId || $topicId || ($status !== null && $status !== ''))
                        <a href="{{ route('admin.mcqs.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-xl transition-colors" title="Clear Filters">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- MCQs Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full min-w-[720px] text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-3.5">ID</th>
                            <th class="px-5 py-3.5">Q#</th>
                            <th class="px-5 py-3.5">Topic Taxonomy</th>
                            <th class="px-5 py-3.5">Question Text</th>
                            <th class="px-5 py-3.5 text-center">Correct</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($mcqs as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-4 font-mono text-gray-400">#{{ $item->id }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-bold text-xs bg-slate-100 text-[#153B70]">
                                        Q{{ $item->question_number }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-bold text-[#243247]">{{ $item->topic?->name ?? 'Unassigned' }}</p>
                                    <p class="text-[11px] text-gray-400">
                                        {{ $item->category?->name }}
                                        @if($item->subject)
                                            &bull; {{ $item->subject->name }}
                                        @endif
                                    </p>
                                </td>
                                <td class="px-5 py-4 max-w-md">
                                    <p class="font-medium text-[#243247] line-clamp-2">{{ $item->question_text }}</p>
                                    @if($item->question_image)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#087F82] mt-1">
                                            <i data-lucide="image" class="w-3.5 h-3.5"></i> Diagram attached
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-800 font-bold text-xs inline-flex items-center justify-center">
                                        {{ $item->correct_option }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <form action="{{ route('admin.mcqs.toggle-status', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-2.5 py-0.5 rounded-full text-xs font-semibold cursor-pointer transition-opacity hover:opacity-80 {{ $item->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}"
                                                title="Click to toggle status">
                                            {{ $item->is_active ? 'Published' : 'Draft' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('mcq.show', $item->id) }}" target="_blank" class="p-1.5 text-gray-400 hover:text-[#153B70] hover:bg-slate-100 rounded-lg transition-colors" title="View Public Practice">
                                            <i data-lucide="external-link" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('admin.mcqs.edit', $item->id) }}" class="p-1.5 text-gray-500 hover:text-[#087F82] hover:bg-teal-50 rounded-lg transition-colors" title="Edit Question">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        <button @click="openDelete({{ $item->id }}, {{ $item->question_number }})" type="button" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Question">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                    <div class="max-w-sm mx-auto space-y-2">
                                        <i data-lucide="inbox" class="w-8 h-8 mx-auto text-gray-300"></i>
                                        <p class="text-sm font-bold text-gray-600">No MCQs Found</p>
                                        <p class="text-xs text-gray-400">No questions match your current search or filter criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Table Pagination Footer --}}
            @if($mcqs->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
                    <span>Showing {{ $mcqs->firstItem() ?? 0 }} to {{ $mcqs->lastItem() ?? 0 }} of {{ number_format($mcqs->total()) }} questions</span>
                    <div>
                        {{ $mcqs->links() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="deleteModal !== null" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs" @click="deleteModal = null"></div>
            <div class="relative bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center z-10">
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-[#153B70]">Delete MCQ Question?</h3>
                <p class="text-xs text-gray-500 mt-1">
                    Are you sure you want to delete Question #<span x-text="deleteModal?.number"></span>? Questions with dependent user activity (bookmarks, discussions, reports) will be safely deactivated instead.
                </p>
                <form :action="deleteAction" method="POST" class="mt-5 flex items-center justify-center gap-2">
                    @csrf
                    @method('DELETE')
                    <button @click="deleteModal = null" type="button" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 rounded-xl hover:bg-rose-700">Delete</button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
