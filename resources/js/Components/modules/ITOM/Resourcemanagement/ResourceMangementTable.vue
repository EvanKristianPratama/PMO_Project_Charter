<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-white/10">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">SDM List</h2>
            <div class="flex items-center gap-3">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari SDM..."
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white w-48"
                />
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-white dark:hover:bg-slate-100 dark:text-slate-950 dark:focus:ring-white dark:focus:ring-offset-[#171717]"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add SDM
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-16">No</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Internal ID</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Jabatan</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">SK Penugasan</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Start Date</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">End Date</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-36">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="(resource, index) in filteredRows"
                        :key="resource.id"
                        class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                    >
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ index + 1 }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300 font-mono text-xs">{{ resource.internal_id || '-' }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300 font-medium">{{ resource.name || '-' }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ resource.organization ? (resource.organization.jabatan || resource.organization.name) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ resource.sk || '-' }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ resource.start || '-' }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ resource.end || '-' }}</td>
                        <td class="px-4 py-3 text-center space-x-3 w-36">
                            <button
                                @click="openEditModal(resource)"
                                class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition"
                            >
                                Edit
                            </button>
                            <button
                                @click="openDeleteModal(resource)"
                                class="inline-flex items-center text-xs font-semibold text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr v-if="filteredRows.length === 0">
                        <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                            Data SDM tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Create & Edit Modal -->
    <ConfirmationModal
        :show="isModalOpen"
        :title="modalMode === 'create' ? 'Tambah SDM' : 'Edit SDM'"
        :message="modalMode === 'create' ? 'Silakan isi formulir di bawah ini untuk menambahkan SDM baru.' : 'Silakan sesuaikan data SDM di bawah ini.'"
        confirm-text="Simpan"
        cancel-text="Batal"
        type="info"
        :loading="form.processing"
        @close="isModalOpen = false"
        @confirm="submitForm"
    >
        <div class="mt-4 space-y-4">
            <!-- Internal ID Input -->
            <div class="flex flex-col gap-1.5">
                <label for="res_internal_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Internal ID</label>
                <input
                    id="res_internal_id"
                    v-model="form.internal_id"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white font-mono"
                    placeholder="Contoh: INT-12345"
                />
                <span v-if="form.errors.internal_id" class="text-xs text-red-500 font-medium">{{ form.errors.internal_id }}</span>
            </div>

            <!-- Name Input -->
            <div class="flex flex-col gap-1.5">
                <label for="res_name" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama</label>
                <input
                    id="res_name"
                    v-model="form.name"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: John Doe"
                />
                <span v-if="form.errors.name" class="text-xs text-red-500 font-medium">{{ form.errors.name }}</span>
            </div>

            <!-- Jabatan Input -->
            <div class="flex flex-col gap-1.5">
                <label for="res_jabatan" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Jabatan</label>
                <select
                    id="res_jabatan"
                    v-model="form.jabatan"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                >
                    <option value="" disabled>Pilih Jabatan...</option>
                    <option v-for="org in organizations" :key="org.id" :value="org.id">
                        {{ org.jabatan ? `${org.jabatan} (${org.name})` : org.name }}
                    </option>
                </select>
                <span v-if="form.errors.jabatan" class="text-xs text-red-500 font-medium">{{ form.errors.jabatan }}</span>
            </div>

            <!-- SK Input -->
            <div class="flex flex-col gap-1.5">
                <label for="res_sk" class="text-xs font-semibold text-slate-700 dark:text-slate-300">SK Penugasan</label>
                <input
                    id="res_sk"
                    v-model="form.sk"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: 31 Desember 2026 atau 1 Tahun"
                />
                <span v-if="form.errors.sk" class="text-xs text-red-500 font-medium">{{ form.errors.sk }}</span>
            </div>

            <!-- Start & End Date Inputs -->
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label for="res_start" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Start Date</label>
                    <input
                        id="res_start"
                        v-model="form.start"
                        type="date"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    />
                    <span v-if="form.errors.start" class="text-xs text-red-500 font-medium">{{ form.errors.start }}</span>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="res_end" class="text-xs font-semibold text-slate-700 dark:text-slate-300">End Date</label>
                    <input
                        id="res_end"
                        v-model="form.end"
                        type="date"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    />
                    <span v-if="form.errors.end" class="text-xs text-red-500 font-medium">{{ form.errors.end }}</span>
                </div>
            </div>
        </div>
    </ConfirmationModal>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
        :show="isDeleteModalOpen"
        title="Hapus SDM"
        :message="`Apakah Anda yakin ingin menghapus SDM '${selectedResource?.name || selectedResource?.id}'? Tindakan ini tidak dapat dibatalkan.`"
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
    resources: {
        type: Array,
        default: () => [],
    },
    organizations: {
        type: Array,
        default: () => [],
    },
});

const searchQuery = ref('');
const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedResource = ref(null);
const modalMode = ref('create');

const form = useForm({
    name: '',
    jabatan: '',
    internal_id: '',
    sk: '',
    start: '',
    end: '',
});

// Build sorted/flattened organization hierarchy list
const sortedOrganizations = computed(() => {
    const orgs = props.organizations;
    const orgMap = new Map(orgs.map(org => [org.id, { ...org, children: [] }]));
    const roots = [];

    orgs.forEach(org => {
        const mapped = orgMap.get(org.id);
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

const orgOrderMap = computed(() => {
    const map = new Map();
    sortedOrganizations.value.forEach((org, index) => {
        map.set(Number(org.id), index);
    });
    return map;
});

const filteredRows = computed(() => {
    let rows = props.resources;

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase().trim();
        rows = rows.filter(res =>
            (res.name || '').toLowerCase().includes(q) ||
            (res.organization?.jabatan || '').toLowerCase().includes(q) ||
            (res.organization?.name || '').toLowerCase().includes(q) ||
            (res.internal_id || '').toLowerCase().includes(q) ||
            (res.sk || '').toLowerCase().includes(q) ||
            (res.start || '').toLowerCase().includes(q) ||
            (res.end || '').toLowerCase().includes(q) ||
            String(res.id).includes(q)
        );
    }

    // Sort rows based on organization hierarchy order
    return [...rows].sort((a, b) => {
        const orderA = a.jabatan ? (orgOrderMap.value.get(Number(a.jabatan)) ?? Infinity) : Infinity;
        const orderB = b.jabatan ? (orgOrderMap.value.get(Number(b.jabatan)) ?? Infinity) : Infinity;

        if (orderA !== orderB) {
            return orderA - orderB;
        }

        return (a.name || '').localeCompare(b.name || '');
    });
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.clearErrors();
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (resource) => {
    modalMode.value = 'edit';
    selectedResource.value = resource;
    form.clearErrors();
    form.name = resource.name || '';
    form.jabatan = resource.jabatan || '';
    form.internal_id = resource.internal_id || '';
    form.sk = resource.sk || '';
    form.start = resource.start || '';
    form.end = resource.end || '';
    isModalOpen.value = true;
};

const openDeleteModal = (resource) => {
    selectedResource.value = resource;
    isDeleteModalOpen.value = true;
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('itom.resource-management.store'), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('itom.resource-management.update', selectedResource.value.id), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const submitDelete = () => {
    form.delete(route('itom.resource-management.destroy', selectedResource.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
};
</script>
