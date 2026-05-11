<template>
    <div class="space-y-3">
        <!-- Tabel 1: Code & Status Implementasi -->
        <div class="rounded-lg border border-slate-200 dark:border-white/10">
            <div
                v-if="actionableProjects.length > 0"
                class="flex flex-wrap items-center gap-2 border-b border-slate-200 bg-slate-50/80 px-3 py-2 dark:border-white/10 dark:bg-white/[0.03]"
            >
                <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                    Quick Action
                </span>
                <button
                    v-for="(proj, projIndex) in actionableProjects"
                    :key="`add-status-${proj?.id ?? projIndex}`"
                    type="button"
                    class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-semibold bg-indigo-100 text-indigo-800 hover:bg-indigo-200 dark:bg-indigo-500/20 dark:text-indigo-300 dark:hover:bg-indigo-500/30 transition-colors cursor-pointer"
                    :title="`Add status untuk ${getProjectActionLabel(proj)}`"
                    @click="openAddModal(proj)"
                >
                    <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Status
                    <span
                        v-if="projectList.length > 1"
                        class="ml-1.5 rounded-full bg-white/80 px-1.5 py-0.5 text-[9px] font-semibold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-200"
                    >
                        {{ getProjectActionLabel(proj) }}
                    </span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full table-fixed divide-y divide-x divide-slate-200 text-[11px] dark:divide-white/10">
                    <colgroup>
                        <col class="w-[7%]">
                        <col class="w-[15%]">
                        <col class="w-[7%]">
                        <col class="w-[7%]">
                        <col class="w-[9%]">
                        <col class="w-[8%]">
                        <col class="w-[8%]">
                        <col class="w-[27%]">
                        <col class="w-[12%]">
                    </colgroup>
                    <thead v-if="showHeader" class="bg-slate-50 dark:bg-white/[0.03]">
                        <tr class="divide-x divide-slate-200 dark:divide-white/10">
                            <th
                                class="px-1 py-1.5 text-center text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                                Code</th>
                            <th
                                class="px-1 py-1.5 text-center text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                                Project Name</th>
                            <th
                                class="px-1 py-1.5 text-center text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                                Target</th>
                            <th
                                class="px-1 py-1.5 text-center text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                                Progres</th>
                            <th
                                class="px-1 py-1.5 text-center text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                                Status</th>
                            <th
                                class="px-1 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                                Periode</th>
                            <th
                                class="px-1 py-1.5 text-center text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                                Year</th>
                            <th
                                class="px-1 py-1.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                                Description</th>
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
                                    {{ displayValue(proj.code) }}
                                </td>
                                <!-- Name: rowspan on first row -->
                                <td v-if="logIndex === 0"
                                    :rowspan="getStatusRowCount(proj)"
                                    class="whitespace-normal break-words px-2 py-3 text-[11px] font-medium text-slate-700 dark:text-slate-200 align-middle text-center border-b border-slate-200 dark:border-white/10"
                                    :title="proj.name">
                                    {{ displayValue(proj.name) }}
                                </td>
                                <!-- Target -->
                                <td class="px-1 py-2 align-middle text-center">
                                    <span v-if="log?.target != null" class="text-[11px] font-semibold text-slate-700 dark:text-slate-200">
                                        {{ log.target }}%
                                    </span>
                                    <span v-else class="text-[10px] italic text-slate-400">{{ EMPTY_VALUE }}</span>
                                </td>
                                <!-- Progres -->
                                <td class="px-1 py-2 align-middle text-center">
                                    <span v-if="log?.progress != null"
                                        class="text-[11px] font-semibold text-slate-700 dark:text-slate-200">
                                        {{ log.progress }}%
                                    </span>
                                    <span v-else class="text-[10px] italic text-slate-400">{{ EMPTY_VALUE }}</span>
                                </td>
                                <!-- Status -->
                                <td class="px-2 py-3 text-center text-[10px] font-medium text-slate-600 dark:text-slate-300">
                                    <span v-if="log?.status"
                                        class="inline-flex rounded-md px-1.5 py-0.5 text-[9px] font-medium"
                                        :class="statusBadgeClass(log.status)">
                                        {{ log.status }}
                                    </span>
                                    <span v-else class="text-[10px] italic text-slate-400">{{ EMPTY_VALUE }}</span>
                                </td>
                                <!-- Periode -->
                                <td class="px-2 py-3 text-[10px] font-medium text-slate-600 dark:text-slate-300">
                                    {{ getPeriodeLabel(log) }}
                                </td>
                                <!-- Year -->
                                <td class="px-2 py-3 text-center text-[10px] font-medium text-slate-600 dark:text-slate-300">
                                    {{ getYearLabel(log) }}
                                </td>
                                <!-- Description -->
                                <td class="whitespace-pre-line break-words px-2 py-3 text-[11px] font-medium text-slate-700 dark:text-slate-300">
                                    {{ displayValue(log?.description) }}
                                </td>
                                <!-- Action -->
                                <td class="px-1 py-1 text-center align-middle">
                                    <div v-if="log?.id" class="flex items-center justify-center gap-1.5 w-max mx-auto">
                                        <button
                                            type="button"
                                            @click="openEditModal(log)"
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-amber-100 text-amber-800 hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300 dark:hover:bg-amber-500/30 transition-colors cursor-pointer"
                                            title="Edit Status"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            :disabled="deleteProcessing"
                                            @click="openDeleteModal(log)"
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-rose-100 text-rose-800 hover:bg-rose-200 disabled:opacity-50 dark:bg-rose-500/20 dark:text-rose-300 dark:hover:bg-rose-500/30 transition-colors cursor-pointer"
                                            title="Delete Status"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                    <span v-else class="text-[10px] italic text-slate-400">{{ EMPTY_VALUE }}</span>
                                </td>
                            </tr>
                        </template>
                        <tr v-if="projectList.length === 0">
                            <td colspan="9" class="px-4 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
                                Data status implementasi belum tersedia.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
                                v-if="getRowStatus(row) !== null"
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-medium capitalize"
                                :class="statusBadgeClassById(getRowStatus(row))">
                                {{ statusLabelFromOptions(getRowStatus(row), statusOptions) }}
                            </span>
                            <span v-else class="text-[10px] italic text-slate-400">{{ EMPTY_VALUE }}</span>
                        </td>
                        <td class="px-2 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            {{ displayValue(row.charter?.duration) }}
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
            @close="closeModal"
        />
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Swal from 'sweetalert2';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import { statusLabelFromOptions } from '@/Composables/initiativeStatus';
import StatusImplementationModal from './StatusImplementationModal.vue';

const EMPTY_VALUE = 'N/A';

const props = defineProps({
    project: {
        type: Object,
        default: null,
    },
    projects: {
        type: Array,
        default: () => [],
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

const route = useRouteHelper();
const isModalOpen = ref(false);
const editingStatus = ref(null);
const selectedProjectIdForModal = ref(0);
const deleteProcessing = ref(false);

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

const actionableProjects = computed(() => {
    return projectList.value.filter((project) => project?.id !== null && project?.id !== undefined);
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
                versionLabel: EMPTY_VALUE,
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
    return EMPTY_VALUE;
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

const hasDisplayValue = (value) => {
    if (value === null || value === undefined) {
        return false;
    }

    if (typeof value === 'string') {
        return value.trim() !== '';
    }

    return true;
};

const displayValue = (value) => {
    return hasDisplayValue(value) ? value : EMPTY_VALUE;
};

const asArray = (value) => {
    if (Array.isArray(value)) {
        return value;
    }

    if (value && typeof value === 'object') {
        return Object.values(value);
    }

    return [];
};

const parseImplementationYear = (value) => {
    const parsed = Number.parseInt(String(value ?? '').trim(), 10);
    return Number.isFinite(parsed) ? parsed : null;
};

const parseImplementationTimestamp = (value) => {
    const parsed = Date.parse(String(value ?? '').trim());
    return Number.isNaN(parsed) ? null : parsed;
};

const sortImplementationHistory = (left, right) => {
    const leftYear = parseImplementationYear(left?.year);
    const rightYear = parseImplementationYear(right?.year);

    if (leftYear !== rightYear) {
        return (rightYear ?? Number.MIN_SAFE_INTEGER) - (leftYear ?? Number.MIN_SAFE_INTEGER);
    }

    const leftMonth = monthOrderMap.get(String(left?.month ?? '').trim()) ?? 0;
    const rightMonth = monthOrderMap.get(String(right?.month ?? '').trim()) ?? 0;

    if (leftMonth !== rightMonth) {
        return rightMonth - leftMonth;
    }

    const leftTimestamp =
        parseImplementationTimestamp(left?.created_at) ??
        parseImplementationTimestamp(left?.updated_at);
    const rightTimestamp =
        parseImplementationTimestamp(right?.created_at) ??
        parseImplementationTimestamp(right?.updated_at);

    if (leftTimestamp !== rightTimestamp) {
        return (rightTimestamp ?? Number.MIN_SAFE_INTEGER) - (leftTimestamp ?? Number.MIN_SAFE_INTEGER);
    }

    return Number(right?.id || 0) - Number(left?.id || 0);
};

const getImplementationHistory = (project) => {
    return asArray(
        project?.pc_status_implementations ?? project?.pcStatusImplementations,
    ).sort(sortImplementationHistory);
};

const getProjectActionLabel = (project) => {
    const code = String(project?.code ?? '').trim();
    if (code) {
        return code;
    }

    const name = String(project?.name ?? '').trim();
    if (name) {
        return name;
    }

    return `Project #${project?.id ?? EMPTY_VALUE}`;
};

// Returns all status rows for a project; at least 1 empty placeholder row if none
const getStatusRows = (project) => {
    const history = getImplementationHistory(project);
    return history.length > 0 ? history : [null];
};

const getStatusRowCount = (project) => {
    return getStatusRows(project).length;
};

const openDeleteModal = (log) => {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data status implementation ini akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            deleteStatus(log);
        }
    });
};

const deleteStatus = (log) => {
    const statusId = log?.id;
    if (!statusId) return;

    deleteProcessing.value = true;

    router.delete(route('it-initiatives.implementation-status.destroy', statusId), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: 'Terhapus!',
                text: 'Data status implementation berhasil dihapus.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        },
        onFinish: () => {
            deleteProcessing.value = false;
        },
    });
};

const getPeriodeLabel = (log) => {
    if (!log) return EMPTY_VALUE;
    const month = String(log?.month ?? '').trim();
    return month || EMPTY_VALUE;
};

const getYearLabel = (log) => {
    if (!log) return EMPTY_VALUE;
    return displayValue(log?.year);
};

const formatDateLong = (value) => {
    const raw = String(value || '').trim();
    if (!raw) return EMPTY_VALUE;
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
    if (!charters.length) return EMPTY_VALUE;

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
    if (!latest || !previous) return EMPTY_VALUE;

    if (String(charter?.id ?? '') !== String(latest?.id ?? '')) {
        return EMPTY_VALUE;
    }

    const diff = monthsBetween(previous?.tgl_dokumen, latest?.tgl_dokumen);
    return diff === null ? EMPTY_VALUE : String(diff);
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

const statusBadgeClass = (status) => {
    const normalized = String(status ?? '').trim().toLowerCase();
    if (normalized === 'on track') return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300';
    if (normalized === 'done' || normalized === 'completed') return 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300';
    if (normalized === 'at risk') return 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300';
    if (normalized === 'delayed') return 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-300';
    if (normalized === 'not started') return 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300';
    return 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300';
};
</script>
