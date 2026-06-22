<template>
    <!-- Company View -->
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-white/10">
            <button
                @click="openCompanyModal"
                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-emerald-400 dark:focus:ring-offset-[#171717]"
            >
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Company
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-[11px] dark:divide-white/10 table-fixed">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-16">No</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Company</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Organization</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-64">Grup</th>
                        <th class="px-4 py-2 text-center text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-48">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="(company, index) in companies"
                        :key="company.id"
                        class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                    >
                        <td class="px-4 py-2.5 text-slate-500 dark:text-slate-400 text-left w-16">{{ index + 1 }}</td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white font-medium text-left">
                            {{ company.name }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white text-left font-medium">
                            {{ company.organization || '-' }}
                        </td>
                        <td class="px-4 py-2.5 text-left w-64">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span
                                    v-for="group in getCompanyGroups(company.id)"
                                    :key="group.id"
                                    class="inline-flex items-center rounded-md bg-slate-100 dark:bg-white/5 px-2 py-0.5 text-[10px] font-semibold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-white/10"
                                >
                                    {{ group.group_name }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-2.5 text-center w-48">
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    @click="openEditCompanyModal(company)"
                                    class="inline-flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-white/10 dark:hover:bg-white/20 dark:text-slate-200 px-3 py-1 text-[10px] font-semibold transition"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="openDeleteCompanyModal(company)"
                                    class="inline-flex items-center justify-center rounded-full bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-500/10 dark:hover:bg-red-500/20 dark:text-red-400 px-3 py-1 text-[10px] font-semibold transition"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="companies.length === 0">
                        <td colspan="5" class="px-4 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                            Data company tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Create/Edit Company Modal -->
    <ConfirmationModal
        :show="isCompanyModalOpen"
        :title="companyModalMode === 'create' ? 'Tambah Company' : 'Edit Company & Groups'"
        message=""
        confirm-text="Save"
        cancel-text="Cancel"
        type="info"
        :loading="companyForm.processing"
        @close="isCompanyModalOpen = false"
        @confirm="submitCompanyForm"
    >
        <div class="mt-4 space-y-4">
            <!-- Company Name Input -->
            <div class="flex flex-col gap-1.5">
                <label for="company_name" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Company</label>
                <input
                    id="company_name"
                    v-model="companyForm.name"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: PT. PLN (Persero)"
                    required
                />
                <span v-if="companyForm.errors.name" class="text-xs text-red-500 font-medium">{{ companyForm.errors.name }}</span>
            </div>

            <!-- Organization Input -->
            <div class="flex flex-col gap-1.5">
                <label for="company_organization" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Organization</label>
                <input
                    id="company_organization"
                    v-model="companyForm.organization"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: Divisi TI / Unit Induk"
                />
                <span v-if="companyForm.errors.organization" class="text-xs text-red-500 font-medium">{{ companyForm.errors.organization }}</span>
            </div>

            <!-- Manage Groups Section (Only shown when editing company) -->
            <div v-if="companyModalMode === 'edit'" class="space-y-4 pt-4 border-t border-slate-200 dark:border-white/10">
                
                <!-- Add Group Inline Form -->
                <div class="flex gap-2 items-end">
                    <div class="flex-1 flex flex-col gap-1.5">
                        <label for="new_group_name" class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">New Grup</label>
                        <input
                            id="new_group_name"
                            v-model="groupCreateForm.name"
                            type="text"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                            placeholder="Contoh: Operation / Holding"
                            @keydown.enter.prevent="submitCreateGroup"
                        />
                    </div>
                    <button
                        type="button"
                        @click="submitCreateGroup"
                        :disabled="groupCreateForm.processing"
                        class="inline-flex items-center gap-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white px-3 py-2 text-xs font-semibold shadow-sm transition-all focus:outline-none"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add
                    </button>
                </div>

                <!-- Groups List -->
                <div class="max-h-48 overflow-y-auto space-y-2 pr-1">
                    <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1">Grup List</div>
                    <div
                        v-for="group in getCompanyGroups(selectedCompany?.id)"
                        :key="group.id"
                        class="flex items-center justify-between p-2 rounded-lg border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5"
                    >
                        <!-- Group Name Display / Edit Mode -->
                        <div class="flex-1 mr-4">
                            <div v-if="editingGroupId !== group.id" class="text-xs font-medium text-slate-950 dark:text-slate-100">
                                {{ group.group_name }}
                            </div>
                            <input
                                v-else
                                v-model="groupEditForm.name"
                                type="text"
                                class="w-full rounded border border-slate-300 bg-white px-2 py-1 text-xs text-slate-900 focus:border-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                                required
                                @keydown.enter.prevent="submitUpdateGroup(group.id)"
                                @keydown.esc="cancelEditGroup"
                            />
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 shrink-0">
                            <template v-if="editingGroupId !== group.id">
                               <button
                                   type="button"
                                   @click="startEditGroup(group)"
                                   class="text-[10px] font-semibold text-blue-500 hover:text-blue-600"
                               >
                                   Edit
                               </button>
                               <button
                                   type="button"
                                   @click="submitDeleteGroup(group)"
                                   class="text-[10px] font-semibold text-red-500 hover:text-red-600"
                               >
                                   Delete
                               </button>
                            </template>
                            <template v-else>
                               <button
                                   type="button"
                                   @click="submitUpdateGroup(group.id)"
                                   :disabled="groupEditForm.processing"
                                   class="text-[10px] font-semibold text-green-500 hover:text-green-600"
                               >
                                   Simpan
                               </button>
                               <button
                                   type="button"
                                   @click="cancelEditGroup"
                                   class="text-[10px] font-semibold text-slate-500 hover:text-slate-600 dark:text-slate-400"
                               >
                                   Batal
                               </button>
                            </template>
                        </div>
                    </div>
                    <div v-if="getCompanyGroups(selectedCompany?.id).length === 0" class="text-xs text-center text-slate-500 dark:text-slate-400 py-4">
                        Grup Not Available
                    </div>
                </div>
            </div>
        </div>
    </ConfirmationModal>

    <!-- Delete Company Confirmation Modal -->
    <ConfirmationModal
        :show="isCompanyDeleteModalOpen"
        title="Hapus Company"
        :message="`Apakah Anda yakin ingin menghapus company '${selectedCompany?.name}'? Tindakan ini akan menghapus seluruh grup dan struktur organisasi di dalamnya secara permanen.`"
        confirm-text="Delete"
        cancel-text="Cancel"
        type="danger"
        :loading="companyForm.processing"
        @close="isCompanyDeleteModalOpen = false"
        @confirm="submitDeleteCompany"
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
    groubOptions: {
        type: Array,
        default: () => [],
    },
});

const isCompanyModalOpen = ref(false);
const isCompanyDeleteModalOpen = ref(false);
const companyModalMode = ref('create'); // 'create' or 'edit'
const selectedCompany = ref(null);
const companyForm = useForm({
    name: '',
    organization: '',
});

// Group CRUD state
const editingGroupId = ref(null);

const groupCreateForm = useForm({
    company_id: '',
    name: '',
});

const groupEditForm = useForm({
    name: '',
});

const groupDeleteForm = useForm({});

const getCompanyGroups = (companyId) => {
    if (!companyId) return [];
    return props.groubOptions.filter(opt => opt.company_id !== null && opt.company_id !== undefined && Number(opt.company_id) === Number(companyId));
};

const openCompanyModal = () => {
    companyModalMode.value = 'create';
    companyForm.clearErrors();
    companyForm.reset();
    isCompanyModalOpen.value = true;
};

const openEditCompanyModal = (company) => {
    companyModalMode.value = 'edit';
    selectedCompany.value = company;
    companyForm.clearErrors();
    companyForm.name = company.name;
    companyForm.organization = company.organization || '';
    
    // Initialize group CRUD state for the selected company
    editingGroupId.value = null;
    groupCreateForm.reset();
    groupCreateForm.company_id = company.id;
    
    isCompanyModalOpen.value = true;
};

const openDeleteCompanyModal = (company) => {
    selectedCompany.value = company;
    isCompanyDeleteModalOpen.value = true;
};

const submitCompanyForm = () => {
    if (companyModalMode.value === 'create') {
        companyForm.post(route('architecture.organization-structure.company.store'), {
            onSuccess: () => {
                isCompanyModalOpen.value = false;
                companyForm.reset();
            },
        });
    } else {
        companyForm.put(route('architecture.organization-structure.company.update', selectedCompany.value.id), {
            onSuccess: () => {
                isCompanyModalOpen.value = false;
                companyForm.reset();
            },
        });
    }
};

const submitDeleteCompany = () => {
    companyForm.delete(route('architecture.organization-structure.company.destroy', selectedCompany.value.id), {
        onSuccess: () => {
            isCompanyDeleteModalOpen.value = false;
        },
    });
};

// Group CRUD methods
const submitCreateGroup = () => {
    groupCreateForm.post(route('architecture.organization-structure.group.store'), {
        onSuccess: () => {
            groupCreateForm.reset('name');
        },
    });
};

const startEditGroup = (group) => {
    editingGroupId.value = group.id;
    groupEditForm.name = group.group_name;
};

const cancelEditGroup = () => {
    editingGroupId.value = null;
    groupEditForm.reset();
};

const submitUpdateGroup = (groupId) => {
    groupEditForm.put(route('architecture.organization-structure.group.update', groupId), {
        onSuccess: () => {
            editingGroupId.value = null;
            groupEditForm.reset();
        },
    });
};

const submitDeleteGroup = (group) => {
    if (confirm(`Apakah Anda yakin ingin menghapus grup '${group.group_name}'? Seluruh struktur organisasi di dalamnya akan dihapus.`)) {
        groupDeleteForm.delete(route('architecture.organization-structure.group.destroy', group.id), {
            onSuccess: () => {
                // Success is automatically handled/redirected by Inertia
            },
        });
    }
};
</script>
