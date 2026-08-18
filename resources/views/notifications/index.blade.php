@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-heading fw-bold">Notifications</h2>
            <p class="text-muted mb-0">Stay updated on your loan applications and Chama activities.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom p-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-bell-fill text-primary me-2"></i> Recent Notifications</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush rounded-bottom-4">
                        @forelse($notifications as $notification)
                            <div class="list-group-item p-4 {{ !$notification->is_read ? 'bg-primary bg-opacity-10' : '' }}">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="d-flex gap-3">
                                        <!-- Icon based on type -->
                                        <div class="mt-1">
                                            @if($notification->type === 'loan_application')
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-bank2"></i>
                                                </div>
                                            @elseif(str_contains($notification->type, 'approved'))
                                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-check-lg"></i>
                                                </div>
                                            @elseif(str_contains($notification->type, 'rejected'))
                                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-x-lg"></i>
                                                </div>
                                            @else
                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-info-lg"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Content -->
                                        <div>
                                            <h6 class="mb-1 fw-bold text-heading">{{ $notification->title }}</h6>
                                            <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Timestamp -->
                                    <small class="text-muted text-nowrap ms-3">
                                        <i class="bi bi-clock me-1"></i> {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="p-5 text-center text-muted">
                                <i class="bi bi-bell-slash display-1 text-black-50 mb-3 opacity-25" style="font-size: 5rem;"></i>
                                <h5 class="fw-bold text-heading">No notifications yet</h5>
                                <p>You're all caught up! New notifications will appear here.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                @if($notifications->hasPages())
                    <div class="card-footer bg-transparent border-top p-3">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
