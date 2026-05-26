<template>
    <section v-if="displayData.length > 0" class="mt-8 overflow-hidden animate-fade-in-up delay-125">
        <div class="overflow-x-auto rounded-2xl border border-slate-900 shadow-sm dark:border-white/20">
            <table class="w-full border-collapse text-left text-[11px]">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:bg-white/5 dark:text-slate-400">
                        <th rowspan="2" class="border-b border-r border-slate-900 px-4 py-3 dark:border-white/20">
                            <div class="flex items-center justify-between gap-4">
                                <span>Project Owner</span>
                                <select 
                                    v-model="viewMode"
                                    class="bg-white dark:bg-[#1a1a1a] border border-slate-300 dark:border-white/10 rounded px-2 py-0.5 text-[9px] font-bold outline-none focus:ring-1 focus:ring-indigo-500 transition-all cursor-pointer"
                                >
                                    <option value="original">Original</option>
                                    <option value="restructure">Refinement</option>
                                </select>
                            </div>
                        </th>
                        <th colspan="2" class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Aproval</th>
                        <th colspan="2" class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">No Approved</th>
                        <th rowspan="2" class="border-b border-l border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            Total All
                        </th>
                    </tr>
                    <tr class="bg-slate-50/50 text-[9px] font-bold uppercase tracking-widest text-slate-400 dark:bg-white/5 dark:text-slate-500">
                        <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Total</th>
                        <th class="border-b border-r border-slate-900 px-4 py-2 dark:border-white/20">Inisiatif</th>
                        <th class="border-b border-r border-slate-900 px-4 py-2 text-center dark:border-white/20">Total</th>
                        <th class="border-b border-slate-900 px-4 py-2 dark:border-white/20">Inisiatif</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-[#171717]">
                    <tr v-for="row in displayData" :key="row.owner" class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <td class="border-r border-slate-900 px-4 py-4 font-black normal-case text-slate-900 dark:border-white/20 dark:text-white">
                            {{ row.owner }}
                        </td>
                        <td class="border-r border-slate-900 px-4 py-4 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                            {{ row.approved.length }}
                        </td>
                        <td class="border-r border-slate-900 px-4 py-4 dark:border-white/20">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="init in row.approved"
                                    :key="init.no"
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="getCircleColor(init.status)"
                                    :title="init.status"
                                >
                                    {{ init.no }}
                                </span>
                                <span v-if="row.approved.length === 0" class="text-slate-400">-</span>
                            </div>
                        </td>
                        <td class="border-r border-slate-900 px-4 py-4 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                            {{ row.notApproved.length }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="init in row.notApproved"
                                    :key="init.no"
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="getCircleColor(init.status)"
                                    :title="init.status"
                                >
                                    {{ init.no }}
                                </span>
                                <span v-if="row.notApproved.length === 0" class="text-slate-400">-</span>
                            </div>
                        </td>
                        <td class="border-l border-slate-900 px-4 py-4 text-center font-black text-slate-900 dark:border-white/20 dark:text-white">
                            {{ row.totalCount }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50 font-black text-slate-900 dark:bg-white/5 dark:text-white uppercase text-[10px]">
                        <td class="border-t border-r border-slate-900 px-4 py-3 dark:border-white/20">
                            Grand Total
                        </td>
                        <td class="border-t border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            {{ totals.approvedCount }}
                        </td>
                        <td class="border-t border-r border-slate-900 px-4 py-3 dark:border-white/20">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="init in totals.approvedItems"
                                    :key="`total-app-${init.no}`"
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="getCircleColor(init.status)"
                                    :title="init.status"
                                >
                                    {{ init.no }}
                                </span>
                            </div>
                        </td>
                        <td class="border-t border-r border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            {{ totals.notApprovedCount }}
                        </td>
                        <td class="border-t border-slate-900 px-4 py-3">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="init in totals.notApprovedItems"
                                    :key="`total-notapp-${init.no}`"
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                    :class="getCircleColor(init.status)"
                                    :title="init.status"
                                >
                                    {{ init.no }}
                                </span>
                            </div>
                        </td>
                        <td class="border-t border-l border-slate-900 px-4 py-3 text-center dark:border-white/20">
                            {{ totals.totalCount }}
                        </td>
                    </tr>
                    <tr class="bg-slate-100/80 text-slate-900 dark:bg-white/10 dark:text-white font-black uppercase text-[11px] border-t-1 border-slate-900 dark:border-white/40">
                        <td colspan="5" class="px-4 py-2.5 text-right border-r border-slate-900 dark:border-white/20">
                            Total Keseluruhan Inisiatif (Approved + No Approved)
                        </td>
                        <td class="px-4 py-2.5 text-center text-[13px]">
                            {{ totals.totalCount }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    originalData: { type: Array, required: true },
    restructureData: { type: Array, required: true },
    getCircleColor: { type: Function, required: true },
});

const viewMode = ref('original'); // 'original' or 'restructure'

const displayData = computed(() => {
    return viewMode.value === 'original' ? props.originalData : props.restructureData;
});

const totals = computed(() => {
    const agg = displayData.value.reduce((acc, row) => {
        acc.approvedCount += row.approved.length;
        acc.notApprovedCount += row.notApproved.length;
        acc.totalCount += row.totalCount ?? (row.approved.length + row.notApproved.length);
        acc.approvedItems.push(...row.approved);
        acc.notApprovedItems.push(...row.notApproved);
        return acc;
    }, { totalCount: 0, approvedCount: 0, notApprovedCount: 0, approvedItems: [], notApprovedItems: [] });

    // Sort by no to keep capsules ordered
    agg.approvedItems.sort((a, b) => (Number(a.no) || 0) - (Number(b.no) || 0));
    agg.notApprovedItems.sort((a, b) => (Number(a.no) || 0) - (Number(b.no) || 0));

    return agg;
});
</script>
