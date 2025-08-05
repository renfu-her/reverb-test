@extends('layouts.app')

@section('title', 'My Rooms')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-crown"></i> Rooms I Own</h5>
            </div>
            <div class="card-body">
                @if($ownedRooms->count() > 0)
                    @foreach($ownedRooms as $room)
                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                            <div>
                                <h6 class="mb-1">{{ $room->name }}</h6>
                                <small class="text-muted">
                                    Code: {{ $room->room_code }} • 
                                    {{ $room->max_participants > 0 ? $room->max_participants . ' max' : 'Unlimited' }}
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('rooms.show', $room) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-sign-in-alt"></i> Enter
                                </a>
                                <button class="btn btn-warning btn-sm" onclick="editRoom({{ $room->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteRoom({{ $room->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-crown fa-2x text-muted mb-3"></i>
                        <p class="text-muted">You haven't created any rooms yet.</p>
                        <a href="{{ route('rooms.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Your First Room
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users"></i> Rooms I Participate In</h5>
            </div>
            <div class="card-body">
                @if($participatingRooms->count() > 0)
                    @foreach($participatingRooms as $room)
                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                            <div>
                                <h6 class="mb-1">{{ $room->name }}</h6>
                                <small class="text-muted">
                                    Owner: {{ $room->owner->display_name }} • 
                                    Code: {{ $room->room_code }}
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('rooms.show', $room) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-sign-in-alt"></i> Enter
                                </a>
                                <button class="btn btn-danger btn-sm" onclick="leaveRoom({{ $room->id }})">
                                    <i class="fas fa-sign-out-alt"></i> Leave
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-2x text-muted mb-3"></i>
                        <p class="text-muted">You're not participating in any rooms.</p>
                        <a href="{{ route('rooms.index') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Find Rooms to Join
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to All Rooms
    </a>
    <a href="{{ route('rooms.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create New Room
    </a>
</div>

@push('scripts')
<script>
function editRoom(roomId) {
    // TODO: Implement room editing
    alert('Room editing feature coming soon!');
}

function deleteRoom(roomId) {
    if (confirm('Are you sure you want to delete this room? This action cannot be undone.')) {
        $.ajax({
            url: '/rooms/' + roomId,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Room deleted successfully!');
                    location.reload();
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.error || 'Failed to delete room';
                alert(error);
            }
        });
    }
}

function leaveRoom(roomId) {
    if (confirm('Are you sure you want to leave this room?')) {
        $.ajax({
            url: '{{ route("chat.leave-room") }}',
            method: 'POST',
            data: {
                room_id: roomId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Left room successfully!');
                    location.reload();
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.error || 'Failed to leave room';
                alert(error);
            }
        });
    }
}
</script>
@endpush
@endsection 