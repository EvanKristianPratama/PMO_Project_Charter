<template>
    <!-- Company View -->
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 px-4 py-3 gap-3 dark:border-white/10">
            <!-- Left Side: Add Company & Filter -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                <button
                    @click="openCompanyModal"
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-emerald-400 dark:focus:ring-offset-[#171717]"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Company
                </button>

                <!-- Parent Company Filter -->
                <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg px-2.5 py-1">
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Parent:</span>
                    <select
                        v-model="selectedParentFilter"
                        class="border-0 bg-transparent py-0 pl-1 pr-7 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-0 cursor-pointer"
                    >
                        <option value="all" class="dark:bg-[#171717]">Semua Company</option>
                        <option
                            v-for="c in parentFilterOptions"
                            :key="c.id"
                            :value="c.id"
                            class="dark:bg-[#171717]"
                        >
                            {{ c.name }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- View Mode Toggle -->
            <div class="flex gap-1 shrink-0">
                <button
                    :class="[
                        'inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition',
                        viewMode === 'table'
                            ? 'bg-blue-500 text-white shadow-sm hover:bg-blue-600'
                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-white/10 dark:text-slate-300 dark:hover:bg-white/20',
                    ]"
                    @click="viewMode = 'table'"
                >
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M10 3v18M14 3v18" />
                    </svg>
                    Table
                </button>
                <button
                    :class="[
                        'inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition',
                        viewMode === 'tree'
                            ? 'bg-blue-500 text-white shadow-sm hover:bg-blue-600'
                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-white/10 dark:text-slate-300 dark:hover:bg-white/20',
                    ]"
                    @click="viewMode = 'tree'"
                >
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h6" />
                    </svg>
                    Tree
                </button>
            </div>
        </div>

        <!-- Table View -->
        <div v-if="viewMode === 'table'" class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-[11px] dark:divide-white/10 table-fixed">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-16">No</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Company</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Singkatan</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-20">Level</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Grup</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Parent</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Organization</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-64">Daftar Grup</th>
                        <th class="px-4 py-2 text-center text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-48">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="(company, index) in filteredCompanies"
                        :key="company.id"
                        class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                    >
                        <td class="px-4 py-2.5 text-slate-500 dark:text-slate-400 text-left w-16">{{ index + 1 }}</td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white font-medium text-left">
                            {{ company.name }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-700 dark:text-slate-300 text-left">
                            {{ company.singkatan || '-' }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-700 dark:text-slate-300 text-left font-semibold text-blue-600 dark:text-blue-400">
                            {{ company.level || '-' }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-700 dark:text-slate-300 text-left font-semibold text-indigo-600 dark:text-indigo-400">
                            {{ company.grup || '-' }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-700 dark:text-slate-300 text-left">
                            {{ company.parent_name || '-' }}
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
                            <div class="flex flex-col items-center justify-center gap-1">
                                <button
                                    @click="openEditCompanyModal(company)"
                                    class="inline-flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-white/10 dark:hover:bg-white/20 dark:text-slate-200 px-2.5 py-0.5 text-[10px] font-semibold transition w-full max-w-[56px]"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="openDeleteCompanyModal(company)"
                                    class="inline-flex items-center justify-center rounded-full bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-500/10 dark:hover:bg-red-500/20 dark:text-red-400 px-2.5 py-0.5 text-[10px] font-semibold transition w-full max-w-[56px]"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredCompanies.length === 0">
                        <td colspan="9" class="px-4 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                            Data company tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tree View -->
        <div v-if="viewMode === 'tree'" class="px-4 py-4">
            <CompanyThreeView
                :companies="filteredCompanies"
                :is-root="true"
            />
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

            <!-- Parent Company Dropdown -->
            <div class="flex flex-col gap-1.5">
                <label for="company_parent_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Parent Company <span class="font-normal text-slate-400">(opsional)</span></label>
                <select
                    id="company_parent_id"
                    v-model="companyForm.parent_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                >
                    <option :value="null">— Tidak ada (Company Induk) —</option>
                    <option
                        v-for="c in parentCompanyOptions"
                        :key="c.id"
                        :value="c.id"
                    >{{ c.name }}</option>
                </select>
                <span v-if="companyForm.errors.parent_id" class="text-xs text-red-500 font-medium">{{ companyForm.errors.parent_id }}</span>
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

            <!-- Singkatan Input -->
            <div class="flex flex-col gap-1.5">
                <label for="company_singkatan" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Singkatan <span class="font-normal text-slate-400">(opsional)</span></label>
                <input
                    id="company_singkatan"
                    v-model="companyForm.singkatan"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: PLN, PGN, PGAS"
                />
                <span v-if="companyForm.errors.singkatan" class="text-xs text-red-500 font-medium">{{ companyForm.errors.singkatan }}</span>
            </div>

            <!-- Grup Input -->
            <div class="flex flex-col gap-1.5">
                <label for="company_grup" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Grup <span class="font-normal text-slate-400">(opsional)</span></label>
                <input
                    id="company_grup"
                    v-model="companyForm.grup"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: PLN Group, Pertamina Group"
                />
                <span v-if="companyForm.errors.grup" class="text-xs text-red-500 font-medium">{{ companyForm.errors.grup }}</span>
            </div>

            <!-- Level Input -->
            <div class="flex flex-col gap-1.5">
                <label for="company_level" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Level <span class="font-normal text-slate-400">(opsional, angka)</span></label>
                <input
                    id="company_level"
                    v-model="companyForm.level"
                    type="number"
                    min="1"
                    step="1"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: 1, 2, 3"
                />
                <span v-if="companyForm.errors.level" class="text-xs text-red-500 font-medium">{{ companyForm.errors.level }}</span>
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
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import CompanyThreeView from '@/Components/modules/ITOM/Architecture/Organization/CompanyThreeView.vue';

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
const viewMode = ref('tree'); // 'table' | 'tree'
const selectedCompany = ref(null);
const companyForm = useForm({
    parent_id: null,
    name: '',
    organization: '',
    singkatan: '',
    grup: '',
    level: '',
});

// Computed: companies selectable as parent (excludes the company being edited)
const parentCompanyOptions = computed(() => {
    if (companyModalMode.value === 'edit' && selectedCompany.value) {
        return props.companies.filter(c => c.id !== selectedCompany.value.id);
    }
    return props.companies;
});

// Filtering state & logic
const selectedParentFilter = ref('all');

const getDescendants = (companyId, list) => {
    const children = list.filter(c => Number(c.parent_id) === Number(companyId));
    let result = [...children];
    for (const child of children) {
        result = [...result, ...getDescendants(child.id, list)];
    }
    return result;
};

const filteredCompanies = computed(() => {
    if (selectedParentFilter.value === 'all') {
        return props.companies;
    }
    
    const selectedId = Number(selectedParentFilter.value);
    const targetCompany = props.companies.find(c => Number(c.id) === selectedId);
    if (!targetCompany) return [];
    
    return [targetCompany, ...getDescendants(selectedId, props.companies)];
});

const parentFilterOptions = computed(() => {
    const parentIds = new Set(props.companies.map(c => c.parent_id).filter(id => id !== null && id !== undefined));
    return [...props.companies]
        .filter(c => parentIds.has(c.id))
        .sort((a, b) => a.name.localeCompare(b.name));
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
    companyForm.parent_id = company.parent_id ?? null;
    companyForm.name = company.name;
    companyForm.organization = company.organization || '';
    companyForm.singkatan = company.singkatan || '';
    companyForm.grup = company.grup || '';
    companyForm.level = company.level ?? '';
    
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
        companyForm.post(route('itom.business-process.organization-structure.company.store'), {
            onSuccess: () => {
                isCompanyModalOpen.value = false;
                companyForm.reset();
            },
        });
    } else {
        companyForm.put(route('itom.business-process.organization-structure.company.update', selectedCompany.value.id), {
            onSuccess: () => {
                isCompanyModalOpen.value = false;
                companyForm.reset();
            },
        });
    }
};

const submitDeleteCompany = () => {
    companyForm.delete(route('itom.business-process.organization-structure.company.destroy', selectedCompany.value.id), {
        onSuccess: () => {
            isCompanyDeleteModalOpen.value = false;
        },
    });
};

// Group CRUD methods
const submitCreateGroup = () => {
    groupCreateForm.post(route('itom.business-process.organization-structure.group.store'), {
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
    groupEditForm.put(route('itom.business-process.organization-structure.group.update', groupId), {
        onSuccess: () => {
            editingGroupId.value = null;
            groupEditForm.reset();
        },
    });
};

const submitDeleteGroup = (group) => {
    if (confirm(`Apakah Anda yakin ingin menghapus grup '${group.group_name}'? Seluruh struktur organisasi di dalamnya akan dihapus.`)) {
        groupDeleteForm.delete(route('itom.business-process.organization-structure.group.destroy', group.id), {
            onSuccess: () => {
                // Success is automatically handled/redirected by Inertia
            },
        });
    }
};
</script>
