<x-layouts.user>
    <x-slot:title>My Profile Settings - AptiMedha</x-slot:title>

    <div class="space-y-8 max-w-4xl">
        <div>
            <h1 class="text-2xl font-bold text-[#243247]">Profile & Account Settings</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your identity, login email, and security settings.</p>
        </div>

        {{-- Profile Header Card --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-[#153B70] text-white flex items-center justify-center font-black text-2xl uppercase shadow-md shadow-[#153B70]/20">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-[#243247]">{{ Auth::user()->name ?? 'Learner' }}</h2>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email ?? 'user@example.com' }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            Active Account
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-[#153B70]">
                            Registered Student
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            {{-- Edit Personal Details Form --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-7">
                <h3 class="text-base font-bold text-[#153B70] mb-4">Personal Details</h3>

                <form action="{{ route('user.profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Full Name</label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', Auth::user()->name) }}"
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none">
                    </div>

                    <div>
                        <label for="username" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Username</label>
                        <input type="text"
                               name="username"
                               id="username"
                               value="{{ old('username', Auth::user()->username) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Email Address</label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email', Auth::user()->email) }}"
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            {{-- Change Password Form --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-7">
                <h3 class="text-base font-bold text-[#153B70] mb-4">Change Password</h3>

                <form action="{{ route('user.profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Current Password</label>
                        <input type="password"
                               name="current_password"
                               id="current_password"
                               placeholder="Enter existing password"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none">
                    </div>

                    <div>
                        <label for="new_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">New Password</label>
                        <input type="password"
                               name="new_password"
                               id="new_password"
                               placeholder="Min 8 characters"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none">
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Confirm New Password</label>
                        <input type="password"
                               name="new_password_confirmation"
                               id="new_password_confirmation"
                               placeholder="Confirm new password"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-[#153B70] hover:bg-[#0e2a52] text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-layouts.user>
