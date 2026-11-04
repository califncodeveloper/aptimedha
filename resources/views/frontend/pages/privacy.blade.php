<x-layouts.app>
    <x-slot:title>{{ $page->meta_title ?? ($page->title ?? 'Privacy Policy - AptiMedha') }}</x-slot:title>
    @if(!empty($page?->meta_description))
        <x-slot:metaDescription>{{ $page->meta_description }}</x-slot:metaDescription>
    @endif

    <div class="bg-white border-b border-gray-200/80 py-10 sm:py-14">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">
                <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-[#087F82]">Privacy Policy</span>
            </nav>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-[#153B70] tracking-tight">{{ $page->title ?? 'Privacy Policy' }}</h1>
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
                    <h2 class="text-xl font-bold text-[#153B70]">1. Information We Collect</h2>
                    <p>When you contact us, we collect your name, email address, and message. We do not sell enquiry details to third parties.</p>
                </section>

                <section class="space-y-3">
                    <h2 class="text-xl font-bold text-[#153B70]">2. Cookies and Security</h2>
                    <p>We use session cookies necessary for authenticated accounts and CSRF security.</p>
                </section>
            @endif
        </div>
    </div>
</x-layouts.app>
