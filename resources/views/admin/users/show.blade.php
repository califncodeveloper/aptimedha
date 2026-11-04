<x-layouts.admin>
    <x-slot:title>User Profile - {{ $user->name }} - AptiMedha Admin</x-slot:title>
    <x-slot:header>User Account: {{ $user->name }}</x-slot:header>

    <div class="space-y-6 max-w-4xl">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Users
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="px-3.5 py-1.5 rounded-lg bg-[#087F82] text-white text-xs font-bold hover:bg-[#066668] transition-colors">
                    Edit Account
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
                <div class="w-16 h-16 rounded-2xl bg-[#153B70] text-white flex items-center justify-center font-black text-2xl uppercase shadow-md shadow-[#153B70]/20">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-[#243247]">{{ $user->name }}</h2>
                    <p class="text-xs text-gray-500">{{ $user->email }} &bull; @<span>{{ $user->username ?? 'user_' . $user->id }}</span></p>
                    <div class="flex items-center gap-2 mt-2">
                        @if($user->isAdmin())
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Administrator</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-[#153B70]">Student / Learner</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-xl font-bold text-[#153B70]">{{ $user->bookmarks_count }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Saved Bookmarks</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-xl font-bold text-[#087F82]">{{ $user->discussions_count }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Discussions</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-xl font-bold text-amber-600">{{ $user->reports_count }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Reports Filed</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-xl font-bold text-teal-700">{{ $user->blogs_count }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Authored Articles</p>
                </div>
            </div>

            <div class="pt-2 text-xs text-gray-400 flex flex-col sm:flex-row sm:items-center justify-between border-t border-gray-100 gap-2">
                <span>Account Created: {{ $user->created_at?->format('F d, Y \a\t H:i') ?? 'N/A' }}</span>
                <span>Last Updated: {{ $user->updated_at?->diffForHumans() ?? 'N/A' }}</span>
            </div>
        </div>

        {{-- Recent Activity by this User --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Recent Discussions --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 space-y-3">
                <h3 class="text-sm font-bold text-[#153B70] flex items-center gap-2">
                    <i data-lucide="message-square" class="w-4 h-4 text-[#087F82]"></i>
                    Recent Discussions ({{ $user->discussions_count }})
                </h3>
                @forelse($recentDiscussions as $discussion)
                    <div class="p-3 bg-gray-50 rounded-xl text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-[#153B70]">Question #{{ $discussion->question_id }}</span>
                            <span class="text-gray-400 text-[10px]">{{ $discussion->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-700 line-clamp-2">{{ $discussion->comment ?? $discussion->body }}</p>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 italic">No discussions posted yet.</p>
                @endforelse
            </div>

            {{-- Recent Reports --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 space-y-3">
                <h3 class="text-sm font-bold text-[#153B70] flex items-center gap-2">
                    <i data-lucide="flag" class="w-4 h-4 text-amber-500"></i>
                    Recent Reports ({{ $user->reports_count }})
                </h3>
                @forelse($recentReports as $report)
                    <div class="p-3 bg-gray-50 rounded-xl text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-[#153B70]">Question #{{ $report->question_id }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                            </span>
                        </div>
                        <p class="text-gray-700 line-clamp-2">{{ $report->description }}</p>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 italic">No error reports submitted.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.admin>
