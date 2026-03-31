<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FortiTech HRIS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="font-sans antialiased">

    <nav class="landing-nav">
        <div class="landing-nav-container">
            <div class="landing-nav-logo-row">
                <x-application-logo class="landing-nav-logo-svg" />
                <span class="landing-nav-brand">FortiTech HRIS</span>
            </div>
            <div class="landing-nav-links">
                @auth
                    <a href="{{ url('/dashboard') }}" class="landing-nav-btn-register">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="landing-nav-btn-login">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="landing-nav-btn-register">Get Started</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <section class="landing-hero">
        <div class="landing-hero-inner">
            <div>
                <span class="landing-hero-badge">&#10022; FortiTech HR Platform</span>
                <h1 class="landing-hero-headline">Smarter HR,<br>Stronger Teams</h1>
                <p class="landing-hero-subtext">A modern Human Resource Information System built for growing companies. Manage employees, schedules, and attendance — all in one place.</p>
                @auth
                    <div class="landing-hero-actions">
                        <a href="{{ url('/dashboard') }}" class="landing-hero-btn-primary">Go to Dashboard</a>
                    </div>
                @else
                    <div class="landing-hero-actions">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="landing-hero-btn-primary">Get Started Free</a>
                        @endif
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="landing-hero-btn-secondary">Log In</a>
                        @endif
                    </div>
                @endauth
            </div>
            <div class="landing-hero-visual">
                <div class="landing-hero-card">
                    <div class="landing-hero-card-label">Employees</div>
                    <div class="landing-hero-card-value">—</div>
                </div>
                <div class="landing-hero-card">
                    <div class="landing-hero-card-label">Schedules</div>
                    <div class="landing-hero-card-value">—</div>
                </div>
                <div class="landing-hero-card">
                    <div class="landing-hero-card-label">On Time</div>
                    <div class="landing-hero-card-value">—</div>
                </div>
                <div class="landing-hero-card">
                    <div class="landing-hero-card-label">Attendance</div>
                    <div class="landing-hero-card-value">—</div>
                </div>
            </div>
        </div>
    </section>

    <section class="landing-features" id="features">
        <div class="landing-features-header">
            <span class="landing-features-label">Core Modules</span>
            <h2 class="landing-features-title">Everything you need in one place</h2>
            <p class="landing-features-subtitle">FortiTech HRIS covers every essential HR function so your team can focus on people, not paperwork.</p>
        </div>
        <div class="landing-features-grid">
            <div class="landing-feature-card">
                <div class="landing-feature-icon-wrap">
                    <svg class="landing-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="landing-feature-name">Employee Management</div>
                <div class="landing-feature-desc">Store and manage complete employee profiles including personal details, employment type, department, and status.</div>
            </div>
            <div class="landing-feature-card">
                <div class="landing-feature-icon-wrap">
                    <svg class="landing-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="landing-feature-name">Work Schedules</div>
                <div class="landing-feature-desc">Define shift schedules and assign them to employees. Support for varied work arrangements and patterns.</div>
            </div>
            <div class="landing-feature-card">
                <div class="landing-feature-icon-wrap">
                    <svg class="landing-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="landing-feature-name">Attendance Tracking</div>
                <div class="landing-feature-desc">Record daily time-in and time-out, monitor tardiness, and generate attendance summaries automatically.</div>
            </div>
            <div class="landing-feature-card">
                <div class="landing-feature-icon-wrap">
                    <svg class="landing-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div class="landing-feature-name">Department Overview</div>
                <div class="landing-feature-desc">Organise your workforce by department. Clear picture of headcount and distribution across your organisation.</div>
            </div>
            <div class="landing-feature-card">
                <div class="landing-feature-icon-wrap">
                    <svg class="landing-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="landing-feature-name">Employment Status</div>
                <div class="landing-feature-desc">Track active, inactive, and terminated employees with full audit trails for every status change.</div>
            </div>
            <div class="landing-feature-card">
                <div class="landing-feature-icon-wrap">
                    <svg class="landing-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div class="landing-feature-name">Payroll-Ready Data</div>
                <div class="landing-feature-desc">Structured salary and attendance data ready for payroll processing — reduce manual work and errors.</div>
            </div>
        </div>
    </section>

    <section class="landing-stats">
        <div class="landing-stats-inner">
            <div class="landing-stat-item">
                <div class="landing-stat-value">9</div>
                <div class="landing-stat-label">Departments</div>
            </div>
            <div class="landing-stat-item">
                <div class="landing-stat-value">100%</div>
                <div class="landing-stat-label">Cloud-Based</div>
            </div>
            <div class="landing-stat-item">
                <div class="landing-stat-value">Live</div>
                <div class="landing-stat-label">Attendance</div>
            </div>
            <div class="landing-stat-item">
                <div class="landing-stat-value">Full</div>
                <div class="landing-stat-label">Audit Trails</div>
            </div>
        </div>
    </section>

    <section class="landing-why">
        <div class="landing-why-grid">
            <div class="landing-why-content">
                <span class="landing-why-label">Why FortiTech HRIS</span>
                <h2 class="landing-why-heading">Built for the way HR teams work</h2>
                <p class="landing-why-text">Designed for modern companies, FortiTech HRIS gives HR managers the tools they need to work efficiently and compliantly.</p>
                <ul class="landing-why-list">
                    <li class="landing-why-list-item">
                        <svg class="landing-why-list-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Centralised employee records with complete work history
                    </li>
                    <li class="landing-why-list-item">
                        <svg class="landing-why-list-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Flexible scheduling system for any shift pattern
                    </li>
                    <li class="landing-why-list-item">
                        <svg class="landing-why-list-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Real-time attendance monitoring and reporting
                    </li>
                    <li class="landing-why-list-item">
                        <svg class="landing-why-list-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Secure role-based access and full audit logging
                    </li>
                </ul>
            </div>
            <div class="landing-why-visual">
                <div class="landing-why-card">
                    <div class="landing-why-card-row">
                        <div class="landing-why-card-avatar">JR</div>
                        <div class="landing-why-card-info">
                            <div class="landing-why-card-name">Juan Reyes</div>
                            <div class="landing-why-card-role">Software Engineer</div>
                        </div>
                    </div>
                    <div class="landing-why-card-meta">Operations &bull; Active &bull; Full-Time</div>
                </div>
                <div class="landing-why-card">
                    <div class="landing-why-card-row">
                        <div class="landing-why-card-avatar">MA</div>
                        <div class="landing-why-card-info">
                            <div class="landing-why-card-name">Maria Aquino</div>
                            <div class="landing-why-card-role">HR Specialist</div>
                        </div>
                    </div>
                    <div class="landing-why-card-meta">Human Resources &bull; Active &bull; Regular</div>
                </div>
            </div>
        </div>
    </section>

    <section class="landing-cta">
        <div class="landing-cta-inner">
            <h2 class="landing-cta-headline">Ready to transform your HR operations?</h2>
            <p class="landing-cta-subtext">Join FortiTech HRIS today and bring clarity, efficiency, and control to your human resources management.</p>
            <div class="landing-cta-actions">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="landing-cta-btn-primary">Create Account</a>
                @endif
                <a href="#features" class="landing-cta-btn-secondary">Explore Features</a>
            </div>
        </div>
    </section>

    <footer class="landing-footer">
        <div class="landing-footer-inner">
            <div class="landing-footer-brand">
                <span class="landing-footer-brand-name">FortiTech HRIS</span>
                <p class="landing-footer-tagline">Smarter HR, Stronger Teams.</p>
            </div>
            <p class="landing-footer-copy">&copy; {{ date('Y') }} FortiTech. All rights reserved.</p>
            <div class="landing-footer-links">
                <a href="#" class="landing-footer-link">Privacy</a>
                <a href="#" class="landing-footer-link">Terms</a>
                <a href="#" class="landing-footer-link">Support</a>
            </div>
        </div>
    </footer>

</body>
</html>
