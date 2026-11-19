<x-layouts.admin>
    <x-slot:title>Contact & System Settings - AptiMedha Admin</x-slot:title>
    <x-slot:header>Contact & Social Media Settings</x-slot:header>

    <div class="space-y-6 max-w-5xl" x-data="{ activeTab: '{{ request('tab', $activeTab ?? 'contact') }}' }">
        {{-- Header Navigation & Quick Link to Contact Messages --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#153B70]">Settings Management</h2>
                <p class="text-xs text-gray-500">Configure contact channels, WhatsApp integration, social profiles, and site parameters.</p>
            </div>
            <a href="{{ route('admin.contacts.index') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-gray-200 text-xs font-bold text-[#153B70] hover:border-[#087F82] hover:text-[#087F82] shadow-2xs transition-colors self-start sm:self-auto">
                <i data-lucide="mail" class="w-4 h-4 text-[#087F82]"></i>
                <span>View Contact Messages</span>
            </a>
        </div>

        {{-- Settings Navigation Tabs --}}
        <div class="flex items-center space-x-2 border-b border-gray-200 overflow-x-auto scrollbar-thin">
            <button type="button"
                    @click="activeTab = 'contact'"
                    :class="activeTab === 'contact' ? 'border-[#087F82] text-[#087F82] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                    class="py-3 px-4 border-b-2 text-xs sm:text-sm transition-colors cursor-pointer whitespace-nowrap">
                Contact & Social Links
            </button>
            <button type="button"
                    @click="activeTab = 'general'"
                    :class="activeTab === 'general' ? 'border-[#087F82] text-[#087F82] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                    class="py-3 px-4 border-b-2 text-xs sm:text-sm transition-colors cursor-pointer whitespace-nowrap">
                General & Branding
            </button>
            <button type="button"
                    @click="activeTab = 'practice'"
                    :class="activeTab === 'practice' ? 'border-[#087F82] text-[#087F82] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                    class="py-3 px-4 border-b-2 text-xs sm:text-sm transition-colors cursor-pointer whitespace-nowrap">
                MCQ Practice Rules
            </button>
            <button type="button"
                    @click="activeTab = 'security'"
                    :class="activeTab === 'security' ? 'border-[#087F82] text-[#087F82] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                    class="py-3 px-4 border-b-2 text-xs sm:text-sm transition-colors cursor-pointer whitespace-nowrap">
                Access & Moderation
            </button>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="active_tab" :value="activeTab">

            {{-- Tab 1: Contact & Social Links (Primary Focus) --}}
            <div x-show="activeTab === 'contact'" class="space-y-6" x-cloak>
                {{-- Official Contact Information --}}
                <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div>
                            <h3 class="text-base font-bold text-[#153B70] flex items-center gap-2">
                                <i data-lucide="mail" class="w-5 h-5 text-[#087F82]"></i>
                                <span>Official Contact Information</span>
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Primary email addresses and desk contact details displayed on the website.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Official Support Email</label>
                            <input type="email"
                                   name="support_email"
                                   value="{{ old('support_email', $settings['support_email'] ?? '') }}"
                                   placeholder="support@aptimedha.com"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                            <p class="text-[11px] text-gray-400 mt-1">Recipient for student assistance and technical issues.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Contact Enquiry Recipient</label>
                            <input type="email"
                                   name="contact_email"
                                   value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                                   placeholder="contact@aptimedha.com"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                            <p class="text-[11px] text-gray-400 mt-1">Public inquiry recipient displayed on contact and footer pages.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Contact Phone Number</label>
                            <input type="text"
                                   name="contact_phone"
                                   value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                                   placeholder="+91 98765 43210"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                            <p class="text-[11px] text-gray-400 mt-1">Primary phone / helpdesk number.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Operating Address / Region</label>
                            <input type="text"
                                   name="contact_address"
                                   value="{{ old('contact_address', $settings['contact_address'] ?? '') }}"
                                   placeholder="India"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                            <p class="text-[11px] text-gray-400 mt-1">Operating region or official correspondence address.</p>
                        </div>
                    </div>
                </div>

                {{-- WhatsApp Support Integration --}}
                <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div>
                            <h3 class="text-base font-bold text-[#153B70] flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-600 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                <span>WhatsApp Support & Direct Chat Link</span>
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Used across public website footer, contact page, and student support touchpoints.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">WhatsApp Contact Number</label>
                            <input type="text"
                                   name="whatsapp_number"
                                   value="{{ old('whatsapp_number', $settings['whatsapp_number'] ?? '') }}"
                                   placeholder="+91 98765 43210"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                            <p class="text-[11px] text-gray-400 mt-1">Number with country code (e.g. +91 98765 43210).</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">WhatsApp Direct Chat URL</label>
                            <input type="url"
                                   name="whatsapp_url"
                                   value="{{ old('whatsapp_url', $settings['whatsapp_url'] ?? '') }}"
                                   placeholder="https://wa.me/919876543210"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none font-mono">
                            <p class="text-[11px] text-gray-400 mt-1">Leave blank to auto-generate from the WhatsApp number.</p>
                        </div>
                    </div>

                    @if(!empty($settings['whatsapp_url']))
                        <div class="p-3.5 bg-emerald-50 rounded-xl border border-emerald-200/70 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2 text-emerald-800">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Active WhatsApp Link: <code class="font-mono text-emerald-900">{{ $settings['whatsapp_url'] }}</code></span>
                            </div>
                            <a href="{{ $settings['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1 font-bold text-emerald-700 hover:text-emerald-900 hover:underline">
                                <span>Test Link</span>
                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Social Media Platforms & Channels --}}
                <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div>
                            <h3 class="text-base font-bold text-[#153B70] flex items-center gap-2">
                                <i data-lucide="share-2" class="w-5 h-5 text-[#087F82]"></i>
                                <span>Social Media Platforms & URLs</span>
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Social channels linked in the website footer, contact pages, and article shares.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">LinkedIn Profile / Page</label>
                            <input type="url"
                                   name="linkedin_url"
                                   value="{{ old('linkedin_url', $settings['linkedin_url'] ?? '') }}"
                                   placeholder="https://linkedin.com/company/aptimedha"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Twitter / X Handle</label>
                            <input type="url"
                                   name="twitter_url"
                                   value="{{ old('twitter_url', $settings['twitter_url'] ?? '') }}"
                                   placeholder="https://twitter.com/aptimedha"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Facebook Page (Optional)</label>
                            <input type="url"
                                   name="facebook_url"
                                   value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}"
                                   placeholder="https://facebook.com/aptimedha"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Instagram Profile (Optional)</label>
                            <input type="url"
                                   name="instagram_url"
                                   value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}"
                                   placeholder="https://instagram.com/aptimedha"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">YouTube Channel (Optional)</label>
                            <input type="url"
                                   name="youtube_url"
                                   value="{{ old('youtube_url', $settings['youtube_url'] ?? '') }}"
                                   placeholder="https://youtube.com/@aptimedha"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#087F82] focus:outline-none font-mono">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab 2: General & Branding --}}
            <div x-show="activeTab === 'general'" class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8 space-y-5" x-cloak>
                <h3 class="text-base font-bold text-[#153B70]">Brand Identity & Site Metadata</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Site Title</label>
                        <input type="text"
                               name="site_title"
                               value="{{ old('site_title', $settings['site_title'] ?? 'AptiMedha') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tagline / Motto</label>
                        <input type="text"
                               name="tagline"
                               value="{{ old('tagline', $settings['tagline'] ?? 'Practise Smart. Get Placement Ready.') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">
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
                    <textarea name="meta_description"
                              rows="2"
                              class="w-full p-3 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">{{ old('meta_description', $settings['meta_description'] ?? 'Sharpen your aptitude, reasoning and subject skills to crack placement exams and interviews.') }}</textarea>
                </div>
            </div>

            {{-- Tab 3: MCQ Practice Rules --}}
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
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white text-xs font-bold shadow-xs transition-colors cursor-pointer">
                    Save Configuration
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
