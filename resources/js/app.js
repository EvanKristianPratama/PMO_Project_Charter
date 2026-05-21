import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/index.esm.js';

const appName = import.meta.env.VITE_APP_NAME || 'Review ITSPS';
const pages = import.meta.glob('./Pages/**/*.vue');

function normalizePagePath(path) {
    return path.replace(/\\/g, '/').replace(/^\.\/+/, './');
}

function resolvePage(name) {
    const candidates = [
        `./Pages/${name}.vue`,
        `./Pages/${name}/Index.vue`,
    ];

    for (const candidate of candidates) {
        const exact = pages[candidate];
        if (exact) {
            return exact();
        }

        const normalizedCandidate = normalizePagePath(candidate).toLowerCase();
        const foundKey = Object.keys(pages).find((key) => normalizePagePath(key).toLowerCase() === normalizedCandidate);

        if (foundKey) {
            return pages[foundKey]();
        }
    }

    throw new Error(`Page not found: ${candidates[0]}`);
}

createInertiaApp({
    title: (title) => title ? `${title} — ${appName}` : appName,
    resolve: (name) => resolvePage(name),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#6366f1',
    },
});
