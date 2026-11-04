<x-layouts.admin>
    <x-slot:title>Blog Management - AptiMedha Admin</x-slot:title>
    <x-slot:header>Editorial & Career Articles</x-slot:header>

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

        {{-- Statistics Row --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Articles</span>
                    <span class="p-2 rounded-xl bg-blue-50 text-[#153B70]"><i data-lucide="file-text" class="w-4 h-4"></i></span>
                </div>
                <p class="text-2xl font-black text-[#153B70] mt-2">{{ number_format($stats['total']) }}</p>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Published</span>
                    <span class="p-2 rounded-xl bg-emerald-50 text-emerald-700"><i data-lucide="check" class="w-4 h-4"></i></span>
                </div>
                <p class="text-2xl font-black text-emerald-700 mt-2">{{ number_format($stats['published']) }}</p>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Drafts</span>
                    <span class="p-2 rounded-xl bg-amber-50 text-amber-700"><i data-lucide="file-clock" class="w-4 h-4"></i></span>
                </div>
                <p class="text-2xl font-black text-amber-700 mt-2">{{ number_format($stats['draft']) }}</p>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Views</span>
                    <span class="p-2 rounded-xl bg-teal-50 text-[#087F82]"><i data-lucide="eye" class="w-4 h-4"></i></span>
                </div>
                <p class="text-2xl font-black text-[#087F82] mt-2">{{ number_format($stats['views']) }}</p>
            </div>
        </div>

        {{-- Header Navigation & Action Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.blogs.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#153B70] text-white shadow-2xs">
                    All Articles ({{ $blogs->total() }})
                </a>
                <a href="{{ route('admin.blog-categories.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-gray-700 border border-gray-200 hover:border-[#087F82] hover:text-[#087F82] transition-colors">
                    Manage Categories
                </a>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.blogs.create') }}" class="inline-flex items-center px-4 py-2.5 text-xs font-bold rounded-xl text-white bg-[#087F82] hover:bg-[#066668] transition-colors shadow-2xs">
                    <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Write New Article
                </a>
            </div>
        </div>

        {{-- Filters Card --}}
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-2xs">
            <form method="GET" action="{{ route('admin.blogs.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                {{-- Search --}}
                <div class="sm:col-span-2">
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by article title, excerpt, author..." class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-gray-300 focus:border-[#087F82] focus:outline-none">
                    </div>
                </div>

                {{-- Status Filter --}}
                <div>
                    <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-300 focus:border-[#087F82] focus:outline-none">
                        <option value="">All Statuses</option>
                        <option value="published" @selected(request('status') === 'published')>Published</option>
                        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        <option value="archived" @selected(request('status') === 'archived')>Archived</option>
                    </select>
                </div>

                {{-- Category Filter & Submit --}}
                <div class="flex items-center gap-2">
                    <select name="category" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-300 focus:border-[#087F82] focus:outline-none">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string)request('category') === (string)$category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-[#153B70] text-white text-xs font-bold rounded-xl hover:bg-[#0e2a52] transition-colors whitespace-nowrap">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'category']))
                        <a href="{{ route('admin.blogs.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl" title="Reset Filters">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Articles Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5">Article</th>
                            <th class="px-6 py-3.5">Category</th>
                            <th class="px-6 py-3.5">Author</th>
                            <th class="px-6 py-3.5">Readership</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($blogs as $post)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        @if($post->featured_image_url)
                                            <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="w-12 h-10 object-cover rounded-lg border border-gray-200 flex-shrink-0">
                                        @else
                                            <div class="w-12 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 flex-shrink-0">
                                                <i data-lucide="image" class="w-5 h-5"></i>
                                            </div>
                                        @endif
                                        <div class="max-w-md">
                                            <a href="{{ route('admin.blogs.edit', $post) }}" class="font-bold text-[#153B70] hover:underline">
                                                {{ $post->title }}
                                            </a>
                                            <p class="text-[11px] text-gray-400 mt-0.5">
                                                Published: {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Unscheduled' }}
                                                • Slug: <span class="font-mono text-gray-500">/blog/{{ $post->slug }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($post->category)
                                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-[#087F82]/10 text-[#087F82]">
                                            {{ $post->category->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Uncategorized</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-600">
                                    {{ $post->author_display_name }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-[#153B70]">
                                    {{ number_format($post->views_count) }} views
                                </td>
                                <td class="px-6 py-4">
                                    @if($post->status === 'published')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Published
                                        </span>
                                    @elseif($post->status === 'draft')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span> Draft
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            Archived
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Preview --}}
                                        <a href="{{ route('admin.blogs.preview', $post) }}" class="p-1.5 text-gray-500 hover:text-[#153B70] rounded-lg hover:bg-blue-50 transition-colors" title="Preview Article">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>

                                        {{-- Toggle Status --}}
                                        <form action="{{ route('admin.blogs.toggle-status', $post) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-1.5 text-gray-500 hover:text-amber-600 rounded-lg hover:bg-amber-50 transition-colors" title="{{ $post->status === 'published' ? 'Unpublish (Make Draft)' : 'Publish Article' }}">
                                                <i data-lucide="{{ $post->status === 'published' ? 'eye-off' : 'check' }}" class="w-4 h-4"></i>
                                            </button>
                                        </form>

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.blogs.edit', $post) }}" class="p-1.5 text-gray-500 hover:text-[#087F82] rounded-lg hover:bg-teal-50 transition-colors" title="Edit Article">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>

                                        {{-- Delete with confirmation --}}
                                        <form action="{{ route('admin.blogs.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this article: \'{{ addslashes($post->title) }}\'? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Delete Article">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="max-w-xs mx-auto text-center space-y-2">
                                        <div class="w-12 h-12 rounded-full bg-gray-100 mx-auto flex items-center justify-center text-gray-400">
                                            <i data-lucide="file-question" class="w-6 h-6"></i>
                                        </div>
                                        <p class="text-sm font-bold text-gray-700">No articles found</p>
                                        <p class="text-xs text-gray-500">There are no articles matching your current filter criteria.</p>
                                        <div class="pt-2">
                                            <a href="{{ route('admin.blogs.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold text-white bg-[#087F82] rounded-xl hover:bg-[#066668]">
                                                Write First Article
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($blogs->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $blogs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
