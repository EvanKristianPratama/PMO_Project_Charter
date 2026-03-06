<template>
    <div class="space-y-3">
        <!-- Tabel 1: Code & Status Implementasi -->
        <div class="overflow-x-auto rounded-lg border border-slate-100 dark:border-white/5">
            <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                <colgroup>
                    <col class="w-[10%]">
                    <col class="w-[15%]">
                    <col class="w-[20%]">
                    <col class="w-[55%]">
                </colgroup>
                <thead class="bg-slate-50 dark:bg-white/[0.03]">
                    <tr>
                        <th
                            class="px-2 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Code</th>
                        <th
                            class="px-2 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Review Implementation</th>
                        <th
                            class="px-2 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Month/Year</th>
                        <th
                            class="px-2 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-[#1a1a1a]">
                    <tr v-for="(proj, projIndex) in projectList" :key="`t1-status-${proj?.id ?? projIndex}`">
                        <td class="px-2 py-2 text-[11px] font-semibold text-slate-700 dark:text-slate-200">
                            {{ proj.code || '-' }}
                        </td>
                        <td class="px-2 py-2">
                            <span v-if="getLatestReviewStatus(proj)"
                                class="inline-flex rounded-md px-1.5 py-0.5 text-[9px] font-medium"
                                :class="reviewStatusBadgeClass(getLatestReviewStatus(proj))">
                                {{ getLatestReviewStatus(proj) }}
                            </span>
                            <span v-else class="text-[10px] italic text-slate-400">-</span>
                        </td>
                        <td class="px-2 py-2 text-[10px] font-medium text-slate-600 dark:text-slate-300">
                            {{ getLatestImplementationMonthYear(proj) || '-' }}
                        </td>
                        <td class="px-2 py-2">
                            <span v-if="getLatestImplementationStatus(proj)"
                                class="inline-flex rounded-md bg-slate-100 px-1.5 py-0.5 text-[9px] font-medium text-slate-700 dark:bg-white/10 dark:text-slate-300">
                                {{ getLatestImplementationStatus(proj) }}
                            </span>
                            <span v-else class="text-[10px] italic text-slate-400">-</span>
                        </td>
                    </tr>
                    <tr v-if="projectList.length === 0">
                        <td colspan="4" class="px-4 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
                            Data status implementasi belum tersedia.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tabel 2: Informasi Dasar & Timeline -->
        <div class="overflow-x-auto rounded-lg border border-slate-100 dark:border-white/5">
            <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                <colgroup>
                    <col class="w-[20%]">
                    <col class="w-[20%]">
                    <col class="w-[20%]">
                    <col class="w-[20%]">
                    <col class="w-[20%]">
                </colgroup>
                <thead class="bg-slate-50 dark:bg-white/[0.03]">
                    <tr>
                        <th
                            class="px-2 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            History Timeline</th>
                        <th
                            class="px-2 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Timeline Status</th>
                        <th
                            class="px-2 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Duration</th>
                        <th
                            class="px-2 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Document Date</th>
                        <th
                            class="px-2 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                            Duration Processing (Month)</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-[#1a1a1a]">
                    <tr v-for="row in projectCharterRows" :key="row.key">
                        <td class="px-2 py-2 text-[11px] font-semibold text-slate-700 dark:text-slate-200">
                            {{ row.versionLabel }}
                        </td>
                        <td class="px-2 py-2">
                            <span
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-medium capitalize"
                                :class="statusBadgeClassById(getRowStatus(row))">
                                {{ statusLabelFromOptions(getRowStatus(row), statusOptions) }}
                            </span>
                        </td>
                        <td class="px-2 py-2 text-[11px] text-slate-700 dark:text-slate-200">
                            {{ row.charter?.duration || '-' }}
                        </td>
                        <td class="px-2 py-2 text-[10px] font-medium text-slate-600 dark:text-slate-300">
                            {{ formatDateLong(row.charter?.tgl_dokumen) }}
                        </td>
                        <td class="px-2 py-2 text-[11px] text-slate-700 dark:text-slate-200">
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
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { statusLabelFromOptions } from '@/Composables/initiativeStatus';

const props = defineProps({
    project: {
        type: Object,
        default: null,
    },
    projects: {
        type: Array,
        default: () => [],
    },
});

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

const getImplementationHistory = (project) => {
    const source = project?.pc_status_implementations ?? project?.pcStatusImplementations ?? [];
    return Array.isArray(source) ? source : [];
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
    const rawDate = log?.date ?? null;
    if (!rawDate) return null;
    const date = new Date(rawDate);
    if (Number.isNaN(date.getTime())) return null;
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
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
