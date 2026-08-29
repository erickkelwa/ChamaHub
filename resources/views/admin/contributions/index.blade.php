@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">Contributions Management</h2>
        <p class="text-muted mb-0">Track and generate monthly contribution schedules for all members.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-warning rounded-pill px-3 shadow-sm text-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#bulkStkPushModal">
            <i class="bi bi-phone-vibrate me-1"></i> <span class="d-none d-sm-inline">Prompt Members (M-Pesa STK)</span><span class="d-sm-none">STK Prompt</span>
        </button>
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
<!-- Bulk M-Pesa STK Push Prompt Modal -->
<div class="modal fade" id="bulkStkPushModal" tabindex="-1" aria-labelledby="bulkStkPushModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-warning text-dark rounded-top-4 p-4">
                <h5 class="modal-title fw-bold" id="bulkStkPushModalLabel">
                    <i class="bi bi-phone-vibrate-fill me-2"></i> Send M-Pesa STK Push Prompt
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.contributions.bulk-stk-push') }}" method="POST" onsubmit="return confirm('Are you sure you want to trigger M-Pesa payment prompts to members phones?');">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        This tool will trigger real-time <strong>M-Pesa STK Push prompts</strong> directly to members' mobile phones asking them to enter their M-Pesa PIN for their monthly dues.
                    </p>

                    <div class="mb-3">
                        <label for="stk_month" class="form-label fw-bold">Select Month</label>
                        <input list="monthList" name="month" id="stk_month" class="form-control" value="{{ $availableMonths->first() ?? date('F Y') }}" placeholder="e.g. September 2026" required>
                        <datalist id="monthList">
                            @foreach($availableMonths as $m)
                                <option value="{{ $m }}">
                            @endforeach
                        </datalist>
                        <div class="form-text">Choose from existing months or type the month name.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Target Members</label>
                        <div class="form-check p-3 bg-light rounded-3 border mb-2">
                            <input class="form-check-input" type="radio" name="target" id="targetUnpaid" value="unpaid" checked>
                            <label class="form-check-label fw-semibold" for="targetUnpaid">
                                <i class="bi bi-exclamation-circle text-warning me-1"></i> Unpaid & Partial Members Only <span class="badge bg-success ms-1">Recommended</span>
                            </label>
                            <div class="text-muted small ms-4">Only prompts members who still owe dues for this month.</div>
                        </div>
                        <div class="form-check p-3 bg-light rounded-3 border">
                            <input class="form-check-input" type="radio" name="target" id="targetAll" value="all">
                            <label class="form-check-label fw-semibold" for="targetAll">
                                <i class="bi bi-people text-primary me-1"></i> All Members with Records
                            </label>
                            <div class="text-muted small ms-4">Prompts everyone who has a record for this month.</div>
                        </div>
                    </div>

                    <div class="alert alert-warning border-0 shadow-sm small mb-0">
                        <i class="bi bi-info-circle-fill me-2"></i> Members must have a valid Kenyan phone number (e.g., <code>07XXXXXXXX</code> or <code>2547XXXXXXXX</code>) in their profile to receive the STK push.
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark btn-lg rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-send-fill me-2"></i> Dispatch STK Push Prompts
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
