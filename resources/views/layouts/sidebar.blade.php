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
    :class="expanded ? 'w-64' : 'w-20'"
    class="fixed left-0 top-0 h-screen bg-indigo-700 text-white flex flex-col transition-all duration-300 ease-in-out z-50 overflow-hidden"
>
    <!-- Logo Area -->
    <div class="flex items-center h-16 border-b border-indigo-600 px-5">
        <a href="{{ route('dashboard') }}" class="flex items-center">
            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-indigo-700" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                </svg>
            </div>
            <span :class="expanded ? 'opacity-100 ml-3' : 'opacity-0 ml-0 w-0'" class="font-bold text-lg whitespace-nowrap transition-all duration-300 overflow-hidden">HRIS</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4">
        <!-- Main Section -->
        <div class="px-3 mb-6">
            <p :class="expanded ? 'opacity-100' : 'opacity-0'" class="px-3 text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-2 transition-opacity duration-300 whitespace-nowrap">Main</p>
            
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center px-3 py-2.5 rounded-lg mb-1 transition-colors
                      {{ request()->routeIs('dashboard') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600' }}">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span :class="expanded ? 'opacity-100 ml-3' : 'opacity-0 ml-0 w-0'" class="transition-all duration-300 whitespace-nowrap overflow-hidden">Dashboard</span>
            </a>
        </div>

        <!-- Employee Management Section -->
        <div class="px-3 mb-6">
            <button @click="toggleGroup('employees')" 
                    :class="expanded ? 'opacity-100' : 'opacity-0 pointer-events-none h-0 py-0'"
                    class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-indigo-300 uppercase tracking-wider hover:text-white transition-all duration-300">
                <span class="whitespace-nowrap">Employee Management</span>
                <svg :class="activeGroup === 'employees' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            <div x-show="expanded ? activeGroup === 'employees' : true" x-collapse>
                <!-- Employees -->
                <a href="{{ route('employees.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg mb-1 transition-colors
                          {{ request()->routeIs('employees.*') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span :class="expanded ? 'opacity-100 ml-3' : 'opacity-0 ml-0 w-0'" class="transition-all duration-300 whitespace-nowrap overflow-hidden">Employees</span>
                </a>

                <!-- Work Schedules -->
                <a href="{{ route('work-schedules.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg mb-1 transition-colors
                          {{ request()->routeIs('work-schedules.*') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span :class="expanded ? 'opacity-100 ml-3' : 'opacity-0 ml-0 w-0'" class="transition-all duration-300 whitespace-nowrap overflow-hidden">Work Schedules</span>
                </a>
            </div>
        </div>

        <!-- Time & Attendance Section -->
        <div class="px-3 mb-6">
            <button @click="toggleGroup('attendance')" 
                    :class="expanded ? 'opacity-100' : 'opacity-0 pointer-events-none h-0 py-0'"
                    class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-indigo-300 uppercase tracking-wider hover:text-white transition-all duration-300">
                <span class="whitespace-nowrap">Time & Attendance</span>
                <svg :class="activeGroup === 'attendance' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            <div x-show="expanded ? activeGroup === 'attendance' : true" x-collapse>
                <!-- DTR (Time In/Out) -->
                <a href="{{ route('attendance.dtr') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg mb-1 transition-colors
                          {{ request()->routeIs('attendance.dtr') || request()->routeIs('attendance.time-in') || request()->routeIs('attendance.time-out') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span :class="expanded ? 'opacity-100 ml-3' : 'opacity-0 ml-0 w-0'" class="transition-all duration-300 whitespace-nowrap overflow-hidden">DTR</span>
                </a>

                <!-- Attendance History -->
                <a href="{{ route('attendance.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg mb-1 transition-colors
                          {{ request()->routeIs('attendance.index') || request()->routeIs('attendance.show') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span :class="expanded ? 'opacity-100 ml-3' : 'opacity-0 ml-0 w-0'" class="transition-all duration-300 whitespace-nowrap overflow-hidden">Attendance History</span>
                </a>

                <!-- Attendance Calendar (Admin) -->
                <a href="{{ route('attendance.calendar') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg mb-1 transition-colors
                          {{ request()->routeIs('attendance.calendar') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span :class="expanded ? 'opacity-100 ml-3' : 'opacity-0 ml-0 w-0'" class="transition-all duration-300 whitespace-nowrap overflow-hidden">Attendance Calendar</span>
                </a>

                <!-- Leave Management -->
                <a href="#" 
                   class="flex items-center px-3 py-2.5 rounded-lg mb-1 transition-colors
                          {{ request()->routeIs('leaves.*') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span :class="expanded ? 'opacity-100 ml-3' : 'opacity-0 ml-0 w-0'" class="transition-all duration-300 whitespace-nowrap overflow-hidden">Leave Management</span>
                </a>

                <!-- Overtime -->
                <a href="#" 
                   class="flex items-center px-3 py-2.5 rounded-lg mb-1 transition-colors
                          {{ request()->routeIs('overtime.*') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span :class="expanded ? 'opacity-100 ml-3' : 'opacity-0 ml-0 w-0'" class="transition-all duration-300 whitespace-nowrap overflow-hidden">Overtime</span>
                </a>
            </div>
        </div>

        <!-- Payroll Section -->
        <div class="px-3 mb-6">
            <button @click="toggleGroup('payroll')" 
                    :class="expanded ? 'opacity-100' : 'opacity-0 pointer-events-none h-0 py-0'"
                    class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-indigo-300 uppercase tracking-wider hover:text-white transition-all duration-300">
                <span class="whitespace-nowrap">Payroll</span>
                <svg :class="activeGroup === 'payroll' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            <div x-show="expanded ? activeGroup === 'payroll' : true" x-collapse>
                <!-- Payroll Processing -->
                <a href="#" 
                   class="flex items-center px-3 py-2.5 rounded-lg mb-1 transition-colors
                          {{ request()->routeIs('payroll.*') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span :class="expanded ? 'opacity-100 ml-3' : 'opacity-0 ml-0 w-0'" class="transition-all duration-300 whitespace-nowrap overflow-hidden">Payroll</span>
                </a>

                <!-- Salary Structure -->
                <a href="#" 
                   class="flex items-center px-3 py-2.5 rounded-lg mb-1 transition-colors
                          {{ request()->routeIs('salary.*') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span :class="expanded ? 'opacity-100 ml-3' : 'opacity-0 ml-0 w-0'" class="transition-all duration-300 whitespace-nowrap overflow-hidden">Salary Structure</span>
                </a>
            </div>
        </div>

        <!-- Reports Section -->
        <div class="px-3 mb-6">
            <button @click="toggleGroup('reports')" 
                    :class="expanded ? 'opacity-100' : 'opacity-0 pointer-events-none h-0 py-0'"
                    class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-indigo-300 uppercase tracking-wider hover:text-white transition-all duration-300">
                <span class="whitespace-nowrap">Reports</span>
                <svg :class="activeGroup === 'reports' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            <div x-show="expanded ? activeGroup === 'reports' : true" x-collapse>
                <!-- Reports -->
                <a href="#" 
                   class="flex items-center px-3 py-2.5 rounded-lg mb-1 transition-colors
                          {{ request()->routeIs('reports.*') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span :class="expanded ? 'opacity-100 ml-3' : 'opacity-0 ml-0 w-0'" class="transition-all duration-300 whitespace-nowrap overflow-hidden">Reports</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Bottom Section -->
    <div class="border-t border-indigo-600 p-3">
        <!-- Settings -->
        <a href="#" 
           class="flex items-center px-3 py-2.5 rounded-lg mb-2 transition-colors
                  {{ request()->routeIs('settings.*') ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600' }}">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span :class="expanded ? 'opacity-100 ml-3' : 'opacity-0 ml-0 w-0'" class="transition-all duration-300 whitespace-nowrap overflow-hidden">Settings</span>
        </a>

        <!-- Pin Button -->
        <button @click="togglePin()" 
                class="flex items-center px-3 py-2.5 rounded-lg w-full transition-colors"
                :class="pinned ? 'bg-indigo-800 text-white' : 'text-indigo-100 hover:bg-indigo-600'"
                :title="pinned ? 'Unpin sidebar' : 'Pin sidebar'">
            <!-- Pinned icon -->
            <svg x-show="pinned" class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                <path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/>
            </svg>
            <!-- Unpinned icon -->
            <svg x-show="!pinned" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/>
            </svg>
            <span :class="expanded ? 'opacity-100 ml-3' : 'opacity-0 ml-0 w-0'" class="transition-all duration-300 whitespace-nowrap overflow-hidden" x-text="pinned ? 'Unpin' : 'Pin'"></span>
        </button>

        <!-- User Profile -->
        <div class="mt-3 pt-3 border-t border-indigo-600">
            <x-dropdown align="top-right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center w-full px-3 py-2 rounded-lg hover:bg-indigo-600 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-medium">{{ substr(Auth::user()->firstName, 0, 1) }}{{ substr(Auth::user()->lastName, 0, 1) }}</span>
                        </div>
                        <div :class="expanded ? 'opacity-100 ml-3' : 'opacity-0 ml-0 w-0'" class="text-left transition-all duration-300 overflow-hidden">
                            <p class="text-sm font-medium text-white truncate whitespace-nowrap">{{ Auth::user()->fullName }}</p>
                            <p class="text-xs text-indigo-300 truncate whitespace-nowrap">{{ Auth::user()->email }}</p>
                        </div>
                        <svg :class="expanded ? 'opacity-100 ml-auto' : 'opacity-0 w-0'" class="w-4 h-4 text-indigo-300 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
