<x-layouts.app>
    <x-slot:title>Register - Create Your AptiMedha Account</x-slot:title>

    <div class="py-12 sm:py-16">
        <div class="mx-auto max-w-lg px-4 sm:px-6">
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-md p-6 sm:p-8">
                
                {{-- Header with Logo --}}
                <div class="text-center mb-6">
                    <a href="{{ url('/') }}" class="inline-block mb-3">
                        <x-application-logo size="lg" />
                    </a>
                    <h2 class="text-2xl font-extrabold text-[#153B70]">Create Your Account</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Start bookmarking questions, tracking progress, and participating in discussions.</p>
                </div>

                {{-- Google Social Sign Up --}}
                <div class="mb-5">
                    <button type="button" class="w-full flex items-center justify-center gap-3 py-2.5 px-4 rounded-xl border border-gray-300 hover:bg-gray-50 text-sm font-semibold text-[#243247] transition-colors shadow-2xs">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span>Sign up with Google</span>
                    </button>

                    <div class="relative my-5">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                        <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-2 text-gray-400 font-semibold">Or register with email</span></div>
                    </div>
                </div>

                {{-- Registration Form --}}
                <form action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf

                    @if (isset($errors) && $errors->any())
                        <x-alert type="error">
                            <ul class="list-disc list-inside text-xs">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-alert>
                    @endif

                    {{-- Full Name & Username --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Full Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name') }}"
                                   required
                                   placeholder="e.g. Rahul Sharma"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none transition-colors">
                        </div>

                        <div>
                            <label for="username" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Username <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   name="username"
                                   id="username"
                                   value="{{ old('username') }}"
                                   required
                                   placeholder="e.g. rahul_s"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none transition-colors">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email') }}"
                               required
                               placeholder="rahul@example.com"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none transition-colors">
                    </div>

                    {{-- Password & Confirmation --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Password <span class="text-rose-500">*</span>
                            </label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   required
                                   placeholder="At least 8 characters"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none transition-colors">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Confirm Password <span class="text-rose-500">*</span>
                            </label>
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   required
                                   placeholder="Re-type password"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none transition-colors">
                        </div>
                    </div>

                    {{-- Terms Agreement --}}
                    <div class="flex items-start gap-2 pt-1">
                        <input id="terms" name="terms" type="checkbox" required
                               class="h-4 w-4 rounded border-gray-300 text-[#087F82] focus:ring-[#087F82] mt-0.5">
                        <label for="terms" class="text-xs text-gray-600 leading-normal">
                            I agree to AptiMedha's <a href="{{ route('terms') }}" class="text-[#087F82] underline font-semibold">Terms & Conditions</a> and <a href="{{ route('privacy') }}" class="text-[#087F82] underline font-semibold">Privacy Policy</a>.
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full py-3 px-4 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white font-bold text-sm shadow-md shadow-[#087F82]/20 transition-all cursor-pointer mt-2">
                        Create Free Account
                    </button>

                    <div class="text-center text-xs text-gray-500 pt-4 border-t border-gray-100">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-bold text-[#153B70] hover:underline">Sign in instead</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
