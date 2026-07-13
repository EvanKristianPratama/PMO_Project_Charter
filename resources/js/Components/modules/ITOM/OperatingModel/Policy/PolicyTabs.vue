<template>
    <div class="flex flex-col justify-between dark:border-white/10 sm:flex-row sm:items-center -mt-5">
        <div class="flex items-center gap-1.5 rounded-lg bg-slate-100 p-1 dark:bg-white/5 self-start sm:self-auto">
            <!-- Regulation Tabs -->
            <Link
                v-for="reg in regulations"
                :key="reg.id"
                :href="route('itom.operating-model.policy.index', { regulation_id: reg.id })"
                class="rounded-md px-4 py-1.5 text-xs font-semibold transition-all duration-200"
                :class="isActiveRegulation(reg.id)
                    ? 'bg-white text-[#0b2545] shadow-sm dark:bg-[#1A1A1A] dark:text-blue-400'
                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'
                "
            >
                {{ toTitleCase(reg.judul) }}
            </Link>

            <!-- RACI Analysis Tab -->
            <Link
                :href="route('itom.operating-model.raci-analysis.index')"
                class="rounded-md px-4 py-1.5 text-xs font-semibold transition-all duration-200"
                :class="route().current('itom.operating-model.raci-analysis.index') || route().current('itom.operating-model.raci-analysis.manage')
                    ? 'bg-white text-[#0b2545] shadow-sm dark:bg-[#1A1A1A] dark:text-blue-400'
                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'
                "
            >
                RACI Analisis
            </Link>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    regulations: {
        type: Array,
        required: true,
    },
    selectedRegulationId: {
        type: Number,
        default: null,
    },
});

function toTitleCase(str) {
    if (!str) return '';
    return str.replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.slice(1).toLowerCase());
}

function isActiveRegulation(regId) {
    return route().current('itom.operating-model.policy.index') && props.selectedRegulationId === regId;
}
</script>
