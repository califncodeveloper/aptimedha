<x-layouts.admin>
    <x-slot:title>Create Subject - AptiMedha Admin</x-slot:title>
    <x-slot:header>Add New Subject / Module</x-slot:header>

    <div class="space-y-6 max-w-2xl">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.subjects.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Subjects
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8">
            <form action="{{ route('admin.subjects.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Parent Category <span class="text-rose-500">*</span>
                    </label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border @error('category_id') border-rose-500 @else border-gray-300 @enderror text-sm focus:border-[#087F82] focus:outline-none">
                        <option value="">-- Select Parent Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $selectedCategoryId) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Subject Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           placeholder="e.g. Commercial Arithmetic"
                           class="w-full px-3.5 py-2.5 rounded-xl border @error('name') border-rose-500 @else border-gray-300 @enderror text-sm focus:border-[#087F82] focus:outline-none">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        URL Slug <span class="text-gray-400 font-normal lowercase">(leave blank to auto-generate)</span>
                    </label>
                    <input type="text"
                           name="slug"
                           value="{{ old('slug') }}"
                           placeholder="e.g. commercial-arithmetic"
                           class="w-full px-3.5 py-2.5 rounded-xl border @error('slug') border-rose-500 @else border-gray-300 @enderror text-sm focus:border-[#087F82] focus:outline-none font-mono text-xs">
                    @error('slug')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Display Order
                        </label>
                        <input type="number"
                               name="display_order"
                               value="{{ old('display_order', $nextOrder ?? 1) }}"
                               min="0"
                               class="w-full px-3.5 py-2.5 rounded-xl border @error('display_order') border-rose-500 @else border-gray-300 @enderror text-sm focus:border-[#087F82] focus:outline-none">
                        @error('display_order')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center pt-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', '1') ? 'checked' : '' }}
                                   class="w-4 h-4 rounded text-[#087F82] focus:ring-[#087F82]">
                            <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Active</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Short Description
                    </label>
                    <textarea name="description"
                              rows="3"
                              placeholder="Brief description of this subject area..."
                              class="w-full p-3 rounded-xl border @error('description') border-rose-500 @else border-gray-300 @enderror text-sm focus:border-[#087F82] focus:outline-none">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.subjects.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold shadow-xs transition-colors">
                        Save Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
