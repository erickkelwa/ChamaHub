@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">Contributions Management</h2>
        <p class="text-muted mb-0">Track and generate monthly contribution schedules for all members.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-success rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#generateScheduleModal">
            <i class="bi bi-magic me-1"></i> <span class="d-none d-sm-inline">Generate Monthly Dues (1-Click)</span><span class="d-sm-none">Generate Dues</span>
        </button>
        <a href="{{ route('admin.contributions.create') }}" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> <span class="d-none d-sm-inline">Add Manual Record</span><span class="d-sm-none">Add Record</span>
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.contributions.index') }}" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by member name or month..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Member Name</th>
                        <th>Month</th>
                        <th>Amount Due</th>
                        <th>Amount Paid</th>
                        <th>Status</th>
                        <th>Date Paid</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contributions as $contribution)
                        <tr>
                            <td class="fw-semibold">{{ $contribution->user->name ?? 'Deleted User' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $contribution->month }}</span></td>
                            <td>Ksh {{ number_format($contribution->amount_due, 2) }}</td>
                            <td class="fw-bold text-success">Ksh {{ number_format($contribution->amount_paid, 2) }}</td>
                            <td>
                                @if($contribution->status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($contribution->status == 'partial')
                                    <span class="badge bg-warning text-dark">Partial</span>
                                @else
                                    <span class="badge bg-danger">Unpaid</span>
                                @endif
                            </td>
                            <td>{{ $contribution->paid_at ? \Carbon\Carbon::parse($contribution->paid_at)->format('d M Y') : '-' }}</td>
                            <td>
                                <a href="{{ route('admin.contributions.edit', $contribution->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.contributions.destroy', $contribution->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this contribution record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No contribution records found. Click <strong>"Generate Monthly Dues"</strong> above to generate dues for all members!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $contributions->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

@push('modals')
<!-- Automated Monthly Schedule Generator Modal -->
<div class="modal fade" id="generateScheduleModal" tabindex="-1" aria-labelledby="generateScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4 p-4">
                <h5 class="modal-title fw-bold" id="generateScheduleModalLabel">
                    <i class="bi bi-magic me-2"></i> Generate Monthly Contribution Dues
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.contributions.generate-schedule') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        This tool will automatically create a monthly contribution record (status: unpaid) for <strong>all members</strong> in your Chama with 1 click.
                    </p>

                    <div class="mb-3">
                        <label for="schedule_month" class="form-label fw-bold">Target Month & Year</label>
                        <input type="text" name="month" id="schedule_month" class="form-control" value="{{ date('F Y') }}" placeholder="e.g. Sept 2026" maxlength="20" required>
                        <div class="form-text">Example: September 2026, October 2026</div>
                    </div>

                    <div class="mb-3">
                        <label for="schedule_amount_due" class="form-label fw-bold">Monthly Dues Amount per Member (Ksh)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light text-muted fw-bold">Ksh</span>
                            <input type="number" name="amount_due" id="schedule_amount_due" class="form-control fw-bold text-primary" value="1000" placeholder="e.g. 1000" min="1" required>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 shadow-sm small mb-0">
                        <i class="bi bi-info-circle-fill me-2"></i> Members who already have a record for this month will be skipped automatically to prevent duplicate entries.
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-rocket-takeoff-fill me-2"></i> Generate Dues Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
@endsection
