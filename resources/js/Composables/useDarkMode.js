import { onMounted, ref } from 'vue';

const STORAGE_KEY = 'darkMode';
const isDark = ref(false);
let initialized = false;

function getInitialMode() {
    if (typeof window === 'undefined') {
        return false;
    }

    const saved = window.localStorage.getItem(STORAGE_KEY);

    if (saved !== null) {
        return saved === 'true';
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches;
}

function applyMode(enabled) {
    if (typeof document === 'undefined') {
        return;
    }

    document.documentElement.classList.toggle('dark', enabled);
}

function persistMode(enabled) {
    if (typeof window !== 'undefined') {
        window.localStorage.setItem(STORAGE_KEY, String(enabled));
    }

    applyMode(enabled);
}

function setupMode() {
    if (initialized) {
        return;
    }

    isDark.value = getInitialMode();
    applyMode(isDark.value);

    initialized = true;
}

export function useDarkMode() {
    setupMode();

    onMounted(() => {
        applyMode(isDark.value);
    });

    const setDarkMode = (enabled) => {
        isDark.value = Boolean(enabled);
        persistMode(isDark.value);
    };

    const toggleDarkMode = () => {
        setDarkMode(!isDark.value);
    };

    return {
        isDark,
        setDarkMode,
        toggleDarkMode,
    };
}
