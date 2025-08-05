import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'wnsbi729yxi4cejp8sfz',
    wsHost: import.meta.env.VITE_REVERB_HOST || '127.0.0.1',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    enableLogging: true,
    cluster: 'mt1',
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
    },
    // 添加更多調試選項
    activityTimeout: 30000,
    pongTimeout: 10000,
    maxReconnectionAttempts: 5,
    maxReconnectGap: 10000,
});

console.log('Echo initialized with config:', {
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'wnsbi729yxi4cejp8sfz',
    wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: false
});
