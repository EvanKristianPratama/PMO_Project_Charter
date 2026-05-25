import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/index.esm.js';

const appName = import.meta.env.VITE_APP_NAME || 'Review ITSPS';
const pages = import.meta.glob('./Pages/**/*.vue');

function normalizePagePath(path) {
    return path.replace(/\\/g, '/').replace(/^\.\/+/, './').toLowerCase();
}

function normalizeRequestedPage(name) {
    return name
        .replace(/\\/g, '/')
        .replace(/^\.\/Pages\//i, '')
        .replace(/^Pages\//i, '')
        .replace(/^\.\/+/, '')
        .replace(/\.vue$/i, '')
        .replace(/\/+$/, '')
        .toLowerCase();
}

function resolvePage(name) {
    const requested = normalizeRequestedPage(name);
    const requestedWithoutIndex = requested.replace(/\/index$/, '');
    const candidates = [
        `./Pages/${name}.vue`,
        `./Pages/${name}/Index.vue`,
        `./Pages/${requested}.vue`,
        `./Pages/${requested}/Index.vue`,
        `./Pages/${requestedWithoutIndex}.vue`,
        `./Pages/${requestedWithoutIndex}/Index.vue`,
    ];

    for (const candidate of candidates) {
        const exact = pages[candidate];
        if (exact) {
            return exact();
        }

        const normalizedCandidate = normalizePagePath(candidate);
        const foundKey = Object.keys(pages).find((key) => normalizePagePath(key).toLowerCase() === normalizedCandidate);

        if (foundKey) {
            return pages[foundKey]();
        }

        const suffixMatch = Object.keys(pages).find((key) => {
            const normalizedKey = normalizePagePath(key);
            return normalizedKey.endsWith(normalizedCandidate);
        });

        if (suffixMatch) {
            return pages[suffixMatch]();
        }
    }

    throw new Error(`Page not found: ${name}. Checked: ${candidates.join(', ')}`);
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
// trigger reload
