<x-layouts.admin>
    <x-slot:title>User Management - AptiMedha Admin</x-slot:title>
    <x-slot:header>Student & Staff Accounts</x-slot:header>

    <div class="space-y-6">
        {{-- Flash Messages --}}
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
                <h2 class="text-xl font-bold text-[#153B70]">Registered Users ({{ $users->total() }})</h2>
                <p class="text-xs text-gray-500">Real database records of learners, contributors and administrative users.</p>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="px-3 py-1 rounded-lg bg-blue-50 text-[#153B70] font-semibold border border-blue-100">
                    Total: {{ number_format($totalUsers) }}
                </span>
                <span class="px-3 py-1 rounded-lg bg-teal-50 text-[#087F82] font-semibold border border-teal-100">
                    Students: {{ number_format($studentCount) }}
                </span>
                <span class="px-3 py-1 rounded-lg bg-purple-50 text-purple-700 font-semibold border border-purple-100">
                    Admins: {{ number_format($adminCount) }}
                </span>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white p-4 rounded-2xl border border-gray-200/90 shadow-xs">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search by name, username, or email..."
                           class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                </div>

                <select name="role" class="px-3 py-2 border border-gray-200 rounded-xl text-xs focus:border-[#087F82] focus:outline-none w-full sm:w-44">
                    <option value="">All Roles</option>
                    <option value="user" {{ $roleFilter === 'user' ? 'selected' : '' }}>Student / User</option>
                    <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>

                <button type="submit" class="px-4 py-2 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold transition-colors shadow-xs w-full sm:w-auto">
                    Apply Filter
                </button>

                @if($search !== '' || $roleFilter !== '')
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded-xl border border-gray-300 text-xs text-gray-600 hover:bg-gray-50 text-center w-full sm:w-auto">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Users Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5">User</th>
                            <th class="px-6 py-3.5">Username</th>
                            <th class="px-6 py-3.5">Role</th>
                            <th class="px-6 py-3.5">Activity Stats</th>
                            <th class="px-6 py-3.5">Joined</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-[#153B70] text-white flex items-center justify-center font-bold text-xs uppercase">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $user) }}" class="font-bold text-[#243247] hover:text-[#087F82]">
                                                {{ $user->name }}
                                            </a>
                                            <p class="text-[11px] text-gray-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-500">
                                    {{ '@' . ($user->username ?? 'user_' . $user->id) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->isAdmin())
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                            Admin
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-[#153B70]">
                                            Student / User
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    <span class="font-semibold text-[#087F82]">{{ $user->bookmarks_count }}</span> bookmarks &bull;
                                    <span class="font-semibold text-[#153B70]">{{ $user->discussions_count }}</span> posts &bull;
                                    <span class="font-semibold text-amber-600">{{ $user->reports_count }}</span> reports
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400">
                                    {{ $user->created_at?->format('M d, Y') ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.users.show', $user) }}" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-[#153B70] rounded-lg transition-colors">
                                            View
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="px-2.5 py-1 bg-teal-50 hover:bg-teal-100 text-xs font-bold text-[#087F82] rounded-lg transition-colors">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <i data-lucide="users" class="w-10 h-10 mx-auto mb-2 text-gray-300"></i>
                                    <p class="font-bold text-gray-600">No users found</p>
                                    <p class="text-xs text-gray-400 mt-1">Try adjusting your search criteria or role filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
