<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <!-- Header -->
        <div class="flex flex-col gap-4 border-b border-slate-200 px-6 py-4 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-4">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">View Mapping</h2>
                
                <!-- View Switcher -->
                <div class="flex items-center gap-1 rounded-lg bg-slate-100 p-0.5 dark:bg-white/5 w-fit">
                    <button
                        @click="activeView = 'regulation'"
                        :class="[
                            'px-2.5 py-1 text-[11px] font-bold rounded-md transition-all active:scale-95',
                            activeView === 'regulation'
                                ? 'bg-white text-slate-900 shadow-sm dark:bg-[#1a1a1a] dark:text-white'
                                : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'
                        ]"
                    >
                        By Regulation
                    </button>
                    <button
                        @click="activeView = 'function'"
                        :class="[
                            'px-2.5 py-1 text-[11px] font-bold rounded-md transition-all active:scale-95',
                            activeView === 'function'
                                ? 'bg-white text-slate-900 shadow-sm dark:bg-[#1a1a1a] dark:text-white'
                                : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'
                        ]"
                    >
                        By Function
                    </button>
                </div>
            </div>
            
            <!-- Filters by Regulation -->
            <div v-if="activeView === 'regulation'" class="flex flex-wrap items-center gap-3">
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

                <!-- Status Filter -->
                <select
                    v-model="selectedStatus"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white cursor-pointer min-w-[120px]"
                >
                    <option value="">Semua Status</option>
                    <option v-for="status in availableStatuses" :key="status" :value="status">
                        {{ status }}
                    </option>
                </select>

                <!-- Expand Level Filter -->
                <select
                    v-model="expandLevel"
                    @change="handleExpandLevelChange"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white cursor-pointer min-w-[120px]"
                >
                    <option value="custom" disabled>Expand Level...</option>
                    <option value="0">Collapse All (Level 0)</option>
                    <option v-for="depth in maxDepth + 1" :key="depth" :value="depth">
                        Level {{ depth }}
                    </option>
                    <option value="all">Expand All</option>
                </select>
            </div>

            <!-- Filters by Function -->
            <div v-else-if="activeView === 'function'" class="flex flex-wrap items-center gap-3">
                <!-- Search Input -->
                <div class="relative">
                    <input
                        v-model="searchQueryFunction"
                        type="text"
                        placeholder="Cari fungsi atau regulasi..."
                        class="w-64 rounded-lg border border-slate-300 bg-white pl-8 pr-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    />
                    <div class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                </div>

                <!-- Company Filter -->
                <select
                    v-model="companyFilterId"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white cursor-pointer min-w-[140px] max-w-[200px]"
                >
                    <option value="">Semua Perusahaan</option>
                    <option v-for="comp in availableCompanies" :key="comp.id" :value="comp.id">
                        {{ comp.name }}
                    </option>
                </select>

                <!-- Status Filter -->
                <select
                    v-model="selectedStatusFunction"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white cursor-pointer min-w-[120px]"
                >
                    <option value="">Semua Status</option>
                    <option v-for="status in availableStatuses" :key="status" :value="status">
                        {{ status }}
                    </option>
                </select>

                <!-- Expand Level Filter -->
                <select
                    v-model="expandLevelFunction"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white cursor-pointer min-w-[120px]"
                >
                    <option value="custom" disabled>Expand Level...</option>
                    <option value="0">Collapse All</option>
                    <option v-for="depth in maxDepthFunction + 1" :key="depth" :value="depth">
                        Level {{ depth }}
                    </option>
                    <option value="all">Expand All</option>
                </select>
            </div>
        </div>

        <!-- Content -->
        <div v-if="activeView === 'regulation'" class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-xs text-slate-500 dark:text-slate-400">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="px-3 py-3 w-10 text-center border-r border-b border-slate-200 dark:border-white/10">No</th>
                        <th scope="col" class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10">Regulasi</th>
                        <th scope="col" class="px-3 py-3 w-48 border-b border-slate-200 dark:border-white/10">Function Mapping</th>
                    </tr>
                </thead>
                <tbody class="dark:bg-transparent">
                    <!-- Empty State -->
                    <tr v-if="visibleDocRows.length === 0">
                        <td colspan="3" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 border-b border-slate-200 dark:border-white/10">
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
                        <td class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10 max-w-[400px] break-words" :style="{ paddingLeft: (row.depth * 24 + 12) + 'px' }">
                            <div class="flex items-start gap-2">
                                <!-- Toggle Button / Indent indicator -->
                                 <div class="w-5 h-5 flex items-center justify-center shrink-0 mt-0.5">
                                     <button 
                                         v-if="row.hasChildren" 
                                         @click.stop="toggleDocExpand(row.id)" 
                                         class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none shrink-0 flex items-center justify-center"
                                     >
                                         <svg v-if="row.isExpanded" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                             <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                         </svg>
                                         <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                             <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                         </svg>
                                     </button>
                                     <span v-else-if="row.depth > 0" class="text-slate-300 dark:text-white/20 font-mono text-xs select-none">├─</span>
                                 </div>

                                <div class="flex flex-col gap-0.5 min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="font-semibold text-slate-900 dark:text-white text-xs" :title="row.judul">
                                            {{ row.judul }}
                                        </span>

                                        <!-- Status Badge -->
                                        <span v-if="row.status" :class="getStatusBadgeClass(row.status)">
                                            {{ row.status }}
                                        </span>
                                    </div>

                                    <span v-if="row.nomor" class="font-mono text-[10px] text-slate-500 dark:text-slate-400">
                                        {{ row.nomor }}
                                    </span>
                                </div>
                            </div>
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
                                    Function Not Available      
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- By Function View Component -->
        <FunctionMap
            v-else-if="activeView === 'function'"
            :functions="functions"
            :regulations="regulations"
            :search-query="searchQueryFunction"
            :company-filter-id="companyFilterId"
            :selected-status="selectedStatusFunction"
            :expand-level="expandLevelFunction"
            @update:expand-level="expandLevelFunction = $event"
        />
    </section>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import FunctionMap from './FunctionMap.vue';

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

// View switch state
const activeView = ref('regulation');

// Filters for By Regulation
const searchQuery = ref('');
const selectedStatus = ref('');

// Filters for By Function
const searchQueryFunction = ref('');
const companyFilterId = ref('');
const selectedStatusFunction = ref('');
const expandLevelFunction = ref('all');

// Helper to list companies for Function filter
const availableCompanies = computed(() => {
    const compMap = new Map();
    props.functions.forEach(fn => {
        if (fn.company) {
            compMap.set(fn.company.id, fn.company);
        }
    });
    return Array.from(compMap.values()).sort((a, b) => (a.name || '').localeCompare(b.name || ''));
});

// Helper variables for Function expand level depth
const fnMap = computed(() => new Map(props.functions.map(fn => [fn.id, fn])));

const getFnDepth = (id) => {
    let depth = 0;
    let currentId = id;
    const visited = new Set();
    while (currentId && !visited.has(currentId)) {
        visited.add(currentId);
        const node = fnMap.value.get(currentId);
        if (node?.parent_id) {
            depth++;
            currentId = node.parent_id;
        } else {
            break;
        }
    }
    return depth;
};

const maxDepthFunction = computed(() => {
    let max = 0;
    props.functions.forEach(item => {
        const d = getFnDepth(item.id);
        if (d > max) max = d;
    });
    return max;
});

// List all available statuses from regulations
const availableStatuses = computed(() => {
    const statuses = new Set(props.regulations.map(r => r.status).filter(Boolean));
    return Array.from(statuses).sort();
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

// Filter regulations based on search query and status filter
const filteredRegulations = computed(() => {
    let result = props.regulations;

    // Helper: collect all descendant IDs recursively
    const collectDescendantIds = (parentId) => {
        const ids = new Set();
        const queue = [parentId];
        while (queue.length > 0) {
            const current = queue.shift();
            props.regulations.forEach(reg => {
                if (reg.parent_id === current && !ids.has(reg.id)) {
                    ids.add(reg.id);
                    queue.push(reg.id);
                }
            });
        }
        return ids;
    };

    // Helper: collect all ancestor IDs (walk up parent chain)
    const collectAncestorIds = (reg) => {
        const ids = new Set();
        let current = reg;
        while (current.parent_id) {
            const parent = props.regulations.find(r => r.id === current.parent_id);
            if (!parent) break;
            if (ids.has(parent.id)) break; // avoid loops
            ids.add(parent.id);
            current = parent;
        }
        return ids;
    };

    // 1. Status Filter
    if (selectedStatus.value) {
        const matchedByStatus = props.regulations.filter(reg => reg.status === selectedStatus.value);
        const includedIds = new Set();

        matchedByStatus.forEach(reg => {
            includedIds.add(reg.id);
            
            const ancestors = collectAncestorIds(reg);
            ancestors.forEach(id => includedIds.add(id));

            const descendants = collectDescendantIds(reg.id);
            descendants.forEach(id => includedIds.add(id));
        });

        result = result.filter(reg => includedIds.has(reg.id));
    }

    // 2. Search Query Filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase().trim();
        const matchedBySearch = result.filter(reg => {
            const matchesReg = 
                (reg.judul || '').toLowerCase().includes(query) ||
                (reg.nomor || '').toLowerCase().includes(query) ||
                (reg.status || '').toLowerCase().includes(query);
                
            if (matchesReg) return true;
            
            const mappedFns = getMappedFunctions(reg.id);
            return mappedFns.some(fn => 
                (fn.name || '').toLowerCase().includes(query) ||
                (fn.alias || '').toLowerCase().includes(query)
            );
        });

        const includedIds = new Set();
        matchedBySearch.forEach(reg => {
            includedIds.add(reg.id);
            
            const ancestors = collectAncestorIds(reg);
            ancestors.forEach(id => includedIds.add(id));
        });

        result = result.filter(reg => includedIds.has(reg.id));
    }

    return result;
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

const regMap = computed(() => new Map(props.regulations.map(r => [r.id, r])));

const getDepth = (id) => {
    let depth = 0;
    let currentId = id;
    const visited = new Set();
    while (currentId && !visited.has(currentId)) {
        visited.add(currentId);
        const node = regMap.value.get(currentId);
        if (node?.parent_id) {
            depth++;
            currentId = node.parent_id;
        } else {
            break;
        }
    }
    return depth;
};

const maxDepth = computed(() => {
    let max = 0;
    props.regulations.forEach(item => {
        const d = getDepth(item.id);
        if (d > max) max = d;
    });
    return max;
});

const expandedDocIds = ref(new Set());
const expandLevel = ref('all');

const toggleDocExpand = (id) => {
    expandLevel.value = 'custom';
    if (expandedDocIds.value.has(id)) {
        expandedDocIds.value.delete(id);
    } else {
        expandedDocIds.value.add(id);
    }
    expandedDocIds.value = new Set(expandedDocIds.value);
};

const handleExpandLevelChange = () => {
    const val = expandLevel.value;
    if (val === 'custom') return;

    const ids = new Set();

    if (val === 'all') {
        filteredRegulations.value.forEach(item => {
            const isParent = filteredRegulations.value.some(r => r.parent_id === item.id);
            if (isParent) {
                ids.add(item.id);
            }
        });
    } else {
        const targetDepth = parseInt(val, 10);
        if (targetDepth > 0) {
            filteredRegulations.value.forEach(item => {
                const depth = getDepth(item.id);
                if (depth < targetDepth) {
                    const isParent = filteredRegulations.value.some(r => r.parent_id === item.id);
                    if (isParent) {
                        ids.add(item.id);
                    }
                }
            });
        }
    }
    expandedDocIds.value = ids;
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
    expandLevel.value = 'all';
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

// Type badge is no longer needed since Tipe column was removed

const getStatusBadgeClass = (status) => {
    const base = 'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-bold tracking-wide uppercase border';
    if (!status) return `${base} bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400`;
    
    const formatted = status.toLowerCase().trim();
    if (formatted === 'aktif' || formatted === 'active' || formatted === 'berlaku') {
        return `${base} bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400`;
    }
    if (formatted === 'draft') {
        return `${base} bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400`;
    }
    if (formatted === 'dicabut' || formatted === 'revisi' || formatted === 'expired' || formatted === 'tidak berlaku') {
        return `${base} bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400`;
    }
    return `${base} bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400`;
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
