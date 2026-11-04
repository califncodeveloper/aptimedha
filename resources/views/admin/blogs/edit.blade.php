<x-layouts.admin>
    <x-slot:title>Edit Article - AptiMedha Admin</x-slot:title>
    <x-slot:header>Edit Article: {{ Str::limit($blog->title, 40) }}</x-slot:header>

    {{-- Quill Rich Text Editor Styles --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet" />

    <div class="space-y-6 max-w-5xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <a href="{{ route('admin.blogs.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to All Articles
            </a>
            
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.blogs.preview', $blog) }}" target="_blank" class="inline-flex items-center px-3.5 py-1.5 rounded-xl border border-gray-300 text-xs font-bold text-[#153B70] hover:bg-gray-50 transition-colors">
                    <i data-lucide="eye" class="w-3.5 h-3.5 mr-1.5"></i> Preview Live
                </a>
                @if($blog->status === 'published')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Published</span>
                @elseif($blog->status === 'draft')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Draft</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">Archived</span>
                @endif
            </div>
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

        <form id="blog-form" action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Main Content Card --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs p-6 sm:p-8 space-y-6">
                <div>
                    <label for="title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Article Title <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $blog->title) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 text-base font-bold text-[#153B70] focus:border-[#087F82] focus:ring-1 focus:ring-[#087F82] focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="slug" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            URL Slug
                        </label>
                        <div class="flex items-center">
                            <span class="px-3 py-2.5 bg-gray-50 border border-r-0 border-gray-300 text-xs text-gray-500 rounded-l-xl font-mono">/blog/</span>
                            <input type="text" id="slug" name="slug" value="{{ old('slug', $blog->slug) }}"
                                   class="w-full px-3.5 py-2.5 rounded-r-xl border border-gray-300 text-xs font-mono text-[#243247] focus:border-[#087F82] focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="blog_category_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Category
                        </label>
                        <select id="blog_category_id" name="blog_category_id" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('blog_category_id', $blog->blog_category_id) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Author, Status, Date --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="author_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Author Display Name
                        </label>
                        <input type="text" id="author_name" name="author_name" value="{{ old('author_name', $blog->author_name ?? $blog->author?->name) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <select id="status" name="status" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none font-semibold">
                            <option value="draft" @selected(old('status', $blog->status) === 'draft')>Save as Draft</option>
                            <option value="published" @selected(old('status', $blog->status) === 'published')>Published</option>
                            <option value="archived" @selected(old('status', $blog->status) === 'archived')>Archived</option>
                        </select>
                    </div>

                    <div>
                        <label for="published_at" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Publication Date
                        </label>
                        <input type="datetime-local" id="published_at" name="published_at"
                               value="{{ old('published_at', $blog->published_at ? $blog->published_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">
                    </div>
                </div>

                {{-- Excerpt --}}
                <div>
                    <label for="excerpt" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Excerpt (Short Summary)
                    </label>
                    <textarea id="excerpt" name="excerpt" rows="2"
                              class="w-full p-3 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">{{ old('excerpt', $blog->excerpt) }}</textarea>
                </div>

                {{-- Featured Image Upload & Preview --}}
                <div class="p-4 rounded-xl border border-dashed border-gray-300 bg-gray-50/60 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <label class="block text-xs font-bold text-[#153B70] uppercase tracking-wider">Featured Image</label>
                            <p class="text-[11px] text-gray-500">Upload replacement image. Formats: JPEG, PNG, JPG, WebP. Max 2MB.</p>
                        </div>
                    </div>

                    @if($blog->featured_image_url)
                        <div class="flex items-center space-x-4 p-2 bg-white rounded-lg border border-gray-200">
                            <img src="{{ $blog->featured_image_url }}" alt="{{ $blog->featured_image_alt ?? $blog->title }}" class="w-20 h-14 object-cover rounded-md border border-gray-200">
                            <div class="text-xs">
                                <span class="font-bold text-gray-700">Current Featured Image</span>
                                <p class="text-gray-400 font-mono text-[10px] break-all">{{ $blog->featured_image }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                        <div>
                            <input type="file" id="featured_image" name="featured_image" accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#153B70] file:text-white hover:file:bg-[#0e2a52]">
                        </div>
                        <div>
                            <input type="text" name="featured_image_alt" value="{{ old('featured_image_alt', $blog->featured_image_alt) }}" placeholder="Image alt text (accessibility & SEO)"
                                   class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">
                        </div>
                    </div>
                </div>

                {{-- Rich Text Editor for Body --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Article Body Content <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-gray-400">Word count: ~{{ str_word_count(strip_tags($blog->body)) }} words</span>
                    </div>

                    {{-- Hidden input synced before form submit --}}
                    <input type="hidden" name="body" id="body-input" value="{{ old('body', $blog->body) }}">

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
                                <button class="ql-clean"></button>
                            </span>
                        </div>
                        <div id="quill-editor" style="min-height: 340px;" class="p-4 text-sm text-[#243247]">{!! old('body', $blog->body) !!}</div>
                    </div>
                </div>
            </div>

            {{-- SEO Metadata Card --}}
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs p-6 sm:p-8 space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-[#153B70] flex items-center">
                        <i data-lucide="globe" class="w-4 h-4 mr-2 text-[#087F82]"></i> SEO & Search Meta Tags
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">Control how this post displays in search snippet previews.</p>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="meta_title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            SEO Title
                        </label>
                        <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $blog->meta_title) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">
                    </div>

                    <div>
                        <label for="meta_description" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            SEO Meta Description
                        </label>
                        <textarea id="meta_description" name="meta_description" rows="2"
                                  class="w-full p-3 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">{{ old('meta_description', $blog->meta_description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Form Submit Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.blogs.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#153B70] hover:bg-[#0e2a52] text-white text-xs font-bold shadow-xs transition-colors">
                    Update Article
                </button>
            </div>
        </form>
    </div>

    {{-- Quill Script & Editor Initialization --}}
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const quill = new Quill('#quill-editor', {
                modules: {
                    toolbar: '#quill-toolbar'
                },
                theme: 'snow'
            });

            // Sync editor content to hidden input on submit
            const form = document.getElementById('blog-form');
            form.addEventListener('submit', function () {
                const bodyInput = document.getElementById('body-input');
                bodyInput.value = quill.root.innerHTML;
            });
        });
    </script>
</x-layouts.admin>
