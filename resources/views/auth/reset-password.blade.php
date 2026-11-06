<x-layouts.app>
    <x-slot:title>Set New Password - AptiMedha</x-slot:title>
    <x-slot:noindex>true</x-slot:noindex>

    <div class="py-8 sm:py-16 bg-grid-pattern relative flex items-center justify-center min-h-[calc(100vh-14rem)]">
        <div class="mx-auto w-full max-w-md px-3 sm:px-6">
            <div class="bg-white rounded-3xl border border-gray-200/90 shadow-xl p-5 sm:p-8">
                <div class="text-center mb-6">
                    <a href="{{ url('/') }}" class="inline-block mb-3">
                        <x-application-logo size="lg" />
                    </a>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-[#153B70]">Set New Password</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Choose a strong, secure password for your AptiMedha account.</p>
                </div>

                <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token ?? '' }}">

                    @if (isset($errors) && $errors->any())
                        <x-alert type="error">
                            <ul class="list-disc list-inside text-xs space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            @if($errors->has('email') && str_contains(implode(' ', $errors->get('email')), 'invalid'))
                                <div class="mt-2.5 pt-2 border-t border-red-200 text-xs">
                                    <a href="{{ route('password.request') }}" class="font-bold text-[#153B70] hover:underline inline-flex items-center gap-1">
                                        Request a new reset link &rarr;
                                    </a>
                                </div>
                            @endif
                        </x-alert>
                    @endif

                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Email Address
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email', $email ?? '') }}"
                               required
                               placeholder="you@example.com"
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none transition-colors min-h-[44px]">
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            New Password
                        </label>
                        <div x-data="{ showPass: false }" class="relative">
                            <input :type="showPass ? 'text' : 'password'"
                                   name="password"
                                   id="password"
                                   required
                                   placeholder="At least 8 characters"
                                   class="w-full pl-4 pr-12 py-3 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none transition-colors min-h-[44px]">
                            <button type="button"
                                    @click="showPass = !showPass"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#087F82] transition-colors p-2 min-w-[40px] min-h-[40px] flex items-center justify-center cursor-pointer"
                                    :title="showPass ? 'Hide password' : 'Show password'"
                                    aria-label="Toggle password visibility">
                                <i x-show="!showPass" data-lucide="eye" class="w-4 h-4"></i>
                                <i x-show="showPass" data-lucide="eye-off" class="w-4 h-4" x-cloak></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Confirm New Password
                        </label>
                        <div x-data="{ showConfirm: false }" class="relative">
                            <input :type="showConfirm ? 'text' : 'password'"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   required
                                   placeholder="Re-type new password"
                                   class="w-full pl-4 pr-12 py-3 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:ring-2 focus:ring-[#087F82]/20 focus:outline-none transition-colors min-h-[44px]">
                            <button type="button"
                                    @click="showConfirm = !showConfirm"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#087F82] transition-colors p-2 min-w-[40px] min-h-[40px] flex items-center justify-center cursor-pointer"
                                    :title="showConfirm ? 'Hide password' : 'Show password'"
                                    aria-label="Toggle confirm password visibility">
                                <i x-show="!showConfirm" data-lucide="eye" class="w-4 h-4"></i>
                                <i x-show="showConfirm" data-lucide="eye-off" class="w-4 h-4" x-cloak></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full py-3.5 px-4 rounded-xl bg-[#087F82] hover:bg-[#066668] text-white font-bold text-sm shadow-md transition-colors cursor-pointer mt-2 min-h-[46px] flex items-center justify-center">
                        Reset Password & Save
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
