@extends('layouts.app')

@section('title', 'Chat Rooms')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-comments"></i> Chat Rooms</h2>
            <a href="{{ route('rooms.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Room
            </a>
        </div>

        @if($rooms->count() > 0)
            <div class="row">
                @foreach($rooms as $room)
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5 class="card-title">{{ $room->name }}</h5>
                                    <span class="badge bg-{{ $room->is_private ? 'warning' : 'success' }}">
                                        {{ $room->is_private ? 'Private' : 'Public' }}
                                    </span>
                                </div>
                                
                                @if($room->description)
                                    <p class="card-text text-muted">{{ Str::limit($room->description, 100) }}</p>
                                @endif
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i> {{ $room->owner->display_name }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-users"></i> 
                                        {{ $room->max_participants > 0 ? $room->max_participants : '∞' }}
                                    </small>
                                </div>
                                
                                <div class="mt-3">
                                    <span class="badge bg-secondary me-2">Code: {{ $room->room_code }}</span>
                                    <span class="badge bg-info">{{ $room->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('rooms.show', $room) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-sign-in-alt"></i> Join Room
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No chat rooms available</h4>
                <p class="text-muted">Be the first to create a chat room!</p>
                <a href="{{ route('rooms.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create First Room
                </a>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-search"></i> Join by Code</h5>
            </div>
            <div class="card-body">
                <form id="joinByCodeForm">
                    <div class="mb-3">
                        <label for="room_code" class="form-label">Room Code</label>
                        <input type="text" class="form-control" id="room_code" name="room_code" 
                               placeholder="Enter 6-digit code" maxlength="6" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt"></i> Join Room
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Quick Info</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-users text-primary"></i>
                        <strong>Public Rooms:</strong> Anyone can join
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-lock text-warning"></i>
                        <strong>Private Rooms:</strong> Invitation only
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-infinity text-info"></i>
                        <strong>Unlimited:</strong> No participant limit
                    </li>
                    <li>
                        <i class="fas fa-clock text-secondary"></i>
                        <strong>Real-time:</strong> Live messaging
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#joinByCodeForm').on('submit', function(e) {
        e.preventDefault();
        
        const roomCode = $('#room_code').val().toUpperCase();
        
        $.ajax({
            url: '{{ route("rooms.join-by-code") }}',
            method: 'POST',
            data: {
                room_code: roomCode,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = '/rooms/' + response.room.id;
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.error || 'Failed to join room';
                alert(error);
            }
        });
    });
});
</script>
@endpush
@endsection 