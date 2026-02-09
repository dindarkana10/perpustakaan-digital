@section('title', 'Login - Sistem Peminjaman Alat')

<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Selamat Datang</h2>
        <p class="text-gray-600 mt-2 text-base">Silahkan login untuk melanjutkan</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="font-semibold" />
            <x-text-input id="email" 
                class="block mt-1 w-full px-5 py-3 text-base rounded-lg border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500 transition duration-200" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autofocus 
                autocomplete="username" 
                placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-5">
            <x-input-label for="password" :value="__('Password')" class="font-semibold" />
            <x-text-input id="password" 
                class="block mt-1 w-full px-5 py-3 text-base rounded-lg border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500 transition duration-200"
                type="password"
                name="password"
                required 
                autocomplete="current-password" 
                placeholder="Masukkan password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-5">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" 
                    type="checkbox" 
                    class="rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500 cursor-pointer w-4 h-4" 
                    name="remember">
                <span class="ms-2 text-sm text-gray-600 hover:text-gray-800">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <!-- Login Button -->
        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3 text-base font-semibold bg-gradient-to-r from-purple-600 to-purple-400 hover:from-purple-700 hover:to-purple-500 transition duration-200 shadow-lg">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>

        <!-- Register Link (Optional) -->
        {{-- @if (Route::has('register'))
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-purple-600 hover:text-purple-800 font-semibold">
                    Daftar disini
                </a>
            </p>
        </div>
        @endif --}}
    </form>
</x-guest-layout>