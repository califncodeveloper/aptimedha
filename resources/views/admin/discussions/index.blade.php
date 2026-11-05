<x-layouts.admin>
    <x-slot:title>Discussion Moderation - AptiMedha Admin</x-slot:title>
    <x-slot:header>Discussion & Doubt Moderation</x-slot:header>

    <div class="space-y-6">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#153B70]">Community Posts ({{ $discussions->total() }})</h2>
                <p class="text-xs text-gray-500">Ensure respectful peer collaboration and prevent spam or inappropriate messages.</p>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white p-4 rounded-2xl border border-gray-200/90 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
            {{-- Status Tabs --}}
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <a href="{{ route('admin.discussions.index', array_filter(['search' => $search])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ empty($status) ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    All Posts ({{ $counts['all'] }})
                </a>
                <a href="{{ route('admin.discussions.index', array_filter(['status' => 'approved', 'search' => $search])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $status === 'approved' ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Approved ({{ $counts['approved'] }})
                </a>
                <a href="{{ route('admin.discussions.index', array_filter(['status' => 'pending', 'search' => $search])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $status === 'pending' ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Pending ({{ $counts['pending'] }})
                </a>
                <a href="{{ route('admin.discussions.index', array_filter(['status' => 'hidden', 'search' => $search])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $status === 'hidden' ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Hidden ({{ $counts['hidden'] }})
                </a>
            </div>

            {{-- Search Form --}}
            <form method="GET" action="{{ route('admin.discussions.index') }}" class="flex items-center gap-2 w-full md:w-auto">
                @if ($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search comments, users..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-gray-300 focus:border-[#087F82] focus:outline-none">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-2.5 top-2"></i>
                </div>
                <button type="submit" class="px-3 py-1.5 bg-[#087F82] text-white text-xs font-bold rounded-xl hover:bg-[#066668]">
                    Filter
                </button>
                @if ($search || $status)
                    <a href="{{ route('admin.discussions.index') }}" class="px-2 py-1.5 text-xs text-gray-500 hover:text-gray-700">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Discussions Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            @if ($discussions->isNotEmpty())
                <div class="overflow-x-auto scrollbar-thin">
                    <table class="w-full text-left text-xs sm:text-sm min-w-[720px]">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3.5">User</th>
                                <th class="px-6 py-3.5">Question Target</th>
                                <th class="px-6 py-3.5">Comment Text</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5">Date</th>
                                <th class="px-6 py-3.5 text-right">Moderation Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($discussions as $item)
                                @php $q = $item->question; @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-[#153B70] whitespace-nowrap">
                                        {{ $item->user?->name ?? 'User #' . $item->user_id }}
                                        <p class="text-[11px] font-normal text-gray-400">{{ $item->user?->email }}</p>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-600">
                                        @if ($q)
                                            <a href="{{ route('admin.mcqs.edit', $q->id) }}" class="text-[#153B70] hover:text-[#087F82]">
                                                Q#{{ $q->id }} ({{ $q->topicName ?: 'MCQ' }})
                                            </a>
                                            <p class="text-[11px] text-gray-400">{{ $q->categoryName }}</p>
                                        @else
                                            <span class="text-gray-400">Deleted Question</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 max-w-sm">
                                        <p class="text-xs text-gray-800 line-clamp-3 whitespace-pre-line">{{ $item->comment }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($item->status === 'approved')
                                            <span class="bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                                Approved
                                            </span>
                                        @elseif ($item->status === 'pending')
                                            <span class="bg-amber-100 text-amber-800 px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                                Pending
                                            </span>
                                        @else
                                            <span class="bg-rose-100 text-rose-800 px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                                Hidden
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-400 whitespace-nowrap">
                                        {{ $item->created_at->format('M d, Y') }}
                                        <p class="text-[10px]">{{ $item->created_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($item->status !== 'approved')
                                                <form method="POST" action="{{ route('admin.discussions.status', $item->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-bold transition-colors">
                                                        Approve
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($item->status !== 'hidden')
                                                <form method="POST" action="{{ route('admin.discussions.status', $item->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="hidden">
                                                    <button type="submit" class="px-2.5 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg text-xs font-bold transition-colors">
                                                        Hide
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.discussions.destroy', $item->id) }}" onsubmit="return confirm('Permanently delete this discussion comment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg text-xs font-bold transition-colors">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-4 border-t border-gray-100">
                    {{ $discussions->links() }}
                </div>
            @else
                <div class="text-center py-12 text-gray-400">
                    <i data-lucide="message-square" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                    <p class="text-sm">No discussion posts found matching the criteria.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
