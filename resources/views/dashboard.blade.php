<x-app-layout>
    <x-slot name="header">
        <h2 class="header-title">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Stats Cards -->
    <div class="stat-grid">
        <!-- Total Employees -->
        <div class="stat-card stat-card-hover">
            <div class="flex items-center">
                <div class="stat-card-icon-indigo">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="stat-card-body">
                    <p class="stat-card-label">Total Employees</p>
                    <p class="stat-card-value">0</p>
                </div>
            </div>
        </div>

        <!-- Active Employees -->
        <div class="stat-card stat-card-hover">
            <div class="flex items-center">
                <div class="stat-card-icon-green">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="stat-card-body">
                    <p class="stat-card-label">Active</p>
                    <p class="stat-card-value">0</p>
                </div>
            </div>
        </div>

        <!-- On Leave -->
        <div class="stat-card stat-card-hover">
            <div class="flex items-center">
                <div class="stat-card-icon-yellow">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="stat-card-body">
                    <p class="stat-card-label">On Leave</p>
                    <p class="stat-card-value">0</p>
                </div>
            </div>
        </div>

        <!-- Departments -->
        <div class="stat-card stat-card-hover">
            <div class="flex items-center">
                <div class="stat-card-icon-purple">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="stat-card-body">
                    <p class="stat-card-label">Departments</p>
                    <p class="stat-card-value">9</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-grid">
        <!-- Recent Activity -->
        <div class="dashboard-widget lg:col-span-2">
            <div class="dashboard-widget-header">
                <h3 class="dashboard-widget-title">Recent Activity</h3>
            </div>
            <div class="dashboard-widget-body">
                <p class="empty-state-text">No recent activity</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-widget">
            <div class="dashboard-widget-header">
                <h3 class="dashboard-widget-title">Quick Actions</h3>
            </div>
            <div class="dashboard-widget-body">
                <a href="#" class="quick-action-item">
                    <div class="quick-action-icon-indigo">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <span class="quick-action-label">Add New Employee</span>
                </a>
                <a href="#" class="quick-action-item">
                    <div class="quick-action-icon-green">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="quick-action-label">Record Attendance</span>
                </a>
                <a href="#" class="quick-action-item">
                    <div class="quick-action-icon-yellow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <span class="quick-action-label">Process Payroll</span>
                </a>
                <a href="#" class="quick-action-item">
                    <div class="quick-action-icon-purple">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="quick-action-label">Generate Report</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
