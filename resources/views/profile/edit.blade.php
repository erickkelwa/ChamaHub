@extends('layouts.admin')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi bi-person-circle me-2"></i>My Profile</h2>
        <p class="text-muted mb-0">Manage your personal information and security settings</p>
    </div>
</div>

<div class="row g-4">

    {{-- ── Left Column: Profile Card ── --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 p-4 text-center h-100">
            {{-- Avatar circle --}}
            <div class="mx-auto mb-3" style="width: 120px; height: 120px; border-radius: 50%; overflow: hidden; background: linear-gradient(135deg, #4f46e5, #7c3aed); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                @if($user->profile_picture)
                    <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <span style="font-size: 3rem; font-weight: 800; color: white;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
                    </span>
                @endif
            </div>

            <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
            <span class="badge rounded-pill text-capitalize mb-3"
                  style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; font-size: 0.8rem; padding: 6px 16px;">
                {{ $user->role }}
            </span>

            <div class="text-start mt-3" style="font-size: 0.9rem;">
                <div class="d-flex align-items-center py-2" style="border-bottom: 1px solid var(--border-color);">
                    <i class="bi bi-envelope-fill me-3 text-muted"></i>
                    <span>{{ $user->email }}</span>
                </div>
                <div class="d-flex align-items-center py-2" style="border-bottom: 1px solid var(--border-color);">
                    <i class="bi bi-phone-fill me-3 text-muted"></i>
                    <span>{{ $user->phone ?? 'Not set' }}</span>
                </div>
                <div class="d-flex align-items-center py-2" style="border-bottom: 1px solid var(--border-color);">
                    <i class="bi bi-calendar-check-fill me-3 text-muted"></i>
                    <span>Joined {{ $user->created_at->format('M d, Y') }}</span>
                </div>
                <div class="d-flex align-items-center py-2">
                    <i class="bi bi-circle-fill me-3 {{ $user->status === 'active' ? 'text-success' : 'text-danger' }}" style="font-size: 0.6rem;"></i>
                    <span class="text-capitalize">{{ $user->status ?? 'active' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Right Column: Edit forms ── --}}
    <div class="col-12 col-lg-8">

        {{-- Profile Information --}}
        <div class="card border-0 p-4 mb-4">
            <h5 class="fw-bold mb-1"><i class="bi bi-pencil-square me-2 text-primary"></i>Profile Information</h5>
            <p class="text-muted small mb-4">Update your profile settings.</p>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="profileName">Full Name</label>
                        <input id="profileName" type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="profilePicture">Profile Picture (Optional)</label>
                        <input id="profilePicture" type="file" name="profile_picture" accept="image/*"
                               class="form-control @error('profile_picture') is-invalid @enderror">
                        @error('profile_picture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($user->role === 'admin' || $user->role === 'treasurer')
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="profileEmail">Email Address</label>
                            <input id="profileEmail" type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="profilePhone">Phone Number</label>
                            <input id="profilePhone" type="text" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $user->phone) }}"
                                   placeholder="e.g. 0712345678">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <div class="col-12">
                            <div class="alert alert-info py-2 small mb-0 border-0" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                                <i class="bi bi-info-circle-fill me-2"></i> Only administrators can change your registered email and phone number.
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check2-circle me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="card border-0 p-4">
            <h5 class="fw-bold mb-1"><i class="bi bi-shield-lock me-2" style="color: #f59e0b;"></i>Change Password</h5>
            <p class="text-muted small mb-4">Ensure your account is using a strong, unique password.</p>

            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="currentPassword">Current Password</label>
                        <input id="currentPassword" type="password" name="current_password"
                               class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                               placeholder="Enter your current password" required>
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="newPassword">New Password</label>
                        <input id="newPassword" type="password" name="password"
                               class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                               placeholder="Enter new password" required>
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="confirmPassword">Confirm New Password</label>
                        <input id="confirmPassword" type="password" name="password_confirmation"
                               class="form-control"
                               placeholder="Re-type new password" required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn px-4 fw-bold text-white" style="background: linear-gradient(135deg, #f59e0b, #d97706); border:none; border-radius: 10px; padding: 10px 22px;">
                        <i class="bi bi-key-fill me-1"></i> Update Password
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection
