<x-layouts.admin>
    <x-slot:title>Category Management - AptiMedha Admin</x-slot:title>
    <x-slot:header>Category Taxonomy</x-slot:header>

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center justify-between">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#153B70]">Categories ({{ $categories->total() }})</h2>
                <p class="text-xs text-gray-500">Top-level primary learning tracks displayed on homepage and public navigation.</p>
            </div>

            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold rounded-xl text-white bg-[#153B70] hover:bg-[#0e2a52] transition-colors shadow-xs">
                <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Add New Category
            </a>
        </div>

        {{-- Search Toolbar --}}
        <div class="bg-white p-4 rounded-2xl border border-gray-200/90 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="relative w-full max-w-md">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                <input type="text"
                       name="search"
                       value="{{ $search ?? '' }}"
                       placeholder="Search category name or slug..."
                       class="w-full pl-9 pr-20 py-2 border border-gray-200 rounded-xl text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                @if(!empty($search))
                    <a href="{{ route('admin.categories.index') }}" class="absolute right-12 top-2 text-xs text-gray-400 hover:text-gray-600">Clear</a>
                @endif
                <button type="submit" class="absolute right-2 top-1.5 px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">Filter</button>
            </form>
        </div>

        {{-- Categories Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full min-w-[650px] text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5">Order</th>
                            <th class="px-6 py-3.5">Category Name</th>
                            <th class="px-6 py-3.5">URL Slug</th>
                            <th class="px-6 py-3.5">Subjects</th>
                            <th class="px-6 py-3.5">Topics</th>
                            <th class="px-6 py-3.5">MCQs</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($categories as $cat)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-400">{{ $cat->display_order }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-teal-50 text-[#087F82] flex items-center justify-center font-bold">
                                            <i data-lucide="{{ $cat->icon ?: 'folder' }}" class="w-4 h-4"></i>
                                        </div>
                                        <span class="font-bold text-[#243247]">{{ $cat->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $cat->slug }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-700">{{ $cat->subjects_count }} Subjects</td>
                                <td class="px-6 py-4 font-semibold text-gray-700">{{ $cat->topics_count }} Topics</td>
                                <td class="px-6 py-4 font-bold text-[#087F82]">{{ number_format($cat->questions_count) }} MCQs</td>
                                <td class="px-6 py-4">
                                    @if($cat->is_active)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Active</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.categories.edit', $cat->id) }}" class="p-1.5 text-gray-500 hover:text-[#087F82] rounded-lg hover:bg-teal-50 transition-colors" title="Edit Category">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('categories.show', $cat->slug) }}" target="_blank" class="p-1.5 text-gray-500 hover:text-[#153B70] rounded-lg hover:bg-blue-50 transition-colors" title="View Public Page">
                                            <i data-lucide="external-link" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm(@js('Are you sure you want to delete category \"' . $cat->name . '\"? Deletion will be rejected if child subjects or topics exist.'));" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Delete Category">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                    No categories found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
