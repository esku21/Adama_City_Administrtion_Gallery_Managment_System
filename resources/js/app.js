import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'ACAGMS';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    
    // Resolves components in subdirectories like Pages/Admin/Bookings.vue
    resolve: (name) => resolvePageComponent(
        `./Pages/${name}.vue`, 
        import.meta.glob('./Pages/**/*.vue')
    ),

    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, window.Ziggy) // Passing window.Ziggy as a second argument is safer
            .mount(el);
    },

    progress: { 
        color: '#1e3a8a', // Adama Blue
        showSpinner: true,
    },
});