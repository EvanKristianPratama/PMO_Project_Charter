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
                        {{ getLevelPrefix(fn) }}{{ fn.name }} ({{ fn.kode }})
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
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Kode</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Name</th>
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
                        <td class="px-4 py-3 font-mono text-xs text-slate-500 dark:text-slate-400">
                            <span
                                :style="{ paddingLeft: getDepth(fn.id) * 16 + 'px' }"
                                class="inline-block"
                            >
                                <span v-if="getDepth(fn.id) > 0" class="text-slate-400 mr-1">—</span>
                                {{ fn.kode }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ fn.name }}</td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 text-xs">
                            {{ getParentLabel(fn.parent_id) }}
                        </td>
                        <td class="px-4 py-3 text-center space-x-3 w-36">
                            <button
                                @click="openEditModal(fn)"
                                class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition"
                            >
                                Edit
                            </button>
                            <button
                                @click="openDeleteModal(fn)"
                                class="inline-flex items-center text-xs font-semibold text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr v-if="filteredRows.length === 0">
                        <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
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
                        {{ getLevelPrefix(fn) }}{{ fn.name }} ({{ fn.kode }})
                    </option>
                </select>
                <span v-if="form.errors.parent_id" class="text-xs text-red-500 font-medium">{{ form.errors.parent_id }}</span>
            </div>

            <!-- Kode Input -->
            <div class="flex flex-col gap-1.5">
                <label for="fn_kode" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Kode</label>
                <input
                    id="fn_kode"
                    v-model="form.kode"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white font-mono"
                    placeholder="Contoh: FN-001"
                    required
                />
                <span v-if="form.errors.kode" class="text-xs text-red-500 font-medium">{{ form.errors.kode }}</span>
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
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    functions: {
        type: Array,
        default: () => [],
    },
});

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
    return parent ? `${parent.name} (${parent.kode})` : '-';
};

/** Cek apakah orgId adalah turunan dari targetParentId */
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

// ─── Flatten tree (sort by kode, depth-first) ────────────────────────────────

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
        nodes.sort((a, b) => (a.kode || '').localeCompare(b.kode || ''));
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
            (fn.kode || '').toLowerCase().includes(q) ||
            (fn.name || '').toLowerCase().includes(q)
        );
    }

    return rows;
});

// ─── Modal state ─────────────────────────────────────────────────────────────

const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedFn = ref(null);
const modalMode = ref('create');

const form = useForm({
    parent_id: '',
    kode: '',
    name: '',
});

/** Opsi parent di modal — exclude diri sendiri & turunannya saat edit */
const filteredParentOptions = computed(() =>
    flattenedTree.value.filter(fn => {
        if (modalMode.value === 'edit' && selectedFn.value) {
            const currentId = selectedFn.value.id;
            if (Number(fn.id) === Number(currentId)) return false;
            if (isDescendant(fn.id, currentId)) return false;
        }
        return true;
    })
);

const openCreateModal = () => {
    modalMode.value = 'create';
    form.clearErrors();
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (fn) => {
    modalMode.value = 'edit';
    selectedFn.value = fn;
    form.clearErrors();
    form.parent_id = fn.parent_id ? String(fn.parent_id) : '';
    form.kode = fn.kode || '';
    form.name = fn.name || '';
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
