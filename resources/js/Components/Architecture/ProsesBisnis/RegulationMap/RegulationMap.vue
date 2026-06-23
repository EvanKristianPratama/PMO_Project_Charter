<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <!-- Header -->
        <div class="flex flex-col gap-4 border-b border-slate-200 px-6 py-4 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Regulation Map</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pemetaan regulasi dengan fungsi bisnis yang terkait.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <!-- Search Input -->
                <div class="relative">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari regulasi atau fungsi..."
                        class="w-64 rounded-lg border border-slate-300 bg-white pl-8 pr-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    />
                    <div class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                </div>

                <!-- Tipe Filter -->
                <select
                    v-model="selectedType"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white cursor-pointer min-w-[120px]"
                >
                    <option value="">Semua Tipe</option>
                    <option v-for="type in availableTypes" :key="type" :value="type">
                        {{ type }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Content -->
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th scope="col" class="w-12 px-6 py-3.5 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">No</th>
                        <th scope="col" class="w-24 px-6 py-3.5 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Tipe</th>
                        <th scope="col" class="w-40 px-6 py-3.5 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Nomor</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Judul Regulasi</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Fungsi Terkait</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="(reg, index) in filteredRegulations"
                        :key="reg.id"
                        class="group transition hover:bg-slate-50 dark:hover:bg-white/5"
                    >
                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs font-medium">
                            {{ index + 1 }}
                        </td>
                        <td class="px-6 py-4">
                            <span :class="getTypeBadgeClass(reg.tipe)">
                                {{ reg.tipe }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-400 text-xs font-mono">
                            {{ reg.nomor || '-' }}
                        </td>
                        <td class="px-6 py-4 text-slate-900 dark:text-slate-200 text-xs font-semibold max-w-sm truncate break-words" :title="reg.judul">
                            {{ reg.judul }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="fn in getMappedFunctions(reg.id)"
                                    :key="fn.id"
                                    class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50/50 pl-2.5 pr-2 py-0.5 text-[11px] font-medium text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10 cursor-help"
                                    :title="`${fn.name} (${fn.alias || 'Tanpa Alias'})`"
                                >
                                    <span class="font-mono text-[10px] font-bold text-slate-400 dark:text-slate-500 mr-1">
                                        {{ fn.code }}
                                    </span>
                                    <span>{{ fn.name }}</span>
                                </span>
                                <span v-if="getMappedFunctions(reg.id).length === 0" class="text-xs text-slate-400 italic">
                                    Belum ada fungsi terpetakan
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredRegulations.length === 0">
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                            Data mapping regulation tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    functions: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
});

const searchQuery = ref('');
const selectedType = ref('');

// List all available types from regulations
const availableTypes = computed(() => {
    const types = new Set(props.regulations.map(r => r.tipe).filter(Boolean));
    return Array.from(types).sort();
});

// Cache map of regulation ID to associated functions
const regulationToFunctionsMap = computed(() => {
    const map = new Map();
    
    props.regulations.forEach(reg => {
        map.set(reg.id, []);
    });
    
    props.functions.forEach(fn => {
        if (fn.regulations && Array.isArray(fn.regulations)) {
            fn.regulations.forEach(reg => {
                if (map.has(reg.id)) {
                    map.get(reg.id).push(fn);
                } else {
                    map.set(reg.id, [fn]);
                }
            });
        }
    });
    
    return map;
});

const getMappedFunctions = (regulationId) => {
    return regulationToFunctionsMap.value.get(regulationId) || [];
};

// Filter regulations based on search query and type filter
const filteredRegulations = computed(() => {
    return props.regulations.filter(reg => {
        // Tipe filter
        if (selectedType.value && reg.tipe !== selectedType.value) {
            return false;
        }
        
        // Search query filter (matches regulation title, number, type or mapped function code/name)
        if (searchQuery.value) {
            const query = searchQuery.value.toLowerCase().trim();
            const matchesReg = 
                (reg.judul || '').toLowerCase().includes(query) ||
                (reg.nomor || '').toLowerCase().includes(query) ||
                (reg.tipe || '').toLowerCase().includes(query);
                
            if (matchesReg) return true;
            
            // Check mapped functions
            const mappedFns = getMappedFunctions(reg.id);
            return mappedFns.some(fn => 
                (fn.name || '').toLowerCase().includes(query) ||
                (fn.code || '').toLowerCase().includes(query) ||
                (fn.alias || '').toLowerCase().includes(query)
            );
        }
        
        return true;
    });
});

const getTypeBadgeClass = (type) => {
    const base = 'inline-flex items-center rounded px-2 py-0.5 text-[10px] font-bold tracking-wide uppercase';
    if (!type) return `${base} bg-slate-100 text-slate-800 dark:bg-white/10 dark:text-slate-300`;
    
    const formatted = type.toUpperCase().trim();
    if (formatted === 'PTK') {
        return `${base} bg-blue-50 border border-blue-200 text-blue-700 dark:bg-blue-500/10 dark:border-blue-500/20 dark:text-blue-400`;
    }
    if (formatted === 'TKO') {
        return `${base} bg-purple-50 border border-purple-200 text-purple-700 dark:bg-purple-500/10 dark:border-purple-500/20 dark:text-purple-400`;
    }
    if (formatted === 'SKI') {
        return `${base} bg-emerald-50 border border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400`;
    }
    return `${base} bg-amber-50 border border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400`;
};
</script>

<style scoped>
.cursor-help {
    cursor: help;
}
</style>
