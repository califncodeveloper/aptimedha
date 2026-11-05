<x-layouts.admin>
    <x-slot:title>Edit Current Affairs Issue - AptiMedha Admin</x-slot:title>
    <x-slot:header>Edit Issue: {{ $currentAffair->period_label }}</x-slot:header>

    <div class="max-w-4xl space-y-6">
        {{-- Flash Messages --}}
        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <div class="flex items-center space-x-2 mb-2 font-bold">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                    <span>Please fix the following errors:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.current-affairs.update', $currentAffair) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Period Card --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-200/90 shadow-2xs space-y-4">
                <h3 class="text-sm font-bold text-[#153B70] uppercase tracking-wider flex items-center">
                    <i data-lucide="calendar" class="w-4 h-4 mr-2 text-[#087F82]"></i> Issue Period & Title
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Year --}}
                    <div>
                        <label for="year" class="block text-xs font-bold text-[#243247] uppercase tracking-wider mb-1.5">
                            Year <span class="text-rose-500">*</span>
                        </label>
                        <select name="year" id="year" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">
                            @for($y = (int) date('Y') + 1; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ old('year', $currentAffair->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Month --}}
                    <div>
                        <label for="month" class="block text-xs font-bold text-[#243247] uppercase tracking-wider mb-1.5">
                            Month <span class="text-rose-500">*</span>
                        </label>
                        <select name="month" id="month" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" {{ old('month', $currentAffair->month) == $num ? 'selected' : '' }}>
                                    {{ $num }} – {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-xs font-bold text-[#243247] uppercase tracking-wider mb-1.5">
                        Issue Title <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="title"
                           id="title"
                           value="{{ old('title', $currentAffair->title) }}"
                           required
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-xs font-bold text-[#243247] uppercase tracking-wider mb-1.5">
                        Overview & Highlights
                    </label>
                    <textarea name="description"
                              id="description"
                              rows="5"
                              class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">{{ old('description', $currentAffair->description) }}</textarea>
                </div>
            </div>

            {{-- PDF Asset Upload Card --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-200/90 shadow-2xs space-y-4">
                <h3 class="text-sm font-bold text-[#153B70] uppercase tracking-wider flex items-center justify-between">
                    <span class="flex items-center">
                        <i data-lucide="file-up" class="w-4 h-4 mr-2 text-[#087F82]"></i> PDF Document
                    </span>
                    <a href="{{ route('admin.current-affairs.preview', $currentAffair) }}" target="_blank" class="text-xs font-bold text-[#087F82] hover:underline flex items-center">
                        <i data-lucide="external-link" class="w-3.5 h-3.5 mr-1"></i> Preview Current File
                    </a>
                </h3>

                {{-- Existing PDF info --}}
                <div class="p-3.5 rounded-xl bg-teal-50/50 border border-teal-100 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="file-text" class="w-6 h-6 text-rose-500"></i>
                        <div>
                            <span class="text-xs font-bold text-[#243247] block">{{ $currentAffair->pdf_name ?: basename($currentAffair->pdf_path) }}</span>
                            <span class="text-[11px] text-gray-500">{{ $currentAffair->formatted_file_size }} &bull; {{ number_format($currentAffair->downloads_count) }} total downloads</span>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-[#087F82] bg-white px-2.5 py-1 rounded-lg border border-teal-200">Active Document</span>
                </div>

                {{-- Upload Replacement --}}
                <div class="pt-2">
                    <label for="pdf" class="block text-xs font-bold text-[#243247] uppercase tracking-wider mb-1.5">
                        Upload Replacement PDF (Optional)
                    </label>
                    <input type="file"
                           name="pdf"
                           id="pdf"
                           accept="application/pdf,.pdf"
                           class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#153B70] file:text-white hover:file:bg-[#0f2a50] file:cursor-pointer">
                    <p class="text-[11px] text-gray-400 mt-1">Leave blank to retain current PDF. If a new file is uploaded, the existing file is replaced safely.</p>
                </div>
            </div>

            {{-- Preview Image Card (Optional) --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-200/90 shadow-2xs space-y-4">
                <h3 class="text-sm font-bold text-[#153B70] uppercase tracking-wider flex items-center">
                    <i data-lucide="image" class="w-4 h-4 mr-2 text-[#087F82]"></i> Cover / Preview Image
                </h3>

                @if($currentAffair->preview_image)
                    <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <img src="{{ $currentAffair->preview_image_url }}" alt="{{ $currentAffair->preview_image_alt ?? 'Preview' }}" class="w-16 h-20 object-cover rounded-lg border border-gray-200">
                        <div>
                            <span class="text-xs font-bold text-[#243247] block">Current Cover Image</span>
                            <span class="text-[11px] text-gray-400">{{ basename($currentAffair->preview_image) }}</span>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="preview_image" class="block text-xs font-bold text-[#243247] uppercase tracking-wider mb-1.5">
                            {{ $currentAffair->preview_image ? 'Replace Image (Optional)' : 'Upload Image (Optional)' }}
                        </label>
                        <input type="file"
                               name="preview_image"
                               id="preview_image"
                               accept="image/jpeg,image/png,image/webp"
                               class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#087F82] file:text-white hover:file:bg-[#066668] file:cursor-pointer">
                    </div>

                    <div>
                        <label for="preview_image_alt" class="block text-xs font-bold text-[#243247] uppercase tracking-wider mb-1.5">
                            Image Alt Description
                        </label>
                        <input type="text"
                               name="preview_image_alt"
                               id="preview_image_alt"
                               value="{{ old('preview_image_alt', $currentAffair->preview_image_alt) }}"
                               class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">
                    </div>
                </div>
            </div>

            {{-- Publication Status Card --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-200/90 shadow-2xs space-y-4">
                <h3 class="text-sm font-bold text-[#153B70] uppercase tracking-wider flex items-center">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-2 text-[#087F82]"></i> Publishing Options
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center space-x-3 pt-2">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox"
                               name="is_published"
                               id="is_published"
                               value="1"
                               {{ old('is_published', $currentAffair->is_published) ? 'checked' : '' }}
                               class="w-4 h-4 rounded-md border-gray-300 text-[#087F82] focus:ring-[#087F82]">
                        <label for="is_published" class="text-sm font-semibold text-[#243247] cursor-pointer">
                            Published to public website
                        </label>
                    </div>

                    <div>
                        <label for="published_at" class="block text-xs font-bold text-[#243247] uppercase tracking-wider mb-1.5">
                            Publication Date
                        </label>
                        <input type="datetime-local"
                               name="published_at"
                               id="published_at"
                               value="{{ old('published_at', $currentAffair->published_at ? $currentAffair->published_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-3.5 py-2 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">
                    </div>
                </div>
            </div>

            {{-- Submit Row --}}
            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="{{ route('admin.current-affairs.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#087F82] text-white text-sm font-bold hover:bg-[#066668] transition-colors shadow-2xs flex items-center">
                    <i data-lucide="check" class="w-4 h-4 mr-1.5"></i> Update Issue
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
