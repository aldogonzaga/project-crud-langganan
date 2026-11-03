@extends('layouts.app')

@section('title', 'Visual Analytics')
@section('page_title', 'Spending Analytics & Projections')
@section('page_subtitle', 'Interactive visual breakdown of your subscription commitments and category distribution.')

@section('content')
<!-- High Level Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card">
            <span class="metric-label">Monthly Commitments</span>
            <div class="metric-value" style="color: var(--pri) !important;">
                {{ $user->formatCurrency($totalMonthly) }}
            </div>
            <span class="small text-muted">Across {{ $activeSubs->count() }} active subscriptions</span>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card">
            <span class="metric-label">Annual Projected Cost</span>
            <div class="metric-value text-info">
                {{ $user->formatCurrency($totalYearly) }}
            </div>
            <span class="small text-muted">Total 12-month projection</span>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card">
            <span class="metric-label">Average Subscription Cost</span>
            <div class="metric-value">
                {{ $user->formatCurrency($averageMonthlyCost) }}
            </div>
            <span class="small text-muted">Per active service / month</span>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card">
            <span class="metric-label">Monthly Savings (Cancelled)</span>
            <div class="metric-value text-success" style="color: var(--ok) !important;">
                {{ $user->formatCurrency($monthlySavings) }}
            </div>
            <span class="small text-muted">Est. {{ $user->formatCurrency($yearlySavings) }}/yr saved</span>
        </div>
    </div>
</div>

<!-- Main Charts Row -->
<div class="row g-4 mb-4">
    <!-- Chart 1: Interactive Category Doughnut Chart -->
    <div class="col-12 col-lg-7">
        <div class="card-custom h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-pie-chart-fill me-2 text-primary" style="color: var(--pri) !important;"></i>
                        Monthly Spending by Category
                    </h5>
                    <p class="text-muted small mb-0">Percentage breakdown of your monthly subscriptions.</p>
                </div>
            </div>

            @if(count($categoryData) > 0)
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 text-center mb-4 mb-md-0">
                        <div style="max-width: 250px; margin: 0 auto; position: relative;">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex flex-column gap-2.5">
                            @foreach($categoryData as $cat => $data)
                                <div class="p-2 rounded-3 d-flex align-items-center justify-content-between" style="background: var(--sec);">
                                    <div class="d-flex align-items-center gap-2">
                                        <span style="width: 12px; height: 12px; border-radius: 50%; background-color: {{ $data['color'] }};"></span>
                                        <span class="fw-semibold small">{{ $cat }}</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold small">{{ $user->formatCurrency($data['amount']) }}</span>
                                        <span class="text-muted small ms-1">({{ $data['percentage'] }}%)</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted">No active subscriptions to show chart data.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Chart 2: Billing Cycle Distribution Bar Chart -->
    <div class="col-12 col-lg-5">
        <div class="card-custom h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-bar-chart-fill me-2 text-info"></i>
                        Billing Cycle Breakdown
                    </h5>
                    <p class="text-muted small mb-0">Volume and cost by payment cadence.</p>
                </div>
            </div>

            <div style="height: 220px; position: relative;" class="mb-3">
                <canvas id="cycleChart"></canvas>
            </div>

            <div class="row g-2 text-center pt-2 border-top" style="border-color: var(--bd) !important;">
                <div class="col-4">
                    <span class="text-muted small d-block">Monthly</span>
                    <strong class="fs-6">{{ $cycleData['Monthly']['count'] }} subs</strong>
                    <div class="small text-muted">{{ $user->formatCurrency($cycleData['Monthly']['total']) }}</div>
                </div>
                <div class="col-4">
                    <span class="text-muted small d-block">Yearly</span>
                    <strong class="fs-6 text-success">{{ $cycleData['Yearly']['count'] }} subs</strong>
                    <div class="small text-muted">{{ $user->formatCurrency($cycleData['Yearly']['total']) }}</div>
                </div>
                <div class="col-4">
                    <span class="text-muted small d-block">Weekly</span>
                    <strong class="fs-6 text-warning">{{ $cycleData['Weekly']['count'] }} subs</strong>
                    <div class="small text-muted">{{ $user->formatCurrency($cycleData['Weekly']['total']) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row: Top Expenses Ranking & Cancelled Savings -->
<div class="row g-4">
    <!-- Top 5 Most Expensive Subscriptions -->
    <div class="col-12 col-lg-7">
        <div class="card-custom h-100">
            <h5 class="fw-bold mb-1">
                <i class="bi bi-trophy-fill me-2 text-warning"></i>
                Top 5 Most Expensive Subscriptions
            </h5>
            <p class="text-muted small mb-3">Highest impact on your monthly budget.</p>

            @if($topExpenses->isNotEmpty())
                <div class="d-flex flex-column gap-2">
                    @foreach($topExpenses as $index => $sub)
                        @php
                            $pct = $totalMonthly > 0 ? round(($sub->monthly_cost / $totalMonthly) * 100) : 0;
                        @endphp
                        <div class="p-3 rounded-4 d-flex align-items-center justify-content-between" style="background: var(--card); border: 1px solid var(--bd);">
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-bold text-muted" style="width: 20px;">#{{ $index + 1 }}</span>
                                <div class="service-icon" style="width: 40px; height: 40px; font-size: 1.25rem;">
                                    {{ $sub->display_icon }}
                                </div>
                                <div>
                                    <a href="{{ route('subscriptions.show', $sub) }}" class="fw-bold text-decoration-none text-reset">
                                        {{ $sub->name }}
                                    </a>
                                    <div class="small text-muted">
                                        {{ $sub->category }} &bull; {{ $sub->billing_cycle }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <div class="fw-bold fs-6">
                                    {{ $user->formatCurrency($sub->monthly_cost) }} <span class="small text-muted fw-normal">/mo</span>
                                </div>
                                <span class="badge rounded-pill bg-light text-muted border small">
                                    {{ $pct }}% of monthly budget
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">No subscriptions found.</p>
            @endif
        </div>
    </div>

    <!-- Savings & Portfolio Impact Card -->
    <div class="col-12 col-lg-5">
        <div class="card-custom h-100 d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-bold mb-1">
                    <i class="bi bi-piggy-bank-fill me-2 text-success" style="color: var(--ok) !important;"></i>
                    Cancellation Savings Impact
                </h5>
                <p class="text-muted small mb-3">Money kept in your wallet by pruning inactive services.</p>

                <div class="p-4 rounded-4 text-center mb-3" style="background: var(--okb); border: 1.5px solid rgba(16, 185, 129, 0.25);">
                    <span class="small text-muted text-uppercase fw-bold letter-spacing-1">Estimated Annual Savings</span>
                    <div class="display-6 fw-bold text-success my-2" style="color: var(--ok) !important;">
                        {{ $user->formatCurrency($yearlySavings) }}
                    </div>
                    <span class="small text-muted">
                        {{ $user->formatCurrency($monthlySavings) }} saved every month from cancelled subscriptions
                    </span>
                </div>

                <div class="small text-muted">
                    <i class="bi bi-lightbulb-fill text-warning me-1"></i>
                    <strong>Pro-Tip:</strong> Regular audits of your recurring services and free trial cancel dates help the average household save over $400 each year.
                </div>
            </div>

            <div class="pt-3 border-top mt-3" style="border-color: var(--bd) !important;">
                <a href="{{ route('subscriptions.index', ['status' => 'Cancelled']) }}" class="btn btn-sm btn-theme-secondary w-100 justify-content-center">
                    <i class="bi bi-clock-history"></i> Review All Cancelled Subscriptions
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';

        // 1. Category Chart
        const catCanvas = document.getElementById('categoryChart');
        if (catCanvas) {
            const catLabels = @json(array_keys($categoryData));
            const catValues = @json(array_column($categoryData, 'amount'));
            const catColors = @json(array_column($categoryData, 'color'));

            new Chart(catCanvas, {
                type: 'doughnut',
                data: {
                    labels: catLabels,
                    datasets: [{
                        data: catValues,
                        backgroundColor: catColors,
                        borderWidth: 2,
                        borderColor: isDarkMode ? '#131b2e' : '#ffffff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '70%',
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

        // 2. Cycle Chart
        const cycleCanvas = document.getElementById('cycleChart');
        if (cycleCanvas) {
            new Chart(cycleCanvas, {
                type: 'bar',
                data: {
                    labels: ['Monthly', 'Yearly', 'Weekly'],
                    datasets: [{
                        label: 'Subscriptions Count',
                        data: [
                            {{ $cycleData['Monthly']['count'] }},
                            {{ $cycleData['Yearly']['count'] }},
                            {{ $cycleData['Weekly']['count'] }}
                        ],
                        backgroundColor: ['#6366f1', '#10b981', '#f59e0b'],
                        borderRadius: 8
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

