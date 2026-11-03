<x-layouts.admin>
    <x-slot:title>User Management - AptiMedha Admin</x-slot:title>
    <x-slot:header>Student & Staff Accounts</x-slot:header>

    <div class="space-y-6" x-data="{
        searchQuery: '',
        roleFilter: '',
        users: [
            { id: 1, name: 'Super Administrator', username: 'admin', email: 'admin@aptimedha.com', role: 'Admin', status: 'Active', joined: 'Oct 15, 2026', solved: 0 },
            { id: 2, name: 'Pooja Nair', username: 'pooja_nair', email: 'pooja@aptimedha.com', role: 'Staff / Moderator', status: 'Active', joined: 'Oct 20, 2026', solved: 45 },
            { id: 3, name: 'Rahul Sharma', username: 'rahul_s', email: 'rahul@example.com', role: 'Student', status: 'Active', joined: 'Oct 28, 2026', solved: 184 },
            { id: 4, name: 'Sneha Patel', username: 'sneha_p', email: 'sneha@example.com', role: 'Student', status: 'Active', joined: 'Nov 01, 2026', solved: 92 },
            { id: 5, name: 'Amit Verma', username: 'amit_v', email: 'amit@example.com', role: 'Student', status: 'Active', joined: 'Nov 02, 2026', solved: 140 }
        ]
    }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#153B70]">Registered Users (<span x-text="users.length"></span>)</h2>
                <p class="text-xs text-gray-500">Manage user authorization, roles, and learning profile records.</p>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white p-4 rounded-2xl border border-gray-200/90 shadow-xs flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Search by name, username, or email..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
            </div>

            <select x-model="roleFilter" class="px-3 py-2 border border-gray-200 rounded-xl text-xs focus:border-[#087F82] focus:outline-none w-full sm:w-40">
                <option value="">All Roles</option>
                <option value="Admin">Admin</option>
                <option value="Student">Student</option>
                <option value="Staff / Moderator">Staff</option>
            </select>
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
                            <th class="px-6 py-3.5">Practice Stats</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5">Joined</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="user in users.filter(u => (!roleFilter || u.role === roleFilter) && (!searchQuery || u.name.toLowerCase().includes(searchQuery.toLowerCase()) || u.email.toLowerCase().includes(searchQuery.toLowerCase())))" :key="user.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-[#153B70] text-white flex items-center justify-center font-bold text-xs" x-text="user.name.charAt(0)"></div>
                                        <div>
                                            <p class="font-bold text-[#243247]" x-text="user.name"></p>
                                            <p class="text-[11px] text-gray-400" x-text="user.email"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-500" x-text="'@' + user.username"></td>
                                <td class="px-6 py-4">
                                    <span :class="{
                                        'bg-purple-100 text-purple-800': user.role === 'Admin',
                                        'bg-blue-100 text-[#153B70]': user.role === 'Student',
                                        'bg-teal-100 text-[#087F82]': user.role.includes('Staff')
                                    }" class="px-2.5 py-0.5 rounded-full text-xs font-semibold" x-text="user.role"></span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-[#087F82]" x-text="user.solved + ' MCQs solved'"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800" x-text="user.status"></span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400" x-text="user.joined"></td>
                                <td class="px-6 py-4 text-right">
                                    <a :href="'{{ url('admin/users') }}/' + user.id + '/edit'" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-[#153B70] rounded-lg transition-colors">
                                        Manage
                                    </a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
