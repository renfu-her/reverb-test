@extends('layouts.app')

@section('title', 'Create Room')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-plus"></i> Create New Chat Room</h4>
            </div>
            <div class="card-body">
                <form id="createRoomForm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Room Name *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               placeholder="Enter room name" required maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3" placeholder="Enter room description (optional)" maxlength="1000"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_participants" class="form-label">Max Participants</label>
                                <input type="number" class="form-control" id="max_participants" name="max_participants" 
                                       placeholder="0 for unlimited" min="0" max="1000" value="0">
                                <div class="form-text">Enter 0 for unlimited participants</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Room Type</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_private" id="public" value="0" checked>
                                    <label class="form-check-label" for="public">
                                        <i class="fas fa-users text-success"></i> Public Room
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_private" id="private" value="1">
                                    <label class="form-check-label" for="private">
                                        <i class="fas fa-lock text-warning"></i> Private Room
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Rooms
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Room
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Room Settings Guide</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-users text-success"></i> Public Rooms</h6>
                        <ul class="list-unstyled">
                            <li>• Anyone can join</li>
                            <li>• Visible in room list</li>
                            <li>• Great for open discussions</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-lock text-warning"></i> Private Rooms</h6>
                        <ul class="list-unstyled">
                            <li>• Invitation only</li>
                            <li>• Hidden from public list</li>
                            <li>• Perfect for private groups</li>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-infinity text-info"></i> Participant Limits</h6>
                        <ul class="list-unstyled">
                            <li>• 0 = Unlimited participants</li>
                            <li>• 1-1000 = Specific limit</li>
                            <li>• Helps manage room capacity</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-clock text-secondary"></i> Real-time Features</h6>
                        <ul class="list-unstyled">
                            <li>• Live messaging</li>
                            <li>• User join/leave notifications</li>
                            <li>• Message history</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#createRoomForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            name: $('#name').val(),
            description: $('#description').val(),
            max_participants: $('#max_participants').val() || 0,
            is_private: $('input[name="is_private"]:checked').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: '{{ route("rooms.store") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Room created successfully!');
                    window.location.href = '/rooms/' + response.room.id;
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = 'Please fix the following errors:\n';
                    Object.keys(errors).forEach(key => {
                        errorMessage += `• ${errors[key][0]}\n`;
                    });
                    alert(errorMessage);
                } else {
                    alert('Failed to create room. Please try again.');
                }
            }
        });
    });
});
</script>
@endpush
@endsection 