<x-layouts.admin>
    <x-slot:title>Create Placement Guide - AptiMedha Admin</x-slot:title>
    <x-slot:header>Create Placement Guide</x-slot:header>

    {{-- Quill Rich Text Editor Styles --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet" />

    <div class="space-y-6 max-w-5xl">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.placement.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to All Guides
            </a>
            <span class="text-xs text-gray-500 font-medium">New Placement Guide</span>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <div class="flex items-center space-x-2 font-bold mb-1">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                    <span>Please correct the errors below:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="guide-form" action="{{ route('admin.placement.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Main Content Card --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs p-6 sm:p-8 space-y-6">
                <div>
                    <label for="title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Guide Title <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="e.g. 4-Stage Placement Preparation Roadmap"
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 text-base font-bold text-[#153B70] focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="slug" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            URL Slug (Auto-generated if left empty)
                        </label>
                        <div class="flex items-center">
                            <span class="px-3 py-2.5 bg-gray-50 border border-r-0 border-gray-300 text-xs text-gray-500 rounded-l-xl font-mono">/placement-preparation/</span>
                            <input type="text" id="slug" name="slug" value="{{ old('slug') }}" placeholder="placement-preparation-roadmap"
                                   class="w-full px-3.5 py-2.5 rounded-r-xl border border-gray-300 text-xs font-mono text-[#243247] focus:border-[#087F82] focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="category" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Curriculum Category <span class="text-rose-500">*</span>
                        </label>
                        <select id="category" name="category" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none font-semibold">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" @selected(old('category') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Display Order, Status, Date --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="display_order" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Display Order
                        </label>
                        <input type="number" id="display_order" name="display_order" value="{{ old('display_order', 0) }}" min="0"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">
                    </div>

                    <div>
                        <label for="is_published" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <select id="is_published" name="is_published" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none font-semibold">
                            <option value="1" @selected(old('is_published', '1') == '1')>Published</option>
                            <option value="0" @selected(old('is_published') === '0')>Save as Draft</option>
                        </select>
                    </div>

                    <div>
                        <label for="published_at" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Publication Timestamp
                        </label>
                        <input type="datetime-local" id="published_at" name="published_at" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">
                    </div>
                </div>

                {{-- Excerpt --}}
                <div>
                    <label for="excerpt" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Short Summary / Excerpt
                    </label>
                    <textarea id="excerpt" name="excerpt" rows="2" placeholder="Brief 1-2 sentence preview to engage students on the placement hub..."
                              class="w-full p-3 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">{{ old('excerpt') }}</textarea>
                </div>

                {{-- Featured Image Upload --}}
                <div class="p-4 rounded-xl border border-dashed border-gray-300 bg-gray-50/60 space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-[#153B70] uppercase tracking-wider">Featured Image / Banner</label>
                        <p class="text-[11px] text-gray-500">Formats: JPEG, PNG, JPG, WebP. Maximum file size: 5MB.</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                        <div>
                            <input type="file" id="featured_image" name="featured_image" accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#153B70] file:text-white hover:file:bg-[#0e2a52]">
                        </div>
                        <div>
                            <input type="text" name="featured_image_alt" value="{{ old('featured_image_alt') }}" placeholder="Image ALT text for accessibility"
                                   class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">
                        </div>
                    </div>
                </div>

                {{-- Downloadable Resource Upload --}}
                <div class="p-4 rounded-xl border border-dashed border-teal-200 bg-teal-50/30 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-xs font-bold text-[#087F82] uppercase tracking-wider flex items-center">
                                <i data-lucide="file-down" class="w-4 h-4 mr-1.5"></i> Optional Downloadable Resource (PDF / DOCX)
                            </label>
                            <p class="text-[11px] text-gray-500">Attach a genuine resume template, checklist, or roadmap document. Maximum file size: 20MB.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                        <div>
                            <input type="file" id="downloadable_asset" name="downloadable_asset" accept=".pdf,.docx,.doc,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword"
                                   class="text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#087F82] file:text-white hover:file:bg-[#066668]">
                        </div>
                        <div>
                            <input type="text" name="downloadable_asset_name" value="{{ old('downloadable_asset_name') }}" placeholder="Custom download display name (e.g. AptiMedha_Resume_Template.docx)"
                                   class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">
                        </div>
                    </div>
                </div>

                {{-- Rich Text Editor for Content --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Guide Body Content <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-gray-400">Headings, formatting, lists, code, tables, images</span>
                    </div>

                    {{-- Hidden input synced before form submit --}}
                    <input type="hidden" name="content" id="content-input" value="{{ old('content') }}">

                    {{-- Quill Editor Container --}}
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
                                <button class="ql-strike"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-list" value="ordered"></button>
                                <button class="ql-list" value="bullet"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-blockquote"></button>
                                <button class="ql-code-block"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-link"></button>
                                <button class="ql-image" type="button" title="Upload & Insert Image"></button>
                                <button class="ql-clean"></button>
                            </span>
                        </div>
                        <div id="quill-editor" style="min-height: 380px;" class="p-4 text-sm text-[#243247]">{!! old('content') !!}</div>
                    </div>
                </div>
            </div>

            {{-- SEO Metadata Card --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs p-6 sm:p-8 space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-[#153B70] flex items-center">
                        <i data-lucide="globe" class="w-4 h-4 mr-2 text-[#087F82]"></i> SEO & Search Meta Tags
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">Optimize how this placement guide appears in search engines and social shares.</p>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="meta_title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            SEO Title (Optional - defaults to guide title)
                        </label>
                        <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}" placeholder="Optimal length: 50–60 characters"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">
                    </div>

                    <div>
                        <label for="meta_description" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            SEO Meta Description (Optional - defaults to excerpt)
                        </label>
                        <textarea id="meta_description" name="meta_description" rows="2" placeholder="Optimal length: 150–160 characters describing the guide..."
                                  class="w-full p-3 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">{{ old('meta_description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Form Submit Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.placement.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" onclick="document.getElementById('is_published').value='0'" class="px-5 py-2.5 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-bold transition-colors">
                    Save as Draft
                </button>
                <button type="submit" onclick="document.getElementById('is_published').value='1'" class="px-6 py-2.5 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold shadow-xs transition-colors">
                    Publish Guide
                </button>
            </div>
        </form>
    </div>

    {{-- Quill Script & Editor Initialization --}}
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const quill = new Quill('#quill-editor', {
                theme: 'snow',
                modules: {
                    toolbar: {
                        container: '#quill-toolbar',
                        handlers: {
                            image: imageHandler
                        }
                    }
                },
                placeholder: 'Write or paste comprehensive placement preparation content...'
            });

            // Custom image handler for Quill to upload to Cloudinary/server
            function imageHandler() {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/jpeg,image/png,image/jpg,image/webp,image/gif');
                input.click();

                input.onchange = async () => {
                    const file = input.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('image', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    const range = quill.getSelection(true);
                    quill.insertText(range.index, 'Uploading image...', 'italic', true);

                    try {
                        const response = await fetch('{{ route('admin.placement.upload-image') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();
                        quill.deleteText(range.index, 'Uploading image...'.length);

                        if (data.success && data.url) {
                            quill.insertEmbed(range.index, 'image', data.url);
                        } else {
                            alert(data.error || 'Failed to upload image. Please try again.');
                        }
                    } catch (error) {
                        quill.deleteText(range.index, 'Uploading image...'.length);
                        alert('Network or server error while uploading image.');
                    }
                };
            }

            // Sync Quill HTML to hidden content field before form submission
            const form = document.getElementById('guide-form');
            form.addEventListener('submit', function () {
                const contentInput = document.getElementById('content-input');
                contentInput.value = quill.getSemanticHTML();
            });
        });
    </script>
</x-layouts.admin>

