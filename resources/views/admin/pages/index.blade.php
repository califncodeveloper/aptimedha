<x-layouts.admin>
    <x-slot:title>Static Page Management - AptiMedha Admin</x-slot:title>
    <x-slot:header>Static & Legal Pages</x-slot:header>

    <div class="space-y-6" x-data="{
        pages: [
            { id: 'about', title: 'About Us', url: '/about', status: 'Published', updated: 'Today', description: 'Mission, vision, learning philosophy, and origin of AptiMedha.' },
            { id: 'contact', title: 'Contact Us', url: '/contact', status: 'Published', updated: 'Today', description: 'Contact form, error reporting, collaboration, and email desk.' },
            { id: 'disclaimer', title: 'Disclaimer', url: '/disclaimer', status: 'Published', updated: 'Oct 26, 2026', description: 'Educational purpose, exam accuracy, and non-affiliation notice.' },
            { id: 'terms', title: 'Terms and Conditions', url: '/terms-and-conditions', status: 'Published', updated: 'Oct 26, 2026', description: 'Permitted use, bot restrictions, and account regulations.' },
            { id: 'privacy', title: 'Privacy Policy', url: '/privacy-policy', status: 'Published', updated: 'Oct 26, 2026', description: 'Data handling, session cookies, and student privacy rights.' },
            { id: 'copyright', title: 'Copyright Policy', url: '/copyright', status: 'Published', updated: 'Oct 26, 2026', description: 'Proprietary rights in explanations and copyright infringement process.' }
        ]
    }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#153B70]">Static Pages (<span x-text="pages.length"></span>)</h2>
                <p class="text-xs text-gray-500">Edit and update public educational and legal policy texts.</p>
            </div>
        </div>

        {{-- Pages Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5">Page Title</th>
                            <th class="px-6 py-3.5">Route URL</th>
                            <th class="px-6 py-3.5">Content Summary</th>
                            <th class="px-6 py-3.5">Last Updated</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="page in pages" :key="page.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-[#153B70]" x-text="page.title"></td>
                                <td class="px-6 py-4 font-mono text-xs text-[#087F82]" x-text="page.url"></td>
                                <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate" x-text="page.description"></td>
                                <td class="px-6 py-4 text-xs text-gray-400" x-text="page.updated"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800" x-text="page.status"></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a :href="'{{ url('admin/pages') }}/' + page.id + '/edit'" class="px-3 py-1 bg-gray-100 hover:bg-[#087F82] hover:text-white rounded-lg text-xs font-bold text-[#153B70] transition-colors">
                                            Edit
                                        </a>
                                        <a :href="page.url" target="_blank" class="p-1 text-gray-400 hover:text-[#153B70] rounded-lg transition-colors" title="View Public Page">
                                            <i data-lucide="external-link" class="w-4 h-4"></i>
                                        </a>
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
