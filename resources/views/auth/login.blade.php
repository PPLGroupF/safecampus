<x-guest-layout>
    <h1 class="text-2xl font-bold text-gray-900">Masuk</h1>
    <p class="mt-1 text-sm text-gray-500">Gunakan akun SafeCampus yang diberikan oleh admin.</p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
        @csrf

        <div class="space-y-1.5">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" dusk="login-email" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="space-y-1.5">
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" dusk="login-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <label for="remember_me" class="flex items-center gap-2 text-sm text-gray-600">
            <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            Ingat saya
        </label>

        <x-primary-button class="w-full" dusk="login-button">Masuk</x-primary-button>

        <p class="text-center text-xs text-gray-500">
            Lupa kata sandi? Hubungi admin SafeCampus untuk mereset kata sandi Anda.
        </p>
    </form>
</x-guest-layout>
