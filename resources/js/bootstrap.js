import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Setup Laravel Echo (hanya jika ada meta tag broadcast-driver)
const driver = document.querySelector('meta[name="broadcast-driver"]')?.content;
if (driver) {
    import('laravel-echo').then(({ default: Echo }) => {
        import('pusher-js').then(({ default: Pusher }) => {
            window.Pusher = Pusher;

            if (driver === 'pusher') {
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key:         document.querySelector('meta[name="pusher-key"]')?.content,
                    cluster:     document.querySelector('meta[name="pusher-cluster"]')?.content ?? 'ap1',
                    forceTLS:    true,
                });
            } else {
                window.Echo = new Echo({
                    broadcaster:       'reverb',
                    key:               document.querySelector('meta[name="reverb-key"]')?.content,
                    wsHost:            document.querySelector('meta[name="reverb-host"]')?.content ?? window.location.hostname,
                    wsPort:            parseInt(document.querySelector('meta[name="reverb-port"]')?.content ?? '8080'),
                    wssPort:           443,
                    forceTLS:          (document.querySelector('meta[name="reverb-scheme"]')?.content ?? 'http') === 'https',
                    enabledTransports: ['ws', 'wss'],
                });
            }
        });
    });
}
