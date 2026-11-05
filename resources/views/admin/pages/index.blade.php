<x-layouts.admin>
    <x-slot:title>Static Page Management - AptiMedha Admin</x-slot:title>
    <x-slot:header>Static & Policy Pages (CMS)</x-slot:header>

    <div class="space-y-6">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div data-auto-dismiss class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#153B70]">Static Pages ({{ $pages->count() }})</h2>
                <p class="text-xs text-gray-500">Edit public institutional descriptions, educational policies, and legal disclaimers.</p>
            </div>
        </div>

        {{-- Pages Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full text-left text-xs sm:text-sm min-w-[650px]">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5">Page Title</th>
                            <th class="px-6 py-3.5">Route URL</th>
                            <th class="px-6 py-3.5">SEO Meta Description</th>
                            <th class="px-6 py-3.5">Last Updated</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $routeMap = [
                                'about' => url('/about'),
                                'contact' => url('/contact'),
                                'disclaimer' => url('/disclaimer'),
                                'terms-and-conditions' => url('/terms-and-conditions'),
                                'privacy-policy' => url('/privacy-policy'),
                                'copyright' => url('/copyright'),
                            ];
                        @endphp
                        @forelse($pages as $page)
                            @php
                                $publicUrl = $routeMap[$page->slug] ?? url('/' . $page->slug);
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-[#153B70]">
                                    {{ $page->title }}
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-[#087F82]">
                                    /{{ $page->slug }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">
                                    {{ $page->meta_description ?: 'No meta description set' }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400">
                                    {{ $page->updated_at ? $page->updated_at->format('M d, Y') : 'Original' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($page->is_active)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Active</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.pages.edit', $page) }}" class="px-3 py-1 bg-gray-100 hover:bg-[#087F82] hover:text-white rounded-lg text-xs font-bold text-[#153B70] transition-colors">
                                            Edit
                                        </a>
                                        <a href="{{ $publicUrl }}" target="_blank" class="p-1 text-gray-400 hover:text-[#153B70] rounded-lg transition-colors" title="View Public Page">
                                            <i data-lucide="external-link" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400 text-xs">
                                    No static pages found in database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
