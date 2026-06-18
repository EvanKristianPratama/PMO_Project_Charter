<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-white/10">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Organization List</h2>
            <div class="flex items-center gap-3">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari organisasi..."
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white w-48"
                />
                <select
                    v-model="parentFilterId"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                >
                    <option value="">Semua Organisasi</option>
                    <option v-for="org in parentFilterOptions" :key="org.organization_id" :value="org.organization_id">
                        {{ getLevelPrefix(org) }}{{ org.organization_name }} ({{ org.code }})
                    </option>
                </select>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-white dark:hover:bg-slate-100 dark:text-slate-950 dark:focus:ring-white dark:focus:ring-offset-[#171717]"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Organization
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">No</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Group</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">ID</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Code</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Organization</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Alias</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Jabatan</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Pejabat</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">SK</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-36">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="(organizationStructureRow, index) in filteredRows"
                        :key="organizationStructureRow.organization_id"
                        class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                    >
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ index + 1 }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ displayValue(organizationStructureRow.groub_name) }}
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 font-mono text-xs">
                            {{ displayValue(organizationStructureRow.organization_id) }}
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 font-mono text-xs">
                            {{ displayValue(organizationStructureRow.code) }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ displayValue(organizationStructureRow.organization_name) }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ displayValue(organizationStructureRow.alias) }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ displayValue(organizationStructureRow.jabatan) }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ displayValue(organizationStructureRow.pejabat) }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ displayValue(organizationStructureRow.sk) }}
                        </td>
                        <td class="px-4 py-3 text-center space-x-3 w-36">
                            <button
                                @click="openEditModal(organizationStructureRow)"
                                class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition"
                            >
                                Edit
                            </button>
                            <button
                                @click="openDeleteModal(organizationStructureRow)"
                                class="inline-flex items-center text-xs font-semibold text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr v-if="filteredRows.length === 0">
                        <td colspan="10" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                            Data organization tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Create & Edit Modal -->
    <ConfirmationModal
        :show="isModalOpen"
        :title="modalMode === 'create' ? 'Tambah Organisasi' : 'Edit Organisasi'"
        :message="modalMode === 'create' ? 'Silakan isi formulir di bawah ini untuk menambahkan organisasi baru.' : 'Silakan sesuaikan data organisasi di bawah ini.'"
        confirm-text="Simpan"
        cancel-text="Batal"
        type="info"
        :loading="form.processing"
        @close="isModalOpen = false"
        @confirm="submitForm"
    >
        <div class="mt-4 space-y-4">
            <!-- Parent Organization Group Filter -->
            <div class="flex flex-col gap-1.5">
                <label for="parent_group_filter_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Group Organisasi Induk</label>
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

            <!-- Parent Organization Option Select -->
            <div class="flex flex-col gap-1.5">
                <label for="parent_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Organisasi Induk</label>
                <select
                    id="parent_id"
                    v-model="form.parent_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                >
                    <option value="">Tanpa Induk (Root / Level 1)</option>
                    <option v-for="org in filteredParentOrgs" :key="org.organization_id" :value="org.organization_id">
                        {{ getLevelPrefix(org) }}{{ org.organization_name }} ({{ org.code }})
                    </option>
                </select>
                <span v-if="form.errors.parent_id" class="text-xs text-red-500 font-medium">{{ form.errors.parent_id }}</span>
            </div>

            <!-- Group Option Select -->
            <div class="flex flex-col gap-1.5">
                <label for="groub_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Group Organisasi Baru</label>
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
                <label for="code" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Kode</label>
                <input
                    id="code"
                    v-model="form.code"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white font-mono"
                    placeholder="Contoh: 0102"
                    required
                />
                <span v-if="form.errors.code" class="text-xs text-red-500 font-medium">{{ form.errors.code }}</span>
            </div>

            <!-- Name Input -->
            <div class="flex flex-col gap-1.5">
                <label for="name" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Organisasi</label>
                <input
                    id="name"
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
                <label for="alias" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Alias (Singkatan)</label>
                <input
                    id="alias"
                    v-model="form.alias"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: IT-OPS"
                />
                <span v-if="form.errors.alias" class="text-xs text-red-500 font-medium">{{ form.errors.alias }}</span>
            </div>

            <!-- Jabatan Input -->
            <div class="flex flex-col gap-1.5">
                <label for="jabatan" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Jabatan</label>
                <input
                    id="jabatan"
                    v-model="form.jabatan"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: Manager IT"
                />
                <span v-if="form.errors.jabatan" class="text-xs text-red-500 font-medium">{{ form.errors.jabatan }}</span>
            </div>

            <!-- Pejabat Input -->
            <div class="flex flex-col gap-1.5">
                <label for="pejabat" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Pejabat</label>
                <input
                    id="pejabat"
                    v-model="form.pejabat"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: Budi Santoso"
                />
                <span v-if="form.errors.pejabat" class="text-xs text-red-500 font-medium">{{ form.errors.pejabat }}</span>
            </div>

            <!-- SK Input -->
            <div class="flex flex-col gap-1.5">
                <label for="sk" class="text-xs font-semibold text-slate-700 dark:text-slate-300">SK</label>
                <input
                    id="sk"
                    v-model="form.sk"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: SK/123/2026"
                />
                <span v-if="form.errors.sk" class="text-xs text-red-500 font-medium">{{ form.errors.sk }}</span>
            </div>
        </div>
    </ConfirmationModal>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
        :show="isDeleteModalOpen"
        title="Hapus Organisasi"
        :message="`Apakah Anda yakin ingin menghapus organisasi '${selectedOrg?.organization_name}'? Tindakan ini tidak dapat dibatalkan.`"
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
    organizationStructureRows: {
        type: Array,
        default: () => [],
    },
    groubOptions: {
        type: Array,
        default: () => [],
    },
});

const displayValue = (value) => value ?? '-';

const parentFilterId = ref('');
const parentGroupFilterId = ref('');
const searchQuery = ref('');

const getOrganizationDepth = (orgId) => {
    let depth = 0;
    let currentId = orgId;
    const orgMap = new Map(props.organizationStructureRows.map(org => [org.organization_id, org]));
    const visited = new Set();
    
    const org = orgMap.get(orgId);
    if (!org) return 0;

    while (currentId && !visited.has(currentId)) {
        visited.add(currentId);
        const currentOrg = orgMap.get(currentId);
        if (currentOrg && currentOrg.parent_id) {
            depth++;
            currentId = currentOrg.parent_id;
        } else {
            break;
        }
    }

    // Special depth adjustment for Upstream Support Staff (e.g. Corporate Secretary 01100100)
    // to ensure they are indented as Level 3 (depth 2) and NOT parallel to Supportive Directors (depth 1 / Level 2)
    const code = String(org.code || '').trim();
    if (code.startsWith('011')) {
        if (code.startsWith('01100') && code !== '01100000') {
            return 2;
        }
        
        let tempId = org.parent_id;
        const tempVisited = new Set();
        let hasSupportStaffAncestor = false;
        while (tempId && !tempVisited.has(tempId)) {
            tempVisited.add(tempId);
            const parentOrg = orgMap.get(tempId);
            if (parentOrg) {
                const parentCode = String(parentOrg.code || '').trim();
                if (parentCode.startsWith('01100') && parentCode !== '01100000') {
                    hasSupportStaffAncestor = true;
                    break;
                }
                tempId = parentOrg.parent_id;
            } else {
                break;
            }
        }
        if (hasSupportStaffAncestor) {
            return depth + 1;
        }
    }

    return depth;
};

const getLevelPrefix = (org) => {
    const depth = getOrganizationDepth(org.organization_id);
    if (depth === 0) return '';
    return '\u00A0\u00A0'.repeat(depth) + '— ';
};

const parentFilterOptions = computed(() => {
    const orgs = props.organizationStructureRows;
    const orgMap = new Map(orgs.map(org => [org.organization_id, { ...org, children: [] }]));
    const roots = [];
    
    orgs.forEach(org => {
        const mapped = orgMap.get(org.organization_id);
        if (org.parent_id && orgMap.has(org.parent_id)) {
            orgMap.get(org.parent_id).children.push(mapped);
        } else {
            roots.push(mapped);
        }
    });

    const sortNodes = (nodes) => {
        nodes.sort((a, b) => (a.code || '').localeCompare(b.code || ''));
        nodes.forEach(node => {
            if (node.children.length > 0) {
                sortNodes(node.children);
            }
        });
    };
    sortNodes(roots);

    const flattened = [];
    const traverse = (node) => {
        flattened.push(node);
        node.children.forEach(traverse);
    };
    roots.forEach(traverse);
    return flattened;
});

const isDescendant = (orgId, targetParentId) => {
    if (!orgId || !targetParentId) return false;
    const orgMap = new Map(props.organizationStructureRows.map(org => [org.organization_id, org]));
    let currentId = orgId;
    const visited = new Set();
    
    while (currentId && !visited.has(currentId)) {
        visited.add(currentId);
        const currentOrg = orgMap.get(currentId);
        if (currentOrg && currentOrg.parent_id) {
            if (Number(currentOrg.parent_id) === Number(targetParentId)) {
                return true;
            }
            currentId = currentOrg.parent_id;
        } else {
            break;
        }
    }
    return false;
};

const filteredRows = computed(() => {
    let rows = props.organizationStructureRows;

    // 1. Filter by parent organization structure
    if (parentFilterId.value) {
        const filterId = Number(parentFilterId.value);
        rows = rows.filter((row) => {
            return Number(row.organization_id) === filterId || isDescendant(row.organization_id, filterId);
        });
    }

    // 2. Filter by search query text
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase().trim();
        rows = rows.filter((row) => {
            return (
                (row.code || '').toLowerCase().includes(query) ||
                (row.organization_name || '').toLowerCase().includes(query) ||
                (row.alias || '').toLowerCase().includes(query) ||
                (row.jabatan || '').toLowerCase().includes(query) ||
                (row.pejabat || '').toLowerCase().includes(query) ||
                (row.sk || '').toLowerCase().includes(query)
            );
        });
    }

    return rows;
});

watch(() => props.organizationStructureRows, (newRows) => {
    if (parentFilterId.value && !newRows.some(row => Number(row.organization_id) === Number(parentFilterId.value))) {
        parentFilterId.value = '';
    }
});

const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedOrg = ref(null);
const modalMode = ref('create'); // 'create' or 'edit'

const form = useForm({
    groub_id: '',
    parent_id: '',
    code: '',
    name: '',
    alias: '',
    jabatan: '',
    pejabat: '',
    sk: '',
});

const filteredParentOrgs = computed(() => {
    // Normalisasi ke String agar konsisten di semua environment (groub_id bisa number/string/null)
    const targetGroupId = parentGroupFilterId.value ? String(parentGroupFilterId.value) : null;
    const orgs = targetGroupId
        ? props.organizationStructureRows.filter(org => String(org.groub_id ?? '') === targetGroupId)
        : props.organizationStructureRows;
    
    const orgMap = new Map(orgs.map(org => [org.organization_id, { ...org, children: [] }]));
    const roots = [];
    
    orgs.forEach(org => {
        const mapped = orgMap.get(org.organization_id);
        if (org.parent_id && orgMap.has(org.parent_id)) {
            orgMap.get(org.parent_id).children.push(mapped);
        } else {
            roots.push(mapped);
        }
    });

    const sortNodes = (nodes) => {
        nodes.sort((a, b) => (a.code || '').localeCompare(b.code || ''));
        nodes.forEach(node => {
            if (node.children.length > 0) {
                sortNodes(node.children);
            }
        });
    };
    sortNodes(roots);

    const flattened = [];
    const traverse = (node) => {
        flattened.push(node);
        node.children.forEach(traverse);
    };
    roots.forEach(traverse);
    
    return flattened.filter(org => {
        if (modalMode.value === 'edit' && selectedOrg.value) {
            const currentOrgId = selectedOrg.value.organization_id;
            // Exclude the org itself
            if (Number(org.organization_id) === Number(currentOrgId)) return false;
            // Exclude descendants using proper graph traversal (not code-prefix matching)
            if (isDescendant(org.organization_id, currentOrgId)) return false;
        }
        return true;
    });
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.clearErrors();
    form.reset();
    parentGroupFilterId.value = '';
    isModalOpen.value = true;
};

const openEditModal = (org) => {
    modalMode.value = 'edit';
    selectedOrg.value = org;
    form.clearErrors();
    form.groub_id = String(org.groub_id ?? '');
    form.parent_id = String(org.parent_id ?? '');
    form.code = org.code || '';
    form.name = org.organization_name || '';
    form.alias = org.alias || '';
    form.jabatan = org.jabatan || '';
    form.pejabat = org.pejabat || '';
    form.sk = org.sk || '';

    // Gunakan groub_id dari parent org jika ada, fallback ke groub_id org sendiri
    // Normalisasi ke String agar cocok dengan value di <select>
    const parentOrg = props.organizationStructureRows.find(
        o => String(o.organization_id) === String(org.parent_id)
    );
    const resolvedGroupId = parentOrg?.groub_id ?? org.groub_id;
    parentGroupFilterId.value = resolvedGroupId != null ? String(resolvedGroupId) : '';

    isModalOpen.value = true;
};

const openDeleteModal = (org) => {
    selectedOrg.value = org;
    isDeleteModalOpen.value = true;
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('architecture.organization-structure.store'), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('architecture.organization-structure.update', selectedOrg.value.organization_id), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const submitDelete = () => {
    form.delete(route('architecture.organization-structure.destroy', selectedOrg.value.organization_id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
};
</script>
