<x-layouts.admin>
    <x-slot:title>Edit Article - AptiMedha Admin</x-slot:title>
    <x-slot:header>Edit Article: Aptitude Tests in Placements</x-slot:header>

    <div class="space-y-6 max-w-4xl">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.blogs.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Articles
            </a>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Published</span>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8">
            <form action="{{ route('admin.blogs.update', 1) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Article Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="How to Prepare for Aptitude Tests in Placements" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Category</label>
                        <select name="category" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="Placement Strategy" selected>Placement Strategy</option>
                            <option value="Aptitude Insights">Aptitude Insights</option>
                            <option value="Interview Prep">Interview Prep</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Author</label>
                        <input type="text" name="author" value="AptiMedha Editorial Team" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Status</label>
                        <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="published" selected>Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Excerpt</label>
                    <textarea name="excerpt" rows="2" class="w-full p-3 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">A structured guide to mastering arithmetic, logical deduction, and verbal sections within a 60-day roadmap for campus placements.</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Article Body</label>
                    <textarea name="body" rows="8" class="w-full p-3.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none font-mono text-xs"><p>Campus recruitment aptitude tests are designed to assess candidate speed, numerical accuracy, and deductive problem-solving ability under strict time limits.</p></textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.blogs.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#153B70] hover:bg-[#0e2a52] text-white text-xs font-bold shadow-xs transition-colors">
                        Update Article
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
