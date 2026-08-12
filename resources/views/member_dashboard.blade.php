@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">My Member Dashboard 👋</h2>
        <p class="text-muted mb-0">
            Welcome back, {{ auth()->user()->name }}. Track your personal savings and loans here.
        </p>
    </div>
    <button class="btn btn-success btn-lg rounded-pill shadow-sm px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#depositMpesaModal">
        <i class="bi bi-phone-vibrate me-2"></i> Deposit / Save Money
    </button>
</div>

<!-- Personal Stats -->
<div class="row g-3 mb-4">
    <!-- Total Savings -->
    <div class="col-12 col-md-4">
        <div class="card bg-white shadow-sm h-100 p-4 border-0" style="border-left: 4px solid #10b981 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">My Total Savings</h6>
                    <h3 class="fw-bold mb-0 text-success">Ksh {{ number_format($myTotalSavings, 2) }}</h3>
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
                    <h3 class="fw-bold mb-0 text-danger">Ksh {{ number_format($myUnpaidDues, 2) }}</h3>
                </div>
                <div class="bg-light p-3 rounded-circle text-danger">
                    <i class="bi bi-exclamation-octagon fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Loan -->
    <div class="col-12 col-md-4">
        <div class="card bg-white shadow-sm h-100 p-4 border-0" style="border-left: 4px solid #f59e0b !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">Active Loan Balance</h6>
                    @if($myActiveLoan)
                        <h3 class="fw-bold mb-0 text-warning">Ksh {{ number_format($myActiveLoan->balance, 2) }}</h3>
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

<div class="row g-3">
    <!-- Recent Contributions -->
    <div class="col-12 col-lg-8 mb-4">
        <div class="card shadow-sm h-100 p-4 border-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Recent Contributions</h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Month</th>
                            <th>Amount Due</th>
                            <th>Amount Paid</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myRecentContributions as $contrib)
                            <tr>
                                <td class="fw-semibold">{{ $contrib->month }}</td>
                                <td>Ksh {{ number_format($contrib->amount_due, 2) }}</td>
                                <td>Ksh {{ number_format($contrib->amount_paid, 2) }}</td>
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
                                    @if($contrib->status != 'paid')
                                        <form action="{{ route('mpesa.push') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="contribution_id" value="{{ $contrib->id }}">
                                            <input type="hidden" name="phone" value="{{ auth()->user()->phone }}">
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" onclick="return confirm('Initiate M-Pesa STK Push to {{ auth()->user()->phone }}?')">
                                                <i class="bi bi-phone"></i> Pay via M-Pesa
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted"><i class="bi bi-check-circle"></i> Settled</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No contribution records found. Click <strong>"Deposit / Save Money"</strong> above to make a deposit!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-12 col-lg-4 mb-4">
        <div class="card shadow-sm h-100 p-4 border-0">
            <h5 class="fw-bold mb-4">Quick Links</h5>
            
            <button class="btn btn-success w-100 p-3 mb-3 text-start rounded-4 shadow-sm border-0 d-flex align-items-center justify-content-between" data-bs-toggle="modal" data-bs-target="#depositMpesaModal">
                <div>
                    <i class="bi bi-phone-fill fs-5 me-2"></i> <strong>Deposit / Save Money</strong>
                    <div class="small text-white-50">Instant M-Pesa STK Push</div>
                </div>
                <i class="bi bi-arrow-right-circle fs-4"></i>
            </button>

            <a href="#" class="dashboard-btn d-block p-3 mb-3 text-start" onclick="alert('The Meetings page is accessible from the left sidebar.')">
                <i class="bi bi-calendar-event-fill me-2 fs-5"></i> View Upcoming Meetings
            </a>
            
            <div class="mt-auto pt-4 border-top">
                <p class="text-muted small mb-0">Need help? Contact your Chama administrator.</p>
            </div>
        </div>
    </div>
</div>

@push('modals')
<!-- Deposit via M-Pesa Modal -->
<div class="modal fade" id="depositMpesaModal" tabindex="-1" aria-labelledby="depositMpesaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-success text-white rounded-top-4 p-4">
                <h5 class="modal-title fw-bold" id="depositMpesaModalLabel">
                    <i class="bi bi-phone-vibrate me-2"></i> Deposit / Save via M-Pesa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('mpesa.deposit') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Enter the amount you wish to save. An M-Pesa prompt will be sent directly to your phone.
                    </p>

                    <!-- Quick Amount Buttons -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted">Quick Amount Selection</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="document.getElementById('deposit_amount').value = 500">Ksh 500</button>
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="document.getElementById('deposit_amount').value = 1000">Ksh 1,000</button>
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="document.getElementById('deposit_amount').value = 2500">Ksh 2,500</button>
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="document.getElementById('deposit_amount').value = 5000">Ksh 5,000</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deposit_amount" class="form-label fw-bold">Amount to Save (Ksh)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light text-muted fw-bold">Ksh</span>
                            <input type="number" name="amount" id="deposit_amount" class="form-control fw-bold text-success" placeholder="e.g. 1000" min="10" step="10" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deposit_phone" class="form-label fw-bold">M-Pesa Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-telephone-fill"></i></span>
                            <input type="text" name="phone" id="deposit_phone" class="form-control" value="{{ auth()->user()->phone ?? '254700000000' }}" placeholder="e.g. 0712345678 or 254712345678" required>
                        </div>
                        <div class="form-text">Ensure your phone is unlocked to receive the Safaricom PIN prompt.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-lg rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-send-fill me-2"></i> Initiate STK Push
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
@endsection
