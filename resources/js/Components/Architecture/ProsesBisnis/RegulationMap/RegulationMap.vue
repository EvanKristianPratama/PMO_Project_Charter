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
            <table class="w-full border-collapse text-left text-xs text-slate-500 dark:text-slate-400">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="px-3 py-3 w-10 text-center border-r border-b border-slate-200 dark:border-white/10">No</th>
                        <th scope="col" class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10">Judul Regulasi</th>
                        <th scope="col" class="px-3 py-3 w-32 border-r border-b border-slate-200 dark:border-white/10">Nomor</th>
                        <th scope="col" class="px-3 py-3 w-24 text-center border-r border-b border-slate-200 dark:border-white/10">Tipe</th>
                        <th scope="col" class="px-3 py-3 w-24 text-center border-r border-b border-slate-200 dark:border-white/10">Status</th>
                        <th scope="col" class="px-3 py-3 w-48 border-b border-slate-200 dark:border-white/10">Fungsi Terkait</th>
                    </tr>
                </thead>
                <tbody class="dark:bg-transparent">
                    <!-- Empty State -->
                    <tr v-if="visibleDocRows.length === 0">
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 border-b border-slate-200 dark:border-white/10">
                            Data mapping regulation tidak ditemukan.
                        </td>
                    </tr>
                    
                    <tr
                        v-for="(row, index) in visibleDocRows"
                        :key="'doc-' + row.id"
                        class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150 animate-fade-in"
                    >
                        <!-- No -->
                        <td class="px-3 py-3 text-center font-medium text-slate-700 dark:text-slate-300 border-r border-b border-slate-200 dark:border-white/10 w-10">
                            {{ index + 1 }}
                        </td>
                        
                        <!-- Judul Regulasi -->
                        <td class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10 max-w-[300px] break-words" :style="{ paddingLeft: (row.depth * 24 + 12) + 'px' }">
                            <div class="flex items-center gap-1.5">
                                <!-- Toggle Button -->
                                <button 
                                    v-if="row.hasChildren" 
                                    @click.stop="toggleDocExpand(row.id)" 
                                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none shrink-0"
                                >
                                    <svg v-if="row.isExpanded" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </button>
                                <span v-else-if="row.depth > 0" class="text-slate-300 dark:text-white/20 mr-1 font-mono shrink-0">├─</span>
                                
                                <span :class="[row.depth === 0 ? 'font-bold text-slate-900 dark:text-white' : 'font-medium text-slate-700 dark:text-slate-300', 'text-xs']" :title="row.judul">
                                    {{ row.judul }}
                                </span>
                            </div>
                        </td>
                        
                        <!-- Nomor -->
                        <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-mono text-xs font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[120px] break-words">
                            {{ row.nomor || '-' }}
                        </td>
                        
                        <!-- Tipe -->
                        <td class="px-3 py-3 text-center border-r border-b border-slate-200 dark:border-white/10 w-24">
                            <span :class="getTypeBadgeClass(row.tipe)">
                                {{ row.tipe }}
                            </span>
                        </td>
                        
                        <!-- Status -->
                        <td class="px-3 py-3 text-center border-r border-b border-slate-200 dark:border-white/10 w-24">
                            <span :class="getStatusBadgeClass(row.status)">
                                {{ row.status || '-' }}
                            </span>
                        </td>
                        
                        <!-- Fungsi Terkait -->
                        <td class="px-3 py-3 border-b border-slate-200 dark:border-white/10 w-48">
                            <div class="flex flex-col items-start gap-1.5">
                                <span
                                    v-for="fn in getMappedFunctions(row.id)"
                                    :key="fn.id"
                                    class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50/50 pl-2.5 pr-2 py-0.5 text-[11px] font-medium text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10 cursor-help max-w-full"
                                    :title="`${fn.name} (${fn.alias || 'Tanpa Alias'})`"
                                >
                                    <span class="truncate" :title="fn.name">{{ fn.name }}</span>
                                </span>
                                <span v-if="getMappedFunctions(row.id).length === 0" class="text-xs text-slate-400 italic">
                                    Belum ada fungsi terpetakan
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';

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
    // Find direct matches first
    const matched = props.regulations.filter(reg => {
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
                (fn.alias || '').toLowerCase().includes(query)
            );
        }
        
        return true;
    });

    // If there are no active filters, return the full list of regulations
    if (!searchQuery.value && !selectedType.value) {
        return props.regulations;
    }

    // To preserve hierarchy, walk up the parent chain and include all ancestor nodes
    const includedIds = new Set();
    
    const collectAncestorIds = (reg) => {
        let current = reg;
        while (current.parent_id) {
            const parent = props.regulations.find(r => r.id === current.parent_id);
            if (!parent) break;
            if (includedIds.has(parent.id)) break; // prevent loops
            includedIds.add(parent.id);
            current = parent;
        }
    };

    matched.forEach(reg => {
        includedIds.add(reg.id);
        collectAncestorIds(reg);
    });

    return props.regulations.filter(reg => includedIds.has(reg.id));
});

// Tree hierarchy computing
// If a child's parent is absent from the filtered list (orphaned), treat it as a root.
const documentTree = computed(() => {
    const map = {};
    const roots = [];

    filteredRegulations.value.forEach(reg => {
        map[reg.id] = { ...reg, children: [] };
    });

    filteredRegulations.value.forEach(reg => {
        const mapped = map[reg.id];
        if (reg.parent_id && map[reg.parent_id]) {
            // Parent exists in the current filtered list → attach as child
            map[reg.parent_id].children.push(mapped);
        } else {
            // No parent in the filtered list → promote to root
            roots.push(mapped);
        }
    });

    return roots;
});

const expandedDocIds = ref(new Set());

const toggleDocExpand = (id) => {
    if (expandedDocIds.value.has(id)) {
        expandedDocIds.value.delete(id);
    } else {
        expandedDocIds.value.add(id);
    }
    expandedDocIds.value = new Set(expandedDocIds.value);
};

const visibleDocRows = computed(() => {
    const rows = [];
    
    const traverse = (node, depth = 0) => {
        const hasChildren = node.children && node.children.length > 0;
        const isExpanded = expandedDocIds.value.has(node.id);
        
        rows.push({
            ...node,
            depth,
            hasChildren,
            isExpanded
        });
        
        if (hasChildren && isExpanded) {
            node.children.forEach(child => {
                traverse(child, depth + 1);
            });
        }
    };
    
    documentTree.value.forEach(root => {
        traverse(root, 0);
    });
    
    return rows;
});

const initializeExpandedDocs = () => {
    const ids = new Set();
    filteredRegulations.value.forEach(reg => {
        const isParent = filteredRegulations.value.some(r => r.parent_id === reg.id);
        if (isParent) {
            ids.add(reg.id);
        }
    });
    expandedDocIds.value = ids;
};

onMounted(() => {
    initializeExpandedDocs();
});

// Re-initialize expanded state whenever filtered regulations change
watch(
    () => filteredRegulations.value,
    () => {
        initializeExpandedDocs();
    },
    { deep: false }
);

const getTypeBadgeClass = (type) => {
    const base = 'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-bold tracking-wide uppercase border';
    if (!type) return `${base} bg-slate-100 border-slate-200 text-slate-800 dark:bg-white/10 dark:text-slate-300`;
    
    const formatted = type.toUpperCase().trim();
    if (formatted === 'PTK') {
        return `${base} bg-blue-50 border-blue-200 text-blue-700 dark:bg-blue-500/10 dark:border-blue-500/20 dark:text-blue-400`;
    }
    if (formatted === 'TKO') {
        return `${base} bg-purple-50 border-purple-200 text-purple-700 dark:bg-purple-500/10 dark:border-purple-500/20 dark:text-purple-400`;
    }
    if (formatted === 'SKI') {
        return `${base} bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400`;
    }
    return `${base} bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400`;
};

const getStatusBadgeClass = (status) => {
    const base = 'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-bold tracking-wide uppercase border';
    if (!status) return `${base} bg-slate-100 border-slate-200 text-slate-800 dark:bg-white/10 dark:text-slate-300`;
    
    const formatted = status.toUpperCase().trim();
    if (formatted === 'AKTIF' || formatted === 'ACTIVE' || formatted === 'BERLAKU') {
        return `${base} bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400`;
    }
    if (formatted === 'DRAFT') {
        return `${base} bg-blue-50 border-blue-200 text-blue-700 dark:bg-blue-500/10 dark:border-blue-500/20 dark:text-blue-400`;
    }
    if (formatted === 'REVISI' || formatted === 'EXPIRED' || formatted === 'TIDAK BERLAKU') {
        return `${base} bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400`;
    }
    return `${base} bg-slate-100 border-slate-200 text-slate-800 dark:bg-white/10 dark:text-slate-300`;
};
</script>

<style scoped>
.cursor-help {
    cursor: help;
}

.animate-fade-in {
    animation: fadeIn 0.25s ease-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
