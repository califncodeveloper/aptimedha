<x-layouts.admin>
    <x-slot:title>System Settings - AptiMedha Admin</x-slot:title>
    <x-slot:header>Platform Configuration & Branding</x-slot:header>

    <div class="space-y-6 max-w-4xl" x-data="{ activeTab: 'general' }">
        {{-- Settings Navigation Tabs --}}
        <div class="flex items-center space-x-2 border-b border-gray-200">
            <button @click="activeTab = 'general'"
                    :class="activeTab === 'general' ? 'border-[#087F82] text-[#087F82] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                    class="py-3 px-4 border-b-2 text-xs sm:text-sm transition-colors cursor-pointer">
                General & Branding
            </button>
            <button @click="activeTab = 'practice'"
                    :class="activeTab === 'practice' ? 'border-[#087F82] text-[#087F82] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                    class="py-3 px-4 border-b-2 text-xs sm:text-sm transition-colors cursor-pointer">
                MCQ Practice Rules
            </button>
            <button @click="activeTab = 'contact'"
                    :class="activeTab === 'contact' ? 'border-[#087F82] text-[#087F82] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                    class="py-3 px-4 border-b-2 text-xs sm:text-sm transition-colors cursor-pointer">
                Contact & Social Links
            </button>
            <button @click="activeTab = 'security'"
                    :class="activeTab === 'security' ? 'border-[#087F82] text-[#087F82] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                    class="py-3 px-4 border-b-2 text-xs sm:text-sm transition-colors cursor-pointer">
                Access & Moderation
            </button>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Tab 1: General & Branding --}}
            <div x-show="activeTab === 'general'" class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8 space-y-5">
                <h3 class="text-base font-bold text-[#153B70]">Brand Identity & Site Metadata</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Site Title</label>
                        <input type="text" name="site_title" value="AptiMedha" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tagline / Motto</label>
                        <input type="text" name="tagline" value="Practise Smart. Get Placement Ready." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                    </div>
                </div>

                {{-- Client Palette Tokens Display --}}
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-xs font-bold text-gray-600 mb-2 uppercase tracking-wider">Client Approved Brand Color Palette:</p>
                    <div class="flex flex-wrap gap-3">
                        <div class="flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 text-xs">
                            <span class="w-4 h-4 rounded-full bg-[#153B70]"></span>
                            <span>Primary Navy: <code>#153B70</code></span>
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 text-xs">
                            <span class="w-4 h-4 rounded-full bg-[#087F82]"></span>
                            <span>Secondary Teal: <code>#087F82</code></span>
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 text-xs">
                            <span class="w-4 h-4 rounded-full bg-[#F7F9FC] border border-gray-300"></span>
                            <span>Background: <code>#F7F9FC</code></span>
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 text-xs">
                            <span class="w-4 h-4 rounded-full bg-[#243247]"></span>
                            <span>Main Text: <code>#243247</code></span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="w-full p-3 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">Sharpen your aptitude, reasoning and subject skills to crack placement exams and interviews.</textarea>
                </div>
            </div>

            {{-- Tab 2: MCQ Practice Rules --}}
            <div x-show="activeTab === 'practice'" class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8 space-y-5" x-cloak>
                <h3 class="text-base font-bold text-[#153B70]">MCQ Practice & Pagination Handover Rules</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Questions Displayed Per Page</label>
                        <input type="number" name="questions_per_page" value="10" readonly class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-600 font-bold">
                        <p class="text-[11px] text-gray-400 mt-1">Locked to 10 questions per page per client SRS Section 4.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Questions Per Section</label>
                        <input type="number" name="questions_per_section" value="100" readonly class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-600 font-bold">
                        <p class="text-[11px] text-gray-400 mt-1">100 questions per section (Section 1: 1-100, Section 2: 101-200).</p>
                    </div>
                </div>

                <div class="space-y-3 pt-2">
                    <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer">
                        <input type="checkbox" checked disabled class="h-4 w-4 rounded text-[#087F82] border-gray-300">
                        <div>
                            <span class="text-xs font-bold text-[#153B70]">Immediate Answer Reveal</span>
                            <p class="text-[11px] text-gray-500">Reveal correct option and explanation immediately upon user selection.</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer">
                        <input type="checkbox" checked disabled class="h-4 w-4 rounded text-[#087F82] border-gray-300">
                        <div>
                            <span class="text-xs font-bold text-[#153B70]">Open Practice Mode (No Exam Timer)</span>
                            <p class="text-[11px] text-gray-500">Practice platform mode per client requirements — no timer restrictions.</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Tab 3: Contact & Social Links --}}
            <div x-show="activeTab === 'contact'" class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8 space-y-5" x-cloak>
                <h3 class="text-base font-bold text-[#153B70]">Contact Information & Social Channels</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Official Support Email</label>
                        <input type="email" name="support_email" value="support@aptimedha.com" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Contact Enquiry Recipient</label>
                        <input type="email" name="contact_email" value="contact@aptimedha.com" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">LinkedIn URL</label>
                        <input type="url" name="linkedin_url" value="https://linkedin.com/company/aptimedha" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Twitter / X URL</label>
                        <input type="url" name="twitter_url" value="https://twitter.com/aptimedha" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">GitHub Repository</label>
                        <input type="url" name="github_url" value="https://github.com/aptimedha" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none font-mono">
                    </div>
                </div>
            </div>

            {{-- Tab 4: Access & Moderation --}}
            <div x-show="activeTab === 'security'" class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8 space-y-5" x-cloak>
                <h3 class="text-base font-bold text-[#153B70]">User Access & Moderation</h3>

                <div class="space-y-3">
                    <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer">
                        <div>
                            <span class="text-xs font-bold text-[#153B70]">Allow Open Student Registration</span>
                            <p class="text-[11px] text-gray-500">Enable new users to register and create free practice accounts.</p>
                        </div>
                        <input type="checkbox" checked class="h-4 w-4 rounded text-[#087F82] border-gray-300">
                    </label>

                    <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer">
                        <div>
                            <span class="text-xs font-bold text-[#153B70]">Community Comments Pre-Moderation</span>
                            <p class="text-[11px] text-gray-500">Hold student discussion comments for admin review before publishing.</p>
                        </div>
                        <input type="checkbox" class="h-4 w-4 rounded text-[#087F82] border-gray-300">
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-3">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold shadow-xs transition-colors">
                    Save Configuration
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
