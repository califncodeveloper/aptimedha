<x-layouts.admin>
    <x-slot:title>Blog Categories - AptiMedha Admin</x-slot:title>
    <x-slot:header>Blog Categories</x-slot:header>

    <div class="space-y-6">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div data-auto-dismiss class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div data-auto-dismiss class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        {{-- Top Navigation --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.blogs.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-gray-700 border border-gray-200 hover:border-[#153B70] hover:text-[#153B70] transition-colors">
                    ← Back to Articles
                </a>
                <span class="px-4 py-2 rounded-xl text-xs font-bold bg-[#153B70] text-white">
                    Categories ({{ $categories->total() }})
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- Category Creation Form --}}
            <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-gray-200/90 shadow-2xs space-y-4">
                <h3 class="text-sm font-bold text-[#153B70] flex items-center">
                    <i data-lucide="folder-plus" class="w-4 h-4 mr-2 text-[#087F82]"></i> Add New Category
                </h3>
                <p class="text-xs text-gray-500">Reusable topic categories to group placement advice, exams, and articles.</p>

                <form action="{{ route('admin.blog-categories.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Category Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" required placeholder="e.g. Career Roadmaps" value="{{ old('name') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            URL Slug (Optional)
                        </label>
                        <input type="text" name="slug" placeholder="e.g. career-roadmaps" value="{{ old('slug') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs font-mono focus:border-[#087F82] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Description
                        </label>
                        <textarea name="description" rows="3" placeholder="Brief explanation of articles under this category..."
                                  class="w-full p-3 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">{{ old('description') }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold transition-colors shadow-2xs">
                        Create Category
                    </button>
                </form>
            </div>

            {{-- Categories Table --}}
            <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-200/90 shadow-2xs overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-gray-600 uppercase tracking-wider">Existing Categories</h3>
                    <form method="GET" action="{{ route('admin.blog-categories.index') }}" class="flex items-center space-x-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                               class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 focus:outline-none focus:border-[#087F82]">
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-3">Category</th>
                                <th class="px-5 py-3">Slug</th>
                                <th class="px-5 py-3">Articles</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($categories as $category)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-[#153B70]">{{ $category->name }}</div>
                                        @if($category->description)
                                            <p class="text-[11px] text-gray-400 truncate max-w-xs">{{ $category->description }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 font-mono text-xs text-[#087F82]">
                                        {{ $category->slug }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-teal-50 text-[#087F82]">
                                            {{ $category->blogs_count }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.blog-categories.edit', $category) }}" class="p-1.5 text-gray-500 hover:text-[#087F82] rounded-lg hover:bg-teal-50" title="Edit Category">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </a>
                                            <form action="{{ route('admin.blog-categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm(@js('Delete category \'' . $category->name . '\'?'));">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50" title="Delete Category">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-xs text-gray-400">
                                        No categories found. Create one using the form on the left.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($categories->hasPages())
                    <div class="p-3 border-t border-gray-100">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>

