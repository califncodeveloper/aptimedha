<x-layouts.admin>
    <x-slot:title>Placement Preparation Guides - AptiMedha Admin</x-slot:title>
    <x-slot:header>Placement Preparation CMS</x-slot:header>

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
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Guides</span>
                    <span class="p-2 rounded-xl bg-blue-50 text-[#153B70]"><i data-lucide="briefcase" class="w-4 h-4"></i></span>
                </div>
                <p class="text-2xl font-black text-[#153B70] mt-2">{{ number_format($stats['total']) }}</p>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Published</span>
                    <span class="p-2 rounded-xl bg-emerald-50 text-emerald-700"><i data-lucide="check-circle-2" class="w-4 h-4"></i></span>
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
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">With Downloads</span>
                    <span class="p-2 rounded-xl bg-teal-50 text-[#087F82]"><i data-lucide="download" class="w-4 h-4"></i></span>
                </div>
                <p class="text-2xl font-black text-[#087F82] mt-2">{{ number_format($stats['with_downloads']) }}</p>
            </div>
        </div>

        {{-- Header Action Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Placement Curriculum & Guides ({{ $guides->total() }})
                </span>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('placement.index') }}" target="_blank" class="inline-flex items-center px-3.5 py-2 text-xs font-bold rounded-xl text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors shadow-2xs">
                    <i data-lucide="external-link" class="w-4 h-4 mr-1.5"></i> Public Hub
                </a>
                <a href="{{ route('admin.placement.create') }}" class="inline-flex items-center px-4 py-2.5 text-xs font-bold rounded-xl text-white bg-[#087F82] hover:bg-[#066668] transition-colors shadow-2xs">
                    <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> New Placement Guide
                </a>
            </div>
        </div>

        {{-- Filters Card --}}
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-2xs">
            <form method="GET" action="{{ route('admin.placement.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                {{-- Search --}}
                <div class="sm:col-span-2">
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search guides by title or excerpt..." class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-gray-300 focus:border-[#087F82] focus:outline-none">
                    </div>
                </div>

                {{-- Category Filter --}}
                <div>
                    <select name="category" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-300 focus:border-[#087F82] focus:outline-none">
                        <option value="">All Categories</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" @selected(request('category') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter & Submit --}}
                <div class="flex items-center space-x-2">
                    <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-300 focus:border-[#087F82] focus:outline-none">
                        <option value="">All Statuses</option>
                        <option value="published" @selected(request('status') === 'published')>Published</option>
                        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-[#153B70] text-white rounded-xl text-xs font-bold hover:bg-[#0e2a52] transition-colors shrink-0">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'category', 'status']))
                        <a href="{{ route('admin.placement.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl border border-gray-200 transition-colors" title="Reset Filters">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-[#243247]">
                    <thead class="bg-gray-50/75 border-b border-gray-200 text-gray-500 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-4 py-3.5">Order</th>
                            <th class="px-4 py-3.5">Guide Title & Details</th>
                            <th class="px-4 py-3.5">Category</th>
                            <th class="px-4 py-3.5">Asset</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-4 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($guides as $guide)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-4 py-3 font-mono font-bold text-gray-400">
                                    #{{ $guide->display_order }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-start space-x-3">
                                        @if($guide->featured_image)
                                            <img src="{{ $guide->featured_image_url }}" alt="{{ $guide->featured_image_alt ?: $guide->title }}" class="w-12 h-12 rounded-lg object-cover border border-gray-200 shrink-0">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 shrink-0">
                                                <i data-lucide="book-open" class="w-5 h-5"></i>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.placement.edit', $guide) }}" class="font-bold text-[#153B70] hover:text-[#087F82] transition-colors text-sm line-clamp-1">
                                                {{ $guide->title }}
                                            </a>
                                            <p class="text-[11px] text-gray-500 font-mono mt-0.5">/placement-preparation/{{ $guide->slug }}</p>
                                            @if($guide->excerpt)
                                                <p class="text-xs text-gray-600 line-clamp-1 mt-1">{{ $guide->excerpt }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $guide->categoryBadgeClass() }}">
                                        {{ $guide->categoryLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($guide->hasDownloadableAsset())
                                        <a href="{{ route('placement.download', $guide->slug) }}" target="_blank" class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-teal-50 text-[#087F82] hover:bg-teal-100 transition-colors border border-teal-200" title="Download: {{ $guide->downloadable_asset_name }}">
                                            <i data-lucide="file-down" class="w-3.5 h-3.5 mr-1"></i> Asset
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-[11px]">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('admin.placement.toggle-status', $guide) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold transition-all cursor-pointer {{ $guide->is_published ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200' }}" title="Click to toggle publication">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $guide->is_published ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                            {{ $guide->is_published ? 'Published' : 'Draft' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <a href="{{ route('admin.placement.preview', $guide) }}" class="p-1.5 rounded-lg text-gray-500 hover:text-[#153B70] hover:bg-gray-100 transition-colors" title="Admin Preview">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('admin.placement.edit', $guide) }}" class="p-1.5 rounded-lg text-gray-500 hover:text-[#087F82] hover:bg-teal-50 transition-colors" title="Edit Guide">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('admin.placement.destroy', $guide) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this placement guide? Associated unreferenced assets will be cleaned up safely.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Delete Guide">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <i data-lucide="briefcase" class="w-10 h-10 mx-auto mb-2 text-gray-300"></i>
                                    <p class="font-bold text-gray-500 text-sm">No placement guides found.</p>
                                    <p class="text-xs text-gray-400 mt-1">Get started by creating your first placement guide or adjusting filter criteria.</p>
                                    <a href="{{ route('admin.placement.create') }}" class="inline-flex items-center px-4 py-2 mt-4 text-xs font-bold rounded-xl text-white bg-[#087F82] hover:bg-[#066668] transition-colors">
                                        <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Create Placement Guide
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($guides->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $guides->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>

