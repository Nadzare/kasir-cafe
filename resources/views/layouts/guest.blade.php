<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $title ?? 'Login - Wisata Tuksirah')</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative">
        
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center z-0" 
             style="background-image: url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?q=80&w=2560&auto=format&fit=crop');">
        </div>

        <!-- Overlay Gradient -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#1a4d2e]/90 to-black/80 z-0"></div>

        <!-- Login Card dengan Glassmorphism -->
        <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white/95 backdrop-blur-sm shadow-2xl overflow-hidden sm:rounded-3xl relative z-10 border border-white/20">
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <img src="{{ asset('images/logotuk.png') }}" alt="Logo" class="h-24 w-auto drop-shadow-md">
            </div>
            
            {{ $slot }}
        </div>
        
        <!-- Footer -->
        <div class="relative z-10 mt-8 text-white/60 text-sm">
            &copy; {{ date('Y') }} Tuksirah Kali Pemali.
        </div>
    </div>
</body>
</html>
