<template>
    <div class="space-y-4">
        <!-- Functional Organization Main Card -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <!-- Header Section containing Filters and Add Button -->
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between border-b border-slate-200 px-4 py-3 dark:border-white/10">
                <!-- Filters & Search -->
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <!-- Search by Query -->
                    <div class="w-full sm:w-48 flex flex-col gap-1.5">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari nama atau SK..."
                                class="w-full rounded-lg border border-slate-300 bg-white pl-8 pr-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                            />
                            <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Company Filter -->
                    <div class="w-full sm:w-40 flex flex-col gap-1.5">
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

                    <!-- SK Filter -->
                    <div class="w-full sm:w-40 flex flex-col gap-1.5">
                        <select
                            v-model="selectedSkId"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                        >
                            <option value="">Semua SK</option>
                            <option v-for="sk in skOrganizations" :key="sk.id" :value="sk.id">
                                {{ sk.sk }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Add Button -->
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-white dark:hover:bg-slate-100 dark:text-slate-950 dark:focus:ring-white dark:focus:ring-offset-[#171717]"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Functional Organization
                    </button>
                </div>
            </div>

            <!-- Table View -->
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-[11px] dark:divide-white/10 table-auto min-w-[900px]">
                    <thead class="bg-slate-50 dark:bg-white/5">
                        <tr>
                            <th class="px-3 py-2.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-10 text-center">No</th>
                            <th class="px-3 py-2.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-36">Company</th>
                            <th class="px-3 py-2.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-44">Nama Organisasi</th>
                            <th class="px-3 py-2.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-40">SK Organisasi</th>
                            <th class="px-3 py-2.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-48">Structure</th>
                            <th class="px-3 py-2.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-56">Anggota</th>
                            <th class="px-3 py-2.5 text-center text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-40">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                        <tr
                            v-for="(row, idx) in filteredRows"
                            :key="row.id"
                            class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                        >
                            <td class="px-3 py-2 text-slate-500 dark:text-slate-400 w-10 text-center align-top">{{ idx + 1 }}</td>
                            <td class="px-3 py-2 text-slate-600 dark:text-slate-300 w-36 font-medium align-top">
                                <div class="truncate max-w-[9rem]" :title="row.company_name">{{ row.company_name || '-' }}</div>
                            </td>
                            <td class="px-3 py-2 text-slate-900 dark:text-white font-medium w-44 align-top">
                                <div class="truncate max-w-[11rem]" :title="row.name">{{ row.name }}</div>
                            </td>
                            <td class="px-3 py-2 text-slate-600 dark:text-slate-300 w-40 align-top">
                                <div class="truncate max-w-[10rem]" :title="row.sk_name">{{ row.sk_name || '-' }}</div>
                            </td>
                            <td class="px-3 py-2 w-48 align-top">
                                <!-- Fungsi: tree structure (root + indented children) -->
                                <div v-if="row.functions && row.functions.length > 0" class="space-y-1">
                                    <template v-for="fun in buildTree(row.functions)" :key="fun.structure_id">
                                        <!-- Root level -->
                                        <div class="flex items-center gap-1">
                                            <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 ring-1 ring-inset ring-blue-200 dark:bg-blue-500/10 dark:text-blue-300 dark:ring-blue-500/20">
                                                {{ fun.name }}
                                            </span>
                                        </div>
                                        <!-- Children (level 1) -->
                                        <div v-if="fun.children && fun.children.length > 0" class="pl-3 space-y-0.5 border-l border-slate-200 dark:border-white/10 ml-1.5">
                                            <div v-for="child in fun.children" :key="child.structure_id" class="flex items-center gap-1">
                                                <span class="text-[9px] text-slate-400 dark:text-slate-500">└</span>
                                                <span class="inline-flex items-center gap-1 rounded-md bg-slate-50 px-1.5 py-0.5 text-[10px] font-medium text-slate-600 ring-1 ring-inset ring-slate-200 dark:bg-white/5 dark:text-slate-300 dark:ring-white/10">
                                                    {{ child.name }}
                                                </span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <span v-else class="text-slate-400 dark:text-slate-500">—</span>
                            </td>
                            <td class="px-3 py-2 w-56 align-top">
                                <!-- Anggota: grouped by grup_function for AD HOC, else plain list -->
                                <div v-if="row.functions && row.functions.length > 0" class="space-y-1.5">
                                    <template v-for="fun in buildTree(row.functions)" :key="fun.structure_id">
                                        <!-- Root structure label -->
                                        <div class="text-[10px] font-bold text-slate-700 dark:text-slate-200">{{ fun.name }}</div>

                                        <!-- === AD HOC: grouped by grup_function === -->
                                        <template v-if="isAdHocStructure(fun.name)">
                                            <div class="pl-2 space-y-1">
                                                <template v-for="grup in groupMembersByGrupFunction(getMembersForStructure(row.members, fun.structure_id))" :key="grup.label">
                                                    <!-- Group header (collapsible) -->
                                                    <div
                                                        @click="toggleGrupCollapse(fun.structure_id, grup.label)"
                                                        class="flex items-center gap-1 cursor-pointer select-none rounded-md px-1.5 py-0.5 transition hover:bg-slate-100 dark:hover:bg-white/5"
                                                    >
                                                        <svg
                                                            class="h-2.5 w-2.5 shrink-0 text-blue-500 dark:text-blue-400 transition-transform duration-200"
                                                            :class="isGrupCollapsed(fun.structure_id, grup.label) ? '-rotate-90' : 'rotate-0'"
                                                            fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                                                        >
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                        <span class="text-[10px] font-semibold text-blue-600 dark:text-blue-400">{{ grup.label }}</span>
                                                        <span class="ml-auto text-[9px] text-slate-400 dark:text-slate-500">{{ grup.members.length }}</span>
                                                    </div>
                                                    <!-- Group members (collapsible body) -->
                                                    <div
                                                        v-show="!isGrupCollapsed(fun.structure_id, grup.label)"
                                                        class="pl-4 space-y-0.5"
                                                    >
                                                        <div
                                                            v-for="member in grup.members"
                                                            :key="member.organization_id"
                                                            class="flex items-start gap-1 text-[10px] text-slate-600 dark:text-slate-300"
                                                        >
                                                            <span class="shrink-0 text-slate-400 dark:text-slate-500 leading-none mt-px">–</span>
                                                            <span class="leading-tight">
                                                                {{ member.name }}
                                                                <span v-if="member.company_name" class="block text-[9px] text-slate-400 dark:text-slate-500 italic font-normal">{{ member.company_name }}</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </template>
                                                <!-- No members at all -->
                                                <div v-if="getMembersForStructure(row.members, fun.structure_id).length === 0" class="text-[9px] italic text-slate-400 dark:text-slate-500 pl-1">—</div>
                                            </div>
                                        </template>

                                        <!-- === Normal structure: flat indented list === -->
                                        <template v-else>
                                            <div v-if="getMembersForStructure(row.members, fun.structure_id).length > 0" class="pl-3 space-y-0.5">
                                                <div
                                                    v-for="member in getMembersForStructure(row.members, fun.structure_id)"
                                                    :key="member.organization_id"
                                                    class="flex items-start gap-1 text-[10px] text-slate-600 dark:text-slate-300"
                                                >
                                                    <span class="shrink-0 text-slate-400 dark:text-slate-500 leading-none mt-px">–</span>
                                                    <span class="leading-tight">
                                                        {{ member.name }}
                                                        <span v-if="member.company_name" class="block text-[9px] text-slate-400 dark:text-slate-500 italic font-normal">{{ member.company_name }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Children structures -->
                                        <template v-if="fun.children && fun.children.length > 0">
                                            <div v-for="child in fun.children" :key="child.structure_id" class="pl-3 space-y-0.5">
                                                <!-- Child label -->
                                                <div class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                                    <span class="text-slate-300 dark:text-slate-600">└</span>
                                                    {{ child.name }}
                                                </div>

                                                <!-- Child: AD HOC grouped -->
                                                <template v-if="isAdHocStructure(child.name)">
                                                    <div class="pl-2 space-y-1">
                                                        <template v-for="grup in groupMembersByGrupFunction(getMembersForStructure(row.members, child.structure_id))" :key="grup.label">
                                                            <div
                                                                @click="toggleGrupCollapse(child.structure_id, grup.label)"
                                                                class="flex items-center gap-1 cursor-pointer select-none rounded-md px-1.5 py-0.5 transition hover:bg-slate-100 dark:hover:bg-white/5"
                                                            >
                                                                <svg
                                                                    class="h-2.5 w-2.5 shrink-0 text-blue-500 dark:text-blue-400 transition-transform duration-200"
                                                                    :class="isGrupCollapsed(child.structure_id, grup.label) ? '-rotate-90' : 'rotate-0'"
                                                                    fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                                                                >
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                                </svg>
                                                                <span class="text-[10px] font-semibold text-blue-600 dark:text-blue-400">{{ grup.label }}</span>
                                                                <span class="ml-auto text-[9px] text-slate-400 dark:text-slate-500">{{ grup.members.length }}</span>
                                                            </div>
                                                            <div v-show="!isGrupCollapsed(child.structure_id, grup.label)" class="pl-4 space-y-0.5">
                                                                <div
                                                                    v-for="member in grup.members"
                                                                    :key="member.organization_id"
                                                                    class="flex items-start gap-1 text-[10px] text-slate-600 dark:text-slate-300"
                                                                >
                                                                    <span class="shrink-0 text-slate-400 dark:text-slate-500 leading-none mt-px">–</span>
                                                                    <span class="leading-tight">
                                                                        {{ member.name }}
                                                                        <span v-if="member.company_name" class="block text-[9px] text-slate-400 dark:text-slate-500 italic font-normal">{{ member.company_name }}</span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </template>
                                                        <div v-if="getMembersForStructure(row.members, child.structure_id).length === 0" class="text-[9px] italic text-slate-400 dark:text-slate-500 pl-1">—</div>
                                                    </div>
                                                </template>

                                                <!-- Child: normal flat list -->
                                                <template v-else>
                                                    <div v-if="getMembersForStructure(row.members, child.structure_id).length > 0" class="pl-4 space-y-0.5">
                                                        <div
                                                            v-for="member in getMembersForStructure(row.members, child.structure_id)"
                                                            :key="member.organization_id"
                                                            class="flex items-start gap-1 text-[10px] text-slate-600 dark:text-slate-300"
                                                        >
                                                            <span class="shrink-0 text-slate-400 dark:text-slate-500 leading-none mt-px">–</span>
                                                            <span class="leading-tight">
                                                                {{ member.name }}
                                                                <span v-if="member.company_name" class="block text-[9px] text-slate-400 dark:text-slate-500 italic font-normal">{{ member.company_name }}</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div v-else class="pl-4 text-[9px] text-slate-400 dark:text-slate-500 italic">—</div>
                                                </template>
                                            </div>
                                        </template>
                                    </template>

                                    <!-- Virtual Structure for Function Members -->
                                    <template v-if="row.assigned_functions && row.assigned_functions.length > 0">
                                        <div class="text-[10px] font-bold text-slate-700 dark:text-slate-200 pt-1.5 border-t border-slate-100 dark:border-white/5">Fungsi</div>
                                        <div class="pl-3 space-y-0.5">
                                            <div
                                                v-for="af in row.assigned_functions"
                                                :key="af.function_id"
                                                class="flex items-start gap-1 text-[10px] text-slate-600 dark:text-slate-300"
                                            >
                                                <span class="shrink-0 text-slate-400 dark:text-slate-500 leading-none mt-px">–</span>
                                                <span class="leading-tight">
                                                    {{ af.name }}
                                                    <span v-if="af.company_name" class="block text-[9px] text-slate-400 dark:text-slate-500 italic font-normal">{{ af.company_name }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div v-else-if="row.assigned_functions && row.assigned_functions.length > 0" class="space-y-1.5">
                                    <div class="text-[10px] font-bold text-slate-700 dark:text-slate-200">Fungsi</div>
                                    <div class="pl-3 space-y-0.5">
                                        <div
                                            v-for="af in row.assigned_functions"
                                            :key="af.function_id"
                                            class="flex items-start gap-1 text-[10px] text-slate-600 dark:text-slate-300"
                                        >
                                            <span class="shrink-0 text-slate-400 dark:text-slate-500 leading-none mt-px">–</span>
                                            <span class="leading-tight">
                                                {{ af.name }}
                                                <span v-if="af.company_name" class="block text-[9px] text-slate-400 dark:text-slate-500 italic font-normal">{{ af.company_name }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <span v-else class="text-slate-400 dark:text-slate-500">—</span>
                            </td>

                            <td class="px-3 py-2 text-center w-40 align-top">
                                <div class="flex flex-col gap-1 items-center justify-center">
                                    <button
                                        @click="openFunctionModal(row)"
                                        class="inline-flex items-center justify-center rounded-full bg-blue-50 hover:bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 dark:text-blue-400 px-3 py-1 text-[10px] font-semibold transition w-full max-w-[96px]"
                                    >
                                        Structure
                                    </button>
                                    <button
                                        @click="openEditModal(row)"
                                        class="inline-flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-white/10 dark:hover:bg-white/20 dark:text-slate-200 px-3 py-1 text-[10px] font-semibold transition w-full max-w-[96px]"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="openDeleteModal(row)"
                                        class="inline-flex items-center justify-center rounded-full bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-500/10 dark:hover:bg-red-500/20 dark:text-red-400 px-3 py-1 text-[10px] font-semibold transition w-full max-w-[96px]"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredRows.length === 0">
                            <td colspan="7" class="px-4 py-12 text-center text-xs text-slate-500 dark:text-slate-400">
                                Organisasi Fungsional Tidak Ditemukan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- Subcomponents Modals -->
    <FunctionManage
        ref="functionManageRef"
        :companies="companies"
        :sk-organizations="skOrganizations"
    />

    <StructureManage
        ref="structureManageRef"
        :functional-organizations="functionalOrganizations"
        @open-member="openMemberModal"
    />

    <MemberManage
        ref="memberManageRef"
        :companies="companies"
        :bods="bods"
        :functions="functions"
        :functional-organizations="functionalOrganizations"
        @close="onMemberModalClose"
    />
</template>

<script setup>
import { ref, computed } from 'vue';
import FunctionManage from './FunctionManage.vue';
import StructureManage from './StructureManage.vue';
import MemberManage from './MemberManage.vue';

const props = defineProps({
    functionalOrganizations: {
        type: Array,
        default: () => [],
    },
    skOrganizations: {
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
    functions: {
        type: Array,
        default: () => [],
    },
});

const searchQuery = ref('');
const selectedCompanyId = ref('');
const selectedSkId = ref('');

// Template refs for child modals
const functionManageRef = ref(null);
const structureManageRef = ref(null);
const memberManageRef = ref(null);

const filteredRows = computed(() => {
    let rows = props.functionalOrganizations;

    // Filter by Company
    if (selectedCompanyId.value) {
        rows = rows.filter(row => String(row.company_id) === String(selectedCompanyId.value));
    }

    // Filter by SK Organization
    if (selectedSkId.value) {
        rows = rows.filter(row => String(row.sk_id) === String(selectedSkId.value));
    }

    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase().trim();
        rows = rows.filter(row => 
            (row.name || '').toLowerCase().includes(query) ||
            (row.company_name || '').toLowerCase().includes(query) ||
            (row.sk_name || '').toLowerCase().includes(query)
        );
    }

    return rows;
});

const getMembersForStructure = (members, structureId) => {
    return (members || []).filter(m => Number(m.structure_id) === Number(structureId));
};

/**
 * Build a tree from a flat functions array using parent_id.
 * Returns only root nodes (parent_id == null), each with a `children` array.
 */
const buildTree = (functions) => {
    if (!functions || functions.length === 0) return [];
    const map = {};
    const roots = [];
    functions.forEach(fn => {
        map[fn.structure_id] = { ...fn, children: [] };
    });
    functions.forEach(fn => {
        if (fn.parent_id && map[fn.parent_id]) {
            map[fn.parent_id].children.push(map[fn.structure_id]);
        } else {
            roots.push(map[fn.structure_id]);
        }
    });
    return roots;
};

// ─── AD HOC Grouping Helpers ─────────────────────────────────────────────────

/**
 * Returns true if the structure name contains "ad hoc" (case-insensitive).
 * Used to trigger grup_function grouping in the Anggota column.
 */
const isAdHocStructure = (name) => {
    return (name || '').toLowerCase().includes('ad hoc');
};

/**
 * Groups an array of members by their `grup_function` field.
 * Members without grup_function are collected under "Lainnya".
 * Returns [{ label, members[] }, ...] sorted by label.
 */
const groupMembersByGrupFunction = (members) => {
    if (!members || members.length === 0) return [];
    const grouped = {};
    members.forEach(member => {
        const key = (member.grup_function || '').trim() || 'Lainnya';
        if (!grouped[key]) grouped[key] = [];
        grouped[key].push(member);
    });
    // Sort: prioritize Holding > Subholding > APFS > others > Lainnya
    const ORDER = ['Holding', 'Subholding', 'APFS'];
    return Object.keys(grouped)
        .sort((a, b) => {
            const ai = ORDER.indexOf(a);
            const bi = ORDER.indexOf(b);
            if (a === 'Lainnya') return 1;
            if (b === 'Lainnya') return -1;
            if (ai === -1 && bi === -1) return a.localeCompare(b);
            if (ai === -1) return 1;
            if (bi === -1) return -1;
            return ai - bi;
        })
        .map(label => ({ label, members: grouped[label] }));
};

/**
 * Reactive map: key = `${structureId}__${grupLabel}`, value = true (collapsed).
 * All groups start expanded (no key = expanded).
 */
const collapsedGrups = ref({});

const toggleGrupCollapse = (structureId, grupLabel) => {
    const key = `${structureId}__${grupLabel}`;
    collapsedGrups.value = {
        ...collapsedGrups.value,
        [key]: !collapsedGrups.value[key],
    };
};

const isGrupCollapsed = (structureId, grupLabel) => {
    const key = `${structureId}__${grupLabel}`;
    return !!collapsedGrups.value[key];
};

// Modal action triggers
const openCreateModal = () => {
    functionManageRef.value?.openCreate();
};

const openEditModal = (row) => {
    functionManageRef.value?.openEdit(row);
};

const openDeleteModal = (row) => {
    functionManageRef.value?.openDelete(row);
};

const openFunctionModal = (row) => {
    structureManageRef.value?.open(row);
};

const openMemberModal = ({ functional, structure }) => {
    memberManageRef.value?.open(functional, structure);
};

const onMemberModalClose = (functional) => {
    structureManageRef.value?.open(functional);
};
</script>