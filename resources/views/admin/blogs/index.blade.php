<x-layouts.admin>
    <x-slot:title>Blog Management - AptiMedha Admin</x-slot:title>
    <x-slot:header>Editorial & Career Articles</x-slot:header>

    <div class="space-y-6" x-data="{
        articles: [
            { id: 1, title: 'How to Prepare for Aptitude Tests in Placements', category: 'Placement Strategy', author: 'AptiMedha Editorial', views: 4230, status: 'Published', date: 'Mar 12, 2026' },
            { id: 2, title: 'Data Interpretation Tips and Tricks for Speed & Accuracy', category: 'Aptitude Insights', author: 'Pooja Nair', views: 2850, status: 'Published', date: 'Mar 08, 2026' },
            { id: 3, title: 'Common HR Interview Questions and Recommended Responses', category: 'Interview Prep', author: 'Siddharth Rao', views: 3640, status: 'Published', date: 'Mar 05, 2026' },
            { id: 4, title: 'TCS NQT 2026: Complete Syllabus, Sectional Timings & Pattern', category: 'Exam Patterns', author: 'AptiMedha Editorial', views: 5120, status: 'Published', date: 'Feb 26, 2026' }
        ]
    }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#153B70]">Published Articles (<span x-text="articles.length"></span>)</h2>
                <p class="text-xs text-gray-500">Manage placement roadmaps, guides, and career preparation articles.</p>
            </div>

            <a href="{{ route('admin.blogs.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold rounded-xl text-white bg-[#153B70] hover:bg-[#0e2a52] transition-colors shadow-xs">
                <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Write New Article
            </a>
        </div>

        {{-- Articles Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5">ID</th>
                            <th class="px-6 py-3.5">Article Title</th>
                            <th class="px-6 py-3.5">Category</th>
                            <th class="px-6 py-3.5">Author</th>
                            <th class="px-6 py-3.5">Readership</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="post in articles" :key="post.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-mono text-gray-400" x-text="post.id"></td>
                                <td class="px-6 py-4">
                                    <h4 class="font-bold text-[#243247] max-w-sm" x-text="post.title"></h4>
                                    <p class="text-[11px] text-gray-400 mt-0.5" x-text="post.date"></p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-[#087F82]/10 text-[#087F82]" x-text="post.category"></span>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-600" x-text="post.author"></td>
                                <td class="px-6 py-4 font-semibold text-[#153B70]" x-text="post.views.toLocaleString() + ' views'"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800" x-text="post.status"></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a :href="'{{ url('admin/blogs') }}/' + post.id + '/edit'" class="p-1.5 text-gray-500 hover:text-[#087F82] rounded-lg hover:bg-teal-50 transition-colors" title="Edit Article">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        <a :href="'{{ url('blog') }}/how-to-prepare-for-aptitude-tests-in-placements'" target="_blank" class="p-1.5 text-gray-500 hover:text-[#153B70] rounded-lg hover:bg-blue-50 transition-colors" title="View Article">
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
