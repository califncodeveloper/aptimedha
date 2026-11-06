<x-layouts.app>
    <x-slot:title>{{ \App\Services\SeoService::staticPageTitle($page, 'About Us') }}</x-slot:title>
    <x-slot:metaDescription>{{ \App\Services\SeoService::staticPageDescription($page, 'Learn about AptiMedha, our mission, vision, and comprehensive placement preparation resources.') }}</x-slot:metaDescription>

    {{-- Hero Section with Background Grid Overlay --}}
    <div class="bg-gradient-to-br from-[#153B70] via-[#1a4a8a] to-[#087F82] text-white py-16 sm:py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-grid-white opacity-15 pointer-events-none"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-white/10 text-teal-200 border border-white/20 mb-4 shadow-2xs backdrop-blur-md">
                    <i data-lucide="info" class="w-4 h-4 mr-2"></i> About AptiMedha
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                    Practise Smart. Get Placement Ready.
                </h1>
                <p class="mt-4 text-base sm:text-lg text-white/85 leading-relaxed">
                    AptiMedha brings quantitative practice, reasoning development, technical fundamentals, and career preparation together in one place, helping learners prepare with clarity and confidence.
                </p>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 space-y-16">
        
        {{-- Section 1: Name Origin & Purpose --}}
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-4">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#153B70]">The Story Behind Our Name</h2>
                <p class="text-sm sm:text-base text-gray-700 leading-relaxed">
                    Our name combines <strong>"Apti"</strong>, inspired by aptitude, and <strong>"Medha"</strong>, a Sanskrit word associated with intelligence, intellect, and profound understanding.
                </p>
                <p class="text-sm sm:text-base text-gray-700 leading-relaxed">
                    It reflects our foundational belief that effective preparation involves both solving problems and understanding the underlying concepts behind them. We help students, graduates, and job seekers strengthen their knowledge, develop problem-solving skills, and prepare thoroughly for campus placement assessments and job interviews.
                </p>
            </div>

            <div class="lg:col-span-5 bg-white p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">
                <div class="border-l-4 border-[#087F82] pl-4">
                    <h3 class="text-lg font-bold text-[#153B70]">Our Mission</h3>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1 leading-relaxed">
                        To make placement preparation accessible, understandable and purposeful. We aim to support learners through organised practice questions, clear explanations, and practical guidance.
                    </p>
                </div>
                <div class="border-l-4 border-[#153B70] pl-4">
                    <h3 class="text-lg font-bold text-[#153B70]">Our Vision</h3>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1 leading-relaxed">
                        To become a trusted learning companion that helps students turn preparation into opportunity, building abilities at their own pace.
                    </p>
                </div>
            </div>
        </section>

        {{-- Section 2: Our Approach (5 Core Tenets) --}}
        <section class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-8 sm:p-12">
            <div class="max-w-2xl mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-[#087F82]">Educational Philosophy</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#153B70] mt-1">Our Learning Approach</h2>
                <p class="text-sm text-gray-600 mt-2">How we structure every question, explanation, and career guide.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-[#087F82] flex items-center justify-center font-bold">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                    </div>
                    <h4 class="text-base font-bold text-[#153B70]">Build understanding through practice</h4>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                        A question should offer more than a score. Our approach pairs practice with explanations that help learners understand methods, recognise mistakes, and apply principles to new problems.
                    </p>
                </div>

                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#153B70] flex items-center justify-center font-bold">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </div>
                    <h4 class="text-base font-bold text-[#153B70]">Give learning a clear structure</h4>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                        We organise resources by subject and leaf topic so learners start with fundamentals, focus on areas needing attention, and progress systematically.
                    </p>
                </div>

                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                        <i data-lucide="briefcase" class="w-5 h-5"></i>
                    </div>
                    <h4 class="text-base font-bold text-[#153B70]">Connect knowledge with career skills</h4>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                        Placement preparation extends beyond MCQs. Our resources address communicating ideas, explaining projects, participating in group discussions, and professional resumes.
                    </p>
                </div>

                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold">
                        <i data-lucide="compass" class="w-5 h-5"></i>
                    </div>
                    <h4 class="text-base font-bold text-[#153B70]">Encourage independent thinking</h4>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                        Sample answers and templates provide guidance. We encourage learners to adapt them to their authentic experiences and develop their unique voice.
                    </p>
                </div>

                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                    </div>
                    <h4 class="text-base font-bold text-[#153B70]">Improve through feedback</h4>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                        Useful educational content requires continuous review. We welcome corrections and suggestions from learners and educators.
                    </p>
                </div>

            </div>
        </section>

        {{-- Section 3: What We Cover --}}
        <section class="bg-[#F7F9FC] rounded-2xl p-8 sm:p-12 border border-gray-200">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-[#153B70] mb-6">What We Cover</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs sm:text-sm text-gray-700 font-medium">
                <div class="p-4 bg-white rounded-xl border border-gray-200/80 flex items-center gap-3">
                    <i data-lucide="calculator" class="w-5 h-5 text-[#087F82]"></i> Arithmetic Aptitude & DI
                </div>
                <div class="p-4 bg-white rounded-xl border border-gray-200/80 flex items-center gap-3">
                    <i data-lucide="puzzle" class="w-5 h-5 text-[#087F82]"></i> Logical & Verbal Reasoning
                </div>
                <div class="p-4 bg-white rounded-xl border border-gray-200/80 flex items-center gap-3">
                    <i data-lucide="file-text" class="w-5 h-5 text-[#087F82]"></i> English & Verbal Ability
                </div>
                <div class="p-4 bg-white rounded-xl border border-gray-200/80 flex items-center gap-3">
                    <i data-lucide="globe" class="w-5 h-5 text-[#087F82]"></i> General Knowledge & Affairs
                </div>
                <div class="p-4 bg-white rounded-xl border border-gray-200/80 flex items-center gap-3">
                    <i data-lucide="code" class="w-5 h-5 text-[#087F82]"></i> Programming (C, Java, Python)
                </div>
                <div class="p-4 bg-white rounded-xl border border-gray-200/80 flex items-center gap-3">
                    <i data-lucide="cpu" class="w-5 h-5 text-[#087F82]"></i> Engineering Core Disciplines
                </div>
                <div class="p-4 bg-white rounded-xl border border-gray-200/80 flex items-center gap-3">
                    <i data-lucide="users" class="w-5 h-5 text-[#087F82]"></i> HR & Technical Interviews
                </div>
                <div class="p-4 bg-white rounded-xl border border-gray-200/80 flex items-center gap-3">
                    <i data-lucide="file-check" class="w-5 h-5 text-[#087F82]"></i> Resume Guides & GD Prep
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200 text-xs text-gray-500 leading-relaxed">
                <em>Disclaimer: AptiMedha is an independent educational platform. We are not affiliated with employers or examination authorities unless expressly stated. Our resources support preparation and do not guarantee examination results, selection, or employment.</em>
            </div>
        </section>

    </div>
</x-layouts.app>
