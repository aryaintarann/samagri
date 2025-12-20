<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - {{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>

<body class="font-sans text-gray-900 antialiased bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg border border-gray-100 p-10 space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-blue-600 mb-6">
                    <i class="fas fa-lock-open text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                    Set New Password
                </h2>
                <p class="mt-2 text-sm text-gray-500">
                    Create a strong password for your account.
                </p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" name="email" type="email" required autofocus
                            class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 transition-colors bg-gray-50"
                            placeholder="name@company.com" value="{{ old('email', $request->email) }}" readonly>
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                            class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 transition-colors"
                            placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm
                        Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            autocomplete="new-password"
                            class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 transition-colors"
                            placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    Reset Password
                </button>
            </form>

            <!-- Footer -->
            <div class="pt-6 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-400">
                    &copy; {{ date('Y') }} Business Management System.
                </p>
            </div>
        </div>
    </div>
</body>

</html>