<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-white/10">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Function List</h2>
            <div class="flex items-center gap-3">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari function..."
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white w-48"
                />
                <select
                    v-model="parentFilterId"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                >
                    <option value="">Semua Function</option>
                    <option v-for="fn in parentFilterOptions" :key="fn.id" :value="fn.id">
                        {{ getLevelPrefix(fn) }}{{ fn.name }} ({{ fn.code }})
                    </option>
                </select>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-white dark:hover:bg-slate-100 dark:text-slate-950 dark:focus:ring-white dark:focus:ring-offset-[#171717]"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Function
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">No</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Group</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">ID</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Code</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Alias</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Regulation Link</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Parent</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-36">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="(fn, index) in filteredRows"
                        :key="fn.id"
                        class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                    >
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ index + 1 }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ displayValue(fn.groub_name) }}
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 font-mono text-xs">
                            {{ displayValue(fn.id) }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-slate-500 dark:text-slate-400">
                            <span
                                :style="{ paddingLeft: getDepth(fn.id) * 16 + 'px' }"
                                class="inline-block"
                            >
                                <span v-if="getDepth(fn.id) > 0" class="text-slate-400 mr-1">—</span>
                                {{ displayValue(fn.code) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ fn.name }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ displayValue(fn.alias) }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            <div class="flex flex-wrap gap-1">
                                <span v-for="reg in fn.regulations" :key="reg.id" class="inline-flex items-center rounded bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-800 dark:bg-white/10 dark:text-slate-200">
                                    {{ reg.judul }} ({{ reg.nomor }})
                                </span>
                                <span v-if="!fn.regulations || fn.regulations.length === 0">-</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 text-xs">
                            {{ getParentLabel(fn.parent_id) }}
                        </td>
                        <td class="px-4 py-3 text-center w-28 print:hidden">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <button
                                    @click="openEditModal(fn)"
                                    class="w-14 inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="openDeleteModal(fn)"
                                    class="w-14 inline-flex items-center justify-center rounded-full border border-rose-200 bg-white px-2 py-0.5 text-[10px] font-bold text-rose-700 transition hover:bg-rose-50 hover:border-rose-300 dark:border-rose-500/30 dark:bg-[#1a1a1a] dark:text-rose-400 dark:hover:bg-rose-500/10 active:scale-95"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredRows.length === 0">
                        <td colspan="9" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                            Data function tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Create & Edit Modal -->
    <ConfirmationModal
        :show="isModalOpen"
        :title="modalMode === 'create' ? 'Tambah Function' : 'Edit Function'"
        :message="modalMode === 'create' ? 'Silakan isi formulir di bawah ini untuk menambahkan function baru.' : 'Silakan sesuaikan data function di bawah ini.'"
        confirm-text="Simpan"
        cancel-text="Batal"
        type="info"
        :loading="form.processing"
        @close="isModalOpen = false"
        @confirm="submitForm"
    >
        <div class="mt-4 space-y-4">
            <!-- Parent Function Group Filter -->
            <div class="flex flex-col gap-1.5">
                <label for="parent_group_filter_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Group Function Induk</label>
                <select
                    id="parent_group_filter_id"
                    v-model="parentGroupFilterId"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                >
                    <option value="">Semua Group</option>
                    <option v-for="option in groubOptions" :key="option.id" :value="option.id">
                        {{ option.name }}
                    </option>
                </select>
            </div>

            <!-- Parent Function Select -->
            <div class="flex flex-col gap-1.5">
                <label for="fn_parent_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Function Induk</label>
                <select
                    id="fn_parent_id"
                    v-model="form.parent_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                >
                    <option value="">Tanpa Induk (Root / Level 1)</option>
                    <option v-for="fn in filteredParentOptions" :key="fn.id" :value="fn.id">
                        {{ getLevelPrefix(fn) }}{{ fn.name }} ({{ fn.code }})
                    </option>
                </select>
                <span v-if="form.errors.parent_id" class="text-xs text-red-500 font-medium">{{ form.errors.parent_id }}</span>
            </div>

            <!-- Group Option Select -->
            <div class="flex flex-col gap-1.5">
                <label for="groub_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Group Function Baru</label>
                <select
                    id="groub_id"
                    v-model="form.groub_id"
                    @change="parentGroupFilterId = form.groub_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    required
                >
                    <option value="" disabled>Pilih Group...</option>
                    <option v-for="option in groubOptions" :key="option.id" :value="option.id">
                        {{ option.name }}
                    </option>
                </select>
                <span v-if="form.errors.groub_id" class="text-xs text-red-500 font-medium">{{ form.errors.groub_id }}</span>
            </div>

            <!-- Code Input -->
            <div class="flex flex-col gap-1.5">
                <label for="fn_code" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Kode</label>
                <input
                    id="fn_code"
                    v-model="form.code"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white font-mono"
                    placeholder="Contoh: FN-001"
                    required
                />
                <span v-if="form.errors.code" class="text-xs text-red-500 font-medium">{{ form.errors.code }}</span>
            </div>

            <!-- Name Input -->
            <div class="flex flex-col gap-1.5">
                <label for="fn_name" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Function</label>
                <input
                    id="fn_name"
                    v-model="form.name"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: IT Operations"
                    required
                />
                <span v-if="form.errors.name" class="text-xs text-red-500 font-medium">{{ form.errors.name }}</span>
            </div>

            <!-- Alias Input -->
            <div class="flex flex-col gap-1.5">
                <label for="fn_alias" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Alias (Singkatan)</label>
                <input
                    id="fn_alias"
                    v-model="form.alias"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: FN-OPS"
                />
                <span v-if="form.errors.alias" class="text-xs text-red-500 font-medium">{{ form.errors.alias }}</span>
            </div>

            <!-- Regulation Links Input -->
            <!-- Regulation Links Input -->
            <div class="flex flex-col gap-1.5 relative">
                <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Regulation Links</label>
                
                <!-- Trigger Button -->
                <div class="relative">
                    <button 
                        type="button"
                        @click="toggleRegulationDropdown"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-left focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white flex justify-between items-center"
                    >
                        <span class="truncate text-slate-400 dark:text-slate-500">
                            -- Pilih Regulation --
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                </div>

                <!-- Overlay for click outside -->
                <div v-if="isRegulationDropdownOpen" class="fixed inset-0 z-30" @click="isRegulationDropdownOpen = false"></div>

                <!-- Dropdown Content -->
                <div v-if="isRegulationDropdownOpen" class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-xl dark:bg-[#1a1a1a] dark:border-white/10 z-40 max-h-60 overflow-y-auto p-2 space-y-2">
                    <!-- Search input inside dropdown -->
                    <div class="sticky top-0 bg-white dark:bg-[#1a1a1a] pb-1.5">
                        <input 
                            type="text" 
                            v-model="regulationSearchQuery" 
                            placeholder="Cari regulation berdasarkan judul atau nomor..." 
                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-md px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-slate-500/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                            ref="regulationSearchInput"
                            @click.stop
                        />
                    </div>
                    
                    <!-- Options list -->
                    <div class="space-y-0.5">
                        <button
                            v-for="reg in filteredFormRegulations" 
                            :key="reg.id"
                            type="button"
                            @click="toggleRegulation(reg.id)"
                            :class="[
                                'w-full text-left px-2.5 py-1.5 text-xs rounded hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-center justify-between',
                                form.regulation_ids.includes(reg.id) ? 'bg-slate-100 text-slate-900 dark:bg-white/10 dark:text-white font-semibold' : 'text-slate-700 dark:text-slate-300'
                            ]"
                        >
                            <span class="truncate">
                                [{{ reg.tipe }}] {{ reg.judul }} {{ reg.nomor ? `(${reg.nomor})` : '' }}
                            </span>
                            <svg v-if="form.regulation_ids.includes(reg.id)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-slate-900 dark:text-white shrink-0">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div v-if="filteredFormRegulations.length === 0" class="text-center py-4 text-xs text-slate-400">
                            Tidak ada hasil ditemukan.
                        </div>
                    </div>
                </div>

                <!-- Selected list display -->
                <div v-if="form.regulation_ids.length > 0" class="mt-2 flex flex-wrap gap-1.5">
                    <span 
                        v-for="id in form.regulation_ids" 
                        :key="id"
                        class="inline-flex items-center gap-1 rounded-lg bg-slate-100 pl-2.5 pr-1.5 py-1 text-xs font-medium text-slate-700 dark:bg-white/5 dark:text-slate-300 border border-slate-200 dark:border-white/10"
                    >
                        <span class="max-w-[200px] truncate">
                            {{ getRegulationLabel(id) }}
                        </span>
                        <button 
                            type="button" 
                            @click="removeRegulationId(id)"
                            class="rounded-full p-0.5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </span>
                </div>
                <span v-if="form.errors.regulation_ids" class="text-xs text-red-500 font-medium">{{ form.errors.regulation_ids }}</span>
            </div>
        </div>
    </ConfirmationModal>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
        :show="isDeleteModalOpen"
        title="Hapus Function"
        :message="`Apakah Anda yakin ingin menghapus function '${selectedFn?.name}'? Tindakan ini tidak dapat dibatalkan.`"
        confirm-text="Hapus"
        cancel-text="Batal"
        type="danger"
        :loading="form.processing"
        @close="isDeleteModalOpen = false"
        @confirm="submitDelete"
    />
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    functions: {
        type: Array,
        default: () => [],
    },
    groubOptions: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
});

console.log('FunctionTable props.regulations:', props.regulations);

const displayValue = (value) => value ?? '-';

// ─── Helpers ────────────────────────────────────────────────────────────────

const fnMap = computed(() => new Map(props.functions.map(fn => [fn.id, fn])));

/** Hitung kedalaman hierarki dari sebuah node */
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

const getLevelPrefix = (fn) => {
    const depth = getDepth(fn.id);
    if (depth === 0) return '';
    return '\u00A0\u00A0'.repeat(depth) + '— ';
};

const getParentLabel = (parentId) => {
    if (!parentId) return '-';
    const parent = fnMap.value.get(parentId);
    return parent ? `${parent.name} (${parent.code})` : '-';
};

/** Cek apakah fnId adalah turunan dari targetParentId */
const isDescendant = (id, targetId) => {
    if (!id || !targetId) return false;
    let currentId = id;
    const visited = new Set();
    while (currentId && !visited.has(currentId)) {
        visited.add(currentId);
        const node = fnMap.value.get(currentId);
        if (node?.parent_id) {
            if (Number(node.parent_id) === Number(targetId)) return true;
            currentId = node.parent_id;
        } else {
            break;
        }
    }
    return false;
};

// ─── Flatten tree (sort by code, depth-first) ────────────────────────────────

const flattenedTree = computed(() => {
    const fns = props.functions;
    const map = new Map(fns.map(fn => [fn.id, { ...fn, children: [] }]));
    const roots = [];

    fns.forEach(fn => {
        const node = map.get(fn.id);
        if (fn.parent_id && map.has(fn.parent_id)) {
            map.get(fn.parent_id).children.push(node);
        } else {
            roots.push(node);
        }
    });

    const sort = (nodes) => {
        nodes.sort((a, b) => (a.code || '').localeCompare(b.code || ''));
        nodes.forEach(n => sort(n.children));
    };
    sort(roots);

    const result = [];
    const traverse = (node) => {
        result.push(node);
        node.children.forEach(traverse);
    };
    roots.forEach(traverse);
    return result;
});

// ─── Filters ─────────────────────────────────────────────────────────────────

const searchQuery = ref('');
const parentFilterId = ref('');
const parentGroupFilterId = ref('');

const parentFilterOptions = computed(() => flattenedTree.value);

const filteredRows = computed(() => {
    let rows = flattenedTree.value;

    if (parentFilterId.value) {
        const filterId = Number(parentFilterId.value);
        rows = rows.filter(fn =>
            Number(fn.id) === filterId || isDescendant(fn.id, filterId)
        );
    }

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase().trim();
        rows = rows.filter(fn =>
            (fn.code || '').toLowerCase().includes(q) ||
            (fn.name || '').toLowerCase().includes(q) ||
            (fn.alias || '').toLowerCase().includes(q) ||
            (fn.regulations || []).some(reg =>
                (reg.judul || '').toLowerCase().includes(q) ||
                (reg.nomor || '').toLowerCase().includes(q)
            )
        );
    }

    return rows;
});

watch(() => props.functions, (newFunctions) => {
    if (parentFilterId.value && !newFunctions.some(fn => Number(fn.id) === Number(parentFilterId.value))) {
        parentFilterId.value = '';
    }
});

// ─── Modal state ─────────────────────────────────────────────────────────────

const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedFn = ref(null);
const modalMode = ref('create');
const regulationSearchQuery = ref('');

// Dropdown selector state & helpers
const isRegulationDropdownOpen = ref(false);
const regulationSearchInput = ref(null);

const toggleRegulationDropdown = () => {
    isRegulationDropdownOpen.value = !isRegulationDropdownOpen.value;
    if (isRegulationDropdownOpen.value) {
        regulationSearchQuery.value = '';
        setTimeout(() => {
            regulationSearchInput.value?.focus();
        }, 100);
    }
};

const removeRegulationId = (id) => {
    const index = form.regulation_ids.indexOf(id);
    if (index > -1) {
        form.regulation_ids.splice(index, 1);
    }
};

const filteredFormRegulations = computed(() => {
    const query = regulationSearchQuery.value.toLowerCase().trim();
    if (!query) return props.regulations;
    return props.regulations.filter(reg =>
        (reg.judul || '').toLowerCase().includes(query) ||
        (reg.nomor || '').toLowerCase().includes(query) ||
        (reg.tipe || '').toLowerCase().includes(query)
    );
});

const getRegulationLabel = (id) => {
    const reg = props.regulations.find(r => r.id === Number(id));
    return reg ? `[${reg.tipe}] ${reg.judul}` : '';
};

const toggleRegulation = (id) => {
    const index = form.regulation_ids.indexOf(id);
    if (index > -1) {
        form.regulation_ids.splice(index, 1);
    } else {
        form.regulation_ids.push(id);
    }
};

const form = useForm({
    groub_id: '',
    parent_id: '',
    code: '',
    name: '',
    alias: '',
    regulation_ids: [],
});

/** Opsi parent di modal — exclude diri sendiri & turunannya saat edit, pre-filtered by group if selected */
const filteredParentOptions = computed(() => {
    const targetGroupId = parentGroupFilterId.value ? String(parentGroupFilterId.value) : null;
    const fns = targetGroupId
        ? props.functions.filter(fn => String(fn.groub_id ?? '') === targetGroupId)
        : props.functions;

    const map = new Map(fns.map(fn => [fn.id, { ...fn, children: [] }]));
    const roots = [];

    fns.forEach(fn => {
        const node = map.get(fn.id);
        if (fn.parent_id && map.has(fn.parent_id)) {
            map.get(fn.parent_id).children.push(node);
        } else {
            roots.push(node);
        }
    });

    const sort = (nodes) => {
        nodes.sort((a, b) => (a.code || '').localeCompare(b.code || ''));
        nodes.forEach(n => sort(n.children));
    };
    sort(roots);

    const result = [];
    const traverse = (node) => {
        result.push(node);
        node.children.forEach(traverse);
    };
    roots.forEach(traverse);

    return result.filter(fn => {
        if (modalMode.value === 'edit' && selectedFn.value) {
            const currentId = selectedFn.value.id;
            if (Number(fn.id) === Number(currentId)) return false;
            if (isDescendant(fn.id, currentId)) return false;
        }
        return true;
    });
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.clearErrors();
    form.reset();
    parentGroupFilterId.value = '';
    regulationSearchQuery.value = '';
    isRegulationDropdownOpen.value = false;
    isModalOpen.value = true;
};

const openEditModal = (fn) => {
    modalMode.value = 'edit';
    selectedFn.value = fn;
    form.clearErrors();
    form.groub_id = String(fn.groub_id ?? '');
    form.parent_id = fn.parent_id ? String(fn.parent_id) : '';
    form.code = fn.code || '';
    form.name = fn.name || '';
    form.alias = fn.alias || '';
    form.regulation_ids = fn.regulations ? fn.regulations.map(r => r.id) : [];
    regulationSearchQuery.value = '';
    isRegulationDropdownOpen.value = false;

    const parentFn = props.functions.find(
        f => String(f.id) === String(fn.parent_id)
    );
    const resolvedGroupId = parentFn?.groub_id ?? fn.groub_id;
    parentGroupFilterId.value = resolvedGroupId != null ? String(resolvedGroupId) : '';

    isModalOpen.value = true;
};

const openDeleteModal = (fn) => {
    selectedFn.value = fn;
    isDeleteModalOpen.value = true;
};

// ─── Submit ───────────────────────────────────────────────────────────────────

const submitForm = () => {
    const payload = {
        ...form.data(),
        parent_id: form.parent_id || null,
    };

    if (modalMode.value === 'create') {
        form.transform(() => payload).post(route('architecture.function.store'), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.transform(() => payload).put(route('architecture.function.update', selectedFn.value.id), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const submitDelete = () => {
    form.delete(route('architecture.function.destroy', selectedFn.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
};
</script>
