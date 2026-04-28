<aside 
    x-data="{ 
        pinned: localStorage.getItem('sidebarPinned') === 'true',
        hovered: false,
        activeGroup: localStorage.getItem('activeGroup') || '',
        get expanded() {
            return this.pinned || this.hovered;
        },
        togglePin() {
            this.pinned = !this.pinned;
            localStorage.setItem('sidebarPinned', this.pinned);
            $dispatch('sidebar-toggled', { expanded: this.expanded });
        },
        toggleGroup(group) {
            this.activeGroup = this.activeGroup === group ? '' : group;
            localStorage.setItem('activeGroup', this.activeGroup);
        }
    }"
    x-init="$dispatch('sidebar-toggled', { expanded: expanded })"
    @mouseenter="hovered = true; $dispatch('sidebar-toggled', { expanded: true })"
    @mouseleave="hovered = false; $dispatch('sidebar-toggled', { expanded: pinned })"
    :class="expanded ? 'sidebar-expanded' : 'sidebar-collapsed'"
    class="sidebar"
>
    <!-- Logo Area -->
    <div class="sidebar-logo-area">
        <a href="{{ route('dashboard') }}" class="flex items-center">
            <div class="sidebar-logo-icon">
                <svg fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                </svg>
            </div>
            <span :class="expanded ? 'sidebar-brand-visible' : 'sidebar-brand-hidden'" class="sidebar-brand">HRIS</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <!-- Main Section -->
        <div class="sidebar-section">
            <p :class="expanded ? '' : 'sidebar-section-label-hidden'" class="sidebar-section-label">Main</p>
            
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">Dashboard</span>
            </a>
        </div>

        <!-- Employee Management Section -->
        <div class="sidebar-section">
            <button @click="toggleGroup('employees')" 
                    :class="expanded ? '' : 'sidebar-section-btn-hidden'"
                    class="sidebar-section-btn">
                <span class="whitespace-nowrap">Employee Management</span>
                <svg :class="activeGroup === 'employees' ? 'sidebar-section-chevron-open' : ''" class="sidebar-section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            <div x-show="expanded ? activeGroup === 'employees' : true" x-collapse>
                <!-- Employees -->
                <a href="{{ route('employees.index') }}" 
                   class="sidebar-link {{ request()->routeIs('employees.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">Employees</span>
                </a>

                <!-- Work Schedules -->
                <a href="{{ route('work-schedules.index') }}" 
                   class="sidebar-link {{ request()->routeIs('work-schedules.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">Work Schedules</span>
                </a>
            </div>
        </div>

        <!-- Time & Attendance Section -->
        <div class="sidebar-section">
            <button @click="toggleGroup('attendance')" 
                    :class="expanded ? '' : 'sidebar-section-btn-hidden'"
                    class="sidebar-section-btn">
                <span class="whitespace-nowrap">Time & Attendance</span>
                <svg :class="activeGroup === 'attendance' ? 'sidebar-section-chevron-open' : ''" class="sidebar-section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            <div x-show="expanded ? activeGroup === 'attendance' : true" x-collapse>
                <!-- DTR (Time In/Out) -->
                <a href="{{ route('attendance.dtr') }}" 
                   class="sidebar-link {{ request()->routeIs('attendance.dtr') || request()->routeIs('attendance.time-in') || request()->routeIs('attendance.time-out') ? 'sidebar-link-active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">DTR</span>
                </a>

                <!-- Attendance History -->
                <a href="{{ route('attendance.index') }}" 
                   class="sidebar-link {{ request()->routeIs('attendance.index') || request()->routeIs('attendance.show') ? 'sidebar-link-active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">Attendance History</span>
                </a>

                <!-- Attendance Calendar (Admin) -->
                <a href="{{ route('attendance.calendar') }}" 
                   class="sidebar-link {{ request()->routeIs('attendance.calendar') ? 'sidebar-link-active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">Attendance Calendar</span>
                </a>

                <!-- Leave Requests -->
                <a href="{{ route('leave-requests.index') }}" 
                   class="sidebar-link {{ request()->routeIs('leave-requests.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">Leave Requests</span>
                </a>

                <!-- My Leave Balances -->
                <a href="{{ route('leave-balances.index') }}" 
                   class="sidebar-link {{ request()->routeIs('leave-balances.index') ? 'sidebar-link-active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">My Balances</span>
                </a>

                <!-- Leave Types -->
                <a href="{{ route('leave-types.index') }}" 
                   class="sidebar-link {{ request()->routeIs('leave-types.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">Leave Types</span>
                </a>

                <!-- Manage Balances -->
                <a href="{{ route('leave-balances.manage') }}" 
                   class="sidebar-link {{ request()->routeIs('leave-balances.manage') ? 'sidebar-link-active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">Manage Balances</span>
                </a>

                <!-- Overtime -->
                <a href="#" 
                   class="sidebar-link {{ request()->routeIs('overtime.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">Overtime</span>
                </a>
            </div>
        </div>

        <!-- Payroll Section -->
        <div class="sidebar-section">
            <button @click="toggleGroup('payroll')" 
                    :class="expanded ? '' : 'sidebar-section-btn-hidden'"
                    class="sidebar-section-btn">
                <span class="whitespace-nowrap">Payroll</span>
                <svg :class="activeGroup === 'payroll' ? 'sidebar-section-chevron-open' : ''" class="sidebar-section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            <div x-show="expanded ? activeGroup === 'payroll' : true" x-collapse>
                <!-- Payroll Periods -->
                <a href="{{ route('payroll.index') }}" 
                   class="sidebar-link {{ request()->routeIs('payroll.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">Payroll Periods</span>
                </a>

                <!-- My Payslips -->
                <a href="{{ route('payslips.index') }}" 
                   class="sidebar-link {{ request()->routeIs('payslips.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">My Payslips</span>
                </a>

                <!-- Employee Loans -->
                <a href="{{ route('employee-loans.index') }}" 
                   class="sidebar-link {{ request()->routeIs('employee-loans.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">Employee Loans</span>
                </a>

                <!-- Holidays -->
                <a href="{{ route('holidays.index') }}" 
                   class="sidebar-link {{ request()->routeIs('holidays.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" transform="translate(9,11) scale(0.35)"/>
                    </svg>
                    <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">Holidays</span>
                </a>
            </div>
        </div>

        <!-- Reports Section -->
        <div class="sidebar-section">
            <button @click="toggleGroup('reports')" 
                    :class="expanded ? '' : 'sidebar-section-btn-hidden'"
                    class="sidebar-section-btn">
                <span class="whitespace-nowrap">Reports</span>
                <svg :class="activeGroup === 'reports' ? 'sidebar-section-chevron-open' : ''" class="sidebar-section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            <div x-show="expanded ? activeGroup === 'reports' : true" x-collapse>
                <!-- Reports -->
                <a href="#" 
                   class="sidebar-link {{ request()->routeIs('reports.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">Reports</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Bottom Section -->
    <div class="sidebar-pin-btn">
        <!-- Settings -->
        <a href="#" 
           class="sidebar-link {{ request()->routeIs('settings.*') ? 'sidebar-link-active' : '' }} mb-2">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text">Settings</span>
        </a>

        <!-- Pin Button -->
        <button @click="togglePin()" 
                class="sidebar-link w-full"
                :class="pinned ? 'sidebar-link-active' : ''"
                :title="pinned ? 'Unpin sidebar' : 'Pin sidebar'">
            <!-- Pinned icon -->
            <svg x-show="pinned" class="sidebar-link-icon" fill="currentColor" viewBox="0 0 24 24">
                <path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/>
            </svg>
            <!-- Unpinned icon -->
            <svg x-show="!pinned" class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/>
            </svg>
            <span :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text" x-text="pinned ? 'Unpin' : 'Pin'"></span>
        </button>

        <!-- User Profile -->
        <div class="sidebar-user-profile">
            <x-dropdown align="top-right" width="48">
                <x-slot name="trigger">
                    <button class="sidebar-link w-full">
                        <div class="sidebar-user-avatar">
                            <span class="text-sm font-medium">{{ substr(Auth::user()->firstName, 0, 1) }}{{ substr(Auth::user()->lastName, 0, 1) }}</span>
                        </div>
                        <div :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-link-text text-left">
                            <p class="sidebar-user-name">{{ Auth::user()->fullName }}</p>
                            <p class="sidebar-user-email">{{ Auth::user()->email }}</p>
                        </div>
                        <svg :class="expanded ? 'sidebar-link-text-visible' : 'sidebar-link-text-hidden'" class="sidebar-section-chevron sidebar-user-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</aside>
