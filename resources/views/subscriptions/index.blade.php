@extends('layouts.app')

@section('title', 'Subscriptions')
@section('page_title', 'All Subscriptions')
@section('page_subtitle', 'Browse, filter, and manage your active and trial subscriptions.')

@section('content')
    <!-- Status Tabs & Actions Bar -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <!-- Status Tabs (All, Active, Free Trial, Cancelled) -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('subscriptions.index', array_merge(request()->except('page', 'status'), ['status' => 'All'])) }}" 
               class="filter-chip {{ ($filters['status'] ?? 'All') === 'All' ? 'active' : '' }}">
                <span>All</span>
                <span class="badge rounded-pill bg-light text-dark small px-1.5 py-0.5" style="font-size: 0.7rem;">{{ $totalCount }}</span>
            </a>
            <a href="{{ route('subscriptions.index', array_merge(request()->except('page', 'status'), ['status' => 'Active'])) }}" 
               class="filter-chip {{ ($filters['status'] ?? '') === 'Active' ? 'active' : '' }}">
                <span>Active</span>
                <span class="badge rounded-pill bg-success-subtle text-success small px-1.5 py-0.5" style="font-size: 0.7rem;">{{ $activeCount }}</span>
            </a>
            <a href="{{ route('subscriptions.index', array_merge(request()->except('page', 'status'), ['status' => 'Free Trial'])) }}" 
               class="filter-chip {{ ($filters['status'] ?? '') === 'Free Trial' ? 'active' : '' }}">
                <span>Free Trial</span>
                <span class="badge rounded-pill bg-warning-subtle text-warning small px-1.5 py-0.5" style="font-size: 0.7rem;">{{ $trialCount }}</span>
            </a>
            <a href="{{ route('subscriptions.index', array_merge(request()->except('page', 'status'), ['status' => 'Cancelled'])) }}" 
               class="filter-chip {{ ($filters['status'] ?? '') === 'Cancelled' ? 'active' : '' }}">
                <span>Cancelled</span>
                <span class="badge rounded-pill bg-secondary-subtle text-secondary small px-1.5 py-0.5" style="font-size: 0.7rem;">{{ $cancelledCount }}</span>
            </a>
            @if($trashCount > 0)
                <a href="{{ route('subscriptions.trash') }}" class="filter-chip text-muted">
                    <i class="bi bi-trash3"></i> Trash ({{ $trashCount }})
                </a>
            @endif
        </div>

        <!-- View Switcher (Cards vs Table) & Add Button -->
        <div class="d-flex align-items-center gap-2">
            <div class="btn-group p-1 rounded-pill" style="background: var(--card); border: 1px solid var(--bd);">
                <a href="{{ route('subscriptions.index', array_merge(request()->all(), ['view' => 'cards'])) }}" 
                   class="btn btn-sm rounded-pill px-3 py-1 {{ ($filters['view'] ?? 'cards') === 'cards' ? 'btn-primary' : 'btn-light border-0 text-muted' }}" 
                   title="Card Grid View">
                    <i class="bi bi-grid-fill"></i>
                </a>
                <a href="{{ route('subscriptions.index', array_merge(request()->all(), ['view' => 'table'])) }}" 
                   class="btn btn-sm rounded-pill px-3 py-1 {{ ($filters['view'] ?? 'cards') === 'table' ? 'btn-primary' : 'btn-light border-0 text-muted' }}" 
                   title="Table View">
                    <i class="bi bi-table"></i>
                </a>
            </div>

            <a href="{{ route('subscriptions.create') }}" class="btn-theme">
                <i class="bi bi-plus-lg"></i>
                <span>Add New</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card-custom mb-4 p-3">
        <form method="GET" action="{{ route('subscriptions.index') }}" id="filterForm">
            <!-- Hidden field for active view & status tab -->
            <input type="hidden" name="view" value="{{ $filters['view'] ?? 'cards' }}">
            <input type="hidden" name="status" value="{{ $filters['status'] ?? 'All' }}">

            <div class="row g-2 align-items-center">
                <!-- Search -->
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3" style="border-color: var(--bd);">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2" 
                               placeholder="Search subscriptions or notes..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="col-6 col-md-3">
                    <select name="category" class="form-select form-select-custom" onchange="document.getElementById('filterForm').submit()">
                        <option value="All">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ ($filters['category'] ?? '') === $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Billing Cycle Filter -->
                <div class="col-6 col-md-2">
                    <select name="cycle" class="form-select form-select-custom" onchange="document.getElementById('filterForm').submit()">
                        <option value="All">All Cycles</option>
                        <option value="Monthly" {{ ($filters['cycle'] ?? '') === 'Monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="Yearly" {{ ($filters['cycle'] ?? '') === 'Yearly' ? 'selected' : '' }}>Yearly</option>
                        <option value="Weekly" {{ ($filters['cycle'] ?? '') === 'Weekly' ? 'selected' : '' }}>Weekly</option>
                    </select>
                </div>

                <!-- Sort By Filter -->
                <div class="col-12 col-md-3">
                    <select name="sort" class="form-select form-select-custom" onchange="document.getElementById('filterForm').submit()">
                        <option value="due_soon" {{ ($filters['sort'] ?? '') === 'due_soon' ? 'selected' : '' }}>Next Renewal (Soonest)</option>
                        <option value="cost_high" {{ ($filters['sort'] ?? '') === 'cost_high' ? 'selected' : '' }}>Highest Cost</option>
                        <option value="cost_low" {{ ($filters['sort'] ?? '') === 'cost_low' ? 'selected' : '' }}>Lowest Cost</option>
                        <option value="name_asc" {{ ($filters['sort'] ?? '') === 'name_asc' ? 'selected' : '' }}>Name (A - Z)</option>
                        <option value="newest" {{ ($filters['sort'] ?? '') === 'newest' ? 'selected' : '' }}>Recently Added</option>
                    </select>
                </div>
            </div>

            <!-- Active Filter Badges with Quick Reset -->
            @if(!empty($filters['search']) || ($filters['category'] ?? 'All') !== 'All' || ($filters['cycle'] ?? 'All') !== 'All' || ($filters['sort'] ?? 'due_soon') !== 'due_soon')
                <div class="d-flex align-items-center gap-2 mt-3 pt-2 border-top flex-wrap" style="border-color: var(--bd) !important;">
                    <span class="small text-muted">Active Filters:</span>
                    @if(!empty($filters['search']))
                        <span class="badge rounded-pill bg-light text-dark border small px-2 py-1">
                            Search: "{{ $filters['search'] }}"
                        </span>
                    @endif
                    @if(($filters['category'] ?? 'All') !== 'All')
                        <span class="badge rounded-pill bg-light text-dark border small px-2 py-1">
                            Category: {{ $filters['category'] }}
                        </span>
                    @endif
                    @if(($filters['cycle'] ?? 'All') !== 'All')
                        <span class="badge rounded-pill bg-light text-dark border small px-2 py-1">
                            Cycle: {{ $filters['cycle'] }}
                        </span>
                    @endif
                    @if(($filters['sort'] ?? 'due_soon') !== 'due_soon')
                        <span class="badge rounded-pill bg-light text-dark border small px-2 py-1">
                            Sorted: {{ ucwords(str_replace('_', ' ', $filters['sort'])) }}
                        </span>
                    @endif
                    <a href="{{ route('subscriptions.index', ['status' => $filters['status'] ?? 'All', 'view' => $filters['view'] ?? 'cards']) }}" class="small text-danger ms-auto text-decoration-none fw-bold">
                        <i class="bi bi-x-circle"></i> Reset All Filters
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Subscriptions Listing Container -->
    @if($subscriptions->isNotEmpty())
        @if(($filters['view'] ?? 'cards') === 'cards')
            <!-- CARD GRID VIEW (Modern KeikiSpace style, without pink) -->
            <div class="row g-3 mb-4">
                @foreach($subscriptions as $sub)
                    @php
                        $days = $sub->days_until_due;
                        $isCancelled = $sub->status === 'Cancelled';
                        $isCritical = !$isCancelled && $days <= 3; // Date logic: Red highlight if <= 3 days
                        $isWarning = !$isCancelled && !$isCritical && $days <= 7;
                    @endphp
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card-custom h-100 d-flex flex-column justify-content-between position-relative {{ $isCritical ? 'card-critical' : ($isWarning ? 'card-warning' : '') }}"
                             style="{{ $isCancelled ? 'opacity: 0.75;' : '' }}">
                            
                            <div>
                                <!-- Top Row: Icon, Service Name, Status Badge -->
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="service-icon">
                                            {!! $sub->rendered_icon !!}
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-0">
                                                <a href="{{ route('subscriptions.show', $sub) }}" class="text-decoration-none text-reset">
                                                    {{ $sub->name }}
                                                </a>
                                            </h5>
                                            <span class="badge rounded-pill small px-2 py-0.5 mt-1" style="background: var(--sec); color: var(--mu); font-size: 0.72rem;">
                                                {{ $sub->category }}
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        @if($sub->status === 'Active')
                                            <span class="badge-pill-custom badge-active"><i class="bi bi-check2"></i> Active</span>
                                        @elseif($sub->status === 'Free Trial')
                                            <span class="badge-pill-custom badge-trial"><i class="bi bi-hourglass-split"></i> Free Trial</span>
                                        @else
                                            <span class="badge-pill-custom badge-cancelled"><i class="bi bi-x"></i> Cancelled</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Cost & Cycle Information -->
                                <div class="mb-3">
                                    <div class="d-flex align-items-baseline gap-1">
                                        <span class="fs-3 fw-bold" style="color: {{ $isCritical ? 'var(--da)' : 'var(--tx)' }};">
                                            {{ Auth::user()->formatCurrency($sub->cost) }}
                                        </span>
                                        <span class="text-muted small fw-semibold">
                                            /{{ strtolower($sub->billing_cycle) }}
                                        </span>
                                    </div>
                                    <div class="small text-muted">
                                        ≈ {{ Auth::user()->formatCurrency($sub->monthly_cost) }} / month
                                        @if($isCancelled)
                                            <span class="badge bg-secondary-subtle text-secondary ms-1">No monthly charge</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Next Billing Date & Urgency Indicator -->
                                <div class="p-2.5 rounded-3 mb-3" style="background: {{ $isCritical ? 'var(--dab)' : ($isWarning ? 'var(--wab)' : 'var(--sec)') }};">
                                    <div class="d-flex justify-content-between align-items-center small">
                                        <span class="text-muted">Next Billing:</span>
                                        <span class="fw-bold" style="color: {{ $isCritical ? 'var(--da)' : ($isWarning ? 'var(--wa)' : 'var(--tx)') }};">
                                            {{ $sub->next_billing_date ? $sub->next_billing_date->format('M d, Y') : '—' }}
                                        </span>
                                    </div>
                                    <div class="mt-1 d-flex justify-content-between align-items-center small">
                                        <span class="text-muted">Schedule Status:</span>
                                        @if($isCancelled)
                                            <span class="text-muted fw-bold">Cancelled (Archived)</span>
                                        @elseif($days < 0)
                                            <span class="badge-pill-custom badge-urgent-critical"><i class="bi bi-circle-fill text-danger me-1"></i>Overdue by {{ abs($days) }}d</span>
                                        @elseif($days === 0)
                                            <span class="badge-pill-custom badge-urgent-critical"><i class="bi bi-circle-fill text-danger me-1"></i>Due Today!</span>
                                        @elseif($days <= 3)
                                            <span class="badge-pill-custom badge-urgent-critical"><i class="bi bi-circle-fill text-danger me-1"></i>Due in {{ $days }} days</span>
                                        @elseif($days <= 7)
                                            <span class="badge-pill-custom badge-urgent-warning"><i class="bi bi-circle-fill text-warning me-1"></i>Due in {{ $days }} days</span>
                                        @else
                                            <span class="badge-pill-custom badge-urgent-normal">Due in {{ $days }} days</span>
                                        @endif
                                    </div>
                                </div>

                                @if($sub->notes)
                                    <p class="text-muted small mb-3 text-truncate-2" style="font-size: 0.82rem; min-height: 2.4em;">
                                        {{ $sub->notes }}
                                    </p>
                                @endif
                            </div>

                            <!-- Actions Row -->
                            <div class="pt-3 border-top d-flex align-items-center justify-content-between" style="border-color: var(--bd) !important;">
                                <div class="d-flex gap-1">
                                    <a href="{{ route('subscriptions.edit', $sub) }}" class="btn btn-sm btn-theme-secondary py-1 px-3" title="Edit Subscription">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="{{ route('subscriptions.show', $sub) }}" class="btn btn-sm btn-circle" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-sm btn-circle" data-bs-toggle="dropdown" aria-expanded="false" title="Quick Options">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0 py-2">
                                        @if($sub->status !== 'Active')
                                            <li>
                                                <form action="{{ route('subscriptions.quickStatus', $sub) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="Active">
                                                    <button type="submit" class="dropdown-item py-2 small text-success"><i class="bi bi-check-circle me-2"></i>Set to Active</button>
                                                </form>
                                            </li>
                                        @endif
                                        @if($sub->status !== 'Cancelled')
                                            <li>
                                                <form action="{{ route('subscriptions.quickStatus', $sub) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="Cancelled">
                                                    <button type="submit" class="dropdown-item py-2 small text-warning"><i class="bi bi-pause-circle me-2"></i>Cancel (Keep in History)</button>
                                                </form>
                                            </li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('subscriptions.destroy', $sub) }}" method="POST" onsubmit="return confirm('Move {{ $sub->name }} to Trash?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item py-2 small text-danger"><i class="bi bi-trash3 me-2"></i>Move to Trash</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- BASIC READ: TABLE VIEW -->
            <div class="card-custom mb-4 p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Service Name</th>
                                <th>Category</th>
                                <th>Cost</th>
                                <th>Cycle</th>
                                <th>Monthly Cost</th>
                                <th>Next Due Date</th>
                                <th>Urgency</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $sub)
                                @php
                                    $days = $sub->days_until_due;
                                    $isCancelled = $sub->status === 'Cancelled';
                                    $isCritical = !$isCancelled && $days <= 3;
                                    $isWarning = !$isCancelled && !$isCritical && $days <= 7;
                                @endphp
                                <tr class="{{ $isCritical ? 'table-danger-subtle' : '' }}" style="{{ $isCancelled ? 'opacity: 0.75;' : '' }}">
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="service-icon" style="width: 38px; height: 38px; font-size: 1.2rem;">
                                                {!! $sub->rendered_icon !!}
                                            </div>
                                            <div>
                                                <a href="{{ route('subscriptions.show', $sub) }}" class="fw-bold text-decoration-none text-reset">
                                                    {{ $sub->name }}
                                                </a>
                                                @if($sub->notes)
                                                    <div class="text-muted small text-truncate" style="max-width: 200px;">
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
                                        <div class="fw-bold fs-6" style="color: {{ $isCritical ? 'var(--da)' : 'var(--tx)' }};">
                                            {{ Auth::user()->formatCurrency($sub->cost) }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $sub->billing_cycle }}</span>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-muted">
                                            {{ Auth::user()->formatCurrency($sub->monthly_cost) }}/mo
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $sub->next_billing_date ? $sub->next_billing_date->format('M d, Y') : '—' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($isCancelled)
                                            <span class="text-muted small">None (Cancelled)</span>
                                        @elseif($days < 0)
                                            <span class="badge-pill-custom badge-urgent-critical"><i class="bi bi-circle-fill text-danger me-1"></i>Overdue {{ abs($days) }}d</span>
                                        @elseif($days === 0)
                                            <span class="badge-pill-custom badge-urgent-critical"><i class="bi bi-circle-fill text-danger me-1"></i>Due Today</span>
                                        @elseif($days <= 3)
                                            <span class="badge-pill-custom badge-urgent-critical"><i class="bi bi-circle-fill text-danger me-1"></i>Due in {{ $days }}d</span>
                                        @elseif($days <= 7)
                                            <span class="badge-pill-custom badge-urgent-warning"><i class="bi bi-circle-fill text-warning me-1"></i>In {{ $days }}d</span>
                                        @else
                                            <span class="small text-muted">In {{ $days }}d</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($sub->status === 'Active')
                                            <span class="badge-pill-custom badge-active"><i class="bi bi-check2"></i> Active</span>
                                        @elseif($sub->status === 'Free Trial')
                                            <span class="badge-pill-custom badge-trial"><i class="bi bi-hourglass-split"></i> Trial</span>
                                        @else
                                            <span class="badge-pill-custom badge-cancelled"><i class="bi bi-x"></i> Cancelled</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('subscriptions.edit', $sub) }}" class="btn btn-sm btn-circle" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('subscriptions.show', $sub) }}" class="btn btn-sm btn-circle" title="Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <form action="{{ route('subscriptions.destroy', $sub) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete {{ $sub->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-circle text-danger" title="Delete">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2">
            <span class="small text-muted">
                Showing {{ $subscriptions->firstItem() }} to {{ $subscriptions->lastItem() }} of {{ $subscriptions->total() }} subscriptions
            </span>
            <div>
                {{ $subscriptions->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="card-custom text-center py-5">
            <div class="display-5 mb-3 text-muted"><i class="bi bi-search"></i></div>
            <h4 class="fw-bold mb-1">No subscriptions found</h4>
            <p class="text-muted small mb-4" style="max-width: 400px; margin: 0 auto;">
                No subscriptions matched your active search or filter criteria. Try resetting filters or add a new record.
            </p>
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('subscriptions.index') }}" class="btn btn-theme-secondary">
                    Clear Filters
                </a>
                <a href="{{ route('subscriptions.create') }}" class="btn btn-theme">
                    <i class="bi bi-plus-lg"></i> Add Subscription
                </a>
            </div>
        </div>
    @endif
@endsection

