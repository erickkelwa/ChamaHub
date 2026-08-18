@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">Welcome back, {{ auth()->user()->name }}! 👋</h2>
        <p class="text-muted mb-0">
            Here is an overview of ChamaHub's performance today. 
            <span class="badge bg-secondary ms-2 text-capitalize">{{ auth()->user()->role }} Account</span>
        </p>
    </div>
    <button class="btn btn-success btn-lg rounded-pill shadow-sm px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#depositMpesaModal">
        <i class="bi bi-phone-vibrate me-2"></i> Deposit / Save Money
    </button>
</div>

<!-- Quick Stats -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card bg-white shadow-sm h-100 p-4 border-0" style="border-left: 4px solid #4f46e5 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">Total Members</h6>
                    <h3 class="fw-bold mb-0">{{ $totalMembers }}</h3>
                </div>
                <div class="bg-light p-3 rounded-circle text-primary">
                    <i class="bi bi-people fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card bg-white shadow-sm h-100 p-4 border-0" style="border-left: 4px solid #10b981 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">Total Contributions</h6>
                    <h3 class="fw-bold mb-0">Ksh {{ number_format($totalContributions, 0) }}</h3>
                </div>
                <div class="bg-light p-3 rounded-circle text-success">
                    <i class="bi bi-wallet2 fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card bg-white shadow-sm h-100 p-4 border-0" style="border-left: 4px solid #f59e0b !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">Active Loans</h6>
                    <h3 class="fw-bold mb-0">Ksh {{ number_format($totalLoans, 0) }}</h3>
                </div>
                <div class="bg-light p-3 rounded-circle text-warning">
                    <i class="bi bi-bank fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card bg-white shadow-sm h-100 p-4 border-0" style="border-left: 4px solid #ef4444 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">Pending Loans</h6>
                    <h3 class="fw-bold mb-0">{{ $pendingLoansCount }}</h3>
                </div>
                <div class="bg-light p-3 rounded-circle text-danger">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Chart Section -->
    <div class="col-12 col-lg-8 mb-4">
        <div class="card shadow-sm h-100 p-4 border-0">
            <h5 class="fw-bold mb-4">Financial Overview (Mock Data)</h5>
            <canvas id="financialChart" height="100"></canvas>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-12 col-lg-4 mb-4">
        <div class="card shadow-sm h-100 p-4">
            <h5 class="fw-bold mb-4">Quick Actions</h5>
            
            <button class="btn btn-success w-100 p-3 mb-3 text-start rounded-4 shadow-sm border-0 d-flex align-items-center justify-content-between" data-bs-toggle="modal" data-bs-target="#depositMpesaModal">
                <div>
                    <i class="bi bi-phone-fill fs-5 me-2"></i> <strong>Deposit via M-Pesa</strong>
                    <div class="small text-white-50">Instant STK Push</div>
                </div>
                <i class="bi bi-arrow-right-circle fs-4"></i>
            </button>

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'treasurer')
                <a href="{{ route('admin.members.create') }}" class="dashboard-btn d-block p-3 mb-3 text-start">
                    <i class="bi bi-person-plus-fill me-2 fs-5"></i> Add New Member
                </a>
                <a href="{{ route('admin.contributions.create') }}" class="dashboard-btn d-block p-3 mb-3 text-start">
                    <i class="bi bi-wallet2 me-2 fs-5"></i> Record Contribution
                </a>
                <a href="{{ route('admin.loans.create') }}" class="dashboard-btn d-block p-3 mb-3 text-start">
                    <i class="bi bi-bank2 me-2 fs-5"></i> Process Loan
                </a>
            @else
                <div class="alert alert-info border-0 shadow-sm">
                    <i class="bi bi-info-circle-fill me-2"></i> Only Admins and Treasurers have access to quick actions.
                </div>
                <a href="{{ route('admin.contributions.index') }}" class="dashboard-btn d-block p-3 text-start mt-3">
                    <i class="bi bi-wallet2 me-2 fs-5"></i> View My Contributions
                </a>
            @endif
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
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="document.getElementById('admin_deposit_amount').value = 500">Ksh 500</button>
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="document.getElementById('admin_deposit_amount').value = 1000">Ksh 1,000</button>
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="document.getElementById('admin_deposit_amount').value = 2500">Ksh 2,500</button>
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="document.getElementById('admin_deposit_amount').value = 5000">Ksh 5,000</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="admin_deposit_amount" class="form-label fw-bold">Amount to Save (Ksh)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light text-muted fw-bold">Ksh</span>
                            <input type="number" name="amount" id="admin_deposit_amount" class="form-control fw-bold text-success" placeholder="e.g. 1000" min="10" step="10" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="admin_deposit_phone" class="form-label fw-bold">M-Pesa Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-telephone-fill"></i></span>
                            <input type="text" name="phone" id="admin_deposit_phone" class="form-control" value="{{ auth()->user()->phone ?? '254700000000' }}" placeholder="e.g. 0712345678 or 254712345678" required>
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

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('financialChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
            datasets: [
                {
                    label: 'Contributions (Ksh)',
                    data: [12000, 15000, 14000, 18000, 22000, 25000, 23000, {{ $totalContributions > 0 ? $totalContributions : 28000 }}],
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Loans Disbursed (Ksh)',
                    data: [5000, 8000, 6000, 12000, 10000, 15000, 11000, {{ $totalLoans > 0 ? $totalLoans : 14000 }}],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
