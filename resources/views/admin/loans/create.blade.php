@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h2><a href="{{ route('admin.loans.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i></a> New Loan Application</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.loans.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Member</label>
                    <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">-- Select Member --</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('user_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                        @endforeach
                    </select>
                    @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Amount Requested (Ksh)</label>
                    <input type="number" step="0.01" name="amount_requested" class="form-control @error('amount_requested') is-invalid @enderror" value="{{ old('amount_requested') }}" required>
                    @error('amount_requested') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Repayment Months</label>
                    <input type="number" name="repayment_months" class="form-control @error('repayment_months') is-invalid @enderror" value="{{ old('repayment_months', 1) }}" required>
                    @error('repayment_months') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Reason for Loan</label>
                    <input type="text" name="reason" class="form-control @error('reason') is-invalid @enderror" value="{{ old('reason') }}" required>
                    @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            
            <hr class="my-4">
            <h5 class="mb-3">Approval Details <small class="text-muted fs-6">(Fill this if approving the loan right now)</small></h5>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Amount Approved (Ksh)</label>
                    <input type="number" step="0.01" name="amount_approved" class="form-control @error('amount_approved') is-invalid @enderror" value="{{ old('amount_approved') }}">
                    @error('amount_approved') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Interest Rate (%)</label>
                    <input type="number" step="0.01" name="interest_rate" class="form-control @error('interest_rate') is-invalid @enderror" value="{{ old('interest_rate', 10) }}">
                    <div class="form-text">Standard group interest rate is 10%.</div>
                    @error('interest_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Application</button>
            </div>
        </form>
    </div>
</div>
@endsection
