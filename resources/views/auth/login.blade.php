<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- حاوية الصورة والاسم (بتنسيق Tailwind) -->
    <div class="flex flex-col items-center mb-6">
        <img src="{{ asset('amr.jpg') }}" alt="Dr. Amr Khallaf" class="w-32 h-32 rounded-full border-4 border-blue-700 object-cover">
        <h2 class="text-2xl font-bold text-blue-800 mt-4">د. عمرو خلاف</h2>
        <p class="text-gray-600">أخصائي الجراحة العامة</p>
    </div>

    <!-- النموذج -->
    <form method="POST" action="{{ route('login') }}" class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 rounded-lg" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            @if ($errors->has('email'))
                <div class="text-red-500 text-sm mt-2">
                    {{ __($errors->first('email')) }}
                </div>
            @endif
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full border-gray-300 rounded-lg"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3 bg-blue-700 hover:bg-blue-800 rounded-lg px-6 py-2">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>