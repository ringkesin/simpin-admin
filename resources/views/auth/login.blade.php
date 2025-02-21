<x-guest-layout>
    <h1 class="mb-6 text-3xl font-bold text-slate-800 dark:text-slate-100">{{ __('Login terlebih dahulu') }} ✨</h1>
    @if (session('status'))
        <div class="mb-4 text-sm font-medium text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <x-label for="username_email" value="{{ __('Username') }}" />
                <x-input id="username_email" class="w-full" type="text" name="username" :value="old('username')" required autofocus />
            </div>

            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="w-full" type="password" name="password" required autocomplete="current-password" />
            </div>
        </div>

        <div class="flex items-center justify-between mt-6">
            <x-button class="px-5">
                {{ __('Log in') }}
            </x-button>
        </div>
    </form>
    <x-validation-errors class="mt-4" />
</x-guest-layout>
