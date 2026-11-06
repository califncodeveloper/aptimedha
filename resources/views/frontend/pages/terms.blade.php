<x-layouts.app>
    <x-slot:title>{{ \App\Services\SeoService::staticPageTitle($page, 'Terms and Conditions') }}</x-slot:title>
    <x-slot:metaDescription>{{ \App\Services\SeoService::staticPageDescription($page, 'Terms governing access to and use of AptiMedha, educational resources, intellectual property, and acceptable user conduct.') }}</x-slot:metaDescription>
    <x-slot:schema>{!! \App\Services\SchemaService::toJsonLd(\App\Services\SchemaService::forStaticPage($page, 'Terms and Conditions', route('terms'))) !!}</x-slot:schema>

    <div class="bg-white border-b border-gray-200/80 py-10 sm:py-14">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <nav class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">
                <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-[#087F82]">Terms & Conditions</span>
            </nav>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-[#153B70] tracking-tight">{{ $page->title ?? 'Terms and Conditions' }}</h1>
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
                    <h2 class="text-xl font-bold text-[#153B70]">1. Acceptance of Terms</h2>
                    <p>By accessing or using AptiMedha, you agree to these Terms and Conditions. If you do not agree, please do not use the website.</p>
                </section>

                <section class="space-y-3">
                    <h2 class="text-xl font-bold text-[#153B70]">2. Permitted Use</h2>
                    <p>AptiMedha is provided for personal, non-commercial educational practice and preparation. You may view content, practice questions, and use approved downloadable templates for your own career preparation.</p>
                </section>

                <section class="space-y-3">
                    <h2 class="text-xl font-bold text-[#153B70]">3. Intellectual Property</h2>
                    <p>Original editorial materials, explanations, code samples, design layouts, and trademarks appearing on AptiMedha belong to AptiMedha and are protected by applicable intellectual property laws.</p>
                </section>
            @endif
        </div>
    </div>
</x-layouts.app>
