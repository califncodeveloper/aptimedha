<x-layouts.admin>
    <x-slot:title>Edit Static Page - AptiMedha Admin</x-slot:title>
    <x-slot:header>Edit Page: About Us</x-slot:header>

    <div class="space-y-6 max-w-4xl">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.pages.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Static Pages
            </a>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Published</span>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8">
            <form action="{{ route('admin.pages.update', 'about') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Page Title</label>
                        <input type="text" name="title" value="About AptiMedha" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Route Slug</label>
                        <input type="text" name="slug" value="about" readonly class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm font-mono text-xs text-gray-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">SEO Meta Description</label>
                    <input type="text" name="meta_description" value="AptiMedha brings aptitude practice, technical learning and career preparation together in one place." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Page Content Body (HTML / Markdown)</label>
                    <textarea name="content" rows="10" class="w-full p-3.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none font-mono text-xs"><h2>Practise Smart. Get Placement Ready.</h2><p>AptiMedha is an educational platform that helps students, graduates and job seekers strengthen their knowledge, develop problem-solving skills and prepare for placement assessments and interviews.</p></textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ url('about') }}" target="_blank" class="inline-flex items-center text-xs font-bold text-[#087F82] hover:underline">
                        <i data-lucide="external-link" class="w-3.5 h-3.5 mr-1"></i> Preview Live Page
                    </a>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.pages.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#153B70] hover:bg-[#0e2a52] text-white text-xs font-bold shadow-xs transition-colors">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
