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
                    <!-- Notifications -->
                    <div class="relative group" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                            class="relative p-2 text-gray-400 hover:text-indigo-600 transition-colors">
                            <i class="far fa-bell text-xl"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span
                                    class="absolute top-1.5 right-1.5 h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                            @endif
                        </button>

                        <!-- Dropdown -->
                        <div x-show="open" style="display: none;" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="origin-top-right absolute right-0 mt-2 w-80 rounded-xl shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                            <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                                <span class="font-semibold text-gray-700">Notifications</span>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <button
                                        @click="fetch('{{ route('notifications.readAll') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => { window.location.reload(); })"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Mark all
                                        read</button>
                                @endif
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                @forelse(auth()->user()->unreadNotifications as $notification)
                                    <div @click="fetch('{{ route('notifications.read', $notification->id) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => { window.location.href = '#'; /* Add link to project if needed */ })"
                                        class="block px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50 last:border-0 relative group cursor-pointer">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 mt-1">
                                                <div
                                                    class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                                    <i class="fas fa-briefcase text-xs"></i>
                                                </div>
                                            </div>
                                            <div class="ml-3 w-0 flex-1">
                                                <p class="text-sm text-gray-800 font-medium">
                                                    {{ $notification->data['message'] ?? 'New Notification' }}</p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                            <div class="ml-auto pl-3">
                                                <button
                                                    @click.stop="fetch('{{ route('notifications.read', $notification->id) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => { window.location.reload(); })"
                                                    class="text-gray-400 hover:text-indigo-600" title="Mark as read">
                                                    <i class="fas fa-check text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-8 text-center text-gray-500">
                                        <i class="far fa-bell-slash text-2xl text-gray-300 mb-2 block"></i>
                                        <p class="text-sm">No new notifications</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- User Profile -->
                    <a href="{{ route('profile.edit') }}"
                        class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold shadow-sm cursor-pointer hover:bg-indigo-700 transition-colors">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </a>
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