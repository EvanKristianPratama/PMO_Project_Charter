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
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-24">ID</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Jabatan</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Internal ID</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Masa Berlaku</th>
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
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 font-mono text-xs">
                            {{ resource.id }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300 font-medium">{{ resource.name || '-' }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ resource.jabatan || '-' }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300 font-mono text-xs">{{ resource.internal_id || '-' }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ resource.masa_berlaku || '-' }}</td>
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
                        <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
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
                <input
                    id="res_jabatan"
                    v-model="form.jabatan"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: Senior Developer"
                />
                <span v-if="form.errors.jabatan" class="text-xs text-red-500 font-medium">{{ form.errors.jabatan }}</span>
            </div>

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

            <!-- Masa Berlaku Input -->
            <div class="flex flex-col gap-1.5">
                <label for="res_masa_berlaku" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Masa Berlaku</label>
                <input
                    id="res_masa_berlaku"
                    v-model="form.masa_berlaku"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: 31 Desember 2026 atau 1 Tahun"
                />
                <span v-if="form.errors.masa_berlaku" class="text-xs text-red-500 font-medium">{{ form.errors.masa_berlaku }}</span>
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
    masa_berlaku: '',
});

const filteredRows = computed(() => {
    let rows = props.resources;

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase().trim();
        rows = rows.filter(res =>
            (res.name || '').toLowerCase().includes(q) ||
            (res.jabatan || '').toLowerCase().includes(q) ||
            (res.internal_id || '').toLowerCase().includes(q) ||
            (res.masa_berlaku || '').toLowerCase().includes(q) ||
            String(res.id).includes(q)
        );
    }

    return rows;
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
    form.masa_berlaku = resource.masa_berlaku || '';
    isModalOpen.value = true;
};

const openDeleteModal = (resource) => {
    selectedResource.value = resource;
    isDeleteModalOpen.value = true;
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('resource-management.store'), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('resource-management.update', selectedResource.value.id), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const submitDelete = () => {
    form.delete(route('resource-management.destroy', selectedResource.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
};
</script>
