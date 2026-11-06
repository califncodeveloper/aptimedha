<x-layouts.app>
    <x-slot:title>Placement Preparation Roadmap, Resume Guides & Interview Tips | AptiMedha</x-slot:title>
    <x-slot:metaDescription>Complete placement preparation guides for campus placements, fresh graduates, and professionals. Master aptitude tests, resumes, technical rounds, and HR interviews.</x-slot:metaDescription>
    <x-slot:schema>{!! \App\Services\SchemaService::toJsonLd(\App\Services\SchemaService::forPlacementIndex()) !!}</x-slot:schema>

    {{-- Hero Section (Premium Gradient + White Grid Overlay) --}}
    <div class="bg-gradient-to-br from-[#153B70] via-[#1a4a8a] to-[#087F82] text-white py-16 sm:py-20 relative overflow-hidden">
        <div data-motion-parallax data-motion-parallax-speed="0.1" class="absolute inset-0 bg-grid-white opacity-15 pointer-events-none"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                <span data-motion="fade-down" class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-white/10 text-teal-200 border border-white/20 mb-4 shadow-2xs backdrop-blur-md">
                    <i data-lucide="graduation-cap" class="w-4 h-4 mr-2"></i> Campus to Corporate Complete Guide
                </span>
                <h1 data-motion-text="words" class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                    Comprehensive Placement Preparation
                </h1>
                <p data-motion="fade-up" data-motion-delay="0.2" class="mt-4 text-base sm:text-lg text-white/85 leading-relaxed">
                    From resume building and aptitude rounds to technical interviews, group discussions, and HR questions — everything you need to crack your dream placement.
                </p>
                <div data-motion="fade-up" data-motion-delay="0.3" class="mt-8 flex flex-col sm:flex-row flex-wrap gap-3.5">
                    <a href="#placement-guides" data-motion-spring class="w-full sm:w-auto text-center px-6 py-3 rounded-xl bg-white text-[#153B70] font-bold text-sm shadow-md hover:bg-gray-100 transition-all hover:-translate-y-0.5">
                        Browse Guides ({{ $guides->count() }}) &darr;
                    </a>
                    <a href="#placement-roadmap" data-motion-spring class="w-full sm:w-auto text-center px-6 py-3 rounded-xl bg-[#087F82] text-white font-bold text-sm border border-white/20 hover:bg-[#066668] transition-all hover:-translate-y-0.5">
                        4-Stage Roadmap
                    </a>
                    <a href="#resume-guide" data-motion-spring class="w-full sm:w-auto text-center px-6 py-3 rounded-xl bg-white/15 text-white font-bold text-sm hover:bg-white/25 transition-all">
                        Resume Preparation
                    </a>
                    <a href="#topics-grid" data-motion-spring class="w-full sm:w-auto text-center px-6 py-3 rounded-xl bg-white/10 text-white font-bold text-sm hover:bg-white/20 transition-all border border-white/10">
                        16 Placement Topics
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 space-y-16">

        {{-- Dynamic Placement Guides CMS Section --}}
        <section id="placement-guides" class="scroll-mt-24 space-y-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#087F82]">Database Curriculum</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#153B70] mt-1">Featured Placement Guides</h2>
                    <p class="text-sm text-gray-600 mt-1">In-depth preparation articles written and reviewed by our career mentors.</p>
                </div>

                {{-- Category Filter Pills --}}
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('placement.index') }}#placement-guides"
                       @class([
                           'px-3.5 py-1.5 rounded-full text-xs font-bold transition-all shadow-2xs',
                           'bg-[#153B70] text-white' => empty($selectedCategory),
                           'bg-white text-gray-600 border border-gray-200 hover:border-[#153B70]' => !empty($selectedCategory),
                       ])>
                        All Guides ({{ $guides->count() }})
                    </a>
                    @foreach($categories as $catKey => $catLabel)
                        <a href="{{ route('placement.index', ['category' => $catKey]) }}#placement-guides"
                           @class([
                               'px-3.5 py-1.5 rounded-full text-xs font-bold transition-all shadow-2xs',
                               'bg-[#087F82] text-white' => $selectedCategory === $catKey,
                               'bg-white text-gray-600 border border-gray-200 hover:border-[#087F82]' => $selectedCategory !== $catKey,
                           ])>
                            {{ $catLabel }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Guides Grid --}}
            @if($guides->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($guides as $guide)
                        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs hover:shadow-md hover:border-[#087F82] transition-all flex flex-col justify-between overflow-hidden group">
                            <div>
                                @if($guide->featured_image)
                                    <div class="h-44 overflow-hidden bg-gray-100">
                                        <img src="{{ $guide->featured_image_url }}" alt="{{ $guide->featured_image_alt ?: $guide->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                @endif
                                <div class="p-6 space-y-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $guide->categoryBadgeClass() }}">
                                            {{ $guide->categoryLabel() }}
                                        </span>
                                        @if($guide->hasDownloadableAsset())
                                            <span class="inline-flex items-center text-[10px] font-bold text-[#087F82] bg-teal-50 px-2 py-0.5 rounded-md border border-teal-200" title="Includes Downloadable Resource">
                                                <i data-lucide="file-down" class="w-3 h-3 mr-1"></i> Asset
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-base sm:text-lg font-bold text-[#153B70] group-hover:text-[#087F82] transition-colors leading-snug line-clamp-2">
                                        <a href="{{ route('placement.show', $guide->slug) }}">
                                            {{ $guide->title }}
                                        </a>
                                    </h3>
                                    @if($guide->excerpt)
                                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed line-clamp-2">
                                            {{ $guide->excerpt }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="p-6 pt-0 border-t border-gray-100 mt-4 flex items-center justify-between">
                                <a href="{{ route('placement.show', $guide->slug) }}" class="text-xs font-bold text-[#087F82] group-hover:underline flex items-center">
                                    Read Guide <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                                </a>
                                @if($guide->hasDownloadableAsset())
                                    <a href="{{ route('placement.download', $guide->slug) }}" class="text-xs font-bold text-gray-500 hover:text-[#153B70] flex items-center" title="Direct download: {{ $guide->downloadable_asset_name }}">
                                        <i data-lucide="download" class="w-3.5 h-3.5 mr-1"></i> Download
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center text-gray-500">
                    <i data-lucide="book-open" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="font-bold text-[#153B70]">No guides found in this category.</p>
                    <p class="text-xs text-gray-400 mt-1">Please check back soon as our mentors publish new placement content.</p>
                    <a href="{{ route('placement.index') }}#placement-guides" class="inline-flex items-center px-4 py-2 mt-4 text-xs font-bold rounded-xl text-white bg-[#087F82]">
                        View All Guides
                    </a>
                </div>
            @endif
        </section>

        {{-- Section 1: 4-Stage Placement Roadmap with Scroll Parallax Card Effect --}}
        <section id="placement-roadmap" class="scroll-mt-24">
            <div data-motion="fade-up" class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#153B70]">4-Stage Placement Preparation Roadmap</h2>
                    <p class="text-sm sm:text-base text-gray-600 mt-1">A structured step-by-step path from your pre-final year to corporate onboarding.</p>
                </div>
                @if($roadmapGuide)
                    <a href="{{ route('placement.show', $roadmapGuide->slug) }}" class="inline-flex items-center text-xs font-bold text-[#087F82] hover:underline shrink-0">
                        Read Full Roadmap Guide &rarr;
                    </a>
                @endif
            </div>

            <div data-motion-stagger data-motion-stagger-delay="0.08" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- Stage 1 --}}
                <div data-motion-item
                     data-motion-parallax-card
                     class="bg-white rounded-2xl p-6 border border-gray-200 shadow-xs relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#153B70] font-black text-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        01
                    </div>
                    <h3 class="text-lg font-bold text-[#153B70] mb-2">Profile & Resume</h3>
                    <p class="text-xs sm:text-sm text-gray-500 leading-relaxed mb-4">
                        Craft an ATS-compliant one-page resume, optimize your LinkedIn profile, and prepare GitHub/portfolio project repositories.
                    </p>
                    <ul class="text-xs text-gray-600 space-y-1.5 border-t border-gray-100 pt-3">
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-[#087F82]"></i> One-Page Resume Template</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-[#087F82]"></i> Project Descriptions (STAR)</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-[#087F82]"></i> LinkedIn & Portfolio Links</li>
                    </ul>
                </div>

                {{-- Stage 2 --}}
                <div data-motion-item
                     data-motion-parallax-card
                     class="bg-white rounded-2xl p-6 border border-gray-200 shadow-xs relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-[#087F82] font-black text-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        02
                    </div>
                    <h3 class="text-lg font-bold text-[#153B70] mb-2">Aptitude & Online Test</h3>
                    <p class="text-xs sm:text-sm text-gray-500 leading-relaxed mb-4">
                        Clear the initial screening barrier with speed and accuracy across Quantitative Aptitude, Logical Reasoning, and Verbal Ability.
                    </p>
                    <ul class="text-xs text-gray-600 space-y-1.5 border-t border-gray-100 pt-3">
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-[#087F82]"></i> Arithmetic & Speed Math</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-[#087F82]"></i> Puzzles & Data Interpretation</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-[#087F82]"></i> Company Assessment Patterns</li>
                    </ul>
                </div>

                {{-- Stage 3 --}}
                <div data-motion-item
                     data-motion-parallax-card
                     class="bg-white rounded-2xl p-6 border border-gray-200 shadow-xs relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 font-black text-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        03
                    </div>
                    <h3 class="text-lg font-bold text-[#153B70] mb-2">Technical & Coding</h3>
                    <p class="text-xs sm:text-sm text-gray-500 leading-relaxed mb-4">
                        Master Data Structures, Algorithms, Core CS (DBMS, OS, CN), and explain your academic projects with deep technical confidence.
                    </p>
                    <ul class="text-xs text-gray-600 space-y-1.5 border-t border-gray-100 pt-3">
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-[#087F82]"></i> DSA & Live Coding MCQs</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-[#087F82]"></i> DBMS, SQL Queries & Indexing</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-[#087F82]"></i> Core Engineering Subjects</li>
                    </ul>
                </div>

                {{-- Stage 4 --}}
                <div data-motion-item
                     data-motion-parallax-card
                     class="bg-white rounded-2xl p-6 border border-gray-200 shadow-xs relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 font-black text-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        04
                    </div>
                    <h3 class="text-lg font-bold text-[#153B70] mb-2">GD & HR Round</h3>
                    <p class="text-xs sm:text-sm text-gray-500 leading-relaxed mb-4">
                        Demonstrate leadership, culture fit, behavioral readiness, salary discussion clarity, and professional communication etiquette.
                    </p>
                    <ul class="text-xs text-gray-600 space-y-1.5 border-t border-gray-100 pt-3">
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-[#087F82]"></i> Top 25 HR Interview Q&As</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-[#087F82]"></i> Group Discussion Strategies</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-[#087F82]"></i> Offer Negotiation & Ethics</li>
                    </ul>
                </div>

            </div>
        </section>

        {{-- Section 2: Resume Preparation Illustrated Guide --}}
        <section data-motion="scale-up" id="resume-guide" class="scroll-mt-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center bg-gradient-to-r from-teal-50 to-blue-50/50 rounded-2xl p-8 sm:p-12 border border-teal-100">
                <div class="lg:col-span-7 space-y-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#087F82]">Illustrated Guide</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#153B70]">AptiMedha Resume Preparation Guide</h2>
                    <p class="text-sm text-[#243247]/80 leading-relaxed">
                        A modern recruiter spends fewer than 10 seconds on a preliminary resume screen. Learn how to format education, project bullet points using the STAR method (Situation, Task, Action, Result), technical skill clusters, and internship achievements.
                    </p>
                    <div class="pt-2 flex flex-col sm:flex-row flex-wrap gap-3">
                        @if($resumeGuide && $resumeGuide->hasDownloadableAsset())
                            <a href="{{ route('placement.download', $resumeGuide->slug) }}" data-motion-spring class="inline-flex items-center justify-center w-full sm:w-auto px-5 py-2.5 bg-[#087F82] text-white font-bold text-xs rounded-xl hover:bg-[#066668] transition-colors shadow-xs">
                                <i data-lucide="download" class="w-4 h-4 mr-1.5 shrink-0"></i> Download Resume Template
                            </a>
                        @endif
                        @if($resumeGuide)
                            <a href="{{ route('placement.show', $resumeGuide->slug) }}" data-motion-spring class="inline-flex items-center justify-center w-full sm:w-auto px-5 py-2.5 bg-white text-[#153B70] border border-gray-300 font-bold text-xs rounded-xl hover:bg-gray-50 transition-colors">
                                Read Resume Guide Article &rarr;
                            </a>
                        @endif
                    </div>
                </div>

                <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-gray-200/80 shadow-md">
                    <div class="border-b border-gray-100 pb-3 mb-3">
                        <h4 class="text-sm font-bold text-[#153B70]">Recruiter's Resume Checklist</h4>
                    </div>
                    <ul class="text-xs text-gray-600 space-y-2.5">
                        <li class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-[#087F82] flex-shrink-0 mt-0.5"></i>
                            <span>Single page layout for undergraduate applicants</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-[#087F82] flex-shrink-0 mt-0.5"></i>
                            <span>Action verbs: "Architected", "Engineered", "Optimized"</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-[#087F82] flex-shrink-0 mt-0.5"></i>
                            <span>Quantifiable metrics: "% faster", "X active users"</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-[#087F82] flex-shrink-0 mt-0.5"></i>
                            <span>Clear links to working GitHub repositories</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        {{-- Section 3: HR Interview Questions & Model Answers --}}
        <section data-motion="fade-up" id="hr-interviews" class="scroll-mt-24 bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-10" x-data="{ openQ: 1 }">
            <div class="max-w-2xl mb-8">
                <span class="text-xs font-bold uppercase tracking-wider text-[#087F82]">Behavioral Preparation</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#153B70] mt-1">Frequently Asked HR Interview Questions</h2>
                <p class="text-sm text-gray-600 mt-2">
                    Review sample questions with recommended response frameworks. Adapt each answer authentically to your own college background.
                </p>
            </div>

            <div class="space-y-4">
                {{-- Question 1 --}}
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="openQ = (openQ === 1 ? null : 1)" class="w-full flex items-center justify-between p-4 sm:p-5 text-left bg-gray-50/70 hover:bg-gray-100/70 transition-colors">
                        <span class="text-base font-bold text-[#153B70]">1. "Tell me about yourself and your background."</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': openQ === 1 }"></i>
                    </button>
                    <div x-show="openQ === 1" x-transition class="p-5 text-sm text-gray-700 leading-relaxed border-t border-gray-200 bg-white">
                        <p class="font-semibold text-[#087F82] mb-1">Recommended Framework (Present - Past - Future):</p>
                        <p class="mb-3">Start with your current qualification and primary technical interest, mention key project experiences or internships, and conclude with why you are eager to contribute to this company.</p>
                        <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-[#087F82] text-xs sm:text-sm">
                            <em>"I am completing my degree in Computer Science, where I developed a keen interest in software architecture and algorithms. During my major project, my team built a real-time web portal that reduced booking latency by 35%. I am excited to apply these problem-solving skills in a forward-thinking engineering culture like yours."</em>
                        </div>
                    </div>
                </div>

                {{-- Question 2 --}}
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="openQ = (openQ === 2 ? null : 2)" class="w-full flex items-center justify-between p-4 sm:p-5 text-left bg-gray-50/70 hover:bg-gray-100/70 transition-colors">
                        <span class="text-base font-bold text-[#153B70]">2. "What are your greatest strengths and areas for improvement?"</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': openQ === 2 }"></i>
                    </button>
                    <div x-show="openQ === 2" x-transition class="p-5 text-sm text-gray-700 leading-relaxed border-t border-gray-200 bg-white" x-cloak>
                        <p class="font-semibold text-[#087F82] mb-1">Recommended Framework:</p>
                        <p class="mb-3">Ground your strength with a concrete example. For weakness, name an honest professional attribute you are actively taking steps to improve.</p>
                        <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-[#087F82] text-xs sm:text-sm">
                            <em>"My strength is analytical discipline — when debugging code or solving complex aptitude logic, I break problems down systematically. An area I am improving is public speaking under impromptu pressure; to overcome this, I joined our college debate society and actively present sprint demos."</em>
                        </div>
                    </div>
                </div>

                {{-- Question 3 --}}
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="openQ = (openQ === 3 ? null : 3)" class="w-full flex items-center justify-between p-4 sm:p-5 text-left bg-gray-50/70 hover:bg-gray-100/70 transition-colors">
                        <span class="text-base font-bold text-[#153B70]">3. "Why do you want to join our organization?"</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': openQ === 3 }"></i>
                    </button>
                    <div x-show="openQ === 3" x-transition class="p-5 text-sm text-gray-700 leading-relaxed border-t border-gray-200 bg-white" x-cloak>
                        <p class="font-semibold text-[#087F82] mb-1">Recommended Framework:</p>
                        <p class="mb-3">Demonstrate that you have researched the company's recent work, training programs, and values rather than reciting generic praise.</p>
                        <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-[#087F82] text-xs sm:text-sm">
                            <em>"I have closely followed your firm's contributions to digital transformation across banking and enterprise cloud services. Your structured graduate training program and commitment to mentorship make this an ideal environment for building strong engineering fundamentals."</em>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section 4: Complete 28 Placement Preparation Topics (From Client Topics Document) --}}
        <section id="topics-grid" class="scroll-mt-24 space-y-8" x-data="{ activeTab: 'core' }">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#087F82]">Client-Approved Curriculum</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#153B70] mt-1">28 Placement Preparation Topics</h2>
                    <p class="text-sm text-gray-600 mt-1">Every module from the official AptiMedha placement syllabus, organized into core stages and specialized career guidance.</p>
                </div>

                {{-- Tab Switcher --}}
                <div class="flex items-center space-x-2 bg-gray-100 p-1 rounded-xl shrink-0">
                    <button @click="activeTab = 'core'" :class="activeTab === 'core' ? 'bg-white text-[#153B70] shadow-xs' : 'text-gray-500 hover:text-gray-800'" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all">
                        16 Core Curriculum Topics
                    </button>
                    <button @click="activeTab = 'advanced'" :class="activeTab === 'advanced' ? 'bg-white text-[#153B70] shadow-xs' : 'text-gray-500 hover:text-gray-800'" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all">
                        12 Career & Strategy Modules
                    </button>
                </div>
            </div>

            @php
                $coreTopics = [
                    ['id' => '01', 'title' => 'Placement Preparation Roadmap', 'stage' => 'Planning', 'category' => 'roadmap', 'desc' => 'Multi-stage structured schedule, diagnostics, daily study routines, and milestone targets.'],
                    ['id' => '02', 'title' => 'Placement Eligibility & Recruitment Process', 'stage' => 'Eligibility', 'category' => 'roadmap', 'desc' => 'Academic cut-offs, company shortlisting tiers, screening assessments, and verification.'],
                    ['id' => '03', 'title' => 'Career Selection & Job Search Strategy', 'stage' => 'Strategy', 'category' => 'roadmap', 'desc' => 'Target role evaluation, software vs analytics vs core paths, and job description mapping.'],
                    ['id' => '04', 'title' => 'Resume Preparation & ATS Optimization', 'stage' => 'Documentation', 'category' => 'resume', 'desc' => 'Single-page formatting, STAR bullet points, project highlights, and applicant tracking systems.'],
                    ['id' => '05', 'title' => 'Cover Letters & Job Application Emails', 'stage' => 'Outreach', 'category' => 'resume', 'desc' => 'Professional outreach, role-tailored introductions, subject lines, and follow-up templates.'],
                    ['id' => '06', 'title' => 'LinkedIn Profile & Portfolio Preparation', 'stage' => 'Branding', 'category' => 'resume', 'desc' => 'Headlines, project repository links, certifications showcase, and networking etiquette.'],
                    ['id' => '07', 'title' => 'Aptitude & Speed Assessment Preparation', 'stage' => 'Screening', 'category' => 'aptitude', 'desc' => 'Quantitative shortcuts, logical deduction, verbal comprehension, and time-allocation tactics.'],
                    ['id' => '08', 'title' => 'Technical Interview Preparation', 'stage' => 'Core CS', 'category' => 'technical', 'desc' => 'Core engineering fundamentals, DBMS, OS, Computer Networks, and systems design basics.'],
                    ['id' => '09', 'title' => 'Coding & Live Algorithm Rounds', 'stage' => 'Coding', 'category' => 'technical', 'desc' => 'Data structures, time complexity analysis, recursion, problem-solving, and clean syntax.'],
                    ['id' => '10', 'title' => 'Academic Project & Internship Presentation', 'stage' => 'Projects', 'category' => 'technical', 'desc' => 'Explaining architecture, individual contributions, technical challenges overcome, and demo preparation.'],
                    ['id' => '11', 'title' => 'HR Interview & Behavioral Preparation', 'stage' => 'Behavioral', 'category' => 'interview', 'desc' => 'Competency-based questioning, situational frameworks, strengths/weaknesses, and ethics.'],
                    ['id' => '12', 'title' => 'Group Discussion Preparation', 'stage' => 'Group Dynamics', 'category' => 'interview', 'desc' => 'Initiation, consensus building, counter-arguments, body language, and effective summarization.'],
                    ['id' => '13', 'title' => 'Virtual Interview Preparation', 'stage' => 'Digital', 'category' => 'interview', 'desc' => 'Lighting, audio, background etiquette, eye contact, and presenting slides seamlessly.'],
                    ['id' => '14', 'title' => 'Communication Skills & Professional Etiquette', 'stage' => 'Soft Skills', 'category' => 'interview', 'desc' => 'Clarity, listening skills, active feedback, professional dress code, and corporate conduct.'],
                    ['id' => '15', 'title' => 'Written Communication & Case Assessments', 'stage' => 'Written', 'category' => 'interview', 'desc' => 'Business case study analysis, structured written reports, and professional memo drafting.'],
                    ['id' => '16', 'title' => 'Company-Wise Placement Preparation', 'stage' => 'Targeting', 'category' => 'company', 'desc' => 'Pattern analysis for IT services, product companies, consulting, and core engineering firms.']
                ];

                $advancedTopics = [
                    ['id' => '17', 'title' => 'Role-Wise Placement Preparation', 'stage' => 'Specialization', 'category' => 'roadmap', 'desc' => 'Targeted prep for Software Engineer, QA, Data Analyst, Cloud Support, and Core Engineering.'],
                    ['id' => '18', 'title' => 'Placement Papers and Mock Tests', 'stage' => 'Practice', 'category' => 'aptitude', 'desc' => 'Solving previous test patterns under timed exam conditions with accuracy benchmarking.'],
                    ['id' => '19', 'title' => 'Mock Interview Practice', 'stage' => 'Simulation', 'category' => 'interview', 'desc' => 'Peer-to-peer rehearsals, recording answers for self-review, and eliminating verbal fillers.'],
                    ['id' => '20', 'title' => 'Time Management & Assessment Strategies', 'stage' => 'Test Tactics', 'category' => 'aptitude', 'desc' => 'The 60-second rule, section order tactics, skipping traps, and negative marking control.'],
                    ['id' => '21', 'title' => 'Performance Review & Revision Planning', 'stage' => 'Optimization', 'category' => 'roadmap', 'desc' => 'Maintaining mistake notebooks, weekly revision cycles, and score plateau breakthroughs.'],
                    ['id' => '22', 'title' => 'Interview-Day & Document Checklist', 'stage' => 'Logistics', 'category' => 'general', 'desc' => 'Hard copies, certificates, portfolio backups, dress code, and arriving with mental composure.'],
                    ['id' => '23', 'title' => 'Interview Follow-Up & Thank-You Emails', 'stage' => 'Post-Interview', 'category' => 'interview', 'desc' => 'Writing polite post-interview notes, expressing continued interest, and timing follow-ups.'],
                    ['id' => '24', 'title' => 'Understanding Job Offers & Salary Discussions', 'stage' => 'Negotiation', 'category' => 'general', 'desc' => 'CTC vs in-hand breakdown, PF/gratuity, variable bonuses, service bonds, and offer ethics.'],
                    ['id' => '25', 'title' => 'Career Gaps, Backlogs & Career Changes', 'stage' => 'Career Bridge', 'category' => 'general', 'desc' => 'Addressing academic gaps honestly, explaining backlogs cleared, and pivoting skillsets.'],
                    ['id' => '26', 'title' => 'Handling Rejection & Improving Preparation', 'stage' => 'Resilience', 'category' => 'general', 'desc' => 'Analyzing feedback constructively, emotional resilience, and refining the next application.'],
                    ['id' => '27', 'title' => 'Job Search Safety & Recruitment Scam Awareness', 'stage' => 'Safety', 'category' => 'general', 'desc' => 'Recognizing fake offer letters, never paying registration fees, and verified portals.'],
                    ['id' => '28', 'title' => 'From Campus to Corporate – Complete Guide', 'stage' => 'Onboarding', 'category' => 'general', 'desc' => 'First 90 days on the job, professional accountability, manager communication, and growth.']
                ];
            @endphp

            {{-- Tab 1: 16 Core Topics --}}
            <div x-show="activeTab === 'core'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($coreTopics as $topic)
                    @php
                        $matchedGuide = $guides->first(fn($g) => str_contains(strtolower($g->title), strtolower(substr($topic['title'], 0, 15))));
                    @endphp
                    <div class="bg-white rounded-2xl p-5 border border-gray-200/90 hover:border-[#087F82] hover:shadow-xs transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-black text-gray-400 font-mono">{{ $topic['id'] }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-[#087F82]">{{ $topic['stage'] }}</span>
                            </div>
                            <h4 class="text-sm font-bold text-[#153B70] leading-snug mb-1.5">
                                {{ $topic['title'] }}
                            </h4>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                {{ $topic['desc'] }}
                            </p>
                        </div>
                        <div class="pt-3 border-t border-gray-100 mt-3">
                            @if($matchedGuide)
                                <a href="{{ route('placement.show', $matchedGuide->slug) }}" class="text-xs font-bold text-[#087F82] hover:underline flex items-center">
                                    Read Guide <i data-lucide="arrow-right" class="w-3 h-3 ml-1"></i>
                                </a>
                            @else
                                <a href="{{ route('placement.index', ['category' => $topic['category']]) }}#placement-guides" class="text-xs font-bold text-gray-500 hover:text-[#087F82] flex items-center">
                                    View Related <i data-lucide="chevron-right" class="w-3 h-3 ml-0.5"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Tab 2: 12 Advanced & Strategic Modules --}}
            <div x-show="activeTab === 'advanced'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($advancedTopics as $topic)
                    @php
                        $matchedGuide = $guides->first(fn($g) => str_contains(strtolower($g->title), strtolower(substr($topic['title'], 0, 15))));
                    @endphp
                    <div class="bg-white rounded-2xl p-5 border border-gray-200/90 hover:border-[#153B70] hover:shadow-xs transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-black text-gray-400 font-mono">{{ $topic['id'] }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-[#153B70]">{{ $topic['stage'] }}</span>
                            </div>
                            <h4 class="text-sm font-bold text-[#153B70] leading-snug mb-1.5">
                                {{ $topic['title'] }}
                            </h4>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                {{ $topic['desc'] }}
                            </p>
                        </div>
                        <div class="pt-3 border-t border-gray-100 mt-3">
                            @if($matchedGuide)
                                <a href="{{ route('placement.show', $matchedGuide->slug) }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                                    Read Guide <i data-lucide="arrow-right" class="w-3 h-3 ml-1"></i>
                                </a>
                            @else
                                <a href="{{ route('placement.index', ['category' => $topic['category']]) }}#placement-guides" class="text-xs font-bold text-gray-500 hover:text-[#153B70] flex items-center">
                                    View Related <i data-lucide="chevron-right" class="w-3 h-3 ml-0.5"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Section 5: Top Company Practice Sets & MCQ Integration --}}
        <section data-motion="fade-up" class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-10">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#087F82]">Assessment Practice</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#153B70] mt-1">Placement Assessment Question Banks</h2>
                    <p class="text-sm text-gray-600 mt-1">Practice verified multiple-choice questions categorized by curriculum domains.</p>
                </div>
                <a href="{{ route('categories.index') }}" class="text-xs font-bold text-[#087F82] hover:underline flex items-center">
                    Browse All MCQ Categories &rarr;
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @if(isset($mcqCategories) && $mcqCategories->isNotEmpty())
                    @foreach($mcqCategories as $cat)
                        <a href="{{ route('categories.show', $cat->slug) }}" class="p-4 rounded-xl border border-gray-200 text-center hover:border-[#087F82] hover:bg-teal-50/20 group transition-all">
                            <div class="w-10 h-10 rounded-lg bg-gray-50 group-hover:bg-[#153B70] text-[#153B70] group-hover:text-white flex items-center justify-center mx-auto mb-2 transition-colors">
                                <i data-lucide="book-open" class="w-5 h-5"></i>
                            </div>
                            <p class="text-xs font-bold text-[#243247] group-hover:text-[#087F82] line-clamp-1">{{ $cat->name }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">MCQ Practice &rarr;</p>
                        </a>
                    @endforeach
                @else
                    @php
                        $practiceAreas = [
                            ['name' => 'Quantitative Math', 'route' => 'mcq.index'],
                            ['name' => 'Data Interpretation', 'route' => 'mcq.index'],
                            ['name' => 'Logical Reasoning', 'route' => 'mcq.index'],
                            ['name' => 'Verbal Ability', 'route' => 'mcq.index'],
                            ['name' => 'Programming (C/Java)', 'route' => 'mcq.index'],
                            ['name' => 'Core Engineering', 'route' => 'mcq.index'],
                        ];
                    @endphp
                    @foreach($practiceAreas as $area)
                        <a href="{{ route($area['route']) }}" class="p-4 rounded-xl border border-gray-200 text-center hover:border-[#087F82] hover:bg-teal-50/20 group transition-all">
                            <div class="w-10 h-10 rounded-lg bg-gray-50 group-hover:bg-[#153B70] text-[#153B70] group-hover:text-white flex items-center justify-center mx-auto mb-2 transition-colors">
                                <i data-lucide="book-open" class="w-5 h-5"></i>
                            </div>
                            <p class="text-xs font-bold text-[#243247] group-hover:text-[#087F82]">{{ $area['name'] }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">MCQ Practice</p>
                        </a>
                    @endforeach
                @endif
            </div>
        </section>

    </div>
</x-layouts.app>
