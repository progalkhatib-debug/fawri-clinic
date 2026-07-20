<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- تصميم الصورة والمعلومات (مطابق لصفحة الحجز) -->
    <div class="text-center mb-6">
        <img src="{{ asset('images/amr.jpg') }}" alt="دكتور عمرو خلاف" class="w-32 h-32 rounded-full border-4 border-blue-600 mx-auto object-cover">
        <h2 class="text-2xl font-bold text-blue-800 mt-4">د. عمرو خلاف</h2>
        <p class="text-gray-600 font-medium">أخصائي الجراحة العامة</p>
    </div>

    <!-- نموذج الدخول (بنفس تنسيق حقول الحجز) -->
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full p-3 border rounded-lg" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full p-3 border rounded-lg"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="bg-blue-600 hover:bg-blue-700 py-3 px-6 rounded-lg font-bold">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>