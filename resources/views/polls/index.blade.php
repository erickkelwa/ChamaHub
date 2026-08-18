@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="h3 mb-0 text-heading fw-bold">Decisions & Polls</h2>
            <p class="text-muted mb-0">Have your say in the Chama's collective decisions.</p>
        </div>
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'treasurer')
            <button class="btn btn-primary rounded-pill shadow-sm px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#createPollModal">
                <i class="bi bi-plus-circle me-1"></i> New Proposal
            </button>
        @endif
    </div>

    <div class="row g-4">
        @forelse($polls as $poll)
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm h-100 {{ $poll->status == 'closed' ? 'opacity-75' : '' }}">
                    <div class="card-header bg-transparent border-bottom p-4 d-flex justify-content-between align-items-center">
                        <div>
                            @if($poll->status == 'active')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 mb-2"><i class="bi bi-record-circle-fill me-1"></i> Active</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2 mb-2"><i class="bi bi-lock-fill me-1"></i> Closed</span>
                            @endif
                            <h5 class="fw-bold mb-0 text-heading">{{ $poll->title }}</h5>
                        </div>
                        
                        @if($poll->status == 'active' && (auth()->user()->role === 'admin' || auth()->user()->role === 'treasurer'))
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Manage
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li>
                                        <form action="{{ route('admin.decisions.close', $poll) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to close this poll early?')">
                                                <i class="bi bi-x-circle me-2"></i> Close Poll
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted mb-4" style="white-space: pre-line;">{{ $poll->description }}</p>
                        
                        <div class="d-flex align-items-center flex-wrap gap-3 text-muted small mb-4">
                            <span><i class="bi bi-person-circle me-1"></i> Proposed by {{ $poll->creator->name }}</span>
                            <span><i class="bi bi-calendar3 me-1"></i> {{ $poll->created_at->format('M d, Y') }}</span>
                            @if($poll->expires_at && $poll->status == 'active')
                                <span class="text-warning"><i class="bi bi-hourglass-split me-1"></i> Expires {{ $poll->expires_at->diffForHumans() }}</span>
                            @endif
                        </div>

                        <!-- Voting Area -->
                        <div class="bg-light rounded-4 p-4 border">
                            @if($poll->status == 'active' && !$poll->has_voted)
                                <h6 class="fw-bold mb-3 text-heading">Cast your vote:</h6>
                                <form action="{{ route('decisions.vote', $poll) }}" method="POST" class="d-flex gap-2 flex-wrap">
                                    @csrf
                                    <button type="submit" name="choice" value="yes" class="btn btn-success flex-grow-1 py-2 fw-bold rounded-pill shadow-sm"><i class="bi bi-hand-thumbs-up-fill me-1"></i> Yes</button>
                                    <button type="submit" name="choice" value="no" class="btn btn-danger flex-grow-1 py-2 fw-bold rounded-pill shadow-sm"><i class="bi bi-hand-thumbs-down-fill me-1"></i> No</button>
                                    <button type="submit" name="choice" value="abstain" class="btn btn-secondary flex-grow-1 py-2 fw-bold rounded-pill shadow-sm"><i class="bi bi-dash-circle-fill me-1"></i> Abstain</button>
                                </form>
                            @else
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold mb-0 text-heading">Results ({{ $poll->votes->count() }} {{ Str::plural('vote', $poll->votes->count()) }})</h6>
                                    @if($poll->has_voted)
                                        <span class="badge bg-primary rounded-pill shadow-sm px-3 py-2"><i class="bi bi-check2-all me-1"></i> You voted {{ ucfirst($poll->user_vote) }}</span>
                                    @endif
                                </div>

                                <!-- Progress Bars -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small mb-1 fw-semibold text-success">
                                        <span>Yes ({{ $poll->yes_votes }})</span>
                                        <span>{{ number_format($poll->yes_percentage, 1) }}%</span>
                                    </div>
                                    <div class="progress shadow-sm" style="height: 12px; border-radius: 10px; background-color: rgba(0,0,0,0.05);">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $poll->yes_percentage }}%;"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small mb-1 fw-semibold text-danger">
                                        <span>No ({{ $poll->no_votes }})</span>
                                        <span>{{ number_format($poll->no_percentage, 1) }}%</span>
                                    </div>
                                    <div class="progress shadow-sm" style="height: 12px; border-radius: 10px; background-color: rgba(0,0,0,0.05);">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $poll->no_percentage }}%;"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="d-flex justify-content-between small mb-1 fw-semibold text-secondary">
                                        <span>Abstain ({{ $poll->abstain_votes }})</span>
                                        <span>{{ number_format($poll->abstain_percentage, 1) }}%</span>
                                    </div>
                                    <div class="progress shadow-sm" style="height: 12px; border-radius: 10px; background-color: rgba(0,0,0,0.05);">
                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $poll->abstain_percentage }}%;"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-ui-radios display-1 text-black-50 mb-3 opacity-25" style="font-size: 5rem;"></i>
                    <h5 class="fw-bold text-heading">No Decisions Yet</h5>
                    <p>There are currently no active or past proposals to vote on.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

@push('modals')
    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'treasurer')
        <!-- Create Poll Modal -->
        <div class="modal fade" id="createPollModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('admin.decisions.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                    @csrf
                    <div class="modal-header bg-primary text-white rounded-top-4 p-4 border-0">
                        <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> New Proposal</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-heading">Proposal Title</label>
                            <input type="text" name="title" class="form-control form-control-lg" required placeholder="E.g., Should we buy land in Kamakis?">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-heading">Details / Context</label>
                            <textarea name="description" class="form-control" rows="4" required placeholder="Provide context so members can make an informed decision..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-heading">Voting Deadline (Optional)</label>
                            <input type="datetime-local" name="expires_at" class="form-control">
                            <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i>Leave blank if you want to close it manually later.</div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 p-4 rounded-bottom-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Post Proposal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endpush
@endsection
