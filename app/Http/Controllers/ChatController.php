<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Events\UserJoinedRoom;
use App\Events\UserLeftRoom;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'content' => 'required|string|max:1000',
        ]);

        $room = Room::findOrFail($request->room_id);
        $user = Auth::user();

        // Check if user is in the room
        $isParticipant = $room->participants()
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->exists();

        if (!$isParticipant) {
            return response()->json(['error' => 'You are not a participant in this room'], 403);
        }

        $message = Message::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'content' => $request->content,
            'type' => 'message',
        ]);

        // Broadcast the message
        broadcast(new ChatMessageSent($message, $user, $room))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $message->load('user'),
        ]);
    }

    public function joinRoom(Request $request): JsonResponse
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $room = Room::findOrFail($request->room_id);
        $user = Auth::user();

        if (!$room->canJoin($user)) {
            return response()->json(['error' => 'Cannot join this room'], 403);
        }

        // Check if already in room (active participation)
        $existingParticipation = $room->participants()
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->first();

        if ($existingParticipation) {
            return response()->json(['error' => 'Already in this room'], 400);
        }

        // Check if user previously left the room (has a record with left_at)
        $previousParticipation = $room->participants()
            ->where('user_id', $user->id)
            ->whereNotNull('left_at')
            ->first();

        if ($previousParticipation) {
            // Update existing record to rejoin
            $room->participants()->updateExistingPivot($user->id, [
                'joined_at' => now(),
                'left_at' => null,
            ]);
        } else {
            // Create new participation record
            $room->participants()->attach($user->id, [
                'joined_at' => now(),
                'left_at' => null,
            ]);
        }

        // Update user status to online
        if ($user->profile) {
            $user->profile->update([
                'status' => 'online',
                'last_seen_at' => now(),
            ]);
        }

        // Create join message
        $message = Message::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'content' => $user->name . ' joined the room',
            'type' => 'join',
        ]);

        // Broadcast join event
        broadcast(new UserJoinedRoom($user, $room))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined the room',
        ]);
    }

    public function leaveRoom(Request $request): JsonResponse
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $room = Room::findOrFail($request->room_id);
        $user = Auth::user();

        // Find the participation record and update it
        $participation = $room->participants()
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->first();

        if (!$participation) {
            return response()->json(['error' => 'You are not a participant in this room'], 403);
        }

        // Update the pivot table record
        $room->participants()->updateExistingPivot($user->id, ['left_at' => now()]);

        // Update user last seen time
        if ($user->profile) {
            $user->profile->update(['last_seen_at' => now()]);
        }

        // Create leave message
        $message = Message::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'content' => $user->name . ' left the room',
            'type' => 'leave',
        ]);

        // Broadcast leave event
        broadcast(new UserLeftRoom($user, $room))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Successfully left the room',
        ]);
    }

    public function getMessages(Request $request): JsonResponse
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'limit' => 'integer|min:1|max:100',
        ]);

        $room = Room::findOrFail($request->room_id);
        $limit = $request->get('limit', 50);

        $messages = $room->messages()
            ->with('user.profile')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values(); // Ensure it's a proper array

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    public function getParticipants(Request $request): JsonResponse
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $room = Room::findOrFail($request->room_id);

        $participants = $room->participants()
            ->whereNull('left_at')
            ->with('profile')
            ->get();

        return response()->json([
            'success' => true,
            'participants' => $participants,
        ]);
    }
}
