<template>
    <MasterDataLayout title="Master Data — PIC Project">
        <div class="space-y-3">
            <!-- Table Section -->
            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-slate-200 text-[11px] dark:divide-white/10">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <!-- Filter & Actions Row (At the top of the table) -->
                            <tr>
                                <th class="px-3 py-2 align-top text-left">
                                    <select
                                        v-model="selectedOrganization"
                                        class="w-full min-w-[200px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] font-normal text-slate-700 outline-none focus:border-[#1C75BC] focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                    >
                                        <option value="">Semua Organisasi</option>
                                        <option v-for="org in organizations" :key="org.id" :value="org.id">
                                            {{ org.name }}
                                        </option>
                                    </select>
                                </th>
                                <th class="px-3 py-2 align-top">
                                    <!-- Spacer -->
                                </th>
                                <th class="px-3 py-2 align-top text-right">
                                    <div class="flex flex-nowrap items-center justify-end gap-1 whitespace-nowrap">
                                        <button
                                            type="button"
                                            class="inline-flex rounded border border-[#1C75BC] bg-[#1C75BC] px-2 py-1 text-[10px] font-semibold text-white transition hover:bg-[#165f98]"
                                            @click="toggleCreateRow"
                                        >
                                            {{ showCreateRow ? 'Tutup' : 'Tambah' }}
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex rounded border border-slate-300 px-2 py-1 text-[10px] font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/10"
                                            @click="resetFilters"
                                        >
                                            Reset
                                        </button>
                                    </div>
                                </th>
                            </tr>
                            <!-- Column Labels Row -->
                            <tr class="border-t border-slate-200/80 dark:border-white/10">
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                                    Organisasi / Business Unit
                                </th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                                    Nama PIC
                                </th>
                                <th class="px-3 py-2 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#171717]">
                            <!-- Create row (WYSIWYG) -->
                            <tr v-if="showCreateRow" class="bg-slate-50/60 dark:bg-white/5">
                                <td class="px-3 py-2 align-top">
                                    <select
                                        v-model="createForm.organization_id"
                                        class="w-full min-w-[200px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                    >
                                        <option value="" disabled>Pilih Organisasi</option>
                                        <option v-for="org in organizations" :key="org.id" :value="org.id">{{ org.name }}</option>
                                    </select>
                                    <p v-if="createForm.errors.organization_id" class="mt-1 text-[10px] text-rose-500">
                                        {{ createForm.errors.organization_id }}
                                    </p>
                                </td>
                                <td class="px-3 py-2 align-top">
                                    <input
                                        v-model="createForm.name"
                                        type="text"
                                        placeholder="Nama PIC"
                                        class="w-full min-w-[200px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                    />
                                    <p v-if="createForm.errors.name" class="mt-1 text-[10px] text-rose-500">
                                        {{ createForm.errors.name }}
                                    </p>
                                </td>
                                <td class="px-3 py-2 align-top">
                                    <div class="flex items-center justify-end gap-1">
                                        <button
                                            type="button"
                                            class="inline-flex rounded border border-slate-300 bg-white px-2 py-1 text-[10px] font-semibold text-slate-700 hover:bg-slate-100 disabled:opacity-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200 dark:hover:bg-white/10"
                                            :disabled="createForm.processing"
                                            @click="createRow"
                                        >
                                            Simpan
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex rounded border border-slate-300 px-2 py-1 text-[10px] font-medium text-slate-500 hover:bg-slate-100 dark:border-white/10 dark:text-slate-400 dark:hover:bg-white/10"
                                            @click="resetCreate"
                                        >
                                            Batal
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-for="item in filteredRows" :key="item.id" class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                                <td class="px-3 py-2.5 align-top text-slate-700 dark:text-slate-200">
                                    <template v-if="isEditing(item)">
                                        <select
                                            v-model="editForm.organization_id"
                                            class="w-full min-w-[200px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                        >
                                            <option value="" disabled>Pilih Organisasi</option>
                                            <option v-for="org in organizations" :key="org.id" :value="org.id">{{ org.name }}</option>
                                        </select>
                                        <p v-if="editForm.errors.organization_id" class="mt-1 text-[10px] text-rose-500">
                                            {{ editForm.errors.organization_id }}
                                        </p>
                                    </template>
                                    <template v-else>{{ item.organization?.name ?? '-' }}</template>
                                </td>
                                <td class="px-3 py-2.5 align-top text-slate-600 dark:text-slate-300">
                                    <template v-if="isEditing(item)">
                                        <input
                                            v-model="editForm.name"
                                            type="text"
                                            class="w-full min-w-[200px] rounded border border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                                        />
                                        <p v-if="editForm.errors.name" class="mt-1 text-[10px] text-rose-500">
                                            {{ editForm.errors.name }}
                                        </p>
                                    </template>
                                    <template v-else>{{ item.name }}</template>
                                </td>
                                <td class="px-3 py-2.5 align-top text-right">
                                    <div v-if="isEditing(item)" class="flex items-center justify-end gap-1">
                                        <button
                                            type="button"
                                            class="inline-flex rounded border border-slate-300 bg-white px-2 py-1 text-[10px] font-semibold text-slate-700 hover:bg-slate-100 disabled:opacity-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200 dark:hover:bg-white/10"
                                            :disabled="editForm.processing"
                                            @click="saveEdit"
                                        >
                                            Update
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex rounded border border-slate-300 px-2 py-1 text-[10px] font-medium text-slate-500 hover:bg-slate-100 dark:border-white/10 dark:text-slate-400 dark:hover:bg-white/10"
                                            @click="cancelEdit"
                                        >
                                            Batal
                                        </button>
                                    </div>
                                    <div v-else class="flex items-center justify-end gap-1">
                                        <button
                                            type="button"
                                            class="inline-flex rounded border border-slate-300 bg-white px-2 py-1 text-[10px] font-semibold text-slate-700 hover:bg-slate-100 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200 dark:hover:bg-white/10"
                                            @click="startEdit(item)"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex rounded border border-rose-200 bg-rose-50 px-2 py-1 text-[10px] font-semibold text-rose-700 hover:bg-rose-100 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300 dark:hover:bg-rose-500/20"
                                            @click="confirmDelete(item)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="filteredRows.length === 0">
                                <td colspan="3" class="px-6 py-8 text-center text-xs text-slate-500">Belum ada data.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <ConfirmationModal
            :show="showDeleteModal"
            title="Hapus PIC Project"
            :message="deleteMessage"
            confirm-text="Ya, Hapus"
            cancel-text="Batal"
            type="danger"
            :loading="deleteForm.processing"
            @close="showDeleteModal = false"
            @confirm="executeDelete"
        />
    </MasterDataLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import MasterDataLayout from '@/Layouts/MasterDataLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const route = useRouteHelper();

const props = defineProps({
    picProjects: { type: Array, default: () => [] },
    organizations: { type: Array, default: () => [] },
});

const picProjectRows = computed(() => props.picProjects);
const selectedOrganization = ref('');
const showCreateRow = ref(false);

const createForm = useForm({
    organization_id: '',
    name: '',
});

const editForm = useForm({
    organization_id: '',
    name: '',
});

const editingId = ref(null);
const showDeleteModal = ref(false);
const deleteTarget = ref(null);
const deleteForm = useForm({});

const filteredRows = computed(() => {
    return picProjectRows.value.filter((item) => {
        if (selectedOrganization.value && item.organization_id !== parseInt(selectedOrganization.value)) {
            return false;
        }

        return true;
    });
});

const resetCreate = () => {
    createForm.reset();
    createForm.clearErrors();
};

const toggleCreateRow = () => {
    showCreateRow.value = !showCreateRow.value;

    if (!showCreateRow.value) {
        resetCreate();
    }
};

const resetFilters = () => {
    selectedOrganization.value = '';
};

const createRow = () => {
    createForm.post(route('master-data.pic-projects.store'), {
        preserveScroll: true,
        onSuccess: () => {
            resetCreate();
            showCreateRow.value = false;
        },
    });
};

const startEdit = (item) => {
    editingId.value = item.id;
    editForm.organization_id = item.organization_id ?? '';
    editForm.name = item.name ?? '';
    editForm.clearErrors();
};

const cancelEdit = () => {
    editingId.value = null;
    editForm.reset();
    editForm.clearErrors();
};

const saveEdit = () => {
    if (!editingId.value) return;

    editForm.put(route('master-data.pic-projects.update', editingId.value), {
        preserveScroll: true,
        onSuccess: () => cancelEdit(),
    });
};

const confirmDelete = (item) => {
    deleteTarget.value = item;
    showDeleteModal.value = true;
};

const executeDelete = () => {
    if (!deleteTarget.value) return;

    deleteForm.delete(route('master-data.pic-projects.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            deleteTarget.value = null;
        },
    });
};

const isEditing = (item) => editingId.value === item.id;

const deleteMessage = computed(() => {
    if (!deleteTarget.value) return 'Apakah Anda yakin ingin menghapus data ini?';
    return `Apakah Anda yakin ingin menghapus PIC "${deleteTarget.value.name}"?`;
});
</script>
