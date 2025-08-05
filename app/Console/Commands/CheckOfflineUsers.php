<?php

namespace App\Console\Commands;

use App\Models\UserProfile;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckOfflineUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-offline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for users who are offline (no heartbeat in last 5 seconds)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fiveSecondsAgo = Carbon::now()->subSeconds(5);
        
        // Find users who haven't sent a heartbeat in the last 5 seconds
        $offlineUsers = UserProfile::where('status', 'online')
            ->where(function($query) use ($fiveSecondsAgo) {
                $query->where('last_seen_at', '<', $fiveSecondsAgo)
                      ->orWhereNull('last_seen_at');
            })
            ->get();

        $count = 0;
        foreach ($offlineUsers as $profile) {
            $profile->update(['status' => 'offline']);
            $count++;
            $this->info("Marked user {$profile->user->name} as offline");
        }

        if ($count > 0) {
            $this->info("Marked {$count} users as offline");
        } else {
            $this->info("No users to mark as offline");
        }

        return 0;
    }
}
