import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

const realtimeEnabled = import.meta.env.VITE_REALTIME_ENABLED === 'true'

if (realtimeEnabled && typeof window !== 'undefined') {
  window.Pusher = Pusher

  window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
    wsPort: Number(import.meta.env.VITE_REVERB_PORT || 8080),
    wssPort: Number(import.meta.env.VITE_REVERB_PORT || 8080),
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      },
    },
  })
}

export const isRealtimeEnabled = realtimeEnabled

export default typeof window !== 'undefined' ? window.Echo : null