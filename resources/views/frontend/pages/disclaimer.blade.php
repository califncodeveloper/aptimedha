<x-layouts.app>
    <x-slot:title>{{ \App\Services\SeoService::staticPageTitle($page, 'Website Disclaimer') }}</x-slot:title>
    <x-slot:metaDescription>{{ \App\Services\SeoService::staticPageDescription($page, 'Educational purpose, accuracy, no guarantee of results, and independent platform disclaimer for AptiMedha.') }}</x-slot:metaDescription>

    <div class="bg-white border-b border-gray-200/80 py-10 sm:py-14">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <nav class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">
                <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-[#087F82]">Disclaimer</span>
            </nav>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-[#153B70] tracking-tight">{{ $page->title ?? 'Website Disclaimer' }}</h1>
            <p class="mt-2 text-xs sm:text-sm text-gray-500">Effective Date: October 26, 2026 | Last Updated: {{ $page?->updated_at?->format('F d, Y') ?? 'October 26, 2026' }}</p>
        </div>
    </div>

    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-10 space-y-8 text-sm sm:text-base text-gray-700 leading-relaxed">
            @if(isset($page) && !empty($page->content))
                <div class="prose max-w-none text-[#243247] prose-headings:text-[#153B70] prose-headings:font-bold prose-h3:text-xl prose-a:text-[#087F82] space-y-4">
                    {!! $page->content !!}
                </div>
            @else
                <section class="space-y-3">
                    <h2 class="text-xl font-bold text-[#153B70]">1. Educational Purpose</h2>
                    <p>AptiMedha provides questions, explanations, articles, sample answers, resume guidance and related resources for educational and practice purposes.</p>
                    <p>The content is intended to support independent learning. It does not replace official recruitment notices, examination instructions, prescribed syllabi or professional advice.</p>
                </section>

                <section class="space-y-3">
                    <h2 class="text-xl font-bold text-[#153B70]">2. Accuracy and Updates</h2>
                    <p>We aim to make our content accurate and useful. However, errors, omissions and outdated information may occur. Answers may depend on assumptions, software versions or the context stated in a question.</p>
                    <p>Please verify important information with the relevant employer, institution, examination authority or official documentation before acting on it.</p>
                </section>

                <section class="space-y-3">
                    <h2 class="text-xl font-bold text-[#153B70]">3. No Guarantee of Results</h2>
                    <p>Using AptiMedha does not guarantee a particular score, examination result, interview call, placement, salary or job offer. Outcomes depend on factors such as individual preparation, eligibility, assessment performance and employer requirements.</p>
                </section>

                <section class="space-y-3">
                    <h2 class="text-xl font-bold text-[#153B70]">4. Independent Platform</h2>
                    <p>AptiMedha is not affiliated with or endorsed by any employer, university, examination authority or other organisation unless expressly stated.</p>
                </section>
            @endif
        </div>
    </div>
</x-layouts.app>
