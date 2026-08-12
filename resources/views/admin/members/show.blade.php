@extends('layouts.admin')

@section('content')
<!-- Header & Navigation -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('admin.members.index') }}" class="text-decoration-none text-muted small me-2">
            <i class="bi bi-arrow-left"></i> Back to Members
        </a>
        <h2 class="fw-bold mb-0 mt-1">{{ $member->name }}'s Savings & Profile</h2>
    </div>
    <div>
        <a href="{{ route('admin.members.edit', $member->id) }}" class="btn btn-outline-primary rounded-pill px-3 me-2">
            <i class="bi bi-pencil me-1"></i> Edit Profile
        </a>
        <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#adminDepositModal">
            <i class="bi bi-phone-vibrate me-1"></i> Record Deposit / M-Pesa Push
        </button>
    </div>
</div>

<!-- Profile Info Card -->
<div class="card shadow-sm border-0 mb-4 p-4">
    <div class="row align-items-center">
        <div class="col-md-2 text-center text-md-start mb-3 mb-md-0">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold fs-2 shadow-sm" style="width: 80px; height: 80px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                {{ strtoupper(substr($member->name, 0, 2)) }}
            </div>
        </div>
        <div class="col-md-6">
            <h3 class="fw-bold mb-1">{{ $member->name }}</h3>
            <p class="text-muted mb-2">
                <i class="bi bi-envelope me-1"></i> {{ $member->email }} &bull; 
                <i class="bi bi-telephone me-1"></i> {{ $member->phone ?? 'No phone registered' }}
            </p>
            <div>
                <span class="badge bg-{{ $member->role == 'admin' ? 'danger' : ($member->role == 'treasurer' ? 'warning' : 'primary') }} text-capitalize me-1">
                    {{ $member->role }}
                </span>
                <span class="badge bg-{{ $member->status == 'active' ? 'success' : 'secondary' }} text-capitalize me-1">
                    {{ $member->status }}
                </span>
                <span class="text-muted small ms-2">
                    <i class="bi bi-calendar-check me-1"></i> Member Since {{ $member->created_at ? $member->created_at->format('M Y') : 'N/A' }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="row g-3 mb-4">
    <!-- Total Savings Paid -->
    <div class="col-12 col-md-4">
        <div class="card bg-white shadow-sm h-100 p-4 border-0" style="border-left: 4px solid #10b981 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">Total Savings Paid</h6>
                    <h3 class="fw-bold mb-0 text-success">Ksh {{ number_format($totalSavings, 2) }}</h3>
                </div>
                <div class="bg-light p-3 rounded-circle text-success">
                    <i class="bi bi-safe fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Outstanding Dues -->
    <div class="col-12 col-md-4">
        <div class="card bg-white shadow-sm h-100 p-4 border-0" style="border-left: 4px solid #ef4444 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">Outstanding Dues</h6>
                    <h3 class="fw-bold mb-0 text-danger">Ksh {{ number_format($pendingDues, 2) }}</h3>
                </div>
                <div class="bg-light p-3 rounded-circle text-danger">
                    <i class="bi bi-exclamation-octagon fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Loan Balance -->
    <div class="col-12 col-md-4">
        <div class="card bg-white shadow-sm h-100 p-4 border-0" style="border-left: 4px solid #f59e0b !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">Active Loan Balance</h6>
                    @if($activeLoan)
                        <h3 class="fw-bold mb-0 text-warning">Ksh {{ number_format($activeLoan->balance, 2) }}</h3>
                    @else
                        <h4 class="fw-bold mb-0 text-muted">No Active Loan</h4>
                    @endif
                </div>
                <div class="bg-light p-3 rounded-circle text-warning">
                    <i class="bi bi-bank fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Savings & Paid Deposits History -->
<div class="card shadow-sm border-0 p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0">Savings & Paid Deposits History</h5>
            <p class="text-muted small mb-0">Complete statement of contributions, deposits, and payments recorded for {{ $member->name }}.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Month / Description</th>
                    <th>Amount Due</th>
                    <th>Amount Paid</th>
                    <th>Status</th>
                    <th>Date Paid</th>
                    <th>Payment Method / Ref</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contributions as $contrib)
                    <tr>
                        <td class="fw-semibold">
                            {{ $contrib->month }}
                        </td>
                        <td>Ksh {{ number_format($contrib->amount_due, 2) }}</td>
                        <td class="fw-bold text-success">Ksh {{ number_format($contrib->amount_paid, 2) }}</td>
                        <td>
                            @if($contrib->status == 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($contrib->status == 'partial')
                                <span class="badge bg-warning text-dark">Partial</span>
                            @else
                                <span class="badge bg-danger">Unpaid</span>
                            @endif
                        </td>
                        <td>
                            {{ $contrib->paid_at ? \Carbon\Carbon::parse($contrib->paid_at)->format('d M Y, h:i A') : '-' }}
                        </td>
                        <td>
                            @php
                                $lastPayment = $contrib->payments->last();
                            @endphp
                            @if($lastPayment)
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-phone text-success me-1"></i> {{ strtoupper($lastPayment->payment_method ?? 'M-Pesa') }}
                                    @if($lastPayment->mpesa_reference)
                                        ({{ $lastPayment->mpesa_reference }})
                                    @endif
                                </span>
                            @elseif($contrib->status == 'paid')
                                <span class="badge bg-light text-dark border"><i class="bi bi-cash-stack text-primary me-1"></i> Direct Entry</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No savings or deposit records found for this member.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-3">
        {{ $contributions->links('pagination::bootstrap-5') }}
    </div>
</div>

@push('modals')
<!-- Admin Initiated STK Push for Member Deposit -->
<div class="modal fade" id="adminDepositModal" tabindex="-1" aria-labelledby="adminDepositModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-success text-white rounded-top-4 p-4">
                <h5 class="modal-title fw-bold" id="adminDepositModalLabel">
                    <i class="bi bi-phone-vibrate me-2"></i> Record Deposit for {{ $member->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('mpesa.deposit') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Initiate an M-Pesa STK Push deposit directly to <strong>{{ $member->name }}'s</strong> phone number.
                    </p>

                    <div class="mb-3">
                        <label for="member_deposit_amount" class="form-label fw-bold">Deposit Amount (Ksh)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light text-muted fw-bold">Ksh</span>
                            <input type="number" name="amount" id="member_deposit_amount" class="form-control fw-bold text-success" placeholder="e.g. 1000" min="10" step="10" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="member_deposit_phone" class="form-label fw-bold">M-Pesa Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-telephone-fill"></i></span>
                            <input type="text" name="phone" id="member_deposit_phone" class="form-control" value="{{ $member->phone ?? '254700000000' }}" placeholder="e.g. 0712345678" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-lg rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-send-fill me-2"></i> Send M-Pesa Prompt
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
@endsection
