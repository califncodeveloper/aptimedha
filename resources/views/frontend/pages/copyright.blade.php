<x-layouts.app>
    <x-slot:title>Copyright Policy - AptiMedha</x-slot:title>

    <div class="bg-white border-b border-gray-200/80 py-10 sm:py-14">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">
                <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-[#087F82]">Copyright Policy</span>
            </nav>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-[#153B70] tracking-tight">Copyright Notice & Policy</h1>
            <p class="mt-2 text-xs sm:text-sm text-gray-500">Effective Date: October 26, 2026 | Last Updated: October 26, 2026</p>
        </div>
    </div>

    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-10 space-y-8 text-sm sm:text-base text-gray-700 leading-relaxed">
            
            <section class="space-y-3">
                <h2 class="text-xl font-bold text-[#153B70]">1. Ownership of Content</h2>
                <p>
                    Unless otherwise stated, copyright in the original content published on AptiMedha.com—including written explanations, article text, site layouts, original practice sets, and illustrated guides—belongs to AptiMedha.
                </p>
                <p>
                    Third-party company names, examination titles, and trademarks referenced on the site belong to their respective owners. Their inclusion is solely for candidate preparation and identification context under fair dealing provisions.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-bold text-[#153B70]">2. Limited License for Individual Learners</h2>
                <p>
                    You are granted a revocable, non-exclusive, non-transferable license to access, view, and solve questions on AptiMedha for your own individual educational use.
                </p>
                <p>You may not:</p>
                <ul class="list-disc pl-5 space-y-1.5 text-sm">
                    <li>Republish AptiMedha question banks or step-by-step solutions on another website, mobile app, or commercial publication without written authorization.</li>
                    <li>Remove copyright notices, logos, or proprietary watermarks from downloadable materials.</li>
                    <li>Sell, rent, or bundle AptiMedha practice materials into paid courses.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-bold text-[#153B70]">3. Reporting a Copyright Concern</h2>
                <p>
                    If you believe any content on AptiMedha infringes your copyright or intellectual property rights, please send an inquiry to <a href="mailto:copyright@aptimedha.com" class="text-[#087F82] font-semibold underline">copyright@aptimedha.com</a> or use our <a href="{{ route('contact') }}" class="text-[#087F82] font-semibold underline">Contact Form</a>.
                </p>
                <p class="text-sm text-gray-500">
                    Please provide the exact page URL, a description of the copyrighted work claimed to be infringed, and your contact details. We promptly review all legitimate claims.
                </p>
            </section>

        </div>
    </div>
</x-layouts.app>

