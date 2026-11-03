<x-layouts.admin>
    <x-slot:title>Bulk Import MCQs - AptiMedha Admin</x-slot:title>
    <x-slot:header>Bulk MCQ Import (.xlsx / .csv)</x-slot:header>

    <div class="space-y-8 max-w-4xl" x-data="{ 
        fileName: '', 
        isUploading: false,
        fileSelected(e) {
            if(e.target.files.length) {
                this.fileName = e.target.files[0].name;
            }
        }
    }">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.mcqs.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to MCQ Bank
            </a>
            <span class="text-xs text-gray-400">Canonical Handover Schema Compliant</span>
        </div>

        {{-- Upload Container --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8">
            <form action="{{ route('admin.mcqs.import') }}" method="POST" enctype="multipart/form-data" @submit="isUploading = true" class="space-y-6">
                @csrf

                {{-- Drag & Drop Area --}}
                <div class="border-2 border-dashed border-gray-300 rounded-2xl p-8 sm:p-10 text-center hover:border-[#087F82] bg-[#F7F9FC]/50 transition-colors relative cursor-pointer group">
                    <input type="file" 
                           name="file" 
                           accept=".xlsx,.csv" 
                           required 
                           @change="fileSelected"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    
                    <div class="w-14 h-14 rounded-2xl bg-teal-50 text-[#087F82] flex items-center justify-center mx-auto mb-3 group-hover:bg-[#087F82] group-hover:text-white transition-colors">
                        <i data-lucide="file-spreadsheet" class="w-7 h-7"></i>
                    </div>

                    <p class="text-sm font-bold text-[#153B70]" x-text="fileName ? fileName : 'Choose Excel workbook (.xlsx) or UTF-8 CSV'"></p>
                    <p class="text-xs text-gray-400 mt-1">Drag and drop spreadsheet file here, or click to browse</p>
                    <p class="text-[11px] text-[#087F82] font-semibold mt-2">Maximum batch size: 5,000 MCQs per upload</p>
                </div>

                {{-- Action Bar --}}
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                    <button type="button" class="inline-flex items-center text-xs font-bold text-[#087F82] hover:text-[#066668] transition-colors">
                        <i data-lucide="download" class="w-4 h-4 mr-1.5"></i> Download Canonical Import Template (.xlsx)
                    </button>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.mcqs.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold shadow-xs transition-colors cursor-pointer">
                            Validate and Import Batch
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- 10-Column Canonical Schema Specification Card matching SRS Section 9 --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8 space-y-4">
            <div>
                <h3 class="text-base font-bold text-[#153B70]">Required 10-Column Spreadsheet Schema</h3>
                <p class="text-xs text-gray-500 mt-0.5">Your spreadsheet must preserve the columns in this exact sequence:</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border border-gray-200 rounded-xl overflow-hidden">
                    <thead class="bg-gray-50 text-gray-700 font-bold border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2.5">Col</th>
                            <th class="px-3 py-2.5">Header Name</th>
                            <th class="px-3 py-2.5">Description</th>
                            <th class="px-3 py-2.5">Example Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-mono text-[11px]">
                        <tr>
                            <td class="px-3 py-2 font-bold text-[#087F82]">1</td>
                            <td class="px-3 py-2 font-sans font-semibold">Question Topic</td>
                            <td class="px-3 py-2 font-sans text-gray-500">Canonical Category</td>
                            <td class="px-3 py-2 text-gray-600">Arithmetic Aptitude</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-bold text-[#087F82]">2</td>
                            <td class="px-3 py-2 font-sans font-semibold">Question Subtopic</td>
                            <td class="px-3 py-2 font-sans text-gray-500">Path below category</td>
                            <td class="px-3 py-2 text-gray-600">Commercial / Percentages</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-bold text-[#087F82]">3</td>
                            <td class="px-3 py-2 font-sans font-semibold">Question Number</td>
                            <td class="px-3 py-2 font-sans text-gray-500">Unique integer within topic</td>
                            <td class="px-3 py-2 text-gray-600">1</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-bold text-[#087F82]">4</td>
                            <td class="px-3 py-2 font-sans font-semibold">Question Text</td>
                            <td class="px-3 py-2 font-sans text-gray-500">Full question statement</td>
                            <td class="px-3 py-2 text-gray-600">What is 25% of 240?</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-bold text-[#087F82]">5-8</td>
                            <td class="px-3 py-2 font-sans font-semibold">Option A, B, C, D</td>
                            <td class="px-3 py-2 font-sans text-gray-500">Four answer choices</td>
                            <td class="px-3 py-2 text-gray-600">40 | 50 | 60 | 80</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-bold text-[#087F82]">9</td>
                            <td class="px-3 py-2 font-sans font-semibold">Correct Option</td>
                            <td class="px-3 py-2 font-sans text-gray-500">Letter choice (A, B, C, or D)</td>
                            <td class="px-3 py-2 text-emerald-600 font-bold">C</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-bold text-[#087F82]">10</td>
                            <td class="px-3 py-2 font-sans font-semibold">Explanation</td>
                            <td class="px-3 py-2 font-sans text-gray-500">Step-by-step solution</td>
                            <td class="px-3 py-2 text-gray-600">25% = 1/4, so 240/4 = 60.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
