import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';

// Vuetify
import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

// vue-toastification
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";

const toastOptions = {
  position: POSITION.TOP_RIGHT,
  timeout: 3000 , // 3 seconds
  closeOnClick: true,
  pauseOnHover: true,
  draggable: false
};

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const vuetify = createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: 'light',
  }
});

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(vuetify)
            .use(Toast, toastOptions)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
