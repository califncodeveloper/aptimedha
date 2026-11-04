<x-layouts.admin>
    <x-slot:title>Edit Page: {{ $page->title }} - AptiMedha Admin</x-slot:title>
    <x-slot:header>Edit CMS Page: {{ $page->title }}</x-slot:header>

    {{-- Quill Rich Text Editor Styles --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet" />

    <div class="space-y-6 max-w-4xl">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.pages.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Static Pages
            </a>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $page->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                {{ $page->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs p-6 sm:p-8">
            <form id="page-form" action="{{ route('admin.pages.update', $page) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Page Title <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title', $page->title) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Route Slug (Permanent)
                        </label>
                        <input type="text" name="slug" value="{{ $page->slug }}" readonly
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-xs font-mono text-gray-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        SEO Meta Title
                    </label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}"
                           placeholder="Browser tab title and search engine heading..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        SEO Meta Description
                    </label>
                    <textarea name="meta_description" rows="2"
                              placeholder="Brief summary for search engine snippet..."
                              class="w-full p-3 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">{{ old('meta_description', $page->meta_description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Page Content Body <span class="text-rose-500">*</span>
                    </label>

                    {{-- Hidden input synced before submit --}}
                    <input type="hidden" name="content" id="content-input" value="{{ old('content', $page->content) }}">

                    <div class="bg-white rounded-xl border border-gray-300 overflow-hidden">
                        <div id="quill-toolbar" class="border-b border-gray-200 bg-gray-50/70">
                            <span class="ql-formats">
                                <select class="ql-header">
                                    <option value="2">Heading 2</option>
                                    <option value="3">Heading 3</option>
                                    <option value="4">Heading 4</option>
                                    <option selected>Normal Text</option>
                                </select>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-bold"></button>
                                <button class="ql-italic"></button>
                                <button class="ql-underline"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-list" value="ordered"></button>
                                <button class="ql-list" value="bullet"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-link"></button>
                                <button class="ql-clean"></button>
                            </span>
                        </div>
                        <div id="quill-editor" style="min-height: 280px;" class="p-4 text-sm text-[#243247]">{!! old('content', $page->content) !!}</div>
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $page->is_active))
                           class="w-4 h-4 rounded text-[#087F82] focus:ring-[#087F82] border-gray-300">
                    <label for="is_active" class="text-xs font-bold text-gray-700">
                        Page is Active and Visible to Public Visitors
                    </label>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    @php
                        $routeMap = [
                            'about' => url('/about'),
                            'contact' => url('/contact'),
                            'disclaimer' => url('/disclaimer'),
                            'terms-and-conditions' => url('/terms-and-conditions'),
                            'privacy-policy' => url('/privacy-policy'),
                            'copyright' => url('/copyright'),
                        ];
                        $previewUrl = $routeMap[$page->slug] ?? url('/' . $page->slug);
                    @endphp
                    <a href="{{ $previewUrl }}" target="_blank" class="inline-flex items-center text-xs font-bold text-[#087F82] hover:underline">
                        <i data-lucide="external-link" class="w-3.5 h-3.5 mr-1"></i> Preview Live Page
                    </a>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.pages.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#153B70] hover:bg-[#0e2a52] text-white text-xs font-bold shadow-xs transition-colors">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Quill Script --}}
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const quill = new Quill('#quill-editor', {
                modules: { toolbar: '#quill-toolbar' },
                theme: 'snow'
            });

            const form = document.getElementById('page-form');
            form.addEventListener('submit', function () {
                document.getElementById('content-input').value = quill.root.innerHTML;
            });
        });
    </script>
</x-layouts.admin>
