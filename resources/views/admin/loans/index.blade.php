@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Loans Management</h2>
    <a href="{{ route('admin.loans.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> New Loan Application</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.loans.index') }}" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by member name..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Member</th>
                        <th>Requested</th>
                        <th>Approved</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td>{{ $loan->user->name ?? 'Deleted User' }}</td>
                            <td>Ksh {{ number_format($loan->amount_requested, 2) }}</td>
                            <td>
                                @if($loan->amount_approved)
                                    Ksh {{ number_format($loan->amount_approved, 2) }} <br>
                                    <small class="text-muted">@ {{ $loan->interest_rate }}%</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($loan->balance)
                                    Ksh {{ number_format($loan->balance, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($loan->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($loan->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($loan->status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @elseif($loan->status == 'repaid')
                                    <span class="badge bg-info text-dark">Repaid</span>
                                @else
                                    <span class="badge bg-secondary">Defaulted</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.loans.edit', $loan->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.loans.destroy', $loan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this loan record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No loan records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $loans->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
