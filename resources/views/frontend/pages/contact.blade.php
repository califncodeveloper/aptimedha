<x-layouts.app>
    <x-slot:title>{{ \App\Services\SeoService::staticPageTitle($page, 'Contact Us') }}</x-slot:title>
    <x-slot:metaDescription>{{ \App\Services\SeoService::staticPageDescription($page, 'Contact the AptiMedha team for questions, suggestions, corrections, or support with your placement preparation.') }}</x-slot:metaDescription>

    {{-- Contact Hero with Background Grid Overlay --}}
    <div class="bg-gradient-to-br from-[#153B70] via-[#1a4a8a] to-[#087F82] text-white py-16 sm:py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-grid-white opacity-15 pointer-events-none"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-white/10 text-teal-200 border border-white/20 mb-3 shadow-2xs backdrop-blur-md">
                    <i data-lucide="headphones" class="w-4 h-4 mr-2"></i> Support & Suggestions
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                    Contact Us
                </h1>
                <p class="mt-4 text-base sm:text-lg text-white/85 leading-relaxed">
                    We're here to help you prepare better. Have a question, found an incorrect answer, or want to suggest a new practice topic? We welcome your observations.
                </p>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            {{-- Left Column: How Can We Help --}}
            <div class="lg:col-span-5 space-y-6">
                <div>
                    <h2 class="text-2xl font-extrabold text-[#153B70]">How Can We Help?</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        At AptiMedha, your observations help us improve questions, explanations, and learning experience.
                    </p>
                </div>

                <div class="space-y-4">
                    <div class="p-4 bg-white rounded-xl border border-gray-200/90 shadow-xs flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="alert-circle" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-[#153B70]">Report a Question or Answer Error</h4>
                            <p class="text-xs text-gray-500 mt-1">Share the page link, question number, and your suggested correction or supporting reference.</p>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-gray-200/90 shadow-xs flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-lg bg-teal-50 text-[#087F82] flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="lightbulb" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-[#153B70]">Suggest a Topic</h4>
                            <p class="text-xs text-gray-500 mt-1">Tell us which chapter, subject, or placement preparation resource you would like us to cover.</p>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-gray-200/90 shadow-xs flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-lg bg-blue-50 text-[#153B70] flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="wrench" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-[#153B70]">Technical Support</h4>
                            <p class="text-xs text-gray-500 mt-1">Having trouble viewing diagrams or accessing pages? Let us know your device and browser.</p>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-gray-200/90 shadow-xs flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="briefcase" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-[#153B70]">Collaboration & Copyright</h4>
                            <p class="text-xs text-gray-500 mt-1">For educational partnerships, licensing proposals, or privacy enquiries, contact our administrative desk.</p>
                        </div>
                    </div>
                </div>

                {{-- Direct Contact Box --}}
                <div class="p-5 bg-teal-50/60 rounded-xl border border-teal-200/60 text-xs text-[#243247] space-y-2">
                    <p class="font-bold text-[#153B70] flex items-center gap-1.5">
                        <i data-lucide="mail" class="w-4 h-4 text-[#087F82]"></i> Direct Email:
                    </p>
                    <p class="text-sm font-semibold text-[#087F82]">aptimedha@gmail.com</p>
                    <p class="text-gray-500 text-[11px]">We aim to review and reply to inquiries within 1-2 business days.</p>
                </div>
            </div>

            {{-- Right Column: Interactive Contact Form matching client doc --}}
            <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-200/90 shadow-sm p-6 sm:p-10" x-data="{ submitted: false }">
                <div class="mb-6">
                    <h3 class="text-xl font-extrabold text-[#153B70]">Let's Hear From You</h3>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Fill out the form below and our team will get in touch with you.</p>
                </div>

                <form method="POST" action="{{ route('contact.submit') }}" @submit="submitted = true" class="space-y-5">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Your Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               required
                               placeholder="Enter your name"
                               value="{{ old('name') }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none">
                        @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               required
                               placeholder="Enter an email address where we can reply"
                               value="{{ old('email') }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none">
                        @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Enquiry Type & Subject Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="enquiry_type" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Enquiry Type <span class="text-rose-500">*</span>
                            </label>
                            <select name="enquiry_type"
                                    id="enquiry_type"
                                    required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none">
                                <option value="General Enquiry">General Enquiry</option>
                                <option value="Report an Error">Report an Error</option>
                                <option value="Suggest a Topic">Suggest a Topic</option>
                                <option value="Technical Support">Technical Support</option>
                                <option value="Collaboration">Collaboration</option>
                                <option value="Copyright">Copyright</option>
                                <option value="Privacy">Privacy</option>
                            </select>
                        </div>

                        <div>
                            <label for="subject" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Subject <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   name="subject"
                                   id="subject"
                                   required
                                   placeholder="Briefly describe your enquiry"
                                   value="{{ old('subject') }}"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none">
                            @error('subject') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Page URL (Optional) --}}
                    <div>
                        <label for="page_url" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Page URL <span class="text-xs text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <input type="url"
                               name="page_url"
                               id="page_url"
                               placeholder="Paste the relevant AptiMedha page link (if reporting an error)"
                               value="{{ old('page_url') }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none">
                    </div>

                    {{-- Message --}}
                    <div>
                        <label for="message" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Message <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="message"
                                  id="message"
                                  rows="4"
                                  required
                                  placeholder="Tell us how we can help..."
                                  class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none">{{ old('message') }}</textarea>
                        @error('message') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Privacy Note from Client Document --}}
                    <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200 text-xs text-gray-500 leading-relaxed">
                        We will use the details you provide to respond to your enquiry, as explained in our <a href="{{ route('privacy') }}" class="text-[#087F82] underline font-semibold">Privacy Policy</a>. Please do not include passwords, Aadhaar numbers, bank details or other sensitive information.
                    </div>

                    {{-- Cloudflare Turnstile Security Verification --}}
                    <x-turnstile action="contact" />

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="w-full py-3.5 px-6 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white font-bold text-sm shadow-md shadow-[#087F82]/20 transition-all cursor-pointer">
                        Send Message
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-layouts.app>
