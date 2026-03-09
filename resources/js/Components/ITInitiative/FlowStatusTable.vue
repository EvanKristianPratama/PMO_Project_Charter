<template>
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
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Code</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">IT Arsitektur Building Blok</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Daftar Inisiatif</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Project Charter</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tanggal Project Charter </th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                    <tr v-for="(project, index) in items" :key="project.id" class="group transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
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
                                :class="statusBadgeClassById(project.status)"
                            >
                                {{ statusLabelFromOptions(project.status, statusOptions) }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-[11px] font-medium text-slate-700 dark:text-slate-300">
                            {{ formatDate(project.charter?.tgl_dokumen) }}
                        </td>
                        <td class="px-3 py-3 text-[10px] font-medium">
                            <div class="flex flex-col items-start gap-1">
                                <Link
                                    :href="`/it-initiatives/${project.id}?tab=detail`"
                                    :class="actionCellClass(hasScopeCharter(project))"
                                    title="View Scope Charter"
                                >
                                    Scope Charter
                                </Link>
                                <Link
                                    :href="`/it-initiatives/${project.id}?tab=charter`"
                                    :class="actionCellClass(hasProjectCharter(project))"
                                    title="View Project Charter"
                                >
                                    Project Charter
                                </Link>
                                <Link
                                    :href="project?.charter?.id ? `/roadmap?pc_id=${project.charter.id}` : '/roadmap'"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-sky-100 text-sky-700 transition-colors hover:bg-sky-200 dark:bg-sky-500/20 dark:text-sky-300 dark:hover:bg-sky-500/30"
                                    title="Open Roadmap"
                                >
                                    Roadmap
                                </Link>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="items.length === 0">
                        <td :colspan="tableColspan" class="px-6 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                            <span v-if="activeFlowFilter === null">
                                Silakan klik salah satu status di atas untuk menampilkan data inisiatif.
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
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { statusBadgeClassById, statusLabelFromOptions } from '@/Composables/initiativeStatus';

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

const tableColspan = 7;

const shouldShowCategory = (index) => {
    if (index === 0) return true;
    const current = props.items[index]?.charter?.category || 'Uncategorized';
    const previous = props.items[index - 1]?.charter?.category || 'Uncategorized';
    return current !== previous;
};

const getCategoryRowspan = (index) => {
    let count = 1;
    const current = props.items[index]?.charter?.category || 'Uncategorized';
    for (let i = index + 1; i < props.items.length; i += 1) {
        if ((props.items[i]?.charter?.category || 'Uncategorized') === current) {
            count += 1;
        } else {
            break;
        }
    }
    return count;
};

const hasFilled = (value) => value !== null && value !== undefined && String(value).trim() !== '';

const hasScopeCharter = (project) => hasFilled(project?.code) && hasFilled(project?.name);

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
        charter.scope,
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
