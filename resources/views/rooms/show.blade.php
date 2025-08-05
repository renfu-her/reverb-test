@extends('layouts.app')

@section('title', $room->name)

@section('content')
<div class="chat-container">
    <div class="row h-100">
        <!-- Chat Messages Area -->
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-comments"></i> {{ $room->name }}
                        </h5>
                        <small class="text-muted">
                            Created by {{ $room->owner->display_name }} • 
                            {{ $room->created_at->diffForHumans() }}
                        </small>
                    </div>
                    <div>
                        <span class="badge bg-{{ $room->is_private ? 'warning' : 'success' }} me-2">
                            {{ $room->is_private ? 'Private' : 'Public' }}
                        </span>
                        <span class="badge bg-secondary">Code: {{ $room->room_code }}</span>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="messages-container p-3" id="messagesContainer">
                        <!-- Messages will be loaded here -->
                    </div>
                </div>
                
                <div class="card-footer">
                    <form id="messageForm" class="d-flex">
                        <input type="text" class="form-control me-2" id="messageInput" 
                               placeholder="Type your message..." maxlength="1000" required>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Participants Sidebar -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-users"></i> Participants
                        <span class="badge bg-primary ms-2" id="participantCount">0</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="participants-list p-3" id="participantsList">
                        <!-- Participants will be loaded here -->
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-sm" id="joinRoomBtn" style="display: none;">
                            <i class="fas fa-sign-in-alt"></i> Join Room
                        </button>
                        <button class="btn btn-danger btn-sm" id="leaveRoomBtn" style="display: none;">
                            <i class="fas fa-sign-out-alt"></i> Leave Room
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const roomId = {{ $room->id }};
    const currentUserId = {{ Auth::id() }};
    let isInRoom = false;
    
    // Initialize chat
    loadMessages();
    loadParticipants();
    checkRoomStatus();
    
    // Join room automatically if not already in
    if (!isInRoom) {
        joinRoom();
    }
    
    // Message form submission
    $('#messageForm').on('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });
    
    // Join room button
    $('#joinRoomBtn').on('click', function() {
        joinRoom();
    });
    
    // Leave room button
    $('#leaveRoomBtn').on('click', function() {
        leaveRoom();
    });
    
    // Real-time event listeners
    window.Echo.channel('room.' + roomId)
        .listen('.message.sent', (event) => {
            addMessage(event.message, event.user, false);
        })
        .listen('.user.joined', (event) => {
            addSystemMessage(event.user.name + ' joined the room');
            loadParticipants();
        })
        .listen('.user.left', (event) => {
            addSystemMessage(event.user.name + ' left the room');
            loadParticipants();
        });
    
    function loadMessages() {
        $.get('{{ route("chat.messages") }}', { room_id: roomId })
            .done(function(response) {
                if (response.success) {
                    $('#messagesContainer').empty();
                    response.messages.forEach(function(message) {
                        const isOwn = message.user_id === currentUserId;
                        addMessage(message, message.user, isOwn);
                    });
                    scrollToBottom();
                }
            });
    }
    
    function loadParticipants() {
        $.get('{{ route("chat.participants") }}', { room_id: roomId })
            .done(function(response) {
                if (response.success) {
                    $('#participantsList').empty();
                    $('#participantCount').text(response.participants.length);
                    
                    response.participants.forEach(function(participant) {
                        const statusClass = 'status-' + (participant.profile?.status || 'offline');
                        const avatar = participant.profile?.avatar ? 
                            '/storage/' + participant.profile.avatar : 
                            'https://via.placeholder.com/32';
                        
                        $('#participantsList').append(`
                            <div class="d-flex align-items-center mb-2">
                                <img src="${avatar}" class="rounded-circle me-2" width="32" height="32">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <span class="user-status ${statusClass}"></span>
                                        <strong>${participant.display_name}</strong>
                                    </div>
                                    <small class="text-muted">${participant.profile?.status || 'offline'}</small>
                                </div>
                            </div>
                        `);
                    });
                }
            });
    }
    
    function checkRoomStatus() {
        // Check if user is already in the room
        $.get('{{ route("chat.participants") }}', { room_id: roomId })
            .done(function(response) {
                if (response.success) {
                    const isParticipant = response.participants.some(p => p.id === currentUserId);
                    isInRoom = isParticipant;
                    updateRoomButtons();
                }
            });
    }
    
    function joinRoom() {
        $.post('{{ route("chat.join-room") }}', {
            room_id: roomId,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                isInRoom = true;
                updateRoomButtons();
                loadParticipants();
            }
        })
        .fail(function(xhr) {
            const error = xhr.responseJSON?.error || 'Failed to join room';
            alert(error);
        });
    }
    
    function leaveRoom() {
        $.post('{{ route("chat.leave-room") }}', {
            room_id: roomId,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                isInRoom = false;
                updateRoomButtons();
                loadParticipants();
            }
        })
        .fail(function(xhr) {
            const error = xhr.responseJSON?.error || 'Failed to leave room';
            alert(error);
        });
    }
    
    function sendMessage() {
        const content = $('#messageInput').val().trim();
        if (!content) return;
        
        $.post('{{ route("chat.send-message") }}', {
            room_id: roomId,
            content: content,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                $('#messageInput').val('');
                addMessage(response.message, response.message.user, true);
            }
        })
        .fail(function(xhr) {
            const error = xhr.responseJSON?.error || 'Failed to send message';
            alert(error);
        });
    }
    
    function addMessage(message, user, isOwn) {
        const messageClass = isOwn ? 'own' : 'other';
        const avatar = user.profile?.avatar ? 
            '/storage/' + user.profile.avatar : 
            'https://via.placeholder.com/32';
        
        const messageHtml = `
            <div class="message ${messageClass}">
                <div class="d-flex align-items-start">
                    <img src="${avatar}" class="rounded-circle me-2" width="24" height="24">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <strong class="me-2">${user.display_name}</strong>
                            <small class="text-muted">${new Date(message.created_at).toLocaleTimeString()}</small>
                        </div>
                        <div>${message.content}</div>
                    </div>
                </div>
            </div>
        `;
        
        $('#messagesContainer').append(messageHtml);
        scrollToBottom();
    }
    
    function addSystemMessage(content) {
        const messageHtml = `
            <div class="message system">
                <small>${content}</small>
            </div>
        `;
        
        $('#messagesContainer').append(messageHtml);
        scrollToBottom();
    }
    
    function updateRoomButtons() {
        if (isInRoom) {
            $('#joinRoomBtn').hide();
            $('#leaveRoomBtn').show();
        } else {
            $('#joinRoomBtn').show();
            $('#leaveRoomBtn').hide();
        }
    }
    
    function scrollToBottom() {
        const container = $('#messagesContainer');
        container.scrollTop(container[0].scrollHeight);
    }
});
</script>
@endpush
@endsection 