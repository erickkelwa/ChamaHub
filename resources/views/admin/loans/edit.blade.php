@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h2><a href="{{ route('admin.loans.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i></a> Manage Loan: {{ $loan->user->name ?? 'Unknown Member' }}</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.loans.update', $loan->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Amount Requested</label>
                    <input type="text" class="form-control" value="Ksh {{ number_format($loan->amount_requested, 2) }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Repayment Duration</label>
                    <input type="text" class="form-control" value="{{ $loan->repayment_months }} months" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Reason</label>
                    <input type="text" class="form-control" value="{{ $loan->reason }}" disabled>
                </div>
            </div>

            <hr class="my-4">
            <h5 class="mb-3">Approval Decision</h5>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="pending" {{ old('status', $loan->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ old('status', $loan->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('status', $loan->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="repaid" {{ old('status', $loan->status) == 'repaid' ? 'selected' : '' }}>Repaid</option>
                        <option value="defaulted" {{ old('status', $loan->status) == 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Amount Approved (Ksh)</label>
                    <input type="number" step="0.01" name="amount_approved" class="form-control @error('amount_approved') is-invalid @enderror" value="{{ old('amount_approved', $loan->amount_approved) }}">
                    @error('amount_approved') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Interest Rate (%)</label>
                    <input type="number" step="0.01" name="interest_rate" class="form-control @error('interest_rate') is-invalid @enderror" value="{{ old('interest_rate', $loan->interest_rate ?? 10) }}">
                    @error('interest_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <label class="form-label">Rejection Note (If rejecting)</label>
                    <textarea name="rejection_note" class="form-control @error('rejection_note') is-invalid @enderror" rows="2">{{ old('rejection_note', $loan->rejection_note) }}</textarea>
                    @error('rejection_note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Loan</button>
            </div>
        </form>
    </div>
</div>
@endsection
