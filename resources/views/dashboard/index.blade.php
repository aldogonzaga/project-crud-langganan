@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Subscription Dashboard')
@section('page_subtitle', 'Real-time overview of your recurring expenses, renewals, and free trials.')

@section('content')
    <!-- Top Metrics Overview Grid -->
    <div class="row g-3 mb-4">
        <!-- Metric 1: Total Monthly Spend -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="metric-label">Total Monthly Spend</span>
                    <div class="metric-icon-box" style="background: var(--sec); color: var(--pri);">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
                <div>
                    <div class="metric-value text-primary" style="color: var(--pri) !important;">
                        {{ $user->formatCurrency($totalMonthlySpend) }}
                    </div>
                    <div class="small text-muted d-flex align-items-center gap-1">
                        <i class="bi bi-calendar3"></i>
                        <span>Est. {{ $user->formatCurrency($totalYearlySpend) }}/year</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric 2: Active Subscriptions -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="metric-label">Active Services</span>
                    <div class="metric-icon-box" style="background: var(--okb); color: var(--ok);">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                </div>
                <div>
                    <div class="metric-value text-success" style="color: var(--ok) !important;">
                        {{ $activeSubscriptions->count() }}
                    </div>
                    <div class="small text-muted d-flex align-items-center gap-1">
                        <a href="{{ route('subscriptions.index', ['status' => 'Active']) }}" class="text-decoration-none text-muted">
                            View active list <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric 3: Active Free Trials -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card h-100 {{ $trialsEndingSoon->where('days_until_due', '<=', 3)->count() > 0 ? 'card-critical' : '' }}">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="metric-label">Free Trials</span>
                    <div class="metric-icon-box" style="background: var(--wab); color: var(--wa);">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
                <div>
                    <div class="metric-value text-warning" style="color: var(--wa) !important;">
                        {{ $freeTrials->count() }}
                    </div>
                    <div class="small text-muted d-flex align-items-center gap-1">
                        @php
                            $endingCritical = $trialsEndingSoon->where('days_until_due', '<=', 3)->count();
                        @endphp
                        @if($endingCritical > 0)
                            <span class="text-danger fw-bold"><i class="bi bi-exclamation-circle-fill"></i> {{ $endingCritical }} ending in ≤ 3 days!</span>
                        @else
                            <span>Manage before renewal</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric 4: Due in 7 Days -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card h-100 {{ $criticalPaymentsCount > 0 ? 'card-critical' : '' }}">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="metric-label">Due Next 7 Days</span>
                    <div class="metric-icon-box" style="background: {{ $criticalPaymentsCount > 0 ? 'var(--dab)' : 'var(--sec)' }}; color: {{ $criticalPaymentsCount > 0 ? 'var(--da)' : 'var(--pri)' }};">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="metric-value {{ $criticalPaymentsCount > 0 ? 'text-danger' : '' }}" style="{{ $criticalPaymentsCount > 0 ? 'color: var(--da) !important;' : '' }}">
                        {{ $upcomingPayments->count() }}
                    </div>
                    <div class="small text-muted d-flex align-items-center gap-1">
                        @if($criticalPaymentsCount > 0)
                            <span class="text-danger fw-bold"><i class="bi bi-fire"></i> {{ $criticalPaymentsCount }} due within 3 days!</span>
                        @else
                            <span>No urgent bills due</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Red Alert Callout if Critical Payments are Due in <= 3 Days -->
    @if($criticalPaymentsCount > 0)
        <div class="card-custom mb-4 p-3 d-flex flex-row align-items-center justify-content-between flex-wrap gap-2" style="background: var(--dab); border: 2px solid var(--da);">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-grid place-items-center text-white" style="width: 40px; height: 40px; background: var(--da); font-size: 1.2rem; flex-shrink: 0;">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold" style="color: var(--da);">Urgent Payment Alert: {{ $criticalPaymentsCount }} subscription(s) due within 3 days!</h6>
                    <span class="small" style="color: var(--tx);">Review your subscriptions below to prevent unwanted renewals or maintain sufficient funds.</span>
                </div>
            </div>
            <a href="#due-in-7-days" class="btn btn-sm btn-theme-danger">
                Review Upcoming Bills <i class="bi bi-arrow-down-short"></i>
            </a>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <!-- Specialized View: Payments due in the next 7 days -->
        <div class="col-12 col-lg-7" id="due-in-7-days">
            <div class="card-custom h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-calendar-event me-2 text-primary" style="color: var(--pri) !important;"></i>
                            Payments Due in Next 7 Days
                        </h5>
                        <p class="text-muted small mb-0">Upcoming automatic renewals and trial deadlines.</p>
                    </div>
                    <span class="badge rounded-pill px-3 py-2" style="background: var(--sec); color: var(--pri); font-weight: 700;">
                        {{ $upcomingPayments->count() }} bills
                    </span>
                </div>

                @if($upcomingPayments->isNotEmpty())
                    <div class="d-flex flex-column gap-2">
                        @foreach($upcomingPayments as $sub)
                            @php
                                $days = $sub->days_until_due;
                                $isCritical = $days <= 3; // Date Logic: highlight subscriptions in red if within 3 days
                            @endphp
                            <div class="p-3 rounded-4 d-flex align-items-center justify-content-between flex-wrap gap-2 transition-all {{ $isCritical ? 'card-critical' : '' }}" 
                                 style="background: {{ $isCritical ? 'var(--dab)' : 'var(--card)' }}; border: 1.5px solid {{ $isCritical ? 'var(--da)' : 'var(--bd)' }};">
                                
                                <div class="d-flex align-items-center gap-3">
                                    <div class="service-icon" style="background: {{ $isCritical ? '#ffffff' : 'var(--sec)' }};">
                                        {{ $sub->display_icon }}
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ route('subscriptions.show', $sub) }}" class="fw-bold text-decoration-none text-reset">
                                                {{ $sub->name }}
                                            </a>
                                            <span class="badge rounded-pill small px-2 py-1" style="background: var(--sec); color: var(--mu); font-size: 0.72rem;">
                                                {{ $sub->category }}
                                            </span>
                                            @if($sub->status === 'Free Trial')
                                                <span class="badge-pill-custom badge-trial">⏳ Free Trial</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted mt-1">
                                            Due {{ $sub->next_billing_date->format('M d, Y') }} &bull;
                                            @if($days < 0)
                                                <span class="text-danger fw-bold">Overdue by {{ abs($days) }} days</span>
                                            @elseif($days === 0)
                                                <span class="text-danger fw-bold">Due TODAY!</span>
                                            @elseif($days <= 3)
                                                <span class="text-danger fw-bold"><i class="bi bi-clock-history"></i> Due in {{ $days }} {{ Str::plural('day', $days) }} (Urgent)</span>
                                            @else
                                                <span class="text-warning fw-bold">Due in {{ $days }} days</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <div class="text-end">
                                        <div class="fw-bold fs-6" style="color: {{ $isCritical ? 'var(--da)' : 'var(--tx)' }};">
                                            {{ $user->formatCurrency($sub->cost) }}
                                        </div>
                                        <div class="text-muted small">
                                            /{{ strtolower($sub->billing_cycle) }}
                                        </div>
                                    </div>

                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-circle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0 py-2">
                                            <li><a class="dropdown-item py-2 small" href="{{ route('subscriptions.edit', $sub) }}"><i class="bi bi-pencil me-2 text-muted"></i>Edit Price / Date</a></li>
                                            @if($sub->status === 'Free Trial')
                                                <li>
                                                    <form action="{{ route('trials.cancel', $sub) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item py-2 small text-danger"><i class="bi bi-x-circle me-2"></i>Cancel Trial Now</button>
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <form action="{{ route('subscriptions.quickStatus', $sub) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="Cancelled">
                                                        <button type="submit" class="dropdown-item py-2 small text-warning"><i class="bi bi-pause-circle me-2"></i>Mark Cancelled</button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="display-6 mb-2 text-muted">🏖️</div>
                        <h6 class="fw-bold mb-1">Clear Horizon!</h6>
                        <p class="text-muted small mb-3">No payments or renewal dates due in the next 7 days.</p>
                        <a href="{{ route('subscriptions.create') }}" class="btn btn-sm btn-theme-secondary">
                            <i class="bi bi-plus-lg"></i> Add another subscription
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Free Trials Management Widget -->
        <div class="col-12 col-lg-5">
            <div class="card-custom h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-hourglass-split me-2 text-warning"></i>
                            Free Trials Watchdog
                        </h5>
                        <p class="text-muted small mb-0">Cancel before trials end to prevent unexpected charges.</p>
                    </div>
                    <a href="{{ route('trials.index') }}" class="btn btn-sm btn-theme-secondary">
                        View All
                    </a>
                </div>

                @if($freeTrials->isNotEmpty())
                    <div class="d-flex flex-column gap-3">
                        @foreach($trialsEndingSoon->take(3) as $trial)
                            @php
                                $dLeft = $trial->days_until_due;
                                $isUrgent = $dLeft <= 3;
                                $progress = max(5, min(100, 100 - ($dLeft / 14 * 100)));
                            @endphp
                            <div class="p-3 rounded-4 {{ $isUrgent ? 'card-critical' : '' }}" 
                                 style="background: {{ $isUrgent ? 'var(--dab)' : 'var(--card)' }}; border: 1px solid {{ $isUrgent ? 'var(--da)' : 'var(--bd)' }};">
                                
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fs-5">{{ $trial->display_icon }}</span>
                                        <span class="fw-bold">{{ $trial->name }}</span>
                                    </div>
                                    @if($isUrgent)
                                        <span class="badge-pill-custom badge-urgent-critical">
                                            <i class="bi bi-alarm-fill"></i> Ends in {{ $dLeft <= 0 ? 'Today' : $dLeft . ' days' }}
                                        </span>
                                    @else
                                        <span class="badge-pill-custom badge-trial">
                                            {{ $dLeft }} days left
                                        </span>
                                    @endif
                                </div>

                                <div class="trial-bar mb-2">
                                    <div class="trial-bar-fill {{ $isUrgent ? 'danger' : 'warning' }}" style="width: {{ $progress }}%;"></div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mt-2 pt-1">
                                    <span class="small text-muted">
                                        Auto-renews at <strong>{{ $user->formatCurrency($trial->cost) }}</strong>
                                    </span>
                                    <form action="{{ route('trials.cancel', $trial) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-theme-danger py-1 px-3" onclick="return confirm('Cancel trial for {{ $trial->name }}?')">
                                            <i class="bi bi-x-circle"></i> Cancel Trial
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="display-6 mb-2 text-muted">✨</div>
                        <h6 class="fw-bold mb-1">No Active Free Trials</h6>
                        <p class="text-muted small mb-3">You don't have any free trials pending auto-renewal.</p>
                        <a href="{{ route('subscriptions.create') }}" class="btn btn-sm btn-theme-secondary">
                            Track a new free trial
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Visualizations: Spending by Category Chart & Billing Cycles -->
    <div class="row g-4 mb-4">
        <!-- Category Doughnut Chart -->
        <div class="col-12 col-lg-6">
            <div class="card-custom h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-pie-chart-fill me-2 text-primary" style="color: var(--pri) !important;"></i>
                            Spending by Category
                        </h5>
                        <p class="text-muted small mb-0">Monthly budget distribution across categories.</p>
                    </div>
                    <a href="{{ route('analytics.index') }}" class="btn btn-sm btn-theme-secondary">
                        Deep Analytics <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                @if(count($categoryBreakdown) > 0)
                    <div class="row align-items-center py-2">
                        <div class="col-12 col-sm-6 text-center mb-3 mb-sm-0">
                            <div style="max-width: 220px; margin: 0 auto; position: relative;">
                                <canvas id="categoryDonutChart"></canvas>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="d-flex flex-column gap-2">
                                @php
                                    $catPalette = [
                                        'Entertainment' => '#6366f1',
                                        'Utilities' => '#0ea5e9',
                                        'Health' => '#10b981',
                                        'Work' => '#8b5cf6',
                                        'Education' => '#f59e0b',
                                        'Food' => '#ec4899',
                                        'Shopping' => '#14b8a6',
                                        'Others' => '#94a3b8',
                                    ];
                                @endphp
                                @foreach($categoryBreakdown as $catName => $amount)
                                    @php
                                        $pct = $totalMonthlySpend > 0 ? round(($amount / $totalMonthlySpend) * 100) : 0;
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-between small">
                                        <div class="d-flex align-items-center gap-2">
                                            <span style="width: 10px; height: 10px; border-radius: 50%; background-color: {{ $catPalette[$catName] ?? '#94a3b8' }};"></span>
                                            <span>{{ $catName }}</span>
                                        </div>
                                        <div class="fw-bold">
                                            {{ $user->formatCurrency($amount) }}
                                            <span class="text-muted fw-normal">({{ $pct }}%)</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <p class="text-muted mb-0">No active spending to display chart yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Billing Cycle Distribution & Quick Summary -->
        <div class="col-12 col-lg-6">
            <div class="card-custom h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-bar-chart-steps me-2 text-info"></i>
                            Billing Cycle Distribution
                        </h5>
                        <p class="text-muted small mb-0">Frequency breakdown of active subscriptions.</p>
                    </div>
                </div>

                <div class="row g-3 py-2 mb-3">
                    <div class="col-4">
                        <div class="p-3 rounded-4 text-center" style="background: var(--sec); border: 1px solid var(--bd);">
                            <span class="small text-muted d-block fw-semibold">Monthly</span>
                            <div class="fs-4 fw-bold mt-1" style="color: var(--pri);">{{ $cycleBreakdown['Monthly'] }}</div>
                            <span class="small text-muted">Recurring</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded-4 text-center" style="background: var(--okb); border: 1px solid rgba(16, 185, 129, 0.2);">
                            <span class="small text-muted d-block fw-semibold">Yearly</span>
                            <div class="fs-4 fw-bold text-success mt-1" style="color: var(--ok) !important;">{{ $cycleBreakdown['Yearly'] }}</div>
                            <span class="small text-muted">Annual savings</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded-4 text-center" style="background: var(--wab); border: 1px solid rgba(245, 158, 11, 0.2);">
                            <span class="small text-muted d-block fw-semibold">Weekly</span>
                            <div class="fs-4 fw-bold text-warning mt-1" style="color: var(--wa) !important;">{{ $cycleBreakdown['Weekly'] }}</div>
                            <span class="small text-muted">Frequent bills</span>
                        </div>
                    </div>
                </div>

                <div style="height: 140px;">
                    <canvas id="cycleBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Subscriptions Table -->
    <div class="card-custom">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-1">
                    <i class="bi bi-clock-history me-2 text-muted"></i>
                    Recently Added Subscriptions
                </h5>
                <p class="text-muted small mb-0">Quick look at your recent entries.</p>
            </div>
            <a href="{{ route('subscriptions.index') }}" class="btn btn-sm btn-theme-secondary">
                View All ({{ $subscriptions->count() }})
            </a>
        </div>

        @if($recentSubscriptions->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Service Name</th>
                            <th>Category</th>
                            <th>Cost & Cycle</th>
                            <th>Next Billing</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSubscriptions as $sub)
                            @php
                                $dLeft = $sub->days_until_due;
                                $isCrit = $dLeft <= 3 && $sub->status !== 'Cancelled';
                            @endphp
                            <tr class="{{ $isCrit ? 'table-danger-subtle' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="service-icon" style="width: 38px; height: 38px; font-size: 1.15rem;">
                                            {{ $sub->display_icon }}
                                        </div>
                                        <div>
                                            <a href="{{ route('subscriptions.show', $sub) }}" class="fw-bold text-decoration-none text-reset">
                                                {{ $sub->name }}
                                            </a>
                                            @if($sub->notes)
                                                <div class="text-muted small text-truncate" style="max-width: 220px;">
                                                    {{ $sub->notes }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: var(--sec); color: var(--mu); font-weight: 600;">
                                        {{ $sub->category }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $user->formatCurrency($sub->cost) }}</div>
                                    <div class="text-muted small">{{ $sub->billing_cycle }} (≈ {{ $user->formatCurrency($sub->monthly_cost) }}/mo)</div>
                                </td>
                                <td>
                                    <div>{{ $sub->next_billing_date ? $sub->next_billing_date->format('M d, Y') : '—' }}</div>
                                    @if($sub->status !== 'Cancelled')
                                        @if($dLeft <= 3)
                                            <span class="badge-pill-custom badge-urgent-critical">🔴 Due in {{ $dLeft <= 0 ? 'Today' : $dLeft . 'd' }}</span>
                                        @elseif($dLeft <= 7)
                                            <span class="badge-pill-custom badge-urgent-warning">🟡 In {{ $dLeft }}d</span>
                                        @else
                                            <span class="small text-muted">In {{ $dLeft }}d</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($sub->status === 'Active')
                                        <span class="badge-pill-custom badge-active"><i class="bi bi-check2"></i> Active</span>
                                    @elseif($sub->status === 'Free Trial')
                                        <span class="badge-pill-custom badge-trial"><i class="bi bi-hourglass-split"></i> Free Trial</span>
                                    @else
                                        <span class="badge-pill-custom badge-cancelled"><i class="bi bi-x"></i> Cancelled</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('subscriptions.edit', $sub) }}" class="btn btn-sm btn-circle" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('subscriptions.show', $sub) }}" class="btn btn-sm btn-circle" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <div class="display-6 mb-2 text-muted">📦</div>
                <h6 class="fw-bold mb-1">No Subscriptions Tracked Yet</h6>
                <p class="text-muted small mb-3">Add your first subscription to get started!</p>
                <a href="{{ route('subscriptions.create') }}" class="btn btn-theme">
                    <i class="bi bi-plus-lg"></i> Add Subscription
                </a>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const textColor = isDarkMode ? '#f8fafc' : '#0f172a';

        // 1. Category Donut Chart
        const catLabels = @json(array_keys($categoryBreakdown));
        const catData = @json(array_values($categoryBreakdown));
        const catColors = [
            '#6366f1', '#0ea5e9', '#10b981', '#8b5cf6', '#f59e0b', '#ec4899', '#14b8a6', '#94a3b8'
        ];

        const donutCanvas = document.getElementById('categoryDonutChart');
        if (donutCanvas && catData.length > 0) {
            new Chart(donutCanvas, {
                type: 'doughnut',
                data: {
                    labels: catLabels,
                    datasets: [{
                        data: catData,
                        backgroundColor: catColors.slice(0, catLabels.length),
                        borderWidth: 2,
                        borderColor: isDarkMode ? '#131b2e' : '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return ' ' + ctx.label + ': {{ $user->currency }}' + ctx.raw.toFixed(2) + '/mo';
                                }
                            }
                        }
                    }
                }
            });
        }

        // 2. Billing Cycle Bar Chart
        const cycleCanvas = document.getElementById('cycleBarChart');
        if (cycleCanvas) {
            new Chart(cycleCanvas, {
                type: 'bar',
                data: {
                    labels: ['Monthly', 'Yearly', 'Weekly'],
                    datasets: [{
                        label: 'Active Count',
                        data: [
                            {{ $cycleBreakdown['Monthly'] }},
                            {{ $cycleBreakdown['Yearly'] }},
                            {{ $cycleBreakdown['Weekly'] }}
                        ],
                        backgroundColor: ['#6366f1', '#10b981', '#f59e0b'],
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, color: isDarkMode ? '#94a3b8' : '#64748b' },
                            grid: { color: isDarkMode ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            ticks: { color: isDarkMode ? '#94a3b8' : '#64748b' },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection

