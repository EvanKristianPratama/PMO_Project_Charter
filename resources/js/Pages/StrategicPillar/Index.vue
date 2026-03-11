<template>
    <UserLayout title="Strategic Pillars">
        <div class="animate-fade-in">
            <div class="mb-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Strategic Pillars & Themes</h2>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Filter Dropdown -->
                        <div class="flex w-full flex-col items-start gap-1.5 sm:w-auto sm:flex-row sm:items-center sm:gap-2">
                            <label class="text-[10px] font-medium text-slate-700 dark:text-slate-300 sm:whitespace-nowrap">
                                Pillar:
                            </label>
                            <select
                                v-model="selectedGoalId"
                                @change="applyFilter"
                                class="w-full rounded border border-slate-300 bg-white px-2 py-1 text-[10px] text-slate-900 focus:border-transparent focus:ring-1 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white sm:min-w-[150px] sm:w-auto"
                            >
                                <option :value="null">All Pillars</option>
                                <option v-for="goal in allGoals" :key="goal.id" :value="goal.id">
                                    {{ goal.code }} - {{ goal.title }}
                                </option>
                            </select>
                        </div>

                        <!-- Organization Filter -->
                        <div class="flex w-full flex-col items-start gap-1.5 sm:w-auto sm:flex-row sm:items-center sm:gap-2">
                            <label class="text-[10px] font-medium text-slate-700 dark:text-slate-300 sm:whitespace-nowrap">
                                Owner:
                            </label>
                            <select
                                v-model="selectedOrgId"
                                @change="applyFilter"
                                class="w-full rounded border border-slate-300 bg-white px-2 py-1 text-[10px] text-slate-900 focus:border-transparent focus:ring-1 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white sm:min-w-[150px] sm:w-auto"
                            >
                                <option :value="null">All Owners</option>
                                <option v-for="org in allOrganizations" :key="org.id" :value="org.id">
                                    {{ org.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Matrix Button -->
                        <button
                            @click="showMatrixModal = true"
                            class="inline-flex items-center rounded px-2.5 py-1.5 text-xs font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition-colors shadow-sm"
                        >
                            <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 3v18M14 3v18"/></svg>
                            Matrix
                        </button>

                        <!-- Add Tagging Button -->
                        <button
                            @click="showTaggingModal = true"
                            class="inline-flex items-center rounded px-2.5 py-1.5 text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition-colors shadow-sm"
                        >
                            <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Tagging
                        </button>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="flex flex-wrap items-center gap-3 mb-3 px-1">
                <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Legend:</span>
                <div class="flex items-center gap-1">
                    <span class="inline-block w-3 h-3 rounded-sm bg-slate-300 border border-slate-400 dark:bg-slate-700 dark:border-slate-500"></span>
                    <span class="text-[10px] text-slate-600 dark:text-slate-400">Drafting</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="inline-block w-3 h-3 rounded-sm bg-blue-400/30 border border-blue-400/50"></span>
                    <span class="text-[10px] text-slate-600 dark:text-slate-400">Propose</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="inline-block w-3 h-3 rounded-sm bg-amber-400/30 border border-amber-400/50"></span>
                    <span class="text-[10px] text-slate-600 dark:text-slate-400">Review</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="inline-block w-3 h-3 rounded-sm bg-emerald-400/30 border border-emerald-400/50"></span>
                    <span class="text-[10px] text-slate-600 dark:text-slate-400">Approve</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="inline-block w-3 h-3 rounded-sm bg-violet-400/30 border border-violet-400/50"></span>
                    <span class="text-[10px] text-slate-600 dark:text-slate-400">Baseline</span>
                </div>
            </div>

            <!-- Goals Table -->
            <div class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm dark:border-slate-600 dark:bg-[#1a1a1a]">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] border-collapse">
                        <thead>
                            <tr class="bg-slate-100 dark:bg-slate-800 border-b-2 border-slate-300 dark:border-slate-600">
                                <th class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 border-r border-slate-300 dark:border-slate-600 w-12">
                                    Code
                                </th>
                                <th class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 border-r border-slate-300 dark:border-slate-600 w-1/5">
                                    Strategic Pillar Title
                                </th>
                                <th class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 border-r border-slate-300 dark:border-slate-600 w-[250px]">
                                    Themes
                                </th>
                                <th class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                                    Digital Initiatives
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="pillar in filteredPillars" :key="pillar.id" class="border-b border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-2 py-2 border-r border-slate-300 dark:border-slate-600 text-center align-top">
                                    <span class="text-[11px] font-bold text-slate-900 dark:text-white">
                                        {{ pillar.code }}
                                    </span>
                                </td>
                                <td class="px-2 py-2 border-r border-slate-300 dark:border-slate-600 text-center align-top">
                                    <div class="text-[11px] font-medium text-slate-900 dark:text-white">
                                        {{ pillar.title }}
                                    </div>

                                    <div v-if="getGoalInitiatives(pillar.code).length > 0" class="mt-3 flex flex-col gap-1.5 items-center w-full">
                                        <div class="text-[9px] font-semibold tracking-wider text-slate-400 dark:text-slate-500 uppercase">Mapped directly to Pillar</div>
                                        <div class="flex flex-wrap justify-center gap-1 w-full">
                                            <div
                                                v-for="tag in getGoalInitiatives(pillar.code)"
                                                :key="tag.id"
                                                @click="navigateToScope(tag)"
                                                :class="['inline-flex items-center gap-1 rounded px-1.5 py-0.5 text-[9px] font-medium border w-[220px] cursor-pointer hover:brightness-95 transition-all', getStatusColor(tag)]"
                                            >
                                                <div class="flex items-center gap-1 overflow-hidden flex-1 min-w-0" :title="tag.initiative ? `${tag.initiative.code} - ${tag.initiative.name}` : ''">
                                                    <span class="font-bold flex-shrink-0">{{ tag.initiative ? tag.initiative.code : '?' }}</span>
                                                    <span class="truncate opacity-90">{{ tag.initiative ? tag.initiative.name : '' }}</span>
                                                    <span v-if="tag.initiative?.organization?.name" class="shrink-0 opacity-60 text-[8px]">· {{ tag.initiative.organization.name }}</span>
                                                </div>
                                                <button @click.stop="confirmDelete(tag)" class="flex-shrink-0 opacity-50 hover:opacity-100 hover:text-red-600 transition-opacity" title="Remove">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="2" class="p-0 align-top">
                                    <!-- Themes & Theme-Level Initiatives Table -->
                                    <div v-if="pillar.themes && pillar.themes.length > 0" class="w-full h-full">
                                        <table class="w-full h-full border-collapse">
                                            <tbody>
                                                <tr v-for="theme in pillar.themes" :key="theme.id" class="border-b border-slate-300 dark:border-slate-600 last:border-b-0">
                                                    <!-- Theme Name -->
                                                    <td class="px-2 py-2 text-[11px] text-slate-700 dark:text-slate-200 border-r border-slate-300 dark:border-slate-600 w-[250px] align-top">
                                                        <div class="flex items-start justify-between gap-1">
                                                            <span><span class="font-semibold mr-1">{{ theme.theme_number }}.</span>{{ theme.name }}</span>
                                                            <span
                                                                v-if="getThemeInitiatives(theme.id).length > 0"
                                                                class="flex-shrink-0 inline-flex items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[9px] font-semibold min-w-[18px] h-[18px] px-1"
                                                                :title="`${getThemeInitiatives(theme.id).length} initiatives`"
                                                            >
                                                                {{ getThemeInitiatives(theme.id).length }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <!-- Theme-Level Initiatives -->
                                                    <td class="px-2 py-2 align-top">
                                                        <div v-if="getThemeInitiatives(theme.id).length > 0" class="grid grid-cols-2 gap-1">
                                                            <div
                                                                v-for="tag in getThemeInitiatives(theme.id)"
                                                                :key="tag.id"
                                                                @click="navigateToScope(tag)"
                                                                :class="['inline-flex items-center gap-1 rounded px-1.5 py-0.5 text-[9px] font-medium border w-full cursor-pointer hover:brightness-95 transition-all', getStatusColor(tag)]"
                                                            >
                                                                <div class="flex items-center gap-1 overflow-hidden flex-1 min-w-0" :title="tag.initiative ? `${tag.initiative.code} - ${tag.initiative.name}` : ''">
                                                                    <span class="font-bold flex-shrink-0">{{ tag.initiative ? tag.initiative.code : '?' }}</span>
                                                                    <span class="truncate opacity-90">{{ tag.initiative ? tag.initiative.name : '' }}</span>
                                                                    <span v-if="tag.initiative?.organization?.name" class="shrink-0 opacity-60 text-[8px]">· {{ tag.initiative.organization.name }}</span>
                                                                </div>
                                                                <button @click.stop="confirmDelete(tag)" class="flex-shrink-0 opacity-50 hover:opacity-100 hover:text-red-600 transition-opacity" title="Remove">
                                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <span v-else class="text-[10px] italic text-slate-400 dark:text-slate-500">—</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div v-else class="py-3 grid h-full" style="grid-template-columns: 250px 1fr">
                                        <div class="border-r border-slate-300 dark:border-slate-600 flex items-center h-full px-2">
                                            <span class="text-xs text-slate-400 dark:text-slate-500 italic">No themes</span>
                                        </div>
                                        <div class="flex items-center h-full px-2">
                                            <span class="text-xs text-slate-400 dark:text-slate-500 italic">—</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!filteredPillars || filteredPillars.length === 0" class="text-center py-16 bg-white dark:bg-[#1a1a1a] rounded-xl border border-slate-200 dark:border-white/5 mt-4">
                <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">No strategic pillars found</h3>
                <p class="text-slate-500 dark:text-slate-400">
                    {{ selectedGoalId ? 'No data for the selected pillar.' : 'Start by adding your strategic pillars and themes.' }}
                </p>
            </div>

            <!-- Tagging Modal -->
            <InitiativeTaggingModal
                :show="showTaggingModal"
                :initiatives="allInitiatives"
                :goals="strategicPillars"
                @close="showTaggingModal = false"
            />

            <!-- Delete Confirmation -->
            <ConfirmationModal
                :show="showDeleteModal"
                title="Hapus Initiative Tagging"
                message="Apakah Anda yakin ingin menghapus mapping / tagging ini?"
                confirm-text="Ya, Hapus"
                cancel-text="Batal"
                type="danger"
                :loading="deleteForm.processing"
                @close="showDeleteModal = false"
                @confirm="executeDelete"
            />

            <!-- Matrix View Modal -->
            <Teleport to="body">
                <div v-if="showMatrixModal" class="fixed inset-0 z-50 flex flex-col bg-black/60 backdrop-blur-sm" @click.self="showMatrixModal = false">
                    <div class="flex-1 flex flex-col overflow-hidden m-4 rounded-xl bg-white dark:bg-[#1a1a1a] shadow-2xl border border-slate-200 dark:border-slate-700">
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-slate-700 flex-shrink-0">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Initiative × Theme Matrix</h3>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">■ = Initiative mapped to theme</p>
                            </div>
                            <button @click="showMatrixModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <!-- Matrix Table -->
                        <div class="flex-1 overflow-auto p-2">
                            <table class="border-collapse text-[9px]" style="min-width: max-content">
                                <thead>
                                    <!-- Group header row -->
                                    <tr class="bg-slate-200 dark:bg-slate-700 sticky top-0 z-10">
                                        <th rowspan="2" class="sticky left-0 z-20 bg-slate-200 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 px-2 py-1.5 text-left font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap min-w-[200px]">
                                            Initiative
                                        </th>
                                        <th
                                            v-for="group in themeGroupsWithCount"
                                            :key="group.label"
                                            :colspan="group.count"
                                            class="border border-slate-300 dark:border-slate-600 px-2 py-1 text-center text-[8px] font-bold text-slate-700 dark:text-slate-200 whitespace-nowrap"
                                        >
                                            {{ group.label }}
                                        </th>
                                    </tr>
                                    <!-- Theme number row -->
                                    <tr class="bg-slate-100 dark:bg-slate-800 sticky top-[29px] z-10">
                                        <th
                                            v-for="theme in allThemes"
                                            :key="theme.id"
                                            class="border border-slate-300 dark:border-slate-600 px-1 py-1 text-center font-semibold text-slate-600 dark:text-slate-400 whitespace-nowrap w-[44px] min-w-[44px]"
                                            :title="`${theme.theme_number}. ${theme.name}`"
                                        >
                                            <div class="text-[8px] font-bold">T{{ theme.theme_number }}</div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(initiative, idx) in matrixInitiatives"
                                        :key="initiative.id"
                                        :class="idx % 2 === 0 ? 'bg-white dark:bg-[#1a1a1a]' : 'bg-slate-50 dark:bg-slate-800/30'"
                                        class="hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-colors"
                                    >
                                        <td class="sticky left-0 z-10 border border-slate-300 dark:border-slate-600 px-2 py-1 font-medium text-slate-800 dark:text-slate-200 whitespace-nowrap"
                                            :class="idx % 2 === 0 ? 'bg-white dark:bg-[#1a1a1a]' : 'bg-slate-50 dark:bg-slate-800/40'">
                                            <span class="text-slate-400 dark:text-slate-500 mr-1.5">{{ idx + 1 }}.</span>
                                            <span class="font-bold mr-1">{{ initiative.code }}</span>
                                            <span class="text-[9px]">{{ initiative.name }}</span>
                                        </td>
                                        <td
                                            v-for="theme in allThemes"
                                            :key="theme.id"
                                            class="border border-slate-300 dark:border-slate-600 text-center py-1 w-[44px]"
                                        >
                                            <span
                                                v-if="isTagged(initiative.id, theme.id)"
                                                class="inline-block w-3.5 h-3.5 rounded-sm bg-indigo-500 dark:bg-indigo-400"
                                                :title="`${initiative.code} → ${theme.theme_number}. ${theme.name}`"
                                            ></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Modal Footer -->
                        <div class="px-4 py-2 border-t border-slate-200 dark:border-slate-700 flex-shrink-0 flex items-center gap-4 text-[10px] text-slate-500 dark:text-slate-400">
                            <span>{{ matrixInitiatives.length }} digital initiatives</span>
                            <span>{{ allThemes.length }} themes</span>
                            <span>{{ matrixTaggedCount }} mappings</span>
                        </div>
                    </div>
                </div>
            </Teleport>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import InitiativeTaggingModal from '@/Components/StrategicPillar/InitiativeTaggingModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    strategicPillars: {
        type: Array,
        default: () => [],
    },
    allGoals: {
        type: Array,
        default: () => [],
    },
    taggings: {
        type: Array,
        default: () => [],
    },
    allInitiatives: {
        type: Array,
        default: () => [],
    },
    allThemes: {
        type: Array,
        default: () => [],
    },
    matrixInitiatives: {
        type: Array,
        default: () => [],
    },
    allOrganizations: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

// --- Filters (client-side only, no server request) ---
const selectedGoalId = ref(props.filters.goal_id ? Number(props.filters.goal_id) : null);
const selectedOrgId = ref(props.filters.org_id ? Number(props.filters.org_id) : null);

const applyFilter = () => {
    // Filtering dilakukan client-side, tidak perlu request ke server
};

// --- Filtered data ---
const filteredPillars = computed(() => {
    if (!selectedGoalId.value) return props.strategicPillars;
    return props.strategicPillars.filter(p => p.id === selectedGoalId.value);
});

// --- Navigation ---
const navigateToScope = (tag) => {
    const initiative = tag.initiative;
    if (!initiative) return;

    // 1. Check if it's an IT Initiative (has mapped projects)
    if (initiative.mapped_projects && initiative.mapped_projects.length > 0) {
        const projectId = initiative.mapped_projects[0].id;
        router.get(`/it-initiatives/${projectId}`);
        return;
    }

    // 2. Check if it's a Digital Initiative (linked to Scope Charter / Compendium)
    if (initiative.map_sc && initiative.map_sc.length > 0) {
        const scId = initiative.map_sc[0].sc_id;
        router.get(`/program-planning/program-definition/digital-initiatives/compendium/${scId}/edit`);
        return;
    }
};

// --- Initiative Computed Groupings ---
const sortByCode = (tags) => [...tags].sort((a, b) => {
    const codeA = Number(a.initiative?.code ?? 99999);
    const codeB = Number(b.initiative?.code ?? 99999);
    return codeA - codeB;
});

const getGoalInitiatives = (pillarCode) => {
    if (!props.taggings) return [];
    return sortByCode(props.taggings.filter(tag => {
        if (tag.goal !== pillarCode || tag.themes_id) return false;
        if (selectedOrgId.value && tag.initiative?.business_unit != selectedOrgId.value) return false;
        return true;
    }));
};

const getThemeInitiatives = (themeId) => {
    if (!props.taggings) return [];
    return sortByCode(props.taggings.filter(tag => {
        if (tag.themes_id !== themeId) return false;
        if (selectedOrgId.value && tag.initiative?.business_unit != selectedOrgId.value) return false;
        return true;
    }));
};

// --- Status Styling ---
const getStatusColor = (tag) => {
    const initiative = tag?.initiative;
    if (!initiative) return 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600';

    // Get status from latestStatus relation OR fallback to status column
    let statusVal = initiative.latest_status?.status || initiative.status;
    const rawStatus = String(statusVal || '').toLowerCase().trim();
    
    // Status ID Mappings based on PMO Planning Standard (User explicit request)
    // 4: Approved/Hijau
    if (rawStatus === '4' || rawStatus.includes('approved') || rawStatus === 'approved') {
        return 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20';
    }
    
    // 5: Baseline/Ungu
    if (rawStatus === '5' || rawStatus.includes('baseline') || rawStatus === 'baseline') {
        return 'bg-violet-500/10 text-violet-700 border-violet-500/20 dark:bg-violet-500/10 dark:text-violet-400 dark:border-violet-500/20';
    }

    // 0/1: Drafting (Gray)
    if (rawStatus === '0' || rawStatus === '1' || rawStatus.includes('draft') || rawStatus.includes('not start')) {
        return 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600';
    }
    
    // 2: Propose (Blue)
    if (rawStatus === '2' || rawStatus.includes('propose')) {
        return 'bg-blue-500/10 text-blue-700 border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20';
    }
    
    // 3: Review (Orange/Amber)
    if (rawStatus === '3' || rawStatus.includes('review')) {
        return 'bg-amber-500/10 text-amber-700 border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20';
    }
    
    // Fallback String-based logic for other keywords
    if (rawStatus.includes('progress') || rawStatus.includes('active') || rawStatus.includes('implement')) {
        return 'bg-blue-500/10 text-blue-700 border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20';
    }
    
    if (rawStatus.includes('cancel') || rawStatus.includes('reject') || rawStatus.includes('drop') || rawStatus.includes('hold')) {
        return 'bg-rose-500/10 text-rose-700 border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20';
    }
    
    // Default fallback
    return 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600';
};

// --- Initiative Tagging ---
const showTaggingModal = ref(false);
const showDeleteModal = ref(false);
const showMatrixModal = ref(false);
const pendingDeleteTag = ref(null);
const deleteForm = useForm({});

// --- Matrix helpers ---
const THEME_GROUPS = [
    { label: 'A – Maximizing Legacy Business',        from: 1,  to: 4  },
    { label: 'B – Building Low Carbon Business',      from: 5,  to: 8  },
    { label: 'C – Holding Inputs / Enablers Required', from: 9, to: 12 },
    { label: 'D – Sustainability',                    from: 13, to: 16 },
];

const themeGroupsWithCount = computed(() =>
    THEME_GROUPS.map(g => ({
        ...g,
        count: props.allThemes.filter(t => t.theme_number >= g.from && t.theme_number <= g.to).length,
    })).filter(g => g.count > 0)
);

const taggedSet = computed(() => {
    const set = new Set();
    props.taggings.forEach(tag => {
        if (tag.initiative?.id && tag.themes_id) {
            set.add(`${tag.initiative.id}-${tag.themes_id}`);
        }
    });
    return set;
});

const isTagged = (initiativeId, themeId) => taggedSet.value.has(`${initiativeId}-${themeId}`);

const matrixTaggedCount = computed(() => taggedSet.value.size);

// ESC key closes matrix modal
const handleKeydown = (e) => {
    if (e.key === 'Escape' && showMatrixModal.value) showMatrixModal.value = false;
};
onMounted(() => window.addEventListener('keydown', handleKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', handleKeydown));

const confirmDelete = (tag) => {
    pendingDeleteTag.value = tag;
    showDeleteModal.value = true;
};

const executeDelete = () => {
    if (!pendingDeleteTag.value) return;
    deleteForm.delete(`/strategic-pillars/tagging/${pendingDeleteTag.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            pendingDeleteTag.value = null;
        },
    });
};
</script>
