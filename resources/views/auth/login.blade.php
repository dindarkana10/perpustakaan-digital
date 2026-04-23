@section('title', 'Login - Sistem Peminjaman Buku')

<x-guest-layout>
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>
        <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Selamat Datang</h2>
        <p class="text-gray-500 mt-2 text-sm">Perpustakaan Digital SMKN 1 Ciomas</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Error Alert --}}
    @if ($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-red-50 border-l-4 border-red-500 flex items-start gap-3 shadow-sm">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-red-800">Login Gagal</h3>
                <div class="text-xs text-red-700 mt-1">Periksa kembali email atau password Anda.</div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium ml-1" />
            <div class="relative mt-1 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-blue-600 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                    </svg>
                </div>
                <x-text-input id="email" 
                    class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-300" 
                    type="email" name="email" :value="old('email')" required autofocus placeholder="nama@gmail.com" />
            </div>
        </div>

        {{-- Password --}}
        <div>
            <div class="flex justify-between items-center mb-1 ml-1">
                <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium" />
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:underline">Lupa Password?</a>
                @endif
            </div>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <x-text-input id="password" 
                    class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-300" 
                    type="password" name="password" required placeholder="••••••••" />
            </div>
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <label for="remember_me" class="ml-2 text-sm text-gray-600">Ingat perangkat ini</label>
        </div>

        {{-- Submit --}}
        <div class="pt-2">
            <button type="submit" class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition-all duration-300 active:scale-[0.98]">
                Masuk ke Akun
            </button>
        </div>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:text-blue-700 ml-1 transition-colors">Daftar Sekarang</a>
            </p>
        </div>
    </form>
</x-guest-layout>