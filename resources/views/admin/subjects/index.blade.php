<x-layouts.admin>
    <x-slot:title>Subject Management - AptiMedha Admin</x-slot:title>
    <x-slot:header>Subject & Module Taxonomy</x-slot:header>

    <div class="space-y-6" x-data="{
        subjects: [
            { id: 1, name: 'Commercial Arithmetic', slug: 'commercial-arithmetic', category: 'Arithmetic Aptitude', topicsCount: 8, order: 1, status: 'Active' },
            { id: 2, name: 'Number & Algebra', slug: 'number-and-algebra', category: 'Arithmetic Aptitude', topicsCount: 10, order: 2, status: 'Active' },
            { id: 3, name: 'Time, Speed & Work', slug: 'time-speed-work', category: 'Arithmetic Aptitude', topicsCount: 7, order: 3, status: 'Active' },
            { id: 4, name: 'Analytical Reasoning', slug: 'analytical-reasoning', category: 'Logical Reasoning', topicsCount: 8, order: 1, status: 'Active' },
            { id: 5, name: 'Critical Deduction', slug: 'critical-deduction', category: 'Logical Reasoning', topicsCount: 6, order: 2, status: 'Active' },
            { id: 6, name: 'Programming Languages', slug: 'languages', category: 'Programming', topicsCount: 5, order: 1, status: 'Active' },
            { id: 7, name: 'Core Computer Science', slug: 'core-cs', category: 'Programming', topicsCount: 6, order: 2, status: 'Active' },
            { id: 8, name: 'Static GK', slug: 'static-gk', category: 'General Knowledge', topicsCount: 9, order: 1, status: 'Active' }
        ]
    }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#153B70]">Subjects & Submodules (<span x-text="subjects.length"></span>)</h2>
                <p class="text-xs text-gray-500">Intermediate level grouping between primary categories and leaf topics.</p>
            </div>

            <a href="{{ route('admin.subjects.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold rounded-xl text-white bg-[#153B70] hover:bg-[#0e2a52] transition-colors shadow-xs">
                <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Add New Subject
            </a>
        </div>

        {{-- Subjects Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5">Order</th>
                            <th class="px-6 py-3.5">Subject Name</th>
                            <th class="px-6 py-3.5">Parent Category</th>
                            <th class="px-6 py-3.5">URL Slug</th>
                            <th class="px-6 py-3.5">Leaf Topics</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="subj in subjects" :key="subj.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-400" x-text="subj.order"></td>
                                <td class="px-6 py-4 font-bold text-[#243247]" x-text="subj.name"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-[#153B70]/10 text-[#153B70]" x-text="subj.category"></span>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-400" x-text="subj.slug"></td>
                                <td class="px-6 py-4 font-bold text-[#087F82]" x-text="subj.topicsCount + ' Topics'"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800" x-text="subj.status"></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a :href="'{{ url('admin/subjects') }}/' + subj.id + '/edit'" class="p-1.5 text-gray-500 hover:text-[#087F82] rounded-lg hover:bg-teal-50 transition-colors" title="Edit Subject">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
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
