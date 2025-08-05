<?php

namespace App\Http\Controllers;

use App\Events\UserHeartbeat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeartbeatController extends Controller
{
    /**
     * Handle user heartbeat
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Update user's last seen time
        if ($user->profile) {
            $user->profile->update([
                'status' => 'online',
                'last_seen_at' => now(),
            ]);
        }

        // Broadcast heartbeat event
        broadcast(new UserHeartbeat($user));

        return response()->json([
            'success' => true,
            'timestamp' => now()->toISOString(),
        ]);
    }
} 