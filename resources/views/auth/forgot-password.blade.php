<x-layouts.app>
    <x-slot:title>Forgot Password - AptiMedha</x-slot:title>

    <div class="py-12 sm:py-16">
        <div class="mx-auto max-w-md px-4 sm:px-6">
            <x-card title="Reset Your Password">
                <p class="text-sm text-gray-600 mb-6">
                    Enter your email address and we'll send you a password reset link.
                </p>

                <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                    @csrf

                    @if (session('status'))
                        <x-alert type="success">
                            {{ session('status') }}
                        </x-alert>
                    @endif

                    @if (isset($errors) && $errors->any())
                        <x-alert type="error">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-alert>
                    @endif

                    <x-input
                        label="Email Address"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="you@example.com"
                    />

                    <x-button type="submit" variant="primary" class="w-full">
                        Send Password Reset Link
                    </x-button>

                    <div class="text-center text-sm text-gray-600 pt-2 border-t border-gray-100">
                        Remember your password?
                        <a href="{{ route('login') }}" class="font-medium text-[#087F82] hover:underline">Back to login</a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-layouts.app>
