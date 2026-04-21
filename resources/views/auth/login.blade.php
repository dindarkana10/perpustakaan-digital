@section('title', 'Login - Sistem Peminjaman Alat')

<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Selamat Datang</h2>
        <p class="text-gray-600 mt-2 text-base">Silahkan login untuk melanjutkan</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Alert error global jika ada --}}
    @if ($errors->any())
        <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-red-700 mb-1">Login gagal</p>
                @foreach ($errors->all() as $error)
                    <p class="text-sm text-red-600">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" class="font-semibold" />
            <x-text-input id="email"
                class="block mt-1 w-full px-5 py-3 text-base rounded-lg border-gray-300 
                       focus:border-blue-600 focus:ring-2 focus:ring-blue-600 transition duration-200
                       {{ $errors->has('email') ? 'border-red-400 bg-red-50 focus:border-red-500 focus:ring-red-500' : '' }}"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="nama@email.com" />

            {{-- Pesan error email dengan ikon --}}
            @error('email')
                <div class="mt-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm text-red-600 font-medium">{{ $message }}</span>
                </div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mt-5">
            <x-input-label for="password" :value="__('Password')" class="font-semibold" />
            <x-text-input id="password"
                class="block mt-1 w-full px-5 py-3 text-base rounded-lg border-gray-300
                       focus:border-blue-600 focus:ring-2 focus:ring-blue-600 transition duration-200
                       {{ $errors->has('password') ? 'border-red-400 bg-red-50 focus:border-red-500 focus:ring-red-500' : '' }}"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan password" />

            @error('password')
                <div class="mt-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm text-red-600 font-medium">{{ $message }}</span>
                </div>
            @enderror
        </div>

        {{-- Remember me --}}
        <div class="block mt-5">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-600 cursor-pointer w-4 h-4"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600 hover:text-gray-800">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        {{-- Submit --}}
        <div class="mt-6">
            <x-primary-button
                class="w-full justify-center py-3 text-base font-semibold 
                       bg-gradient-to-r from-blue-700 to-blue-500 
                       hover:from-blue-800 hover:to-blue-600 
                       transition duration-200 shadow-lg border-none text-white">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>

    </form>
</x-guest-layout>