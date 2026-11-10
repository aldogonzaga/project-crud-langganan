@extends('layouts.app')

@section('title', $subscription->name)
@section('page_title', $subscription->name)
@section('page_subtitle', 'Subscription details, renewal schedule, and management.')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        @php
            $days = $subscription->days_until_due;
            $isCancelled = $subscription->status === 'Cancelled';
            $isCritical = !$isCancelled && $days <= 3;
            $isWarning = !$isCancelled && !$isCritical && $days <= 7;
        @endphp

        <!-- Main Subscription Detail Card -->
        <div class="card-custom mb-4 {{ $isCritical ? 'card-critical' : '' }}">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 mb-4 border-bottom" style="border-color: var(--bd) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="service-icon" style="width: 56px; height: 56px; font-size: 2rem;">
                        {!! $subscription->rendered_icon !!}
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="fw-bold mb-0">{{ $subscription->name }}</h3>
                            <span class="badge rounded-pill bg-light text-dark border px-2.5 py-1 small">
                                {{ $subscription->category }}
                            </span>
                        </div>
                        <span class="text-muted small">Added on {{ $subscription->created_at->format('M d, Y') }} &bull; Updated {{ $subscription->updated_at->diffForHumans() }}</span>
                    </div>
                </div>

                <div>
                    @if($subscription->status === 'Active')
                        <span class="badge-pill-custom badge-active fs-6 px-3 py-1.5"><i class="bi bi-check2"></i> Active Subscription</span>
                    @elseif($subscription->status === 'Free Trial')
                        <span class="badge-pill-custom badge-trial fs-6 px-3 py-1.5"><i class="bi bi-hourglass-split"></i> Free Trial</span>
                    @else
                        <span class="badge-pill-custom badge-cancelled fs-6 px-3 py-1.5"><i class="bi bi-x"></i> Cancelled</span>
                    @endif
                </div>
            </div>

            <!-- Cost Breakdown Cards -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-4">
                    <div class="p-3 rounded-4" style="background: var(--sec); border: 1px solid var(--bd);">
                        <span class="small text-muted fw-semibold">Charged Amount</span>
                        <div class="fs-4 fw-bold mt-1" style="color: var(--pri);">
                            {{ Auth::user()->formatCurrency($subscription->cost) }}
                        </div>
                        <span class="small text-muted">Per {{ strtolower($subscription->billing_cycle) }}</span>
                    </div>
                </div>

                <div class="col-12 col-sm-4">
                    <div class="p-3 rounded-4" style="background: var(--okb); border: 1px solid rgba(16, 185, 129, 0.2);">
                        <span class="small text-muted fw-semibold">Monthly Equivalent</span>
                        <div class="fs-4 fw-bold text-success mt-1" style="color: var(--ok) !important;">
                            {{ Auth::user()->formatCurrency($subscription->monthly_cost) }}
                        </div>
                        <span class="small text-muted">Normalized rate</span>
                    </div>
                </div>

                <div class="col-12 col-sm-4">
                    <div class="p-3 rounded-4" style="background: var(--card); border: 1px solid var(--bd);">
                        <span class="small text-muted fw-semibold">Yearly Total</span>
                        <div class="fs-4 fw-bold mt-1" style="color: var(--tx);">
                            {{ Auth::user()->formatCurrency($subscription->yearly_cost) }}
                        </div>
                        <span class="small text-muted">Annual expense</span>
                    </div>
                </div>
            </div>

            <!-- Next Billing & Urgency Highlight -->
            <div class="p-3 rounded-4 mb-4" style="background: {{ $isCritical ? 'var(--dab)' : ($isWarning ? 'var(--wab)' : 'var(--sec)') }}; border: 1px solid {{ $isCritical ? 'var(--da)' : ($isWarning ? 'var(--wa)' : 'var(--bd)') }};">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="small text-muted fw-semibold d-block">Next Scheduled Renewal Date:</span>
                        <span class="fs-5 fw-bold" style="color: {{ $isCritical ? 'var(--da)' : 'var(--tx)' }};">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $subscription->next_billing_date ? $subscription->next_billing_date->format('l, F j, Y') : '—' }}
                        </span>
                    </div>
                    <div>
                        @if($isCancelled)
                            <span class="badge rounded-pill bg-secondary text-white px-3 py-2">
                                <i class="bi bi-pause-circle"></i> Service Cancelled
                            </span>
                        @elseif($days < 0)
                            <span class="badge-pill-custom badge-urgent-critical fs-6 px-3 py-2">
                                <i class="bi bi-exclamation-octagon-fill"></i> Overdue by {{ abs($days) }} days
                            </span>
                        @elseif($days === 0)
                            <span class="badge-pill-custom badge-urgent-critical fs-6 px-3 py-2">
                                <i class="bi bi-bell-fill"></i> Due Today!
                            </span>
                        @elseif($days <= 3)
                            <span class="badge-pill-custom badge-urgent-critical fs-6 px-3 py-2">
                                <i class="bi bi-alarm-fill"></i> Urgent: Due in {{ $days }} days!
                            </span>
                        @elseif($days <= 7)
                            <span class="badge-pill-custom badge-urgent-warning fs-6 px-3 py-2">
                                <i class="bi bi-clock"></i> Due in {{ $days }} days
                            </span>
                        @else
                            <span class="badge-pill-custom badge-urgent-normal fs-6 px-3 py-2">
                                In {{ $days }} days
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description / Notes -->
            <div class="mb-4">
                <h6 class="fw-bold mb-2">Notes & Details</h6>
                <div class="p-3 rounded-3 text-muted" style="background: var(--bg); border: 1px solid var(--bd);">
                    {{ $subscription->notes ?: 'No additional notes provided for this subscription.' }}
                </div>
            </div>

            <!-- Action Controls -->
            <div class="pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color: var(--bd) !important;">
                <a href="{{ route('subscriptions.index') }}" class="btn btn-theme-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Subscriptions
                </a>

                <div class="d-flex gap-2">
                    @if($subscription->status === 'Free Trial')
                        <form action="{{ route('trials.activate', $subscription) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success rounded-pill px-3 py-2 text-white">
                                <i class="bi bi-check2-circle"></i> Convert to Active
                            </button>
                        </form>
                    @endif

                    @if($subscription->status !== 'Cancelled')
                        <form action="{{ route('subscriptions.quickStatus', $subscription) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Cancelled">
                            <button type="submit" class="btn btn-warning rounded-pill px-3 py-2 text-dark">
                                <i class="bi bi-x-circle"></i> Mark Cancelled
                            </button>
                        </form>
                    @else
                        <form action="{{ route('subscriptions.quickStatus', $subscription) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Active">
                            <button type="submit" class="btn btn-outline-success rounded-pill px-3 py-2">
                                <i class="bi bi-arrow-clockwise"></i> Reactivate
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('subscriptions.edit', $subscription) }}" class="btn btn-theme">
                        <i class="bi bi-pencil"></i> Edit
                    </a>

                    <form action="{{ route('subscriptions.destroy', $subscription) }}" method="POST" onsubmit="return confirm('Move {{ $subscription->name }} to Trash?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-theme-danger" title="Move to Trash">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

