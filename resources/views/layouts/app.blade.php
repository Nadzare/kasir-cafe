<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Favicon -->
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

        <title>@yield('title', $title ?? config('app.name', 'Cafe POS')))</title>

        <!-- Modern Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- FontAwesome 6 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        
        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50" 
          x-data="{ sidebarOpen: false, sidebarExpanded: true }"
          @keydown.escape.window="sidebarOpen = false">
        <div class="min-h-screen">
            <!-- Mobile Overlay -->
            <div 
                x-show="sidebarOpen" 
                @click="sidebarOpen = false"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-40 bg-gray-900/80 lg:hidden"
            ></div>

            <!-- Sidebar -->
            <aside 
                class="fixed inset-y-0 left-0 z-50 bg-gradient-to-b from-amber-700 to-orange-900 transform transition-all duration-300 ease-in-out shadow-2xl"
                :class="{
                    '-translate-x-full': !sidebarOpen,
                    'translate-x-0': sidebarOpen,
                    'lg:translate-x-0': true,
                    'w-64': sidebarExpanded,
                    'w-20': !sidebarExpanded
                }">
                <div class="flex flex-col h-full">
                    <!-- Logo & Brand -->
                    <div class="flex items-center justify-center h-24 bg-orange-900 border-b border-amber-700 shadow-sm overflow-hidden relative z-10 px-4">
                        <a href="{{ route('dashboard') }}" class="flex items-center justify-center w-full h-full py-2">
                            <img src="{{ asset('images/kndlogo.png') }}"
                                 alt="Logo Cafe"
                                 class="max-h-20 w-auto object-contain transition-transform duration-300 hover:scale-105 origin-center">
                        </a>
                    </div>

                    <!-- Navigation Menu -->
                    <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
                        @php
                            $userRole = auth()->user()->role ?? 'guest';
                        @endphp

                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" 
                           @click="sidebarOpen = false"
                           class="flex items-center space-x-3 px-4 py-3.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-white/15 text-white shadow-lg backdrop-blur-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span x-show="sidebarExpanded" x-transition class="font-medium whitespace-nowrap">Dashboard</span>
                        </a>

                        @if(in_array($userRole, ['kasir', 'admin']))
                            <!-- POS -->
                            <a href="{{ route('pos.index') }}" 
                               @click="sidebarOpen = false"
                               class="flex items-center space-x-3 px-4 py-3.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('pos.index') ? 'bg-white/15 text-white shadow-lg backdrop-blur-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span x-show="sidebarExpanded" x-transition class="font-medium whitespace-nowrap">Point of Sale</span>
                            </a>
                        @endif

                        @if($userRole === 'admin')
                            <!-- Admin Dashboard -->
                            <a href="{{ route('admin.dashboard') }}" 
                               @click="sidebarOpen = false"
                               class="flex items-center space-x-3 px-4 py-3.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-white/15 text-white shadow-lg backdrop-blur-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span x-show="sidebarExpanded" x-transition class="font-medium whitespace-nowrap">Laporan Admin</span>
                            </a>

                            <!-- Reports & Finance -->
                            <a href="{{ route('reports.index') }}" 
                               @click="sidebarOpen = false"
                               class="flex items-center space-x-3 px-4 py-3.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('reports.index') ? 'bg-white/15 text-white shadow-lg backdrop-blur-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                                <i class="fa-solid fa-file-invoice-dollar w-5 h-5 flex-shrink-0 text-center"></i>
                                <span x-show="sidebarExpanded" x-transition class="font-medium whitespace-nowrap">Keuangan</span>
                            </a>
                        @endif

                        <!-- Divider -->
                        <div class="border-t border-white/10 my-4"></div>

                        <!-- Profile -->
                        <a href="{{ route('profile') }}" 
                           @click="sidebarOpen = false"
                           class="flex items-center space-x-3 px-4 py-3.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('profile') ? 'bg-white/15 text-white shadow-lg backdrop-blur-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span x-show="sidebarExpanded" x-transition class="font-medium whitespace-nowrap">Profile</span>
                        </a>
                    </nav>

                    <!-- User Info & Logout -->
                    <div class="border-t border-white/10 p-4 space-y-3">
                        <div class="flex items-center space-x-3" x-show="sidebarExpanded" x-transition>
                            <div class="w-11 h-11 bg-gradient-to-br from-amber-400 to-orange-600 rounded-xl flex items-center justify-center shadow-lg ring-2 ring-white/20 flex-shrink-0">
                                <span class="text-white font-bold text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-white font-semibold text-sm truncate">{{ auth()->user()->name }}</p>
                                <p class="text-green-300/80 text-xs capitalize font-medium">{{ auth()->user()->role }}</p>
                            </div>
                        </div>
                        
                        <div x-show="!sidebarExpanded" x-transition class="flex justify-center">
                            <div class="w-11 h-11 bg-gradient-to-br from-amber-400 to-orange-600 rounded-xl flex items-center justify-center shadow-lg ring-2 ring-white/20">
                                <span class="text-white font-bold text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                            </div>
                        </div>
                        
                        <livewire:layout.navigation-logout />
                        
                        <!-- Sidebar Toggle Button (Desktop) -->
                        <button 
                            @click="sidebarExpanded = !sidebarExpanded"
                            class="hidden lg:flex items-center justify-center w-full py-2.5 rounded-lg bg-white/10 hover:bg-white/15 text-white/70 hover:text-white transition-all duration-200">
                            <svg x-show="sidebarExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            </svg>
                            <svg x-show="!sidebarExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="min-h-screen flex flex-col transition-all duration-300" :class="{ 'lg:ml-64': sidebarExpanded, 'lg:ml-20': !sidebarExpanded }">
                <!-- Top Header -->
                <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200/50 sticky top-0 z-30">
                    <div class="h-20 px-4 sm:px-6 lg:px-8 flex items-center">
                        <div class="flex items-center justify-between w-full">
                            <!-- Mobile Menu Button -->
                            <button 
                                @click="sidebarOpen = !sidebarOpen"
                                class="lg:hidden text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#1a4d2e] rounded-lg p-2 transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <!-- Page Title (Dynamic from Slot) -->
                            @if (isset($header))
                                <div class="flex-1 lg:flex-none">
                                    {{ $header }}
                                </div>
                            @endif

                            <!-- User Info (Desktop) -->
                            <div class="hidden lg:flex items-center space-x-4">
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 capitalize font-medium">{{ auth()->user()->role }}</p>
                                </div>
                                <div class="w-11 h-11 bg-gradient-to-br from-amber-700 to-orange-900 rounded-xl flex items-center justify-center shadow-lg ring-2 ring-orange-500/20">
                                    <span class="text-white font-bold text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Scripts -->
        @livewireScripts
        @stack('scripts')
    </body>
</html>
