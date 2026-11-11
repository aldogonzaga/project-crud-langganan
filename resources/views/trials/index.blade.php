@extends('layouts.app')

@section('title', 'Free Trials Watchdog')
@section('page_title', 'Free Trials Watchdog')
@section('page_subtitle', 'Never get charged by surprise again. Cancel unwanted trials before they convert to paid subscriptions.')

@section('content')
<!-- Header Banner with Potential Savings / Pending Charge -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="metric-card">
            <span class="metric-label">Active Trials Tracked</span>
            <div class="metric-value" style="color: var(--wa) !important;">
                {{ $trials->count() }}
            </div>
            <span class="small text-muted">Currently in free trial periods</span>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="metric-card">
            <span class="metric-label">Urgent (Ending ≤ 3 Days)</span>
            <div class="metric-value {{ $urgentTrials->count() > 0 ? 'text-danger' : 'text-success' }}" style="{{ $urgentTrials->count() > 0 ? 'color: var(--da) !important;' : 'color: var(--ok) !important;' }}">
                {{ $urgentTrials->count() }}
            </div>
            <span class="small text-muted">{{ $urgentTrials->count() > 0 ? 'Immediate action required!' : 'No imminent deadlines' }}</span>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="metric-card">
            <span class="metric-label">Pending Charges if Uncancelled</span>
            <div class="metric-value" style="color: var(--pri) !important;">
                {{ Auth::user()->formatCurrency($potentialCharge) }}
            </div>
            <span class="small text-muted">Total auto-renewal exposure</span>
        </div>
    </div>
</div>

@if($trials->isNotEmpty())
    <!-- Section 1: Urgent Trials (≤ 3 Days) -->
    @if($urgentTrials->isNotEmpty())
        <div class="mb-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge rounded-pill bg-danger text-white px-3 py-1.5 fw-bold">
                    <i class="bi bi-fire"></i> CRITICAL: Ending within 3 Days!
                </span>
                <span class="small text-muted">Cancel immediately if you do not plan to pay for renewal.</span>
            </div>

            <div class="row g-3">
                @foreach($urgentTrials as $trial)
                    @php
                        $dLeft = $trial->days_until_due;
                        $progress = max(10, min(100, 100 - ($dLeft / 14 * 100)));
                    @endphp
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card-custom h-100 card-critical d-flex flex-column justify-content-between p-4" style="background: var(--dab);">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="service-icon" style="background: #ffffff;">
                                            {!! $trial->rendered_icon !!}
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-0">{{ $trial->name }}</h5>
                                            <span class="badge rounded-pill bg-white text-danger border border-danger-subtle small px-2 py-0.5">
                                                {{ $trial->category }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="badge-pill-custom badge-urgent-critical">
                                        <i class="bi bi-alarm-fill"></i> {{ $dLeft <= 0 ? 'Due Today' : $dLeft . 'd left' }}
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <span class="small text-muted fw-semibold d-block">Renewal Cost:</span>
                                    <div class="fs-4 fw-bold text-danger">
                                        {{ Auth::user()->formatCurrency($trial->cost) }}
                                        <span class="fs-6 fw-normal text-muted">/{{ strtolower($trial->billing_cycle) }}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>Trial Timeline</span>
                                        <span class="fw-bold text-danger">Ends {{ $trial->next_billing_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="trial-bar">
                                        <div class="trial-bar-fill danger" style="width: {{ $progress }}%;"></div>
                                    </div>
                                </div>

                                @if($trial->notes)
                                    <p class="small text-muted mb-3">{{ $trial->notes }}</p>
                                @endif
                            </div>

                            <div class="d-flex gap-2 pt-3 border-top border-danger-subtle">
                                <form action="{{ route('trials.cancel', $trial) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Cancel free trial for {{ $trial->name }}? You will not be charged.')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-theme-danger w-100 justify-content-center">
                                        <i class="bi bi-x-circle-fill"></i> Cancel Trial (Save Money)
                                    </button>
                                </form>

                                <form action="{{ route('trials.activate', $trial) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 py-2" title="Keep and convert to Active">
                                        <i class="bi bi-check-lg"></i> Keep
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Section 2: Other Active Free Trials -->
    @php
        $otherTrials = $trials->filter(fn ($t) => $t->days_until_due > 3);
    @endphp

    @if($otherTrials->isNotEmpty())
        <div class="mb-4">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-shield-check me-2 text-success"></i>
                Upcoming Free Trials
            </h5>

            <div class="row g-3">
                @foreach($otherTrials as $trial)
                    @php
                        $dLeft = $trial->days_until_due;
                        $isWarning = $dLeft <= 7;
                        $progress = max(5, min(100, 100 - ($dLeft / 14 * 100)));
                    @endphp
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card-custom h-100 d-flex flex-column justify-content-between p-4 {{ $isWarning ? 'card-warning' : '' }}">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="service-icon">
                                            {!! $trial->rendered_icon !!}
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-0">{{ $trial->name }}</h5>
                                            <span class="badge rounded-pill bg-light text-muted border small px-2 py-0.5">
                                                {{ $trial->category }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="badge-pill-custom {{ $isWarning ? 'badge-urgent-warning' : 'badge-active' }}">
                                        {{ $dLeft }} days left
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <span class="small text-muted fw-semibold d-block">Renewal Rate:</span>
                                    <div class="fs-4 fw-bold">
                                        {{ Auth::user()->formatCurrency($trial->cost) }}
                                        <span class="fs-6 fw-normal text-muted">/{{ strtolower($trial->billing_cycle) }}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>Trial Timeline</span>
                                        <span>Ends {{ $trial->next_billing_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="trial-bar">
                                        <div class="trial-bar-fill {{ $isWarning ? 'warning' : '' }}" style="width: {{ $progress }}%;"></div>
                                    </div>
                                </div>

                                @if($trial->notes)
                                    <p class="small text-muted mb-3">{{ $trial->notes }}</p>
                                @endif
                            </div>

                            <div class="d-flex gap-2 pt-3 border-top" style="border-color: var(--bd) !important;">
                                <form action="{{ route('trials.cancel', $trial) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Cancel free trial for {{ $trial->name }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-theme-secondary w-100 justify-content-center text-danger">
                                        <i class="bi bi-x-circle"></i> Cancel Trial
                                    </button>
                                </form>

                                <form action="{{ route('trials.activate', $trial) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 py-2" title="Convert to Active">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>

                                <a href="{{ route('subscriptions.edit', $trial) }}" class="btn btn-sm btn-circle" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@else
    <div class="card-custom text-center py-5">
        <h4 class="fw-bold mb-1">No Active Free Trials Tracked</h4>
        <p class="text-muted small mb-4" style="max-width: 440px; margin: 0 auto;">
            Track free software trials or introductory memberships with their auto-renewal dates to make sure you cancel before money leaves your bank.
        </p>
        <a href="{{ route('subscriptions.create') }}" class="btn btn-theme">
            <i class="bi bi-plus-lg"></i> Add a Free Trial
        </a>
    </div>
@endif
@endsection

