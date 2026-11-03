<x-layouts.admin>
    <x-slot:title>User Profile - AptiMedha Admin</x-slot:title>
    <x-slot:header>Student Account: Rahul Sharma</x-slot:header>

    <div class="space-y-6 max-w-3xl">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Users
            </a>
            <a href="{{ route('admin.users.edit', 3) }}" class="px-3.5 py-1.5 rounded-lg bg-[#087F82] text-white text-xs font-bold hover:bg-[#066668]">
                Edit Account
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
                <div class="w-16 h-16 rounded-2xl bg-[#153B70] text-white flex items-center justify-center font-black text-2xl uppercase shadow-md shadow-[#153B70]/20">
                    R
                </div>
                <div>
                    <h2 class="text-xl font-bold text-[#243247]">Rahul Sharma</h2>
                    <p class="text-xs text-gray-500">rahul@example.com &bull; @rahul_s</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-[#153B70]">Student</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Active</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-xl font-bold text-[#153B70]">184</p>
                    <p class="text-xs text-gray-400 mt-0.5">MCQs Solved</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-xl font-bold text-[#087F82]">78%</p>
                    <p class="text-xs text-gray-400 mt-0.5">Accuracy</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-xl font-bold text-[#153B70]">12</p>
                    <p class="text-xs text-gray-400 mt-0.5">Bookmarks</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-xl font-bold text-amber-600">2</p>
                    <p class="text-xs text-gray-400 mt-0.5">Reports Filed</p>
                </div>
            </div>

            <div class="pt-2 text-xs text-gray-400 flex items-center justify-between border-t border-gray-100">
                <span>Account Created: October 28, 2026</span>
                <span>Last Active: Today at 18:42</span>
            </div>
        </div>
    </div>
</x-layouts.admin>
