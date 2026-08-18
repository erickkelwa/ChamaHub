@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-heading fw-bold">End-of-Year Dividends</h2>
            <p class="text-muted mb-0">Calculate and distribute profits based on member contributions.</p>
        </div>
        <div>
            <form action="{{ route('admin.dividends.index') }}" method="GET" class="d-flex align-items-center">
                <label for="year" class="me-2 text-muted fw-bold mb-0">Select Year:</label>
                <select name="year" id="year" class="form-select border-0 shadow-sm" style="width: auto; min-width: 120px;" onchange="this.form.submit()">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Chama Contributions -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-1 opacity-75 fw-semibold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">Total Contributions</h6>
                            <h3 class="mb-0 fw-bold">Ksh {{ number_format($totalChamaContributions, 2) }}</h3>
                        </div>
                        <div class="p-2 bg-white bg-opacity-25 rounded-3 shadow-sm">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <span class="small opacity-75"><i class="bi bi-info-circle me-1"></i>Total collected in {{ $year }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Profit Pool -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-1 opacity-75 fw-semibold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">Total Profit Pool</h6>
                            <h3 class="mb-0 fw-bold">Ksh {{ number_format($totalProfitPool, 2) }}</h3>
                        </div>
                        <div class="p-2 bg-white bg-opacity-25 rounded-3 shadow-sm">
                            <i class="bi bi-graph-up-arrow fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <span class="small opacity-75"><i class="bi bi-info-circle me-1"></i>From loan interest (Fully Repaid)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expected Payout -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-1 opacity-75 fw-semibold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">Total Payout</h6>
                            <h3 class="mb-0 fw-bold">Ksh {{ number_format($memberDividends->sum('dividend_amount'), 2) }}</h3>
                        </div>
                        <div class="p-2 bg-white bg-opacity-25 rounded-3 shadow-sm">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <span class="small opacity-75"><i class="bi bi-info-circle me-1"></i>Sum of all calculated dividends</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Breakdown Table -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-transparent border-bottom p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Member Distribution Breakdown</h5>
            <button class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-semibold" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print Report
            </button>
        </div>
        <div class="card-body p-0">
            @if($memberDividends->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Member</th>
                                <th class="py-3">Total Contribution</th>
                                <th class="py-3" style="width: 25%;">Share (%)</th>
                                <th class="text-end pe-4 py-3">Dividend Payout</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($memberDividends as $dividend)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            @if($dividend->user->profile_picture)
                                                <img src="{{ asset('storage/' . $dividend->user->profile_picture) }}" alt="{{ $dividend->user->name }}" class="rounded-circle me-3 border shadow-sm" style="width: 44px; height: 44px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                                    {{ strtoupper(substr($dividend->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0 fw-semibold">{{ $dividend->user->name }}</h6>
                                                <span class="text-muted small text-capitalize">{{ $dividend->user->role }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="fw-medium text-heading">Ksh {{ number_format($dividend->total_contribution, 2) }}</span>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-3 shadow-sm" style="height: 8px; border-radius: 10px; background-color: rgba(0,0,0,0.05);">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $dividend->share_percent }}%; border-radius: 10px;" aria-valuenow="{{ $dividend->share_percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="text-muted small fw-bold" style="min-width: 50px;">{{ number_format($dividend->share_percent, 1) }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <span class="badge bg-success bg-opacity-10 text-success fs-6 px-3 py-2 rounded-pill border border-success border-opacity-25 shadow-sm">
                                            + Ksh {{ number_format($dividend->dividend_amount, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-folder2-open display-1 text-black-50 mb-3 opacity-25" style="font-size: 5rem;"></i>
                    <h5 class="fw-bold text-heading">No Data for {{ $year }}</h5>
                    <p>There are no recorded contributions for this year. Dividends cannot be calculated.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
