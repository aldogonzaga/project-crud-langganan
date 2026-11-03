@extends('layouts.app')

@section('title', 'Edit ' . $subscription->name)
@section('page_title', 'Edit Subscription')
@section('page_subtitle', 'Update rates, renewal schedule, or status for ' . $subscription->name . '.')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom">
            <!-- Header info -->
            <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom" style="border-color: var(--bd) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="service-icon" style="font-size: 1.5rem;">
                        {{ $subscription->display_icon }}
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">{{ $subscription->name }}</h4>
                        <span class="text-muted small">Tracking since {{ $subscription->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                <div>
                    @if($subscription->status === 'Active')
                        <span class="badge-pill-custom badge-active"><i class="bi bi-check2"></i> Active</span>
                    @elseif($subscription->status === 'Free Trial')
                        <span class="badge-pill-custom badge-trial"><i class="bi bi-hourglass-split"></i> Free Trial</span>
                    @else
                        <span class="badge-pill-custom badge-cancelled"><i class="bi bi-x"></i> Cancelled</span>
                    @endif
                </div>
            </div>

            <form action="{{ route('subscriptions.update', $subscription) }}" method="POST" id="editSubForm">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- Service Name -->
                    <div class="col-12 col-md-8">
                        <label for="name" class="form-label fw-semibold small">Service Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3" style="border-color: var(--bd);">
                                <i class="bi bi-tag text-muted"></i>
                            </span>
                            <input type="text" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2 @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $subscription->name) }}" required placeholder="e.g., Spotify, Netflix">
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Icon / Emoji Picker -->
                    <div class="col-12 col-md-4">
                        <label for="icon" class="form-label fw-semibold small">Icon / Emoji</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3" id="icon-preview" style="border-color: var(--bd); font-size: 1.2rem;">
                                {{ $subscription->display_icon }}
                            </span>
                            <input type="text" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2 @error('icon') is-invalid @enderror" 
                                   id="icon" name="icon" value="{{ old('icon', $subscription->icon) }}" maxlength="10">
                        </div>
                        <div class="d-flex gap-1 mt-1">
                            <button type="button" class="btn btn-sm btn-light border p-0 px-1" onclick="setIcon('🎵')">🎵</button>
                            <button type="button" class="btn btn-sm btn-light border p-0 px-1" onclick="setIcon('🎬')">🎬</button>
                            <button type="button" class="btn btn-sm btn-light border p-0 px-1" onclick="setIcon('💻')">💻</button>
                            <button type="button" class="btn btn-sm btn-light border p-0 px-1" onclick="setIcon('🏋️')">🏋️</button>
                            <button type="button" class="btn btn-sm btn-light border p-0 px-1" onclick="setIcon('⚡')">⚡</button>
                            <button type="button" class="btn btn-sm btn-light border p-0 px-1" onclick="setIcon('☁️')">☁️</button>
                            <button type="button" class="btn btn-sm btn-light border p-0 px-1" onclick="setIcon('📚')">📚</button>
                            <button type="button" class="btn btn-sm btn-light border p-0 px-1" onclick="setIcon('🥗')">🥗</button>
                        </div>
                    </div>

                    <!-- Cost (Rate hike adjustment) -->
                    <div class="col-12 col-md-6">
                        <label for="cost" class="form-label fw-semibold small">Cost <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3 fw-bold" style="border-color: var(--bd); color: var(--pri);">
                                {{ Auth::user()->currency }}
                            </span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2 @error('cost') is-invalid @enderror" 
                                   id="cost" name="cost" value="{{ old('cost', $subscription->cost) }}" required placeholder="0.00" oninput="updateCostPreview()">
                        </div>
                        <div class="form-text small text-muted">Update cost if the service announced a price hike.</div>
                        @error('cost')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Billing Cycle -->
                    <div class="col-12 col-md-6">
                        <label for="billing_cycle" class="form-label fw-semibold small">Billing Cycle <span class="text-danger">*</span></label>
                        <select class="form-select form-select-custom @error('billing_cycle') is-invalid @enderror" 
                                id="billing_cycle" name="billing_cycle" required onchange="updateCostPreview()">
                            @foreach($cycles as $cycle)
                                <option value="{{ $cycle }}" {{ old('billing_cycle', $subscription->billing_cycle) === $cycle ? 'selected' : '' }}>
                                    {{ $cycle }}
                                </option>
                            @endforeach
                        </select>
                        @error('billing_cycle')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="col-12 col-md-6">
                        <label for="category" class="form-label fw-semibold small">Category <span class="text-danger">*</span></label>
                        <select class="form-select form-select-custom @error('category') is-invalid @enderror" 
                                id="category" name="category" required>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ old('category', $subscription->category) === $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status (Convert trial / Cancel) -->
                    <div class="col-12 col-md-6">
                        <label for="status" class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                        <select class="form-select form-select-custom @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ old('status', $subscription->status) === $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small text-muted">
                            Cancel stops adding to monthly spend total while keeping record history.
                        </div>
                        @error('status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Next Billing Date -->
                    <div class="col-12">
                        <label for="next_billing_date" class="form-label fw-semibold small">
                            Next Billing Date / Trial End <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3" style="border-color: var(--bd);">
                                <i class="bi bi-calendar-event text-muted"></i>
                            </span>
                            <input type="date" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2 @error('next_billing_date') is-invalid @enderror" 
                                   id="next_billing_date" name="next_billing_date" 
                                   value="{{ old('next_billing_date', $subscription->next_billing_date ? $subscription->next_billing_date->format('Y-m-d') : '') }}" required>
                        </div>
                        @error('next_billing_date')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label for="notes" class="form-label fw-semibold small">Notes / Description (Optional)</label>
                        <textarea class="form-control form-control-custom @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="2" placeholder="e.g., Plan details, reminder...">{{ old('notes', $subscription->notes) }}</textarea>
                        @error('notes')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Live Monthly Cost Preview Box -->
                <div class="rounded-4 p-3 my-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--sec); border: 1px solid var(--bd);">
                    <div>
                        <span class="small text-muted fw-semibold">Updated Monthly Expense:</span>
                        <div class="fs-4 fw-bold" id="monthly-preview" style="color: var(--pri);">
                            {{ Auth::user()->currency }}{{ number_format($subscription->monthly_cost, 2) }} / mo
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="small text-muted fw-semibold">Updated Annual Cost:</span>
                        <div class="fs-6 fw-bold" id="yearly-preview" style="color: var(--tx);">
                            {{ Auth::user()->currency }}{{ number_format($subscription->yearly_cost, 2) }} / yr
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-theme-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-theme px-4">
                            <i class="bi bi-check-lg"></i> Update Subscription
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function setIcon(emoji) {
        document.getElementById('icon').value = emoji;
        document.getElementById('icon-preview').textContent = emoji;
    }

    document.getElementById('icon').addEventListener('input', function() {
        document.getElementById('icon-preview').textContent = this.value || '✨';
    });

    function updateCostPreview() {
        const cost = parseFloat(document.getElementById('cost').value) || 0;
        const cycle = document.getElementById('billing_cycle').value;
        const currency = "{{ Auth::user()->currency }}";

        let monthly = cost;
        let yearly = cost * 12;

        if (cycle === 'Weekly') {
            monthly = (cost * 52) / 12;
            yearly = cost * 52;
        } else if (cycle === 'Yearly') {
            monthly = cost / 12;
            yearly = cost;
        }

        document.getElementById('monthly-preview').textContent = currency + monthly.toFixed(2) + ' / mo';
        document.getElementById('yearly-preview').textContent = currency + yearly.toFixed(2) + ' / yr';
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateCostPreview();
    });
</script>
@endsection

