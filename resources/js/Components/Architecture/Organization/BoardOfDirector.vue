<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 px-4 py-3 gap-3 dark:border-white/10">
            <div class="flex items-center gap-3">
                <h3 class="text-xs font-bold tracking-wider text-slate-700 dark:text-slate-200">
                    Board of Directors
                </h3>
                <!-- Company Filter (hanya di Table view) -->
                <select
                    v-if="viewMode === 'table'"
                    v-model="selectedCompanyId"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                >
                    <option value="">Semua Company</option>
                    <option v-for="company in companies" :key="company.id" :value="company.id">
                        {{ company.name }}
                    </option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <!-- View Mode Toggle -->
                <div class="flex gap-1">
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
                <button
                    @click="openBODModal"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-emerald-400 dark:focus:ring-offset-[#171717]"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add BOD Member
                </button>
            </div>
        </div>

        <!-- Table View -->
        <div v-if="viewMode === 'table'" class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-[11px] dark:divide-white/10 table-fixed">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-12">No</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Company</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Organization Structure</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Parent</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Pejabat</th>
                        <th class="px-4 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500">Sumber</th>
                        <th class="px-4 py-2 text-center text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="(bod, index) in filteredBods"
                        :key="bod.id"
                        class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                    >
                        <td class="px-4 py-2.5 text-slate-500 dark:text-slate-400 text-left w-12">{{ index + 1 }}</td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white font-medium text-left">
                            {{ bod.company_name }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white font-medium text-left">
                            {{ bod.name }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white font-medium text-left">
                            {{ getParentName(bod.parent_id) || '-' }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white text-left font-medium">
                            {{ bod.pejabat || '-' }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-900 dark:text-white text-left font-medium">
                            {{ bod.sumber || '-' }}
                        </td>
                        <td class="px-4 py-2.5 text-center w-40">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <button
                                    @click="openEditBODModal(bod)"
                                    class="inline-flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-white/10 dark:hover:bg-white/20 dark:text-slate-200 px-2.5 py-0.5 text-[10px] font-semibold transition w-full max-w-[56px]"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="openDeleteBODModal(bod)"
                                    class="inline-flex items-center justify-center rounded-full bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-500/10 dark:hover:bg-red-500/20 dark:text-red-400 px-2.5 py-0.5 text-[10px] font-semibold transition w-full max-w-[56px]"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredBods.length === 0">
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-center">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-white/5 text-slate-400 dark:text-slate-500 mb-4 border border-slate-200 dark:border-white/10">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-1">
                                    Board of Directors Not Available
                                </h4>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tree View -->
        <div v-if="viewMode === 'tree'" class="px-4 py-4 space-y-6">
            <CompanyThreeView
                :companies="companies"
                :is-root="true"
            />
            <BodThreeView
                :companies="companies"
                :bods="bods"
                :is-root="true"
            />
        </div>
    </section>

    <!-- Create/Edit BOD Modal -->
    <ConfirmationModal
        :show="isBODModalOpen"
        :title="bodModalMode === 'create' ? 'Tambah BOD Member' : 'Edit BOD Member'"
        message=""
        confirm-text="Save"
        cancel-text="Cancel"
        type="info"
        :loading="bodForm.processing"
        @close="isBODModalOpen = false"
        @confirm="submitBODForm"
    >
        <div class="mt-4 space-y-4">
            <!-- Company Selection -->
            <div class="flex flex-col gap-1.5">
                <label for="bod_company" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Company</label>
                <select
                    id="bod_company"
                    v-model="bodForm.company_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    required

                >
                    <option value="" disabled>Pilih Company...</option>
                    <option v-for="company in companies" :key="company.id" :value="company.id">
                        {{ company.name }}
                    </option>
                </select>
                <span v-if="bodForm.errors.company_id" class="text-xs text-red-500 font-medium">{{ bodForm.errors.company_id }}</span>
            </div>

            <!-- Parent BOD Selection -->
            <div class="flex flex-col gap-1.5">
                <label for="bod_parent" class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                    Parent Jabatan <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <select
                    id="bod_parent"
                    v-model="bodForm.parent_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                >
                    <option value="">-- Tidak ada parent (jabatan tertinggi) --</option>
                    <option
                        v-for="parentBod in availableParents"
                        :key="parentBod.id"
                        :value="parentBod.id"
                    >
                        [{{ getCompanyName(parentBod.company_id) }}] {{ parentBod.name }}
                    </option>
                </select>
                <span v-if="bodForm.errors.parent_id" class="text-xs text-red-500 font-medium">{{ bodForm.errors.parent_id }}</span>
            </div>

            <!-- BOD Member Name Input -->
            <div class="flex flex-col gap-1.5">
                <label for="bod_name" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Jabatan (Organization Structure)</label>
                <input
                    id="bod_name"
                    v-model="bodForm.name"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: Direktur Utama / Direktur Hulu"
                    required
                />
                <span v-if="bodForm.errors.name" class="text-xs text-red-500 font-medium">{{ bodForm.errors.name }}</span>
            </div>

            <!-- BOD Member Pejabat Input -->
            <div class="flex flex-col gap-1.5">
                <label for="bod_pejabat" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Pejabat (Official)</label>
                <input
                    id="bod_pejabat"
                    v-model="bodForm.pejabat"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: Ir. Budi Santoso"
                />
                <span v-if="bodForm.errors.pejabat" class="text-xs text-red-500 font-medium">{{ bodForm.errors.pejabat }}</span>
            </div>

            <!-- BOD Sumber Input -->
            <div class="flex flex-col gap-1.5">
                <label for="bod_sumber" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Sumber</label>
                <input
                    id="bod_sumber"
                    v-model="bodForm.sumber"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: SK Direksi / Internal"
                />
                <span v-if="bodForm.errors.sumber" class="text-xs text-red-500 font-medium">{{ bodForm.errors.sumber }}</span>
            </div>
        </div>
    </ConfirmationModal>

    <!-- Delete BOD Confirmation Modal -->
    <ConfirmationModal
        :show="isBODDeleteModalOpen"
        title="Hapus BOD Member"
        :message="`Apakah Anda yakin ingin menghapus '${selectedBod?.name}' dari Board of Directors?`"
        confirm-text="Delete"
        cancel-text="Cancel"
        type="danger"
        :loading="bodForm.processing"
        @close="isBODDeleteModalOpen = false"
        @confirm="submitDeleteBOD"
    />
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import BodThreeView from '@/Components/Architecture/Organization/BodThreeView.vue';
import CompanyThreeView from '@/Components/Architecture/Organization/CompanyThreeView.vue';

const props = defineProps({
    companies: {
        type: Array,
        default: () => [],
    },
    bods: {
        type: Array,
        default: () => [],
    },
});

const selectedCompanyId = ref('');
const viewMode = ref('table'); // 'table' | 'tree'

const filteredBods = computed(() => {
    if (!selectedCompanyId.value) {
        return props.bods;
    }
    return props.bods.filter(bod => Number(bod.company_id) === Number(selectedCompanyId.value));
});

/**
 * Kandidat parent: Semua BOD dari semua company, exclude diri sendiri (saat edit).
 * Ditampilkan dengan informasi nama company untuk konteks.
 */
const availableParents = computed(() => {
    return props.bods.filter(bod => {
        // Saat edit, exclude diri sendiri dari pilihan parent
        if (bodModalMode.value === 'edit' && selectedBod.value && bod.id === selectedBod.value.id) return false;
        return true;
    });
});

/**
 * Dapatkan nama company berdasarkan company_id.
 */
const getCompanyName = (companyId) => {
    if (!companyId) return '-';
    const company = props.companies.find(c => Number(c.id) === Number(companyId));
    return company ? company.name : '-';
};

/**
 * Dapatkan nama jabatan parent berdasarkan parent_id.
 */
const getParentName = (parentId) => {
    if (!parentId) return null;
    const parent = props.bods.find(b => b.id === Number(parentId));
    return parent ? parent.name : null;
};

const isBODModalOpen = ref(false);
const isBODDeleteModalOpen = ref(false);
const bodModalMode = ref('create'); // 'create' or 'edit'
const selectedBod = ref(null);

const bodForm = useForm({
    company_id: '',
    parent_id: '',
    name: '',
    sumber: '',
    pejabat: '',
});

const openBODModal = () => {
    bodModalMode.value = 'create';
    selectedBod.value = null;
    bodForm.clearErrors();
    bodForm.reset();
    isBODModalOpen.value = true;
};

const openEditBODModal = (bod) => {
    bodModalMode.value = 'edit';
    selectedBod.value = bod;
    bodForm.clearErrors();
    bodForm.company_id = bod.company_id;
    bodForm.parent_id = bod.parent_id ?? '';
    bodForm.name = bod.name;
    bodForm.sumber = bod.sumber || '';
    bodForm.pejabat = bod.pejabat || '';
    isBODModalOpen.value = true;
};

const openDeleteBODModal = (bod) => {
    selectedBod.value = bod;
    isBODDeleteModalOpen.value = true;
};

const submitBODForm = () => {
    if (bodModalMode.value === 'create') {
        bodForm.post(route('architecture.organization-structure.bod.store'), {
            onSuccess: () => {
                isBODModalOpen.value = false;
                bodForm.reset();
            },
        });
    } else {
        bodForm.put(route('architecture.organization-structure.bod.update', selectedBod.value.id), {
            onSuccess: () => {
                isBODModalOpen.value = false;
                bodForm.reset();
            },
        });
    }
};

const submitDeleteBOD = () => {
    bodForm.delete(route('architecture.organization-structure.bod.destroy', selectedBod.value.id), {
        onSuccess: () => {
            isBODDeleteModalOpen.value = false;
        },
    });
};
</script>
