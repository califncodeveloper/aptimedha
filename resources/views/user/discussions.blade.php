<x-layouts.user>
    <x-slot:title>My Discussions - AptiMedha</x-slot:title>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#243247]">Discussion Threads & Queries</h1>
                <p class="text-sm text-gray-500 mt-1">Review your comments, solution shortcuts, and replies from mentors.</p>
            </div>
            <a href="{{ route('mcq.index') }}" class="px-4 py-2 bg-[#087F82] text-white text-xs font-bold rounded-xl hover:bg-[#066668] transition-colors self-start sm:self-auto shadow-xs">
                Join Community Discussions
            </a>
        </div>

        <div class="space-y-4">
            {{-- Thread 1 --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 space-y-3">
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md font-bold bg-[#153B70]/10 text-[#153B70]">Arithmetic Aptitude</span>
                        <span class="text-gray-400">&bull;</span>
                        <span class="font-semibold text-gray-600">Question #1 (Percentages)</span>
                    </div>
                    <span class="text-gray-400">2 days ago</span>
                </div>

                <h3 class="text-base font-bold text-[#243247]">
                    <a href="{{ route('mcq.show', 1) }}" class="hover:text-[#087F82] transition-colors">
                        "What is 25% of 240?"
                    </a>
                </h3>

                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 text-xs sm:text-sm text-gray-700">
                    <p class="font-bold text-[#153B70] text-xs mb-1">Your Comment:</p>
                    <p>"Very straightforward! Remember 25% is just dividing by 4."</p>
                </div>

                <div class="flex items-center justify-between pt-1 text-xs">
                    <span class="text-emerald-600 font-semibold flex items-center gap-1">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> 2 replies from learners
                    </span>
                    <a href="{{ route('mcq.show', 1) }}" class="font-bold text-[#087F82] hover:underline">
                        View Thread &rarr;
                    </a>
                </div>
            </div>

            {{-- Thread 2 --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 space-y-3">
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md font-bold bg-[#153B70]/10 text-[#153B70]">Arithmetic Aptitude</span>
                        <span class="text-gray-400">&bull;</span>
                        <span class="font-semibold text-gray-600">Question #2 (Percentages)</span>
                    </div>
                    <span class="text-gray-400">3 days ago</span>
                </div>

                <h3 class="text-base font-bold text-[#243247]">
                    <a href="{{ route('mcq.show', 2) }}" class="hover:text-[#087F82] transition-colors">
                        "A value increases from 80 to 100. What is the percentage increase?"
                    </a>
                </h3>

                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 text-xs sm:text-sm text-gray-700">
                    <p class="font-bold text-[#153B70] text-xs mb-1">Your Comment:</p>
                    <p>"Always remember to divide by the INITIAL value (80), not the final value (100)."</p>
                </div>

                <div class="flex items-center justify-between pt-1 text-xs">
                    <span class="text-gray-400 font-medium">1 reply</span>
                    <a href="{{ route('mcq.show', 2) }}" class="font-bold text-[#087F82] hover:underline">
                        View Thread &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.user>
