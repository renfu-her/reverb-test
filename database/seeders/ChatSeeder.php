<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users with profiles
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password'),
                'profile' => [
                    'display_name' => 'John Doe',
                    'bio' => 'Software developer and tech enthusiast',
                    'status' => 'online'
                ]
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => bcrypt('password'),
                'profile' => [
                    'display_name' => 'Jane Smith',
                    'bio' => 'UX Designer and creative thinker',
                    'status' => 'online'
                ]
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
                'password' => bcrypt('password'),
                'profile' => [
                    'display_name' => 'Bob Wilson',
                    'bio' => 'Project manager and team leader',
                    'status' => 'away'
                ]
            ]
        ];

        foreach ($users as $userData) {
            $profileData = $userData['profile'];
            unset($userData['profile']);
            
            $user = User::create($userData);
            UserProfile::create([
                'user_id' => $user->id,
                'display_name' => $profileData['display_name'],
                'bio' => $profileData['bio'],
                'status' => $profileData['status']
            ]);
        }

        // Create test rooms
        $rooms = [
            [
                'name' => 'General Discussion',
                'description' => 'A place for general chat and discussions',
                'owner_id' => 1,
                'max_participants' => 0,
                'is_private' => false
            ],
            [
                'name' => 'Tech Talk',
                'description' => 'Discuss the latest in technology and programming',
                'owner_id' => 1,
                'max_participants' => 10,
                'is_private' => false
            ],
            [
                'name' => 'Private Team Room',
                'description' => 'Private room for team discussions',
                'owner_id' => 2,
                'max_participants' => 5,
                'is_private' => true
            ]
        ];

        foreach ($rooms as $roomData) {
            Room::create($roomData);
        }

        $this->command->info('Chat test data seeded successfully!');
        $this->command->info('Test users:');
        $this->command->info('- john@example.com (password: password)');
        $this->command->info('- jane@example.com (password: password)');
        $this->command->info('- bob@example.com (password: password)');
    }
}
