<x-layouts.app>
    <x-slot:title>Current Affairs & Monthly GK PDFs - AptiMedha</x-slot:title>
    <x-slot:metaDescription>Download curated monthly Current Affairs PDF capsules and practice General Knowledge MCQs for campus placements, banking, SSC, and competitive examinations.</x-slot:metaDescription>

    <div class="min-h-screen bg-[#F7F9FC]">
        {{-- Hero Header --}}
        <section class="bg-gradient-to-b from-[#153B70] to-[#0f2a50] text-white py-12 lg:py-16 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#087F82_1px,transparent_1px)] [background-size:16px_16px]"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-xs text-[#087F82] border border-white/10 text-xs font-bold uppercase tracking-wider mb-4">
                        <i data-lucide="newspaper" class="w-3.5 h-3.5 text-teal-300"></i>
                        <span class="text-teal-200">Monthly Knowledge Capsule</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white mb-4">
                        Monthly Current Affairs & GK Digests
                    </h1>
                    <p class="text-base sm:text-lg text-white/80 leading-relaxed max-w-2xl">
                        Comprehensive monthly PDF capsules designed for aptitude tests, campus recruitment assessments, and government exams. Review national policies, global summits, economy trends, and high-frequency MCQs.
                    </p>
                </div>
            </div>
        </section>

        {{-- Main Container --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 space-y-10">

            {{-- Spotlight / Latest Edition Card (shown if no search filter active) --}}
            @if($latestIssue && empty($searchTerm) && empty($selectedMonth) && (empty($selectedYear) || $selectedYear == $latestIssue->year))
                <div class="bg-white rounded-3xl border border-gray-200/90 shadow-sm overflow-hidden transition-all hover:shadow-md">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
                        <div class="lg:col-span-8 p-6 sm:p-8 lg:p-10 flex flex-col justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-[#087F82] border border-teal-200">
                                        <i data-lucide="sparkles" class="w-3 h-3 mr-1 text-[#087F82]"></i> Latest Edition
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#153B70]/10 text-[#153B70]">
                                        {{ $latestIssue->period_label }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        {{ $latestIssue->formatted_file_size }}
                                    </span>
                                </div>

                                <h2 class="text-2xl sm:text-3xl font-black text-[#153B70] tracking-tight mb-3">
                                    <a href="{{ $latestIssue->public_url }}" class="hover:text-[#087F82] transition-colors">
                                        {{ $latestIssue->title }}
                                    </a>
                                </h2>

                                <p class="text-sm sm:text-base text-[#243247]/80 leading-relaxed mb-6">
                                    {{ Str::limit(strip_tags($latestIssue->description), 240) }}
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-100">
                                <a href="{{ route('current-affairs.download', $latestIssue) }}"
                                   class="inline-flex items-center px-5 py-2.5 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-sm font-bold shadow-2xs transition-all">
                                    <i data-lucide="download" class="w-4 h-4 mr-2"></i> Download PDF ({{ $latestIssue->formatted_file_size }})
                                </a>

                                <a href="{{ route('current-affairs.preview', $latestIssue) }}"
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white border border-gray-300 hover:bg-gray-50 text-[#153B70] text-sm font-bold transition-all">
                                    <i data-lucide="eye" class="w-4 h-4 mr-2 text-[#087F82]"></i> Preview Online
                                </a>

                                <a href="{{ $latestIssue->public_url }}"
                                   class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-[#087F82] hover:underline ml-auto">
                                    Full Issue Details <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Right preview banner --}}
                        <div class="lg:col-span-4 bg-gradient-to-br from-[#153B70] to-[#087F82] p-8 flex flex-col items-center justify-center text-center text-white relative">
                            <div class="w-20 h-20 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 flex flex-col items-center justify-center shadow-lg mb-4">
                                <span class="text-xs uppercase font-extrabold tracking-wider text-teal-200">{{ substr($latestIssue->month_name, 0, 3) }}</span>
                                <span class="text-2xl font-black">{{ $latestIssue->year }}</span>
                            </div>
                            <span class="text-xs font-semibold text-white/70 uppercase tracking-widest mb-1">AptiMedha Capsule</span>
                            <span class="text-sm font-bold text-white max-w-[200px]">{{ $latestIssue->period_label }}</span>
                            <div class="mt-4 flex items-center text-xs text-white/80 space-x-2">
                                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                <span>{{ number_format($latestIssue->downloads_count) }} downloads</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Filter & Search Bar --}}
            <div class="bg-white p-4 sm:p-6 rounded-2xl border border-gray-200/90 shadow-2xs space-y-4">
                <form method="GET" action="{{ route('current-affairs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5">
                    {{-- Search --}}
                    <div class="lg:col-span-5 relative">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search monthly digests, GK topics..."
                               class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">
                    </div>

                    {{-- Year Filter --}}
                    <div class="lg:col-span-3">
                        <select name="year" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">
                            <option value="">All Years</option>
                            @foreach($availableYears as $yr)
                                <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>Year {{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Month Filter --}}
                    <div class="lg:col-span-3">
                        <select name="month" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-hidden focus:ring-2 focus:ring-[#087F82]/20 focus:border-[#087F82]">
                            <option value="">All Months</option>
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Button --}}
                    <div class="lg:col-span-1 flex items-center space-x-2">
                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#153B70] hover:bg-[#0f2a50] text-white text-xs font-bold transition-colors flex items-center justify-center min-h-[42px]">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'year', 'month']))
                            <a href="{{ route('current-affairs.index') }}" title="Reset Filters" class="p-2.5 rounded-xl border border-gray-200 text-gray-400 hover:text-gray-700 hover:bg-gray-50 flex items-center justify-center min-h-[42px]">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </a>
                        @endif
                    </div>
                </form>

                {{-- Quick Year Tabs --}}
                @if(count($availableYears) > 1)
                    <div class="flex items-center gap-2 pt-2 border-t border-gray-100 overflow-x-auto pb-1 text-xs font-bold">
                        <span class="text-gray-400 uppercase tracking-wider mr-1">Year:</span>
                        <a href="{{ route('current-affairs.index', array_merge(request()->except('year'), [])) }}"
                           class="px-3 py-1.5 rounded-lg transition-colors {{ empty($selectedYear) ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            All Years
                        </a>
                        @foreach($availableYears as $yr)
                            <a href="{{ route('current-affairs.index', array_merge(request()->except('year'), ['year' => $yr])) }}"
                               class="px-3 py-1.5 rounded-lg transition-colors {{ $selectedYear == $yr ? 'bg-[#153B70] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                {{ $yr }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Issues Grid --}}
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-black text-[#153B70] tracking-tight">
                        Monthly Archive ({{ $issues->total() }})
                    </h3>
                    @if($selectedYear)
                        <span class="text-xs font-bold text-[#087F82] bg-teal-50 px-3 py-1 rounded-full border border-teal-200">
                            Showing Year {{ $selectedYear }}
                        </span>
                    @endif
                </div>

                @if($issues->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($issues as $issue)
                            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between overflow-hidden group">
                                <div class="p-6">
                                    {{-- Issue Header --}}
                                    <div class="flex items-start justify-between gap-3 mb-4">
                                        <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-100 text-[#087F82] flex flex-col items-center justify-center shrink-0 group-hover:bg-[#087F82] group-hover:text-white transition-colors">
                                            <span class="text-[10px] uppercase font-bold tracking-wider leading-none">{{ substr($issue->month_name, 0, 3) }}</span>
                                            <span class="text-sm font-black leading-tight">{{ $issue->year }}</span>
                                        </div>

                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600">
                                                {{ $issue->formatted_file_size }}
                                            </span>
                                            <span class="block text-[11px] text-gray-400 mt-1">
                                                {{ number_format($issue->downloads_count) }} downloads
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Title --}}
                                    <h4 class="text-base font-bold text-[#153B70] group-hover:text-[#087F82] transition-colors line-clamp-2 mb-2">
                                        <a href="{{ $issue->public_url }}">
                                            {{ $issue->title }}
                                        </a>
                                    </h4>

                                    {{-- Excerpt --}}
                                    <p class="text-xs text-[#243247]/75 line-clamp-3 leading-relaxed">
                                        {{ strip_tags($issue->description) }}
                                    </p>
                                </div>

                                {{-- Card Footer Actions --}}
                                <div class="px-6 py-4 bg-[#F7F9FC] border-t border-gray-100 flex items-center justify-between gap-2">
                                    <a href="{{ route('current-affairs.preview', $issue) }}"
                                       target="_blank"
                                       class="inline-flex items-center text-xs font-bold text-[#153B70] hover:text-[#087F82] transition-colors py-1.5 px-2 rounded-lg hover:bg-white">
                                        <i data-lucide="eye" class="w-3.5 h-3.5 mr-1 text-[#087F82]"></i> Preview
                                    </a>

                                    <a href="{{ route('current-affairs.download', $issue) }}"
                                       class="inline-flex items-center px-3.5 py-2 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold shadow-2xs transition-colors">
                                        <i data-lucide="download" class="w-3.5 h-3.5 mr-1.5"></i> Download PDF
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($issues->hasPages())
                        <div class="pt-6">
                            {{ $issues->links() }}
                        </div>
                    @endif
                @else
                    {{-- Empty State --}}
                    <div class="bg-white rounded-2xl border border-gray-200/90 p-12 text-center max-w-lg mx-auto">
                        <div class="w-16 h-16 rounded-2xl bg-teal-50 text-[#087F82] flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="calendar-x" class="w-8 h-8"></i>
                        </div>
                        <h4 class="text-lg font-bold text-[#153B70] mb-2">No Current Affairs Issues Found</h4>
                        <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                            We couldn't find any monthly issues matching your search criteria. Please adjust your filters or browse our complete archive.
                        </p>
                        <a href="{{ route('current-affairs.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-[#153B70] text-white text-xs font-bold hover:bg-[#0f2a50] transition-colors">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 mr-1.5"></i> Clear All Filters
                        </a>
                    </div>
                @endif
            </div>

            {{-- Cross-learning MCQs section --}}
            <div class="bg-white rounded-3xl border border-gray-200/90 p-6 sm:p-8 shadow-2xs">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-xl font-black text-[#153B70] tracking-tight">Test Your Current Affairs Knowledge</h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">Practice timed and self-paced MCQs across General Knowledge and Placement subjects.</p>
                    </div>
                    <a href="{{ route('mcq.index') }}" class="inline-flex items-center text-xs font-bold text-[#087F82] hover:underline self-start md:self-auto">
                        Browse All MCQs <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach($relatedCategories as $cat)
                        <a href="{{ route('categories.show', $cat->slug) }}" class="p-4 rounded-xl border border-gray-200/80 hover:border-[#087F82]/40 hover:bg-teal-50/30 transition-all group">
                            <span class="text-xs font-bold text-[#153B70] group-hover:text-[#087F82] block truncate">{{ $cat->name }}</span>
                            <span class="text-[11px] text-gray-400 mt-0.5 block">{{ $cat->questions_count ?? '' }} practice sets</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

