@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Reports & Analytics</h2>
        <p class="text-muted">Financial summary and group performance overview.</p>
    </div>
    <form action="{{ route('admin.reports.reminders') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary" onclick="return confirm('Send reminders to all members with unpaid contributions?')">
            <i class="bi bi-bell me-1"></i> Send Payment Reminders
        </button>
    </form>
</div>

<!-- Summary Stats -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm p-4 h-100 border-0" style="border-left: 5px solid #4f46e5 !important;">
            <h6 class="text-muted text-uppercase mb-2" style="letter-spacing: 1px; font-size: 0.8rem;">Active Members</h6>
            <h2 class="fw-bold">{{ $totalMembers }}</h2>
            <p class="text-muted mb-0"><i class="bi bi-people"></i> Registered active members</p>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm p-4 h-100 border-0" style="border-left: 5px solid #10b981 !important;">
            <h6 class="text-muted text-uppercase mb-2" style="letter-spacing: 1px; font-size: 0.8rem;">Total Contributions Collected</h6>
            <h2 class="fw-bold">Ksh {{ number_format($paidContributions, 2) }}</h2>
            <p class="text-muted mb-0"><i class="bi bi-arrow-down-circle-fill text-success"></i> Confirmed paid</p>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm p-4 h-100 border-0" style="border-left: 5px solid #ef4444 !important;">
            <h6 class="text-muted text-uppercase mb-2" style="letter-spacing: 1px; font-size: 0.8rem;">Outstanding Contributions</h6>
            <h2 class="fw-bold">Ksh {{ number_format($unpaidContributions, 2) }}</h2>
            <p class="text-muted mb-0"><i class="bi bi-exclamation-triangle text-danger"></i> Currently unpaid</p>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm p-4 h-100 border-0" style="border-left: 5px solid #f59e0b !important;">
            <h6 class="text-muted text-uppercase mb-2" style="letter-spacing: 1px; font-size: 0.8rem;">Total Loans Disbursed</h6>
            <h2 class="fw-bold">Ksh {{ number_format($totalLoansDisbursed, 2) }}</h2>
            <p class="text-muted mb-0"><i class="bi bi-bank2 text-warning"></i> Across all members</p>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm p-4 h-100 border-0" style="border-left: 5px solid #3b82f6 !important;">
            <h6 class="text-muted text-uppercase mb-2" style="letter-spacing: 1px; font-size: 0.8rem;">Active Loan Balance</h6>
            <h2 class="fw-bold">Ksh {{ number_format($activeLoansValue, 2) }}</h2>
            <p class="text-muted mb-0"><i class="bi bi-arrow-up-circle text-info"></i> Still outstanding</p>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm p-4 h-100 border-0" style="border-left: 5px solid #8b5cf6 !important;">
            <h6 class="text-muted text-uppercase mb-2" style="letter-spacing: 1px; font-size: 0.8rem;">Meetings This Year</h6>
            <h2 class="fw-bold">{{ $meetingsThisYear }}</h2>
            <p class="text-muted mb-0"><i class="bi bi-calendar-event text-purple"></i> Scheduled in {{ now()->year }}</p>
        </div>
    </div>
</div>

<!-- Contributions Chart -->
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm p-4">
            <h5 class="fw-bold mb-4">Contribution Status by Month</h5>
            <canvas id="reportChart" height="80"></canvas>
        </div>
    </div>
</div>

<!-- Contribution Table -->
<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Monthly Breakdown</h5>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-printer"></i> Print Report
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Month</th>
                        <th>Amount Collected (Ksh)</th>
                        <th>Amount Expected (Ksh)</th>
                        <th>Collection Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contributionsByMonth as $row)
                        @php $rate = $row->total_due > 0 ? round(($row->total_paid / $row->total_due) * 100, 1) : 0; @endphp
                        <tr>
                            <td class="fw-semibold">{{ $row->month }}</td>
                            <td>{{ number_format($row->total_paid, 2) }}</td>
                            <td>{{ number_format($row->total_due, 2) }}</td>
                            <td>
                                <div class="progress" style="height: 8px; width: 150px;">
                                    <div class="progress-bar bg-{{ $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger') }}"
                                         style="width: {{ $rate }}%"></div>
                                </div>
                                <small class="text-muted">{{ $rate }}%</small>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No contribution records yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('reportChart').getContext('2d');
    const labels = {!! json_encode($contributionsByMonth->pluck('month')) !!};
    const paid   = {!! json_encode($contributionsByMonth->pluck('total_paid')) !!};
    const due    = {!! json_encode($contributionsByMonth->pluck('total_due')) !!};

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels.length ? labels : ['No Data'],
            datasets: [
                {
                    label: 'Collected (Ksh)',
                    data: paid,
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderRadius: 6,
                },
                {
                    label: 'Expected (Ksh)',
                    data: due,
                    backgroundColor: 'rgba(16, 185, 129, 0.5)',
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endsection
