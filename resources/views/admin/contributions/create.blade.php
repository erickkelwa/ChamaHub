@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h2><a href="{{ route('admin.contributions.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i></a> Add Contribution Record</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.contributions.store') }}" method="POST">
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
                <div class="col-md-6">
                    <label class="form-label">Contribution Month (e.g. August 2026)</label>
                    <input type="text" name="month" class="form-control @error('month') is-invalid @enderror" value="{{ old('month') }}" required>
                    @error('month') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">Amount Due (Ksh)</label>
                    <input type="number" step="0.01" name="amount_due" class="form-control @error('amount_due') is-invalid @enderror" value="{{ old('amount_due') }}" required>
                    @error('amount_due') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Amount Paid (Ksh)</label>
                    <input type="number" step="0.01" name="amount_paid" class="form-control @error('amount_paid') is-invalid @enderror" value="{{ old('amount_paid', 0) }}" required>
                    @error('amount_paid') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="unpaid" {{ old('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="partial" {{ old('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.contributions.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Record</button>
            </div>
        </form>
    </div>
</div>
@endsection
