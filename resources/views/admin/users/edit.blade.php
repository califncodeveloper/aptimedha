<x-layouts.admin>
    <x-slot:title>Manage User - AptiMedha Admin</x-slot:title>
    <x-slot:header>Manage Account: Rahul Sharma</x-slot:header>

    <div class="space-y-6 max-w-2xl">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to All Users
            </a>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Active</span>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8">
            <form action="{{ route('admin.users.update', 3) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Full Name</label>
                    <input type="text" name="name" value="Rahul Sharma" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Username</label>
                        <input type="text" name="username" value="rahul_s" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Email</label>
                        <input type="email" name="email" value="rahul@example.com" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Platform Role</label>
                        <select name="role" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="user" selected>Student / General User</option>
                            <option value="moderator">Staff / Moderator</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Account Status</label>
                        <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="active" selected>Active & Verified</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <button type="button" class="text-xs font-bold text-amber-600 hover:underline">
                        Send Password Reset Link
                    </button>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#153B70] hover:bg-[#0e2a52] text-white text-xs font-bold shadow-xs transition-colors">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
