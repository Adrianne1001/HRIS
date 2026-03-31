<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'HRIS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="auth-wrapper">

            {{-- ===== Left Brand Panel ===== --}}
            <div class="auth-brand-panel">

                {{-- Decorative background circles --}}
                <div class="auth-brand-decor">
                    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="90%" cy="8%"  r="200" fill="rgba(255,255,255,0.04)" />
                        <circle cx="8%"  cy="75%" r="240" fill="rgba(255,255,255,0.04)" />
                        <circle cx="65%" cy="92%" r="130" fill="rgba(255,255,255,0.06)" />
                        <circle cx="18%" cy="18%" r="90"  fill="rgba(255,255,255,0.05)" />
                        <circle cx="50%" cy="50%" r="320" fill="rgba(255,255,255,0.02)" />
                    </svg>
                </div>

                {{-- Logo row --}}
                <div class="auth-brand-logo-row">
                    <x-application-logo class="auth-brand-logo-svg" />
                    <span class="auth-brand-name">HRIS</span>
                </div>

                {{-- Main content --}}
                <div class="auth-brand-content">
                    <h1 class="auth-brand-headline">Streamline Your Workforce Management</h1>
                    <p class="auth-brand-subtitle">
                        A centralized platform for FortiTech's HR operations — from employee records to attendance tracking, all in one place.
                    </p>

                    <div class="auth-brand-features">

                        <div class="auth-brand-feature">
                            <div class="auth-brand-feature-icon">
                                <svg class="auth-brand-feature-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="auth-brand-feature-text">Employee Management</span>
                        </div>

                        <div class="auth-brand-feature">
                            <div class="auth-brand-feature-icon">
                                <svg class="auth-brand-feature-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="auth-brand-feature-text">Work Schedules</span>
                        </div>

                        <div class="auth-brand-feature">
                            <div class="auth-brand-feature-icon">
                                <svg class="auth-brand-feature-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <span class="auth-brand-feature-text">Attendance Tracking</span>
                        </div>

                    </div>
                </div>

                {{-- Copyright --}}
                <p class="auth-brand-footer">© {{ date('Y') }} FortiTech. All rights reserved.</p>

            </div>

            {{-- ===== Right Form Panel ===== --}}
            <div class="auth-form-panel">
                <div class="auth-form-container">

                    {{-- Mobile-only logo --}}
                    <div class="auth-mobile-logo">
                        <x-application-logo class="auth-mobile-logo-svg" />
                        <span class="auth-mobile-logo-name">HRIS</span>
                    </div>

                    {{ $slot }}

                </div>
            </div>

        </div>
    </body>
</html>
