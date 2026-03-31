<x-app-layout>
    <x-slot name="header">
        <div class="header-row-between">
            <div class="header-row">
                <a href="{{ route('leave-types.index') }}" class="back-link">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="header-title">
                    {{ __('Leave Type Details') }}
                </h2>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('leave-types.edit', $leaveType) }}" class="btn-ghost">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                @if($leaveType->leaveRequests->isEmpty())
                    <form action="{{ route('leave-types.destroy', $leaveType) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this leave type? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="page-container">
        <div class="page-content-md">
            {{-- Success Message --}}
            @if (session('success'))
                <div class="alert-success" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Leave Type Information --}}
            <div class="card">
                <div class="card-body">
                    <h3 class="section-title">
                        <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Leave Type Information
                    </h3>

                    <dl class="detail-list">
                        <div>
                            <dt class="detail-term">Name</dt>
                            <dd class="detail-value">{{ $leaveType->name }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Code</dt>
                            <dd class="detail-value">{{ $leaveType->code }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Default Credits</dt>
                            <dd class="detail-value">{{ $leaveType->defaultCredits }} days</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Description</dt>
                            <dd class="detail-value">{{ $leaveType->description ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Paid Status</dt>
                            <dd class="detail-value">
                                <span class="{{ $leaveType->isPaid ? 'badge-success' : 'badge-warning' }}">
                                    {{ $leaveType->isPaid ? 'Paid' : 'Unpaid' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="detail-term">Active Status</dt>
                            <dd class="detail-value">
                                <span class="{{ $leaveType->isActive ? 'badge-success' : 'badge-danger' }}">
                                    {{ $leaveType->isActive ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="detail-term">Requires Document</dt>
                            <dd class="detail-value">{{ $leaveType->requiresDocument ? 'Yes' : 'No' }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Max Consecutive Days</dt>
                            <dd class="detail-value">{{ $leaveType->maxConsecutiveDays ?? 'No limit' }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Gender Restriction</dt>
                            <dd class="detail-value">{{ $leaveType->gender?->value ?? 'All genders' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
