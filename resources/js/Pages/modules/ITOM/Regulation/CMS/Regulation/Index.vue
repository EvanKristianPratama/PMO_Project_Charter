<template>
    <ModulLayout title="CMS - Regulation Management">
        <div class="animate-fade-in-up space-y-6 -mx-4 sm:-mx-6 lg:-mx-8 -mt-5">
            <!-- Navigation Tabs -->
            <div
                class="flex flex-col justify-between dark:border-white/10 sm:flex-row sm:items-center px-4 sm:px-6 lg:px-8"
            >
                <div
                    class="flex items-center gap-1.5 rounded-lg bg-slate-100 p-1 dark:bg-white/5 self-start sm:self-auto"
                >
                    <Link
                        :href="route('itom.policy.CMS.index')"
                        class="rounded-md px-4 py-1.5 text-xs font-semibold transition-all duration-200"
                        :class="
                            route().current('itom.policy.CMS.index')
                                ? 'bg-white text-[#821f44] shadow-sm dark:bg-[#1A1A1A] dark:text-[#db588c]'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'
                        "
                    >
                        Document
                    </Link>
                    <Link
                        :href="route('itom.policy.CMS.regulation.index')"
                        class="rounded-md px-4 py-1.5 text-xs font-semibold transition-all duration-200"
                        :class="
                            route().current('itom.policy.CMS.regulation.index')
                                ? 'bg-white text-[#821f44] shadow-sm dark:bg-[#1A1A1A] dark:text-[#db588c]'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'
                        "
                    >
                        Regulation
                    </Link>
                </div>
            </div>

            <div class="px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Table with Deferred Loading -->
                <Deferred data="prosesBisnisV2">
                    <template #fallback>
                        <TableSkeleton />
                    </template>

                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                        <!-- Table Header / Search & Filter -->
                        <div class="flex flex-row items-center justify-between gap-3 border-b border-slate-200 px-5 py-3 dark:border-white/10 flex-wrap">
                            <div class="flex flex-wrap items-center gap-2">
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Cari Proses Bisnis..."
                                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white w-48 transition"
                                />
                                <select
                                    v-model="companyFilterId"
                                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white w-40 truncate"
                                >
                                    <option value="">Semua Perusahaan</option>
                                    <option v-for="option in availableCompanies" :key="option.id" :value="option.id">
                                        {{ option.name }}
                                    </option>
                                </select>
                                <select
                                    v-model="parentFilterId"
                                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white w-40 truncate"
                                >
                                    <option value="">Semua Parent</option>
                                    <option v-for="item in parentFilterOptions" :key="item.id" :value="item.id">
                                        {{ getLevelPrefix(item) }}{{ item.name }}
                                    </option>
                                </select>
                                <select
                                    v-model="expandLevel"
                                    @change="handleExpandLevelChange"
                                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white cursor-pointer"
                                >
                                    <option value="custom" disabled>Expand Level...</option>
                                    <option value="0">Collapse All</option>
                                    <option v-for="depth in maxDepth + 1" :key="depth" :value="depth">
                                        Level {{ depth }}
                                    </option>
                                    <option value="all">Expand All</option>
                                </select>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full table-fixed divide-y divide-slate-200 text-sm dark:divide-white/10">
                                <thead class="bg-slate-50 dark:bg-white/5">
                                    <tr class="border-b border-slate-200 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:border-white/5 dark:bg-[#1f1f1f]/50 dark:text-slate-400">
                                        <th class="px-1 py-3 text-left w-24 border-r border-slate-200 dark:border-white/10">Company</th>
                                        <th class="pl-1 pr-1 py-3 text-left w-[15%] border-r border-slate-200 dark:border-white/10">Business Process Name</th>
                                        <th class="pl-1 pr-1 py-3 text-left border-r border-slate-200 dark:border-white/10">Daftar STK</th>
                                        <th class="pl-1 pr-1 py-3 text-left">URL</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-xs font-medium text-slate-700 dark:divide-white/10 dark:text-slate-300">
                                    <tr
                                        v-for="item in visibleRows"
                                        :key="'pb2-' + item.id"
                                        class="group transition duration-150 hover:bg-slate-50/50 dark:hover:bg-white/[0.02] animate-fade-in"
                                    >
                                        <td class="px-1 py-3 text-slate-600 dark:text-slate-300 break-words whitespace-normal border-r border-slate-200 dark:border-white/10">
                                            {{ item.depth === 0 ? (item.company?.name || '-') : '' }}
                                        </td>
                                        <td 
                                            class="pl-1 pr-1 py-3 text-slate-900 dark:text-white break-words font-semibold border-r border-slate-200 dark:border-white/10" 
                                            :style="{ paddingLeft: (item.depth * 8 + 4) + 'px' }"
                                        >
                                            <div class="flex items-center gap-2">
                                                <!-- Toggle Button / Branch Spacer -->
                                                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                                                    <button 
                                                        v-if="item.hasChildren" 
                                                        @click.stop="toggleExpand(item.id)" 
                                                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none shrink-0 flex items-center justify-center"
                                                    >
                                                        <svg v-if="item.isExpanded" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                        </svg>
                                                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                                        </svg>
                                                    </button>
                                                    <span v-else-if="item.depth > 0" class="text-slate-300 dark:text-white/20 font-mono text-xs select-none">├─</span>
                                                </div>
                                                
                                                <span>
                                                    {{ item.name }}
                                                </span>
                                            </div>
                                        </td>
                                        <td colspan="2" class="p-0 text-slate-600 dark:text-slate-300">
                                            <div v-if="item.regulations && item.regulations.length > 0" class="divide-y divide-slate-100 dark:divide-white/5">
                                                <div 
                                                    v-for="reg in item.regulations" 
                                                    :key="'grid-reg-' + reg.id"
                                                    class="grid grid-cols-2 text-[11px]"
                                                >
                                                    <!-- Left: STK -->
                                                    <div class="pl-1 pr-1 py-1.5 border-r border-slate-200 dark:border-white/10 flex items-start gap-1">
                                                        <span class="shrink-0 select-none text-slate-400">-</span>
                                                        <span class="flex flex-col items-start text-left w-full">
                                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                                <Link 
                                                                    :href="route('itom.policy.regulation.procedure.index', { regulation_id: reg.id })" 
                                                                    class="font-semibold text-slate-900 dark:text-white hover:underline hover:text-[#821f44] dark:hover:text-[#db588c]"
                                                                >
                                                                    {{ reg.judul }}
                                                                </Link>
                                                                <span v-if="reg.status" :class="getStatusBadgeClass(reg.status)">
                                                                    {{ reg.status }}
                                                                </span>
                                                                <button 
                                                                    v-if="reg.sop_categories && reg.sop_categories.length > 0"
                                                                    type="button"
                                                                    @click.stop="toggleRegulationProcedures(reg.id)"
                                                                    class="inline-flex items-center text-[10px] font-semibold text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition focus:outline-none shrink-0"
                                                                >
                                                                    <span class="mr-1">({{ reg.sop_categories.length }} Prosedur)</span>
                                                                    <svg 
                                                                        :class="[
                                                                            'w-3 h-3 transform transition-transform duration-150',
                                                                            expandedRegulationIds.has(reg.id) ? 'rotate-180' : ''
                                                                        ]" 
                                                                        fill="none" 
                                                                        viewBox="0 0 24 24" 
                                                                        stroke="currentColor" 
                                                                        stroke-width="2.5"
                                                                    >
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5">{{ reg.nomor }}</span>
                                                            
                                                            <!-- Category List (Collapsible) -->
                                                            <transition
                                                                enter-active-class="transition duration-100 ease-out"
                                                                enter-from-class="transform scale-95 opacity-0"
                                                                enter-to-class="transform scale-100 opacity-100"
                                                                leave-active-class="transition duration-75 ease-in"
                                                                leave-from-class="transform scale-100 opacity-100"
                                                                leave-to-class="transform scale-95 opacity-0"
                                                            >
                                                                <ul v-if="reg.sop_categories && reg.sop_categories.length > 0 && expandedRegulationIds.has(reg.id)" class="mt-1 pl-2 space-y-0.5 w-full">
                                                                    <li 
                                                                        v-for="cat in reg.sop_categories" 
                                                                        :key="'cat-' + cat.id"
                                                                        class="text-[10px] text-black dark:text-slate-300 list-none flex items-start gap-1"
                                                                    >
                                                                        <span class="shrink-0 select-none">-</span>
                                                                        <Link 
                                                                            :href="route('itom.policy.regulation.procedure.index', { regulation_id: reg.id }) + '#sop-cat-' + cat.id"
                                                                            class="hover:underline hover:text-[#821f44] dark:hover:text-[#db588c]"
                                                                        >
                                                                            {{ cat.tipe }}
                                                                        </Link>
                                                                    </li>
                                                                </ul>
                                                            </transition>
                                                        </span>
                                                    </div>

                                                    <!-- Right: URL -->
                                                    <div class="pl-1 pr-1 py-1.5 flex flex-col items-start w-full">
                                                        <div v-if="reg.source && getLines(reg.source).length > 0" class="space-y-1 w-full">
                                                            <div 
                                                                v-for="(line, idx) in getLines(reg.source)" 
                                                                :key="idx"
                                                                class="flex items-start gap-1 leading-relaxed text-[11px]"
                                                            >
                                                                <span class="shrink-0 select-none text-slate-400">-</span>
                                                                <span class="flex flex-col items-start text-left w-full break-all">
                                                                    <template v-if="isUrl(line)">
                                                                        <a 
                                                                            :href="formatUrl(line)" 
                                                                            target="_blank"
                                                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline underline-offset-2"
                                                                        >
                                                                            {{ line }}
                                                                        </a>
                                                                    </template>
                                                                    <template v-else>
                                                                        {{ line }}
                                                                    </template>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <span v-else class="text-slate-400 dark:text-slate-600 italic pl-1">—</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-else class="pl-1 py-3 text-slate-400 dark:text-slate-600 font-mono text-xs select-none">
                                                —
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="visibleRows.length === 0">
                                        <td colspan="4" class="px-1 py-12 text-center text-slate-500 dark:text-slate-400">
                                            Data Proses Bisnis tidak ditemukan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </Deferred>
            </div>
        </div>


    </ModulLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Link, Deferred } from '@inertiajs/vue3';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';

const isUrl = (str) => {
    if (!str) return false;
    const trimmed = str.trim();
    const urlRegex = /^(https?:\/\/)?([\w.-]+\.[a-zA-Z]{2,6})(\/[\w.-]*)*\/?(\?.*)?$/;
    return urlRegex.test(trimmed);
};

const formatUrl = (str) => {
    if (!str) return '';
    const trimmed = str.trim();
    if (/^https?:\/\//i.test(trimmed)) {
        return trimmed;
    }
    return `https://${trimmed}`;
};

const getLines = (str) => {
    if (!str) return [];
    return str.split(/\r?\n/).map(line => line.trim()).filter(line => line.length > 0);
};

const props = defineProps({
    prosesBisnisV2: {
        type: Array,
        default: () => [],
    },
    companyOptions: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
});

const getStatusBadgeClass = (status) => {
    const base = 'inline-flex items-center gap-1 rounded-full px-1.5 py-0.5 text-[8px] font-bold tracking-wide uppercase border shrink-0';
    if (!status) return `${base} bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400`;
    
    const formatted = status.toLowerCase().trim();
    if (formatted === 'aktif' || formatted === 'active' || formatted === 'berlaku') {
        return `${base} bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400`;
    }
    if (formatted === 'draft' || formatted === 'draft usulan' || formatted === 'draft dicabut') {
        return `${base} bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400`;
    }
    if (formatted === 'dicabut' || formatted === 'revisi' || formatted === 'expired' || formatted === 'tidak berlaku') {
        return `${base} bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400`;
    }
    return `${base} bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400`;
};

const itemsMap = computed(() => new Map(props.prosesBisnisV2.map(item => [item.id, item])));

const availableCompanies = computed(() => {
    const compMap = new Map();
    props.prosesBisnisV2.forEach(item => {
        if (item.company) {
            compMap.set(item.company.id, item.company);
        }
    });
    return Array.from(compMap.values()).sort((a, b) => (a.name || '').localeCompare(b.name || ''));
});

const getLevelPrefix = (item) => {
    const depth = item.depth !== undefined ? item.depth : 0;
    if (depth === 0) return '';
    return '\u00A0\u00A0'.repeat(depth) + '— ';
};

const isDescendant = (id, targetId) => {
    if (!id || !targetId) return false;
    let currentId = id;
    const visited = new Set();
    while (currentId && !visited.has(currentId)) {
        visited.add(currentId);
        const node = itemsMap.value.get(currentId);
        if (node?.parent_id) {
            if (Number(node.parent_id) === Number(targetId)) return true;
            currentId = node.parent_id;
        } else {
            break;
        }
    }
    return false;
};

// ─── Hierarchy tree mapping ───
const treeData = computed(() => {
    const map = {};
    const roots = [];

    props.prosesBisnisV2.forEach(item => {
        map[item.id] = { ...item, children: [] };
    });

    props.prosesBisnisV2.forEach(item => {
        const mapped = map[item.id];
        if (item.parent_id && map[item.parent_id]) {
            map[item.parent_id].children.push(mapped);
        } else {
            roots.push(mapped);
        }
    });

    const sort = (nodes) => {
        nodes.sort((a, b) => {
            const orderA = a.order !== null && a.order !== undefined ? Number(a.order) : null;
            const orderB = b.order !== null && b.order !== undefined ? Number(b.order) : null;
            
            if (orderA !== null && orderB !== null) {
                if (orderA !== orderB) {
                    return orderA - orderB;
                }
            } else if (orderA !== null) {
                return -1;
            } else if (orderB !== null) {
                return 1;
            }
            return (a.name || '').localeCompare(b.name || '');
        });
        nodes.forEach(n => sort(n.children));
    };
    sort(roots);

    return roots;
});

// ─── Expand / Collapse State ───
const expandedIds = ref(new Set());
const expandLevel = ref('all');

const maxDepth = computed(() => {
    return 1;
});

const toggleExpand = (id) => {
    expandLevel.value = 'custom';
    if (expandedIds.value.has(id)) {
        expandedIds.value.delete(id);
    } else {
        expandedIds.value.add(id);
    }
    expandedIds.value = new Set(expandedIds.value);
};

const handleExpandLevelChange = () => {
    const val = expandLevel.value;
    if (val === 'custom') return;

    const ids = new Set();

    if (val === 'all' || parseInt(val, 10) >= 1) {
        props.prosesBisnisV2.forEach(item => {
            const isParent = props.prosesBisnisV2.some(r => r.parent_id === item.id);
            if (isParent && item.depth === 0) {
                ids.add(item.id);
            }
        });
    }
    expandedIds.value = ids;
};

const initializeExpanded = () => {
    const ids = new Set();
    props.prosesBisnisV2.forEach(item => {
        const isParent = props.prosesBisnisV2.some(r => r.parent_id === item.id);
        if (isParent && item.depth === 0) {
            ids.add(item.id);
        }
    });
    expandedIds.value = ids;
    expandLevel.value = 'all';
};

onMounted(() => {
    initializeExpanded();
});

watch(
    () => props.prosesBisnisV2,
    (newVal) => {
        initializeExpanded();
        if (parentFilterId.value && !newVal.some(item => Number(item.id) === Number(parentFilterId.value))) {
            parentFilterId.value = '';
        }
    },
    { deep: false }
);

const expandedRegulationIds = ref(new Set());

const toggleRegulationProcedures = (regId) => {
    if (expandedRegulationIds.value.has(regId)) {
        expandedRegulationIds.value.delete(regId);
    } else {
        expandedRegulationIds.value.add(regId);
    }
    expandedRegulationIds.value = new Set(expandedRegulationIds.value);
};

// ─── Search / Filter ───
const searchQuery = ref('');
const companyFilterId = ref('');
const parentFilterId = ref('');

const matchesSearch = (node) => {
    if (!searchQuery.value) return true;
    const q = searchQuery.value.toLowerCase().trim();
    const matchesName = (node.name || '').toLowerCase().includes(q);
    const matchesDesc = (node.deskripsi || '').toLowerCase().includes(q);
    return matchesName || matchesDesc;
};

const shouldShowNode = (node) => {
    if (matchesSearch(node)) return true;
    if (node.children && node.children.length > 0) {
        return node.children.some(child => shouldShowNode(child));
    }
    return false;
};

watch(searchQuery, (newQuery) => {
    if (newQuery) {
        const ids = new Set();
        props.prosesBisnisV2.forEach(item => {
            const isParent = props.prosesBisnisV2.some(r => r.parent_id === item.id);
            if (isParent) {
                ids.add(item.id);
            }
        });
        expandedIds.value = ids;
        expandLevel.value = 'all';
    } else {
        initializeExpanded();
    }
});

// Root selection filter
const filteredRoots = computed(() => {
    let roots = treeData.value;

    if (companyFilterId.value) {
        const compId = Number(companyFilterId.value);
        roots = roots.filter(root => Number(root.company_id) === compId);
    }

    if (parentFilterId.value) {
        const filterId = Number(parentFilterId.value);
        
        const findNode = (nodes) => {
            for (const n of nodes) {
                if (n.id === filterId) return [n];
                if (n.children && n.children.length > 0) {
                    const found = findNode(n.children);
                    if (found) return found;
                }
            }
            return null;
        };
        
        return findNode(roots) || [];
    }
    return roots;
});

const getRecursiveRegulations = (node) => {
    const regs = [];
    const seenIds = new Set();

    const collect = (n) => {
        if (n.regulations) {
            n.regulations.forEach(reg => {
                if (!seenIds.has(reg.id)) {
                    seenIds.add(reg.id);
                    regs.push(reg);
                }
            });
        }
        if (n.children) {
            n.children.forEach(collect);
        }
    };

    collect(node);
    return regs;
};

// Build active visible rows
const visibleRows = computed(() => {
    const rows = [];
    
    const traverse = (node, depth = 0) => {
        if (depth > 1) {
            return;
        }
        const visibleChildren = (node.children || []).filter(child => shouldShowNode(child));
        const hasChildren = depth < 1 && visibleChildren.length > 0;
        const isExpanded = expandedIds.value.has(node.id);
        
        rows.push({
            ...node,
            depth,
            hasChildren,
            isExpanded,
            regulations: depth === 1 ? getRecursiveRegulations(node) : []
        });
        
        if (hasChildren && isExpanded) {
            visibleChildren.forEach(child => {
                traverse(child, depth + 1);
            });
        }
    };
    
    filteredRoots.value.forEach(root => {
        if (shouldShowNode(root)) {
            traverse(root, 0);
        }
    });
    
    return rows;
});

// Flatten tree for dropdown select
const parentFilterOptions = computed(() => {
    const result = [];
    const traverse = (node, depth = 0) => {
        if (depth > 0) return;
        result.push({ ...node, depth });
        if (node.children && node.children.length > 0) {
            node.children.forEach(child => traverse(child, depth + 1));
        }
    };
    
    let roots = treeData.value;
    if (companyFilterId.value) {
        const compId = Number(companyFilterId.value);
        roots = roots.filter(root => Number(root.company_id) === compId);
    }

    roots.forEach(root => traverse(root, 0));
    return result;
});

watch(companyFilterId, (newCompanyFilterId) => {
    if (parentFilterId.value) {
        const selectedParent = props.prosesBisnisV2.find(item => Number(item.id) === Number(parentFilterId.value));
        if (selectedParent && newCompanyFilterId && Number(selectedParent.company_id) !== Number(newCompanyFilterId)) {
            parentFilterId.value = '';
        }
    }
});

</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.4s ease-out;
}

.animate-fade-in {
    animation: fadeIn 0.25s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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
