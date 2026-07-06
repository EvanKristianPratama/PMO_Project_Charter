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
                <!-- Status Filter (Multi-select Dropdown) -->
                <div class="relative inline-block text-left">
                    <button 
                        type="button"
                        @click="isStatusDropdownOpen = !isStatusDropdownOpen"
                        class="inline-flex items-center justify-between gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white cursor-pointer min-w-[130px] text-left select-none active:scale-[0.98] transition-transform duration-100"
                    >
                        <span class="truncate">
                            {{ selectedStatuses.length === 0 ? 'Semua Status' : selectedStatuses.join(', ') }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div v-if="isStatusDropdownOpen" class="fixed inset-0 z-30" @click="isStatusDropdownOpen = false"></div>

                    <div v-if="isStatusDropdownOpen" class="absolute left-0 mt-1 w-44 rounded-lg bg-white border border-slate-200 shadow-lg dark:bg-[#1a1a1a] dark:border-white/10 z-40 p-2 space-y-1">
                        <label 
                            v-for="status in availableStatuses" 
                            :key="status"
                            class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-slate-100 dark:hover:bg-white/5 transition duration-150 cursor-pointer select-none text-xs text-slate-700 dark:text-slate-300 font-medium"
                        >
                            <input 
                                type="checkbox" 
                                :value="status" 
                                v-model="selectedStatuses"
                                class="rounded border-slate-300 text-[#821f44] focus:ring-[#821f44] dark:border-white/10 dark:bg-black/20"
                            />
                            <span>{{ status }}</span>
                        </label>
                        <div v-if="selectedStatuses.length > 0" class="border-t border-slate-100 dark:border-white/5 pt-1.5 mt-1 flex justify-end">
                            <button 
                                type="button" 
                                @click="selectedStatuses = []" 
                                class="text-[10px] text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white font-bold"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Function Filter (Multi-select Dropdown) -->
                <div class="relative inline-block text-left">
                    <button 
                        type="button"
                        @click="isFunctionDropdownOpen = !isFunctionDropdownOpen"
                        class="inline-flex items-center justify-between gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white cursor-pointer min-w-[155px] max-w-[240px] text-left select-none active:scale-[0.98] transition-transform duration-100"
                    >
                        <span class="truncate">
                            {{ selectedFunctionIds.length === 0 ? 'Semua Fungsi' : getSelectedFunctionsLabel() }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div v-if="isFunctionDropdownOpen" class="fixed inset-0 z-30" @click="isFunctionDropdownOpen = false"></div>

                    <div v-if="isFunctionDropdownOpen" class="absolute left-0 mt-1 w-72 rounded-lg bg-white border border-slate-200 shadow-lg dark:bg-[#1a1a1a] dark:border-white/10 z-40 p-2 space-y-1 max-h-60 overflow-y-auto">
                        <label 
                            v-for="fn in allFunctionOptions" 
                            :key="fn.id"
                            class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-slate-100 dark:hover:bg-white/5 transition duration-150 cursor-pointer select-none text-xs text-slate-700 dark:text-slate-300 font-medium"
                        >
                            <input 
                                type="checkbox" 
                                :value="fn.id" 
                                v-model="selectedFunctionIds"
                                class="rounded border-slate-300 text-[#821f44] focus:ring-[#821f44] dark:border-white/10 dark:bg-black/20"
                            />
                            <span class="truncate">
                                <span v-if="fn.company?.singkatan" class="text-[9px] text-[#821f44] dark:text-[#db588c] font-bold uppercase mr-1">
                                    [{{ fn.company.singkatan }}]
                                </span>
                                <span>{{ getFunctionLevelPrefix(fn.depth) }}{{ fn.name }}</span>
                            </span>
                        </label>
                        <div v-if="selectedFunctionIds.length > 0" class="border-t border-slate-100 dark:border-white/5 pt-1.5 mt-1 flex justify-end">
                            <button 
                                type="button" 
                                @click="selectedFunctionIds = []" 
                                class="text-[10px] text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white font-bold"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

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

                <!-- Function Filter Select -->
                <select
                    v-model="functionFilterId"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white cursor-pointer min-w-[140px] max-w-[200px] truncate"
                >
                    <option value="">Semua Fungsi</option>
                    <option v-for="fn in functionOptions" :key="fn.id" :value="fn.id">
                        {{ getFunctionLevelPrefix(fn.depth) }}{{ fn.name }}
                    </option>
                </select>

                <!-- Status Filter (Multi-select Dropdown) -->
                <div class="relative inline-block text-left">
                    <button 
                        type="button"
                        @click="isStatusDropdownOpenFunction = !isStatusDropdownOpenFunction"
                        class="inline-flex items-center justify-between gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white cursor-pointer min-w-[130px] text-left select-none active:scale-[0.98] transition-transform duration-100"
                    >
                        <span class="truncate">
                            {{ selectedStatusesFunction.length === 0 ? 'Semua Status' : selectedStatusesFunction.join(', ') }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div v-if="isStatusDropdownOpenFunction" class="fixed inset-0 z-30" @click="isStatusDropdownOpenFunction = false"></div>

                    <div v-if="isStatusDropdownOpenFunction" class="absolute left-0 mt-1 w-44 rounded-lg bg-white border border-slate-200 shadow-lg dark:bg-[#1a1a1a] dark:border-white/10 z-40 p-2 space-y-1">
                        <label 
                            v-for="status in availableStatuses" 
                            :key="status"
                            class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-slate-100 dark:hover:bg-white/5 transition duration-150 cursor-pointer select-none text-xs text-slate-700 dark:text-slate-300 font-medium"
                        >
                            <input 
                                type="checkbox" 
                                :value="status" 
                                v-model="selectedStatusesFunction"
                                class="rounded border-slate-300 text-[#821f44] focus:ring-[#821f44] dark:border-white/10 dark:bg-black/20"
                            />
                            <span>{{ status }}</span>
                        </label>
                        <div v-if="selectedStatusesFunction.length > 0" class="border-t border-slate-100 dark:border-white/5 pt-1.5 mt-1 flex justify-end">
                            <button 
                                type="button" 
                                @click="selectedStatusesFunction = []" 
                                class="text-[10px] text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white font-bold"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

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
                        <th scope="col" class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10 w-[600px] max-w-[600px]">Regulation</th>
                        <th scope="col" class="px-3 py-3 border-b border-slate-200 dark:border-white/10">Fungsi / Unit Organisasi / Jabatan Terkait</th>
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
                        <td class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10 w-[300px] max-w-[300px] break-words" :style="{ paddingLeft: (row.depth * 24 + 12) + 'px' }">
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
                                        <Link 
                                            :href="route('itom.policy.regulation.procedure.index', { regulation_id: row.id })" 
                                            class="font-semibold text-slate-900 dark:text-white hover:underline hover:text-[#821f44] dark:hover:text-[#db588c] text-xs"
                                            :title="row.judul"
                                        >
                                            {{ row.judul }}
                                        </Link>

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
                        <td class="px-3 py-3 border-b border-slate-200 dark:border-white/10">
                            <ul v-if="getMappedFunctions(row.id).length > 0" class="space-y-0.5 list-none">
                                <li
                                    v-for="fn in getMappedFunctions(row.id)"
                                    :key="fn.id"
                                    class="flex items-start gap-1.5 whitespace-pre-wrap leading-relaxed text-[11px] text-slate-700 dark:text-slate-300"
                                    :title="`${fn.name} (${fn.alias || 'Tanpa Alias'})`"
                                >
                                    <span class="shrink-0 select-none text-slate-400 dark:text-slate-600">-</span>
                                    <span>{{ fn.name }}</span>
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

        <!-- By Function View Component -->
        <FunctionMap
            v-else-if="activeView === 'function'"
            :functions="functions"
            :regulations="regulations"
            :company-filter-id="companyFilterId"
            :function-filter-id="functionFilterId"
            :selected-statuses="selectedStatusesFunction"
            :expand-level="expandLevelFunction"
            @update:expand-level="expandLevelFunction = $event"
        />
    </section>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
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

// Status dropdown open states
const isStatusDropdownOpen = ref(false);
const isStatusDropdownOpenFunction = ref(false);

// Filters for By Regulation
const selectedStatuses = ref([]);
const isFunctionDropdownOpen = ref(false);
const selectedFunctionIds = ref([]);

// Filters for By Function
const companyFilterId = ref('');
const functionFilterId = ref('');
const selectedStatusesFunction = ref([]);
const expandLevelFunction = ref('all');

// Computed option elements for the Function Filter
const functionOptions = computed(() => {
    const map = {};
    const roots = [];
    
    // Pre-filter functions by company if companyFilterId is set
    let fns = props.functions;
    if (companyFilterId.value) {
        const compId = Number(companyFilterId.value);
        fns = fns.filter(fn => Number(fn.company_id) === compId);
    }
    
    fns.forEach(item => {
        map[item.id] = { ...item, children: [] };
    });
    
    fns.forEach(item => {
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
    
    const result = [];
    const traverse = (node, depth = 0) => {
        result.push({ ...node, depth });
        if (node.children && node.children.length > 0) {
            node.children.forEach(child => traverse(child, depth + 1));
        }
    };
    
    roots.forEach(root => traverse(root, 0));
    return result;
});

const getSelectedFunctionsLabel = () => {
    const selected = props.functions.filter(fn => selectedFunctionIds.value.includes(fn.id));
    return selected.map(fn => fn.name).join(', ');
};

const allFunctionOptions = computed(() => {
    const map = {};
    const roots = [];
    
    props.functions.forEach(item => {
        map[item.id] = { ...item, children: [] };
    });
    
    props.functions.forEach(item => {
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
    
    const result = [];
    const traverse = (node, depth = 0) => {
        result.push({ ...node, depth });
        if (node.children && node.children.length > 0) {
            node.children.forEach(child => traverse(child, depth + 1));
        }
    };
    
    roots.forEach(root => traverse(root, 0));
    return result;
});

const getFunctionLevelPrefix = (depth) => {
    if (depth === 0) return '';
    return '\u00A0\u00A0'.repeat(depth) + '— ';
};

// Reset selected function filter if company filter changes and is no longer matching
watch(companyFilterId, (newCompanyFilterId) => {
    if (functionFilterId.value) {
        const selectedFn = props.functions.find(f => Number(f.id) === Number(functionFilterId.value));
        if (selectedFn && newCompanyFilterId && Number(selectedFn.company_id) !== Number(newCompanyFilterId)) {
            functionFilterId.value = '';
        }
    }
});

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
    if (selectedStatuses.value && selectedStatuses.value.length > 0) {
        const matchedByStatus = props.regulations.filter(reg => reg.status && selectedStatuses.value.includes(reg.status));
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

    // 2. Function Filter
    if (selectedFunctionIds.value && selectedFunctionIds.value.length > 0) {
        const matchedByFunction = props.regulations.filter(reg => {
            const mappedFns = getMappedFunctions(reg.id);
            return mappedFns.some(fn => selectedFunctionIds.value.includes(fn.id));
        });
        
        const includedIds = new Set();

        matchedByFunction.forEach(reg => {
            includedIds.add(reg.id);
            
            const ancestors = collectAncestorIds(reg);
            ancestors.forEach(id => includedIds.add(id));

            const descendants = collectDescendantIds(reg.id);
            descendants.forEach(id => includedIds.add(id));
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
    expandedDocIds.value = new Set();
    expandLevel.value = '0';
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
