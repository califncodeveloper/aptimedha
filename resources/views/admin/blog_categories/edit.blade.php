<x-layouts.admin>
    <x-slot:title>Edit Category - AptiMedha Admin</x-slot:title>
    <x-slot:header>Edit Category: {{ $category->name }}</x-slot:header>

    <div class="space-y-6 max-w-xl">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.blog-categories.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Categories
            </a>
            <span class="text-xs text-gray-500 font-medium">Articles: {{ $category->blogs()->count() }}</span>
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
            <form action="{{ route('admin.blog-categories.update', $category) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Category Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        URL Slug
                    </label>
                    <input type="text" name="slug" value="{{ old('slug', $category->slug) }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs font-mono focus:border-[#087F82] focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Description
                    </label>
                    <textarea name="description" rows="3"
                              class="w-full p-3 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.blog-categories.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#153B70] hover:bg-[#0e2a52] text-white text-xs font-bold shadow-xs transition-colors">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>

