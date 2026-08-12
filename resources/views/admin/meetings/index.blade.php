@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Meetings Management</h2>
    <a href="{{ route('admin.meetings.create') }}" class="btn btn-primary"><i class="bi bi-calendar-plus"></i> Schedule New Meeting</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.meetings.index') }}" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by title or venue..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>Title</th>
                        <th>Venue/Link</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meetings as $meeting)
                        <tr>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}</strong><br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('h:i A') }}</small>
                            </td>
                            <td>{{ $meeting->title }}</td>
                            <td>{{ $meeting->venue }}</td>
                            <td>{{ $meeting->creator->name ?? 'System' }}</td>
                            <td>
                                <a href="{{ route('admin.meetings.edit', $meeting->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.meetings.destroy', $meeting->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this meeting?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No meetings scheduled.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $meetings->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
