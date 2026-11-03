<x-layouts.admin>
    <x-slot:title>Edit Subject - AptiMedha Admin</x-slot:title>
    <x-slot:header>Edit Subject: Commercial Arithmetic</x-slot:header>

    <div class="space-y-6 max-w-2xl">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.subjects.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Subjects
            </a>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Active</span>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8">
            <form action="{{ route('admin.subjects.update', 1) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Parent Category <span class="text-rose-500">*</span></label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                        <option value="1" selected>Arithmetic Aptitude</option>
                        <option value="2">Data Interpretation</option>
                        <option value="3">Logical Reasoning</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Subject Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="Commercial Arithmetic" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">URL Slug</label>
                    <input type="text" name="slug" value="commercial-arithmetic" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none font-mono text-xs">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Display Order</label>
                    <input type="number" name="display_order" value="1" min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.subjects.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#153B70] hover:bg-[#0e2a52] text-white text-xs font-bold shadow-xs transition-colors">
                        Update Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
