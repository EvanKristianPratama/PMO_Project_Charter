<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-white/10">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Organization List</h2>
            <button
                @click="openCreateModal"
                class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-white dark:hover:bg-slate-100 dark:text-slate-950 dark:focus:ring-white dark:focus:ring-offset-[#171717]"
            >
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Organisasi
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">No</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Company</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Group</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Code</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Organization</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-36">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="(organizationStructureRow, index) in organizationStructureRows"
                        :key="organizationStructureRow.organization_id"
                        class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                    >
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ index + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-100">
                            {{ displayValue(organizationStructureRow.company_name) }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ displayValue(organizationStructureRow.groub_name) }}
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 font-mono text-xs">
                            {{ displayValue(organizationStructureRow.code) }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ displayValue(organizationStructureRow.organization_name) }}
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
                    <tr v-if="organizationStructureRows.length === 0">
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
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
            <!-- Group Option Select -->
            <div class="flex flex-col gap-1.5">
                <label for="groub_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Group</label>
                <select
                    id="groub_id"
                    v-model="form.groub_id"
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
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
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
import { ref } from 'vue';
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

const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedOrg = ref(null);
const modalMode = ref('create'); // 'create' or 'edit'

const form = useForm({
    groub_id: '',
    code: '',
    name: '',
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.clearErrors();
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (org) => {
    modalMode.value = 'edit';
    selectedOrg.value = org;
    form.clearErrors();
    form.groub_id = org.groub_id || '';
    form.code = org.code || '';
    form.name = org.organization_name || '';
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
