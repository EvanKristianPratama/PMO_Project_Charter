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
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white w-40 truncate"
                >
                    <option value="">All APQC</option>
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
                        <th class="px-4 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Name</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Deskripsi</th>

                        <th class="px-4 py-2 text-center text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-36">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="item in visibleApqcRows"
                        :key="'apqc-' + item.id"
                        class="group transition duration-150 hover:bg-slate-50/50 dark:hover:bg-white/5 animate-fade-in"
                    >
                        <td 
                            class="px-4 py-1.5 text-slate-500 dark:text-slate-400 text-xs break-words" 
                            :style="{ paddingLeft: (item.depth * 24 + 16) + 'px' }"
                        >
                            <div class="flex items-center gap-2">
                                <!-- Toggle Button -->
                                <button 
                                    v-if="item.hasChildren" 
                                    @click.stop="toggleApqcExpand(item.id)" 
                                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none shrink-0"
                                >
                                    <svg v-if="item.isExpanded" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </button>
                                <span v-else-if="item.depth > 0" class="text-slate-300 dark:text-white/20 mr-1.5 font-mono shrink-0">├─</span>
                                
                                <span>
                                    {{ item.name }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-1.5 text-slate-500 dark:text-slate-400 text-xs max-w-xs">
                            <span class="line-clamp-2" :title="item.deskripsi || ''">
                                {{ item.deskripsi || '-' }}
                            </span>
                        </td>

                        <td class="px-4 py-1.5 text-center print:hidden">
                            <div class="flex items-center justify-center gap-1.5">
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
                    <tr v-if="visibleApqcRows.length === 0">
                        <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
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

            <!-- Deskripsi Input -->
            <div class="flex flex-col gap-1.5">
                <label for="apqc_deskripsi" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Deskripsi</label>
                <textarea
                    id="apqc_deskripsi"
                    v-model="form.deskripsi"
                    rows="3"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white resize-none"
                    placeholder="Deskripsi singkat tentang proses APQC ini..."
                />
                <span v-if="form.errors.deskripsi" class="text-xs text-red-500 font-medium">{{ form.errors.deskripsi }}</span>
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
import { ref, computed, watch, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    apqcList: {
        type: Array,
        default: () => [],
    },
});

const apqcMap = computed(() => new Map(props.apqcList.map(item => [item.id, item])));

const getLevelPrefix = (item) => {
    const depth = item.depth !== undefined ? item.depth : 0;
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

// ─── Hierarchy tree mapping ───
const apqcTree = computed(() => {
    const map = {};
    const roots = [];

    props.apqcList.forEach(item => {
        map[item.id] = { ...item, children: [] };
    });

    props.apqcList.forEach(item => {
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

// ─── Expand / Collapse State ───
const expandedApqcIds = ref(new Set());
const expandLevel = ref('all');

const maxDepth = computed(() => {
    let max = 0;
    props.apqcList.forEach(item => {
        const d = item.depth !== undefined ? item.depth : 0;
        if (d > max) max = d;
    });
    return max;
});

const toggleApqcExpand = (id) => {
    expandLevel.value = 'custom';
    if (expandedApqcIds.value.has(id)) {
        expandedApqcIds.value.delete(id);
    } else {
        expandedApqcIds.value.add(id);
    }
    expandedApqcIds.value = new Set(expandedApqcIds.value);
};

const handleExpandLevelChange = () => {
    const val = expandLevel.value;
    if (val === 'custom') return;

    const ids = new Set();

    if (val === 'all') {
        props.apqcList.forEach(item => {
            const isParent = props.apqcList.some(r => r.parent_id === item.id);
            if (isParent) {
                ids.add(item.id);
            }
        });
    } else {
        const targetDepth = parseInt(val, 10);
        if (targetDepth > 0) {
            props.apqcList.forEach(item => {
                const depth = item.depth !== undefined ? item.depth : 0;
                if (depth < targetDepth) {
                    const isParent = props.apqcList.some(r => r.parent_id === item.id);
                    if (isParent) {
                        ids.add(item.id);
                    }
                }
            });
        }
    }
    expandedApqcIds.value = ids;
};

const initializeExpandedApqc = () => {
    const ids = new Set();
    props.apqcList.forEach(item => {
        const isParent = props.apqcList.some(r => r.parent_id === item.id);
        if (isParent) {
            ids.add(item.id);
        }
    });
    expandedApqcIds.value = ids;
    expandLevel.value = 'all';
};

onMounted(() => {
    initializeExpandedApqc();
});

watch(
    () => props.apqcList,
    () => {
        initializeExpandedApqc();
    },
    { deep: false }
);

// ─── Search / Filter ───
const searchQuery = ref('');
const parentFilterId = ref('');

const matchesSearch = (node) => {
    if (!searchQuery.value) return true;
    const q = searchQuery.value.toLowerCase().trim();
    return (node.name || '').toLowerCase().includes(q);
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
        // Expand all items when searching so matches are visible
        const ids = new Set();
        props.apqcList.forEach(item => {
            const isParent = props.apqcList.some(r => r.parent_id === item.id);
            if (isParent) {
                ids.add(item.id);
            }
        });
        expandedApqcIds.value = ids;
        expandLevel.value = 'all';
    } else {
        initializeExpandedApqc();
    }
});

// Root selection filter
const filteredRoots = computed(() => {
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
        
        return findNode(apqcTree.value) || [];
    }
    return apqcTree.value;
});

// Build active visible rows representing the collapsed/expanded list
const visibleApqcRows = computed(() => {
    const rows = [];
    
    const traverse = (node, depth = 0) => {
        const visibleChildren = (node.children || []).filter(child => shouldShowNode(child));
        const hasChildren = visibleChildren.length > 0;
        const isExpanded = expandedApqcIds.value.has(node.id);
        
        rows.push({
            ...node,
            depth,
            hasChildren,
            isExpanded
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

// Flatten tree for dropdown selection options
const parentFilterOptions = computed(() => {
    const result = [];
    const traverse = (node, depth = 0) => {
        result.push({ ...node, depth });
        if (node.children && node.children.length > 0) {
            node.children.forEach(child => traverse(child, depth + 1));
        }
    };
    apqcTree.value.forEach(root => traverse(root, 0));
    return result;
});

// ─── Modal state & form ───
const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedItem = ref(null);
const modalMode = ref('create');

const form = useForm({
    parent_id: '',
    name: '',
    deskripsi: '',
});

const filteredParentOptions = computed(() => {
    return parentFilterOptions.value.filter(item => {
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
    form.deskripsi = item.deskripsi || '';
    isModalOpen.value = true;
};

const openDeleteModal = (item) => {
    selectedItem.value = item;
    isDeleteModalOpen.value = true;
};

const submitForm = () => {
    const payload = {
        name: form.name,
        deskripsi: form.deskripsi || null,
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
