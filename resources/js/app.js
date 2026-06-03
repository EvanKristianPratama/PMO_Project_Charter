import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/index.esm.js';

const appName = import.meta.env.VITE_APP_NAME || 'Review ITSPS';
const pages = import.meta.glob('./Pages/**/*.vue');

function normalizePagePath(path) {
    return String(path ?? '')
        .replace(/\\/g, '/')
        .replace(/^\.\//, '')
        .replace(/^\//, '')
        .replace(/^@\//, '')
        .replace(/^resources\/js\//i, '')
        .replace(/^pages\//i, '')
        .replace(/\.vue$/i, '')
        .replace(/\/+$/, '')
        .toLowerCase();
}

function resolvePage(name) {
    const requested = normalizePagePath(name);
    const requestedWithoutIndex = requested.replace(/\/index$/, '');
    const candidates = [
        requested,
        `${requested}/index`,
        requestedWithoutIndex,
        `${requestedWithoutIndex}/index`,
    ].filter(Boolean);

    const normalizedPages = Object.entries(pages).reduce((carry, [key, loader]) => {
        const normalizedKey = normalizePagePath(key);

        if (!carry[normalizedKey]) {
            carry[normalizedKey] = loader;
        }

        return carry;
    }, {});

    for (const candidate of candidates) {
        if (normalizedPages[candidate]) {
            return normalizedPages[candidate]();
        }

        const suffixMatch = Object.entries(normalizedPages).find(([key]) => key.endsWith(candidate));

        if (suffixMatch) {
            return suffixMatch[1]();
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
