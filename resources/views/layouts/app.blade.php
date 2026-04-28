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
    <body class="font-sans antialiased overflow-x-hidden">
        <div class="min-h-screen bg-gray-100" 
             x-data="{ sidebarExpanded: localStorage.getItem('sidebarPinned') === 'true', mobileMenuOpen: false }"
             @sidebar-toggled.window="sidebarExpanded = $event.detail.expanded">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content Area -->
            <div :class="sidebarExpanded ? 'ml-64' : 'ml-20'" class="transition-all duration-300 min-h-screen max-md:!ml-0">
                <!-- Top Header Bar -->
                <header class="header-bar">
                    <div class="header-inner">
                        <div class="flex items-center gap-3">
                            <!-- Mobile Hamburger -->
                            <button @click="mobileMenuOpen = !mobileMenuOpen" class="header-hamburger">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>

                            <!-- Page Heading -->
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>

                        <!-- Right Side Actions -->
                        <div class="header-actions">
                            <!-- Notifications -->
                            <button class="header-action-btn">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </button>

                            <!-- Quick Actions -->
                            <button class="header-action-btn">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="p-6">
                    {{ $slot }}
                </main>
            </div>

            <!-- Mobile sidebar backdrop -->
            <div x-show="mobileMenuOpen" 
                 @click="mobileMenuOpen = false" 
                 class="sidebar-mobile-backdrop"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
            ></div>
        </div>

        <!-- Toast Notifications -->
        <x-toast-notifications />

        <!-- Sync sidebar state -->
        <script>
            document.addEventListener('alpine:init', () => {
                window.addEventListener('storage', (e) => {
                    if (e.key === 'sidebarExpanded') {
                        Alpine.store('sidebar', { expanded: e.newValue === 'true' });
                    }
                });
            });
        </script>
        @stack('scripts')
    </body>
</html>
