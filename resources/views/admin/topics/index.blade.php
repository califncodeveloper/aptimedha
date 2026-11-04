<x-layouts.admin>
    <x-slot:title>Topic Management - AptiMedha Admin</x-slot:title>
    <x-slot:header>Leaf Topic Taxonomy</x-slot:header>

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
                <h2 class="text-xl font-bold text-[#153B70]">Leaf Topics ({{ $topics->total() }})</h2>
                <p class="text-xs text-gray-500">Target topics where MCQs and question sets attach directly.</p>
            </div>

            <a href="{{ route('admin.topics.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold rounded-xl text-white bg-[#153B70] hover:bg-[#0e2a52] transition-colors shadow-xs">
                <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Add New Topic
            </a>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white p-4 rounded-2xl border border-gray-200/90 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
            <form action="{{ route('admin.topics.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <div class="relative w-full sm:w-56">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Search topic name..."
                           class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                </div>

                <div class="w-full sm:w-48">
                    <select name="category_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                        <option value="">-- All Categories --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ ($categoryId ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-48">
                    <select name="subject_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                        <option value="">-- All Subjects --</option>
                        @foreach($subjects as $subj)
                            <option value="{{ $subj->id }}" {{ ($subjectId ?? '') == $subj->id ? 'selected' : '' }}>
                                {{ $subj->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700">Filter</button>
                    @if(!empty($search) || !empty($categoryId) || !empty($subjectId))
                        <a href="{{ route('admin.topics.index') }}" class="px-3 py-2 text-xs font-semibold rounded-xl text-gray-500 hover:text-gray-700">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Topics Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5">ID</th>
                            <th class="px-6 py-3.5">Topic Name</th>
                            <th class="px-6 py-3.5">Parent Hierarchy</th>
                            <th class="px-6 py-3.5">URL Slug</th>
                            <th class="px-6 py-3.5">MCQs</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topics as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-mono text-gray-400">{{ $item->id }}</td>
                                <td class="px-6 py-4 font-bold text-[#243247]">{{ $item->name }}</td>
                                <td class="px-6 py-4">
                                    @if($item->subject)
                                        <span class="font-semibold text-gray-700">{{ $item->subject->name }}</span>
                                    @else
                                        <span class="font-semibold text-gray-400 italic">Direct Topic</span>
                                    @endif
                                    <p class="text-[11px] text-gray-400">{{ $item->category->name ?? 'Unassigned' }}</p>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $item->slug }}</td>
                                <td class="px-6 py-4 font-bold text-[#087F82]">{{ number_format($item->questions_count) }} MCQs</td>
                                <td class="px-6 py-4">
                                    @if($item->is_active)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Active</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.topics.edit', $item->id) }}" class="p-1.5 text-gray-500 hover:text-[#087F82] rounded-lg hover:bg-teal-50 transition-colors" title="Edit Topic">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('mcq.index', ['category' => $item->category->slug ?? '', 'topic' => $item->slug]) }}" target="_blank" class="p-1.5 text-gray-500 hover:text-[#153B70] rounded-lg hover:bg-blue-50 transition-colors" title="Practice Topic MCQs">
                                            <i data-lucide="external-link" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('admin.topics.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete topic &quot;{{ $item->name }}&quot;? Deletion will be rejected if associated MCQs exist.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Delete Topic">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                    No topics found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($topics->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $topics->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
