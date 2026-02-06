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
             x-data="{ sidebarExpanded: localStorage.getItem('sidebarPinned') === 'true' }"
             @sidebar-toggled.window="sidebarExpanded = $event.detail.expanded">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content Area -->
            <div :class="sidebarExpanded ? 'ml-64' : 'ml-20'" class="transition-all duration-300 min-h-screen">
                <!-- Top Header Bar -->
                <header class="bg-white shadow-sm sticky top-0 z-40">
                    <div class="flex items-center justify-between px-6 py-4">
                        <!-- Page Heading -->
                        @isset($header)
                            <div>
                                {{ $header }}
                            </div>
                        @else
                            <div></div>
                        @endisset

                        <!-- Right Side Actions -->
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </button>

                            <!-- Quick Actions -->
                            <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
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
        </div>

        <!-- Sync sidebar state -->
        <script>
            document.addEventListener('alpine:init', () => {
                // Watch for sidebar toggle changes
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
