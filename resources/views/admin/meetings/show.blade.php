@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-heading fw-bold">Meeting Details</h2>
            <p class="text-muted mb-0">View meeting information and record attendance.</p>
        </div>
        <div>
            <a href="{{ route('admin.meetings.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Meetings
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Meeting Info -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">{{ $meeting->title }}</h5>
                    
                    <div class="mb-3">
                        <small class="text-muted text-uppercase fw-bold" style="letter-spacing: 0.5px;">Date & Time</small>
                        <p class="mb-0 fw-medium"><i class="bi bi-calendar-event me-2 text-primary"></i>{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('F j, Y, g:i A') }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted text-uppercase fw-bold" style="letter-spacing: 0.5px;">Venue</small>
                        <p class="mb-0 fw-medium"><i class="bi bi-geo-alt me-2 text-danger"></i>{{ $meeting->venue }}</p>
                    </div>

                    <div class="mb-0">
                        <small class="text-muted text-uppercase fw-bold" style="letter-spacing: 0.5px;">Agenda</small>
                        <div class="p-3 bg-light rounded mt-1 border">
                            {{ $meeting->agenda }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Register -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom p-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-clipboard-check me-2 text-success"></i>Attendance Register</h5>
                </div>
                <div class="card-body p-0">
                    <form action="{{ route('admin.meetings.attendance', $meeting->id) }}" method="POST">
                        @csrf
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th class="ps-4 py-3">Member</th>
                                        <th class="py-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        @php
                                            $currentStatus = isset($attendances[$user->id]) ? $attendances[$user->id]->status : 'present'; // Default to present
                                        @endphp
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    @if($user->profile_picture)
                                                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}" class="rounded-circle me-3 border shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold shadow-sm" style="width: 40px; height: 40px;">
                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
                                                        <small class="text-muted">{{ ucfirst($user->role) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <input type="radio" class="btn-check" name="attendance[{{ $user->id }}]" id="present_{{ $user->id }}" value="present" {{ $currentStatus == 'present' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success btn-sm rounded-pill px-3 fw-medium" for="present_{{ $user->id }}">Present</label>

                                                    <input type="radio" class="btn-check" name="attendance[{{ $user->id }}]" id="apology_{{ $user->id }}" value="apology" {{ $currentStatus == 'apology' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-warning btn-sm rounded-pill px-3 fw-medium" for="apology_{{ $user->id }}">Apology</label>

                                                    <input type="radio" class="btn-check" name="attendance[{{ $user->id }}]" id="absent_{{ $user->id }}" value="absent" {{ $currentStatus == 'absent' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-medium" for="absent_{{ $user->id }}">Absent</label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-transparent p-3 text-end">
                            <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm">
                                <i class="bi bi-save me-1"></i> Save Attendance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
