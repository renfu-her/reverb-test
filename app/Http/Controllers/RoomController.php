<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::where('status', 'active')
            ->with('owner')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('rooms.index', compact('rooms'));
    }

    public function create(): View
    {
        return view('rooms.create');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'max_participants' => 'nullable|integer|min:0|max:1000',
            'is_private' => 'boolean',
        ]);

        $room = Room::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => Auth::id(),
            'max_participants' => $request->max_participants ?? 0,
            'is_private' => $request->boolean('is_private', false),
        ]);

        return response()->json([
            'success' => true,
            'room' => $room,
            'message' => 'Room created successfully',
        ]);
    }

    public function show(Room $room): View
    {
        $room->load(['owner', 'messages.user.profile']);
        
        return view('rooms.show', compact('room'));
    }

    public function joinByCode(Request $request): JsonResponse
    {
        $request->validate([
            'room_code' => 'required|string|size:6',
        ]);

        $room = Room::where('room_code', strtoupper($request->room_code))
            ->where('status', 'active')
            ->first();

        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        if (!$room->canJoin(Auth::user())) {
            return response()->json(['error' => 'Cannot join this room'], 403);
        }

        return response()->json([
            'success' => true,
            'room' => $room,
        ]);
    }

    public function myRooms(): View
    {
        $ownedRooms = Auth::user()->ownedRooms()
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $participatingRooms = Auth::user()->participatingRooms()
            ->where('status', 'active')
            ->wherePivotNull('left_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('rooms.my-rooms', compact('ownedRooms', 'participatingRooms'));
    }

    public function update(Request $request, Room $room): JsonResponse
    {
        // Check if user is the owner
        if ($room->owner_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'max_participants' => 'nullable|integer|min:0|max:1000',
            'is_private' => 'boolean',
        ]);

        $room->update([
            'name' => $request->name,
            'description' => $request->description,
            'max_participants' => $request->max_participants ?? 0,
            'is_private' => $request->boolean('is_private', false),
        ]);

        return response()->json([
            'success' => true,
            'room' => $room,
            'message' => 'Room updated successfully',
        ]);
    }

    public function destroy(Room $room): JsonResponse
    {
        // Check if user is the owner
        if ($room->owner_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $room->update(['status' => 'archived']);

        return response()->json([
            'success' => true,
            'message' => 'Room archived successfully',
        ]);
    }
}
