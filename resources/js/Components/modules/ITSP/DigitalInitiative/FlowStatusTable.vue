<template>
    <div class="space-y-4">
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
            <div class="overflow-x-hidden">
                <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                <colgroup>
                    <col class="w-[3%]">
                    <col class="w-[6%]">
                    <col class="w-[12%]">
                    <col class="w-[14%]">
                    <col class="w-[8%]">
                    <col class="w-[10%]">
                    <col class="w-[10%]">
                </colgroup>
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 align-top dark:text-slate-400">No</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 align-top dark:text-slate-400">Code</th>
                        <th class="px-3 py-2 text-left align-top">
                            <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Center Of Excelence</span>
                            <select v-model="filterCategory" class="mt-1.5 w-full rounded border border-slate-300 bg-white px-1.5 py-1 text-[10px] font-normal text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200">
                                <option value="">Semua</option>
                                <option v-for="cat in availableCategories" :key="cat" :value="cat">{{ cat }}</option>
                            </select>
                        </th>
                        <th class="px-3 py-2 text-left align-top">
                            <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Daftar Inisiatif</span>
                            <select v-model="filterInitiativeName" class="mt-1.5 w-full rounded border border-slate-300 bg-white px-1.5 py-1 text-[10px] font-normal text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200">
                                <option value="">Semua</option>
                                <option v-for="name in availableInitiatives" :key="name" :value="name">{{ name }}</option>
                            </select>
                        </th>
                        <th class="px-3 py-2 text-left align-top">
                            <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Project Charter</span>
                            <select v-model="filterStatus" class="mt-1.5 w-full rounded border border-slate-300 bg-white px-1.5 py-1 text-[10px] font-normal text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200">
                                <option value="">Semua</option>
                                <option v-for="status in availableStatuses" :key="status.id" :value="status.id">{{ status.label }}</option>
                            </select>
                        </th>
                        <th class="px-3 py-2 text-left align-top">
                            <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tanggal Project</span>
                            <select v-model="filterDate" class="mt-1.5 w-full rounded border border-slate-300 bg-white px-1.5 py-1 text-[10px] font-normal text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-1 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200">
                                <option value="">Semua</option>
                                <option v-for="date in availableDates" :key="date" :value="date">{{ date }}</option>
                            </select>
                        </th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 align-top dark:text-slate-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                    <tr v-for="(project, index) in displayedItems" :key="project.id" class="group transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-3 py-3 text-[11px] font-medium text-slate-600 dark:text-slate-400">
                            {{ index + 1 }}
                        </td>
                        <td class="px-3 py-3 text-[11px] font-medium text-slate-600 dark:text-slate-400">
                            {{ project.code || '-' }}
                        </td>
                        <td
                            v-if="shouldShowCategory(index)"
                            :rowspan="getCategoryRowspan(index)"
                            class="px-3 py-3 align-top border-r border-slate-100 bg-slate-50/50 dark:border-white/5 dark:bg-white/[0.02]"
                        >
                            <span class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold leading-tight text-blue-800 dark:bg-blue-500/20 dark:text-blue-300">
                                {{ project.charter?.category || 'Uncategorized' }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            <span class="font-medium break-words text-slate-700 dark:text-slate-200">{{ project.name || '-' }}</span>
                        </td>
                        <td class="px-3 py-3">
                            <span
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium capitalize"
                                :class="statusBadgeClassById(resolvedProjectStatusId(project))"
                            >
                                {{ statusLabelFromOptions(resolvedProjectStatusId(project), statusOptions) }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-[11px] font-medium text-slate-700 dark:text-slate-300">
                            {{ formatDate(resolvedProjectStatusDate(project)) }}
                        </td>
                        <td class="px-3 py-3 text-[10px] font-medium">
                            <div class="flex flex-col items-start gap-1">
                                <Link
                                    :href="route('itsp.digital-initiatives.edit', project.id)"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-amber-100 text-amber-700 transition-colors hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300 dark:hover:bg-amber-500/30"
                                    title="Edit Project Charter"
                                >
                                    Edit
                                </Link>
                                <Link
                                    v-if="hasProjectCharter(project)"
                                    :href="route('itsp.digital-initiatives.show', { digital_initiative: project.id, tab: 'charter' })"
                                    :class="actionCellClass(hasProjectCharter(project))"
                                    title="View Project Charter"
                                >
                                    Project Charter
                                </Link>
                                <Link
                                    v-if="hasProjectCharter(project)"
                                    :href="project?.charter?.id ? route('itsp.roadmap.index', { pc_id: project.charter.id }) : route('itsp.roadmap.index')"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-sky-100 text-sky-700 transition-colors hover:bg-sky-200 dark:bg-sky-500/20 dark:text-sky-300 dark:hover:bg-sky-500/30"
                                    title="Open Roadmap"
                                >
                                    Roadmap
                                </Link>
                                <Link
                                    v-if="hasProjectCharter(project)"
                                    :href="route('itsp.digital-initiatives.show', { digital_initiative: project.id, tab: 'detail' })"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-blue-100 text-blue-700 transition-colors hover:bg-blue-200 dark:bg-blue-500/20 dark:text-blue-300 dark:hover:bg-blue-500/30"
                                    title="View Status Implementation"
                                >
                                    Status
                                </Link>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="displayedItems.length === 0">
                        <td :colspan="7" class="px-6 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                            <span v-if="activeFlowFilter === null && items.length === 0">
                                Silakan klik salah satu status di atas untuk menampilkan data inisiatif.
                            </span>
                            <span v-else-if="items.length > 0 && displayedItems.length === 0">
                                Tidak ada data yang sesuai dengan filter pencarian ini.
                            </span>
                            <span v-else>
                                Tidak ada data yang sesuai dengan filter opsi ini.
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { statusBadgeClassById, statusLabelFromOptions } from '@/Composables/initiativeStatus';

const FLOW_NOT_YET_ID = 0;

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    statusOptions: {
        type: Array,
        default: () => [],
    },
    activeFlowFilter: {
        type: [Number, String],
        default: null,
    },
});

const latestProjectStatusHistory = (project) => {
    const histories = project?.project_status_histories ?? project?.projectStatusHistories ?? [];
    return Array.isArray(histories) && histories.length > 0 ? histories[0] : null;
};

const resolvedProjectStatusId = (project) => {
    // 1. Check history first
    const historyStatus = latestProjectStatusHistory(project)?.status;
    if (historyStatus !== null && historyStatus !== undefined && historyStatus !== "") {
        const parsedHistoryStatus = Number(historyStatus);
        if (Number.isInteger(parsedHistoryStatus) && parsedHistoryStatus >= 0) {
            return parsedHistoryStatus;
        }
    }

    // 2. Fallback to project status
    if (project?.status !== null && project?.status !== undefined) {
        const parsedStatus = Number(project.status);
        if (Number.isInteger(parsedStatus) && parsedStatus >= 0) {
            return parsedStatus;
        }
    }

    return FLOW_NOT_YET_ID;
};

const resolvedProjectStatusDate = (project) => {
    return latestProjectStatusHistory(project)?.tanggal ?? null;
};

const shouldShowCategory = (index) => {
    if (index === 0) return true;
    const current = displayedItems.value[index]?.charter?.category || 'Uncategorized';
    const previous = displayedItems.value[index - 1]?.charter?.category || 'Uncategorized';
    return current !== previous;
};

const getCategoryRowspan = (index) => {
    let count = 1;
    const current = displayedItems.value[index]?.charter?.category || 'Uncategorized';
    for (let i = index + 1; i < displayedItems.value.length; i += 1) {
        if ((displayedItems.value[i]?.charter?.category || 'Uncategorized') === current) {
            count += 1;
        } else {
            break;
        }
    }
    return count;
};

const filterCategory = ref('');
const filterInitiativeName = ref('');
const filterStatus = ref('');
const filterDate = ref('');

const availableCategories = computed(() => {
    const cats = new Set(props.items.map(p => p.charter?.category || 'Uncategorized'));
    return Array.from(cats).sort();
});

const availableInitiatives = computed(() => {
    const inits = new Set(props.items.map(p => p.name).filter(Boolean));
    return Array.from(inits).sort();
});

const availableStatuses = computed(() => {
    const statuses = new Map();
    props.items.forEach(p => {
        const id = resolvedProjectStatusId(p);
        const label = statusLabelFromOptions(id, props.statusOptions);
        if (!statuses.has(id)) {
            statuses.set(id, { id, label });
        }
    });
    return Array.from(statuses.values()).sort((a,b) => a.id - b.id);
});

const availableDates = computed(() => {
    const dates = new Set(props.items.map(p => {
        const dateStr = resolvedProjectStatusDate(p);
        if (!dateStr) return null;
        return dateStr.substring(0, 7); // YYYY-MM
    }).filter(Boolean));
    return Array.from(dates).sort((a,b) => b.localeCompare(a));
});

const displayedItems = computed(() => {
    return props.items.filter(project => {
        // Filter by category
        if (filterCategory.value && filterCategory.value !== '') {
            const cat = project.charter?.category || 'Uncategorized';
            if (cat !== filterCategory.value) return false;
        }

        // Filter by Name
        if (filterInitiativeName.value && filterInitiativeName.value !== '') {
            if (project.name !== filterInitiativeName.value) return false;
        }

        // Filter by Status
        if (filterStatus.value !== '') {
            if (resolvedProjectStatusId(project) !== Number(filterStatus.value)) return false;
        }

        // Filter by date (YYYY-MM)
        if (filterDate.value && filterDate.value !== '') {
            const dateStr = resolvedProjectStatusDate(project);
            if (!dateStr || !String(dateStr).startsWith(filterDate.value)) return false;
        }

        return true;
    });
});

const hasFilled = (value) => value !== null && value !== undefined && String(value).trim() !== '';

const hasProjectCharter = (project) => {
    const charter = project?.charter;
    if (!charter || typeof charter !== 'object') {
        return false;
    }

    return [
        charter.category,
        charter.duration,
        charter.background,
        charter.objectives,
        charter.impact_value,
    ].some(hasFilled);
};

const actionCellClass = (isReady) => {
    if (isReady) {
        return 'inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-500/20 dark:text-emerald-300 dark:hover:bg-emerald-500/30 transition-colors cursor-pointer';
    }

    return 'inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-rose-100 text-rose-800 hover:bg-rose-200 dark:bg-rose-500/20 dark:text-rose-300 dark:hover:bg-rose-500/30 transition-colors cursor-pointer';
};

const formatDate = (rawDate) => {
    if (!rawDate) return '-';
    const date = new Date(rawDate);
    if (Number.isNaN(date.getTime())) return rawDate;
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};
</script>
