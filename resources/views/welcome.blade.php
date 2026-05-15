<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gym Ni Bai</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-900 text-white">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-slate-900 border-b border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <span class="text-xl font-bold text-white">Gym Ni Bai</span>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('customer.login') }}" class="text-sm font-medium text-gray-300 hover:text-white transition">
                            Login
                        </a>
                        <a href="{{ route('customer.register') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition">
                            Register
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative min-h-screen flex items-center justify-center" style="background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1200 800%22><rect fill=%22%23111827%22 width=%221200%22 height=%22800%22/><g fill=%22%23374151%22><circle cx=%22200%22 cy=%22200%22 r=%2250%22 opacity=%220.3%22/><circle cx=%22800%22 cy=%22300%22 r=%2230%22 opacity=%220.2%22/><circle cx=%22400%22 cy=%22600%22 r=%2240%22 opacity=%220.25%22/><rect x=%22100%22 y=%22400%22 width=%22100%22 height=%2260%22 rx=%2210%22 opacity=%220.2%22/><rect x=%22900%22 y=%22150%22 width=%22120%22 height=%2280%22 rx=%2215%22 opacity=%220.15%22/></g></svg>'); background-size: cover; background-position: center;">
            <div class="text-center max-w-4xl px-4">
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight">
                    EMBRACE THE JOURNEY.<br>
                    TRANSFORM YOUR LIFE.
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto">
                    Your custom fitness path, dynamic classes, and results tracking, all in one place. Starts today.
                </p>
                <a href="{{ route('customer.register') }}" class="inline-block px-8 py-4 text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition transform hover:scale-105">
                    GET STARTED
                </a>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-slate-900 border-t border-slate-800 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} Gym Ni Bai. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</body>
</html>
