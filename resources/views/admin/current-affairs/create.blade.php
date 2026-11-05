<x-layouts.admin>
    <x-slot:title>Create Current Affairs Issue - AptiMedha Admin</x-slot:title>
    <x-slot:header>New Monthly Current Affairs Issue</x-slot:header>

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

        <form method="POST" action="{{ route('admin.current-affairs.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

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
                                <option value="{{ $y }}" {{ old('year', $currentYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
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
                                <option value="{{ $num }}" {{ old('month', $currentMonth) == $num ? 'selected' : '' }}>
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
                           value="{{ old('title') }}"
                           required
                           placeholder="e.g. Monthly Current Affairs – November 2026"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">
                    <p class="text-[11px] text-gray-400 mt-1">Recommended format: "Monthly Current Affairs – [Month] [Year]".</p>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-xs font-bold text-[#243247] uppercase tracking-wider mb-1.5">
                        Overview & Highlights
                    </label>
                    <textarea name="description"
                              id="description"
                              rows="5"
                              placeholder="Key national and international topics, government policies, economy notes, and GK highlights included in this monthly edition..."
                              class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">{{ old('description') }}</textarea>
                    <p class="text-[11px] text-gray-400 mt-1">HTML formatting is sanitized automatically before saving.</p>
                </div>
            </div>

            {{-- PDF Asset Upload Card --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-200/90 shadow-2xs space-y-4">
                <h3 class="text-sm font-bold text-[#153B70] uppercase tracking-wider flex items-center">
                    <i data-lucide="file-up" class="w-4 h-4 mr-2 text-[#087F82]"></i> PDF Document Upload
                </h3>

                <div class="p-4 rounded-xl border-2 border-dashed border-gray-200 bg-[#F7F9FC] text-center hover:border-[#087F82]/40 transition-colors">
                    <i data-lucide="upload-cloud" class="w-8 h-8 mx-auto text-[#087F82] mb-2"></i>
                    <label for="pdf" class="block text-xs font-bold text-[#153B70] cursor-pointer mb-1">
                        Select Monthly Current Affairs PDF Document <span class="text-rose-500">*</span>
                    </label>
                    <input type="file"
                           name="pdf"
                           id="pdf"
                           accept="application/pdf,.pdf"
                           required
                           class="block w-full max-w-sm mx-auto text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#153B70] file:text-white hover:file:bg-[#0f2a50] file:cursor-pointer">
                    <p class="text-[11px] text-gray-400 mt-2">Maximum file size: 50 MB. Stored securely on Cloudinary or local fallback storage.</p>
                </div>
            </div>

            {{-- Preview Image Card (Optional) --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-200/90 shadow-2xs space-y-4">
                <h3 class="text-sm font-bold text-[#153B70] uppercase tracking-wider flex items-center">
                    <i data-lucide="image" class="w-4 h-4 mr-2 text-[#087F82]"></i> Cover / Preview Image (Optional)
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="preview_image" class="block text-xs font-bold text-[#243247] uppercase tracking-wider mb-1.5">
                            Image File
                        </label>
                        <input type="file"
                               name="preview_image"
                               id="preview_image"
                               accept="image/jpeg,image/png,image/webp"
                               class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#087F82] file:text-white hover:file:bg-[#066668] file:cursor-pointer">
                        <p class="text-[11px] text-gray-400 mt-1">Accepts JPG, PNG, WEBP up to 5 MB.</p>
                    </div>

                    <div>
                        <label for="preview_image_alt" class="block text-xs font-bold text-[#243247] uppercase tracking-wider mb-1.5">
                            Image Alt Description
                        </label>
                        <input type="text"
                               name="preview_image_alt"
                               id="preview_image_alt"
                               value="{{ old('preview_image_alt') }}"
                               placeholder="e.g. Current Affairs November 2026 Edition Cover"
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
                               {{ old('is_published', '1') == '1' ? 'checked' : '' }}
                               class="w-4 h-4 rounded-md border-gray-300 text-[#087F82] focus:ring-[#087F82]">
                        <label for="is_published" class="text-sm font-semibold text-[#243247] cursor-pointer">
                            Publish immediately to students & public
                        </label>
                    </div>

                    <div>
                        <label for="published_at" class="block text-xs font-bold text-[#243247] uppercase tracking-wider mb-1.5">
                            Scheduled Publication Date
                        </label>
                        <input type="datetime-local"
                               name="published_at"
                               id="published_at"
                               value="{{ old('published_at') }}"
                               class="w-full px-3.5 py-2 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">
                        <p class="text-[11px] text-gray-400 mt-1">Leave blank to use current time upon publishing.</p>
                    </div>
                </div>
            </div>

            {{-- Submit Row --}}
            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="{{ route('admin.current-affairs.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#087F82] text-white text-sm font-bold hover:bg-[#066668] transition-colors shadow-2xs flex items-center">
                    <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Save & Publish Issue
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>

