<template>
    <section v-if="rows.length > 0" class="overflow-hidden rounded-2xl border border-slate-900 shadow-sm dark:border-white/20">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed border-collapse text-left text-[11px]">
                <colgroup>
                    <col class="w-[20%]" />
                    <col class="w-[5%]" />
                    <col class="w-[9%]" />
                    <col class="w-[5%]" />
                    <col class="w-[9%]" />
                    <col class="w-[5%]" />
                    <col class="w-[9%]" />
                    <col class="w-[5%]" />
                    <col class="w-[9%]" />
                    <col class="w-[5%]" />
                    <col class="w-[9%]" />
                    <col class="w-[10%]" />
                </colgroup>
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:bg-white/5 dark:text-slate-400">
                        <th rowspan="2" class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">
                            Pelaksana
                        </th>
                        <th colspan="2" class="border-b border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            Project Sponsor
                        </th>
                        <th colspan="2" class="border-b border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            Project Owner
                        </th>
                        <th colspan="2" class="border-b border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            Project Leader
                        </th>
                        <th colspan="2" class="border-b border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            Cross Function Involvement
                        </th>
                        <th colspan="2" class="border-b border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            Personel Utama
                        </th>
                        <th rowspan="2" class="border-b border-l border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            Total All
                        </th>
                    </tr>
                    <tr class="bg-slate-50/50 text-[9px] font-bold uppercase tracking-widest text-slate-400 dark:bg-white/5 dark:text-slate-500">
                        <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Total</th>
                        <th class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20">Inisiatif</th>
                        <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Total</th>
                        <th class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20">Inisiatif</th>
                        <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Total</th>
                        <th class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20">Inisiatif</th>
                        <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Total</th>
                        <th class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20">Inisiatif</th>
                        <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Total</th>
                        <th class="border-b border-slate-900 px-4 py-2 dark:border-white/20">Inisiatif</th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-[#171717]">
                    <tr
                        v-for="row in rows"
                        :key="row.organization_id"
                        class="align-top transition-colors hover:bg-slate-50 dark:hover:bg-white/5"
                    >
                        <td class="border-b border-r border-slate-900 px-4 py-4 align-top font-black text-slate-900 dark:border-b-white/20 dark:border-r-white/20 dark:text-white">
                            <div class="flex flex-col gap-1">
                                <div class="flex flex-wrap items-baseline gap-1">
                                    <span>{{ row.actor || '-' }}</span>
                                    <span v-if="row.pejabat" class="font-normal italic text-slate-500 dark:text-slate-400">
                                        - {{ row.pejabat }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="border-b border-r border-slate-900 px-4 py-3 text-center font-black text-slate-900 dark:border-b-white/20 dark:border-r-white/20 dark:text-white">
                            {{ row.project_sponsors?.length ?? 0 }}
                        </td>
                        <td class="border-b border-r border-slate-900 px-4 py-3 dark:border-b-white/20 dark:border-r-white/20">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="item in row.project_sponsors"
                                    :key="`sp-${row.organization_id}-${item.initiative_id}`"
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="statusColor(item.status)"
                                    :title="capsuleTooltip(item, item.status)"
                                >
                                    {{ item.no }}
                                </span>
                                <span v-if="!row.project_sponsors?.length" class="text-slate-400">-</span>
                            </div>
                        </td>

                        <td class="border-b border-r border-slate-900 px-4 py-3 text-center font-black text-slate-900 dark:border-b-white/20 dark:border-r-white/20 dark:text-white">
                            {{ row.project_owners?.length ?? 0 }}
                        </td>
                        <td class="border-b border-r border-slate-900 px-4 py-3 dark:border-b-white/20 dark:border-r-white/20">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="item in row.project_owners"
                                    :key="`ow-${row.organization_id}-${item.initiative_id}`"
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="statusColor(item.status)"
                                    :title="capsuleTooltip(item, item.status)"
                                >
                                    {{ item.no }}
                                </span>
                                <span v-if="!row.project_owners?.length" class="text-slate-400">-</span>
                            </div>
                        </td>

                        <td class="border-b border-r border-slate-900 px-4 py-3 text-center font-black text-slate-900 dark:border-b-white/20 dark:border-r-white/20 dark:text-white">
                            {{ row.project_leaders?.length ?? 0 }}
                        </td>
                        <td class="border-b border-r border-slate-900 px-4 py-3 dark:border-b-white/20 dark:border-r-white/20">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="item in row.project_leaders"
                                    :key="`ld-${row.organization_id}-${item.initiative_id}`"
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="statusColor(item.status)"
                                    :title="capsuleTooltip(item, item.status)"
                                >
                                    {{ item.no }}
                                </span>
                                <span v-if="!row.project_leaders?.length" class="text-slate-400">-</span>
                            </div>
                        </td>

                        <td class="border-b border-r border-slate-900 px-4 py-3 text-center font-black text-slate-900 dark:border-b-white/20 dark:border-r-white/20 dark:text-white">
                            {{ row.cross_function_involvements?.length ?? 0 }}
                        </td>
                        <td class="border-b border-r border-slate-900 px-4 py-3 dark:border-b-white/20 dark:border-r-white/20">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="item in row.cross_function_involvements"
                                    :key="`cf-${row.organization_id}-${item.initiative_id}`"
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="statusColor(item.status)"
                                    :title="capsuleTooltip(item, item.status)"
                                >
                                    {{ item.no }}
                                </span>
                                <span v-if="!row.cross_function_involvements?.length" class="text-slate-400">-</span>
                            </div>
                        </td>

                        <td class="border-b border-r border-slate-900 px-4 py-3 text-center font-black text-slate-900 dark:border-b-white/20 dark:border-r-white/20 dark:text-white">
                            {{ row.personel_utama?.length ?? 0 }}
                        </td>
                        <td class="border-b px-4 py-3 dark:border-b-white/20">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="item in row.personel_utama"
                                    :key="`kp-${row.organization_id}-${item.personel_key ?? item.no ?? item.code ?? item.initiative_id}`"
                                    class="inline-flex h-5 min-w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="statusColor(item.implementation_status || item.status)"
                                    :title="capsuleTooltip(item, item.implementation_status || item.status)"
                                >
                                    {{ item.no }}
                                </span>
                                <span v-if="!row.personel_utama?.length" class="text-slate-400">-</span>
                            </div>
                        </td>
                        <td class="border-b border-l border-slate-900 px-4 py-3 text-center font-black text-slate-900 dark:border-b-white/20 dark:border-l-white/20 dark:text-white">
                            {{ rowTotalAll(row) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section
        v-else
        class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center text-sm text-slate-500 shadow-sm dark:border-white/10 dark:bg-[#171717] dark:text-slate-400"
    >
        Belum ada data analysis yang dapat ditampilkan.
    </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    rows: {
        type: Array,
        default: () => [],
    },
});

const totals = computed(() => props.rows.reduce((acc, row) => {
    acc.projectSponsors += row.project_sponsors?.length ?? 0;
    acc.projectOwners += row.project_owners?.length ?? 0;
    acc.projectLeaders += row.project_leaders?.length ?? 0;
    acc.crossFunctionInvolvements += row.cross_function_involvements?.length ?? 0;
    acc.personelUtama += row.personel_utama?.length ?? 0;
    return acc;
}, {
    projectSponsors: 0,
    projectOwners: 0,
    projectLeaders: 0,
    crossFunctionInvolvements: 0,
    personelUtama: 0,
}));

const rowTotalAll = (row) => (
    (row.project_sponsors?.length ?? 0)
    + (row.project_owners?.length ?? 0)
    + (row.project_leaders?.length ?? 0)
    + (row.cross_function_involvements?.length ?? 0)
    + (row.personel_utama?.length ?? 0)
);

const statusLabel = (status) => {
    const s = String(status ?? '').trim().toLowerCase();
    const n = Number(status);

    if (Number.isFinite(n)) {
        if (n === 1) return 'Drafting';
        if (n === 2) return 'Propose';
        if (n === 3) return 'Review';
        if (n === 4) return 'Approved';
        if (n === 5) return 'Baseline';
    }

    if (s === 'on track' || s === 'on-track') return 'On Track';
    if (s === 'at risk' || s === 'at-risk') return 'At Risk';
    if (s === 'delayed') return 'Delayed';
    if (s === 'on progress' || s === 'on-progress' || s === 'on progres' || s === 'in progress') return 'On Progress';
    if (s === 'not started' || s === 'not-started' || s === 'not start') return 'Not Started';
    if (s === 'not signed' || s === 'not-signed') return 'Not Signed';
    if (s === 'done' || s === 'completed') return 'Done';

    const raw = String(status ?? '').trim();
    return raw !== '' ? raw : '-';
};

const capsuleTooltip = (item, statusValue = null) => {
    const projectName = String(item?.name ?? '').trim() || '-';
    const status = statusLabel(statusValue);
    return `${projectName} - ${status}`;
};

const statusColor = (status) => {
    const s = String(status ?? '').trim().toLowerCase();
    const n = Number(status);

    if (Number.isFinite(n)) {
        if (n === 1) return 'bg-slate-500';
        if (n === 2) return 'bg-blue-500';
        if (n === 3) return 'bg-amber-500';
        if (n === 4) return 'bg-emerald-500';
        if (n === 5) return 'bg-purple-500';
    }

    if (['on track', 'on-track'].includes(s)) return 'bg-emerald-500';
    if (['at risk', 'at-risk'].includes(s)) return 'bg-orange-500';
    if (['delayed'].includes(s)) return 'bg-orange-600';
    if (['on progress', 'on-progress', 'on progres', 'in progress'].includes(s)) return 'bg-sky-500';
    if (['not started', 'not-started', 'not start'].includes(s)) return 'bg-blue-500';
    if (['not signed', 'not-signed'].includes(s)) return 'bg-rose-500';
    if (['done', 'completed'].includes(s)) return 'bg-slate-500';

    return 'bg-slate-400';
};
</script>
