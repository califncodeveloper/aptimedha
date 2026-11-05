<x-layouts.admin>
    <x-slot:title>Bulk Import MCQs - AptiMedha Admin</x-slot:title>
    <x-slot:header>Bulk MCQ Import with Cloudinary Image Integration</x-slot:header>

    <div class="space-y-8 max-w-5xl" x-data="{ 
        fileName: '', 
        zipName: '',
        isUploading: false,
        fileSelected(e) {
            if(e.target.files.length) {
                this.fileName = e.target.files[0].name;
            }
        },
        zipSelected(e) {
            if(e.target.files.length) {
                this.zipName = e.target.files[0].name;
            } else {
                this.zipName = '';
            }
        }
    }">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.mcqs.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to MCQ Bank
            </a>
            <span class="text-xs text-gray-500 font-medium">Canonical Schema & Cloudinary Integrated</span>
        </div>

        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-start gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 text-red-500 mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-bold">Import Error</p>
                    <p class="mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs flex items-start gap-3">
                <i data-lucide="info" class="w-5 h-5 shrink-0 text-blue-500 mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-bold">Information</p>
                    <p class="mt-0.5">{{ session('info') }}</p>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-start gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 shrink-0 text-emerald-600 mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-bold">Success</p>
                    <p class="mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Upload Container --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8">
            <form action="{{ route('admin.mcqs.import') }}" method="POST" enctype="multipart/form-data" @submit="isUploading = true" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Spreadsheet File Drop Area --}}
                    <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:border-[#087F82] bg-[#F7F9FC]/60 transition-colors relative cursor-pointer group flex flex-col items-center justify-center min-h-[200px]">
                        <input type="file" 
                               name="file" 
                               accept=".xlsx,.csv" 
                               required 
                               @change="fileSelected"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        
                        <div class="w-12 h-12 rounded-xl bg-teal-50 text-[#087F82] flex items-center justify-center mb-2.5 group-hover:bg-[#087F82] group-hover:text-white transition-colors">
                            <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
                        </div>

                        <p class="text-xs font-bold text-[#153B70]" x-text="fileName ? fileName : '1. Upload Excel / CSV (Required)'"></p>
                        <p class="text-[11px] text-gray-500 mt-1">.xlsx or .csv up to 15MB</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 mt-2">Required</span>
                    </div>

                    {{-- Images ZIP Archive Drop Area --}}
                    <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:border-[#087F82] bg-[#F7F9FC]/60 transition-colors relative cursor-pointer group flex flex-col items-center justify-center min-h-[200px]">
                        <input type="file" 
                               name="zip_file" 
                               accept=".zip" 
                               @change="zipSelected"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center mb-2.5 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                            <i data-lucide="archive" class="w-6 h-6"></i>
                        </div>

                        <p class="text-xs font-bold text-[#153B70]" x-text="zipName ? zipName : '2. Upload Images ZIP (Optional)'"></p>
                        <p class="text-[11px] text-gray-500 mt-1">.zip with referenced images up to 50MB</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600 mt-2">Optional Cloudinary Sync</span>
                    </div>
                </div>

                {{-- Action Bar --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.mcqs.import.template') }}" class="inline-flex items-center text-xs font-bold text-[#087F82] hover:text-[#066668] transition-colors py-2">
                        <i data-lucide="download" class="w-4 h-4 mr-1.5 shrink-0"></i> Download Import Template with Image Columns (.csv)
                    </a>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <a href="{{ route('admin.mcqs.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50 text-center">Cancel</a>
                        <button type="submit" 
                                :disabled="isUploading"
                                class="px-6 py-2.5 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold shadow-xs transition-colors cursor-pointer flex items-center justify-center gap-2">
                            <span x-show="!isUploading">Upload and Validate Workbook</span>
                            <span x-show="isUploading" style="display:none;" class="flex items-center gap-2">
                                <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Validating Workbook...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ZIP Folder Structure & Cloudinary Instructions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 space-y-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-teal-50 text-[#087F82] flex items-center justify-center">
                        <i data-lucide="folder-tree" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-sm font-bold text-[#153B70]">ZIP Folder Structure Guide</h3>
                </div>
                <p class="text-xs text-gray-600 leading-relaxed">
                    You can preserve a folder structure or keep unique filenames in the ZIP archive:
                </p>
                <div class="bg-[#F7F9FC] p-3 rounded-xl border border-gray-200 font-mono text-[11px] text-gray-700 leading-snug">
                    bulk-import.zip<br>
                    ├── images/<br>
                    │&nbsp;&nbsp;&nbsp;├── q1.png<br>
                    │&nbsp;&nbsp;&nbsp;├── q1_option_a.png<br>
                    │&nbsp;&nbsp;&nbsp;├── q1_option_b.png<br>
                    │&nbsp;&nbsp;&nbsp;└── q1_explanation.png<br>
                    └── q2_chart.png
                </div>
                <p class="text-[11px] text-gray-500">
                    Image columns in your Excel/CSV can reference either relative paths (e.g. <code class="text-[#087F82]">images/q1.png</code>) or unique basenames (<code class="text-[#087F82]">q1.png</code>).
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 space-y-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-[#153B70] flex items-center justify-center">
                        <i data-lucide="cloud" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-sm font-bold text-[#153B70]">Cloudinary Storage Folders</h3>
                </div>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Referenced images from valid MCQ rows are uploaded into dedicated Cloudinary directories:
                </p>
                <ul class="text-xs space-y-1.5 text-gray-600">
                    <li class="flex items-center gap-2">
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#087F82]"></i>
                        <span>Questions: <code class="font-mono text-[#153B70] bg-gray-100 px-1 py-0.5 rounded text-[11px]">aptimedha/mcqs/questions</code></span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#087F82]"></i>
                        <span>Options A–D: <code class="font-mono text-[#153B70] bg-gray-100 px-1 py-0.5 rounded text-[11px]">aptimedha/mcqs/options</code></span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#087F82]"></i>
                        <span>Explanations: <code class="font-mono text-[#153B70] bg-gray-100 px-1 py-0.5 rounded text-[11px]">aptimedha/mcqs/explanations</code></span>
                    </li>
                </ul>
                <p class="text-[11px] text-gray-500">
                    Only images referenced by successfully imported MCQs are uploaded. Unreferenced files are discarded safely.
                </p>
            </div>
        </div>

        {{-- Canonical Schema Specification Card matching SRS Section 9 --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8 space-y-4">
            <div>
                <h3 class="text-base font-bold text-[#153B70]">Required 10-Column Spreadsheet Schema</h3>
                <p class="text-xs text-gray-500 mt-0.5">Required core columns (1–10) and optional image reference columns (11–16):</p>
            </div>

            <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full text-left text-xs border border-gray-200 rounded-xl overflow-hidden min-w-[650px]">
                    <thead class="bg-gray-50 text-gray-700 font-bold border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2.5">Col</th>
                            <th class="px-3 py-2.5">Header Name</th>
                            <th class="px-3 py-2.5">Required</th>
                            <th class="px-3 py-2.5">Description</th>
                            <th class="px-3 py-2.5">Example Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-mono text-[11px]">
                        <tr>
                            <td class="px-3 py-2 font-bold text-[#087F82]">1</td>
                            <td class="px-3 py-2 font-sans font-semibold">Question Topic</td>
                            <td class="px-3 py-2 font-sans text-red-600 font-bold">Yes</td>
                            <td class="px-3 py-2 font-sans text-gray-500">Canonical Category name</td>
                            <td class="px-3 py-2 text-gray-600">Arithmetic Aptitude</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-bold text-[#087F82]">2</td>
                            <td class="px-3 py-2 font-sans font-semibold">Question Subtopic</td>
                            <td class="px-3 py-2 font-sans text-red-600 font-bold">Yes</td>
                            <td class="px-3 py-2 font-sans text-gray-500">Topic name or Subject / Topic</td>
                            <td class="px-3 py-2 text-gray-600">Number System</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-bold text-[#087F82]">3</td>
                            <td class="px-3 py-2 font-sans font-semibold">Question Number</td>
                            <td class="px-3 py-2 font-sans text-red-600 font-bold">Yes</td>
                            <td class="px-3 py-2 font-sans text-gray-500">Unique integer within topic</td>
                            <td class="px-3 py-2 text-gray-600">1</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-bold text-[#087F82]">4</td>
                            <td class="px-3 py-2 font-sans font-semibold">Question Text</td>
                            <td class="px-3 py-2 font-sans text-red-600 font-bold">Yes</td>
                            <td class="px-3 py-2 font-sans text-gray-500">Full question statement</td>
                            <td class="px-3 py-2 text-gray-600">How many positive factors does 36 have?</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-bold text-[#087F82]">5-8</td>
                            <td class="px-3 py-2 font-sans font-semibold">Option A, B, C, D</td>
                            <td class="px-3 py-2 font-sans text-red-600 font-bold">Yes</td>
                            <td class="px-3 py-2 font-sans text-gray-500">Four non-empty options</td>
                            <td class="px-3 py-2 text-gray-600">10 | 11 | 9 | 8</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-bold text-[#087F82]">9</td>
                            <td class="px-3 py-2 font-sans font-semibold">Correct Option</td>
                            <td class="px-3 py-2 font-sans text-red-600 font-bold">Yes</td>
                            <td class="px-3 py-2 font-sans text-gray-500">Single letter: A, B, C, or D</td>
                            <td class="px-3 py-2 text-emerald-600 font-bold">C</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-bold text-[#087F82]">10</td>
                            <td class="px-3 py-2 font-sans font-semibold">Explanation</td>
                            <td class="px-3 py-2 font-sans text-gray-400">Optional</td>
                            <td class="px-3 py-2 font-sans text-gray-500">Detailed step-by-step solution</td>
                            <td class="px-3 py-2 text-gray-600">36 = 2^2 × 3^2. Factor count = (2+1)(2+1) = 9.</td>
                        </tr>
                        <tr class="bg-teal-50/40">
                            <td class="px-3 py-2 font-bold text-[#087F82]">11</td>
                            <td class="px-3 py-2 font-sans font-semibold text-[#087F82]">Question Image</td>
                            <td class="px-3 py-2 font-sans text-gray-400">Optional</td>
                            <td class="px-3 py-2 font-sans text-gray-600">Filename in ZIP archive or URL</td>
                            <td class="px-3 py-2 text-gray-600">images/q1.png</td>
                        </tr>
                        <tr class="bg-teal-50/40">
                            <td class="px-3 py-2 font-bold text-[#087F82]">12-15</td>
                            <td class="px-3 py-2 font-sans font-semibold text-[#087F82]">Option A–D Image</td>
                            <td class="px-3 py-2 font-sans text-gray-400">Optional</td>
                            <td class="px-3 py-2 font-sans text-gray-600">Filenames for option graphics</td>
                            <td class="px-3 py-2 text-gray-600">images/q1_opt_a.png</td>
                        </tr>
                        <tr class="bg-teal-50/40">
                            <td class="px-3 py-2 font-bold text-[#087F82]">16</td>
                            <td class="px-3 py-2 font-sans font-semibold text-[#087F82]">Explanation Image</td>
                            <td class="px-3 py-2 font-sans text-gray-400">Optional</td>
                            <td class="px-3 py-2 font-sans text-gray-600">Filename for diagram in explanation</td>
                            <td class="px-3 py-2 text-gray-600">images/q1_explanation.png</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Bulk Import History Audit Trail --}}
        @if(isset($recentBatches) && $recentBatches->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-[#153B70]">Recent Import Audit Batches</h3>
                        <p class="text-xs text-gray-500 mt-0.5">History of recent bulk uploads and processing logs</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border border-gray-200 rounded-xl overflow-hidden">
                        <thead class="bg-gray-50 text-gray-700 font-bold border-b border-gray-200">
                            <tr>
                                <th class="px-3 py-2.5">Batch #</th>
                                <th class="px-3 py-2.5">Filename</th>
                                <th class="px-3 py-2.5">Initiated By</th>
                                <th class="px-3 py-2.5">Total</th>
                                <th class="px-3 py-2.5">Imported</th>
                                <th class="px-3 py-2.5">Skipped / Failed</th>
                                <th class="px-3 py-2.5">Status</th>
                                <th class="px-3 py-2.5">Date</th>
                                <th class="px-3 py-2.5 text-right">Audit Report</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-[11px]">
                            @foreach($recentBatches as $batch)
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-3 py-2.5 font-bold text-[#087F82]">#{{ $batch->id }}</td>
                                    <td class="px-3 py-2.5 font-semibold text-gray-800">{{ $batch->filename }}</td>
                                    <td class="px-3 py-2.5 text-gray-600">{{ $batch->user?->name ?? 'Admin' }}</td>
                                    <td class="px-3 py-2.5 text-gray-700 font-medium">{{ $batch->total_rows }}</td>
                                    <td class="px-3 py-2.5 text-emerald-600 font-bold">{{ $batch->successful_rows }}</td>
                                    <td class="px-3 py-2.5 {{ $batch->failed_rows > 0 ? 'text-amber-600 font-semibold' : 'text-gray-400' }}">
                                        {{ $batch->failed_rows }}
                                    </td>
                                    <td class="px-3 py-2.5">
                                        @if($batch->status === 'completed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Completed</span>
                                        @elseif($batch->status === 'failed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">Failed</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">{{ ucfirst($batch->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-gray-500">{{ $batch->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-3 py-2.5 text-right">
                                        <a href="{{ route('admin.mcqs.import.export-errors', $batch->id) }}" class="inline-flex items-center font-bold text-[#087F82] hover:underline">
                                            <i data-lucide="download" class="w-3.5 h-3.5 mr-1"></i> Export Log
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>
