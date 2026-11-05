<x-layouts.admin>
    <x-slot:title>Import Preview - AptiMedha Admin</x-slot:title>
    <x-slot:header>Bulk MCQ Import Validation Preview</x-slot:header>

    <div class="space-y-6 max-w-6xl" x-data="{ 
        filter: 'all',
        isConfirming: false
    }">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.mcqs.import.cancel') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <button type="submit" class="text-xs font-bold text-[#153B70] hover:underline flex items-center cursor-pointer">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Cancel & Re-upload
                    </button>
                </form>
            </div>
            <div class="flex items-center gap-4 text-xs text-gray-500">
                <div>File: <span class="font-bold text-gray-700">{{ $filename }}</span></div>
                @if(!empty($zipFilename))
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-teal-50 text-[#087F82] font-semibold">
                        <i data-lucide="archive" class="w-3.5 h-3.5"></i>
                        <span>{{ $zipFilename }} ({{ $summary['images_archive_count'] ?? 0 }} images)</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Validation KPI Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-gray-200/90 shadow-xs">
                <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total Rows</div>
                <div class="text-2xl font-bold text-[#153B70] mt-1">{{ number_format($summary['total_rows']) }}</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200/90 shadow-xs">
                <div class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Valid MCQs</div>
                <div class="text-2xl font-bold text-emerald-700 mt-1">{{ number_format($summary['valid_count']) }}</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200/90 shadow-xs">
                <div class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider">Duplicate Rows</div>
                <div class="text-2xl font-bold text-amber-700 mt-1">{{ number_format($summary['duplicate_count']) }}</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200/90 shadow-xs">
                <div class="text-[11px] font-semibold text-red-600 uppercase tracking-wider">Invalid Rows</div>
                <div class="text-2xl font-bold text-red-700 mt-1">{{ number_format($summary['invalid_count']) }}</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200/90 shadow-xs col-span-2 sm:col-span-1">
                <div class="text-[11px] font-semibold text-purple-600 uppercase tracking-wider">Image Warnings</div>
                <div class="text-2xl font-bold text-purple-700 mt-1">{{ number_format($summary['missing_image_count']) }}</div>
            </div>
        </div>

        {{-- Confirmation Callout Card --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6">
            <form action="{{ route('admin.mcqs.import.confirm') }}" method="POST" @submit="isConfirming = true" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-bold text-[#153B70]">Ready to Execute Import</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            @if($summary['valid_count'] > 0)
                                You can safely commit <strong class="text-emerald-700">{{ $summary['valid_count'] }}</strong> valid questions into the database using safe transaction wrapping.
                                @if(!empty($hasImages))
                                    Referenced images will be automatically synced to Cloudinary.
                                @endif
                            @else
                                No valid questions found in this batch. Please fix row errors and re-upload.
                            @endif
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <form action="{{ route('admin.mcqs.import.cancel') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <button type="submit" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50 cursor-pointer">
                                Cancel
                            </button>
                        </form>

                        @if($summary['valid_count'] > 0)
                            <button type="submit" 
                                    :disabled="isConfirming"
                                    class="px-6 py-2.5 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold shadow-xs transition-colors cursor-pointer flex items-center gap-2">
                                <span x-show="!isConfirming">Confirm & Import {{ $summary['valid_count'] }} Valid MCQs</span>
                                <span x-show="isConfirming" style="display:none;" class="flex items-center gap-2">
                                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Writing & Syncing to Cloudinary...
                                </span>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-6 pt-1 text-xs text-gray-600">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="skip_duplicates" value="1" checked class="w-4 h-4 rounded border-gray-300 text-[#087F82] focus:ring-[#087F82]">
                        <span>Skip duplicate rows automatically (Recommended)</span>
                    </label>

                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="skip_invalids" value="1" checked class="w-4 h-4 rounded border-gray-300 text-[#087F82] focus:ring-[#087F82]">
                        <span>Skip invalid rows automatically</span>
                    </label>
                </div>
            </form>
        </div>

        {{-- Preview Table Container --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            {{-- Table Filter Tabs --}}
            <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="filter = 'all'" 
                            :class="filter === 'all' ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                        All Rows ({{ $summary['total_rows'] }})
                    </button>
                    <button type="button" 
                            @click="filter = 'valid'" 
                            :class="filter === 'valid' ? 'bg-emerald-700 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                        Valid ({{ $summary['valid_count'] }})
                    </button>
                    <button type="button" 
                            @click="filter = 'duplicate'" 
                            :class="filter === 'duplicate' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                        Duplicates ({{ $summary['duplicate_count'] }})
                    </button>
                    <button type="button" 
                            @click="filter = 'invalid'" 
                            :class="filter === 'invalid' ? 'bg-red-700 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100'" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                        Invalid ({{ $summary['invalid_count'] }})
                    </button>
                </div>

                <div class="text-[11px] text-gray-400">
                    Showing first {{ min(200, count($rows)) }} rows preview
                </div>
            </div>

            {{-- Rows Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50/80 text-gray-600 font-bold border-b border-gray-100 text-[11px]">
                        <tr>
                            <th class="px-4 py-3">Row</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Category & Topic</th>
                            <th class="px-4 py-3">Q#</th>
                            <th class="px-4 py-3">Question Statement</th>
                            <th class="px-4 py-3">Images</th>
                            <th class="px-4 py-3">Ans</th>
                            <th class="px-4 py-3">Validation Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 text-[11px]">
                        @foreach(array_slice($rows, 0, 200) as $row)
                            <tr x-show="filter === 'all' || filter === '{{ $row['status'] }}'" class="hover:bg-gray-50/60">
                                <td class="px-4 py-3 font-mono font-bold text-gray-500 whitespace-nowrap">
                                    {{ $row['sheet'] }} #{{ $row['row_number'] }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($row['status'] === 'valid')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            Valid
                                        </span>
                                    @elseif($row['status'] === 'duplicate')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Duplicate
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
                                            Invalid
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-bold text-gray-800">{{ $row['category_name'] }}</div>
                                    <div class="text-[10px] text-[#087F82] font-semibold">{{ $row['topic_name'] }}</div>
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-gray-700 whitespace-nowrap">
                                    {{ $row['question_number'] ?? 'Auto' }}
                                </td>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $row['question_text'] }}">
                                    {{ $row['question_text'] }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $imgs = [];
                                        if(!empty($row['question_image'])) $imgs[] = 'Q';
                                        if(!empty($row['option_a_image'])) $imgs[] = 'Opt A';
                                        if(!empty($row['option_b_image'])) $imgs[] = 'Opt B';
                                        if(!empty($row['option_c_image'])) $imgs[] = 'Opt C';
                                        if(!empty($row['option_d_image'])) $imgs[] = 'Opt D';
                                        if(!empty($row['explanation_image'])) $imgs[] = 'Exp';
                                    @endphp
                                    @if(!empty($imgs))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-teal-50 text-[#087F82] border border-teal-200">
                                            <i data-lucide="image" class="w-3 h-3"></i>
                                            <span>{{ implode(', ', $imgs) }}</span>
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-[10px]">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-bold text-emerald-700 whitespace-nowrap">
                                    {{ $row['correct_option'] }}
                                </td>
                                <td class="px-4 py-3 max-w-sm">
                                    @if(!empty($row['errors']))
                                        <div class="text-red-600 font-medium">
                                            {{ implode('; ', $row['errors']) }}
                                        </div>
                                    @endif
                                    @if(!empty($row['warnings']))
                                        <div class="text-amber-600 text-[10px] mt-0.5">
                                            {{ implode('; ', $row['warnings']) }}
                                        </div>
                                    @endif
                                    @if(empty($row['errors']) && empty($row['warnings']))
                                        <span class="text-gray-400">Ready to insert</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
