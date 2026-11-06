<x-layouts.app>
    <x-slot:title>Forgot Password - AptiMedha</x-slot:title>
    <x-slot:noindex>true</x-slot:noindex>

    <div class="py-8 sm:py-16 bg-grid-pattern relative flex items-center justify-center min-h-[calc(100vh-14rem)]">
        <div class="mx-auto w-full max-w-md px-3 sm:px-6">
            <div class="bg-white rounded-3xl border border-gray-200/90 shadow-xl p-5 sm:p-8">
                
                {{-- Header with Logo --}}
                <div class="text-center mb-6">
                    <a href="{{ url('/') }}" class="inline-block mb-3">
                        <x-application-logo size="lg" />
                    </a>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-[#153B70]">Reset Password</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Enter your registered email and we'll send a password reset link.</p>
                </div>

                <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                    @csrf

                    @if (session('status'))
                        <div class="mb-4">
                            <x-alert type="success">
                                <p>{{ session('status') }}</p>
                                @if(config('mail.default') === 'log')
                                    <div class="mt-2.5 pt-2.5 border-t border-green-200 text-xs text-green-900 leading-relaxed font-sans">
                                        <span class="font-bold">Development Mode Notice:</span> Mail driver is configured to <code class="bg-green-100 px-1 py-0.5 rounded text-green-800">log</code>. Password reset links are written to <code class="bg-green-100 px-1 py-0.5 rounded text-green-800">storage/logs/laravel.log</code>.
                                    </div>
                                @endif
                            </x-alert>
                        </div>
                    @endif

                    @if (isset($errors) && $errors->any())
                        <x-alert type="error">
                            <ul class="list-disc list-inside text-xs">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-alert>
                    @endif

                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Registered Email Address
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email') }}"
                               required
                               placeholder="you@example.com"
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none transition-colors min-h-[44px]">
                    </div>

                    <button type="submit"
                            class="w-full py-3.5 px-4 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white font-bold text-sm shadow-md transition-colors cursor-pointer mt-2 min-h-[46px] flex items-center justify-center">
                        Send Password Reset Link
                    </button>

                    <div class="text-center text-xs text-gray-500 pt-4 border-t border-gray-100">
                        Remember your password?
                        <a href="{{ route('login') }}" class="font-bold text-[#153B70] hover:underline">Back to sign in</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
