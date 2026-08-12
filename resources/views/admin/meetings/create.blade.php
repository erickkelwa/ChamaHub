@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h2><a href="{{ route('admin.meetings.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i></a> Schedule New Meeting</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.meetings.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Meeting Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date and Time</label>
                    <input type="datetime-local" name="meeting_date" class="form-control @error('meeting_date') is-invalid @enderror" value="{{ old('meeting_date') }}" required>
                    @error('meeting_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Venue (Physical location or Video call link)</label>
                    <input type="text" name="venue" class="form-control @error('venue') is-invalid @enderror" value="{{ old('venue') }}" required>
                    @error('venue') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <label class="form-label">Meeting Agenda</label>
                    <textarea name="agenda" class="form-control @error('agenda') is-invalid @enderror" rows="5" required>{{ old('agenda') }}</textarea>
                    @error('agenda') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.meetings.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-calendar-check"></i> Schedule Meeting</button>
            </div>
        </form>
    </div>
</div>
@endsection
