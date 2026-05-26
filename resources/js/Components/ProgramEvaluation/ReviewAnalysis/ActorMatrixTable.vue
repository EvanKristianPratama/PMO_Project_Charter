<template>
    <section v-if="rows.length > 0" class="overflow-hidden rounded-2xl border border-slate-900 shadow-sm dark:border-white/20">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-[11px]">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:bg-white/5 dark:text-slate-400">
                        <th class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">Aktor</th>
                        <th class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">Project Sponsor</th>
                        <th class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">Project Owner</th>
                        <th class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">Project Leader</th>
                        <th class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">Cross Function Involvement</th>
                        <th class="border-b border-slate-900 px-4 py-3 dark:border-white/20">Personel Utama</th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-[#171717]">
                    <tr
                        v-for="row in rows"
                        :key="row.organization_id"
                        class="align-top transition-colors hover:bg-slate-50 dark:hover:bg-white/5"
                    >
                        <td class="border-b border-r border-slate-900 px-4 py-4 font-black text-slate-900 dark:border-b-white/20 dark:border-r-white/20 dark:text-white">
                            <div class="flex flex-col gap-1">
                                <span>{{ row.actor || '-' }}</span>
                                <span class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                                    {{ row.organization_code || '-' }}
                                </span>
                                <span v-if="row.organization_name" class="text-[10px] font-semibold text-slate-500">
                                    {{ row.organization_name }}
                                </span>
                            </div>
                        </td>

                        <td class="border-b border-r border-slate-900 px-4 py-3 dark:border-b-white/20 dark:border-r-white/20">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="item in row.project_sponsors"
                                    :key="`sp-${row.organization_id}-${item.initiative_id}`"
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="statusColor(item.status)"
                                    :title="item.name"
                                >
                                    {{ item.no }}
                                </span>
                                <span v-if="!row.project_sponsors?.length" class="text-slate-400">-</span>
                            </div>
                        </td>

                        <td class="border-b border-r border-slate-900 px-4 py-3 dark:border-b-white/20 dark:border-r-white/20">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="item in row.project_owners"
                                    :key="`ow-${row.organization_id}-${item.initiative_id}`"
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="statusColor(item.status)"
                                    :title="item.name"
                                >
                                    {{ item.no }}
                                </span>
                                <span v-if="!row.project_owners?.length" class="text-slate-400">-</span>
                            </div>
                        </td>

                        <td class="border-b border-r border-slate-900 px-4 py-3 dark:border-b-white/20 dark:border-r-white/20">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="item in row.project_leaders"
                                    :key="`ld-${row.organization_id}-${item.initiative_id}`"
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="statusColor(item.status)"
                                    :title="item.name"
                                >
                                    {{ item.no }}
                                </span>
                                <span v-if="!row.project_leaders?.length" class="text-slate-400">-</span>
                            </div>
                        </td>

                        <td class="border-b border-r border-slate-900 px-4 py-3 dark:border-b-white/20 dark:border-r-white/20">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="item in row.cross_function_involvements"
                                    :key="`cf-${row.organization_id}-${item.initiative_id}`"
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="statusColor(item.status)"
                                    :title="item.name"
                                >
                                    {{ item.no }}
                                </span>
                                <span v-if="!row.cross_function_involvements?.length" class="text-slate-400">-</span>
                            </div>
                        </td>

                        <td class="border-b px-4 py-3 dark:border-b-white/20">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="item in row.personel_utama"
                                    :key="`kp-${row.organization_id}-${item.label}`"
                                    class="inline-flex rounded-full border border-slate-300 bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 shadow-sm dark:border-white/10 dark:bg-white/5 dark:text-slate-200"
                                    :title="item.note || item.label"
                                >
                                    {{ item.label }}
                                </span>
                                <span v-if="!row.personel_utama?.length" class="text-slate-400">-</span>
                            </div>
                        </td>
                    </tr>
                </tbody>

                <tfoot>
                    <tr class="bg-slate-50 font-black text-slate-900 dark:bg-white/5 dark:text-white uppercase text-[10px]">
                        <td class="border-t border-r border-slate-900 px-4 py-3 dark:border-white/20">Grand Total</td>
                        <td class="border-t border-r border-slate-900 px-4 py-3 dark:border-white/20">
                            {{ totals.projectSponsors }}
                        </td>
                        <td class="border-t border-r border-slate-900 px-4 py-3 dark:border-white/20">
                            {{ totals.projectOwners }}
                        </td>
                        <td class="border-t border-r border-slate-900 px-4 py-3 dark:border-white/20">
                            {{ totals.projectLeaders }}
                        </td>
                        <td class="border-t border-r border-slate-900 px-4 py-3 dark:border-white/20">
                            {{ totals.crossFunctionInvolvements }}
                        </td>
                        <td class="border-t border-slate-900 px-4 py-3 dark:border-white/20">
                            {{ totals.personelUtama }}
                        </td>
                    </tr>
                </tfoot>
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

const statusColor = (status) => {
    const s = String(status ?? '').trim().toLowerCase();

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
