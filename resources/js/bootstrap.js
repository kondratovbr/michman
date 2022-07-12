window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: window.PUSHER_APP_KEY,
    cluster: window.PUSHER_APP_CLUSTER,
    forceTLS: window.PUSHER_FORCE_TLS,
    // These two parameters are needed to make beyondcode/laravel-websockets work.
    // TODO: CRITICAL! DEPLOYMENT. Make sure it actually works in production with my Nginx setup and that the browser doesn't make any requests to pusher.com. The front-end library is a bit wonky on it.
    wsHost: window.WEBSOCKETS_HOST,
    wsPort: window.WEBSOCKETS_PORT,
    // This disables Pusher API statistics. beyondcode/laravel-websockets has its own statistic gathering engine.
    disableStats: true,
});

console.log(window.PUSHER_APP_KEY)
console.log(window.PUSHER_APP_CLUSTER)
console.log(window.PUSHER_FORCE_TLS)
console.log(window.WEBSOCKETS_HOST)
console.log(window.WEBSOCKETS_PORT)
