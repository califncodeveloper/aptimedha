<x-layouts.admin>
    <x-slot:title>Category Management - AptiMedha Admin</x-slot:title>
    <x-slot:header>Category Taxonomy</x-slot:header>

    <div class="space-y-6" x-data="{
        categories: [
            { id: 1, name: 'Arithmetic Aptitude', slug: 'arithmetic-aptitude', icon: 'calculator', subjectsCount: 4, questionsCount: 1250, order: 1, status: 'Active' },
            { id: 2, name: 'Data Interpretation', slug: 'data-interpretation', icon: 'bar-chart-2', subjectsCount: 2, questionsCount: 840, order: 2, status: 'Active' },
            { id: 3, name: 'Logical Reasoning', slug: 'logical-reasoning', icon: 'puzzle', subjectsCount: 3, questionsCount: 1120, order: 3, status: 'Active' },
            { id: 4, name: 'Verbal Ability', slug: 'verbal-ability', icon: 'message-square', subjectsCount: 3, questionsCount: 950, order: 4, status: 'Active' },
            { id: 5, name: 'English', slug: 'english', icon: 'file-text', subjectsCount: 1, questionsCount: 680, order: 5, status: 'Active' },
            { id: 6, name: 'General Knowledge', slug: 'general-knowledge', icon: 'globe', subjectsCount: 2, questionsCount: 1400, order: 6, status: 'Active' },
            { id: 7, name: 'Programming', slug: 'programming', icon: 'code', subjectsCount: 2, questionsCount: 1600, order: 7, status: 'Active' },
            { id: 8, name: 'Engineering', slug: 'engineering', icon: 'cpu', subjectsCount: 1, questionsCount: 1100, order: 8, status: 'Active' }
        ]
    }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#153B70]">Categories (<span x-text="categories.length"></span>)</h2>
                <p class="text-xs text-gray-500">Top-level primary learning areas displayed on homepage and navigation.</p>
            </div>

            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold rounded-xl text-white bg-[#153B70] hover:bg-[#0e2a52] transition-colors shadow-xs">
                <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Add New Category
            </a>
        </div>

        {{-- Categories Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5">Order</th>
                            <th class="px-6 py-3.5">Category Name</th>
                            <th class="px-6 py-3.5">URL Slug</th>
                            <th class="px-6 py-3.5">Subjects</th>
                            <th class="px-6 py-3.5">Questions</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="cat in categories" :key="cat.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-400" x-text="cat.order"></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-teal-50 text-[#087F82] flex items-center justify-center font-bold">
                                            <i data-lucide="folder" class="w-4 h-4"></i>
                                        </div>
                                        <span class="font-bold text-[#243247]" x-text="cat.name"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-500" x-text="cat.slug"></td>
                                <td class="px-6 py-4 font-semibold text-gray-700" x-text="cat.subjectsCount + ' Subjects'"></td>
                                <td class="px-6 py-4 font-bold text-[#087F82]" x-text="cat.questionsCount.toLocaleString() + ' MCQs'"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800" x-text="cat.status"></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a :href="'{{ url('admin/categories') }}/' + cat.id + '/edit'" class="p-1.5 text-gray-500 hover:text-[#087F82] rounded-lg hover:bg-teal-50 transition-colors" title="Edit Category">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        <a :href="'{{ url('categories') }}/' + cat.slug" target="_blank" class="p-1.5 text-gray-500 hover:text-[#153B70] rounded-lg hover:bg-blue-50 transition-colors" title="View Public Page">
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
