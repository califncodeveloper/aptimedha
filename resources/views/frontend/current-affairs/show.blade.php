<x-layouts.app>
    <x-slot:title>{{ $issue->title }} - AptiMedha Current Affairs</x-slot:title>
    <x-slot:metaDescription>{{ Str::limit(strip_tags($issue->description), 150) }}</x-slot:metaDescription>

    <div class="min-h-screen bg-[#F7F9FC] py-8 lg:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Breadcrumb Navigation --}}
            <nav class="flex items-center text-xs font-semibold text-gray-400 space-x-2" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-[#087F82] transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <a href="{{ route('current-affairs.index') }}" class="hover:text-[#087F82] transition-colors">Current Affairs</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <a href="{{ route('current-affairs.index', ['year' => $issue->year]) }}" class="hover:text-[#087F82] transition-colors">{{ $issue->year }}</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-[#153B70] truncate max-w-xs">{{ $issue->month_name }}</span>
            </nav>

            {{-- Main Layout Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- Left Main Column (Overview & PDF Viewer) --}}
                <div class="lg:col-span-8 space-y-6">

                    {{-- Issue Header Card --}}
                    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/90 shadow-2xs space-y-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-[#087F82] border border-teal-200">
                                <i data-lucide="calendar" class="w-3 h-3 mr-1.5"></i> {{ $issue->period_label }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#153B70]/10 text-[#153B70]">
                                <i data-lucide="file" class="w-3 h-3 mr-1.5"></i> {{ $issue->formatted_file_size }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                <i data-lucide="download" class="w-3 h-3 mr-1.5"></i> {{ number_format($issue->downloads_count) }} downloads
                            </span>
                        </div>

                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-[#153B70] tracking-tight">
                            {{ $issue->title }}
                        </h1>

                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <a href="{{ route('current-affairs.download', $issue) }}"
                               class="inline-flex items-center px-6 py-3 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-sm font-bold shadow-2xs transition-all">
                                <i data-lucide="download" class="w-4 h-4 mr-2"></i> Download Full PDF ({{ $issue->formatted_file_size }})
                            </a>

                            <a href="{{ route('current-affairs.preview', $issue) }}"
                               target="_blank"
                               class="inline-flex items-center px-5 py-3 rounded-xl bg-white border border-gray-300 hover:bg-gray-50 text-[#153B70] text-sm font-bold transition-all">
                                <i data-lucide="maximize-2" class="w-4 h-4 mr-2 text-[#087F82]"></i> Open Fullscreen
                            </a>
                        </div>
                    </div>

                    {{-- Description & Topic Overview --}}
                    @if(!empty($issue->description))
                        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/90 shadow-2xs space-y-4">
                            <h2 class="text-lg font-bold text-[#153B70] flex items-center">
                                <i data-lucide="book-open" class="w-5 h-5 mr-2 text-[#087F82]"></i> Edition Overview & Topics Covered
                            </h2>
                            <div class="prose max-w-none text-[#243247]/90 text-sm sm:text-base leading-relaxed">
                                {!! $issue->description !!}
                            </div>
                        </div>
                    @endif

                    {{-- Embedded PDF Viewer Card --}}
                    <div class="bg-white p-4 sm:p-6 rounded-3xl border border-gray-200/90 shadow-2xs space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-[#153B70] flex items-center">
                                <i data-lucide="eye" class="w-4 h-4 mr-2 text-[#087F82]"></i> Online PDF Reader
                            </h3>
                            <a href="{{ route('current-affairs.preview', $issue) }}" target="_blank" class="text-xs font-bold text-[#087F82] hover:underline flex items-center">
                                <i data-lucide="external-link" class="w-3.5 h-3.5 mr-1"></i> New Tab
                            </a>
                        </div>

                        <div class="w-full rounded-2xl overflow-hidden border border-gray-200 bg-[#F7F9FC]">
                            <iframe src="{{ route('current-affairs.preview', $issue) }}"
                                    title="PDF Reader: {{ $issue->title }}"
                                    class="w-full h-[650px] sm:h-[750px] border-0"
                                    loading="lazy">
                            </iframe>
                        </div>

                        <p class="text-xs text-gray-500 text-center">
                            Having trouble viewing the PDF?
                            <a href="{{ route('current-affairs.download', $issue) }}" class="text-[#087F82] font-bold hover:underline">Click here to download</a> directly to your device.
                        </p>
                    </div>
                </div>

                {{-- Right Sidebar --}}
                <div class="lg:col-span-4 space-y-6">

                    {{-- Other Issues in Same Year --}}
                    <div class="bg-white p-6 rounded-3xl border border-gray-200/90 shadow-2xs space-y-4">
                        <h3 class="text-base font-bold text-[#153B70] flex items-center justify-between">
                            <span>More from {{ $issue->year }}</span>
                            <a href="{{ route('current-affairs.index', ['year' => $issue->year]) }}" class="text-xs font-bold text-[#087F82] hover:underline">View All</a>
                        </h3>

                        <div class="divide-y divide-gray-100">
                            @forelse($otherIssues as $other)
                                <a href="{{ $other->public_url }}" class="py-3 flex items-center justify-between group block">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 rounded-lg bg-teal-50 border border-teal-100 text-[#087F82] flex flex-col items-center justify-center shrink-0 group-hover:bg-[#087F82] group-hover:text-white transition-colors">
                                            <span class="text-[9px] uppercase font-bold">{{ substr($other->month_name, 0, 3) }}</span>
                                            <span class="text-xs font-black">{{ $other->year }}</span>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-[#153B70] group-hover:text-[#087F82] transition-colors line-clamp-1">
                                                {{ $other->month_name }} {{ $other->year }}
                                            </h4>
                                            <span class="text-[11px] text-gray-400">{{ $other->formatted_file_size }}</span>
                                        </div>
                                    </div>
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400 group-hover:text-[#087F82] transition-colors"></i>
                                </a>
                            @empty
                                <p class="text-xs text-gray-400 py-2">No other issues for this year.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Placement Prep Promotion Card --}}
                    <div class="bg-gradient-to-br from-[#153B70] to-[#087F82] p-6 rounded-3xl text-white shadow-2xs space-y-4">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                            <i data-lucide="graduation-cap" class="w-5 h-5 text-teal-200"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white">Placement Preparation Hub</h3>
                            <p class="text-xs text-white/80 mt-1 leading-relaxed">
                                Boost your campus placement performance with resume blueprints, HR behavioral frameworks, and aptitude question banks.
                            </p>
                        </div>
                        <a href="{{ route('placement.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-white text-[#153B70] text-xs font-bold hover:bg-gray-100 transition-colors">
                            Explore Placement Guides <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1.5"></i>
                        </a>
                    </div>

                    {{-- Practice MCQs Card --}}
                    <div class="bg-white p-6 rounded-3xl border border-gray-200/90 shadow-2xs space-y-3">
                        <h4 class="text-sm font-bold text-[#153B70]">Daily GK & Aptitude MCQs</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Practice real test questions with instant verified answers and step-by-step explanations.
                        </p>
                        <a href="{{ route('mcq.index') }}" class="inline-flex items-center text-xs font-bold text-[#087F82] hover:underline pt-1">
                            Browse Question Bank <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

