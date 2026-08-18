@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="h3 mb-0 text-heading fw-bold">Penalties & Fines</h2>
            <p class="text-muted mb-0">Manage automatic and manual member fines.</p>
        </div>
        <div>
            <form action="{{ route('admin.reports.index') }}" method="GET" class="d-inline">
                <!-- Action to manually run command or something similar? For now just link to reports or show a button -->
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-1 opacity-75 fw-semibold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">Unpaid Fines</h6>
                            <h3 class="mb-0 fw-bold">Ksh {{ number_format($fines->where('status', 'unpaid')->sum('amount'), 2) }}</h3>
                        </div>
                        <div class="p-2 bg-white bg-opacity-25 rounded-3 shadow-sm">
                            <i class="bi bi-exclamation-octagon fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <span class="small opacity-75"><i class="bi bi-info-circle me-1"></i>Total pending collection</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-1 opacity-75 fw-semibold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">Collected Fines</h6>
                            <h3 class="mb-0 fw-bold">Ksh {{ number_format($fines->where('status', 'paid')->sum('amount'), 2) }}</h3>
                        </div>
                        <div class="p-2 bg-white bg-opacity-25 rounded-3 shadow-sm">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <span class="small opacity-75"><i class="bi bi-info-circle me-1"></i>Total successfully collected</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%); color: white;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-1 opacity-75 fw-semibold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">Waived Fines</h6>
                            <h3 class="mb-0 fw-bold">{{ $fines->where('status', 'waived')->count() }}</h3>
                        </div>
                        <div class="p-2 bg-white bg-opacity-25 rounded-3 shadow-sm">
                            <i class="bi bi-x-circle fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <span class="small opacity-75"><i class="bi bi-info-circle me-1"></i>Total waived penalties</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fines Table -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-transparent border-bottom p-4">
            <h5 class="mb-0 fw-bold"><i class="bi bi-card-list me-2 text-primary"></i>All Fines</h5>
        </div>
        <div class="card-body p-0">
            @if($fines->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Member</th>
                                <th class="py-3">Amount</th>
                                <th class="py-3">Reason</th>
                                <th class="py-3">Type</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Date</th>
                                <th class="text-end pe-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fines as $fine)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            @if($fine->user->profile_picture)
                                                <img src="{{ asset('storage/' . $fine->user->profile_picture) }}" alt="{{ $fine->user->name }}" class="rounded-circle me-3 border shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold shadow-sm" style="width: 40px; height: 40px;">
                                                    {{ strtoupper(substr($fine->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0 fw-semibold">{{ $fine->user->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="fw-bold text-danger">Ksh {{ number_format($fine->amount, 2) }}</span>
                                    </td>
                                    <td class="py-3 text-muted">
                                        {{ $fine->reason }}
                                    </td>
                                    <td class="py-3">
                                        @if($fine->type === 'meeting_absence')
                                            <span class="badge bg-secondary"><i class="bi bi-calendar-x me-1"></i>Meeting</span>
                                        @elseif($fine->type === 'late_contribution')
                                            <span class="badge bg-primary"><i class="bi bi-wallet2 me-1"></i>Contribution</span>
                                        @elseif($fine->type === 'late_loan')
                                            <span class="badge bg-warning text-dark"><i class="bi bi-bank2 me-1"></i>Loan</span>
                                        @else
                                            <span class="badge bg-dark">Other</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if($fine->status === 'paid')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Paid</span>
                                        @elseif($fine->status === 'waived')
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1" title="{{ $fine->waived_reason }}">Waived</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Unpaid</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-muted small">
                                        {{ $fine->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        @if($fine->status === 'unpaid')
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Action
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li>
                                                        <form action="{{ route('admin.fines.pay', $fine) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i class="bi bi-check-circle me-2"></i> Mark as Paid
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item text-secondary" data-bs-toggle="modal" data-bs-toggle="modal" data-bs-target="#waiveModal{{ $fine->id }}">
                                                            <i class="bi bi-x-circle me-2"></i> Waive Fine
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>

                                            <!-- Waive Modal -->
                                            <div class="modal fade" id="waiveModal{{ $fine->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form action="{{ route('admin.fines.waive', $fine) }}" method="POST" class="modal-content">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-header border-bottom-0">
                                                            <h5 class="modal-title fw-bold">Waive Fine</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-muted text-start">Are you sure you want to waive this Ksh {{ number_format($fine->amount, 2) }} fine for {{ $fine->user->name }}?</p>
                                                            <div class="mb-3 text-start">
                                                                <label class="form-label">Reason for waiving</label>
                                                                <textarea name="waived_reason" class="form-control" rows="3" required placeholder="E.g., Valid apology provided later..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light border-top-0">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-secondary">Confirm Waive</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted small"><i class="bi bi-check2-all"></i> Closed</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-shield-check display-1 text-black-50 mb-3 opacity-25" style="font-size: 5rem;"></i>
                    <h5 class="fw-bold text-heading">No Fines Issued</h5>
                    <p>All members are strictly following the rules. Great job!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
