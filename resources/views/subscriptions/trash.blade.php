@extends('layouts.app')

@section('title', 'Trash')
@section('page_title', 'Trash / Soft-Deleted Subscriptions')
@section('page_subtitle', 'Restore accidentally removed subscriptions or permanently delete them.')

@section('content')
<div class="card-custom mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">
                <i class="bi bi-trash3 me-2 text-danger"></i>
                Deleted Subscriptions
            </h5>
            <p class="text-muted small mb-0">Items here can be restored back to your active list at any time.</p>
        </div>
        <a href="{{ route('subscriptions.index') }}" class="btn btn-sm btn-theme-secondary">
            <i class="bi bi-arrow-left"></i> Back to Subscriptions
        </a>
    </div>

    @if($trashedSubscriptions->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Category</th>
                        <th>Cost</th>
                        <th>Cycle</th>
                        <th>Deleted On</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trashedSubscriptions as $sub)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="service-icon" style="opacity: 0.6;">
                                        {!! $sub->rendered_icon !!}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-decoration-line-through text-muted">{{ $sub->name }}</div>
                                        <div class="small text-muted">{{ $sub->notes ?: 'No notes' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-light text-dark border">{{ $sub->category }}</span>
                            </td>
                            <td>
                                <span class="fw-bold">{{ Auth::user()->formatCurrency($sub->cost) }}</span>
                            </td>
                            <td>{{ $sub->billing_cycle }}</td>
                            <td>
                                <span class="text-muted small">{{ $sub->deleted_at->format('M d, Y H:i') }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <form action="{{ route('subscriptions.restore', $sub->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 text-white">
                                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                                        </button>
                                    </form>

                                    <form action="{{ route('subscriptions.forceDelete', $sub->id) }}" method="POST" onsubmit="return confirm('Permanently delete {{ $sub->name }}? This cannot be undone!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1">
                                            <i class="bi bi-x-octagon"></i> Delete Permanently
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $trashedSubscriptions->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="display-6 mb-2 text-muted"><i class="bi bi-trash3"></i></div>
            <h6 class="fw-bold mb-1">Trash is Empty</h6>
            <p class="text-muted small mb-0">No deleted subscriptions currently in trash.</p>
        </div>
    @endif
</div>
@endsection

