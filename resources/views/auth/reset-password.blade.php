<x-layouts.app>
    <x-slot:title>Set New Password - AptiMedha</x-slot:title>

    <div class="py-12 sm:py-16">
        <div class="mx-auto max-w-md px-4 sm:px-6">
            <x-card title="Set New Password">
                <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token ?? '' }}">

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
                    />

                    <x-input
                        label="New Password"
                        name="password"
                        type="password"
                        required
                    />

                    <x-input
                        label="Confirm New Password"
                        name="password_confirmation"
                        type="password"
                        required
                    />

                    <x-button type="submit" variant="primary" class="w-full">
                        Reset Password
                    </x-button>
                </form>
            </x-card>
        </div>
    </div>
</x-layouts.app>
