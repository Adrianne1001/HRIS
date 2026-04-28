<x-app-layout>
    <x-slot name="header">
        <div class="header-row-between">
            <div class="header-row">
                <a href="{{ route('leave-requests.index') }}" class="back-link">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="header-title">
                    {{ __('Leave Request Details') }}
                </h2>
            </div>

            @if($leaveRequest->status->value === 'Pending')
                <div x-data="{ showReject: false }">
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('leave-requests.approve', $leaveRequest) }}">
                            @csrf
                            <button type="submit" class="btn-success" onclick="return confirm('Are you sure you want to approve this leave request?')">Approve</button>
                        </form>
                        <button @click="showReject = !showReject" class="btn-danger" x-text="showReject ? 'Cancel Reject' : 'Reject'"></button>
                        <form method="POST" action="{{ route('leave-requests.cancel', $leaveRequest) }}">
                            @csrf
                            <button type="submit" class="btn-secondary" onclick="return confirm('Are you sure you want to cancel this leave request?')">Cancel Request</button>
                        </form>
                    </div>
                    <div x-show="showReject" x-cloak class="mt-4">
                        <form method="POST" action="{{ route('leave-requests.reject', $leaveRequest) }}">
                            @csrf
                            <div class="card">
                                <div class="card-body">
                                    <x-input-label for="rejectionReason" :value="__('Rejection Reason')" />
                                    <textarea id="rejectionReason" name="rejectionReason" rows="3" class="form-textarea" required>{{ old('rejectionReason') }}</textarea>
                                    <x-input-error :messages="$errors->get('rejectionReason')" />
                                    <div class="mt-4">
                                        <button type="submit" class="btn-danger">Confirm Rejection</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="page-container">
        <div class="page-content-md">
            {{-- Leave Request Information --}}
            <div class="card">
                <div class="card-body">
                    <h3 class="section-title">
                        <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Request Information
                    </h3>

                    <dl class="detail-list">
                        <div>
                            <dt class="detail-term">Employee</dt>
                            <dd class="detail-value">{{ $leaveRequest->employee->user->fullName }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Leave Type</dt>
                            <dd class="detail-value">
                                {{ $leaveRequest->leaveType->name }}
                                <span class="badge badge-info ml-2">{{ $leaveRequest->leaveType->code }}</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="detail-term">Status</dt>
                            <dd class="detail-value">
                                @php
                                    $statusBadge = match($leaveRequest->status->value) {
                                        'Pending' => 'badge-yellow',
                                        'Approved' => 'badge-success',
                                        'Rejected' => 'badge-danger',
                                        'Cancelled' => 'badge-gray',
                                        default => 'badge-gray',
                                    };
                                @endphp
                                <span class="{{ $statusBadge }}">{{ $leaveRequest->status->value }}</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="detail-term">Date Range</dt>
                            <dd class="detail-value">{{ $leaveRequest->startDate->format('M d, Y') }} - {{ $leaveRequest->endDate->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Total Days</dt>
                            <dd class="detail-value">
                                {{ $leaveRequest->totalDays }}
                                @if($leaveRequest->isHalfDay)
                                    <span class="table-cell-sub">(Half-day - {{ $leaveRequest->halfDayPeriod }})</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="detail-term">Reason</dt>
                            <dd class="detail-value">{{ $leaveRequest->reason }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Filed On</dt>
                            <dd class="detail-value">{{ $leaveRequest->created_at->format('M d, Y \a\t h:i A') }}</dd>
                        </div>

                        @if($leaveRequest->status->value === 'Approved')
                            <div>
                                <dt class="detail-term">Approved By</dt>
                                <dd class="detail-value">{{ $leaveRequest->approvedBy->fullName }}</dd>
                            </div>
                            <div>
                                <dt class="detail-term">Approved At</dt>
                                <dd class="detail-value">{{ $leaveRequest->approvedAt->format('M d, Y \a\t h:i A') }}</dd>
                            </div>
                        @endif

                        @if($leaveRequest->status->value === 'Rejected')
                            <div>
                                <dt class="detail-term">Rejected By</dt>
                                <dd class="detail-value">{{ $leaveRequest->approvedBy->fullName }}</dd>
                            </div>
                            <div>
                                <dt class="detail-term">Rejected At</dt>
                                <dd class="detail-value">{{ $leaveRequest->approvedAt->format('M d, Y \a\t h:i A') }}</dd>
                            </div>
                            <div>
                                <dt class="detail-term">Rejection Reason</dt>
                                <dd class="detail-value">{{ $leaveRequest->rejectionReason }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
