<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-white/10">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">APQC Process List</h2>
            <div class="flex items-center gap-3">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari APQC..."
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white w-48"
                />
                <select
                    v-model="parentFilterId"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                >
                    <option value="">Semua APQC</option>
                    <option v-for="item in parentFilterOptions" :key="item.id" :value="item.id">
                        {{ getLevelPrefix(item) }}{{ item.name }}
                    </option>
                </select>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-white dark:hover:bg-slate-100 dark:text-slate-950 dark:focus:ring-white dark:focus:ring-offset-[#171717]"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add APQC
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Parent</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-36">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="(item, index) in filteredRows"
                        :key="item.id"
                        class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                    >
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            <span
                                :style="{ paddingLeft: getDepth(item.id) * 16 + 'px' }"
                                class="inline-block"
                            >
                                <span v-if="getDepth(item.id) > 0" class="text-slate-400 mr-1.5">—</span>
                                {{ item.name }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 text-xs">
                            {{ getParentLabel(item.parent_id) }}
                        </td>
                        <td class="px-4 py-3 text-center w-28 print:hidden">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <button
                                    @click="openEditModal(item)"
                                    class="w-14 inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="openDeleteModal(item)"
                                    class="w-14 inline-flex items-center justify-center rounded-full border border-rose-200 bg-white px-2 py-0.5 text-[10px] font-bold text-rose-700 transition hover:bg-rose-50 hover:border-rose-300 dark:border-rose-500/30 dark:bg-[#1a1a1a] dark:text-rose-400 dark:hover:bg-rose-500/10 active:scale-95"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredRows.length === 0">
                        <td colspan="3" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">

                            Data APQC tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Create & Edit Modal -->
    <ConfirmationModal
        :show="isModalOpen"
        :title="modalMode === 'create' ? 'Tambah APQC' : 'Edit APQC'"
        :message="modalMode === 'create' ? 'Silakan isi formulir di bawah ini untuk menambahkan proses APQC baru.' : 'Silakan sesuaikan data proses APQC di bawah ini.'"
        confirm-text="Simpan"
        cancel-text="Batal"
        type="info"
        :loading="form.processing"
        @close="isModalOpen = false"
        @confirm="submitForm"
    >
        <div class="mt-4 space-y-4">
            <!-- Parent APQC Select -->
            <div class="flex flex-col gap-1.5">
                <label for="apqc_parent_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">APQC Induk</label>
                <select
                    id="apqc_parent_id"
                    v-model="form.parent_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                >
                    <option value="">Tanpa Induk (Root / Level 1)</option>
                    <option v-for="item in filteredParentOptions" :key="item.id" :value="item.id">
                        {{ getLevelPrefix(item) }}{{ item.name }}
                    </option>
                </select>
                <span v-if="form.errors.parent_id" class="text-xs text-red-500 font-medium">{{ form.errors.parent_id }}</span>
            </div>

            <!-- Name Input -->
            <div class="flex flex-col gap-1.5">
                <label for="apqc_name" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Proses</label>
                <input
                    id="apqc_name"
                    v-model="form.name"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: Develop Vision and Strategy"
                    required
                />
                <span v-if="form.errors.name" class="text-xs text-red-500 font-medium">{{ form.errors.name }}</span>
            </div>
        </div>
    </ConfirmationModal>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
        :show="isDeleteModalOpen"
        title="Hapus APQC"
        :message="`Apakah Anda yakin ingin menghapus proses APQC '${selectedItem?.name}'? Tindakan ini tidak dapat dibatalkan.`"
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
    apqcList: {
        type: Array,
        default: () => [],
    },
});

const apqcMap = computed(() => new Map(props.apqcList.map(item => [item.id, item])));

/** Hitung kedalaman hierarki dari sebuah node */
const getDepth = (id) => {
    let depth = 0;
    let currentId = id;
    const visited = new Set();
    while (currentId && !visited.has(currentId)) {
        visited.add(currentId);
        const node = apqcMap.value.get(currentId);
        if (node?.parent_id) {
            depth++;
            currentId = node.parent_id;
        } else {
            break;
        }
    }
    return depth;
};

const getLevelPrefix = (item) => {
    const depth = getDepth(item.id);
    if (depth === 0) return '';
    return '\u00A0\u00A0'.repeat(depth) + '— ';
};

const getParentLabel = (parentId) => {
    if (!parentId) return '-';
    const parent = apqcMap.value.get(parentId);
    return parent ? parent.name : '-';
};

/** Cek apakah itemId adalah turunan dari targetParentId */
const isDescendant = (id, targetId) => {
    if (!id || !targetId) return false;
    let currentId = id;
    const visited = new Set();
    while (currentId && !visited.has(currentId)) {
        visited.add(currentId);
        const node = apqcMap.value.get(currentId);
        if (node?.parent_id) {
            if (Number(node.parent_id) === Number(targetId)) return true;
            currentId = node.parent_id;
        } else {
            break;
        }
    }
    return false;
};

// ─── Flatten tree (depth-first traversal to display hierarchy naturally) ───
const flattenedTree = computed(() => {
    const list = props.apqcList;
    const map = new Map(list.map(item => [item.id, { ...item, children: [] }]));
    const roots = [];

    list.forEach(item => {
        const node = map.get(item.id);
        if (item.parent_id && map.has(item.parent_id)) {
            map.get(item.parent_id).children.push(node);
        } else {
            roots.push(node);
        }
    });

    const sort = (nodes) => {
        nodes.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
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
        rows = rows.filter(item =>
            Number(item.id) === filterId || isDescendant(item.id, filterId)
        );
    }

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase().trim();
        rows = rows.filter(item =>
            (item.name || '').toLowerCase().includes(q)
        );
    }

    return rows;
});

watch(() => props.apqcList, (newList) => {
    if (parentFilterId.value && !newList.some(item => Number(item.id) === Number(parentFilterId.value))) {
        parentFilterId.value = '';
    }
});

// ─── Modal state & form ──────────────────────────────────────────────────────
const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedItem = ref(null);
const modalMode = ref('create');

const form = useForm({
    parent_id: '',
    name: '',
});

/** Opsi parent di modal — exclude diri sendiri & turunannya saat edit */
const filteredParentOptions = computed(() => {
    return flattenedTree.value.filter(item => {
        if (modalMode.value === 'edit' && selectedItem.value) {
            const currentId = selectedItem.value.id;
            if (Number(item.id) === Number(currentId)) return false;
            if (isDescendant(item.id, currentId)) return false;
        }
        return true;
    });
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.clearErrors();
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (item) => {
    modalMode.value = 'edit';
    selectedItem.value = item;
    form.clearErrors();
    form.parent_id = item.parent_id ? String(item.parent_id) : '';
    form.name = item.name || '';
    isModalOpen.value = true;
};

const openDeleteModal = (item) => {
    selectedItem.value = item;
    isDeleteModalOpen.value = true;
};

const submitForm = () => {
    const payload = {
        name: form.name,
        parent_id: form.parent_id ? Number(form.parent_id) : null,
    };

    if (modalMode.value === 'create') {
        form.transform(() => payload).post(route('architecture.apqc.store'), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.transform(() => payload).put(route('architecture.apqc.update', selectedItem.value.id), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const submitDelete = () => {
    form.delete(route('architecture.apqc.destroy', selectedItem.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
};
</script>
