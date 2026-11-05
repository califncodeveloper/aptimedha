<x-layouts.admin>
    <x-slot:title>Contact Submissions - AptiMedha Admin</x-slot:title>
    <x-slot:header>Contact Messages & Inquiries</x-slot:header>

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
                <h2 class="text-xl font-bold text-[#153B70]">Inquiries & Feedback ({{ $messages->total() }})</h2>
                <p class="text-xs text-gray-500">View and respond to inquiries submitted through the public contact form.</p>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white p-4 rounded-2xl border border-gray-200/90 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
            {{-- Status Tabs --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.contacts.index', array_filter(['search' => $search])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ empty($status) ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    All ({{ $totalCount }})
                </a>
                <a href="{{ route('admin.contacts.index', array_filter(['status' => 'unread', 'search' => $search])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $status === 'unread' ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Unread ({{ $unreadCount }})
                </a>
                <a href="{{ route('admin.contacts.index', array_filter(['status' => 'read', 'search' => $search])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $status === 'read' ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Read ({{ $totalCount - $unreadCount }})
                </a>
            </div>

            {{-- Search Form --}}
            <form method="GET" action="{{ route('admin.contacts.index') }}" class="flex items-center gap-2 w-full md:w-auto">
                @if ($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search name, email, subject..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-gray-300 focus:border-[#087F82] focus:outline-none">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-2.5 top-2"></i>
                </div>
                <button type="submit" class="px-3 py-1.5 bg-[#087F82] text-white text-xs font-bold rounded-xl hover:bg-[#066668]">
                    Filter
                </button>
                @if ($search || $status)
                    <a href="{{ route('admin.contacts.index') }}" class="px-2 py-1.5 text-xs text-gray-500 hover:text-gray-700">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            @if ($messages->isNotEmpty())
                <div class="overflow-x-auto scrollbar-thin">
                    <table class="w-full text-left text-xs sm:text-sm min-w-[700px]">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3.5">Sender</th>
                                <th class="px-6 py-3.5">Subject</th>
                                <th class="px-6 py-3.5">Message Snippet</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5">Date</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($messages as $msg)
                                <tr class="hover:bg-gray-50/50 transition-colors {{ !$msg->is_read ? 'bg-teal-50/20 font-semibold' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="font-bold text-[#153B70]">{{ $msg->name }}</p>
                                        <a href="mailto:{{ $msg->email }}" class="text-xs text-gray-500 hover:text-[#087F82]">{{ $msg->email }}</a>
                                    </td>
                                    <td class="px-6 py-4 max-w-xs truncate text-[#243247]">
                                        {{ $msg->subject }}
                                    </td>
                                    <td class="px-6 py-4 max-w-sm truncate text-xs text-gray-600">
                                        {{ $msg->message }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if (!$msg->is_read)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                                Unread
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                Read
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-400 whitespace-nowrap">
                                        {{ $msg->created_at->format('M d, Y') }}
                                        <p class="text-[10px]">{{ $msg->created_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.contacts.show', $msg->id) }}" class="px-3 py-1 bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold rounded-lg transition-colors">
                                                View
                                            </a>
                                            <form method="POST" action="{{ route('admin.contacts.destroy', $msg->id) }}" onsubmit="return confirm('Delete this contact message?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
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
                    {{ $messages->links() }}
                </div>
            @else
                <div class="text-center py-12 text-gray-400">
                    <i data-lucide="mail" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                    <p class="text-sm">No contact messages found.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>

