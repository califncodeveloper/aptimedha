<x-layouts.admin>
    <x-slot:title>Discussion Moderation - AptiMedha Admin</x-slot:title>
    <x-slot:header>Discussion & Doubt Moderation</x-slot:header>

    <div class="space-y-6" x-data="{
        comments: [
            { id: 1, user: 'Rohit Sharma', question: 'Q#1 (Percentages)', comment: 'Very straightforward! Remember 25% is just dividing by 4.', status: 'Approved', date: '2 days ago' },
            { id: 2, user: 'Sneha Patel', question: 'Q#1 (Percentages)', comment: 'Helped me in TCS NQT quantitative section.', status: 'Approved', date: 'Yesterday' },
            { id: 3, user: 'Amit Verma', question: 'Q#2 (Percentages)', comment: 'Always remember to divide by the INITIAL value (80), not the final value (100).', status: 'Approved', date: '3 days ago' },
            { id: 4, user: 'Ankit Gupta', question: 'Q#5 (Percentages)', comment: 'Can someone explain why passing mark is 240 instead of 200?', status: 'Flagged for Review', date: '1 day ago' }
        ],
        deleteItem(id) {
            this.comments = this.comments.filter(c => c.id !== id);
        }
    }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#153B70]">Community Posts (<span x-text="comments.length"></span>)</h2>
                <p class="text-xs text-gray-500">Ensure respectful peer collaboration and prevent spam or inappropriate messages.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5">User</th>
                            <th class="px-6 py-3.5">Question Target</th>
                            <th class="px-6 py-3.5">Comment Text</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5">Date</th>
                            <th class="px-6 py-3.5 text-right">Moderation Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="item in comments" :key="item.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-[#153B70]" x-text="item.user"></td>
                                <td class="px-6 py-4 font-semibold text-gray-600" x-text="item.question"></td>
                                <td class="px-6 py-4 max-w-sm">
                                    <p class="text-xs text-gray-800 line-clamp-2" x-text="item.comment"></p>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="item.status === 'Approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'" class="px-2.5 py-0.5 rounded-full text-xs font-semibold" x-text="item.status"></span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400" x-text="item.date"></td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="item.status = 'Approved'" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-bold transition-colors">
                                            Approve
                                        </button>
                                        <button @click="deleteItem(item.id)" class="px-2.5 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg text-xs font-bold transition-colors">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
