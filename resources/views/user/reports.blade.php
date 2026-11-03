<x-layouts.user>
    <x-slot:title>My Question Reports - AptiMedha</x-slot:title>

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-[#243247]">Question Reports & Corrections</h1>
            <p class="text-sm text-gray-500 mt-1">
                Track status and editorial responses for errors, typos, or formula clarifications you reported.
            </p>
        </div>

        {{-- Reports Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-[#153B70]">Submitted Feedback (2)</span>
                <span class="text-xs text-gray-400">Statuses: New, Under Review, Resolved, Closed</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3">Question</th>
                            <th class="px-6 py-3">Issue Category</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Date Submitted</th>
                            <th class="px-6 py-3">Editorial Resolution</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="px-6 py-4">
                                <a href="{{ route('mcq.show', 1) }}" class="font-bold text-[#153B70] hover:text-[#087F82]">
                                    Q#1 (Arithmetic Aptitude)
                                </a>
                                <p class="text-xs text-gray-500 truncate max-w-xs mt-0.5">"What is 25% of 240?"</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-[#243247]">
                                    Typo / Formatting
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1 w-fit">
                                    <i data-lucide="check" class="w-3 h-3"></i> Resolved
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-400">Oct 29, 2026</td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                <em>"Thank you! Fixed subscript rendering in explanation."</em>
                            </td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4">
                                <a href="{{ route('mcq.show', 4) }}" class="font-bold text-[#153B70] hover:text-[#087F82]">
                                    Q#4 (Arithmetic Aptitude)
                                </a>
                                <p class="text-xs text-gray-500 truncate max-w-xs mt-0.5">"The price of sugar increases by 20%..."</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-[#243247]">
                                    Formula Clarification
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 flex items-center gap-1 w-fit">
                                    <i data-lucide="clock" class="w-3 h-3"></i> Under Review
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-400">Nov 01, 2026</td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                Assigned to senior math reviewer.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.user>
