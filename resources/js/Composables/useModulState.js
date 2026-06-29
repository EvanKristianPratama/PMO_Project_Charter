import { ref } from 'vue';

const activeModul = ref(localStorage.getItem('active_modul') || 'all');

export function useModulState() {
    const setActiveModul = (modul) => {
        activeModul.value = modul;
        localStorage.setItem('active_modul', modul);
    };

    return {
        activeModul,
        setActiveModul,
    };
}
