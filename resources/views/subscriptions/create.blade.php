@extends('layouts.app')

@section('title', 'Add Subscription')
@section('page_title', 'Add New Subscription')
@section('page_subtitle', 'Record a new recurring service or free trial to keep track of renewals.')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <!-- Quick Preset Suggestions -->
        <div class="card-custom mb-4 p-3">
            <span class="small text-muted fw-semibold d-block mb-2">
                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Autofill Presets:
            </span>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-theme-secondary py-1 px-3" onclick="fillPreset('Spotify Premium', 54990, 'Monthly', 'Entertainment', 'Active', 'bi-spotify')">
                    <i class="bi bi-spotify me-1 text-success"></i> Spotify (Rp 54.990)
                </button>
                <button type="button" class="btn btn-sm btn-theme-secondary py-1 px-3" onclick="fillPreset('Netflix Standard', 186000, 'Monthly', 'Entertainment', 'Active', 'bi-film')">
                    <i class="bi bi-film me-1 text-danger"></i> Netflix (Rp 186.000)
                </button>
                <button type="button" class="btn btn-sm btn-theme-secondary py-1 px-3" onclick="fillPreset('GitHub Copilot', 150000, 'Monthly', 'Work', 'Active', 'bi-github')">
                    <i class="bi bi-github me-1"></i> Copilot (Rp 150.000)
                </button>
                <button type="button" class="btn btn-sm btn-theme-secondary py-1 px-3" onclick="fillPreset('Gym Membership', 350000, 'Monthly', 'Health', 'Active', 'bi-heart-pulse')">
                    <i class="bi bi-heart-pulse me-1 text-danger"></i> Gym (Rp 350.000)
                </button>
                <button type="button" class="btn btn-sm btn-theme-secondary py-1 px-3" onclick="fillPreset('Figma Trial', 180000, 'Monthly', 'Work', 'Free Trial', 'bi-hourglass-split')">
                    <i class="bi bi-hourglass-split me-1 text-warning"></i> Figma Trial (Rp 180.000)
                </button>
            </div>
        </div>

        <div class="card-custom">
            <form action="{{ route('subscriptions.store') }}" method="POST" id="subForm">
                @csrf

                <div class="row g-3">
                    <!-- Service Name -->
                    <div class="col-12 col-md-8">
                        <label for="name" class="form-label fw-semibold small">Service Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3" style="border-color: var(--bd);">
                                <i class="bi bi-tag text-muted"></i>
                            </span>
                            <input type="text" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2 @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required placeholder="e.g., Spotify Premium, Gym, Netflix">
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Icon / Bootstrap Icon Picker -->
                    <div class="col-12 col-md-4">
                        <label for="icon" class="form-label fw-semibold small">Bootstrap Icon (bi-*)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3" id="icon-preview" style="border-color: var(--bd); font-size: 1.2rem;">
                                <i class="bi bi-film"></i>
                            </span>
                            <input type="text" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2 @error('icon') is-invalid @enderror" 
                                   id="icon" name="icon" value="{{ old('icon', 'bi-film') }}" maxlength="50" placeholder="e.g. bi-film, bi-spotify">
                        </div>
                        <div class="d-flex gap-1 mt-1">
                            <button type="button" class="btn btn-sm btn-light border p-0 px-2" onclick="setIcon('bi-music-note-beamed')" title="Music"><i class="bi bi-music-note-beamed"></i></button>
                            <button type="button" class="btn btn-sm btn-light border p-0 px-2" onclick="setIcon('bi-film')" title="Entertainment"><i class="bi bi-film"></i></button>
                            <button type="button" class="btn btn-sm btn-light border p-0 px-2" onclick="setIcon('bi-laptop')" title="Work"><i class="bi bi-laptop"></i></button>
                            <button type="button" class="btn btn-sm btn-light border p-0 px-2" onclick="setIcon('bi-heart-pulse')" title="Health"><i class="bi bi-heart-pulse"></i></button>
                            <button type="button" class="btn btn-sm btn-light border p-0 px-2" onclick="setIcon('bi-lightning-charge')" title="Utilities"><i class="bi bi-lightning-charge"></i></button>
                            <button type="button" class="btn btn-sm btn-light border p-0 px-2" onclick="setIcon('bi-cloud')" title="Cloud"><i class="bi bi-cloud"></i></button>
                            <button type="button" class="btn btn-sm btn-light border p-0 px-2" onclick="setIcon('bi-book')" title="Education"><i class="bi bi-book"></i></button>
                            <button type="button" class="btn btn-sm btn-light border p-0 px-2" onclick="setIcon('bi-cup-hot')" title="Food"><i class="bi bi-cup-hot"></i></button>
                        </div>
                    </div>

                    <!-- Cost -->
                    <div class="col-12 col-md-6">
                        <label for="cost" class="form-label fw-semibold small">Cost <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3 fw-bold" style="border-color: var(--bd); color: var(--pri);">
                                {{ Auth::user()->currency }}
                            </span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2 @error('cost') is-invalid @enderror" 
                                   id="cost" name="cost" value="{{ old('cost', '50000') }}" required placeholder="0.00" oninput="updateCostPreview()">
                        </div>
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
                                <option value="{{ $cycle }}" {{ old('billing_cycle', 'Monthly') === $cycle ? 'selected' : '' }}>
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
                                <option value="{{ $category }}" {{ old('category', 'Entertainment') === $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-md-6">
                        <label for="status" class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                        <select class="form-select form-select-custom @error('status') is-invalid @enderror" 
                                id="status" name="status" required onchange="handleStatusChange()">
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ old('status', 'Active') === $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Next Billing Date -->
                    <div class="col-12">
                        <label for="next_billing_date" class="form-label fw-semibold small" id="date-label">
                            Next Billing Date / Trial End <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3" style="border-color: var(--bd);">
                                <i class="bi bi-calendar-event text-muted"></i>
                            </span>
                            <input type="date" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2 @error('next_billing_date') is-invalid @enderror" 
                                   id="next_billing_date" name="next_billing_date" 
                                   value="{{ old('next_billing_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                        </div>
                        <div class="form-text small text-muted">
                            Bills due within ≤ 3 days will be highlighted in red with high urgency alerts.
                        </div>
                        @error('next_billing_date')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes (Optional) -->
                    <div class="col-12">
                        <label for="notes" class="form-label fw-semibold small">Notes / Description (Optional)</label>
                        <textarea class="form-control form-control-custom @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="2" placeholder="e.g., Plan details, reminder to cancel before renewal, family sharing...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Live Monthly Cost Preview Box -->
                <div class="rounded-4 p-3 my-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--sec); border: 1px solid var(--bd);">
                    <div>
                        <span class="small text-muted fw-semibold">Effective Monthly Expense:</span>
                        <div class="fs-4 fw-bold" id="monthly-preview" style="color: var(--pri);">
                            {{ Auth::user()->currency }}9.99 / mo
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="small text-muted fw-semibold">Projected Annual Cost:</span>
                        <div class="fs-6 fw-bold" id="yearly-preview" style="color: var(--tx);">
                            {{ Auth::user()->currency }}119.88 / yr
                        </div>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-theme-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-theme px-4">
                        <i class="bi bi-check-lg"></i> Save Subscription
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function setIcon(icon) {
        document.getElementById('icon').value = icon;
        updateIconPreview(icon);
    }

    function updateIconPreview(icon) {
        const preview = document.getElementById('icon-preview');
        if (!icon) {
            preview.innerHTML = '<i class="bi bi-tag"></i>';
            return;
        }
        if (icon.startsWith('bi-') || icon.startsWith('bi ')) {
            const cls = icon.startsWith('bi ') ? icon : 'bi ' + icon;
            preview.innerHTML = `<i class="${cls}"></i>`;
        } else {
            preview.textContent = icon;
        }
    }

    document.getElementById('icon').addEventListener('input', function() {
        updateIconPreview(this.value);
    });

    function fillPreset(name, cost, cycle, category, status, icon) {
        document.getElementById('name').value = name;
        document.getElementById('cost').value = cost;
        document.getElementById('billing_cycle').value = cycle;
        document.getElementById('category').value = category;
        document.getElementById('status').value = status;
        setIcon(icon);
        updateCostPreview();
    }

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

        const fmt = (val) => {
            if (currency.trim() === 'Rp') {
                return 'Rp ' + Math.round(val).toLocaleString('id-ID');
            }
            return currency + val.toFixed(2);
        };

        document.getElementById('monthly-preview').textContent = fmt(monthly) + ' / mo';
        document.getElementById('yearly-preview').textContent = fmt(yearly) + ' / yr';
    }

    function handleStatusChange() {
        const status = document.getElementById('status').value;
        const dateLabel = document.getElementById('date-label');
        if (status === 'Free Trial') {
            dateLabel.innerHTML = 'Free Trial Expiration Date <span class="text-danger">*</span>';
        } else {
            dateLabel.innerHTML = 'Next Billing Date <span class="text-danger">*</span>';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateIconPreview(document.getElementById('icon').value);
        updateCostPreview();
    });
</script>
@endsection

