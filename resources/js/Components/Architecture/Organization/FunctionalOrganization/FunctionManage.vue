<template>
    <!-- Create & Edit Modal -->
    <ConfirmationModal
        :show="isModalOpen"
        :title="modalMode === 'create' ? 'Tambah Organisasi Fungsional' : 'Edit Organisasi Fungsional'"
        :message="modalMode === 'create' ? 'Silakan isi formulir di bawah ini untuk menambahkan organisasi fungsional baru.' : 'Silakan sesuaikan data organisasi fungsional di bawah ini.'"
        confirm-text="Simpan"
        cancel-text="Batal"
        type="info"
        :loading="form.processing"
        @close="isModalOpen = false"
        @confirm="submitForm"
    >
        <div class="mt-4 space-y-4 text-left">
            <!-- Company Select -->
            <div class="flex flex-col gap-1.5">
                <label for="company_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Company</label>
                <select
                    id="company_id"
                    v-model="form.company_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    required
                >
                    <option value="" disabled>Pilih Company...</option>
                    <option v-for="company in companies" :key="company.id" :value="company.id">
                        {{ company.name }}
                    </option>
                </select>
                <span v-if="form.errors.company_id" class="text-xs text-red-500 font-medium">{{ form.errors.company_id }}</span>
            </div>

            <!-- Name Input -->
            <div class="flex flex-col gap-1.5">
                <label for="name" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Organisasi</label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: Divisi Keuangan fungsional"
                    required
                />
                <span v-if="form.errors.name" class="text-xs text-red-500 font-medium">{{ form.errors.name }}</span>
            </div>

            <!-- SK Select -->
            <div class="flex flex-col gap-1.5">
                <label for="sk_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">SK Organisasi</label>
                <select
                    id="sk_id"
                    v-model="form.sk_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    required
                >
                    <option value="" disabled>Pilih SK...</option>
                    <option v-for="sk in skOrganizations" :key="sk.id" :value="sk.id">
                        {{ sk.sk }}
                    </option>
                </select>
                <span v-if="form.errors.sk_id" class="text-xs text-red-500 font-medium">{{ form.errors.sk_id }}</span>
            </div>
        </div>
    </ConfirmationModal>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
        :show="isDeleteModalOpen"
        title="Hapus Organisasi Fungsional"
        :message="`Apakah Anda yakin ingin menghapus organisasi fungsional '${selectedFunctional?.name}'? Tindakan ini tidak dapat dibatalkan.`"
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
    companies: {
        type: Array,
        default: () => [],
    },
    skOrganizations: {
        type: Array,
        default: () => [],
    },
});

const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedFunctional = ref(null);
const modalMode = ref('create');

const form = useForm({
    company_id: '',
    name: '',
    sk_id: '',
});

const openCreate = () => {
    modalMode.value = 'create';
    form.clearErrors();
    form.reset();
    isModalOpen.value = true;
};

const openEdit = (row) => {
    modalMode.value = 'edit';
    selectedFunctional.value = row;
    form.clearErrors();
    form.company_id = String(row.company_id || '');
    form.name = row.name || '';
    form.sk_id = String(row.sk_id || '');
    isModalOpen.value = true;
};

const openDelete = (row) => {
    selectedFunctional.value = row;
    isDeleteModalOpen.value = true;
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('business-process.organization-structure.functional.store'), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('business-process.organization-structure.functional.update', selectedFunctional.value.id), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const submitDelete = () => {
    form.delete(route('business-process.organization-structure.functional.destroy', selectedFunctional.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
};

defineExpose({
    openCreate,
    openEdit,
    openDelete,
});
</script>
