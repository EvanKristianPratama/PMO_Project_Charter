<script setup>
import { computed } from 'vue';

const props = defineProps({
    projects: {
        type: Array,
        required: true,
    },
    selectedCompleteness: {
        type: String,
        default: '',
    },
    // We pass the calculation function to stay consistent with the table
    calculateScore: {
        type: Function,
        required: true,
    }
});

const emit = defineEmits(['update:selectedCompleteness']);

const stats = computed(() => {
    let lengkap = 0;
    let tidakLengkap = 0;

    props.projects.forEach(project => {
        const score = props.calculateScore(project);
        if (score >= 100) {
            lengkap++;
        } else {
            tidakLengkap++;
        }
    });

    return [
        { 
            id: 'lengkap', 
            label: 'Lengkap', 
            count: lengkap, 
            cls: 'bg-emerald-500 shadow-emerald-500/20',
            textCls: 'text-emerald-600 dark:text-emerald-400' 
        },
        { 
            id: 'tidak-lengkap', 
            label: 'Tidak Lengkap', 
            count: tidakLengkap, 
            cls: 'bg-rose-500 shadow-rose-500/20',
            textCls: 'text-rose-600 dark:text-rose-400' 
        }
    ];
});

const totalCount = computed(() => props.projects.length);

const toggleFilter = (id) => {
    const newValue = props.selectedCompleteness === id ? '' : id;
    emit('update:selectedCompleteness', newValue);
};
</script>

<template>
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Summary Cards -->
        <div 
            v-for="stat in stats" 
            :key="stat.id"
            @click="toggleFilter(stat.id)"
            :class="[
                'group relative cursor-pointer overflow-hidden rounded-2xl border p-4 transition-all duration-300',
                selectedCompleteness === stat.id 
                    ? 'border-slate-900 bg-slate-50 dark:border-white/40 dark:bg-white/5 ring-1 ring-slate-900 dark:ring-white/40' 
                    : 'border-slate-200 bg-white hover:border-slate-400 dark:border-white/10 dark:bg-[#171717] dark:hover:border-white/30'
            ]"
        >
            <div class="flex items-center justify-between">
                <div class="flex flex-col gap-1">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">
                        {{ stat.label }}
                    </span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">
                            {{ stat.count }}
                        </span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Inisiatif</span>
                    </div>
                </div>
                <div :class="['flex h-10 w-10 items-center justify-center rounded-xl shadow-lg transition-transform group-hover:scale-110', stat.cls]">
                    <svg v-if="stat.id === 'lengkap'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 text-white">
                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 text-white">
                        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            
            <!-- Progress Bar Mini -->
            <div class="mt-4 h-1 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-white/10">
                <div 
                    :class="['h-full transition-all duration-700', stat.cls]" 
                    :style="{ width: totalCount > 0 ? (stat.count / totalCount * 100) + '%' : '0%' }"
                ></div>
            </div>
        </div>

        <!-- Total Card -->
        <div class="flex items-center justify-between rounded-2xl border border-dashed border-slate-300 bg-slate-50/50 p-4 dark:border-white/20 dark:bg-white/5">
            <div class="flex flex-col gap-1">
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">
                    Total Overview
                </span>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-slate-900 dark:text-white">
                        {{ totalCount }}
                    </span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Project Charter</span>
                </div>
            </div>
            <div class="h-10 w-10 rounded-xl bg-slate-100 p-2 text-slate-400 dark:bg-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-full w-full">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
            </div>
        </div>
    </div>
</template>

<style scoped>
.legend-swatch {
    display: block;
    width: 12px;
    height: 12px;
    min-width: 12px;
    min-height: 12px;
    border-radius: 2px;
    flex-shrink: 0;
}
</style>