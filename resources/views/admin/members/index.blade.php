@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="mb-0">Members Management</h2>
    <div class="page-header-actions d-flex gap-2">
        <form action="{{ route('admin.seed-demo') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-success"><i class="bi bi-person-check-fill me-1"></i> Populate Demo Members</button>
        </form>
        <a href="{{ route('admin.members.create') }}" class="btn btn-primary"><i class="bi bi-person-plus"></i> <span class="d-none d-sm-inline">Add New Member</span><span class="d-sm-none">Add Member</span></a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.members.index') }}" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by name, email or phone" value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Total Savings</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td class="fw-semibold">{{ $member->name }}</td>
                            <td>{{ $member->email }}</td>
                            <td>{{ $member->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-success-subtle text-success fs-6 fw-bold border border-success-subtle px-3 py-1">
                                    Ksh {{ number_format($member->total_savings ?? 0, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $member->role == 'admin' ? 'danger' : ($member->role == 'treasurer' ? 'warning' : 'primary') }}">
                                    {{ ucfirst($member->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $member->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="{{ route('admin.members.show', $member->id) }}" class="btn btn-sm btn-outline-info" title="View Profile & Savings History"><i class="bi bi-eye"></i><span class="d-none d-lg-inline"> Details</span></a>
                                    <a href="{{ route('admin.members.edit', $member->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this member?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $members->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
