<x-app-layout>
    <x-slot name="header">
        <h2 class="header-title">{{ __('My Leave Balances') }} — {{ $year }}</h2>
    </x-slot>

    <div class="page-container">
        <div class="page-content">
            @if (session('success'))
                <div class="alert-success" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($balances->isEmpty())
                <div class="card">
                    <div class="card-body">
                        <p class="balance-empty-text">No leave balances allocated for {{ $year }}. Please contact HR.</p>
                    </div>
                </div>
            @else
                <div class="balance-grid">
                    @foreach($balances as $balance)
                        <div class="card">
                            <div class="card-body">
                                <div class="balance-card-header">
                                    <h3 class="balance-card-title">{{ $balance->leaveType->name }}</h3>
                                    <span class="badge badge-info">{{ $balance->leaveType->code }}</span>
                                </div>

                                {{-- Progress bar --}}
                                @php
                                    $usedPercent = $balance->totalCredits > 0 ? ($balance->usedCredits / $balance->totalCredits) * 100 : 0;
                                    $pendingPercent = $balance->totalCredits > 0 ? ($balance->pendingCredits / $balance->totalCredits) * 100 : 0;
                                @endphp
                                <div class="progress-track">
                                    <div class="progress-inner">
                                        <div class="progress-used" style="width: {{ $usedPercent }}%"></div>
                                        <div class="progress-pending" style="width: {{ $pendingPercent }}%"></div>
                                    </div>
                                </div>

                                <div class="balance-stats">
                                    <div class="balance-stat-row">
                                        <span class="balance-stat-label">Total Credits</span>
                                        <span class="balance-stat-value">{{ $balance->totalCredits }} days</span>
                                    </div>
                                    <div class="balance-stat-row">
                                        <span class="balance-stat-label">Used</span>
                                        <span class="balance-stat-value-used">{{ $balance->usedCredits }} days</span>
                                    </div>
                                    <div class="balance-stat-row">
                                        <span class="balance-stat-label">Pending</span>
                                        <span class="balance-stat-value-pending">{{ $balance->pendingCredits }} days</span>
                                    </div>
                                    <div class="balance-stat-row-total">
                                        <span class="balance-stat-label-total">Available</span>
                                        <span class="balance-stat-value-available">{{ number_format($balance->remainingCredits, 2) }} days</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
