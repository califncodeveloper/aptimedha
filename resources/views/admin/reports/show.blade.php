<x-layouts.admin>
    <x-slot:title>Review Report #1 - AptiMedha Admin</x-slot:title>
    <x-slot:header>Report Moderation: REP-1</x-slot:header>

    <div class="space-y-6 max-w-3xl">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Reports Queue
            </a>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Under Review</span>
        </div>

        {{-- Question Under Review Card --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-7 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <span class="text-xs font-bold text-[#153B70]">Target Question (Q#4 Percentages)</span>
                <a href="{{ route('admin.mcqs.edit', 4) }}" class="text-xs font-bold text-[#087F82] hover:underline">
                    Edit Question &rarr;
                </a>
            </div>

            <h3 class="text-base font-bold text-[#243247]">
                "The price of sugar increases by 20%. By what percentage must a household reduce its consumption so as not to increase the expenditure on sugar?"
            </h3>

            <div class="p-3 bg-gray-50 rounded-xl text-xs space-y-1">
                <p><strong>Option A:</strong> 16.67% (Marked Correct)</p>
                <p><strong>Option B:</strong> 20%</p>
                <p><strong>Option C:</strong> 25%</p>
                <p><strong>Option D:</strong> 15%</p>
            </div>
        </div>

        {{-- User Feedback & Resolution Card --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8">
            <h4 class="text-base font-bold text-[#153B70] mb-4">User Feedback & Reporter Information</h4>

            <div class="grid grid-cols-2 gap-4 pb-4 border-b border-gray-100 text-xs text-gray-600 mb-5">
                <div>
                    <span class="font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Reported By</span>
                    <span class="font-bold text-[#243247]">@rahul_s (Rahul Sharma)</span>
                </div>
                <div>
                    <span class="font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Issue Category</span>
                    <span class="font-bold text-rose-600">Formula Clarification</span>
                </div>
                <div class="col-span-2">
                    <span class="font-bold text-gray-400 uppercase tracking-wider block mb-0.5">User Description</span>
                    <p class="text-sm text-gray-800 bg-gray-50 p-3 rounded-xl border border-gray-200">
                        "In step 2 of the explanation, please explicitly state why (100 + R) is used in the denominator rather than (100 - R)."
                    </p>
                </div>
            </div>

            <form action="{{ route('admin.reports.update', 1) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Update Report Status</label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                        <option value="New">New</option>
                        <option value="Under Review" selected>Under Review</option>
                        <option value="Resolved">Resolved (Notify User)</option>
                        <option value="Closed">Closed / Invalid</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Editorial Resolution Notes</label>
                    <textarea name="resolution_notes" rows="3" placeholder="Provide notes on what changes were made..." class="w-full p-3.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">Added clarifying note on price increase formula [R/(100+R)] vs price decrease formula.</textarea>
                </div>

                <div class="pt-3 flex justify-end gap-3">
                    <a href="{{ route('admin.reports.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold shadow-xs transition-colors">
                        Save Resolution
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
