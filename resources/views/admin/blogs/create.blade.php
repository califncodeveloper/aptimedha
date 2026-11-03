<x-layouts.admin>
    <x-slot:title>Write Article - AptiMedha Admin</x-slot:title>
    <x-slot:header>Write New Blog / Guide</x-slot:header>

    <div class="space-y-6 max-w-4xl">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.blogs.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Articles
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8">
            <form action="{{ route('admin.blogs.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Article Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="e.g. How to Crack Campus Aptitude Tests" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Category</label>
                        <select name="category" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="Placement Strategy">Placement Strategy</option>
                            <option value="Aptitude Insights">Aptitude Insights</option>
                            <option value="Interview Prep">Interview Prep</option>
                            <option value="Exam Patterns">Exam Patterns</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Author Display Name</label>
                        <input type="text" name="author" value="AptiMedha Editorial" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Status</label>
                        <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Excerpt (Short Summary)</label>
                    <textarea name="excerpt" rows="2" placeholder="Brief 1-2 sentence teaser for article card..." class="w-full p-3 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Full Article Content (HTML / Rich Text)</label>
                    <textarea name="body" rows="8" placeholder="Write full article body..." class="w-full p-3.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none font-mono text-xs"></textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.blogs.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold shadow-xs transition-colors">
                        Publish Article
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
