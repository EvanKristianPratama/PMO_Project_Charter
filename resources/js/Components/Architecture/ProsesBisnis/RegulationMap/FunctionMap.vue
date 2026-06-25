<template>
    <div class="overflow-x-auto">
        <table class="w-full divide-y divide-slate-200 text-sm dark:divide-white/10 text-left border-collapse">
            <thead class="bg-slate-50 dark:bg-white/5">
                <tr>
                    <th scope="col" class="px-0 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-16">Company</th>
                    <th scope="col" class="px-4 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-80">Fungsi</th>
                    <th scope="col" class="px-4 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-24">Alias</th>
                    <th scope="col" class="px-4 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Regulation Mapping</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                <!-- Empty State -->
                <tr v-if="visibleRows.length === 0">
                    <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                        Data mapping function tidak ditemukan.
                    </td>
                </tr>
                
                <tr
                    v-for="row in visibleRows"
                    :key="'fn-row-' + row.id"
                    class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150 animate-fade-in align-top"
                >
                    <!-- Perusahaan (Rowspanned) -->
                    <td 
                        v-if="row.companyRowspan > 0"
                        :rowspan="row.companyRowspan"
                        class="px-2 py-2 text-slate-600 dark:text-slate-300 text-xs whitespace-normal break-words max-w-[80px] align-top border-r border-slate-100 dark:border-white/5 bg-slate-50/60 dark:bg-white/[0.02]"
                    >
                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ row.company?.name || '-' }}</span>
                    </td>
                    
                    <!-- Nama Fungsi -->
                    <td 
                        class="px-4 py-2 text-slate-900 dark:text-white text-xs break-words font-medium align-top border-r border-slate-100 dark:border-white/5 w-80" 
                        :style="{ paddingLeft: (row.depth * 24 + 16) + 'px' }"
                    >
                        <div class="flex items-center gap-2">
                            <!-- Toggle Button / Indent indicator -->
                            <div class="w-5 h-5 flex items-center justify-center shrink-0">
                                <button 
                                    v-if="row.hasChildren" 
                                    @click.stop="toggleExpand(row.id)" 
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

                            <span>
                                {{ row.name }}
                            </span>
                        </div>
                    </td>

                    <!-- Alias -->
                    <td class="px-4 py-2 text-slate-600 dark:text-slate-300 text-xs align-top border-r border-slate-100 dark:border-white/5">
                        {{ row.alias || '-' }}
                    </td>
                    
                    <!-- Regulation Mapping -->
                    <td class="px-4 py-2 text-slate-600 dark:text-slate-300 text-xs align-top">
                        <ul v-if="getMappedRegulations(row).length > 0" class="space-y-2 list-none">
                            <li
                                v-for="reg in getMappedRegulations(row)"
                                :key="reg.id"
                                class="flex items-start gap-1 whitespace-pre-wrap leading-relaxed text-[11px]"
                            >
                                <span class="shrink-0 select-none text-slate-400 dark:text-slate-600">-</span>
                                <span class="flex flex-col items-start text-left">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <Link 
                                            :href="route('policy.procedure.index', { regulation_id: reg.id })" 
                                            class="font-semibold text-slate-900 dark:text-white hover:underline hover:text-[#821f44] dark:hover:text-[#db588c]"
                                        >
                                            {{ reg.judul }}
                                        </Link>
                                        <span v-if="reg.status" :class="getStatusBadgeClass(reg.status)">
                                            {{ reg.status }}
                                        </span>
                                    </div>
                                    <span v-if="reg.nomor" class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5">
                                        {{ reg.nomor }}
                                    </span>
                                </span>
                            </li>
                        </ul>
                        <span v-else class="text-xs text-slate-400 italic select-none">
                            Function Not Available
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    functions: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
    searchQuery: {
        type: String,
        default: '',
    },
    companyFilterId: {
        type: [String, Number],
        default: '',
    },
    selectedStatus: {
        type: String,
        default: '',
    },
    expandLevel: {
        type: String,
        default: 'all',
    },
});

const emit = defineEmits(['update:expandLevel']);

const expandedIds = ref(new Set());

// Map function by ID for fast lookup
const fnMap = computed(() => new Map(props.functions.map(fn => [fn.id, fn])));

// Compute Depth of each function
const getDepth = (id) => {
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

// Filter functions based on props
const filteredFunctions = computed(() => {
    let result = props.functions;

    // 1. Company Filter
    if (props.companyFilterId) {
        const compId = Number(props.companyFilterId);
        result = result.filter(fn => Number(fn.company_id) === compId);
    }

    // Helper: collect all descendant IDs recursively
    const collectDescendantIds = (parentId) => {
        const ids = new Set();
        const queue = [parentId];
        while (queue.length > 0) {
            const current = queue.shift();
            props.functions.forEach(fn => {
                if (fn.parent_id === current && !ids.has(fn.id)) {
                    ids.add(fn.id);
                    queue.push(fn.id);
                }
            });
        }
        return ids;
    };

    // Helper: collect all ancestor IDs
    const collectAncestorIds = (fn) => {
        const ids = new Set();
        let current = fn;
        while (current.parent_id) {
            const parent = props.functions.find(f => f.id === current.parent_id);
            if (!parent) break;
            if (ids.has(parent.id)) break;
            ids.add(parent.id);
            current = parent;
        }
        return ids;
    };

    // 2. Regulation Status Filter
    if (props.selectedStatus) {
        const statusVal = props.selectedStatus.toLowerCase().trim();
        const matchedByStatus = result.filter(fn => {
            return (fn.regulations || []).some(reg => 
                (reg.status || '').toLowerCase().trim() === statusVal
            );
        });

        const includedIds = new Set();
        matchedByStatus.forEach(fn => {
            includedIds.add(fn.id);
            collectAncestorIds(fn).forEach(id => includedIds.add(id));
            collectDescendantIds(fn.id).forEach(id => includedIds.add(id));
        });

        result = result.filter(fn => includedIds.has(fn.id));
    }

    // 3. Search Query Filter
    if (props.searchQuery) {
        const query = props.searchQuery.toLowerCase().trim();
        const matchedBySearch = result.filter(fn => {
            const matchesFn = 
                (fn.name || '').toLowerCase().includes(query) ||
                (fn.alias || '').toLowerCase().includes(query) ||
                (fn.deskripsi || '').toLowerCase().includes(query);
                
            if (matchesFn) return true;
            
            // Or matches mapped regulations (filtered by status if selectedStatus is set)
            const regs = fn.regulations || [];
            return regs.some(reg => {
                if (props.selectedStatus && (reg.status || '').toLowerCase().trim() !== props.selectedStatus.toLowerCase().trim()) {
                    return false;
                }
                return (reg.judul || '').toLowerCase().includes(query) ||
                       (reg.nomor || '').toLowerCase().includes(query);
            });
        });

        const includedIds = new Set();
        matchedBySearch.forEach(fn => {
            includedIds.add(fn.id);
            collectAncestorIds(fn).forEach(id => includedIds.add(id));
        });

        result = result.filter(fn => includedIds.has(fn.id));
    }

    return result;
});

// Build Tree Hierarchy
const functionTree = computed(() => {
    const map = {};
    const roots = [];

    filteredFunctions.value.forEach(item => {
        map[item.id] = { ...item, children: [] };
    });

    filteredFunctions.value.forEach(item => {
        const mapped = map[item.id];
        if (item.parent_id && map[item.parent_id]) {
            map[item.parent_id].children.push(mapped);
        } else {
            roots.push(mapped);
        }
    });

    const sort = (nodes) => {
        nodes.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
        nodes.forEach(n => sort(n.children));
    };
    sort(roots);

    return roots;
});

// Build active visible rows
const visibleRows = computed(() => {
    const rows = [];
    
    const traverse = (node, depth = 0) => {
        const hasChildren = node.children && node.children.length > 0;
        const isExpanded = expandedIds.value.has(node.id);
        
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
    
    functionTree.value.forEach(root => {
        traverse(root, 0);
    });
    
    // Compute companyRowspan: for each consecutive group sharing same company_id
    let i = 0;
    while (i < rows.length) {
        const companyId = rows[i].company_id ?? null;
        let span = 1;
        while (i + span < rows.length && (rows[i + span].company_id ?? null) === companyId) {
            span++;
        }
        rows[i].companyRowspan = span;
        for (let j = 1; j < span; j++) {
            rows[i + j].companyRowspan = 0;
        }
        i += span;
    }
    
    return rows;
});

const getMappedRegulations = (fn) => {
    let regs = fn.regulations || [];
    if (props.selectedStatus) {
        const statusVal = props.selectedStatus.toLowerCase().trim();
        regs = regs.filter(reg => (reg.status || '').toLowerCase().trim() === statusVal);
    }
    return regs;
};

// Toggle individual node expansion
const toggleExpand = (id) => {
    emit('update:expandLevel', 'custom');
    if (expandedIds.value.has(id)) {
        expandedIds.value.delete(id);
    } else {
        expandedIds.value.add(id);
    }
    expandedIds.value = new Set(expandedIds.value);
};

// Apply bulk expansion based on expandLevel changes
const handleExpandLevelChange = (val) => {
    if (val === 'custom') return;

    const ids = new Set();

    if (val === 'all') {
        filteredFunctions.value.forEach(item => {
            const isParent = filteredFunctions.value.some(r => r.parent_id === item.id);
            if (isParent) {
                ids.add(item.id);
            }
        });
    } else {
        const targetDepth = parseInt(val, 10);
        if (targetDepth > 0) {
            filteredFunctions.value.forEach(item => {
                const depth = getDepth(item.id);
                if (depth < targetDepth) {
                    const isParent = filteredFunctions.value.some(r => r.parent_id === item.id);
                    if (isParent) {
                        ids.add(item.id);
                    }
                }
            });
        }
    }
    expandedIds.value = ids;
};

const initializeExpanded = () => {
    const ids = new Set();
    filteredFunctions.value.forEach(item => {
        const isParent = filteredFunctions.value.some(r => r.parent_id === item.id);
        if (isParent) {
            ids.add(item.id);
        }
    });
    expandedIds.value = ids;
    emit('update:expandLevel', 'all');
};

onMounted(() => {
    initializeExpanded();
});

watch(
    () => props.expandLevel,
    (newVal) => {
        handleExpandLevelChange(newVal);
    }
);

watch(
    () => filteredFunctions.value,
    () => {
        initializeExpanded();
    },
    { deep: false }
);

const getStatusBadgeClass = (status) => {
    const base = 'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[9px] font-bold tracking-wide uppercase border shrink-0';
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
