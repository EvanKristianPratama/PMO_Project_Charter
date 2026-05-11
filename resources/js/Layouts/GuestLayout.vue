<script setup>
import { useDarkMode } from '@/Composables/useDarkMode';

const { isDark, toggleDarkMode } = useDarkMode();
</script>

<template>
    <div class="relative min-h-screen bg-slate-50 text-slate-900 dark:bg-[#0f0f0f] dark:text-slate-100 transition-colors duration-300">
        <button
            type="button"
            @click="toggleDarkMode"
            class="absolute right-4 top-4 z-20 rounded-lg p-2.5 text-slate-500 transition-colors hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10"
            aria-label="Toggle dark mode"
        >
            <svg v-if="isDark" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>
        <slot />
    </div>
</template>
