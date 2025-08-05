# Laravel Chat Room System

A real-time chat room application built with Laravel, Laravel Reverb, Bootstrap 5, and jQuery.

## Features

- **Real-time Messaging**: Live chat using Laravel Reverb WebSocket server
- **Multiple Chat Rooms**: Create and join different chat rooms
- **Room Management**: Set participant limits, private/public rooms, unique room codes
- **User Profiles**: Customizable display names, avatars, and status
- **Join/Leave Notifications**: Real-time notifications when users join or leave rooms
- **Message History**: Persistent chat history for all rooms
- **Responsive Design**: Mobile-friendly interface with Bootstrap 5

## Requirements

- PHP 8.1+
- Laravel 11+
- Node.js & NPM
- SQLite (default) or MySQL/PostgreSQL

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd reverb-test
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   ```bash
   # For SQLite (default)
   touch database/database.sqlite
   
   # Or configure MySQL/PostgreSQL in .env
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed test data**
   ```bash
   php artisan db:seed --class=ChatSeeder
   ```

8. **Build frontend assets**
   ```bash
   npm run build
   ```

## Configuration

### Laravel Reverb Setup

1. **Install broadcasting**
   ```bash
   php artisan install:broadcasting
   ```

2. **Update .env file**
   ```env
   BROADCAST_CONNECTION=reverb
   
   REVERB_APP_ID=282384
   REVERB_APP_KEY=wnsbi729yxi4cejp8sfz
   REVERB_APP_SECRET=yqug4zffxexoilcyuk3b
   REVERB_HOST="localhost"
   REVERB_PORT=8080
   REVERB_SCHEME=http
   
   VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
   VITE_REVERB_HOST="${REVERB_HOST}"
   VITE_REVERB_PORT="${REVERB_PORT}"
   VITE_REVERB_SCHEME="${REVERB_SCHEME}"
   ```

3. **Start Reverb server**
   ```bash
   php artisan reverb:start --debug
   ```

4. **Start queue worker**
   ```bash
   php artisan queue:work
   ```

## Usage

### Starting the Application

1. **Start Laravel development server**
   ```bash
   php artisan serve
   ```

2. **Start Reverb WebSocket server** (in a separate terminal)
   ```bash
   php artisan reverb:start --debug
   ```

3. **Start queue worker** (in another terminal)
   ```bash
   php artisan queue:work
   ```

### Test Users

The seeder creates these test users:
- **john@example.com** (password: password)
- **jane@example.com** (password: password)
- **bob@example.com** (password: password)

### Using the Chat System

1. **Register/Login**: Create an account or use the test users
2. **Browse Rooms**: View all available chat rooms
3. **Create Room**: Set up your own chat room with custom settings
4. **Join Room**: Enter a room using the room code or direct link
5. **Chat**: Send real-time messages and see live updates
6. **Manage Profile**: Update your display name, bio, and status

## Database Structure

### Tables

- **users**: Laravel's default user table
- **user_profiles**: Extended user information (display name, bio, avatar, status)
- **rooms**: Chat room information (name, description, owner, limits, privacy)
- **messages**: Chat messages with type (message, join, leave, system)
- **room_participants**: Many-to-many relationship for room membership

### Key Features

- **Room Codes**: Unique 6-character codes for easy room joining
- **Participant Limits**: Set maximum participants (0 = unlimited)
- **Privacy Settings**: Public or private rooms
- **Message Types**: Regular messages, join/leave notifications, system messages
- **Real-time Events**: Live broadcasting of messages and user actions

## API Endpoints

### Room Management
- `GET /rooms` - List all rooms
- `GET /rooms/create` - Create room form
- `POST /rooms` - Create new room
- `GET /rooms/{room}` - View specific room
- `GET /my-rooms` - User's owned and participating rooms
- `PUT /rooms/{room}` - Update room
- `DELETE /rooms/{room}` - Archive room
- `POST /rooms/join-by-code` - Join room by code

### Chat Operations
- `POST /chat/send-message` - Send a message
- `POST /chat/join-room` - Join a room
- `POST /chat/leave-room` - Leave a room
- `GET /chat/messages` - Get room messages
- `GET /chat/participants` - Get room participants

### Profile Management
- `GET /profile` - View profile
- `GET /profile/edit` - Edit profile form
- `PUT /profile` - Update profile
- `POST /profile/avatar` - Upload avatar
- `PUT /profile/status` - Update status

## Real-time Events

The system broadcasts these events:
- **ChatMessageSent**: When a new message is sent
- **UserJoinedRoom**: When a user joins a room
- **UserLeftRoom**: When a user leaves a room

## Frontend Technologies

- **Bootstrap 5**: Responsive UI framework
- **jQuery**: DOM manipulation and AJAX requests
- **Laravel Echo**: WebSocket client for real-time features
- **Font Awesome**: Icons

## Customization

### Adding New Message Types
1. Update the `type` enum in the messages migration
2. Add corresponding logic in the Message model
3. Update the frontend to handle new message types

### Customizing Room Features
1. Modify the Room model methods
2. Update the room creation/editing forms
3. Add new validation rules as needed

### Styling
- Custom CSS is in the main layout file
- Bootstrap classes are used for responsive design
- Font Awesome icons for visual elements

## Troubleshooting

### Common Issues

1. **Reverb not connecting**
   - Check if Reverb server is running
   - Verify .env configuration
   - Check browser console for WebSocket errors

2. **Messages not appearing**
   - Ensure queue worker is running
   - Check database connections
   - Verify event broadcasting is enabled

3. **Real-time features not working**
   - Confirm Laravel Echo is properly configured
   - Check if Vite assets are built
   - Verify WebSocket server is accessible

### Development Tips

- Use `php artisan reverb:start --debug` for detailed logging
- Check `storage/logs/laravel.log` for errors
- Use browser developer tools to debug WebSocket connections
- Test with multiple browser tabs to verify real-time functionality

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
