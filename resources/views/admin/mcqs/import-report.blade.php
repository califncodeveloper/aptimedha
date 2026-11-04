<x-layouts.admin>
    <x-slot:title>Import Report - AptiMedha Admin</x-slot:title>
    <x-slot:header>Bulk MCQ Import Completion Summary</x-slot:header>

    <div class="space-y-6 max-w-5xl">
        {{-- Completion Banner --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[#153B70]">Bulk MCQ Import Completed</h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Batch <span class="font-bold text-gray-800">#{{ $report['batch_id'] }}</span> for file <span class="font-bold text-gray-800">{{ $filename }}</span> has been processed.
                    </p>
                </div>
            </div>

            {{-- Summary Metric Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mt-6 pt-6 border-t border-gray-100">
                <div class="bg-[#F7F9FC] p-4 rounded-xl border border-gray-200/70">
                    <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Total Rows</div>
                    <div class="text-2xl font-bold text-[#153B70] mt-1">{{ number_format($report['total_rows']) }}</div>
                </div>

                <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-100">
                    <div class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider">Successfully Added</div>
                    <div class="text-2xl font-bold text-emerald-700 mt-1">{{ number_format($report['imported_count']) }}</div>
                </div>

                <div class="bg-amber-50/50 p-4 rounded-xl border border-amber-100">
                    <div class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider">Skipped Duplicates</div>
                    <div class="text-2xl font-bold text-amber-700 mt-1">{{ number_format($report['duplicate_count']) }}</div>
                </div>

                <div class="bg-red-50/50 p-4 rounded-xl border border-red-100">
                    <div class="text-[11px] font-semibold text-red-700 uppercase tracking-wider">Invalid Rows</div>
                    <div class="text-2xl font-bold text-red-700 mt-1">{{ number_format($report['invalid_count']) }}</div>
                </div>

                <div class="bg-purple-50/50 p-4 rounded-xl border border-purple-100 col-span-2 sm:col-span-1">
                    <div class="text-[11px] font-semibold text-purple-700 uppercase tracking-wider">Missing Images</div>
                    <div class="text-2xl font-bold text-purple-700 mt-1">{{ number_format($report['missing_image_count']) }}</div>
                </div>
            </div>

            {{-- Actions Bar --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.mcqs.import.export-errors', $batch->id) }}" class="inline-flex items-center text-xs font-bold text-[#087F82] hover:underline">
                    <i data-lucide="download" class="w-4 h-4 mr-1.5"></i> Download Audit & Error Report (.csv)
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.mcqs.import.form') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                        Import Another File
                    </a>
                    <a href="{{ route('admin.mcqs.index') }}" class="px-6 py-2.5 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold shadow-xs">
                        View MCQ Bank
                    </a>
                </div>
            </div>
        </div>

        {{-- Detailed Audit Log Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-[#153B70]">Batch Processing Log</h3>
                <span class="text-xs text-gray-400">Total entries: {{ count($report['details']) }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 font-bold border-b border-gray-100 text-[11px]">
                        <tr>
                            <th class="px-4 py-3">Row & Sheet</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Question Statement</th>
                            <th class="px-4 py-3">Log Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 text-[11px]">
                        @forelse(array_slice($report['details'], 0, 150) as $item)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3 font-mono font-bold text-gray-600 whitespace-nowrap">
                                    {{ $item['sheet'] }} #{{ $item['row'] }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($item['status'] === 'imported')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            Imported
                                        </span>
                                    @elseif($item['status'] === 'duplicate')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Duplicate Skipped
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
                                            Invalid Skipped
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 max-w-xs truncate text-gray-600">
                                    {{ $item['question'] }}
                                </td>
                                <td class="px-4 py-3 {{ $item['status'] === 'imported' ? 'text-gray-500' : 'text-amber-700 font-medium' }}">
                                    {{ $item['reason'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-400">
                                    No audit entries recorded for this batch.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>

