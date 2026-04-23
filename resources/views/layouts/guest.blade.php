<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-purple-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <!-- Card -->
                <div class="w-full sm:max-w-2xl px-16 py-10 bg-white shadow-lg rounded-xl">
                    {{ $slot }}
                </div>

                <!-- Footer Text -->
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600">
                        © {{ date('Y') }} Sistem Peminjaman Buku
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>