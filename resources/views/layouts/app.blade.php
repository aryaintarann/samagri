<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-800 bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Header -->
            <header
                class="bg-white border-b border-gray-200 py-4 px-8 flex justify-between items-center sticky top-0 z-30 shadow-sm">
                <!-- Left: Title & Welcome -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 capitalize">
                        {{ auth()->user()->name }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">SamagriTech</p>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center space-x-6">
                    <!-- Global Search -->
                    <div class="relative hidden md:block">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <form action="{{ route('global.search') }}" method="GET">
                            <input type="text" name="q" placeholder="Search..." value="{{ request('q') }}"
                                class="pl-12 pr-4 py-2.5 rounded-full bg-gray-50 border-none focus:ring-2 focus:ring-indigo-100 focus:bg-white transition-all text-sm w-72 placeholder-gray-400 text-gray-700">
                        </form>
                    </div>

                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-400 hover:text-indigo-600 transition-colors">
                        <i class="far fa-bell text-xl"></i>
                        <span
                            class="absolute top-1.5 right-1.5 h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>

                    <!-- User Profile -->
                    <div
                        class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-md cursor-pointer hover:shadow-lg transition-transform transform hover:scale-105">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                </div>
            </header>

            <main class="flex-1 p-6 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>