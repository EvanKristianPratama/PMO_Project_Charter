<template>
    <div class="space-y-4">
        <!-- Search & Company Filters Section -->
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <!-- Company Filter -->
                <div class="w-full sm:w-72 flex flex-col gap-1.5">
                    <select
                        v-model="selectedCompanyId"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    >
                        <option value="">Semua Company</option>
                        <option v-for="company in companies" :key="company.id" :value="company.id">
                            {{ company.name }}
                        </option>
                    </select>
                </div>
            </div>
        </section>

        <!-- BOD Tree View of Selected Company -->
        <div v-if="selectedCompany" class="space-y-4">
            <BodThreeView
                :companies="[selectedCompany]"
                :bods="bods"
                :is-root="true"
            />
            <EmployeeStructure
                :companies="companies"
                :bods="bods"
                :initial-company-id="selectedCompanyId"
                :sk-organizations="skOrganizations"
            />
        </div>

        <!-- Organization Structure Main Card -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <!-- Header Section containing: Group Filter, Organization Filter, Add Button, Table/Tree Toggle -->
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between border-b border-slate-200 px-4 py-3 dark:border-white/10">
                <!-- Group & Parent Filters -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Group Filter -->
                    <div class="flex flex-col gap-1.5">
                        <select
                            v-model="selectedGroubName"
                            class="w-full sm:w-32 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                        >
                            <option value="">Semua Group</option>
                            <option v-for="groubName in groubNames" :key="groubName" :value="groubName">
                                {{ groubName }}
                            </option>
                        </select>
                    </div>

                    <!-- Parent Filter -->
                    <div v-if="viewMode === 'table'" class="flex flex-col gap-1.5">
                        <select
                            v-model="parentFilterId"
                            class="w-full sm:w-40 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                        >
                            <option value="">Semua Organisasi</option>
                            <option v-for="org in parentFilterOptions" :key="org.organization_id" :value="org.organization_id">
                                {{ getLevelPrefix(org) }}{{ org.organization_name }} ({{ org.code }})
                            </option>
                        </select>
                    </div>

                    <!-- Search by Query -->
                    <div v-if="viewMode === 'table'" class="w-full sm:w-48 flex flex-col gap-1.5">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari kode, nama, jabatan, pejabat..."
                                class="w-full rounded-lg border border-slate-300 bg-white pl-8 pr-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                            />
                            <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Actions & Toggle -->
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-white dark:hover:bg-slate-100 dark:text-slate-950 dark:focus:ring-white dark:focus:ring-offset-[#171717]"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Organization
                    </button>

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
                </div>
            </div>

            <!-- Table View -->
            <div v-if="viewMode === 'table'" class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-[11px] dark:divide-white/10 table-fixed">
                    <thead class="bg-slate-50 dark:bg-white/5">
                        <tr>
                            <th class="px-2 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-8">No</th>
                            <th class="px-2 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-20">Company</th>
                            <th class="px-2 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-16">Group</th>
                            <th class="px-2 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-10">ID</th>
                            <th class="px-2 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-20">Code</th>
                            <th class="px-2 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-28">Organization</th>
                            <th class="px-2 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-28">Parent</th>
                            <th class="px-2 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-14">Alias</th>
                            <th class="px-2 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-28">Jabatan</th>
                            <th class="px-2 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-24">Pejabat (SDM)</th>
                            <th class="px-2 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-24">Pejabat (Original)</th>
                            <th class="px-2 py-2 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-24">SK</th>
                            <th class="px-2 py-2 text-center text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                        <tr
                            v-for="(organizationStructureRow, index) in filteredRows"
                            :key="organizationStructureRow.organization_id"
                            class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                        >
                            <td class="px-2 py-1.5 text-slate-500 dark:text-slate-400 w-8 text-center">{{ index + 1 }}</td>
                            <td class="px-2 py-1.5 text-slate-600 dark:text-slate-300 w-20" :title="displayValue(organizationStructureRow.company_name)">
                                <div class="break-words whitespace-normal font-medium">
                                    {{ displayValue(organizationStructureRow.company_name) }}
                                </div>
                            </td>
                            <td class="px-2 py-1.5 text-slate-600 dark:text-slate-300 w-16" :title="displayValue(organizationStructureRow.groub_name)">
                                <div class="break-words whitespace-normal">
                                    {{ displayValue(organizationStructureRow.groub_name) }}
                                </div>
                            </td>
                            <td class="px-2 py-1.5 text-slate-500 dark:text-slate-400 font-mono text-[10px] w-10" :title="displayValue(organizationStructureRow.organization_id)">
                                <div class="break-all whitespace-normal">
                                    {{ displayValue(organizationStructureRow.organization_id) }}
                                </div>
                            </td>
                            <td class="px-2 py-1.5 text-slate-500 dark:text-slate-400 font-mono text-[10px] w-20" :title="displayValue(organizationStructureRow.code)">
                                <div class="break-all whitespace-normal">
                                    {{ displayValue(organizationStructureRow.code) }}
                                </div>
                            </td>
                            <td class="px-2 py-1.5 text-slate-600 dark:text-slate-300 font-medium w-28" :title="displayValue(organizationStructureRow.organization_name)">
                                <div class="break-words whitespace-normal">
                                    {{ displayValue(organizationStructureRow.organization_name) }}
                                </div>
                            </td>
                            <td class="px-2 py-1.5 text-slate-500 dark:text-slate-400 text-[10px] w-28" :title="getParentName(organizationStructureRow.parent_id)">
                                <div class="break-words whitespace-normal">
                                    {{ getParentName(organizationStructureRow.parent_id) }}
                                </div>
                            </td>
                            <td class="px-2 py-1.5 text-slate-600 dark:text-slate-300 w-14" :title="displayValue(organizationStructureRow.alias)">
                                <div class="break-words whitespace-normal">
                                    {{ displayValue(organizationStructureRow.alias) }}
                                </div>
                            </td>
                            <td class="px-2 py-1.5 text-slate-600 dark:text-slate-300 w-28" :title="displayValue(organizationStructureRow.jabatan)">
                                <div class="break-words whitespace-normal">
                                    {{ displayValue(organizationStructureRow.jabatan) }}
                                </div>
                            </td>
                            <td class="px-2 py-1.5 text-slate-600 dark:text-slate-300 w-24" :title="displayValue(organizationStructureRow.pejabat)">
                                <div class="break-words whitespace-normal">
                                    {{ displayValue(organizationStructureRow.pejabat) }}
                                </div>
                            </td>
                            <td class="px-2 py-1.5 text-slate-600 dark:text-slate-300 w-24" :title="displayValue(organizationStructureRow.pejabat_original)">
                                <div class="break-words whitespace-normal">
                                    {{ displayValue(organizationStructureRow.pejabat_original) }}
                                </div>
                            </td>
                            <td class="px-2 py-1.5 text-slate-600 dark:text-slate-300 w-24" :title="displayValue(organizationStructureRow.sk)">
                                <div class="break-words whitespace-normal">
                                    {{ displayValue(organizationStructureRow.sk) }}
                                </div>
                            </td>
                            <td class="px-2 py-1.5 text-center w-24">
                                <div class="flex flex-col gap-1 items-center justify-center">
                                    <button
                                        @click="openEditModal(organizationStructureRow)"
                                        class="inline-flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-white/10 dark:hover:bg-white/20 dark:text-slate-200 px-2.5 py-0.5 text-[10px] font-semibold transition w-full max-w-[56px]"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="openDeleteModal(organizationStructureRow)"
                                        class="inline-flex items-center justify-center rounded-full bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-500/10 dark:hover:bg-red-500/20 dark:text-red-400 px-2.5 py-0.5 text-[10px] font-semibold transition w-full max-w-[56px]"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredRows.length === 0">
                            <td colspan="13" class="px-2 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                                Organization Not Available
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tree View -->
            <div v-if="viewMode === 'tree'" class="px-4 py-4">
                <ThreeView
                    :organization-structure-rows="filteredByGroupRows"
                />
            </div>
        </section>
    </div>

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

            <!-- Company Option Select -->
            <div class="flex flex-col gap-1.5">
                <label for="form_company_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Company</label>
                <select
                    id="form_company_id"
                    v-model="selectedFormCompanyId"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    required
                >
                    <option value="" disabled>Pilih Company...</option>
                    <option v-for="company in companies" :key="company.id" :value="company.id">
                        {{ company.name }}
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

            <!-- Group Type Option Select -->
            <div class="flex flex-col gap-1.5">
                <label for="form_group_type" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Group Organisasi Baru</label>
                <select
                    id="form_group_type"
                    v-model="selectedFormGroupType"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    required
                >
                    <option value="" disabled>Pilih Tipe Group...</option>
                    <option value="Holding">Holding</option>
                    <option value="Sub Holding">Sub Holding</option>
                    <option value="All">All</option>
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
import ThreeView from '@/Components/Architecture/Organization/ThreeView.vue';
import BodThreeView from '@/Components/Architecture/Organization/BodThreeView.vue';
import EmployeeStructure from '@/Components/Architecture/Organization/EmployeeStructure.vue';

const props = defineProps({
    organizationStructureRows: {
        type: Array,
        default: () => [],
    },
    groubOptions: {
        type: Array,
        default: () => [],
    },
    companies: {
        type: Array,
        default: () => [],
    },
    bods: {
        type: Array,
        default: () => [],
    },
    skOrganizations: {
        type: Array,
        default: () => [],
    },
});

const displayValue = (value) => value ?? '-';

const getParentName = (parentId) => {
    if (!parentId) return '—';
    const parent = props.organizationStructureRows.find(
        (org) => String(org.organization_id) === String(parentId)
    );
    return parent ? parent.organization_name : '—';
};

const viewMode = ref('table'); // 'table' or 'tree'
const selectedGroubName = ref('');
const selectedCompanyId = ref('');
const parentFilterId = ref('');
const parentGroupFilterId = ref('');
const searchQuery = ref('');

const selectedFormCompanyId = ref('');
const selectedFormGroupType = ref('');

const groubNames = computed(() => {
    return [...new Set(
        props.organizationStructureRows
            .map((organizationStructureRow) => organizationStructureRow.groub_name)
            .filter(Boolean),
    )].sort((left, right) => left.localeCompare(right));
});

const matchesGroubFilter = (groubName) => {
    if (!selectedGroubName.value) {
        return true;
    }
    return (groubName ?? '') === selectedGroubName.value;
};

const matchesCompanyFilter = (companyId) => {
    if (!selectedCompanyId.value) {
        return true;
    }
    return Number(companyId) === Number(selectedCompanyId.value);
};

const filteredByGroupRows = computed(() => {
    return props.organizationStructureRows.filter((row) => {
        return matchesGroubFilter(row.groub_name) && matchesCompanyFilter(row.company_id);
    });
});

const selectedCompany = computed(() => {
    if (!selectedCompanyId.value) return null;
    return props.companies.find(c => String(c.id) === String(selectedCompanyId.value));
});

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
    const orgs = filteredByGroupRows.value;
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
    let rows = filteredByGroupRows.value;

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
                (row.pejabat_original || '').toLowerCase().includes(query) ||
                (row.sk || '').toLowerCase().includes(query)
            );
        });
    }

    return rows;
});

watch(filteredByGroupRows, (newRows) => {
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

// Watcher to find and set the appropriate group_id based on company and group type
watch([selectedFormCompanyId, selectedFormGroupType], ([newCompId, newGrpType]) => {
    if (newCompId && newGrpType) {
        const found = props.groubOptions.find(
            opt => String(opt.company_id) === String(newCompId) && String(opt.group_name).toLowerCase() === String(newGrpType).toLowerCase()
        );
        if (found) {
            form.groub_id = found.id;
            parentGroupFilterId.value = found.id;
        } else {
            form.groub_id = '';
        }
    } else {
        form.groub_id = '';
    }
});



const openCreateModal = () => {
    modalMode.value = 'create';
    form.clearErrors();
    form.reset();
    parentGroupFilterId.value = '';
    selectedFormCompanyId.value = '';
    selectedFormGroupType.value = '';
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
    form.sk = org.sk || '';

    // Pre-select company and group type based on the loaded groub_id
    const currentGroup = props.groubOptions.find(opt => String(opt.id) === String(org.groub_id));
    if (currentGroup) {
        selectedFormCompanyId.value = String(currentGroup.company_id);
        selectedFormGroupType.value = currentGroup.group_name;
    } else {
        selectedFormCompanyId.value = '';
        selectedFormGroupType.value = '';
    }

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
        form.post(route('business-process.organization-structure.store'), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('business-process.organization-structure.update', selectedOrg.value.organization_id), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const submitDelete = () => {
    form.delete(route('business-process.organization-structure.destroy', selectedOrg.value.organization_id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
};
</script>
