<x-layouts.admin>
    <x-slot:title>Current Affairs Management - AptiMedha Admin</x-slot:title>
    <x-slot:header>Current Affairs CMS</x-slot:header>

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
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Issues</span>
                    <span class="p-2 rounded-xl bg-blue-50 text-[#153B70]"><i data-lucide="calendar" class="w-4 h-4"></i></span>
                </div>
                <p class="text-2xl font-black text-[#153B70] mt-2">{{ number_format($metrics['total']) }}</p>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Published</span>
                    <span class="p-2 rounded-xl bg-emerald-50 text-emerald-700"><i data-lucide="check-circle-2" class="w-4 h-4"></i></span>
                </div>
                <p class="text-2xl font-black text-emerald-700 mt-2">{{ number_format($metrics['published']) }}</p>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Drafts</span>
                    <span class="p-2 rounded-xl bg-amber-50 text-amber-700"><i data-lucide="file-clock" class="w-4 h-4"></i></span>
                </div>
                <p class="text-2xl font-black text-amber-700 mt-2">{{ number_format($metrics['draft']) }}</p>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Downloads</span>
                    <span class="p-2 rounded-xl bg-teal-50 text-[#087F82]"><i data-lucide="download" class="w-4 h-4"></i></span>
                </div>
                <p class="text-2xl font-black text-[#087F82] mt-2">{{ number_format($metrics['total_downloads']) }}</p>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-2xs col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">PDF Storage</span>
                    <span class="p-2 rounded-xl bg-indigo-50 text-indigo-700"><i data-lucide="hard-drive" class="w-4 h-4"></i></span>
                </div>
                <p class="text-2xl font-black text-indigo-700 mt-2">{{ $metrics['formatted_size'] }}</p>
            </div>
        </div>

        {{-- Header Action Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Monthly PDF Records ({{ $currentAffairs->total() }})
                </span>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('current-affairs.index') }}" target="_blank" class="inline-flex items-center px-3.5 py-2 text-xs font-bold rounded-xl text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors shadow-2xs">
                    <i data-lucide="external-link" class="w-4 h-4 mr-1.5"></i> Public Hub
                </a>
                <a href="{{ route('admin.current-affairs.create') }}" class="inline-flex items-center px-4 py-2.5 text-xs font-bold rounded-xl text-white bg-[#087F82] hover:bg-[#066668] transition-colors shadow-2xs">
                    <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Add Monthly Issue
                </a>
            </div>
        </div>

        {{-- Filter & Search Card --}}
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-2xs">
            <form method="GET" action="{{ route('admin.current-affairs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5">
                {{-- Search --}}
                <div class="lg:col-span-5 relative">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by title or description..."
                           class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">
                </div>

                {{-- Year Filter --}}
                <div class="lg:col-span-2">
                    <select name="year" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">
                        <option value="">All Years</option>
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Month Filter --}}
                <div class="lg:col-span-2">
                    <select name="month" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">
                        <option value="">All Months</option>
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="lg:col-span-2">
                    <select name="status" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Published</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>

                {{-- Submit and Reset --}}
                <div class="lg:col-span-1 flex items-center space-x-2">
                    <button type="submit" class="w-full px-3 py-2 text-xs font-bold rounded-xl bg-[#153B70] text-white hover:bg-[#0f2a50] transition-colors flex items-center justify-center min-h-[38px]">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'year', 'month', 'status']))
                        <a href="{{ route('admin.current-affairs.index') }}" title="Reset Filters" class="p-2 rounded-xl border border-gray-200 text-gray-400 hover:text-gray-700 hover:bg-gray-50 flex items-center justify-center">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Listing --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-[#F7F9FC] border-b border-gray-200/90 text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <th class="py-3.5 px-4">Period</th>
                            <th class="py-3.5 px-4">Title & Overview</th>
                            <th class="py-3.5 px-4">PDF Document</th>
                            <th class="py-3.5 px-4 text-center">Downloads</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($currentAffairs as $affair)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                {{-- Period --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-100 text-[#087F82] flex flex-col items-center justify-center shrink-0">
                                            <span class="text-[10px] uppercase font-bold tracking-wider leading-none">{{ substr($affair->month_name, 0, 3) }}</span>
                                            <span class="text-xs font-black leading-tight">{{ $affair->year }}</span>
                                        </div>
                                        <div>
                                            <span class="font-bold text-[#153B70] block">{{ $affair->month_name }} {{ $affair->year }}</span>
                                            <span class="text-[11px] text-gray-400">Issue #{{ $affair->id }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Title & Overview --}}
                                <td class="py-3.5 px-4">
                                    <div class="max-w-md">
                                        <a href="{{ route('admin.current-affairs.edit', $affair) }}" class="font-bold text-[#243247] hover:text-[#087F82] transition-colors line-clamp-1">
                                            {{ $affair->title }}
                                        </a>
                                        <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">
                                            {{ Str::limit(strip_tags($affair->description ?: 'No overview text provided.'), 90) }}
                                        </p>
                                    </div>
                                </td>

                                {{-- PDF Document --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="file-text" class="w-4 h-4 text-rose-500 shrink-0"></i>
                                        <div>
                                            <span class="text-xs font-semibold text-[#243247] block truncate max-w-[150px]" title="{{ $affair->pdf_name ?: basename($affair->pdf_path) }}">
                                                {{ $affair->pdf_name ?: basename($affair->pdf_path) }}
                                            </span>
                                            <span class="text-[11px] text-gray-400">
                                                {{ $affair->formatted_file_size }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Downloads --}}
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-teal-50 text-[#087F82]">
                                        <i data-lucide="download" class="w-3 h-3 mr-1"></i>
                                        {{ number_format($affair->downloads_count) }}
                                    </span>
                                </td>

                                {{-- Status Toggle --}}
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <form method="POST" action="{{ route('admin.current-affairs.toggle-status', $affair) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition-colors cursor-pointer {{ $affair->is_published ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 border border-gray-200' }}"
                                                title="Click to toggle publication status">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $affair->is_published ? 'bg-emerald-500' : 'bg-gray-400' }} mr-1.5"></span>
                                            {{ $affair->is_published ? 'Published' : 'Draft' }}
                                        </button>
                                    </form>
                                    @if($affair->published_at)
                                        <span class="block text-[10px] text-gray-400 mt-1">
                                            {{ $affair->published_at->format('M d, Y') }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        {{-- Inline Preview --}}
                                        <a href="{{ route('admin.current-affairs.preview', $affair) }}"
                                           target="_blank"
                                           title="Preview PDF document inline"
                                           class="p-1.5 rounded-lg text-gray-500 hover:text-[#087F82] hover:bg-teal-50 transition-colors">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.current-affairs.edit', $affair) }}"
                                           title="Edit Issue"
                                           class="p-1.5 rounded-lg text-gray-500 hover:text-[#153B70] hover:bg-blue-50 transition-colors">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>

                                        {{-- Delete --}}
                                        <form method="POST"
                                              action="{{ route('admin.current-affairs.destroy', $affair) }}"
                                              class="inline-block"
                                              onsubmit="return confirm('Are you sure you want to delete the Current Affairs issue for {{ $affair->period_label }}? This will safely remove the associated PDF and preview image.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Delete Issue"
                                                    class="p-1.5 rounded-lg text-gray-500 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">
                                    <div class="max-w-xs mx-auto text-center">
                                        <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                            <i data-lucide="newspaper" class="w-6 h-6"></i>
                                        </div>
                                        <h4 class="font-bold text-[#243247] text-base mb-1">No Current Affairs Issues</h4>
                                        <p class="text-xs text-gray-500 mb-4">No monthly issues matched your filters. Add a new issue to publish monthly GK material.</p>
                                        <a href="{{ route('admin.current-affairs.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold rounded-xl text-white bg-[#087F82] hover:bg-[#066668] transition-colors">
                                            <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Add Issue
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($currentAffairs->hasPages())
                <div class="p-4 border-t border-gray-100 bg-[#F7F9FC]">
                    {{ $currentAffairs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>

