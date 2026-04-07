<template>
    <div class="space-y-3">
        <!-- Tabel 1: Code & Status Implementasi -->
        <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-white/10">
            <table class="w-full table-fixed divide-y divide-x divide-slate-200 text-[11px] dark:divide-white/10">
                <colgroup>
                    <col :class="codeLabel !== 'Code' ? 'w-[12%]' : 'w-[8%]'">
                    <col class="w-[18%]">
                    <col class="w-[10%]">
                    <col :class="codeLabel !== 'Code' ? 'w-[12%]' : 'w-[14%]'">
                    <col :class="codeLabel !== 'Code' ? 'w-[35%]' : 'w-[37%]'">
                    <col class="w-[13%]">
                </colgroup>
                <thead v-if="showHeader" class="bg-slate-50 dark:bg-white/[0.03]">
                    <tr class="divide-x divide-slate-200 dark:divide-white/10">
                        <th
                            class="px-1 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            {{ codeLabel }}</th>
                        <th
                            class="px-1 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Project Name</th>
                        <th
                            class="px-1 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Progres Status</th>
                        <th
                            class="px-1 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Periode Status</th>
                        <th
                            class="px-1 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Notes</th>
                        <th
                            class="px-1 py-1.5 text-center text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/10 dark:bg-[#1a1a1a]">
                    <template v-for="(proj, projIndex) in projectList" :key="`proj-${proj?.id ?? projIndex}`">
                        <tr v-for="(log, logIndex) in getStatusRows(proj)" :key="`t1-${proj?.id ?? projIndex}-${log?.id ?? logIndex}`" class="divide-x divide-slate-200 hover:bg-slate-50 dark:divide-white/10 dark:hover:bg-white/5 transition-colors">
                            <!-- Code: rowspan on first row -->
                            <td v-if="logIndex === 0"
                                :rowspan="getStatusRowCount(proj)"
                                class="px-2 py-3 text-[11px] font-semibold text-slate-700 dark:text-slate-200 align-middle text-center border-b border-slate-200 dark:border-white/10">
                                {{ proj.code || '-' }}
                            </td>
                            <!-- Name: rowspan on first row -->
                            <td v-if="logIndex === 0"
                                :rowspan="getStatusRowCount(proj)"
                                class="px-2 py-3 text-[11px] font-medium text-slate-700 dark:text-slate-200 truncate align-middle text-center border-b border-slate-200 dark:border-white/10"
                                :title="proj.name">
                                {{ proj.name || '-' }}
                            </td>
                            <!-- Progres Status -->
                            <td class="px-1 py-2 align-middle text-center">
                                <span v-if="log?.review_status"
                                    class="inline-flex rounded-md px-1.5 py-0.5 text-[9px] font-medium"
                                    :class="reviewStatusBadgeClass(log.review_status)">
                                    {{ log.review_status }}
                                </span>
                                <span v-else class="text-[10px] italic text-slate-400">-</span>
                            </td>
                            <!-- Periode Status -->
                            <td class="px-2 py-3 text-[10px] font-medium text-slate-600 dark:text-slate-300">
                                {{ getLogPeriodeLabel(log) || '-' }}
                            </td>
                            <!-- Notes -->
                            <td class="px-2 py-3 text-[11px] font-medium text-slate-700 dark:text-slate-300">
                                {{ log?.status || '-' }}
                            </td>
                            <!-- Action -->
                            <td class="px-1 py-1 text-center align-middle">
                                <div class="flex items-center justify-center gap-1.5 w-max mx-auto">
                                    <button v-if="logIndex === 0 && proj?.id" @click="openAddModal(proj)" class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-indigo-100 text-indigo-800 hover:bg-indigo-200 dark:bg-indigo-500/20 dark:text-indigo-300 dark:hover:bg-indigo-500/30 transition-colors cursor-pointer" title="Add Status">
                                        <svg class="mr-0.5 h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Add
                                    </button>
                                    <button v-if="log?.id" @click="openEditModal(log)" class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-amber-100 text-amber-800 hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300 dark:hover:bg-amber-500/30 transition-colors cursor-pointer" title="Edit Status">
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr v-if="projectList.length === 0">
                        <td colspan="6" class="px-4 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
                            Data status implementasi belum tersedia.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tabel 2: Informasi Dasar & Timeline -->
        <div v-if="showTimelineHistory" class="overflow-x-auto rounded-lg border border-slate-200 dark:border-white/10">
            <table class="w-full table-fixed divide-y divide-x divide-slate-200 text-[11px] dark:divide-white/10">
                <colgroup>
                    <col class="w-[20%]">
                    <col class="w-[20%]">
                    <col class="w-[20%]">
                    <col class="w-[20%]">
                    <col class="w-[20%]">
                </colgroup>
                <thead v-if="showHeader" class="bg-slate-50 dark:bg-white/[0.03]">
                    <tr class="divide-x divide-slate-200 dark:divide-white/10">
                        <th
                            class="px-1 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Timeline History Project Charter</th>
                        <th
                            class="px-1 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Timeline Status</th>
                        <th
                            class="px-1 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Duration</th>
                        <th
                            class="px-1 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Document Date</th>
                        <th
                            class="px-1 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Duration Processing (Month)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/10 dark:bg-[#1a1a1a]">
                    <tr v-for="row in projectCharterRows" :key="row.key" class="divide-x divide-slate-200 hover:bg-slate-50 dark:divide-white/10 dark:hover:bg-white/5 transition-colors">
                        <td class="px-2 py-3 text-[11px] font-semibold text-slate-700 dark:text-slate-200">
                            {{ row.versionLabel }}
                        </td>
                        <td class="px-2 py-3">
                            <span
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-medium capitalize"
                                :class="statusBadgeClassById(getRowStatus(row))">
                                {{ statusLabelFromOptions(getRowStatus(row), statusOptions) }}
                            </span>
                        </td>
                        <td class="px-2 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            {{ row.charter?.duration || '-' }}
                        </td>
                        <td class="px-2 py-3 text-[10px] font-medium text-slate-600 dark:text-slate-300">
                            {{ formatDateLong(row.charter?.tgl_dokumen) }}
                        </td>
                        <td class="px-2 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            {{ getTimelineDurationMonths(row.project, row.charter) }}
                        </td>
                    </tr>
                    <tr v-if="projectCharterRows.length === 0">
                        <td colspan="5" class="px-4 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
                            Data belum tersedia.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <StatusImplementationModal
            :show="isModalOpen"
            :status-data="editingStatus"
            :project-id="selectedProjectIdForModal"
            :store-route-name="storeRouteName"
            :update-route-name="updateRouteName"
            @close="closeModal"
        />
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { statusLabelFromOptions } from '@/Composables/initiativeStatus';
import StatusImplementationModal from './ReviewStatusImplementationModal.vue';

const props = defineProps({
    project: {
        type: Object,
        default: null,
    },
    projects: {
        type: Array,
        default: () => [],
    },
    historySource: {
        type: String,
        default: 'auto',
    },
    codeLabel: {
        type: String,
        default: 'Code',
    },
    storeRouteName: {
        type: String,
        default: 'it-initiatives.implementation-status.store',
    },
    updateRouteName: {
        type: String,
        default: 'it-initiatives.implementation-status.update',
    },
    showTimelineHistory: {
        type: Boolean,
        default: true,
    },
    showHeader: {
        type: Boolean,
        default: true,
    },
    hideTitle: {
        type: Boolean,
        default: false,
    },
});

const isModalOpen = ref(false);
const editingStatus = ref(null);
const selectedProjectIdForModal = ref(0);

const openAddModal = (proj) => {
    editingStatus.value = null;
    selectedProjectIdForModal.value = proj.id;
    isModalOpen.value = true;
};

const openEditModal = (log) => {
    editingStatus.value = log;
    selectedProjectIdForModal.value = log.project_id;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    editingStatus.value = null;
    selectedProjectIdForModal.value = 0;
};

// Normalize to array of projects
const projectList = computed(() => {
    if (Array.isArray(props.projects) && props.projects.length > 0) {
        return props.projects;
    }
    if (props.project) {
        return [props.project];
    }
    return [];
});

const statusOptions = computed(() => {
    const defaultOptions = [
        { id: 1, name: 'drafting', label: 'Drafting' },
        { id: 2, name: 'propose', label: 'Propose' },
        { id: 3, name: 'review', label: 'Review' },
        { id: 4, name: 'approved', label: 'Approved' },
        { id: 5, name: 'baseline', label: 'Baseline' },
    ];

    if (!props.projects?.[0]?.status_ref?.name && !props.project?.status_ref?.name) {
        return defaultOptions;
    }

    const currentProject = props.project || props.projects?.[0];
    return defaultOptions.map((option) =>
        option.id === Number(currentProject?.status)
            ? {
                ...option,
                name: currentProject.status_ref.name,
                label: String(currentProject.status_ref.name)
                    .replace(/_/g, ' ')
                    .replace(/\b\w/g, (char) => char.toUpperCase()),
            }
            : option
    );
});

const projectCharterRows = computed(() => {
        return projectList.value.flatMap((project, projectIndex) => {
            const charters = Array.isArray(project?.charters) && project.charters.length > 0
                ? project.charters
                : project?.charter
                    ? [project.charter]
                    : [];

        if (charters.length === 0) {
            return [{
                key: `proj-${project?.id ?? projectIndex}-${projectIndex}-charter-empty`,
                project,
                charter: null,
                versionLabel: '-',
            }];
        }

        return charters.map((charter, charterIndex) => ({
            key: `proj-${project?.id ?? projectIndex}-${projectIndex}-charter-${charter?.id ?? charterIndex}`,
            project,
            charter,
            versionLabel: getCharterVersionLabel(charter, charterIndex, charters.length),
        }));
    });
});

// Helper functions for each project row
const getCharterVersionLabel = (charter, index, total) => {
    const label = String(charter?.resolved_version_label ?? charter?.version_label ?? '').trim();
    if (label) return label;
    if (Number.isFinite(total) && total > 0) {
        return `v${total - index}`;
    }
    return '-';
};

const monthOrderMap = new Map([
    ['Januari', 1],
    ['Februari', 2],
    ['Maret', 3],
    ['April', 4],
    ['Mei', 5],
    ['Juni', 6],
    ['Juli', 7],
    ['Agustus', 8],
    ['September', 9],
    ['Oktober', 10],
    ['November', 11],
    ['Desember', 12],
]);

const asArray = (value) => {
    if (Array.isArray(value)) {
        return value;
    }

    if (value && typeof value === 'object') {
        return Object.values(value);
    }

    return [];
};

const resolveHistorySource = (project) => {
    if (props.historySource === 'review') {
        return project?.review_pc_status_implementations ?? project?.reviewPcStatusImplementations ?? [];
    }

    if (props.historySource === 'implementation') {
        return project?.pc_status_implementations ?? project?.pcStatusImplementations ?? [];
    }

    return project?.review_pc_status_implementations
        ?? project?.reviewPcStatusImplementations
        ?? project?.pc_status_implementations
        ?? project?.pcStatusImplementations
        ?? [];
};

const parseHistoryYear = (value) => {
    const parsed = Number.parseInt(String(value ?? '').trim(), 10);
    return Number.isFinite(parsed) ? parsed : null;
};

const parseHistoryTimestamp = (value) => {
    const parsed = Date.parse(String(value ?? '').trim());
    return Number.isNaN(parsed) ? null : parsed;
};

const primaryHistoryMonth = (log) => {
    const endMonth = monthOrderMap.get(String(log?.end ?? '').trim());
    const startMonth = monthOrderMap.get(String(log?.start ?? '').trim());

    return endMonth ?? startMonth ?? 0;
};

const secondaryHistoryMonth = (log) => {
    return monthOrderMap.get(String(log?.start ?? '').trim()) ?? 0;
};

const sortHistoryDescending = (left, right) => {
    const leftYear = parseHistoryYear(left?.year);
    const rightYear = parseHistoryYear(right?.year);

    if (leftYear !== rightYear) {
        return (rightYear ?? Number.MIN_SAFE_INTEGER) - (leftYear ?? Number.MIN_SAFE_INTEGER);
    }

    const leftPrimaryMonth = primaryHistoryMonth(left);
    const rightPrimaryMonth = primaryHistoryMonth(right);

    if (leftPrimaryMonth !== rightPrimaryMonth) {
        return rightPrimaryMonth - leftPrimaryMonth;
    }

    const leftSecondaryMonth = secondaryHistoryMonth(left);
    const rightSecondaryMonth = secondaryHistoryMonth(right);

    if (leftSecondaryMonth !== rightSecondaryMonth) {
        return rightSecondaryMonth - leftSecondaryMonth;
    }

    const leftTimestamp =
        parseHistoryTimestamp(left?.date) ??
        parseHistoryTimestamp(left?.created_at) ??
        parseHistoryTimestamp(left?.updated_at);
    const rightTimestamp =
        parseHistoryTimestamp(right?.date) ??
        parseHistoryTimestamp(right?.created_at) ??
        parseHistoryTimestamp(right?.updated_at);

    if (leftTimestamp !== rightTimestamp) {
        return (rightTimestamp ?? Number.MIN_SAFE_INTEGER) - (leftTimestamp ?? Number.MIN_SAFE_INTEGER);
    }

    return Number(right?.id || 0) - Number(left?.id || 0);
};

const getImplementationHistory = (project) => {
    return asArray(resolveHistorySource(project)).sort(sortHistoryDescending);
};

// Returns all status rows for a project; at least 1 empty placeholder row if none
const getStatusRows = (project) => {
    const history = getImplementationHistory(project);
    return history.length > 0 ? history : [null];
};

const getStatusRowCount = (project) => {
    return getStatusRows(project).length;
};

const getLogPeriodeLabel = (log) => {
    if (!log) return null;

    // Use backend-computed periode_label
    const label = log?.periode_label ?? null;
    if (label) return label;

    // Fallback: old date column
    const rawDate = log?.date ?? null;
    if (!rawDate) return null;
    const date = new Date(rawDate);
    if (Number.isNaN(date.getTime())) return null;
    return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long' });
};

const getLatestImplementationLog = (project) => {
    const history = getImplementationHistory(project);
    return history.length > 0 ? history[0] : null;
};

const getLatestReviewStatus = (project) => {
    const log = getLatestImplementationLog(project);
    const raw = String(log?.review_status ?? '').trim();
    return raw.length > 0 ? raw : null;
};

const getLatestImplementationStatus = (project) => {
    const log = getLatestImplementationLog(project);
    const raw = String(log?.status ?? '').trim();
    return raw.length > 0 ? raw : null;
};

const getLatestImplementationMonthYear = (project) => {
    const log = getLatestImplementationLog(project);
    if (!log) return null;

    // Use the computed periode_label from the backend
    const label = log?.periode_label ?? null;
    if (label) return label;

    // Fallback: old date column for backward compatibility
    const rawDate = log?.date ?? null;
    if (!rawDate) return null;
    const date = new Date(rawDate);
    if (Number.isNaN(date.getTime())) return null;
    return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long' });
};

const formatDateLong = (value) => {
    const raw = String(value || '').trim();
    if (!raw) return '-';
    const parsed = new Date(raw);
    if (Number.isNaN(parsed.getTime())) return raw;
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(parsed);
};

const parseDateValue = (value) => {
    const raw = String(value ?? '').trim();
    if (!raw) return null;
    const parsed = new Date(raw);
    return Number.isNaN(parsed.getTime()) ? null : parsed;
};

const monthsBetween = (fromDate, toDate) => {
    const from = parseDateValue(fromDate);
    const to = parseDateValue(toDate);
    if (!from || !to) return null;
    const fromIndex = from.getUTCFullYear() * 12 + from.getUTCMonth();
    const toIndex = to.getUTCFullYear() * 12 + to.getUTCMonth();
    return Math.max(toIndex - fromIndex, 0);
};

const projectChartersFor = (project) => {
    if (Array.isArray(project?.charters) && project.charters.length > 0) {
        return project.charters;
    }
    if (project?.charter) {
        return [project.charter];
    }
    return [];
};

const getTimelineDurationMonths = (project, charter) => {
    const charters = projectChartersFor(project);
    if (!charters.length) return '-';

    const sorted = [...charters].sort((a, b) => {
        const dateA = parseDateValue(a?.tgl_dokumen);
        const dateB = parseDateValue(b?.tgl_dokumen);
        if (dateA && dateB) return dateB - dateA;
        if (dateB && !dateA) return 1;
        if (dateA && !dateB) return -1;
        return Number(b?.id || 0) - Number(a?.id || 0);
    });

    if (sorted.length === 1) {
        return '0';
    }

    const latest = sorted[0];
    const previous = sorted[1];
    if (!latest || !previous) return '-';

    if (String(charter?.id ?? '') !== String(latest?.id ?? '')) {
        return '-';
    }

    const diff = monthsBetween(previous?.tgl_dokumen, latest?.tgl_dokumen);
    return diff === null ? '-' : String(diff);
};

const getRowStatus = (row) => {
    const charterStatus = row?.charter?.status;
    if (charterStatus !== null && charterStatus !== undefined && charterStatus !== '') {
        return Number(charterStatus);
    }
    return null;
};

const statusBadgeClassById = (statusId) => {
    const val = Number(statusId);
    switch (val) {
        case 1: return 'bg-slate-100 text-slate-600 ring-1 ring-slate-300';
        case 2: return 'bg-blue-100 text-blue-700 ring-1 ring-blue-300';
        case 3: return 'bg-amber-100 text-amber-700 ring-1 ring-amber-300';
        case 4: return 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300';
        case 5: return 'bg-purple-100 text-purple-700 ring-1 ring-purple-300';
        default: return 'bg-slate-100 text-slate-500';
    }
};

const reviewStatusBadgeClass = (reviewStatus) => {
    const normalized = String(reviewStatus ?? '').trim().toLowerCase();
    if (normalized === 'on track') return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300';
    if (normalized === 'at risk') return 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300';
    if (normalized === 'not started') return 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300';
    if (normalized === 'not signed') return 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-300';
    return 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300';
};
</script>
