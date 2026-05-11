import { inject } from 'vue';

function resolveRouteHelper(route) {
    if (typeof route !== 'function') {
        throw new Error('Route helper is not available. Ensure ZiggyVue is registered in resources/js/app.js.');
    }

    return route;
}

export function getRouteHelper() {
    return resolveRouteHelper(globalThis.route ?? null);
}

export function routeHelper(...args) {
    return getRouteHelper()(...args);
}

export function useRouteHelper() {
    return resolveRouteHelper(inject('route', globalThis.route ?? null));
}
