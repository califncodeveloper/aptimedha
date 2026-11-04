<x-layouts.admin>
    <x-slot:title>Edit User - {{ $user->name }} - AptiMedha Admin</x-slot:title>
    <x-slot:header>Manage Account: {{ $user->name }}</x-slot:header>

    <div class="space-y-6 max-w-2xl">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to All Users
            </a>
            @if($user->isAdmin())
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Administrator</span>
            @else
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-[#153B70]">Student / User</span>
            @endif
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Username</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Platform Role</label>
                    <select name="role" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                        <option value="user" {{ old('role', $user->role->value ?? $user->role) === 'user' ? 'selected' : '' }}>Student / Standard Learner</option>
                        <option value="admin" {{ old('role', $user->role->value ?? $user->role) === 'admin' ? 'selected' : '' }}>System Administrator</option>
                    </select>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <button type="button" onclick="if(confirm('Delete user account permanently?')) document.getElementById('delete-user-form').submit();" class="text-xs font-bold text-rose-600 hover:underline cursor-pointer">
                        Delete Account
                    </button>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#153B70] hover:bg-[#0e2a52] text-white text-xs font-bold shadow-xs transition-colors cursor-pointer">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>

            <form id="delete-user-form" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</x-layouts.admin>
